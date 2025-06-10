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
        order: [[0, 'asc']],
        pageLength:50,
        
        

        
    });
}
document.addEventListener("DOMContentLoaded", function () {
    initializeTable();
});

window.viewNewRow = function(state) {
    
   if (state) {
        
        document.getElementById("newform").reset();
        
        $("#button").fadeOut(function() {
            $("#list").fadeOut(function() {
                $("#new").fadeIn();
            });
        });


    } else {
        $("#new").fadeOut(function() {
            $("#button").fadeIn(function() {
                $("#list").fadeIn();
            });
        });

    }

};

window.modRow = function(b64){
        
    viewNewRow(true);

    var js = JSON.parse(atob(b64));
    for (var prop in js) {
        $("#"+prop).val(js[prop]);
    }


};

window.delRowDB = function(id){
    
    var path = "/delete";
    var fields = {};
    
    fields["id"] = id;
    fields["_token"] = $('[name="_token"]').val();

    var param = JSON.stringify(fields);
    var call = $.ajax({
        type: 'POST',
        url: path,
        data: param, // or JSON.stringify ({name: 'jonas'}),
        success: function(data) {
            
            location.reload();


        },
        error: function() {
            alert("Errore server");
        },
        contentType: "application/json",
        dataType: 'json'

    });
    
};