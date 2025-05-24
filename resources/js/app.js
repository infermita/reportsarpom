import './bootstrap';

//import 'jquery/dist/jquery'

//require('metismenu');

//require('raphael');
//require('morris.js/morris');

// require('datatables.net');
// require('datatables.net-bs');
// require('datatables.net-responsive');
// require('datatables.net-responsive-bs');


import DataTable from 'datatables.net-dt';

import 'startbootstrap-sb-admin-2/js/sb-admin-2.min.js';
import 'startbootstrap-sb-admin-2/css/sb-admin-2.min.css';

 console.log('jQuery v', $().jquery);

/*
 
 import 'startbootstrap-sb-admin-2/vendor/jquery/jquery.min.js';
 import 'startbootstrap-sb-admin-2/vendor/bootstrap/js/bootstrap.bundle.min.js';
 import 'startbootstrap-sb-admin-2/vendor/jquery-easing/jquery.easing.min.js';
 
 import 'startbootstrap-sb-admin-2/js/sb-admin-2.min.js';
 import 'startbootstrap-sb-admin-2/vendor/fontawesome-free/css/all.min.css';
 import 'startbootstrap-sb-admin-2/css/sb-admin-2.min.css';
 */
async function initializeTable() {
    

    new DataTable('#datatable', {
        
        
        "pagingType": "full_numbers"

        
    });
}
document.addEventListener("DOMContentLoaded", function () {
    initializeTable();
});