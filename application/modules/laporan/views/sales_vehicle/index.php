<div class="page-content-wrapper">
    <div class="page-content">
        <div class="portlet box blue-hoki">
            <div class="portlet-title">
                <div class="caption">
                    <?php echo $title; ?>
                </div>
            </div>
            <div class="portlet-body">
                <div class="form-inline">
                   <!--  <div class="input-group">
                        <div class="input-group-addon">Tanggal Keberangkatan</div> -->
                        <input type="hidden" class="form-control input-small date" id="datefrom" placeholder="yyyy-mm-dd" value="<?php echo date('Y-m-d'); ?>">
                        <!-- <div class="input-group-addon"><i class="icon-calendar"></i></div>
                    </div> -->     

                    <div class="input-group select2-bootstrap-prepend">
                        <div class="input-group-addon">SHIFT </div>
                        <select id="select2-button-addons-single-input-group-sm" class="form-control js-data-example-ajax select2" dir="">
                            <option value="1">ALL</option>
                            <option value="1">PAGI</option>
                            <option value="2">SIANG</option>
                            <option value="3">MALEM</option>
                        </select>
                    </div>

                    <div class="input-group select2-bootstrap-prepend">
                        <div class="input-group-addon">Pelabuhan </div>
                        <select id="select2-button-addons-single-input-group-sm" class="form-control js-data-example-ajax select2" dir="">
                            <option value="1">ALL</option>
                            <option value="1">BAKAHEUNI</option>
                            <option value="2">MERAK</option>
                        </select>
                    </div>

                   <div class="input-group select2-bootstrap-prepend">
                        <div class="input-group-addon">REGU </div>
                        <select id="select2-button-addons-single-input-group-sm" class="form-control js-data-example-ajax select2" dir="">
                            <option value="1">ALL</option>
                            <option value="1">REGU 1</option>
                            <option value="2">REGU 2</option>
                            <option value="3">REGU 3</option>
                            <option value="4">REGU 4</option>
                        </select>
                    </div> 

                    <div class="input-group select2-bootstrap-prepend">
                        <div class="input-group-addon">LOKET</div>
                        <select id="select2-button-addons-single-input-group-sm" class="form-control js-data-example-ajax select2" dir="">
                            <option value="1">ALL</option>
                            <option value="1">LOKET 1</option>
                            <option value="2">LOKET 2</option>
                            <option value="3">LOKET 3</option>
                            <option value="4">LOKET 4</option>
                        </select>
                    </div>
                    <div class="input-group">
                        <div class="input-group-addon">TANGGAL</div>
                        <input class="form-control input-small date" id="dateto" placeholder="yyyy-mm-dd" value="<?php echo date('Y-m-d'); ?>">
                        <div class="input-group-addon"><i class="icon-calendar"></i></div>
                    </div>

                    <div class="input-group select2-bootstrap-prepend">
                        <div class="input-group-addon">JAM </div>
                        <select id="select2-button-addons-single-input-group-sm" class="form-control js-data-example-ajax select2" dir="">
                            <option value="1">13:00</option>
                            <option value="2">14:00</option>
                            <option value="3">15:00</option>
                            <option value="4">16:00</option>
                        </select>
                    </div>
                    <button type="button" class="btn btn-info" id="cari" data-loading-text="<i class='fa fa-spinner fa-spin'></i> Mencari...">Cari</button>
                </div> 
                <div style="min-height:50px;"></div>               
                <table class="table table-bordered table-hover table-striped" id="tblshipincome">
                    <thead>
                        <tr>
                            <!-- <th rowspan="2">NO</th> -->
                            <!-- <th rowspan="2">JENIS TIKET</th> -->
                            <!-- <th colspan="2">SALDO AWAL</th> -->
                            <!-- <th colspan="2">PENJUALAN</th> -->
                            <!-- <th rowspan="2">TARIF<br>(Rp.)</th>
                            <th rowspan="2">JUMLAH<br>(Rp.)</th>
                            <th colspan="2">SALDO AKHIR</th> -->
                            <!-- <th rowspan="2">Action</th> -->

                            <!-- <th rowspan="2">NO</th> -->
                            <th>NO</th>
                            <th >JENIS TIKET</th>
                            <!-- <th colspan="2">SALDO AWAL</th> -->
                            <th >PENJUALAN</th>
                            <th >TARIF<br>(Rp.)</th>
                            <th >JUMLAH<br>(Rp.)</th>
                            <!-- <th >TOTAL</th> -->
                            <!-- <th rowspan="2">Action</th> -->
                        </tr>
                        <!-- <tr> -->
                            <!-- <th>Nomor Seri</th> -->
                            <!-- <th>Lembar</th> -->
                            <!-- <th>Nomor Seri</th> -->
                            <!-- <th>Lembar</th> -->
                            <!-- <th>Nomor Seri</th> -->
                            <!-- <th>Lembar</th> -->
                            <!-- <th></th> -->
                        <!-- </tr> -->
                    </thead>
                    <tbody></tbody>
                    <tfoot> 
                            <th ></th>
                            <th  class="text-center">Jumlah</th>                        
                            <th></th>
                            <th ></th>
                            <th ></th>
                    </tfoot>
                </table>
            </div>
        </div>        
    </div>
