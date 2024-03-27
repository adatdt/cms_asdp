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

        <?php $now=date("Y-m-d"); $last_week=date('Y-m-d',strtotime("-7 days")); $last_day=date('Y-m-d',strtotime("-1 days")) ?>
        <div class="my-div-body">
            <div class="portlet box blue-madison">
                <div class="portlet-title">
                    
                    <div class="caption"><?php echo $title ?></div>
                    <div class="pull-right btn-add-padding" style="padding-left: 5px"><?php echo $btn_add; ?></div>
                    <div class="pull-right btn-add-padding" style="padding-left: 5px">
                        <div class="btn-group">
                            <button type="button" class="btn btn-warning btn-sm">Format Excel</button>
                            <button type="button" class="btn btn-warning dropdown-toggle btn-sm" data-toggle="dropdown">
                                <i class="fa fa-angle-down"></i>
                            </button>
                            <ul class="dropdown-menu" role="menu">
                                <li>
                                    <a href="<?php echo  base_url() ?>template_excel/ticket_sobek_knd.xlsx" >Form Input Kendaraan</a>
                                </li>
                                <li>
                                    <a href="<?php echo base_url() ?>template_excel/ticket_sobek_pnp.xlsx" >Form Input Penumpang</a>
                                </li>
                            </ul>
                        </div>                        

                    </div>
                </div>
                                <!-- BEGIN EXAMPLE TABLE PORTLET-->
                               
                    <div class="portlet-body">

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
                                            <a class="label label-primary " data-toggle="tab" href="#tab1">Tiket Manual Penumpang</a>
                                    </li>
                                    <li class="nav-item">
                                            <a class="label label-primary " data-toggle="tab" href="#tab2">Tiket Manual Kendaraan</a>
                                    </li>
                                </ul>
              
                                <div class="tab-content " >


                                    <div class="tab-pane active" id="tab1" role="tabpanel" >

                                        <div class="table-toolbar">
                                            <div class="row">
                                                <div class="col-sm-12 form-inline">

                                                    <div class="input-group select2-bootstrap-prepend pad-top">
                                                        <div class="input-group-addon">Tanggal Transaksi</div>
                                                        <input type="text" class="form-control date input-small" id="dateFrom" value="<?php echo $last_week; ?>" readonly>
                                                        <div class="input-group-addon">s/d</div>
                                                        <input type="text" class="form-control date input-small" id="dateTo" value="<?php echo $now; ?>" readonly>
                                                    </div>    



                                                    <div class="input-group select2-bootstrap-prepend pad-top">
                                                        <div class="input-group-addon">Pelabuhan</div>
                                                        <select id="port" class="form-control js-data-example-ajax select2 input-small" dir="" name="port">
                                                            <?php if($row_port!=0) {} else { ?>
                                                            <option value="">Pilih</option>
                                                            <?php } foreach($port as $key=>$value) {?>
                                                            <option value="<?php echo $this->enc->encode($value->id); ?>"><?php echo strtoupper($value->name); ?></option>
                                                            <?php }?>
                                                        </select>
                                                    </div>

                                                    <div class="input-group select2-bootstrap-prepend pad-top">
                                                        <div class="input-group-addon">Shift</div>
                                                        <select id="shift" class="form-control js-data-example-ajax select2 input-small" dir="" name="shift">
                                                            <option value="">Pilih</option>
                                                            <?php  foreach($shift as $key=>$value) {?>
                                                            <option value="<?php echo $this->enc->encode($value->id); ?>"><?php echo strtoupper($value->shift_name); ?></option>
                                                            <?php }?>
                                                        </select>
                                                    </div>   

                                                    <div class="input-group select2-bootstrap-prepend pad-top">
                                                        <div class="input-group-addon">Layanan</div>
                                                        <select id="ship_class" class="form-control js-data-example-ajax select2 input-xs" dir="" name="ship_class">
                                                            <option value="">Pilih</option>
                                                            <?php  foreach($ship_class as $key=>$value) {?>
                                                            <option value="<?php echo $this->enc->encode($value->id); ?>"><?php echo strtoupper($value->name); ?></option>
                                                            <?php }?>
                                                        </select>
                                                    </div> 

                                                    <div class="input-group pad-top">
                                                        <div class="input-group-btn">
                                                            <button type="button" class="btn green dropdown-toggle" data-toggle="dropdown" aria-expanded="false" id='btnData' >Nama Penumpang
                                                                <i class="fa fa-angle-down"></i>
                                                            </button>
                                                            <ul class="dropdown-menu">
                                                                <li>
                                                                    <a href="javascript:;" onclick="myData.changeSearch('Nama Penumpang','name')">Nama Penumpang</a>
                                                                </li>
                                                                <li>
                                                                    <a href="javascript:;" onclick="myData.changeSearch('Nomor Tiket Manual','ticketNumber')">Nomor Tiket Manual</a>
                                                                </li>
                                                                <li>
                                                                    <a href="javascript:;" onclick="myData.changeSearch('Nomor Tiket Baru','newTicket')">Nomor Tiket Baru</a>
                                                                </li>
                                                                <li>
                                                                    <a href="javascript:;" onclick="myData.changeSearch('Nomor Invoice','invoice')">Nomor Invoice</a>
                                                                </li>
                                                                <li>
                                                                    <a href="javascript:;" onclick="myData.changeSearch('Penjual','penjual')">Penjual</a>
                                                                </li>
                                                                <li>
                                                                    <a href="javascript:;" onclick="myData.changeSearch('Alamat','address')">Alamat</a>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    <!-- /btn-group -->
                                                    <input type="text" class="form-control" placeholder="Cari Data" data-name="name" name="searchData" id="searchData"> 
                                                </div>   
                                                <div class="input-group pad-top">
                                                    <button type="button" class="btn btn-danger mt-ladda-btn ladda-button" data-style="zoom-in" id="cari">
                                                        <span class="ladda-label">Cari</span>
                                                        <span class="ladda-spinner"></span>
                                                    </button>
                                                </div>                                                                                                       

                                                    <div class="pull-right btn-add-padding pad-top">
                                                        <?php if ($btn_excel) {?>
                                                            <button  class="btn btn-sm btn-warning download" id="download_excel">Excel</button>
                                                        <?php } ?>
                                                    </div>        

                                                </div>

                                            </div>
                                        </div>

                                        <table class="table table-bordered table-striped   table-hover" id="dataTables">
                                            <thead>
                                                <tr>
                                                    <th>NO</th>
                                                    <th>TANGGAL INPUT</th>
                                                    <th>TANGGAL TRANSAKSI</th>
                                                    <th>NO INVOICE</th>
                                                    <th>NAMA PENUMPANG</th>
                                                    <th>NO TIKET MANUAL</th>
                                                    <th>NO TIKET BARU</th>
                                                    <th>JENIS KELAMIN</th>
                                                    <th>ALAMAT</th>
                                                    <th>PENJUAL</th>
                                                    <th>SHIFT</th>
                                                    <th>LAYANAN</th>
                                                    <th>PELABUHAN</th>
                                                </tr>
                                            </thead>
                                            <tfoot></tfoot>
                                        </table>

                                    </div>

                                    <div class="tab-pane" id="tab2" role="tabpanel">

                                        <div class="table-toolbar">
                                            <div class="row">
                                                <div class="col-sm-12 form-inline">

                                                    <div class="input-group select2-bootstrap-prepend pad-top">
                                                        <div class="input-group-addon">Tanggal Transaksi</div>
                                                        <input type="text" class="form-control date input-small" id="dateFrom2" value="<?php echo $last_week; ?>" readonly>
                                                        <div class="input-group-addon">s/d</div>
                                                        <input type="text" class="form-control date input-small" id="dateTo2" value="<?php echo $now; ?>" readonly>
                                                    </div>    



                                                    <div class="input-group select2-bootstrap-prepend pad-top">
                                                        <div class="input-group-addon">Pelabuhan</div>
                                                        <select id="port2" class="form-control js-data-example-ajax select2 input-small" dir="" name="port2">
                                                            <?php if($row_port!=0) {} else { ?>
                                                            <option value="">Pilih</option>
                                                            <?php } foreach($port as $key=>$value) {?>
                                                            <option value="<?php echo $this->enc->encode($value->id); ?>"><?php echo strtoupper($value->name); ?></option>
                                                            <?php }?>
                                                        </select>
                                                    </div>

                                                    <div class="input-group select2-bootstrap-prepend pad-top">
                                                        <div class="input-group-addon">Shift</div>
                                                        <select id="shift2" class="form-control js-data-example-ajax select2 input-small" dir="" name="shift2">
                                                            <option value="">Pilih</option>
                                                            <?php  foreach($shift as $key=>$value) {?>
                                                            <option value="<?php echo $this->enc->encode($value->id); ?>"><?php echo strtoupper($value->shift_name); ?></option>
                                                            <?php }?>
                                                        </select>
                                                    </div>   

                                                    <div class="input-group select2-bootstrap-prepend pad-top">
                                                        <div class="input-group-addon">Layanan</div>
                                                        <select id="ship_class2" class="form-control js-data-example-ajax select2 input-xs" dir="" name="ship_class2">
                                                            <option value="">Pilih</option>
                                                            <?php  foreach($ship_class as $key=>$value) {?>
                                                            <option value="<?php echo $this->enc->encode($value->id); ?>"><?php echo strtoupper($value->name); ?></option>
                                                            <?php }?>
                                                        </select>
                                                    </div>

                                                    <div class="input-group pad-top">
                                                        <div class="input-group-btn">
                                                            <button type="button" class="btn green dropdown-toggle" data-toggle="dropdown" aria-expanded="false" id='btnData2' >Nama Penumpang
                                                                <i class="fa fa-angle-down"></i>
                                                            </button>
                                                            <ul class="dropdown-menu">
                                                                <li>
                                                                    <a href="javascript:;" onclick="myData.changeSearch2('Nama Penumpang','name')">Nama Penumpang</a>
                                                                </li>
                                                                <li>
                                                                    <a href="javascript:;" onclick="myData.changeSearch2('Nomor Tiket Manual','ticketNumber')">Nomor Tiket Manual</a>
                                                                </li>
                                                                <li>
                                                                    <a href="javascript:;" onclick="myData.changeSearch2('Nomor Tiket Baru','newTicket')">Nomor Tiket Baru</a>
                                                                </li>
                                                                <li>
                                                                    <a href="javascript:;" onclick="myData.changeSearch2('Nomor Invoice','invoice')">Nomor Invoice</a>
                                                                </li>
                                                                <li>
                                                                    <a href="javascript:;" onclick="myData.changeSearch2('Penjual','penjual')">Penjual</a>
                                                                </li>
                                                                <li>
                                                                    <a href="javascript:;" onclick="myData.changeSearch2('Nomor Polisi','platNumber')">Nomor Polisi</a>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                        <!-- /btn-group -->
                                                        <input type="text" class="form-control" placeholder="Cari Data" data-name="name" name="searchData2" id="searchData2"> 
                                                    </div>   
                                                    <div class="input-group pad-top">
                                                        <button type="button" class="btn btn-danger mt-ladda-btn ladda-button" data-style="zoom-in" id="cari2">
                                                            <span class="ladda-label">Cari</span>
                                                            <span class="ladda-spinner"></span>
                                                        </button>
                                                    </div>                                                                                                        

                                                    <div class="pull-right btn-add-padding pad-top">
                                                        <?php if ($btn_excel) {?>
                                                            <button  class="btn btn-sm btn-warning download" id="download_excel2">Excel</button>
                                                        <?php } ?>
                                                    </div>        

                                                </div>

                                            </div>
                                        </div>


                                        <table class="table table-bordered table-striped   table-hover" id="dataTables2">
                                            <thead>
                                                <tr>
                                                    <th>NO</th>
                                                    <th>TANGGAL INPUT</th>
                                                    <th>TANGGAL TRANSAKSI</th>
                                                    <th>NO INVOICE</th>
                                                    <th>NAMA PENUMPANG</th>
                                                    <th>NO TIKET MANUAL</th>
                                                    <th>NO TIKET BARU</th>
                                                    <th>NOMOR POLISI</th>
                                                    <th>GOLONGAN</th>
                                                    <th>PENJUAL</th>
                                                    <th>SHIFT</th>
                                                    <th>LAYANAN</th>
                                                    <th>PELABUHAN</th>
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
                <!-- </div>     -->
            </div>
        </div>
    </div>
