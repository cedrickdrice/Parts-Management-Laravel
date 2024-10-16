// main.js
import './bootstrap';
import 'primeicons/primeicons.css'


// Imports
import { createApp } from 'vue';
import { createPinia } from 'pinia';
import router from './router';  // Import the router
import PrimeVue from 'primevue/config';
import Aura from '@primevue/themes/aura';

import FileUpload from 'primevue/fileupload';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Button from 'primevue/button';
import ButtonGroup from 'primevue/buttongroup';
import Dialog from 'primevue/dialog';

import InputText from 'primevue/inputtext';
import IconField from 'primevue/iconfield';
import InputIcon from 'primevue/inputicon';
import InputNumber from 'primevue/inputnumber';




import VueSweetalert2 from 'vue-sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';

// Components
import Views from './Views/View.vue';

const app = createApp({});
const pinia = createPinia();

// Use
app.use(pinia);
app.use(router);
app.use(VueSweetalert2);
app.use(PrimeVue, {
    theme: {
        preset: Aura,
        options: {
            prefix: 'p',
            darkModeSelector: 'system',
            cssLayer: false
        }
    }
});

// Components
app.component('Dialog', Dialog);
app.component('FileUpload', FileUpload);
app.component('DataTable', DataTable);
app.component('Column', Column);
app.component('Button', Button);
app.component('ButtonGroup', ButtonGroup);
app.component('InputText', InputText);
app.component('IconField', IconField);
app.component('InputIcon', InputIcon);
app.component('InputNumber', InputNumber);
app.component('Views', Views);

// Mount the app
app.mount('#app');
