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
        const userEmail = getUserEmail();  // You still need to define this

        if (!userEmail) {
            console.error('No email provided.');
            return;
        }

        fetch(app.forum.attribute('apiUrl') + '/csrf-token', {
            method: 'GET',
            credentials: 'include'
        })
        .then(response => response.json())
        .then(data => {
            const csrfToken = data.csrfToken;

            return fetch(app.forum.attribute('apiUrl') + '/neoncrm/login', {
                method: 'POST',
                credentials: 'include',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-Token': csrfToken
                },
                body: JSON.stringify({ email: userEmail })
            });
        })
        .then(response => response.json())
        .then(data => {
            if (data.token) {
                // Log in with token
                app.session.loginWithToken(data.token);
                // Optional: Redirect after short delay or immediately
                window.location.href = data.redirect || app.forum.attribute('baseUrl');
            } else {
                console.error('Token not received:', data);
                window.location.href = 'https://www.hopecommunitycenter.org/join';
            }
        })
        .catch(error => {
            console.error('Login process failed:', error);
            window.location.href = 'https://www.hopecommunitycenter.org/join';
        });
    });
});
