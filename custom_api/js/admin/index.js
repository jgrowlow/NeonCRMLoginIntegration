import app from 'flarum/admin/app';

app.initializers.add('neoncrm-login', () => {
    app.extensionData
        .for('neoncrm-login')
        .registerSetting({
            setting: 'neoncrm-login.api_key',
            type: 'text',
            label: app.translator.trans('neoncrm-login.admin.settings.api_key_label'),
            help: '039d541079af4f9265c81d663b35a5a4',
        })
        .registerSetting({
            setting: 'neoncrm-login.redirect_url',
            type: 'text',
            label: app.translator.trans('neoncrm-login.admin.settings.redirect_url_label'),
            help: 'https://www.hccfrontline.org',
        });

    console.log('[NeonCRM Login] Admin settings loaded');
});
