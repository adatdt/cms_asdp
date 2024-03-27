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

        <?php $now=date("Y-m-d"); $last_week=date('Y-m-d',strtotime("-0 days"))?>
        <div class="my-div-body">
            <div class="portlet box blue-madison">
                <div class="portlet-title">
                    
                    <div class="caption"><?php echo $title ?></div>
                    <div class="pull-right btn-add-padding">
                        <?php if ($btn_excel) {?>
                            <button  class="btn btn-sm btn-warning download" id="download_excel">Excel</button>
                        <?php } ?>

                        <?php if ($btn_pdf) {?>
                            <button  class="btn btn-sm btn-warning download" id="download_pdf" >Pdf</button>
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


                                            <div class="input-group select2-bootstrap-prepend pad-top">
                                                <div class="input-group-addon">Pelabuhan</div>
                                                <?php echo form_dropdown('port', $port, '', 'id="port" class="form-control select2"'); ?>
                                            </div>


                                            <div class="input-group select2-bootstrap-prepend pad-top">
                                                <div class="input-group-addon">Check In</div>
                                                <input type="text" class="form-control date input-small" id="dateFrom" value="<?php echo $last_week; ?>" readonly>
                                                <div class="input-group-addon">s/d</div>
                                                <input type="text" class="form-control date input-small" id="dateTo" value="<?php echo $now; ?>" readonly>
                                            </div>

                                            <!-- <div class="input-group select2-bootstrap-prepend pad-top">
                                                <div class="input-group-addon">Check In</div>
                                                <input type="text" class="form-control date input-small" id="checkinDateFrom"  readonly placeholder="YYYY-MM-DD">
                                                <div class="input-group-addon">s/d</div>
                                                <input type="text" class="form-control date input-small" id="checkinDateTo" readonly placeholder="YYYY-MM-DD">
                                            </div> -->
            

                                            <!-- <div class="input-group select2-bootstrap-prepend pad-top ">
                                                <div class="input-group-addon">Lintasan</div>
                                                <?php echo form_dropdown('route',$route, '', 'id="route" class="form-control select2 input-small"'); ?>
                                            </div>                                                                                   
                                            <div class="input-group select2-bootstrap-prepend pad-top">
                                                <div class="input-group-addon">Kelas Layanan</div>
                                                <?php echo form_dropdown('sipClass',$shipClass, '', 'id="shipClass" class="form-control select2"'); ?>
                                            </div> -->

                                            <div class="input-group pad-top">
                                                <div class="input-group-btn">
                                                    <button type="button" class="btn green dropdown-toggle" data-toggle="dropdown" aria-expanded="false" id='btnData' >Kode Booking
                                                        <i class="fa fa-angle-down"></i>
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        <li>
                                                            <a href="javascript:;" onclick="myData.changeSearch('Kode Booking','booking')">Kode Booking</a>
                                                        </li>
                                                        <li>
                                                            <a href="javascript:;" onclick="myData.changeSearch('Plat Number','plat')">Plat Number</a>
                                                        </li>
                                                        <li>
                                                            <a href="javascript:;" onclick="myData.changeSearch('Nomor Tiket','ticket')">Nomor Tiket</a>
                                                        </li>
                                                    </ul>
                                                </div>
                                                <!-- /btn-group -->
                                                <input type="text" class="form-control" placeholder="Cari Data" data-name="booking" name="searchData" id="searchData"> 
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


                                <table class="table table-bordered table-hover table-striped" id="dataTables">
                                    <thead>
                                        <tr>
                                            <th>NO</th>
                                            <th>TANGGAL CHECK IN</th>
                                            <th>PELABUHAN</th>
                                            <th>KODE BOOKING</th>
                                            <th>NO TIKET</th>
                                            <th>PLAT NUMBER</th>
                                            <th>GOLONGAN KETIKA CETAK</th>
                                            <th>PANJANG DARI SENSOR</th>
                                            <th>TINGGI DARI SENSOR</th>
                                            <th>LEBAR DARI SENSOR</th>
                                            <th>BERAT DARI SENSOR</th>
                                            <th>GOLONGAN SESUAI SENSOR</th>
                                            <th>STATUS</th>
                                            <th>PATH GAMBAR</th>
                                            <!-- <th>STATUS TIKET</th>
                                            <th>GOLONGAN PADA PEMESANAN</th>
                                            <th>TARIF PEMESANAN/ LAMA</th>
                                            <th>GOLONGAN CHECKIN</th>
                                            <th>TARIF CHECKIN</th> -->
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

    jQuery(document).ready(function () {
        myData.init;

        $("#download_excel").click(function(event){
            var dateFrom=$("#dateFrom").val();
            var dateTo=$("#dateTo").val();
            var port=$("#port").val();
            var route=$("#route").val();
            var shipClass=$("#shipClass").val();
            var channel=$("#channel").val();
            var search=$('div #dataTables_filter input').val();

            window.location.href="<?php echo site_url('transaction2/vehicleOverPaid/download_excel?') ?>dateFrom="+dateFrom+"&dateTo="+dateTo+"&port="+port+"&route="+route+"&shipClass="+shipClass+"&search="+search;
        });

        $("#download_pdf").click(function(event){
            var dateFrom=$("#dateFrom").val();
            var dateTo=$("#dateTo").val();
            var port=$("#port").val();
            var route=$("#route").val();
            var shipClass=$("#shipClass").val();
            var channel=$("#channel").val();
            var search=$('div #dataTables_filter input').val();

            window.open("<?php echo site_url('transaction2/vehicleOverPaid/download_pdf?') ?>dateFrom="+dateFrom+"&dateTo="+dateTo+"&port="+port+"&route="+route+"&shipClass="+shipClass+"&search="+search);
        });        

        // $('.date').datepicker({
        //     format: 'yyyy-mm-dd',
        //     changeMonth: true,
        //     changeYear: true,
        //     autoclose: true,
        //     todayHighlight: true,           
        // });

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
            // myData.reload();
        });

        setTimeout(function() {
            $('.menu-toggler').trigger('click');
        }, 1);

        // $(".date").change(()=>{
        //     myData.route({"port":$("#port").val()});
        //     // myData.reload;
        // })

        // $("#port").change(()=>{
        //     myData.route({"port":$("#port").val()});
        //     // myData.reload;
        // })

        $("#cari").on("click",function(){
            $(this).button('loading');
            myData.reload;
            $('#dataTables').on('draw.dt', function() {
                $("#cari").button('reset');
            });
        });


    });
</script>
