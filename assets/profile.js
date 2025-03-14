import './styles/profile.scss'

const container = document.querySelector('[data-menu-container]')
const cssClass = 'profile__menu--show'

document.querySelector('[data-menu]').addEventListener('click', (e) => {
    e.stopPropagation()

    if (container.classList.contains(cssClass)) {
        document.removeEventListener('click', clickOutside)
    } else {
        document.addEventListener('click', clickOutside)
    }

    container.classList.toggle('profile__menu--show')
})

function clickOutside(e) {
    if (!container.contains(e.target)) {
        container.classList.toggle(cssClass)
    }
}
