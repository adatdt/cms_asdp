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

        <?php $now=date("Y-m-d"); $last_week=date('Y-m-d',strtotime("-0 days"))?>
        <div class="my-div-body">
            <div class="portlet box blue-madison">
                <div class="portlet-title">
                    
                    <div class="caption"><?php echo $title ?></div>
                    <div class="pull-right btn-add-padding">
                        <?php if ($btn_excel) {?>
                            <button  class="btn btn-sm btn-warning download" id="download_excel" disabled >Excel</button>
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
                                        <div class="col-sm-12 form-inline">

                                            <div class="input-group select2-bootstrap-prepend">
                                                <div class="input-group-addon">Tanggal Boarding</div>
                                                <input type="text" class="form-control date input-small" id="dateFrom" value="<?php echo $last_week; ?>" readonly>
                                                <div class="input-group-addon">s/d</div>
                                                <input type="text" class="form-control date input-small" id="dateTo" value="<?php echo $now; ?>" readonly>
                                            </div>


                                            <div class="input-group select2-bootstrap-prepend">
                                                <div class="input-group-addon">Pelabuhan</div>
                                                <select id="port_origin" class="form-control js-data-example-ajax select2 input-small" dir="" name="port_origin">
                                                    <?php if($row_port!=0){} else { ?>
                                                    <option value="">Pilih</option>
                                                    <?php } foreach($port as $key=>$value) {?>
                                                    <option value="<?php echo $this->enc->encode($value->id); ?>"><?php echo strtoupper($value->name); ?></option>
                                                    <?php } ?>
                                                </select>
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
                                                            <a href="javascript:;" onclick="myData.changeSearch('No. Tiket','ticketNumber')">No. Tiket</a>
                                                        </li>
                                                        <li>
                                                            <a href="javascript:;" onclick="myData.changeSearch('Kode Booking','bookingCode')">Kode Booking</a>
                                                        </li>      
                                                        <li>
                                                            <a href="javascript:;" onclick="myData.changeSearch('Nama Pengguna Jasa','passName')">Nama Pengguna Jasa</a>
                                                        </li>
                                                        <li>
                                                            <a href="javascript:;" onclick="myData.changeSearch('Nama Perangkat','deviceName')">Nama Perangkat</a>
                                                        </li>
                                                        <li>
                                                            <a href="javascript:;" onclick="myData.changeSearch('Nama Kapal','shipName')" >Nama Kapal</a>
                                                        </li>                             
                                                    </ul>
                                                </div>
                                                <!-- /btn-group -->
                                                <input type="text" class="form-control" placeholder="Cari Data" data-name="boardingCode" name="searchData" id="searchData"> 
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
                                            <th>TANGGAL BOARDING</th>
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
                                            <th>KETERANGAN</th>
                                            
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
	var csfrData = {};
    csfrData[`<?php echo $this->security->get_csrf_token_name(); ?>`] =`<?php echo $this->security->get_csrf_hash(); ?>`;
    $.ajaxSetup({
        data: csfrData
    });     	    

    var myData=new MyData();
    jQuery(document).ready(function () {
        myData.init();

        $("#download_excel").click(function(event){
            var dateFrom=$("#dateFrom").val();
            var dateTo=$("#dateTo").val();
            var port=$("#port_origin").val();
            var search= $('.dataTables_filter input').val();
            var searchData = $('#searchData').val();
            var searchName = $("#searchData").attr('data-name');

            window.location.href="<?php echo site_url('transaction/boarding_passanger/download_excel?') ?>dateFrom="+dateFrom+"&dateTo="+dateTo+"&port="+port+"&searchData="+searchData+"&searchName="+searchName;
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

        $("#cari").on("click",function(){
            $(this).button('loading');
            myData.reload();
            $('#dataTables').on('draw.dt', function() {
                $("#cari").button('reset');
            });
        });                
        
    });
</script>