</div>
<script type="text/javascript">
var shipincome= {
    loadData: function() {
        var numericColumn = [3,4];
        var buttonCommon = {
            exportOptions: {
                format: {
                    body: function(data, column, row, node) {
                        return numericColumn.indexOf(column) >= 0 ? parseInt(data.toString().replace(/\./g, '')) : data;
                    }
                }
            }
        };

        $('#tblshipincome').DataTable({
            "ajax": {
                // "url": "<?php echo site_url('laporan/ship_income') ?>",
                "url": "<?php echo site_url('local_data/sales_vehicle.json') ?>",
                "type": "POST",
                "data": function(d) {
                    d.datefrom= document.getElementById('datefrom').value;
                    d.dateto = document.getElementById('dateto').value;
                },
            },

            "serverSide": true,
            "processing": true,
            "rowCallback": function (nRow, aData, iDisplayIndex) {
                 var oSettings = this.fnSettings ();
                 $("td:first", nRow).html(oSettings._iDisplayStart+iDisplayIndex +1);
                 return nRow;
            },
            "columns": [
                // {"data": "number", "orderable": false, "searchable": false, "className": "text-center"},                   

                {"data": "kelas"},
                {"data": "kelas", "orderable": true},
                // {"data": "no_seri_awal", "orderable": true,"className": "text-center"},
                // {"data": "lembar_awal","orderable": true,"className": "text-center"},
                // {"data": "no_seri_jual","orderable":true,"className": "text-center"},
                {"data": "lembar_jual","orderable":true,"className": "text-center"},
                {"data": "tarif","orderable":true,"className": "text-right", "render": $.fn.dataTable.render.number( '.', ',', 0, 'Rp ' )},
                {"data": "total","orderable":true,"className": "text-right sum", "render": $.fn.dataTable.render.number( '.', ',', 0, 'Rp ' )},

                // { "data": null,
                //     "className": "sum text-right",
                //     render: function(data, type, row) {
                //         return data.lembar_jual * data.tarif;
                //     }
                // },
                // {"data": "no_seri_akhir","orderable":true,"className": "text-center"},
                // {"data": "lembar_akhir","orderable":true,"className": "text-center"}
            ],
            // "footerCallback": function(row, data, start, end, display) {
            //     var api = this.api();

            //     api.columns('.sum', { page: 'current' }).every(function () {
            //         var sum = api
            //             .cells( null, this.index(), { page: 'current'} )
            //             .render('display')
            //             .reduce(function (a, b) {
            //                 var x = parseFloat(a) || 0;
            //                 var y = parseFloat(b) || 0;
            //                 return x + y;
            //             }, 0);
            //         //console.log(this.index() +' '+ sum); //alert(sum);
            //         $(this.footer()).html(sum);
            //     });
            // },

            "footerCallback": function ( row, data, start, end, display ) {
            var api = this.api(), data;
 
            // Remove the formatting to get integer data for summation
            var numFormat = $.fn.dataTable.render.number( '\.', '.', 0, 'Rp ' ).display;
            var intVal = function ( i ) {
                return typeof i === 'string' ?
                    i.replace(/[\$,]/g, '')*1 :
                    typeof i === 'number' ?
                        i : 0;
            };

            // Total over all pages
            // total = api
            //     .column( 3,2 )
            //     .data()
            //     .reduce( function (a, b) {
            //         return intVal(a) + intVal(b);
            //     }, 0 );


 
            // Total over this page
            pageTotal = api
                .column( 4, { page: 'current'} )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );


            pageTotal2 = api
                .column( 3, { page: 'current'} )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );

            pageTotal1 = api
            .column( 2, { page: 'current'} )
            .data()
            .reduce( function (a, b) {
                return intVal(a) + intVal(b);
            }, 0 );

            // Update footer
            $( api.column( 4 ).footer() ).html(
                numFormat(pageTotal)
            );

            $( api.column( 3 ).footer() ).html(
                numFormat(pageTotal2)
            );

            $( api.column( 2 ).footer() ).html(
                pageTotal1
            );
        },
           
            "language": {
                "aria": {
                    "sortAscending": ": activate to sort column ascending",
                    "sortDescending": ": activate to sort column descending",
                },
                "processing": "Proses.....",
                "emptyTable": "Tidak ada data",
                "info": "Total _TOTAL_ data",
                "infoEmpty": "Total 0 data",
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
            "rowGroup": {
                "dataSrc": "kapal"
            },

            "bStateSave": true,
            "bInfo": false,
            "searching":false,
            "pageLength": -1,
            "pagingType": "bootstrap_full_number",
            "paging": false,
            "order": [[2, "desc" ]],
            "fixedHeader": {
                "headerOffset": $('.navbar-fixed-top').outerHeight()
            },

            // "fnDrawCallback": function(data) {
            //     $('#total').html('<h4>Total Pendapatan Rp.'+data.json.total+'</h4>');
            // },

            "initComplete": function (data) {
                var $searchInput = $('div.dataTables_filter input');
                var data_tables = $('#tblshipincome').DataTable();
                $searchInput.unbind();
                $searchInput.bind('keyup', function (e) {
                    if (e.keyCode == 13 || e.whiche == 13) {
                        data_tables.search(this.value).draw();
                    }
                });
            }, 

            // dom: 'B<"table-scrollable"t>ri',
            // "buttons": [
            //     <?php if($download_excel){ ?>
            //         $.extend(true, {}, buttonCommon, {
            //             extend: 'excelHtml5',
            //             exportOptions: {
            //                 columns: ':not(:last-child)',
            //             }
            //         }),
            //     <?php } ?>

            //     <?php if($download_pdf){ ?>
            //         $.extend(true, {}, {}, {
            //             extend: 'pdfHtml5',
            //             orientation: 'landscape',
            //             exportOptions: {
            //                 columns: ':not(:last-child)',
            //             },
            //             customize: function(doc) {
            //                 doc.content[1].table.widths = [20, 100, 120, 100, 90, 100, 100, 75];
            //                 var iColumns = $('#tblshipincome thead th').length;
            //                 var rowCount = document.getElementById("tblshipincome").rows.length;

            //                 for (i = 0; i < rowCount; i++) {
            //                     doc.content[1].table.body[i][0].alignment = 'center';
            //                     doc.content[1].table.body[i][3].alignment = 'center';
            //                     doc.content[1].table.body[i][4].alignment = 'right';
            //                     doc.content[1].table.body[i][5].alignment = 'center';
            //                     doc.content[1].table.body[i][6].alignment = 'right';
            //                     doc.content[1].table.body[i][7].alignment = 'right';

            //                 }
            //             }
            //         }),
            //     <?php } ?>
            // ],

            dom: 'B<"table-scrollable"t>ri',
            "buttons": [

                    <?php if($download_excel){ ?>
                    $.extend(true, {}, buttonCommon, {
                        extend: 'excelHtml5',
                        exportOptions: {
                        columns: [  1, 2, 3, 4  ]
                        }
                    }),
                    <?php } ?>

                    <?php if($download_pdf){ ?>
                    $.extend(true, {}, {}, {
                        extend: 'pdfHtml5',
                        orientation: 'landscape',
                        // exportOptions: {
                        //     columns: ':not(:last-child)',
                        // },
                        // customize: function(doc) {
                        //     doc.content[1].table.widths = [20, 100, 120, 100 ];
                        //     var iColumns = $('#tblshipincome thead th').length;
                        //     var rowCount = document.getElementById("tblshipincome").rows.length;

                        //     for (i = 0; i < rowCount; i++) {
                        //         doc.content[1].table.body[i][0].alignment = 'center';
                        //         doc.content[1].table.body[i][1].alignment = 'center';
                        //         doc.content[1].table.body[i][2].alignment = 'right';
                        //         doc.content[1].table.body[i][3].alignment = 'center';


                        //     }
                        // }

                        exportOptions: {
                        columns: [  1, 2, 3,4  ]
                        },
                        customize: function ( doc ) {
                        doc.content[1].table.widths = [
                        '50%',
                        '10%',
                        '20%',
                        '20%',
                        ];


                            var col= $("#tblshipincome").DataTable().page.info().recordsTotal

                            for (var i=1 ; i<=col;i++)
                            {

                                doc.content[1].table.body[i][0].alignment = 'center';
                                doc.content[1].table.body[i][1].alignment = 'center';
                                doc.content[1].table.body[i][2].alignment = 'right';
                                doc.content[1].table.body[i][3].alignment = 'right';
                            }
                        }
                    }),

                    <?php } ?>

            ],
        });

        $('#export_tools  > a.tool-action').on('click', function() {
            var data_tables = $('#tblshipincome').DataTable();
            var action = $(this).attr('data-action');

            data_tables.button(action).trigger();
        });
    },

    reload: function() {
        $('#tblshipincome').DataTable().ajax.reload();
    },

    init: function() {
        if (!jQuery().DataTable) {
            return;
        }

        this.loadData();
    }
};

jQuery(document).ready(function () {
    shipincome.init();

    $('#datefrom').datepicker({
        format: 'yyyy-mm-dd',
        changeMonth: true,
        changeYear: true,
        autoclose: true,
        endDate: new Date(),
    }).on('changeDate',function(e) {
        $('#dateto').datepicker('setStartDate', e.date)
    });

    $('#dateto').datepicker({
        format: 'yyyy-mm-dd',
        changeMonth: true,
        changeYear: true,
        autoclose: true,
        startDate: $('#datefrom').val(),
        endDate: new Date(),
    }).on('changeDate',function(e) {
        $('#datefrom').datepicker('setEndDate', e.date)
    });
    
    $("#cari").on("click",function(){
        $(this).button('loading');
        shipincome.reload();
        $('#tblshipincome').on('draw.dt', function() {
            $("#cari").button('reset');
        });
    });

    setTimeout(function() {
        $('.menu-toggler').trigger('click');
    }, 1);
});
</script>
