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
                    <script type="text/javascript">window.onload = date_time('datetime');</script>
                </div>
            </div>
        </div>

        <?php $now=date("Y-m-d"); $last_week=date('Y-m-d',strtotime("-7 days"))?>
        <div class="my-div-body">
            <div class="portlet box blue-madison">
                <div class="portlet-title">

                    <div class="caption"><?php echo $title ?></div>
                    <!-- <div class="pull-right btn-add-padding"><?php echo $btn_add; ?></div> -->
                </div>
                <div class="portlet-body">
                    <div class="row">
                        <div class="col-md-12">
                                <!-- BEGIN EXAMPLE TABLE PORTLET-->

                            <div class="portlet-body">
                                <div class="table-toolbar">
                                    <div class="row">
                                        <div class="col-sm-12 form-inline">

                                            <div class="input-group select2-bootstrap-prepend pad-top">
                                                <div class="input-group-addon">Tanggal Booking</div>
                                                <input type="text" class="form-control date input-small" id="dateFrom" value="<?php echo $last_week; ?>">
                                                <div class="input-group-addon">s/d</div>
                                                <input type="text" class="form-control date input-small" id="dateTo" value="<?php echo $now; ?>">
                                            </div>

                                            <div class="input-group select2-bootstrap-prepend pad-top">
                                                <div class="input-group-addon">Pelabuhan</div>
                                                <select id="port" class="form-control js-data-example-ajax select2 input-small" dir="" name="port">
                                                    <option value="">All</option>
                                                    <?php foreach($port as $key=>$value) {?>
                                                    <option value="<?php echo $this->enc->encode($value->id); ?>"><?php echo strtoupper($value->name); ?></option>
                                                    <?php }?>
                                                </select>
                                            </div>

                                            <div class="input-group select2-bootstrap-prepend pad-top">
                                                <div class="input-group-addon">Tipe Pembayaran</div>
                                                <?php echo form_dropdown('', $payment_type, '', 'id="payment_type" class="form-control select2"'); ?>
                                            </div>

                                            <div class="col-sm-12 form-inline" style="margin: 5px 0;"></div>

                                            <div class="input-group select2-bootstrap-prepend pad-top">
                                                <div class="input-group-addon">Channel</div>
                                                <?php echo form_dropdown('', $channel, '', 'id="channel" class="form-control select2"'); ?>
                                            </div>

                                            <div class="input-group pad-top">
                                                <div class="input-group-btn">
                                                    <button type="button" class="btn green dropdown-toggle" data-toggle="dropdown" aria-expanded="false" id='btnData' >Kode Booking
                                                        <i class="fa fa-angle-down"></i>
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        <li>
                                                            <a href="javascript:;" onclick="myData.changeSearch('Kode Booking','bookingCode')">Kode Booking</a>
                                                        </li>
                                                        <li>
                                                            <a href="javascript:;" onclick="myData.changeSearch('Nomor Tiket','ticketNumber')">Nomor Tiket</a>
                                                        </li>
                                                        <li>
                                                            <a href="javascript:;" onclick="myData.changeSearch('Nama','name')">Nama</a>
                                                        </li>
                                                        <li>
                                                            <a href="javascript:;" onclick="myData.changeSearch('Nomor Plat','platNumber')">Nomor Plat</a>
                                                        </li>
                                                    </ul>
                                                </div>
                                                <!-- /btn-group -->
                                                <input type="text" class="form-control" placeholder="Cari Data" data-name="bookingCode" name="searchData" id="searchData"> 
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

								<div class="kt-portlet__body" style="padding-top: 20px">
                                    <ul class="nav nav-tabs" role="tablist">
                                        <li class="nav-item active">
                                            <a id="tabPenumpang" class="label label-primary" data-toggle="tab" data-target="#penumpang"data-loading-text="<i class='fa fa-spinner fa-spin'></i> Loading...">Data Pejalan Kaki</a>
                                        </li>

                                        <li class="nav-item">
											<a id="tabKendaraan" class="label label-primary" data-toggle="tab" data-target="#kendaraan" data-loading-text="<i class='fa fa-spinner fa-spin'></i> Loading...">Data Kendaraan</a>
                                        </li>
									</ul>
                                    <button id="excelkita" class="btn btn-sm btn-default" style="display:none"><i class="fa fa-file-excel-o" style="color: #ea5460"></i> EXCEL</button>
								</div>

								<div class="tab-content " >

                                    <!-- tab data penumpang -->
                                    <div class="tab-pane active" id="penumpang" role="tabpanel" style="padding: 10px">
										<table class="table table-bordered table-striped table-hover" id="dataTables">
											<thead>
												<tr>
													<th colspan="16" style="text-align: left">DATA PEJALAN KAKI</th>
												</tr>
												<tr>
													<th>NO</th>
                                                    <th>NOMOR TIKET</th>
													<th>NAMA</th>
													<th>GOLONGAN</th>
													<th>SERVIS</th>
													<th>PELABUHAN</th>
													<th>TIPE PEMBAYARAN</th>
													<th>PEMBAYARAN</th>
													<th>CETAK BOARDING PASS</th>
													<th>GATE IN</th>
													<th>CETAK BOARDING PASS EXPIRED</th>
													<th>GATE IN EXPIRED</th>
													<th>BOARDING EXPIRED</th>
												</tr>
											</thead>
											<tfoot></tfoot>
										</table>
                                    </div>

                                    <!-- Data Kendaraan -->
                                    <div class="tab-pane " id="kendaraan" role="tabpanel" style="padding: 10px" >
										<table class="table table-bordered table-striped   table-hover" id="dataTables2">
											<thead>
												<tr>
													<th colspan="16" style="text-align: left">DATA KENDARAAN</th>
												</tr>
												<tr>
													<th>NO</th>
                                                    <th>NOMOR TIKET</th>
													<th>PLAT</th>
													<th>GOLONGAN</th>
													<th>SERVIS</th>
													<th>PELABUHAN</th>
													<th>TIPE PEMBAYARAN</th>
													<th>PEMBAYARAN</th>
													<th>CETAK BOARDING PASS</th>
													<th>GATE IN</th>
													<th>CETAK BOARDING PASS EXPIRED</th>
													<th>GATE IN EXPIRED</th>
													<th>BOARDING EXPIRED</th>
												</tr>
											</thead>
											<tfoot></tfoot>
										</table>
                                    </div>
                                </div>
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
var initDT= false;
// var initPnp = false;
// var initKnd = false;
var target = '#penumpang';

