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
                    <div class="row">
                        <div class="col-md-12">
                                                        
                            <input type="hidden" class="form-control input-small date" id="datefrom" placeholder="yyyy-mm-dd" value="<?php echo date('Y-m-d'); ?>">

                            <div class="input-group select2-bootstrap-prepend">
                                <div class="input-group-addon">PELABUHAN </div>
                                <select id="select2-button-addons-single-input-group-sm" class="form-control js-data-example-ajax select2" dir="">
                                    <option value="0">All</option>
                                    <option value="1">Merak</option>
                                    <option value="2">Bakauheni</option>
                                    <option value="3">Gilimanuk</option>
                                    <option value="4">Ketapang</option>
                                </select>
                            </div>   
                            <div class="input-group select2-bootstrap-prepend">
                                <div class="input-group-addon">KAPAL </div>
                                <select id="select2-button-addons-single-input-group-sm" class="form-control js-data-example-ajax select2" dir="">
                                    <option value="0">All</option>
                                    <option value="1">RO-RO</option>
                                    <option value="2">NUSA JAYA</option>
                                    <option value="3">PANORAMA NUSANTARA</option>
                                </select>
                            </div>
                            <div class="input-group select2-bootstrap-prepend">
                                <div class="input-group-addon">SHIFT </div>
                                <select id="select2-button-addons-single-input-group-sm" class="form-control js-data-example-ajax select2" dir="">
                                    <option value="1">Pagi</option>
                                    <option value="2">Siang</option>
                                    <option value="3">Malam</option>
                                </select>
                            </div>        
                            <div class="input-group select2-bootstrap-prepend">
                                <div class="input-group-addon">REGU </div>
                                <select id="select2-button-addons-single-input-group-sm" class="form-control js-data-example-ajax select2" dir="">
                                    <option value="1">REGU 1</option>
                                    <option value="2">REGU 2</option>
                                    <option value="3">REGU 3</option>
                                    <option value="4">REGU 4</option>
                                </select>
                            </div> 
                            
                            <div class="input-group select2-bootstrap-prepend">
                                <div class="input-group-addon">LOKET</div>
                                <select id="select2-button-addons-single-input-group-sm" class="form-control js-data-example-ajax select2" dir="">
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
                    </div>
                </div>                
                <table class="table table-bordered table-hover table-striped" id="tblshipincome">
                    <thead>
                        <tr>
                            <!-- <th>NO</th> -->
                            <th>JENIS TIKET</th>
                            <th>PENJUALAN</th>
                            <th>TARIF<br>(Rp.)</th>
                            <th>JUMLAH<br>(Rp.)</th>
                        </tr>
                       
                    </thead>
                    <tbody></tbody>
                    <tfoot>
                        <th>Jumlah</th>                        
                        <th></th>
                        <th></th>
                        <th></th>
                    </tfoot>
                </table>
            </div>
        </div>        
    </div>
</div>
<script type="text/javascript">
var shipincome= {
    loadData: function() {
        var numericColumn = [3, 4, 5, 6, 7];
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
                "url": "<?php echo site_url('local_data/sales_passengers.json') ?>",
                "type": "POST",
                "data": function(d) {
                    d.datefrom= document.getElementById('datefrom').value;
                    d.dateto = document.getElementById('dateto').value;
                },
            },

            "serverSide": true,
            "processing": true,
            "columns": [
                // {"data": "number", "orderable": false, "searchable": false, "className": "text-center"},                   
               
                // {"data": null,
                //     render: function (data, type, row, meta) {
                //         return meta.row + meta.settings._iDisplayStart + 1;
                //     }
                // },
                {"data": "kelas", "orderable": true},
                {"data": "lembar_jual","orderable":true,"className": "text-center sum"},
                {"data": "tarif","orderable":true,"className": "text-right", 
                    render: $.fn.dataTable.render.number( '.', ',', 0, '' )
                },
                {"data": "jumlah","orderable":true,"className": "text-right sum", 
                    render: $.fn.dataTable.render.number( '.', ',', 0, '' )
                }        
            ],

            "footerCallback": function(row, data, start, end, display) {
                var api = this.api();

                api.columns('.sum', { page: 'current' }).every(function () {
                    var sum = api
                        .cells( null, this.index(), { page: 'current'} )
                        .render('display')
                        .reduce(function (a, b) {
                            var x = parseFloat(a) || 0;
                            var y = parseFloat(b) || 0;
                            return x + y;
                        }, 0);
                    //console.log(this.index() +' '+ sum); //alert(sum);
                    $(this.footer()).html(sum);
                });
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
            // "order": [[2, "desc" ]],
            "fixedHeader": {
                "headerOffset": $('.navbar-fixed-top').outerHeight()
            },

            "fnDrawCallback": function(data) {
                $('#total').html('<h4>Total Pendapatan Rp.'+data.json.total+'</h4>');
            },

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

            dom: 'B<"table-scrollable"t>ri',
            "buttons": [
                <?php if($download_excel){ ?>
                    $.extend(true, {}, buttonCommon, {
                        extend: 'excelHtml5',
                        exportOptions: {
                            columns: ':not(:last-child)',
                        }
                    }),
                <?php } ?>

                <?php if($download_pdf){ ?>
                    $.extend(true, {}, {}, {
                        extend: 'pdfHtml5',
                        orientation: 'landscape',
                        exportOptions: {
                            columns: ':not(:last-child)',
                        },
                        customize: function(doc) {
                            doc.content[1].table.widths = [20, 100, 120, 100, 90, 100, 100, 75];
                            var iColumns = $('#tblshipincome thead th').length;
                            var rowCount = document.getElementById("tblshipincome").rows.length;

                            for (i = 0; i < rowCount; i++) {
                                doc.content[1].table.body[i][0].alignment = 'center';
                                doc.content[1].table.body[i][3].alignment = 'center';
                                doc.content[1].table.body[i][4].alignment = 'right';
                                doc.content[1].table.body[i][5].alignment = 'center';
                                doc.content[1].table.body[i][6].alignment = 'right';
                                doc.content[1].table.body[i][7].alignment = 'right';

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
