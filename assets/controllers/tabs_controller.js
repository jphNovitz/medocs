import { Controller } from '@hotwired/stimulus';
const targets = ['qr', 'url', 'send']

export default class extends Controller {

    show(event) {
        const tabName = event.params.tab;
        const clickedButton = event.currentTarget;
        const allButtons = this.element.querySelectorAll('[data-tabs-target]');
        
        const parentTab = this.element.querySelector('#default-tab');
        parentTab.querySelectorAll('li').forEach(li => {
            li.classList.remove('border-b-2', 'border-surface-dark', 'dark:border-surface-light/60');
        });

        clickedButton.closest('li').classList.add('border-b-2', 'border-surface-dark', 'dark:border-surface-light/60');
        // Trouver l'élément cible correspondant au nom de l'onglet
        const targetElement = this.targets.find(tabName);
        // Vérifier si l'élément cible existe
        if (targetElement) {
            // Retirer la classe 'hidden' de l'élément cible
            targetElement.classList.remove('hidden');
            
            // Optionnel : Cacher les autres onglets
            targets.forEach(target => {
                if (target !== tabName) {
                    const otherElement = this.element.querySelector(`[data-tabs-target="${target}"]`);
                    if (otherElement) {
                        otherElement.classList.add('hidden');
                    }
                }
            });
        }
    }
}
