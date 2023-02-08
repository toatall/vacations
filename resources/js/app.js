import './bootstrap';

import 'tw-elements';
import 'table-sort-js'; 


import Alpine from 'alpinejs';

// import './dashboard.chart';

window.Alpine = Alpine;
Alpine.start();

document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function(el) { return new Tooltip(el); });