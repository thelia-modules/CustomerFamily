import { Controller } from '@hotwired/stimulus';

/*
 * CustomerFamily configuration page behaviour (Bootstrap 5, no jQuery).
 * Replaces the legacy Smarty inline jQuery script.
 */
export default class extends Controller {
    static targets = [
        'deleteModal', 'deleteFormEl',
        'updateModal', 'updateFormEl', 'updateCode', 'updateTitle',
        'defaultForm', 'failedModal', 'failedBody',
    ];

    static values = {
        deleteUrl: String,
        updateUrl: String,
        defaultUrl: String,
        redirectUrl: String,
    };

    openDelete(event) {
        event.preventDefault();
        const id = event.currentTarget.dataset.id;
        this.deleteFormElTarget.action = this.deleteUrlValue.replace('CFID', id);
        this.#modal(this.deleteModalTarget).show();
    }

    openUpdate(event) {
        event.preventDefault();
        const el = event.currentTarget;
        this.updateCodeTarget.value = el.dataset.code;
        this.updateTitleTarget.value = el.dataset.title;
        this.updateFormElTarget.action = this.updateUrlValue.replace('CFID', el.dataset.id);
        this.#modal(this.updateModalTarget).show();
    }

    async setDefault(event) {
        const id = event.currentTarget.dataset.id;
        const form = this.defaultFormTarget;
        form.querySelector('#default_family_id').value = id;

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                body: new URLSearchParams(new FormData(form)),
            });
            if (!response.ok) {
                const data = await response.json().catch(() => ({ error: 'Error' }));
                this.failedBodyTarget.textContent = data.error || 'Error';
                this.#modal(this.failedModalTarget).show();
                return;
            }
            window.location.href = this.redirectUrlValue;
        } catch (e) {
            this.failedBodyTarget.textContent = String(e);
            this.#modal(this.failedModalTarget).show();
        }
    }

    toggleRestriction(event) {
        const targetClass = event.currentTarget.dataset.targetId;
        this.element.querySelectorAll('.' + targetClass).forEach((node) => {
            node.style.display = event.currentTarget.checked ? '' : 'none';
        });
    }

    #modal(el) {
        return window.bootstrap.Modal.getOrCreateInstance(el);
    }
}
