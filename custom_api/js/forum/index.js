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
        // Get the user's email (you'll need to implement getUserEmail() based on your NeonCRM API or other methods)
        const userEmail = getUserEmail();  // Replace with actual method to get the user's email

        // Make an initial GET request to retrieve the CSRF token
        fetch(app.forum.attribute('apiUrl') + '/csrf-token', {
            method: 'GET',
            credentials: 'include'
        })
        .then(response => response.json())
        .then(data => {
            const csrfToken = data.csrfToken; // Retrieve CSRF token from response

            // Make the POST request with the CSRF token and email
            fetch(app.forum.attribute('apiUrl') + '/neoncrm/login', {
                method: 'POST',
                credentials: 'include',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-Token': csrfToken // Include CSRF token in the headers
                },
                body: JSON.stringify({ email: userEmail }) // Send the email instead of userId
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Redirect to Flarum dashboard if login is successful
                    window.location.href = '/forum/dashboard';
                } else {
                    // Redirect to the join page if login fails
                    window.location.href = 'https://www.hopecommunitycenter.org/join';
                }
            })
            .catch(error => console.error('Login failed:', error));
        })
        .catch(error => console.error('Failed to retrieve CSRF token:', error));
    });
});