myData = new MyData();


    jQuery(document).ready(function () {
        myData.init();
        myData.init2();

        // $("#tabPenumpang").button('loading');
        // $("#tabKendaraan").button('loading');
        // $("#btnSearch").button('loading');
        $("#excelkita").show();

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

        setTimeout(function() {
            $('.menu-toggler').trigger('click');
		}, 1);

        // table_penumpang.init();
        // table_kendaraan.init();

        $("#excelkita").on("click",function(e){
            port = $("#port").val();
            payment_type = $("#payment_type").val();
            channel = $("#channel").val();
            cari = $("#searchData").val();
            dateFrom = $("#dateFrom").val();
            dateTo = $("#dateTo").val();
            searchName=$("#searchData").attr('data-name');

            if(target == '#kendaraan'){
                url = "<?php echo site_url('transaction/ticket_expired/excel_kendaraan?port=') ?>";
            }else{
                url = "<?php echo site_url('transaction/ticket_expired/excel_penumpang?port=') ?>";
            }

            if (port != null) {
                window.location = url+port+
                "&payment_type=" +payment_type+
                "&channel=" +channel+
                "&cari=" +cari+
                "&searchName=" +searchName+
                "&dateFrom=" +dateFrom+
                "&dateTo=" +dateTo+
                "&pelabuhan="+$('#port').find(":selected").text();
            }
        });

		$("#cari").on("click",function(e){
			$(this).button('loading');
            $("#tabPenumpang").button('loading');
            $("#tabKendaraan").button('loading');
			e.preventDefault();
            $("#excelkita").show();
            myData.reload('dataTables');
            myData.reload('dataTables2');
            $('#dataTables , dataTables2 ').on('draw.dt', function() {
                $("#cari").button('reset');
            });
		});
	});

	$(document).on('click', '[data-toggle="tab"]', function(){
		target = $(this).data('target');
	});
</script>