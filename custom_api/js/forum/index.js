import app from 'flarum/forum/app';

app.initializers.add('neoncrm-login', () => {
    app.extensionData
        .for('neoncrm-login')
        .registerSetting({
            setting: 'neoncrm-login.redirect_url',
            type: 'text',
            label: app.translator.trans('neoncrm-login.admin.settings.redirect_url_label')
        });

    document.getElementById('neoncrm-login')?.addEventListener('click', () => {
        // Make an initial GET request to retrieve the CSRF token
        fetch(app.forum.attribute('apiUrl') + '/csrf-token', {
            method: 'GET',
            credentials: 'include'
        })
        .then(response => response.json())
        .then(data => {
            const csrfToken = data.csrfToken; // Retrieve CSRF token from response

            // Make the POST request with the CSRF token
            fetch(app.forum.attribute('apiUrl') + '/neoncrm/login', {
                method: 'POST',
                credentials: 'include',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-Token': csrfToken // Include CSRF token in the headers
                },
                body: JSON.stringify({ userId: '12345' }) // Include the userId in the request body
            })
            .then(response => response.json())
            .then(data => {
                if (data.redirect) {
                    window.location.href = data.redirect;
                }
            })
            .catch(error => console.error('Login failed:', error));
        })
        .catch(error => console.error('Failed to retrieve CSRF token:', error));
    });
});
