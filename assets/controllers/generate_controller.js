import {Controller} from '@hotwired/stimulus';
import {fakerFR as faker} from '@faker-js/faker';


export default class extends Controller {

    initialize() {
        console.log('Generate QR')
    }

    insert(event) {
        event.preventDefault()
        const url = document.getElementById('url_url');
        const randomName = faker.person.fullName().toLowerCase().replace(/\s+/g, '_');;
        url.value = randomName;
    }
}
