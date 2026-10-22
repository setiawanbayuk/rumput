document.addEventListener('DOMContentLoaded', function () {
    const root = document.getElementById('swagger-ui');

    if (!root || !window.SwaggerUIBundle) {
        return;
    }

    window.SwaggerUIBundle({
        domNode: root,
        url: root.dataset.specUrl,
        deepLinking: true,
        displayRequestDuration: true,
        persistAuthorization: false,
        presets: [window.SwaggerUIBundle.presets.apis],
    });
});
