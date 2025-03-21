import './styles/profile.scss'
import './modal'

const container = document.querySelector('[data-menu-container]')
const cssClass = 'profile__menu--show'

document.querySelector('[data-menu]').addEventListener('click', (e) => {
    e.stopPropagation()

    if (container.classList.contains(cssClass)) {
        document.removeEventListener('click', clickOutside)
    } else {
        document.addEventListener('click', clickOutside)
    }

    container.classList.toggle(cssClass)
})

function clickOutside(e) {
    if (!container.contains(e.target)) {
        container.classList.toggle(cssClass)
    }
}

document
    .querySelector('[data-modal-open]')
    .addEventListener(
        'click',
        () => {
            const modal = document.querySelector('[role="dialog"]')

            if (modal) {
                container.classList.toggle(cssClass)
                modal.classList.remove('hidden')
            }
        }
    )