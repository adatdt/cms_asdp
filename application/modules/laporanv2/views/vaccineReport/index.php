<style type="text/css">
    th, td { white-space: nowrap; }
    div.dataTables_wrapper
    {
        /*width: 800px;*/
        margin: 0 auto;
    }


    /* div.DTFC_LeftBodyWrapper table 
    {
        
        margin-bottom: 0 !important;
        
    } */
    div.DTFC_LeftBodyLiner
    {
        overflow-x:hidden 
    }

    .DTFC_LeftBodyWrapper
    {
        margin-top : -10px !important;
    }

    #dataTables_processing
    {
        z-index: 1;
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
                    <span class="uppercase thin hidden-xs" id="datetime"></span>
                    <script type="text/javascript">
                        window.onload = date_time('datetime');
                    </script>
                </div>
            </div>
        </div>

        <?php 
            $now = date("Y-m-d");
            $last_week = date('Y-m-d', strtotime("-0 days")) 
        ?>

        <div class="my-div-body">
            <div class="portlet box blue-madison">
                <div class="portlet-title">

                    <div class="caption"><?php echo $title ?></div>
                    <div class="pull-right btn-add-padding"><?php echo $btn_add; ?></div>
                    <div class="pull-right btn-add-padding"><?php echo $btn_excel; ?>&nbsp;</div>
                    <div class="pull-right btn-add-padding"><?php echo $btn_pdf; ?>&nbsp;</div>
                </div>
                <div class="portlet-body">

                    <div class="row">
                        <div class="col-sm-12 form-inline">

                            <div class="input-group select2-bootstrap-prepend pad-top">
                                <div class="input-group-addon">Tanggal Masuk Pelabuhan</div>
                                <input type="text" class="form-control" placeholder="YYYY-MM-DD"  name="dateFrom" id="dateFrom" value="<?= $now ?>">
                                <div class="input-group-addon">s/d</div>
                                <input type="text" class="form-control" placeholder="YYYY-MM-DD"  name="dateTo" id="dateTo" value="<?= $last_week ?>">
                            </div>

                            <div class="input-group  pad-top">
                                <div class="input-group-addon">Jam</div>
                                <?php echo form_dropdown('startJam', $jam, '', ' class="form-control select2 input-small "  id="startJam" '); ?>
                            </div>


                            <div class="input-group pad-top">
                                <div class="input-group-addon">Pelabuhan</div>
                                <?php echo form_dropdown('port', $port, '', 'id="port" class="form-control select2"'); ?>
                            </div>

                            <div class="input-group pad-top">
                                <div class="input-group-addon">Layanan</div>
                                <?php echo form_dropdown('shipClass', $shipClass, '', 'id="shipClass" class="form-control select2"'); ?>
                            </div>

                            <div class="input-group pad-top">
                                <div class="input-group-addon">Jenis PJ</div>
                                <?php echo form_dropdown('service', $service, '', 'id="service" class="form-control select2"'); ?>
                            </div>

                            <div class="input-group pad-top">
                                <div class="input-group-addon">Golongan KND</div>
                                <?php echo form_dropdown('vehicleClass', $vehicleClass, '', 'id="vehicleClass" class="form-control select2"'); ?>
                            </div>

                            <div class="input-group pad-top">
                                <div class="input-group-addon">Jenis PNP</div>
                                <?php echo form_dropdown('passangerType', $passangerType, '', 'id="passangerType" class="form-control select2"'); ?>
                            </div>                                                                                    

                            <div class="input-group pad-top">
                                <div class="input-group-addon">STATUS</div>
                                <?php echo form_dropdown('masterStatus', $masterStatus, '', 'id="masterStatus" class="form-control select2"'); ?>
                            </div>      

                            <div class="input-group pad-top">
                                <div class="input-group-addon">STATUS VALIDATED</div>
                                <?php echo form_dropdown('statusValid', $statusValid, '', 'id="statusValid" class="form-control select2"'); ?>
                            </div>                                                                                                                


                            <div class="input-group pad-top">
                                <div class="input-group-btn">
                                    <button type="button" class="btn green dropdown-toggle" data-toggle="dropdown" aria-expanded="false" id='btnData'>Kode Booking
                                        <i class="fa fa-angle-down"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="javascript:;" onclick="myData.changeSearch('Kode Booking','bookingCode')">Kode Booking</a>
                                        </li>
                                        <li>
                                            <a href="javascript:;" onclick="myData.changeSearch('No. Tiket','ticketNumber')">No. Tiket</a>
                                        </li>
                                        <li>
                                            <a href="javascript:;" onclick="myData.changeSearch('No. Polisi','platNo')">No. Polisi</a>
                                        </li>
                                        <li>
                                            <a href="javascript:;" onclick="myData.changeSearch('Nama PJ','name')">Nama PJ</a>
                                        </li>
                                        <li>
                                            <a href="javascript:;" onclick="myData.changeSearch('NO. Identitas','idNo')">NO. Identitas</a>
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
                    <p></p>
                    <table class="table table-striped table-bordered table-hover table-checkable order-column" id="dataTables">
                        <thead>
                            <tr>
                                <th>NO</th>
                                <th>KODE BOOKING</th>
                                <th>NOMOR TIKET </th>
                                <th>PELABUHAN</th>
                                <th>JENIS PJ</th>
                                <th>LAYANAN</th>
                                <th>GOLONGAN KND</th>
                                <th>NO POLISI</th>
                                <th>JENIS PNP</th>
                                <th>NAMA</th>
                                <th>JENIS ID</th>
                                <th>NOMOR ID</th>
                                <th>USIA</th>
                                <th>JENIS KELAMIN</th>
                                <th>ALAMAT</th>
                                <th>TAMBAH MANIFEST</th>
                                <th>TANGGAL KEBERANGKATAN</th>
                                <th>JAM BERANGKAT</th>
                                <th>STATUS</th>
                                <th>NAMA KAPAL</th>
                                <th>STATUS VALIDASI</th>
                                <th>DOSIS</th>
                                <th>TES COVID</th>
                                <th>KETERANGAN</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include "fileJs.php" ?>
<script type="text/javascript">
    let myData = new MyData();

    jQuery(document).ready(function() {
        myData.init();

        $("#download_excel").click(function(event){
            var dateFrom=$("#dateFrom").val();
            var dateTo=$("#dateTo").val();
            var service=$("#service").val();
            var port=$("#port").val();
            var shipClass=$("#shipClass").val();
            var vehicleClass=$("#vehicleClass").val();
            var passangerType=$("#passangerType").val();
            var statusValid=$("#statusValid").val();
            var masterStatus=$("#masterStatus").val();
            var searchData = $('#searchData').val();
            var searchName = $("#searchData").attr('data-name');
            var startJam = document.getElementById('startJam').value;            

            window.location.href="<?php echo site_url('laporanv2/vaccineReport/download_excel?') ?>dateFrom="+dateFrom+"&dateTo="+dateTo+"&service="+service+"&port_origin="+port+"&searchData="+searchData+"&searchName="+searchName+"&dateTo="+dateTo+"&dateFrom="+dateFrom+"&startJam="+startJam+"&shipClass="+shipClass+"&masterStatus="+masterStatus+"&vehicleClass="+vehicleClass+"&passangerType="+passangerType+"&statusValid="+statusValid;
        });

        $("#download_pdf").click(function(event){
            var dateFrom=$("#dateFrom").val();
            var dateTo=$("#dateTo").val();
            var service=$("#service").val();
            var port=$("#port").val();
            var vehicleClass=$("#vehicleClass").val();
            var passangerType=$("#passangerType").val();
            var shipClass=$("#shipClass").val();
            var statusValid=$("#statusValid").val();
            var masterStatus=$("#masterStatus").val();
            var searchData = $('#searchData').val();
            var searchName = $("#searchData").attr('data-name');
            var startJam = document.getElementById('startJam').value;    

            window.location.href="<?php echo site_url('laporanv2/vaccineReport/download_pdf?') ?>dateFrom="+dateFrom+"&dateTo="+dateTo+"&service="+service+"&port_origin="+port+"&searchData="+searchData+"&searchName="+searchName+"&dateTo="+dateTo+"&dateFrom="+dateFrom+"&startJam="+startJam+"&shipClass="+shipClass+"&masterStatus="+masterStatus+"&vehicleClass="+vehicleClass+"&passangerType="+passangerType+"&statusValid="+statusValid;
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
            someDate.setMonth(someDate.getMonth() + 1);
            someDate.getFullYear();
            let endDate = myData.formatDate(someDate);

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


        $("#cari").on("click", function() {
            $(this).button('loading');
            myData.reload();
            $('#dataTables').on('draw.dt', function() {
                $("#cari").button('reset');
            });
        })


    });
</script>