$(document).ready(function () {
    var table = $('#example').DataTable({
        "language": {
            "lengthMenu": "Show _MENU_ spools per page",
            "zeroRecords": "Nothing found - sorry",
            "info": "Showing _START_ to _END_ of _TOTAL_ spools",
            "infoEmpty": "No records available",
            "infoFiltered": "(filtered from _MAX_ total spools)"
        },
        "columnDefs": [
            {"visible": false, "targets": 0},
            {"visible": false, "targets": 1},
            {"visible": false, "targets": 8},
            {"type": "date-euro", "targets": 3},
            {"type": "file-size", "targets": 5}
        ],
        "orderClasses": false,
        "order": [0, 'asc'],
        "displayLength": 10,
        "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
        "columns": [
            {className: "my_class"},
            {className: "my_class"},
            {className: "my_filter"},      // status (2) 
            {className: "my_datefilter"}, // date & time (3)
            {className: "my_class"},
            {className: "my_class"},      // size (5)
            {className: "my_class"},
            {className: ""},             // controles
            {}
        ],
        "drawCallback": function (settings) {
            var api = this.api();
            var rows = api.rows({page: 'current'}).nodes();
            var last = null;
            var queue_statusList = new Array();

            api.column(8, {page: 'current'}).data().each(function (group, i){
                queue_statusList.push(group);
            });

            // Merging by the second column 
            api.column(1, {page: 'current'}).data().each(function (group, i) {
                if (last !== group) {
                    if(queue_statusList[i] === 'ready'){
                        var queueStatus = 'ready';
                    }else{
                        var queueStatus = 'held';
                    }                   
                    $(rows).eq(i).before(                           
                            '<tr class="group_'+queueStatus+'"><td colspan="5">' + group + '</td><td>\n\
                                <button type="button" class="btn btn-default btn-xs btn-success queue-start"><span class="glyphicon glyphicon-play"></span></button> \n\
                                    </button> <button type="button" class="btn btn-default btn-xs btn-warning queue-stop"><span class="glyphicon glyphicon-pause"></span></button></td></tr>'
                            );
                    last = group;
                }
            });
        }
    });
    
    // function button delete spool
    $("#example").on('click', '.spool-cancel',function(){
        var dataTable = $('.table').dataTable();
        var check = confirm('Voulez-vous vraiment supprimer cet élément ?');
        if(check === true){
            var row = $(this).closest('tr'); 
            var nRow = row[0];
            dataTable.dataTable().fnDeleteRow(nRow);
            console.log('spool supprimé');
//            map_control -canceljob:XXXX -qname:XXXX;
        }else{
            console.log('suppression annulée');
        }
    });
    
    // function button free spool
    $("#example").on('click','.spool-free',function(){
        console.log("spool free");
//        map_control -freejob:XXXX -qname:XXXX
    });
    
    // function button hold spool
    $("#example").on('click','.spool-hold',function(){
        console.log("spool held");
//        map_control -holdjob:XXXX -qname:XXXX
    });
    
    // function button start queue
    $("#example").on('click','.queue-start',function(){
        console.log("queue start");
//        map_control -startq:XXXX
    });
    
    // function button stop queue
    $("#example").on('click','.queue-stop',function(){
        console.log("queue stop");
//        map_control -stopq:XXXX
    });
    
    // Setup - add a text input to each footer cell
    $('#example tfoot th.my_class').each(function () {
//        var title = $(this).text(); // recupere le "nom" de la colonne (ex : id, size ...)
        $(this).html('<input type="text" class="form-control" placeholder="Search" />');
    });
    
    // Apply the search
    table.columns('.my_class').every(function () {
        var that = this;
        $('input', this.footer()).on('keyup change', function () {
            if (that.search() !== this.value) {
                that
                        .search(this.value)
                        .draw();
            }
        });
    });
    table.columns('.my_filter').every(function () {
        var column = this;
        var select = $('<select class="form-control"><option value=""></option></select>')
                .appendTo($(column.footer()).empty())
                .on('change', function () {
                    var val = $.fn.dataTable.util.escapeRegex(
                            $(this).val()
                            );
                    column
                            .search(val, true, false)
                            .draw();
                });
                var i = 0;
        column.data().unique().sort().each(function (d, j) {
                if(d.indexOf("processing") !== -1){
                    if(i === 0){
                        d = "processing";
                        select.append('<option value="' + d + '">' + d + '</option>');
                        i++;
                    }
                }else{
                    select.append('<option value="' + d + '">' + d + '</option>');
                }
        });
    });


    // date range picker
    $('#example tfoot th.my_datefilter').each(function () {
        $(this).html('<form class="form-inline"><div class="form-group"><input class="form-control date_range_filter date" type="text" id="datepicker_from"/></div></form>');
    });
    
    function getStartDate() {
    var date = Date.now();
    table.columns('.my_datefilter').every(function () {
        var column = this;
        var tableau = new Array();
        column.data().unique().sort().each(function (d, j) {
            var dateTime = d.split(' '),
            timeParts = dateTime[1].split(':'),
            dateParts = dateTime[0].split('/');
            date = new Date(dateParts[2], dateParts[1] - 1, dateParts[0], timeParts[0], timeParts[1], timeParts[2]).getTime();
            tableau.push(date);
        });
        var tLen, i;
        tLen = tableau.length;
        for (i = 0; i < tLen; i++) {
             if(tableau[i]<date){
                 date = tableau[i];
             }
        }       
    });
    start_date = new Date(date);
    return start_date;
   };
    
    function getEndDate() {
    var date = 0000000000;
    table.columns('.my_datefilter').every(function () {
        var column = this;
        var tableau = new Array();
        column.data().unique().sort().each(function (d, j) {
            var dateTime = d.split(' '),
            timeParts = dateTime[1].split(':'),
            dateParts = dateTime[0].split('/');
            date = new Date(dateParts[2], dateParts[1] - 1, dateParts[0], timeParts[0], timeParts[1], timeParts[2]).getTime();
            tableau.push(date);
        });
        var tLen, i;
        tLen = tableau.length;
        for (i = 0; i < tLen; i++) {
             if(tableau[i]>date){
                 date = tableau[i];
             }
        }       
    });
    end_date = new Date(date);
    return end_date;
   };

   $("#datepicker_from").daterangepicker({
        timePicker: true,
        timePickerIncrement: 30,
        startDate: getStartDate(),
        endDate: getEndDate(),
        locale: {
            format: 'DD/MM/YYYY HH:mm:ss'
        }
   });
   
   $.fn.dataTable.ext.search.push(
    function( settings, data, dataIndex ) {
       var id = $('#datepicker_from').val(); 
       var number = data[3];                
       var dates = number.split(' ');
       var dateSelection = id.split('-');
       var dateDate = dates[0].split('/');
       var dateTime = dates[1].split(':');
       var dateTimestamp = new Date(dateDate[2], dateDate[1] - 1, dateDate[0], dateTime[0], dateTime[1], dateTime[2] ).getTime();
       var dateStart = dateSelection[0].split(' ');
       var dateStartDate = dateStart[0].split('/');
       var dateStartTime = dateStart[1].split(':');
       var start = new Date(dateStartDate[2], dateStartDate[1] - 1, dateStartDate[0], dateStartTime[0], dateStartTime[1], dateStartTime[2] ).getTime();
       var dateEnd = dateSelection[1].split(' ');
       var dateEndDate = dateEnd[1].split('/');
       var dateEndTime = dateEnd[2].split(':');
       var end = new Date(dateEndDate[2], dateEndDate[1] - 1, dateEndDate[0], dateEndTime[0], dateEndTime[1], dateEndTime[2] ).getTime();

        if ((start <= dateTimestamp) && (dateTimestamp <= end))
        {
            return true;
        }
        return false;
    }
);

$('#datepicker_from').change( function() {
        table.draw();
    } );

// filter of name of queue
    var t = $('<label>Queue name: <div id="select-queue"></div>  </label>');
    $(table.table().container()).find('.dataTables_filter').prepend(t);
    table.columns(0).every(function () {
        var column = this;
        var select = $('<select class="form-control"><option value=""></option></select>')
                .appendTo($('#select-queue').empty())
                .on('change', function () {
                    var val = $.fn.dataTable.util.escapeRegex(
                            $(this).val()
                            );
                    column
                            .search(val ? '^' + val + '$' : '', true, false)
                            .draw();
                });
        column.data().unique().sort().each(function (d, j) {
            select.append('<option value="' + d + '">' + d + '</option>');
        });
    });
    
// filter of status of queue
var t = $('<label>Queue status: <div id="select-queue"></div>  </label>');
    $(table.table().container()).find('.dataTables_filter').prepend(t);
    table.columns(8).every(function () {
        var column = this;
        var select = $('<select class="form-control"><option value=""></option></select>')
                .appendTo($('#select-queue').empty())
                .on('change', function () {
                    var val = $.fn.dataTable.util.escapeRegex(
                            $(this).val()
                            );
                    column
                            .search(val ? '^' + val + '$' : '', true, false)
                            .draw();
                });
        column.data().unique().sort().each(function (d, j) {
            select.append('<option value="' + d + '">' + d + '</option>');
        });
    });
});