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
                    <script type="text/javascript">
                        window.onload = date_time('datetime');
                    </script>
                </div>
            </div>
        </div>

        <?php $now = date("Y-m-d");
        $last_week = date('Y-m-d', strtotime("-0 days")) ?>
        <div class="my-div-body">
            <div class="portlet box blue-madison">
                <div class="portlet-title">

                    <div class="caption"><?php echo $title ?></div>
                    <div class="pull-right btn-add-padding">
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="row">
                        <div class="col-md-12">
                            <!-- BEGIN EXAMPLE TABLE PORTLET-->

                            <div class="portlet-body">
                                <div class="table-toolbar">
                                     <div class="row">
                                        <div class="col-sm-12 form-inline">

                                            <div class="input-group select2-bootstrap-prepend">
                                                <div class="input-group-addon">Tanggal Boarding</div>
                                                <input type="text" class="form-control date input-small" id="dateFrom" value="<?php echo $last_week; ?>" placeholder="YYYY-MM-DD" readonly>
                                                <div class="input-group-addon">s/d</div>
                                                <input type="text" class="form-control date input-small" id="dateTo" value="<?php echo $now; ?>" placeholder="YYYY-MM-DD" readonly>
                                            </div>

                                            <div class="input-group select2-bootstrap-prepend">
                                                <div class="input-group-addon">Pelabuhan</div>
                                                <select class="form-control select2" name="port" id="port">

                                                    <?php if($row_port!=0) {} else { ?>
                                                    <option value="">Pilih</option>
                                                    <?php } foreach ($port as $key=>$value) {?>
                                                        <option value="<?php echo $this->enc->encode($value->id) ?>"><?php echo strtoupper($value->name) ?></option>
                                                    <?php } ?>
                                                    
                                                </select>
                                            </div>


                                           <div class="input-group pad-top">
                                                <div class="input-group-btn">
                                                    <button type="button" class="btn green dropdown-toggle" data-toggle="dropdown" aria-expanded="false" id='btnData'>Kode Boarding
                                                        <i class="fa fa-angle-down"></i>
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        <li>
                                                            <a href="javascript:;" onclick="myData.changeSearch('Kode Boarding','boardingCode')">Kode Boarding</a>
                                                        </li>
                                                        <li>
                                                            <a href="javascript:;" onclick="myData.changeSearch('Nama Kapal','shipName')">Nama Kapal</a>
                                                        </li>
                                                        <li>
                                                            <a href="javascript:;" onclick="myData.changeSearch('Dermaga','dockName')">Dermaga</a>
                                                        </li>                                      
                                                    </ul>
                                                </div>
                                                <!-- /btn-group -->
                                                <input type="text" class="form-control" placeholder="Cari Data" data-name="boarding_kode" name="searchData" id="searchData"> 
                                            </div>   
                                            <div class="input-group pad-top">
                                                <button type="button" class="btn btn-danger mt-ladda-btn ladda-button" data-style="zoom-in" id="cari">
                                                    <span class="ladda-label">Cari</span>
                                                    <span class="ladda-spinner"></span>
                                                </button>
                                            </div>       


                                        </div>

                                    </div> 
                                </div>


                                <table class="table table-bordered table-hover" id="dataTables">
                                    <thead>
                                        <tr>
                                            <th>NO</th>
                                            <th>KODE BOARDING</th>
                                            <th>TANGGAL BOARDING</th>
                                            <th>KAPAL</th>
                                            <th>PELABUHAN</th>
                                            <th>DERMAGA</th>
                                            <th>WAKTU PENGIRIMAN</th>
                                            <th>STATUS</th>
                                            <th>
                                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                            AKSI
                                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                            </th>
                                        </tr>
                                    </thead>
                                    <tfoot></tfoot>
                                </table>
                            </div>
                        </div>
                        <!-- END EXAMPLE TABLE PORTLET-->
                    </div>
                </div>
                <!-- </div>     -->
            </div>
        </div>
    </div>
</div>

<?php include "fileJs.php" ?>
<script type="text/javascript">
 
    var myData = new MyData();
    jQuery(document).ready(function() {
        myData.init();

        $("#download_excel").click(function(event) {
            var dateFrom = $("#dateFrom").val();
            var dateTo = $("#dateTo").val();
            var search = $('.dataTables_filter input').val();

            window.location.href = "<?php echo site_url('log/log_transaction/download_excel?') ?>dateFrom=" + dateFrom + "&dateTo=" + dateTo + "&search=" + search;
        });


        $('.date').datepicker({
            format: 'yyyy-mm-dd',
            changeMonth: true,
            changeYear: true,
            autoclose: true,
            // endDate: new Date(),
        });


        $("#cari").on("click",function(){
            $(this).button('loading');
            myData.reload();
            $('#dataTables').on('draw.dt', function() {
                $("#cari").button('reset');
            });
        });        

    });

    function ConfirmResend(message, boarding_code) {
            alertify.confirm(message, function(e) {
                if (e) {
                    this.ResendData(boarding_code);
                }
            });
        }

    function ResendData(boarding_code)
        {
            var code = boarding_code;
            $.ajax({
                url: "<?php echo site_url() ?>transaction/boarding/send_manifest/2",
                type: 'post',
                data:  { code : code, desc : 'RESEND' },
                dataType: 'json',

                beforeSend: function(){
                    $.blockUI({message: '<h4><i class="fa fa-spinner fa-spin"></i> Loading</h4>'});
                },

                success: function(json) {
                    console.log(json);
                    if (json.code == 1) {
                        $("#btnapprove").remove();

                        toastr.success(json.message, 'Sukses');
                        myData.reload();
                        // $('#dataTables').DataTable().ajax.reload();

                        
                    } else {
                        toastr.error(json.message, 'Gagal');
                    }
                },

                error: function() {
                    toastr.error('Silahkan Hubungi Administrator', 'Gagal');
                },

                complete: function(){
                    $.unblockUI();
                }
            });
        }

        
</script>