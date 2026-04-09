const scripts = [
  { id: 'legacy-function-js', src: '/js/function.js' },
  { id: 'legacy-theme-panel-js', src: '/js/theme-panel.js' },
]

function loadScript({ id, src }) {
  return new Promise((resolve, reject) => {
    const existing = document.getElementById(id)
    if (existing) {
      existing.remove()
    }

    const script = document.createElement('script')
    script.id = id
    script.src = src
    script.onload = resolve
    script.onerror = reject
    document.body.appendChild(script)
  })
}

export async function initTemplateScripts() {
  if (window.jQuery) {
    window.jQuery('.slicknav_menu').remove()
    window.jQuery('.cb-cursor').remove()
    window.jQuery('.offcanvas-backdrop').remove()
    window.jQuery('.responsive-menu').empty()
    window.jQuery('.navbar-toggle').empty()
  }

  document.body.style.removeProperty('overflow')
  document.body.style.removeProperty('padding-right')

  for (const script of scripts) {
    await loadScript(script)
  }
}
