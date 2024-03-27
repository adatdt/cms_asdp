<div class="col-md-12 col-md-offset-0" >
    <div class="portlet box blue" id="box" >
        <?php echo headerForm($title) ?>
        <div class="portlet-body" >

            <div class="row">
                <div class="col-md-12">
                       
                    <div class="portlet-body">
                        <div class="table-toolbar">
                            <div class="row">

                                <div class="col-md-3">
                                    <!-- BEGIN WIDGET THUMB -->
                                    <div class="widget-thumb widget-bg-color-white text-uppercase margin-bottom-20 bordered">
                                        <h4 class="widget-thumb-heading">TANGGAL CLOSING</h4>
                                        <div class="widget-thumb-wrap">
                                            <!-- <i class="widget-thumb-icon bg-green icon-bulb"></i> -->
                                            <div class="widget-thumb-body">
                                                <!-- <span class="widget-thumb-subtitle">USD</span> -->
                                                <span class="widget-thumb-body-stat" data-counter="counterup" >
                                                    <?php echo format_date($get_name->close_date); ?> 
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- END WIDGET THUMB -->
                                </div>

                                <div class="col-md-3">
                                    <!-- BEGIN WIDGET THUMB -->
                                    <div class="widget-thumb widget-bg-color-white text-uppercase margin-bottom-20 bordered">
                                        <h4 class="widget-thumb-heading">KODE OPENING BALANCE</h4>
                                        <div class="widget-thumb-wrap">
                                            <!-- <i class="widget-thumb-icon bg-green icon-bulb"></i> -->
                                            <div class="widget-thumb-body">
                                                <!-- <span class="widget-thumb-subtitle">USD</span> -->
                                                <span class="widget-thumb-body-stat" data-counter="counterup" >
                                                    <?php echo $ob_code; ?> 
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- END WIDGET THUMB -->
                                </div>

                                <div class="col-md-3">
                                    <!-- BEGIN WIDGET THUMB -->
                                    <div class="widget-thumb widget-bg-color-white text-uppercase margin-bottom-20 bordered">
                                        <h4 class="widget-thumb-heading">NAMA PETUGAS</h4>
                                        <div class="widget-thumb-wrap">
                                            <!-- <i class="widget-thumb-icon bg-green icon-bulb"></i> -->
                                            <div class="widget-thumb-body">
                                                <!-- <span class="widget-thumb-subtitle">USD</span> -->
                                                <span class="widget-thumb-body-stat" data-counter="counterup" >
                                                    <!-- <span id="total"></span> -->
                                                    <span><?php echo $get_name->full_name; ?></span>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- END WIDGET THUMB -->
                                </div>

                                <?php foreach($total_transaction as $key=>$value) { ?>

                                    <div class="col-md-3">
                                    <!-- BEGIN WIDGET THUMB -->
                                    <div class="widget-thumb widget-bg-color-white text-uppercase margin-bottom-20 bordered">
                                        <h4 class="widget-thumb-heading"><?php echo strtoupper($value->payment_type)  .", TOTAL : ".$value->total_transaction ?> </h4>
                                        <div class="widget-thumb-wrap">
                                            <div class="widget-thumb-body">
                                                <span class="widget-thumb-body-stat" data-counter="counterup" >
                                                    <span>Rp. <?php echo idr_currency($value->amount) ?></span>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- END WIDGET THUMB -->
                                </div>

                                <?php } ?>

                            </div>

                        </div>

                        <div class="col-md-12">
<!--                             <div class="table-scrollable"> -->
                                <table class="table table-striped table-bordered table-hover" id="dataTables">
                                    <thead>
                                        <tr>
                                            <th colspan="16" style="text-align: left">DATA TRANSAKSI POS</th>
                                        </tr>
                                        <tr>
                                            <th>NO</th>
                                            <th>NOMER INVOICE</th>
                                            <th>AMOUNT (Rp)</th>
                                            <th>TIPE PEMBAYARAN</th>
                                            <th>BOOKING CHANNEL</th>
                                            <th>NAMA PERANGKAT</th>
                                        </tr>
                                    </thead>
                                    <tbody> 
                                        <?php $no=1; foreach($detail as $key=>$value) { ?>
                                        <tr>
                                            <td><?php echo $no; ?></td>
                                            <td><?php echo $value->trans_number; ?></td>
                                            <td style="text-align: right;"><?php echo idr_currency($value->amount); ?></th>
                                            <td><?php echo $value->payment_type; ?></td>
                                            <td><?php echo $value->booking_channel; ?></td>
                                            <td><?php echo $value->terminal_name; ?></td>
                                        </tr>
                                        <?php $no++; }?>

                                    </tbody>
                                    <tfoot></tfoot>
                                </table>
                            <!-- </div> -->
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

    var table= {
        loadData: function() {
            $('#dataTables').DataTable({
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
                "pagingType": "bootstrap_full_number",
                "order": [[ 0, "asc" ]],
                "initComplete": function () {
                    var $searchInput = $('div.dataTables_filter input');
                    var data_tables = $('#dataTables').DataTable();
                    $searchInput.unbind();
                    $searchInput.bind('keyup', function (e) {
                        if (e.keyCode == 13 || e.whiche == 13) {
                            data_tables.search(this.value).draw();
                        }
                    });
                },
            });

            $('#export_tools > li > a.tool-action').on('click', function() {
                var data_tables = $('#dataTables').DataTable();
                var action = $(this).attr('data-action');

                data_tables.button(action).trigger();
            });
        },

        reload: function() {
            $('#dataTables').DataTable().ajax.reload();
        },

        init: function() {
            if (!jQuery().DataTable) {
                return;
            }

            this.loadData();
        }
    };


    $(document).ready(function(){

        // get_data();
        table.init();
    })
</script>