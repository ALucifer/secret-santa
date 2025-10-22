import { mount } from 'cypress/vue'
import WishEvent from "../../assets/vue/components/Wish/WishEvent.vue";

// Ce test vérifie le rendu et l'émission d'événements
describe('<Wish />', () => {
    it('affiche le nom et la date formatée', () => {
        const name = 'Anniversaire'
        const date = '2025-12-25'

        mount(WishEvent, {
            props: { name, date },
        })

        cy.contains('Anniversaire').should('exist')
        cy.contains('25 déc. 2025').should('exist')
    })

    it('émet l’événement remove quand le conteneur déclenche remove', () => {
        const onRemove = cy.spy().as('onRemove')

        mount(WishEvent, {
            props: {
                name: 'Fête',
                date: '2025-01-01',
                onRemove,
            },
        })

        // on simule l’événement "remove" venant de WishContainer
        cy.wrap(Cypress.vueWrapper).trigger('remove')
        cy.get('@onRemove').should('have.been.called')
    })
})
