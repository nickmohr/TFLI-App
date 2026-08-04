(() => {
    'use strict';

    const form = document.getElementById('url-form');
    const result = document.getElementById('result');
    const submitButton = document.getElementById('submit-button');

    if (!form || !result || !submitButton) {
        return;
    }

    const fields = [...form.querySelectorAll('input:not([type="hidden"])')];

    const errorClasses = ['border-red-500', 'focus:ring-red-500'];
    const errorSlot = (field) => document.getElementById(`${field.id}-error`);

    function setFieldError(field, message) {
        const slot = errorSlot(field);
        if (!slot) {
            return;
        }

        field.classList.add(...errorClasses);
        field.setAttribute('aria-invalid', 'true');
        slot.textContent = message;
        slot.classList.remove('hidden');
    }

    function clearFieldError(field) {
        const slot = errorSlot(field);
        if (!slot) {
            return;
        }

        field.classList.remove(...errorClasses);
        field.removeAttribute('aria-invalid');
        slot.textContent = '';
        slot.classList.add('hidden');
    }

    function validate() {
        const offsetMs = new Date().getTimezoneOffset() * 60000;
        form.elements.expires_at.min = new Date(Date.now() - offsetMs).toISOString().slice(0, 16);

        const invalid = fields.filter((field) => {
            clearFieldError(field);
            if (field.checkValidity()) {
                return false;
            }
            setFieldError(field, field.validationMessage);
            return true;
        });

        invalid[0]?.focus();
        return invalid.length === 0;
    }

    function show(message, isError) {
        result.classList.remove('hidden', 'bg-green-100', 'text-green-800', 'bg-red-100', 'text-red-800');
        result.classList.add(
            isError ? 'bg-red-100' : 'bg-green-100',
            isError ? 'text-red-800' : 'text-green-800',
        );
        result.replaceChildren(message);
    }

    function showShortUrl(shortUrl) {
        const link = Object.assign(document.createElement('a'), {
            href: shortUrl,
            textContent: shortUrl,
            className: 'underline',
        });
        const fragment = document.createDocumentFragment();
        fragment.append('Short URL: ', link);
        show(fragment, false);
    }

    fields.forEach((field) => field.addEventListener('input', () => clearFieldError(field)));

    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        result.classList.add('hidden');

        if (!validate()) {
            return;
        }

        submitButton.disabled = true;
        const originalLabel = submitButton.textContent;
        submitButton.textContent = 'Shortening';

        try {
            const body = new FormData(form);
            // Sort local time to UTC for the backend - https://stackoverflow.com/questions/948532/how-to-convert-a-date-to-utc
            if (body.get('expires_at')) {
                body.set('expires_at', new Date(body.get('expires_at')).toISOString().slice(0, 16));
            }

            const response = await fetch('/', {
                method: 'POST',
                headers: {
                    Accept: 'application/json',
                    'X-CSRF-Token': String(body.get('csrf_token') ?? ''),
                },
                body,
            });
            const data = await response.json();

            if (!data.success) {
                show(data.errors?.join(' ') ?? data.error ?? 'Something went wrong.', true);
                return;
            }

            showShortUrl(data.short_url);
        } catch {
            show('Network error, please try again.', true);
        } finally {
            submitButton.disabled = false;
            submitButton.textContent = originalLabel;
        }
    });
})();
