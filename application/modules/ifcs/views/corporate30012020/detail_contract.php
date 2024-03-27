
<link href="<?php echo base_url(); ?>assets/global/plugins/icheck/skins/all.css" rel="stylesheet" type="text/css" />

<style type="text/css">
    .wajib{color: red}
</style>
<div class="col-md-10 col-md-offset-1">
    <div class="portlet box blue" id="box">
        <?php echo headerForm($title) ?>
        <div class="portlet-body">
            <div class="box-body">
                 <div class="form-group">



                    <table class="table table-striped table-bordered table-hover table-checkable order-column" id="contract">
                        <thead>

                            <tr>
                                <th>NO</th>
                                <th>KODE CORPORATE</th>
                                <th>NAMA CORPORATE</th>
                                <th>REWARD CODE</th>
                                <th>PRIODE AWAL REWARD</th>
                                <th>PRIODE AKHIR REWARD</th>
                                <th>TANGGAL PERHITUNGAN REWARD</th>
                                <th>AWAL REWARD BERLAKU</th>
                                <th>AKHIR REWARD BERLAKU</th>
                                <th>REWARD DIDAPAT (Rp.)</th>
                                <th>TOTAL TRANSAKSI</th>
                                <th>TRANSAKSI REWARD</th>

                            </tr>
                        </thead>
                    </table>

                </div>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo base_url() ?>assets/js/jquery-easyui-1.5.3/jquery.easyui.min.js"></script>
<script type="text/javascript">
var tableContract= {
    loadData: function() {
        $('#contract').DataTable({
            "ajax": {
                "url": "<?php echo site_url('ifcs/corporate/listDetailContract') ?>",
                "type": "POST",
                "data": function(d) {
                    d.agreement_code ="<?php echo $agreement_code; ?>";
                    // d.team = document.getElementById('team').value;
                },
            },


            "serverSide": true,
            "processing": true,
            "columns": [
                    {"data": "no", "orderable": false, "className": "text-center" , "width": 5},
                    {"data": "corporate_code", "orderable": true, "className": "text-left"},
                    {"data": "corporate_name", "orderable": true, "className": "text-left"},
                    {"data": "reward_code", "orderable": true, "className": "text-left"},
                    {"data": "start_date", "orderable": true, "className": "text-left"},
                    {"data": "end_date", "orderable": true, "className": "text-left"},
                    {"data": "adjustment_date", "orderable": true, "className": "text-left"},
                    {"data": "start_date_reward", "orderable": true, "className": "text-left"},
                    {"data": "end_date_reward", "orderable": true, "className": "text-left"},
                    {"data": "reward", "orderable": true, "className": "text-right"},
                    {"data": "total_transaction", "orderable": true, "className": "text-left"},
                    {"data": "adjustment_transaction", "orderable": true, "className": "text-left"},
                    

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
            "pagingType": "bootstrap_full_number",
            "order": [[ 0, "desc" ]],
            "initComplete": function () {
                var $searchInput = $('div.contract_filter input');
                var data_tables = $('#contract').DataTable();
                $searchInput.unbind();
                $searchInput.bind('keyup', function (e) {
                    if (e.keyCode == 13 || e.whiche == 13) {
                        data_tables.search(this.value).draw();
                    }
                });
            },
        });

    },

    reload: function() {
        $('#contract').DataTable().ajax.reload();
    },

    init: function() {
        if (!jQuery().DataTable) {
            return;
        }

        this.loadData();
    }
};    
    $(document).ready(function(){
        tableContract.init();
    })
</script>