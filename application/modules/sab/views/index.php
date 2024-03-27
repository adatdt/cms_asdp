<style type="text/css">
    .pad-top{
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
                    <div class="pull-right btn-add-padding">
                        <button onclick="showModalSab('<?= $url ?>')" class="btn btn-sm btn-warning" title="Tambah"><i class="fa fa-plus"></i> Tambah</button>
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

                                            <div class="input-group select2-bootstrap-prepend pad-top">
                                                <div class="input-group-addon">Tanggal Booking</div>
                                                <input type="text" class="form-control date input-small" id="dateFrom" value="<?php echo $last_week; ?>">
                                                <div class="input-group-addon">s/d</div>
                                                <input type="text" class="form-control date input-small" id="dateTo" value="<?php echo $now; ?>">

                                            </div>    

                                            <div class="input-group select2-bootstrap-prepend pad-top">
                                                <div class="input-group-addon">Keberangkatan</div>
                                                <select id="port_origin" class="form-control js-data-example-ajax select2 input-small" dir="" name="port_origin">
                                                    <option value="">Pilih</option>
                                                    <?php foreach($port as $key=>$value) {?>
                                                    <option value="<?php echo $this->enc->encode($value->id); ?>"><?php echo strtoupper($value->name); ?></option>
                                                    <?php }?>
                                                </select>
                                            </div> 

                                            <div class="input-group select2-bootstrap-prepend pad-top">
                                                <div class="input-group-addon">Tujuan</div>
                                                <select id="port_destination" class="form-control js-data-example-ajax select2 input-small" dir="" name="port_destination">
                                                    <option value="">Pilih</option>
                                                    <?php foreach($port as $key=>$value) {?>
                                                    <option value="<?php echo $this->enc->encode($value->id); ?>"><?php echo strtoupper($value->name); ?></option>
                                                    <?php }?>
                                                </select>
                                            </div>

                                           <div class="input-group pad-top">
                                                <div class="input-group-btn">
                                                    <button type="button" class="btn green dropdown-toggle" data-toggle="dropdown" aria-expanded="false" id='btnData' >No. Tiket
                                                        <i class="fa fa-angle-down"></i>
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        <li>
                                                            <a href="javascript:;" onclick="myData.changeSearch('No. Tiket','ticketNumber')">No. Tiket</a>
                                                        </li>
                                                        <li>
                                                            <a href="javascript:;" onclick="myData.changeSearch('Nama Penumpang','passName')">Nama Penumpang</a>
                                                        </li>
                                                        <li>
                                                            <a href="javascript:;" onclick="myData.changeSearch('Instalasi','instName')">Instalasi</a>
                                                        </li>                                                                                                                   
                                                    </ul>
                                                </div>
                                                <!-- /btn-group -->
                                                <input type="text" class="form-control" placeholder="Cari Data" data-name="ticketNumber" name="searchData" id="searchData" autocomplete="off"> 
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
                                            <a id="tabPenumpang" class="label label-primary" data-toggle="tab" data-target="#penumpang" data-loading-text="<i class='fa fa-spinner fa-spin'></i> Loading...">Data Pejalan Kaki</a>
                                        </li>

                                        <li class="nav-item">
											<a id="tabKendaraan" class="label label-primary" data-toggle="tab" data-target="#kendaraan" data-loading-text="<i class='fa fa-spinner fa-spin'></i> Loading...">Data Kendaraan</a>
                                        </li>
									</ul>
								</div>

                                <div class="tab-content " >

                                    <!-- tab data penumpang -->
                                    <div class="tab-pane active" id="penumpang" role="tabpanel" style="padding: 10px">
										<table class="table table-bordered table-striped table-hover" id="tabel_penumpang" hidden>
											<thead>
												<tr>
													<th colspan="16" style="text-align: left">DATA PEJALAN KAKI</th>
												</tr>
												<tr>
													<th>NO</th>
													<th>TANGGAL GATE IN</th>
                                                    <th>NOMOR TIKET</th>
													<th>NAMA PENUMPANG</th>
													<th>GOLONGAN</th>
													<th>INSTANSI</th>
													<th>SERVIS</th>
													<th>PELABUHAN</th>
                                                    <th>KAPAL</th>
                                                    <th>
                                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                        AKSI
                                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                    </th>
												</tr>
											</thead>
											<tfoot></tfoot>
										</table>
                                    </div>

                                    <!-- Data Kendaraan -->
                                    <div class="tab-pane " id="kendaraan" role="tabpanel" style="padding: 10px" >
										<table class="table table-bordered table-striped   table-hover" id="tabel_kendaraan" hidden>
											<thead>
												<tr>
													<th colspan="16" style="text-align: left">DATA KENDARAAN</th>
												</tr>
												<tr>
													<th>NO</th>
													<th>TANGGAL GATE IN</th>
                                                    <th>NOMOR TIKET</th>
													<th>PLAT</th>
													<th>GOLONGAN</th>
													<th>INSTANSI</th>
													<th>SERVIS</th>
													<th>PELABUHAN</th>
                                                    <th>KAPAL</th>
                                                    <th>TOTAL PENUMPANG</th>
                                                    <th>
                                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                        AKSI
                                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                    </th>
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
    <input type="hidden" id="tokenHash" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
</div>

<?php include "fileJs.php"; ?>
<script type="text/javascript">
    var csfrData = {};
    csfrData[`<?php echo $this->security->get_csrf_token_name(); ?>`] =`<?php echo $this->security->get_csrf_hash(); ?>`;
    $.ajaxSetup({
        data: csfrData
    });
    
    function showModalSab(url) {
        if (!mfp.isOpen) {
            mfp.open({
                items: {
                    src: url
                },
                modal: true,
                type: 'ajax',
                tLoading: '<i class="fa fa-refresh fa-spin"></i> Mohon tunggu...',
                showCloseBtn: false,
            });
        }
    }

    var myData=new MyData();

    $(document).ready(function () {
        myData.initPnp();
        myData.initKnd();

        $("#tabPenumpang").button('loading');
        $("#tabKendaraan").button('loading');

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

        $(document).on('click', '[data-toggle="tab"]', function(){
    		target = $(this).data('target');
    	});

        $("#cari").on("click",function(){
            $(this).button('loading');
            myData.reload('tabel_penumpang');
            myData.reload('tabel_kendaraan');
            $("#tabPenumpang").button('loading');
            $("#tabKendaraan").button('loading');            
            $('#tabel_penumpang, #tabel_kendaraan').on('draw.dt', function() {
                $("#cari").button('reset');
            });
        });                
    });
</script>
