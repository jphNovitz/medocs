import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ["container", "form", "frequency", "frequencyNew", "moment", "momentNew"];

    open(e) {
        alert()
        e.preventDefault();
        this.containerTarget.classList.remove('hidden');
        this.containerTarget.classList.add('flex');
    }

    close() {
        this.containerTarget.classList.add('hidden');
        this.containerTarget.classList.remove('flex');
    }

    connect() {
            this.frequencyTarget.addEventListener('change', (e)=> {
                // Si l'option "Autre" est sélectionnée (adaptez l'ID selon votre implémentation)
                if (e.target.value === 'autre') {
                    this.frequencyNewTarget.classList.remove('hidden');
                } else {
                    this.frequencyNewTarget.classList.add('hidden');
                }
            });
            this.momentTarget.addEventListener('change', (e)=> {
                // Si l'option "Autre" est sélectionnée (adaptez l'ID selon votre implémentation)
                if (e.target.value === 'autre') {
                    this.momentNewTarget.classList.remove('hidden');
                } else {
                    this.momentNewTarget.classList.add('hidden');
                }
            });
    }


    async submit(event) {
        event.preventDefault();

        const csrfToken = document.getElementById('dose__token').value;
        const formData = new FormData(this.formTarget);

        try {
            const response = await fetch('/admin/dose/api/new', {
                method: 'POST',
                body: JSON.stringify(Object.fromEntries(formData)),
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
            });

            const data = await response.json();

            if (response.ok && data.success) {
                const select = document.querySelector('#line_dose');
                select.options.add(new Option(data.data.name, data.data.id, true, true));
                this.close();
                this.formTarget.reset();
            } else {
                alert(data.message || 'Erreur côté serveur.');
            }
        } catch (error) {
            console.error('Erreur réseau:', error);
            alert('Une erreur est survenue. Veuillez réessayer.');
        }
    }

}
