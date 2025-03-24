import {Controller} from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ["container", "form", "name"];

    open(e) {
        e.preventDefault();
        this.containerTarget.classList.remove('hidden');
        this.containerTarget.classList.add('flex');
    }

    close() {
        this.containerTarget.classList.add('hidden');
        this.containerTarget.classList.remove('flex');
    }

    connect() {
    }


    async submit(event) {
        event.preventDefault();

        const csrfToken = document.getElementById('product__token').value;
        const formData = new FormData(this.formTarget);
        console.log(JSON.stringify(Object.fromEntries(formData)))
        try {
            const response = await fetch('/member/product/api/new\n', {
                method: 'POST',
                body: JSON.stringify(Object.fromEntries(formData)),
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
            });

            const data = await response.json();

            if (response.ok && data.success) {
                const select = document.querySelector('#line_product');
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
