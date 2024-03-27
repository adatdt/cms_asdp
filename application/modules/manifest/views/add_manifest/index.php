<div class="page-content-wrapper">
    <div class="page-content">
        <div class="page-bar">
            <ul class="page-breadcrumb">
                <li>
                    <?php echo '<a href="' . $url_home . '">' . $home . '</a>'; ?>
                    <i class="fa fa-circle"></i>
                </li>
                <li>
                    <span><?php echo $title2; ?></span>
                </li>
            </ul>
            <div class="page-toolbar">
                <div id="dashboard-report-range" class="pull-right tooltips btn btn-sm" data-container="body" data-placement="bottom">
                    <span class="thin uppercase hidden-xs" id="datetime"></span>
                    <script type="text/javascript">window.onload = date_time('datetime');</script>
                </div>
            </div>
        </div>

        <?php $now=date("Y-m-d"); $last_day=date('Y-m-d',strtotime("-0 days"))?>
        <div class="my-div-body">

            <!-- Data Manifest Susulan -->
            <div class="portlet box blue-madison">
                <div class="portlet-title">
                    
                    <div class="caption"><?php echo $title ?></div>
                    <div class="pull-right btn-add-padding"></div>
                </div>
                               
                <div class="portlet-body">

                    <div class="table-toolbar">
                        <div class="row">
                            <div class="col-sm-12 form-inline">

                                <div class="input-group select2-bootstrap-prepend">
                                    <div class="input-group-addon">Tanggal Boarding</div>
                                    <input type="text" class="form-control date input-small" id="dateFrom" value="<?php echo $last_day; ?>" readonly>
                                    <div class="input-group-addon">s/d</div>
                                    <input type="text" class="form-control date input-small" id="dateTo" value="<?php echo $now; ?>" readonly>

                                </div>    

                                <div class="input-group select2-bootstrap-prepend">
                                    <div class="input-group-addon">Pelabuhan</div>
                                    <?php echo form_dropdown("port_origin",$origin,"",'id="port_origin" class="form-control js-data-example-ajax select2 input-small" ');?>
                                </div>

                               <div class="input-group pad-top">
                                    <div class="input-group-btn">
                                        <button type="button" class="btn green dropdown-toggle" data-toggle="dropdown" aria-expanded="false" id='btnData' >Kode Boarding
                                            <i class="fa fa-angle-down"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <a href="javascript:;" onclick="myData.changeSearch('Kode Boarding','boardingCode')">Kode Boarding</a>
                                            </li>
                                            <li>
                                                <a href="javascript:;" onclick="myData.changeSearch('Kode Jadwal','scheduleCode')">Kode Jadwal</a>
                                            </li>
                                            <li>
                                                <a href="javascript:;" onclick="myData.changeSearch('Nama Kapal','shipName')">Nama Kapal</a>
                                            </li>
                                            <li>
                                                <a href="javascript:;" onclick="myData.changeSearch('No. Tiket','ticketNumber')">No. Tiket</a>
                                            </li>
                                            <li>
                                                <a href="javascript:;" onclick="myData.changeSearch('Nama PJ','passName')">Nama PJ</a>
                                            </li>                                                                                                                                                                                
                                        </ul>
                                    </div>
                                    <!-- /btn-group -->
                                    <input type="text" class="form-control" placeholder="Cari Data" data-name="boardingCode" name="searchData" id="searchData" autocomplete="off"> 
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
                                 
                    <div class="kt-portlet">
                        <div class="kt-portlet__head">
                            <div class="kt-portlet__head-label">
                                <h3 class="kt-portlet__head-title">
                                </h3>
                            </div>
                        </div>
                        <div class="kt-portlet__body">
                            <ul class="nav nav-tabs " role="tablist">
                                <li class="nav-item active">
                                    <a class="label label-primary" data-toggle="tab" href="#tabData">Kapal</a>
                                </li>

                                <li class="nav-item ">
                                    <a class="label label-primary " data-toggle="tab" href="#tab1">Data Penumpang</a>
                                </li>                
                                <li class="nav-item">
                                        <a class="label label-primary " data-toggle="tab" href="#tab2">Data Kendaraan</a>
                                </li>
                            </ul>
          
                            <div class="tab-content " >
                                <div class="tab-pane active" id="tabData" role="tabpanel" >
                                    <div id="dashboard-report-range" class="pull-right tooltips btn btn-sm" data-container="body" data-placement="bottom">      <span style="font-style: italic; color:red">Data Kapal Hanya Tampil 4 jam</span> 
                                    </div>
                                    <table class="table table-bordered table-striped   table-hover" id="dataTables">
                                        <thead>
                                            <tr>
                                                <th>NO</th>
                                                <th>TANGGAL JADWAL</th>
                                                <th>KODE JADWAL</th>
                                                <th>KODE BOARDING</th>
                                                <th>TANGGAL BOARDING</th>
                                                <th>KAPAL</th>
                                                <th>PELABUHAN</th>
                                                <th>DERMAGA</th>
                                                <th>TUJUAN</th>
                                                <th>TIPE KAPAL</th>
                                                <th>JAM BERANGKAT</th>
                                                <th>KETERANGAN</th>
                                                <th>AKSI</th>

                                                
                                            </tr>
                                        </thead>
                                        <tfoot></tfoot>
                                    </table>
                                </div>

                                <div class="tab-pane" id="tab1" role="tabpanel" >

