<link href="<?php echo base_url() ?>assets/global/plugins/ladda/ladda-themeless.min.css" rel="stylesheet" type="text/css" />
<style type="text/css">
    .pad-top {
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
                    <script type="text/javascript">
                        window.onload = date_time('datetime');
                    </script>
                </div>
            </div>
        </div>

        <?php $now = date("Y-m-d");
        $last_week = date('Y-m-d', strtotime("0 days")) ?>
        <div class="my-div-body">
            <div class="portlet box blue-madison">
                <div class="portlet-title">

                    <div class="caption"><?php echo $title ?></div>
                    <div class="pull-right btn-add-padding">
                        <?php if ($btn_excel) { ?>
                            <button class="btn btn-sm btn-warning download" id="download_excel" disabled >Excel</button>
                        <?php } ?>
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="row">
                        <div class="col-md-12">
                            <!-- BEGIN EXAMPLE TABLE PORTLET-->

                            <div class="portlet-body">
                                <div class="table-toolbar">
                                    <div class="row">
                                        <div class="col-sm-12 form-inline pad-top">

                                            <div class="input-group select2-bootstrap-prepend pad-top">
                                                <div class="input-group-addon">Tanggal Pembayaran</div>
                                                <input type="text" class="form-control  input-small" id="dateFrom" value="<?php echo $last_week; ?>" readonly>
                                                <div class="input-group-addon">s/d</div>
                                                <input type="text" class="form-control  input-small" id="dateTo" value="<?php echo $now; ?>" readonly>

                                            </div>

                                            <div class="input-group select2-bootstrap-prepend pad-top">
                                                <div class="input-group-addon">Tanggal Invoice</div>
                                                <input type="text" class="form-control date input-small" autocomplete="off" id="due_date" placeholder="YYYY-MM-DD">

                                            </div>

                                            <div class="input-group select2-bootstrap-prepend pad-top">
                                                <div class="input-group-addon">Servis</div>
                                                <select id="service" class="form-control js-data-example-ajax select2 input-small" dir="" name="service">
                                                    <option value="">Pilih</option>
                                                    <?php foreach ($service as $key => $value) { ?>
                                                        <option value="<?php echo $this->enc->encode($value->id); ?>"><?php echo strtoupper($value->name); ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>

                                            <div class="input-group select2-bootstrap-prepend pad-top">
                                                <div class="input-group-addon">Shift</div>
                                                <select class="form-control select2" id="shift">
                                                    <option value="">Pilih</option>
                                                    <?php foreach ($shift as $key => $value) { ?>
                                                        <option value="<?php echo $this->enc->encode($value->id) ?>"><?php echo strtoupper($value->shift_name) ?></option>
                                                    <?php } ?>

                                                </select>

                                            </div>


                                            <div class="input-group select2-bootstrap-prepend pad-top">
                                                <div class="input-group-addon">Channel</div>
                                                <?php echo form_dropdown('', $channel, '', 'id="channel" class="form-control select2"'); ?>
                                            </div>

                                            <div id="fMerchant" class="input-group select2-bootstrap-prepend hide pad-top">
                                                <div class="input-group-addon">Merchant</div>
                                                <select id="merchant" class="form-control select2 input-small" dir="" name="merchant">
                                                </select>
                                            </div>
                                            <div id="fOutletId" class="input-group select2-bootstrap-prepend hide pad-top">
                                                <div class="input-group-addon">Outlet id</div>
                                                <select id="outletId" class="form-control select2 input-small" dir="" name="outletId">
                                                </select>
                                            </div> 

                                            <div class="input-group select2-bootstrap-prepend pad-top">
                                                <div class="input-group-addon">Keberangkatan</div>
                                                <select id="port" class="form-control js-data-example-ajax select2 input-small" dir="" name="port">
                                                    <?php if ($row_port != 0) {
                                                    } else { ?>
                                                        <option value="">Pilih</option>
                                                    <?php }
                                                    foreach ($port as $key => $value) { ?>
                                                        <option value="<?php echo $this->enc->encode($value->id); ?>">
                                                            <?php echo strtoupper($value->name); ?>
                                                        </option>
                                                    <?php } ?>
                                                </select>
                                            </div>

                                            <div class="input-group select2-bootstrap-prepend pad-top">
                                                <div class="input-group-addon">Sof Id</div>
                                                <?php echo form_dropdown('', $sofId, '', 'id="sofId" class="form-control select2"'); ?>
                                            </div>                                            

                                           <div class="input-group pad-top">
                                                <div class="input-group-btn">
                                                    <button type="button" class="btn green dropdown-toggle" data-toggle="dropdown" aria-expanded="false" id='btnData' >No. Invoice
                                                        <i class="fa fa-angle-down"></i>
                                                    </button>    
                                                    <ul class="dropdown-menu">
                                                        <li>
                                                            <a href="javascript:;" onclick="myData.changeSearch('No. Invoice','transNumber')">No. Invoice</a>
                                                        </li>
                                                        <li>
                                                            <a href="javascript:;" onclick="myData.changeSearch('Kode Booking','bookingCode')">Kode Booking</a>
                                                        </li>                                                        
                                                        <li>
                                                            <a href="javascript:;" onclick="myData.changeSearch('Nama Pengguna Jasa','passName')">Nama Pengguna Jasa</a>
                                                        </li>
                                                        <li>
                                                            <a href="javascript:;" onclick="myData.changeSearch('Tipe Pembayaran','paymentType')">Tipe Pembayaran</a>
                                                        </li>
                                                        <li>
                                                            <a href="javascript:;" onclick="myData.changeSearch('Kode Diskon','discountCode')" >Kode Diskon</a>
                                                        </li>
                                                        <li>
                                                            <a href="javascript:;" onclick="myData.changeSearch('Transcode','transCode')" >Transcode</a>
                                                        </li>       
                                                        <li>
                                                            <a href="javascript:;" onclick="myData.changeSearch('No. Kartu','cardNo')" >No Kartu</a>
                                                        </li>       
                                                        <li>
                                                            <a href="javascript:;" onclick="myData.changeSearch('No. Ref','refNo')" >No Ref</a>
                                                        </li>

                                                    </ul>
                                                </div>
                                                <!-- /btn-group -->
                                                <input type="text" class="form-control" placeholder="Cari Data" data-name="transNumber" name="searchData" id="searchData"> 
                                            </div>                          

                                            <div class="input-group select2-bootstrap-prepend pad-top">

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
                                            <th>TANGGAL PEMBAYARAN</th>
                                            <th>TANGGAL TERBENTUK</th>
                                            <th>KODE BOOKING</th>
                                            <th>NOMER INVOICE</th>
                                            <th>NAMA CUSTOMER</th>
                                            <th>TANGGAL INVOICE</th>
                                            <th>TIPE PEMBAYARAN</th>
                                            <th>MERCHANT </th>
                                            <th>TOTAL TARIF (Rp.)</th>
                                            <th>TOTAL TARIF TANPA <br>  BIAYA ADMIN (Rp.)</th>
                                            <th>BIAYA ADMIN (Rp.)</th>
                                            <th>SERVIS</th>
                                            <th>CHANNEL</th>
                                            <th>OUTLET ID</th>
                                            <th>SHIFT</th>
                                            <th>ASAL</th>
                                            <th>TUJUAN</th>
                                            <th>TGL KEBERANGKATAN</th>
                                            <th>JAM KEBERANGKATAN</th>
                                            <th>JENIS TRANSAKSI</th>
                                            <th>TRANS CODE</th>
                                            <th>NOMER KARTU</th>
                                            <th>SOF ID</th>
                                            <!-- <th>SUMBER PEMBAYARAN</th> -->
                                            <th>KODE DISKON</th>
                                            <th>NAMA DISKON</th>
                                            <th>REF NO</th>
                                            <th>
                                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                AKSI
                                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                            </th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                            <th colspan="27" style="text-align:left"></th>
                                        </tr>
                                    </tfoot>
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


<script src="<?php echo base_url() ?>assets/global/plugins/ladda/spin.min.js" type="text/javascript"></script>
<script src="<?php echo base_url() ?>assets/global/plugins/ladda/ladda.min.js" type="text/javascript"></script>
<?php include "fileJs.php" ?>
<script type="text/javascript">

    var csfrData = {};
    csfrData[`<?php echo $this->security->get_csrf_token_name(); ?>`] =`<?php echo $this->security->get_csrf_hash(); ?>`;
    $.ajaxSetup({
        data: csfrData
    }); 

    var myData=new MyData();
    jQuery(document).ready(function() {
        myData.init();

        $("#download_excel").click(function(event) {
            var dateFrom = $("#dateFrom").val();
            var dateTo = $("#dateTo").val();
            var service = $("#service").val();
            var port = $("#port").val();
            var channel = $("#channel").val();
            var due_date = $("#due_date").val();
            var shift = $("#port_destination").val();
            var merchant = $("#merchant").val();
            var outletId = $("#outletId").val();
            var sofId = $("#sofId").val();
            var searchData = $('#searchData').val();
            var searchName = $("#searchData").attr('data-name');            

            window.location.href = "<?php echo site_url('transaction/payment/download_excel?') ?>dateFrom=" + dateFrom + "&dateTo=" + dateTo + "&service=" + service + "&port=" + port + "&channel=" + channel + "&shift=" + shift + "&due_date=" + due_date + "&merchant=" + merchant + "&outletId=" + outletId+ "&sofId=" + sofId+ "&searchData=" + searchData+ "&searchName=" + searchName;
        });

        $('.date').datepicker({
            format: 'yyyy-mm-dd',
            changeMonth: true,
            changeYear: true,
            autoclose: true,
            // endDate: new Date(),
        });

        $('#dateFrom').datepicker({
            format: 'yyyy-mm-dd',
            changeMonth: true,
            changeYear: true,
            autoclose: true,
            todayHighlight: true,
        });

        $('#dateTo').datepicker({
            format: 'yyyy-mm-dd',
            changeMonth: true,
            changeYear: true,
            autoclose: true,
            todayHighlight: true,
            endDate: "+1m",
            startDate: new Date()
        });        


        $("#dateFrom").change(function() {            
            
            var startDate = $(this).val();
            var someDate = new Date(startDate);

            someDate.getDate();
            someDate.setMonth(someDate.getMonth()+1);
            someDate.getFullYear();
            let endDate=myData.formatDate(someDate);

            // destroy ini firts setting
            $('#dateTo').datepicker('remove');
            
              // Re-int with new options
            $('#dateTo').datepicker({
                format: 'yyyy-mm-dd',
                changeMonth: true,
                changeYear: true,
                autoclose: true,
                todayHighlight: true,
                endDate: endDate,
                startDate: startDate
            });

            $('#dateTo').val(startDate).datepicker("update")            
        });



        $("#cari").on("click",function(){
            $(this).button('loading');
            myData.reload();
            $('#dataTables').on('draw.dt', function() {
                $("#cari").button('reset');
            });
        });        

        setTimeout(function() {
            $('.menu-toggler').trigger('click');
        }, 1);

        $("#channel").on("change", function() {
            var channel = $(this).val();
            $.ajax({
                url: "<?php echo site_url('transaction/payment/get_merchant') ?>",
                type: "POST",
                data: {
                    channel: channel
                },
                dataType: 'json',

                beforeSend: function() {
                    var valOption = $("#channel option:selected").html();
                    if (valOption.toLocaleLowerCase() == 'b2b') {
                        $("#fMerchant").removeClass("hide");
                    } else {
                        $("#fMerchant").addClass("hide");
                        $("#fOutletId").addClass("hide");
                    }
                },
                success: function(json) {

                    $("input[name=" + json.csrfName + "]").val(json.tokenHash);
                    csfrData[json['csrfName']] = json['tokenHash'];
                    $.ajaxSetup({
                        data: csfrData
                    });
                  
                    var d = json.data,
                        merchant = $("#merchant"),
                        html = '';

                    if (d.length > 0) {
                        for (var r = 0; r < d.length; r++) {
                            var res = d[r];
                            html += `<option value="${res.id}">${res.name}</option>`;
                        }
                    }
                    merchant.html(html);
                }
            })
        });

        $("#merchant").on("change", function(){
            myData.getOutletId($(this).val());
            $("#fOutletId").removeClass("hide");
        })


    });


</script>