<link href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-tagsinput/bootstrap-tagsinput.css" rel="stylesheet" type="text/css" />

<style type="text/css">
.wajib{
    color: red;
}

.bootstrap-tagsinput {
    background-color: #fff;
    border: 1px solid #ccc;
    box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
    display: inline-block;
    padding: 4px 6px;
    margin-bottom: 0px;
    color: #555;
    vertical-align: middle;
    border-radius: 4px;
    max-width: 100%;
    line-height: 24px;
    cursor: text;
    width: 100%;
}

.pad-top
{
    padding-top: 5px;
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
                    <?php echo form_open('transaction/muntah_kapal/save_muntah/', 'id="ff" autocomplete="off"'); ?>    
                    <div class="table-toolbar">

                        <div class="row">
                            <div class="col-sm-12 form-inline">

                                <div class="input-group pad-top">
                                    <span class="input-group-addon">Tipe</span>
                                    <select class="form-control select2 in-group" required name="type" id="type" data-placeholder="Pilih Tipe">
                                        <option value=""></option>
                                        <option value="kendaraan">Kendaraan</option>
                                        <option value="penumpang">Penumpang</option>
                                    </select>
                                </div>

                                <div class="input-group pad-top">
                                    <span class="input-group-addon">Nomor Tiket</span>
                                    <input type="text" data-role="tagsinput" class="form-control input-large" required name="ticketNumber" id="ticketNumber" placeholder="No.Ticket">
                                </div>         
                                
                                <div class="input-group pad-top">
                                    <button class="btn default green-meadow" type="button" id="btnTicketNumber" data-loading-text="<i class='fa fa-spinner fa-spin'></i> Cari" title="Cari" disabled>
                                        <i class="fa fa-search"> Cari</i>
                                    </button>
                                </div>                                                              

                            </div>
                        </div>
                    </div>                    

                    <div class="box-footer text-right">
                        <?php echo $btn_add; ?>
                    </div>
                    <?php echo form_close(); ?>
                    <br />
                    <div class="clearfix"> </div>
                    <div class="portlet box green-meadow" id="tabelPenumpang">
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
                                        </tr>
                                    </thead>
                                    <tfoot></tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                    <br />
                    <div class="portlet box green-meadow" id="tabelKendaraan">
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

<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-tagsinput/bootstrap-tagsinput.js"></script>

<?php include "fileJs.php" ?>
<script type="text/javascript">

    var myData = new MyData();
    $(document).ready(function(){
        $('#tabelPenumpang').hide();
        $('#tabelKendaraan').hide();

        myData.initPnp();
        myData.initKnd()

        $("#type").on("change",function(){
            $(this).valid();
            $('#btnTicketNumber').attr('disabled',false);
            var selector = $("#type").val();

            if (selector == 'penumpang') {
                $('#tabelPenumpang').show();
                $('#tabelKendaraan').hide();
                // myData.reload();
            } else if (selector == 'kendaraan') {
                $('#tabelPenumpang').show();
                $('#tabelKendaraan').show();
                // myData.reload();
                // table2.reload();
            } else {
                $('#tabelPenumpang').hide();
                $('#tabelKendaraan').hide();
            }
        });

        $("#btnTicketNumber").on("click", function() {
            var selector = $("#type").val();
            var tickets = $('#ticketNumber').tagsinput('items');
            var data = {type:selector, ticketNumber:tickets};
            var reload = false;

            $.ajax({
                url         : "<?php echo site_url('transaction/muntah_kapal/checkTicketNumberBoardingStatus/'); ?>",
                data        : data,
                type        : 'POST',
                dataType    : 'json',

                beforeSend: function(){
                    unBlockUiId('box');
                },

                success: function(json) {
                    if (json.code == 1) {
                        reload = true;
                    } else {
                        reload = false;
                        toastr.error(json.message, 'Gagal');
                    }
                    $('#box').unblock();
                },

                error: function() {
                    toastr.error('Silahkan Hubungi Administrator', 'Gagal');
                },

                complete: function(){
                    if (reload == true) {
                        if (selector == 'penumpang')
                        {
                            $(this).button('loading');
                            myData.reload('dataTables');
                        } 
                        else if (selector == 'kendaraan')
                        {
                            $(this).button('loading');
                            myData.reload('dataTables');
                            myData.reload('dataTables2');
                        }
                    }
                }
            });
        });

        $('#ff').validate({
            ignore      : 'input[type=hidden], .select2-search__field',
            errorClass  : 'validation-error-label',
            successClass: 'validation-valid-label',
            rules       : {},
            messages    : {},

            highlight   : function(element, errorClass) {
                $(element).addClass('val-error');
            },

            unhighlight : function(element, errorClass) {
                $(element).removeClass('val-error');
            },

            errorPlacement: function(error, element) {
                if(element.hasClass('in-group')) {
                    error.appendTo( element.parent().parent() );
                }

                else {
                    error.insertAfter(element);
                }
            },

            submitHandler: function(form) {
                data = getFormData($(form));
                data.ticketNumber = $('#ticketNumber').tagsinput('items');
                $.ajax({
                    url         : form.action,
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
                        // $('#box').unblock();
                    }
                });
            }
        });
    });
</script>
