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
        $last_week = date('Y-m-d', strtotime("-0 days")) ?>
        <div class="my-div-body">
            <div class="portlet box blue-madison">
                <div class="portlet-title">

                    <div class="caption"><?php echo $title ?></div>
                    <div class="pull-right btn-add-padding"><?php echo $btn_excel; ?>&nbsp;</div>
                    <div class="pull-right btn-add-padding"><?php echo $btn_pdf; ?>&nbsp;</div>
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
                                                <div class="input-group-addon">Tanggal Masuk Pelabuhan</div>
                                                <input type="text" class="form-control  input-small" id="dateFrom" value="<?php echo $last_week; ?>" readonly>
                                                <div class="input-group-addon">s/d</div>
                                                <input type="text" class="form-control  input-small" id="dateTo" value="<?php echo $now; ?>" readonly>
                                            </div>

                                            <div class="input-group select2-bootstrap-prepend pad-top ">
                                                <div class="input-group-addon">Jam Masuk</div>
                                                <?= form_dropdown("dataJam",$dataJam,"",'id="dataJam" class="form-control js-data-example-ajax select2 input-small"') ?>
                                            </div>

                                            <div class="input-group select2-bootstrap-prepend pad-top ">
                                                <div class="input-group-addon">Pelabuhan</div>
                                                <?= form_dropdown("port",$port,"",'id="port" class="form-control js-data-example-ajax select2 input-small"') ?>
                                            </div>

                                            <div class="input-group select2-bootstrap-prepend pad-top ">
                                                <div class="input-group-addon">Jenis Layanan</div>
                                                <?= form_dropdown("shipClass",$shipClass,"",'id="shipClass" class="form-control js-data-example-ajax select2 input-small"') ?>
                                            </div>       
                                                                                              

                                            <div class="input-group select2-bootstrap-prepend pad-top ">
                                                <div class="input-group-addon">Jenis PJ</div>
                                                <?= form_dropdown("service",$service,"",'id="service" class="form-control js-data-example-ajax select2 input-small"') ?>

                                            </div>


                                            <div class="input-group select2-bootstrap-prepend pad-top ">
                                                <div class="input-group-addon">Golongan KND</div>
                                                <?= form_dropdown("vehicleClass",$vehicleClass,"",'id="vehicleClass" class="form-control js-data-example-ajax select2 input-small"') ?>
                                            </div>     
                                            
                                            <div class="input-group select2-bootstrap-prepend pad-top ">
                                                <div class="input-group-addon">Golongan PNP</div>
                                                <?= form_dropdown("passangerType",$passangerType,"",'id="passangerType" class="form-control js-data-example-ajax select2 input-small"') ?>

                                            </div> 

                                            <div class="input-group select2-bootstrap-prepend pad-top ">
                                                <div class="input-group-addon">Status Tiket</div>
                                                <?= form_dropdown("statusTicket",$statusTicket,"",'id="statusTicket" class="form-control js-data-example-ajax select2 input-small"') ?>

                                            </div> 

                                            <div class="input-group select2-bootstrap-prepend pad-top ">
                                                <div class="input-group-addon">Status Validasi</div>
                                                <?= form_dropdown("dataValidasi",$dataValidasi,"",'id="dataValidasi" class="form-control js-data-example-ajax select2 input-small"') ?>

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
                                                            <a href="javascript:;" onclick="myData.changeSearch('No Tiket','ticketNumber')">No Tiket</a>
                                                        </li>
                                                        <li>
                                                            <a href="javascript:;" onclick="myData.changeSearch('No Polisi','platNumber')">No Polisi</a>
                                                        </li>
                                                        <li>
                                                            <a href="javascript:;" onclick="myData.changeSearch('Nama Penumpang','passangerName')">Nama Penumpang</a>
                                                        </li>
                                                        <li>
                                                            <a href="javascript:;" onclick="myData.changeSearch('No Identitas','identitas')">No Identitas</a>
                                                        </li>
                                                        <li>
                                                            <a href="javascript:;" onclick="myData.changeSearch('User Verifikator','verificationUser')">User Verifikator</a>
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


                                <table class="table table-bordered table-hover table-striped" id="dataTables">
                                    <thead>
                                        <tr>
                                            <th>NO</th>
                                            <th>KODE BOOKING</th>
                                            <th>NOMOR TIKET</th>
                                            <th>PELABUHAN ASAL</th>
                                            <th>JENIS PJ</th>
                                            <th>LAYANAN</th>
                                            <th>GOLONGAN KND</th>
                                            <th>GOLONGAN PNP</th>
                                            <th>NO POLISI</th>
                                            <th>NAMA PENUMPANG</th>
                                            <th>JENIS IDENTITAS</th>
                                            <th>NO IDENTITAS</th>
                                            <th>UMUR</th>
                                            <th>JENIS KELAMIN</th>
                                            <th>DOMISILI</th>
                                            <th>TANGGAL MASUK PELABUHAN</th>
                                            <th>JAM MASUK PELABUHAN</th>
                                            <th>STATUS TIKET</th>
                                            <th>WAKTU CHECKIN</th>
                                            <th>WAKTU GATEIN</th>
                                            <th>WAKTU BOARDING</th>
                                            <th>STATUS VERIFIKATOR</th>
                                            <th>USER VERIFIKATOR</th>
                                            <th>WAKTU VERIFIKASI</th>
                                            <th>PERANGKAT VERIFIKASI</th>
                                            <th>ID PERANGKAT</th>

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

<?php include "fileJs.php"?>
<script type="text/javascript">
    myData= new MyData();
    jQuery(document).ready(function() {
        myData.init();

        $("#download_pdf").click(function(event){

                     
            let paramPdf = "dateTo="+document.getElementById('dateTo').value +
            "&dateFrom="+document.getElementById('dateFrom').value+
            "&port="+document.getElementById('port').value+
            "&shipClass="+document.getElementById('shipClass').value+
            "&passangerType="+document.getElementById('passangerType').value+
            "&vehicleClass="+document.getElementById('vehicleClass').value+
            "&service="+document.getElementById('service').value+
            "&statusTicket="+ document.getElementById('statusTicket').value+
            "&dataValidasi="+ document.getElementById('dataValidasi').value+
            "&dataJam="+document.getElementById('dataJam').value+
            "&searchData="+document.getElementById('searchData').value+
            "&searchName="+$('#searchData').attr('data-name')

            window.open("<?php echo site_url('transaction/verifikator/download_pdf?') ?>"+paramPdf)

        });


        $("#download_excel").click(function(event) {

            let paramExcel = "dateTo="+document.getElementById('dateTo').value +
            "&dateFrom="+document.getElementById('dateFrom').value+
            "&port="+document.getElementById('port').value+
            "&shipClass="+document.getElementById('shipClass').value+
            "&passangerType="+document.getElementById('passangerType').value+
            "&vehicleClass="+document.getElementById('vehicleClass').value+
            "&service="+document.getElementById('service').value+
            "&statusTicket="+ document.getElementById('statusTicket').value+
            "&dataValidasi="+ document.getElementById('dataValidasi').value+
            "&dataJam="+document.getElementById('dataJam').value+
            "&searchData="+document.getElementById('searchData').value+
            "&searchName="+$('#searchData').attr('data-name')

            window.location.href = "<?php echo site_url('transaction/verifikator/download_excel?') ?>"+paramExcel;
        });

        $('.date').datepicker({
            format: 'yyyy-mm-dd',
            changeMonth: true,
            changeYear: true,
            autoclose: true,
            todayHighlight: true,           
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
            // myData.reload();
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