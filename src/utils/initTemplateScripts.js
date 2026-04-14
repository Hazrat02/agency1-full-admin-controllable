const scripts = [
  { id: 'legacy-function-js', src: '/js/function.js' },
]

let activeInitialization = Promise.resolve()

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

function waitForFrames(count = 1) {
  return new Promise((resolve) => {
    const step = () => {
      if (count <= 0) {
        resolve()
        return
      }

      count -= 1
      window.requestAnimationFrame(step)
    }

    window.requestAnimationFrame(step)
  })
}

export async function initTemplateScripts() {
  activeInitialization = activeInitialization
    .catch(() => {})
    .then(async () => {
      await waitForFrames(2)

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

      await waitForFrames(2)

      if (window.ScrollTrigger?.refresh) {
        window.ScrollTrigger.refresh()
      }
    })

  return activeInitialization
}
