const rawApiBaseUrl = import.meta.env.VITE_API_BASE_URL ?? ''

export const apiBaseUrl = rawApiBaseUrl.replace(/\/+$/, '')

export function buildApiUrl(path = '') {
  const normalizedPath = String(path).replace(/^\/+/, '')

  if (!normalizedPath) {
    return apiBaseUrl
  }

  return `${apiBaseUrl}/${normalizedPath}`
}

export function resolveAssetUrl(path) {
  if (!path) {
    return ''
  }

  if (/^(?:https?:)?\/\//i.test(path) || /^(?:data|blob):/i.test(path)) {
    return path
  }

  const baseOrigin = apiBaseUrl.replace(/\/api$/, '')
  const normalizedPath = path.startsWith('/') ? path : `/${path}`

  if (!baseOrigin) {
    if (typeof window === 'undefined') {
      return normalizedPath
    }

    return new URL(normalizedPath, window.location.origin).toString()
  }

  return `${baseOrigin}${normalizedPath}`
}
