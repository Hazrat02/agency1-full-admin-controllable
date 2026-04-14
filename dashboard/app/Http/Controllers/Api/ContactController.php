<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ContactSubmission;
use App\Models\SiteContent;
use App\Support\SiteContentDefaults;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function info(): JsonResponse
    {
        return response()->json([
            'contact' => $this->value('contact_info', SiteContentDefaults::contactInfo()),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'full_name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'form_email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'form_phone' => ['nullable', 'string', 'max:50'],
            'subject' => ['nullable', 'string', 'max:255'],
            'form_subject' => ['nullable', 'string', 'max:255'],
            'sub' => ['nullable', 'string', 'max:255'],
            'message' => ['nullable', 'string', 'max:5000'],
            'form_message' => ['nullable', 'string', 'max:5000'],
            'sms' => ['nullable', 'string', 'max:5000'],
        ]);

        $name = trim((string) ($validated['name'] ?? $validated['full_name'] ?? ''));
        $email = trim((string) ($validated['email'] ?? $validated['form_email'] ?? ''));
        $phone = trim((string) ($validated['phone'] ?? $validated['form_phone'] ?? ''));
        $subject = trim((string) ($validated['subject'] ?? $validated['form_subject'] ?? $validated['sub'] ?? ''));
        $message = trim((string) ($validated['message'] ?? $validated['form_message'] ?? $validated['sms'] ?? ''));

        if ($name === '' || $email === '' || $subject === '' || $message === '') {
            return response()->json([
                'message' => 'Name, email, subject, and message are required.',
                'errors' => [
                    'name' => $name === '' ? ['The name field is required.'] : [],
                    'email' => $email === '' ? ['The email field is required.'] : [],
                    'subject' => $subject === '' ? ['The subject field is required.'] : [],
                    'message' => $message === '' ? ['The message field is required.'] : [],
                ],
            ], 422);
        }

        $contact = ContactSubmission::query()->create([
            'name' => $name,
            'email' => $email,
            'phone' => $phone !== '' ? $phone : null,
            'subject' => $subject,
            'message' => $message,
            'status' => 'New',
        ]);

        $this->sendWelcomeMail($contact);

        return response()->json([
            'message' => 'Contact request submitted successfully.',
        ]);
    }

    private function sendWelcomeMail(ContactSubmission $contact): void
    {
        $smtp = $this->value('smtp_settings', SiteContentDefaults::smtpSettings());
        $contactInfo = $this->value('contact_info', SiteContentDefaults::contactInfo());

        if (empty($smtp['host']) || empty($smtp['port']) || empty($smtp['from_email'])) {
            return;
        }

        config([
            'mail.default' => 'smtp',
            'mail.mailers.smtp.transport' => 'smtp',
            'mail.mailers.smtp.host' => $smtp['host'],
            'mail.mailers.smtp.port' => (int) $smtp['port'],
            'mail.mailers.smtp.username' => $smtp['username'] ?? '',
            'mail.mailers.smtp.password' => $smtp['password'] ?? '',
            'mail.mailers.smtp.encryption' => ($smtp['encryption'] ?? 'tls') === 'none' ? null : ($smtp['encryption'] ?? 'tls'),
            'mail.from.address' => $smtp['from_email'],
            'mail.from.name' => $smtp['from_name'] ?: 'PEACEMAIN',
        ]);

        $subject = $contactInfo['welcome_subject'] ?? 'Welcome to PEACEMAIN';
        $template = $smtp['mail_template_html'] ?? '';
        $welcomeMessage = $contactInfo['welcome_message'] ?? 'Thanks for contacting us. Our team will get back to you shortly.';

        if (! $template) {
            $template = '<div style="font-family:Arial,sans-serif;padding:24px"><h2>Welcome [[name]]</h2><p>[[welcome_message]]</p><p>Regards,<br>[[from_name]]</p></div>';
        }

        $values = [
            $contact->name,
            $contact->email,
            $contact->subject,
            e($contact->message),
            nl2br(e($welcomeMessage)),
            $smtp['from_name'] ?: 'PEACEMAIN',
        ];
        $html = str_replace(
            ['[[name]]', '[[email]]', '[[subject]]', '[[message]]', '[[welcome_message]]', '[[from_name]]'],
            $values,
            $template
        );
        $html = str_replace(
            ['{{name}}', '{{email}}', '{{subject}}', '{{message}}', '{{welcome_message}}', '{{from_name}}'],
            $values,
            $html
        );

        try {
            Mail::html($html, function ($message) use ($contact, $subject): void {
                $message->to($contact->email)->subject($subject);
            });
        } catch (\Throwable $exception) {
            Log::error('Welcome mail send failed', [
                'contact_submission_id' => $contact->id,
                'error' => $exception->getMessage(),
            ]);
        }
    }

    private function value(string $key, mixed $default): mixed
    {
        $record = SiteContent::query()->where('key', $key)->first();

        return $record?->value ?? $default;
    }
}
