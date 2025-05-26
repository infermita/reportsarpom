import './bootstrap';

//import 'jquery/dist/jquery'

//require('metismenu');

//require('raphael');
//require('morris.js/morris');

// require('datatables.net');
// require('datatables.net-bs');
// require('datatables.net-responsive');
// require('datatables.net-responsive-bs');


import JSZip from 'jszip'; // For Excel export
import PDFMake from 'pdfmake'; // For PDF export
import pdfFonts from 'pdfmake/build/vfs_fonts';


import DataTable from 'datatables.net-dt';

import 'datatables.net-buttons';
import 'datatables.net-buttons/js/dataTables.buttons.min.mjs'
import 'datatables.net-buttons/js/buttons.html5.mjs';
import 'datatables.net-buttons/js/buttons.print.mjs';
import 'datatables.net-buttons/js/buttons.colVis.mjs';

window.JSZip = JSZip;

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
        language: {
            "url": "it-IT.json",
        },
        layout: {
            topStart: {
                buttons: ['pageLength','excelHtml5', 'csvHtml5', 'pdfHtml5', 'printHtml5']
            }
        },
        "pagingType": "full_numbers",
        order: [[1, 'asc']],
        pageLength:50,
        
        

        
    });
}
document.addEventListener("DOMContentLoaded", function () {
    initializeTable();
});