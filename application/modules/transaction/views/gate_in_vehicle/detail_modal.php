<div class="col-md-12 col-md-offset-0" >
    <div class="portlet box blue" id="box" >
        <?php echo headerForm($title) ?>
        <div class="portlet-body" >

            <div class="row">
                <div class="col-md-12">
                       
                    <div class="portlet-body">
                        <div class="table-toolbar">
                            <div class="row">
                                
                                <div class="col-lg-3 col-md-4 col-xs-12">
                                    <div class="mt-element-ribbon bg-grey-steel">
                                        <div class="ribbon ribbon-color-primary uppercase">Total Gete In Penumpang Kendaraan</div>
                                        <p class="ribbon-content" id="total"><?php echo $total_passanger; ?></p>
                                    </div>
                                </div>

                            </div>

                        </div>
                        <div class="col-md-12">
                            <div class="table">
                                <table class="table table-striped table-bordered table-hover" id="dataTables2">
                                    <thead>
                                        <tr>
                                            <th colspan="16" style="text-align: left">DATA PENUMPANG KENDARAAN</th>
                                        </tr>
                                        <tr>

                                            <th>NO</th>
                                            <th>TANGGAL CHECKIN</th>
                                            <th>KODE BOOKING</th>
                                            <th>NOMER TICKET</th>
                                            <th>NAMA PENUMPANG</th>
                                            <th>UMUR</th>
                                            <th>JENIS KELAMIN</th>
                                            <th>NOMER INVOICE</th>
                                            <th>SERVIS</th>
                                            <th>PERANGKAT GATE IN</th>
                                        
                                        </tr>
                                    </thead>
                                    <tbody id="data_body">                                
                                    </tbody>
                                    <tfoot></tfoot>
                                </table>
                            </div>
                        </div>

                    </div>
                </div>
                        <!-- END EXAMPLE TABLE PORTLET-->
            </div>

        </div>
    </div>
</div>

<script type="text/javascript">

    function addCommas(nStr)
    {
        nStr += '';
        x = nStr.split('.');
        x1 = x[0];
        x2 = x.length > 1 ? '.' + x[1] : '';
        var rgx = /(\d+)(\d{3})/;
        while (rgx.test(x1)) {
            x1 = x1.replace(rgx, '$1' + '.' + '$2');
        }
        return x1 + x2;
    }

    $(document).ready(function(){

        var t = $('#dataTables2').DataTable({
	            "ajax": {
	                "url": "<?php echo site_url('transaction/gate_in_vehicle/listDetail') ?>",
	                "type": "POST",
                    "data" : function(d){
                        d.id = "<?php echo $id ?>"
                    },
                    dataSrc :function(x)
                    {
                        // seting csrf
                        let getTokenName = x.csrfName;
                        let getToken = x.csrfToken;			
                        csfrData[getTokenName] = getToken;
                        $.ajaxSetup({
                            data: csfrData
                        });

                        return x.data
                    }                        
	            },
	            "processing": true,
	            "columns": [
	                    {"data": "no", "width": 5},
	                    {"data": "created_on", "orderable": true, "className": "text-left"},
	                    {"data": "booking_code", "orderable": true, "className": "text-left"},
	                    {"data": "ticket_passanger", "orderable": true, "className": "text-left"},
	                    {"data": "passanger_name", "orderable": true, "className": "text-left"},
	                    {"data": "age", "orderable": true, "className": "text-left"},
	                    {"data": "gender", "orderable": true, "className": "text-left"},
	                    {"data": "trans_number", "orderable": true, "className": "text-left"},
	                    {"data": "service_name", "orderable": true, "className": "text-left"},
                        {"data": "terminal_name", "orderable": true, "className": "text-left"},
                        
	            ],
	            "language": {
	                "aria": {
	                    "sortAscending": ": activate to sort column ascending",
	                    "sortDescending": ": activate to sort column descending"
	                },
	                  "processing": "Proses.....",
	                  "emptyTable": "Tidak ada data",
	                  "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
	                  "infoEmpty": "Menampilkan 0 sampai 0 dari 0 entri",
	                  "infoFiltered": "(disaring dari _MAX_ entri keseluruhan)",
	                  "lengthMenu": "Menampilkan _MENU_",
	                  "search": "Pencarian :",
	                  "zeroRecords": "Tidak ditemukan data yang sesuai",
	                  "paginate": {
	                    "previous": "Sebelumnya",
	                    "next": "Selanjutnya",
	                    "last": "Terakhir",
	                    "first": "Pertama"
	                }
	            },
	            "lengthMenu": [
	                [10, 25, 50, 100],
	                [10, 25, 50, 100]
	            ],
	            "pageLength": 10,
	            "searching":true,
	            "pagingType": "bootstrap_full_number",
	            "order": [[ 0, "desc" ]],
	            "initComplete": function () {
	                var $searchInput = $('div #dataTables2_filter input');
	                var data_tables = $('#dataTables2').DataTable();
	                $searchInput.unbind();
	                $searchInput.bind('keyup', function (e) {
	                    if (e.keyCode == 13 || e.whiche == 13) {
	                        data_tables.search(this.value).draw();
	                    }
	                });
	            },

	            "columnDefs" : [
	                {
	                    targets : [3],
	                    visible:<?php echo $gs ?>

	                },
                                {
                        "searchable": false,
                        "orderable": false,
                        "targets": 0
                    }
	           ]
	        });

            t.on( 'draw.dt', function () {
                var PageInfo = $('#dataTables2').DataTable().page.info();
                    t.column(0, { page: 'current' }).nodes().each( function (cell, i) {
                        cell.innerHTML = i + 1 + PageInfo.start;
                    } );
                } );            

    })



</script>