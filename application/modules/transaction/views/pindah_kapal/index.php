<style type="text/css">
.wajib{
    color: red;
}
</style>
<div class="page-content-wrapper">
    <div class="page-content">
        <div class="page-bar">
            <ul class="page-breadcrumb">
                <li>
                    <?php echo '<a href="' . $url_home . '">' . $home . '</a>'; ?>
                    <i class="fa fa-circle"></i>
                </li>
                <li>
                    <span><?php echo $title; ?></span>
                </li>
            </ul>
            <div class="page-toolbar">
                <div id="dashboard-report-range" class="pull-right tooltips btn btn-sm" data-container="body" data-placement="bottom">
                    <span class="thin uppercase hidden-xs" id="datetime"></span>
                    <script type="text/javascript">window.onload = date_time('datetime');</script>
                </div>
            </div>
        </div>

        <?php
        $curr_date = date('Y-m-d');
        $curr_time = date('H:i:s');
        ?>
        <div class="my-div-body">
            <div class="portlet box blue-madison">
                <div class="portlet-title">
                    <div class="caption"><?php echo $title; ?></div>
                    <div class="pull-right btn-add-padding"></div>
                </div>
                <div class="portlet-body" id="box">
                    <div class="box-body">
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-4 form-group">
                                    <div class="input-group">
                                        <span class="input-group-addon">Nomor Tiket <span class="wajib">*</span></span> 
                                        <input type="text" name="ticketNumber2" id="ticketNumber2" class="form-control" placeholder="No. Tiket">

                                        <span class="input-group-btn">
                                            <button class="btn default green-meadow" type="button" id="btnticketNumber">
                                                <i class="fa fa-search"></i>
                                            </button>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php echo form_open('transaction/pindah_kapal/save_pindah/', 'id="ff" autocomplete="off"'); ?>

                    <input type="hidden" name="ticketNumber" id="ticketNumber">

                    <div class="box-footer text-right">
                        <?php echo $btn_add ?>
                    </div>
                    <?php echo form_close(); ?>
                    <br />
                    <div class="portlet box green-meadow">
                        <div class="portlet-title">
                            <div class="caption">Data Penumpang</div>
                        </div>
                        <div class="portlet-body">
                            <div class="box-body">
                                <table class="table table-bordered table-hover" id="dataTables">
                                    <thead>
                                        <tr>
                                            <th>NO</th>
                                            <th>WAKTU BOARDING</th>
                                            <th>NO TIKET</th>
                                            <th>NAMA CUSTOMER</th>
                                            <th>JENIS KELAMIN</th>
                                            <th>USIA</th>
                                            <th>DOMISILI</th>
                                            <th>ID</th>
                                            <th>NO ID</th>
                                            <th>PELABUHAN</th>
                                            <th>DERMAGA</th>
                                            <th>KAPAL</th>
                                            <th>KODE JADWAL</th>
                                        </tr>
                                    </thead>
                                    <tfoot></tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                    <br />
                    <div class="portlet box green-meadow">
                        <div class="portlet-title">
                            <div class="caption">Data Kendaraan</div>
                        </div>
                        <div class="portlet-body">
                            <div class="box-body">
                                <table class="table table-bordered table-hover" id="dataTables2">
                                    <thead>
                                        <tr>
                                            <th>NO</th>
                                            <th>WAKTU BOARDING</th>
                                            <th>NO TIKET</th>
                                            <th>NO ID</th>
                                            <th>TIPE KENDARAAN</th>
                                            <th>PANJANG</th>
                                            <th>TINGGI</th>
                                            <th>BERAT</th>
                                            <th>PELABUHAN</th>
                                            <th>DERMAGA</th>
                                            <th>KAPAL</th>
                                            <th>KODE JADWAL</th>
                                        </tr>
                                    </thead>
                                    <tfoot></tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include "fileJs.php" ?>

<script type="text/javascript">

var myData = new MyData();

$(document).ready(function(){
    myData.init();
    myData.init2();

    $("#btnticketNumber").on("click", function() {
        myData.reload('dataTables');
        myData.reload('dataTables2');
    });

    $("#ticketNumber2").keyup(()=>{
        $('#ticketNumber').val($("#ticketNumber2").val());
    })

    setTimeout(function() {
        $('.menu-toggler').trigger('click');
    }, 1);

    validateForm('#ff', function(url, data) {
        $.ajax({
            url         : url,
            data        : data,
            type        : 'POST',
            dataType    : 'json',

            beforeSend: function(){
                unBlockUiId('box')
            },

            success: function(json) {
                if (json.code == 1) {
                    toastr.success(json.message, 'Sukses');
                    setTimeout(location.reload.bind(location), 1000);
                } else {
                    toastr.error(json.message, 'Gagal');
                    $('#box').unblock();
                }
            },

            error: function() {
                toastr.error('Silahkan Hubungi Administrator', 'Gagal');
            },

            complete: function(){
                $('#box').unblock();
            }
        });
    });
});
</script>