</div>

<?php include "fileJs2.php" ?>

<script type="text/javascript">

myData = new MyData();

    $(document).ready(function () {
        myData.init();
        myData.init2();

        $("#download_excel").click(function(event){
            var dateFrom=$("#dateFrom").val();
            var dateTo=$("#dateTo").val();
            var port=$("#port").val();
            var shift=$("#shift").val();
            var ship_class=$("#ship_class").val();
            var cari = $("#searchData").val();
            var searchName=$("#searchData").attr('data-name');

            window.location.href="<?php echo site_url('transaction/ticket_sobek/download_excel?') ?>dateFrom="+dateFrom+"&dateTo="+dateTo+"&port="+port+"&ship_class="+ship_class+"&cari="+cari+"&searchName="+searchName;
        });

        $("#download_excel2").click(function(event){
            var dateFrom2=$("#dateFrom2").val();
            var dateTo2=$("#dateTo2").val();
            var port2=$("#port2").val();
            var shift2=$("#shift2").val();
            var ship_class2=$("#ship_class2").val();
            var cari = $("#searchData2").val();
            var searchName=$("#searchData2").attr('data-name');

            window.location.href="<?php echo site_url('transaction/ticket_sobek/download_excel2?') ?>dateFrom="+dateFrom2+"&dateTo="+dateTo2+"&port="+port2+"&ship_class="+ship_class2+"&cari="+cari+"&searchName="+searchName;
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


        $('#dateFrom2').datepicker({
            format: 'yyyy-mm-dd',
            changeMonth: true,
            changeYear: true,
            autoclose: true,
            todayHighlight: true,
        });

        $('#dateTo2').datepicker({
            format: 'yyyy-mm-dd',
            changeMonth: true,
            changeYear: true,
            autoclose: true,
            todayHighlight: true,
            endDate: "+1m",
            startDate: new Date()
        });        


        $("#dateFrom2").change(function() {            
            
            var startDate = $(this).val();
            var someDate = new Date(startDate);

            someDate.getDate();
            someDate.setMonth(someDate.getMonth()+1);
            someDate.getFullYear();
            let endDate=myData.formatDate(someDate);

            // destroy ini firts setting
            $('#dateTo2').datepicker('remove');
            
              // Re-int with new options
            $('#dateTo2').datepicker({
                format: 'yyyy-mm-dd',
                changeMonth: true,
                changeYear: true,
                autoclose: true,
                todayHighlight: true,
                endDate: endDate,
                startDate: startDate
            });

            $('#dateTo2').val(startDate).datepicker("update")            
        });

        // $("#dateTo").change(function(){
        //     myData.reload('dataTables');
        // });

        // $("#dateFrom").change(function(){
        //     myData.reload('dataTables');
        // });

        // $("#port").change(function(){
        //     myData.reload('dataTables');
        // });

        // $("#ship_class").change(function(){
        //     myData.reload('dataTables');
        // });

        // $("#shift").change(function(){
        //     myData.reload('dataTables');
        // });

        // $("#dateTo2").change(function(){
        //     myData.reload();
        // });

        // $("#dateFrom2").change(function(){
        //     myData.reload();
        // });

        // $("#port2").change(function(){
        //     myData.reload();
        // });

        // $("#ship_class2").change(function(){
        //     myData.reload();
        // });

        // $("#shift2").change(function(){
        //     myData.reload();
        // });  

        $("#cari").on("click",function(e){
			$(this).button('loading');
            // $("#tab1").button('loading');
            // $("#tab2").button('loading');
			e.preventDefault();
            // $("#excelkita").show();
            myData.reload('dataTables');
            // myData.reload('dataTables2');
            $('#dataTables').on('draw.dt', function() {
                $("#cari").button('reset');
            });
		}); 

        $("#cari2").on("click",function(e){
			$(this).button('loading');
            // $("#tab1").button('loading');
            // $("#tab2").button('loading');
			e.preventDefault();
            // $("#excelkita").show();
            // myData.reload('dataTables');
            myData.reload('dataTables2');
            $('#dataTables2 ').on('draw.dt', function() {
                $("#cari2").button('reset');
            });
		});                            



        
    });
</script>
