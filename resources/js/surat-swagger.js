import SwaggerUI from 'swagger-ui-dist/swagger-ui-bundle.js';
import 'swagger-ui-dist/swagger-ui.css';
import '../sass/swagger-docs.scss';

const root = document.getElementById('swagger-ui');

if (root) {
    SwaggerUI({
        domNode: root,
        url: root.dataset.specUrl,
        deepLinking: true,
        displayRequestDuration: true,
        persistAuthorization: false,
        presets: [SwaggerUI.presets.apis],
    });
}