<!--                                     <div class="table-toolbar">
                                        <div class="row">
                                            <div class="col-sm-12 form-inline">

                                                <div class="input-group select2-bootstrap-prepend">
                                                    <div class="input-group-addon">Tanggal Boarding</div>
                                                    <input type="text" class="form-control date input-small" id="dateFrom2" value="<?php echo $last_day; ?>" readonly>
                                                    <div class="input-group-addon">s/d</div>
                                                    <input type="text" class="form-control date input-small" id="dateTo2" value="<?php echo $now; ?>" readonly>

                                                </div>    

                                                <div class="input-group select2-bootstrap-prepend">
                                                    <div class="input-group-addon">Pelabuhan</div>
                                                    <?php echo form_dropdown("port_origin2",$origin,"",'id="port_origin2" class="form-control js-data-example-ajax select2 input-small" ');?>
                                                </div> 

                                            </div>

                                        </div>
                                    </div>                                
 -->
                                    <?php  echo $downloadExcelPnp; ?>                                    

                                    <table class="table table-bordered table-hover" id="dataTables2">
                                        <thead>
                                            <tr>
                                                <th>NO</th>
                                                <th>TANGGAL BOARDING</th>
                                                <th>KODE JADWAL</th>
                                                <th>KODE BOARDING</th>
                                                <th>PELABUHAN</th>
                                                <th>DERMAGA</th>
                                                <th>KODE BOOKING</th>
                                                <th>NOMER TIKET</th>
                                                <th>NAMA PENUMPANG</th>
                                                <th>UMUR</th>
                                                <th>JENIS KELAMIN</th>
                                                <th>TIPE PENUMPANG</th>
                                                <th>SERVICE</th>
                                                <th>NAMA KAPAL</th>
                                                <th>TIPE KAPAL</th>
                                                <th>PERANGKAT BOARDING</th>
                                                
                                            </tr>
                                        </thead>
                                        <tfoot></tfoot>
                                    </table>
                                </div>

                                <div class="tab-pane" id="tab2" role="tabpanel">
<!--                                     <div class="table-toolbar">
                                        <div class="row">
                                            <div class="col-sm-12 form-inline">

                                                <div class="input-group select2-bootstrap-prepend">
                                                    <div class="input-group-addon">Tanggal Boarding</div>
                                                    <input type="text" class="form-control date input-small" id="dateFrom3" value="<?php echo $last_day; ?>" readonly>
                                                    <div class="input-group-addon">s/d</div>
                                                    <input type="text" class="form-control date input-small" id="dateTo3" value="<?php echo $now; ?>" readonly>

                                                </div>    

                                                <div class="input-group select2-bootstrap-prepend">
                                                    <div class="input-group-addon">Pelabuhan</div>
                                                    <?php echo form_dropdown("port_origin3",$origin,"",'id="port_origin3" class="form-control js-data-example-ajax select2 input-small" ');?>
                                                </div> 

                                            </div>

                                        </div>
                                    </div> -->                                
                                    
                                    <?php  echo $downloadExcelKnd; ?>  

                                    <table class="table table-bordered table-hover" id="dataTables3">
                                        <thead>
                                            <tr>
                                                <th>NO</th>
                                                <th>TANGGAL BOARDING</th>
                                                <th>KODE JADWAL</th>
                                                <th>KODE BOARDING</th>
                                                <th>PELABUHAN</th>
                                                <th>DERMAGA</th>
                                                <th>KODE BOOKING</th>
                                                <th>NOMER TIKET</th>
                                                <th>NAMA PENGEMUDI</th>
                                                <th>NOMER PLAT</th>
                                                <th>GOLONGAN KENDARAAN</th>
                                                <th>SERVIS</th>
                                                <th>TIPE KAPAL</th>
                                                <th>NAMA KAPAL</th>
                                                <th>PERANGKAT BOARDING</th>
                                                <th>TOTAL PENUMPANG</th>

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
</div>

<?php include "fileJs.php" ?>
<script type="text/javascript">

    var myData = new MyData();
    
    jQuery(document).ready(function () {
        myData.init();
        myData.init2();
        myData.init3();

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

        $("#dateTo").change(function(){
            myData.reload();
        });

        $("#dateFrom").change(function(){
            myData.reload();
        });

        $("#port_origin").change(function(){
            myData.reload();
        });

        $("#downloadExcel2").click(function(event){
            var dateFrom=$("#dateFrom").val();
            var dateTo=$("#dateTo").val();
            var port=$("#port_origin").val();
            var service="<?php echo $this->enc->encode(1) ?>";
            var searchData = $('#searchData').val();
            var searchName = $("#searchData").attr('data-name');


            window.location.href="<?php echo site_url('manifest/add_manifest/downloadExcel?') ?>dateFrom="+dateFrom+"&dateTo="+dateTo+"&port="+port+"&searchData="+searchData+"&searchName="+searchName+"&service="+service;
        });

        $("#downloadExcel3").click(function(event){
            var dateFrom=$("#dateFrom").val();
            var dateTo=$("#dateTo").val();
            var port=$("#port_origin").val();
            var service="<?php echo $this->enc->encode(2) ?>";            
            var searchData = $('#searchData').val();
            var searchName = $("#searchData").attr('data-name');


            window.location.href="<?php echo site_url('manifest/add_manifest/downloadExcel?') ?>dateFrom="+dateFrom+"&dateTo="+dateTo+"&port="+port+"&searchData="+searchData+"&searchName="+searchName+"&service="+service;
        });

        $("#cari").on("click",function(){
            $(this).button('loading');
            myData.reload('dataTables');
            myData.reload('dataTables2');
            myData.reload('dataTables3');
            $('#dataTables , dataTables2 , dataTables3').on('draw.dt', function() {
                $("#cari").button('reset');
            });
        });

        setTimeout(function() {
            $('.menu-toggler').trigger('click');
        }, 1);
        
    });
</script>
