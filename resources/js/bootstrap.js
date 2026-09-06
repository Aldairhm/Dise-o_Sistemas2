import axios from 'axios';
window.axios = axios;

// resources/js/bootstrap.js (o al inicio de app.js)
import Alpine from 'alpinejs';
window.Alpine = Alpine;

import Swal from 'sweetalert2';
window.Swal = Swal;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
