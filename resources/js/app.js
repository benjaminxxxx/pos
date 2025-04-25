import Swal from 'sweetalert2'
import { createApp } from 'vue';

import App from './components/App.vue';
createApp(App).mount('#app-vender');
window.Swal = Swal
