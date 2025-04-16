import $ from 'jquery';
window.$ = window.jQuery = $;

//Select migliorato 'Select2'

import * as bootstrap from 'bootstrap';
window.bootstrap = bootstrap;

import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

