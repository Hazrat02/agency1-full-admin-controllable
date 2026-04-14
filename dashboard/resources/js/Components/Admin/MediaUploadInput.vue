<script setup>
import { ref } from 'vue';

const props = defineProps({
    modelValue: {
        type: String,
        default: '',
    },
    label: {
        type: String,
        default: '',
    },
    placeholder: {
        type: String,
        default: '',
    },
    folder: {
        type: String,
        default: 'content',
    },
    accept: {
        type: String,
        default: '*/*',
    },
});

const emit = defineEmits(['update:modelValue']);

const fileInput = ref(null);
const uploading = ref(false);

const openPicker = () => {
    fileInput.value?.click();
};

const onFileChange = async (event) => {
    const file = event.target.files?.[0];

    if (!file) {
        return;
    }

    uploading.value = true;

    try {
        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        const formData = new FormData();
        formData.append('file', file);
        formData.append('folder', props.folder);

        const response = await fetch('/admin/uploads', {
            method: 'POST',
            headers: {
                Accept: 'application/json',
                'X-CSRF-TOKEN': token,
            },
            body: formData,
        });

        const payload = await response.json();

        if (!response.ok) {
            throw new Error(payload.message || 'Upload failed.');
        }

        emit('update:modelValue', payload.url || '');
    } catch (error) {
        window.alert(error.message || 'Upload failed.');
    } finally {
        uploading.value = false;
        event.target.value = '';
    }
};
</script>

<template>
    <div class="mb-3">
        <label v-if="label" class="form-label">{{ label }}</label>
        <div class="input-group">
            <input
                :value="modelValue"
                class="form-control"
                type="text"
                :placeholder="placeholder"
                @input="emit('update:modelValue', $event.target.value)"
            />
            <button type="button" class="btn btn-outline-secondary" :disabled="uploading" @click="openPicker">
                <i :class="uploading ? 'fas fa-spinner fa-spin' : 'fas fa-upload'"></i>
            </button>
        </div>
        <input ref="fileInput" class="d-none" type="file" :accept="accept" @change="onFileChange" />
    </div>
</template>
