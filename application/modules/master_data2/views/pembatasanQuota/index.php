<style>
    td.details-control {
        cursor: pointer;
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


        <div class="my-div-body">
            <div class="portlet box blue-madison">
                <div class="portlet-title">
                    
                    <div class="caption"><?php echo $title ?></div>
                    <div class="pull-right btn-add-padding"><?php echo $btn_add; ?>

                        <?= $btn_excel." ".$btn_pdf ?>
                                        
                    </div>
                </div>
                <?php $now = date("Y-m-d");
                    $last_week = date('Y-m-d', strtotime("-0 days"))
                ?>

                <div class="portlet-body">
                    <div class=" form-inline " align="left">

                        <div class="input-group select2-bootstrap-prepend pad-top">
                            <div class="input-group-addon">Tanggal Berlaku dan Akhir Berlaku</div>
                            <input type="text" class="form-control  date input-small" id="dateFrom" value="<?php echo $last_week; ?>" >
                            <div class="input-group-addon">s/d</div>
                            <input type="text" class="form-control  date input-small" id="dateTo" value="<?php echo $now; ?>" >
                        </div>

                        <div class="input-group select2-bootstrap-prepend pad-top">
                            <div class="input-group-addon">Pelabuhan</div>
                            <?php echo form_dropdown('portId', $port, '', 'id="portId" class="form-control select2"'); ?>
                        </div>

                        <div class="input-group select2-bootstrap-prepend pad-top">
                            <div class="input-group-addon">Layanan</div>
                            <?php echo form_dropdown('shipClass', $shipClass, '', 'id="shipClass" class="form-control select2"'); ?>
                        </div>

                        <div class="input-group select2-bootstrap-prepend pad-top">
                            <div class="input-group-addon">Golongan</div>
                            <?php echo form_dropdown('vehicleClassId', $vehicleClassId, '', 'id="vehicleClassId" class="form-control select2"'); ?>
                        </div>

                        <div class="input-group pad-top">
                            <button type="button" class="btn btn-danger mt-ladda-btn ladda-button" data-style="zoom-in" onClick="myData.cariDataDetail()" id="cari" >
                                <span class="ladda-label">Cari</span>
                                <span class="ladda-spinner"></span>
                            </button>
                        </div>

                    </div>                    
                    <p></p>
                    <table class="table table-striped table-bordered table-hover table-checkable order-column" id="dataTables">
                        <thead>
                            <tr>
                                <!-- <th></th> -->
                                <th>NO</th>
                                <th>PELABUHAN</th>
                                <th>LAYANAN</th>
                                <th>JENIS PJ</th>
                                <th>GOLONGAN</th>
                                <th>BATAS QUOTA</th>
                                <th>BATAS LINEMETER</th>
                                <th>TANGGAL BERLAKU</th>
                                <th>AKHIR BERLAKU</th>
                                <th>JAM</th>
                                <th>STATUS</th>
                                <th>
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    AKSI
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                </th> 
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

    myData = new MyData();
    
    jQuery(document).ready(function () {
        myData.init();

        // $("#port").on("change",function(){
        //     table.reload();
        // });

        // $("#team").on("change",function(){
        //     table.reload();
        // });

        $("#download_excel").click(function(event) {
            let dateFrom = $("#dateFrom").val();
            let dateTo = $("#dateTo").val();
            let shipClass = $("#shipClass").val();
            let portId = $("#portId").val();
            let vehicleClassId = $("#vehicleClassId").val();
  
            window.location.href = "<?php echo site_url('master_data2/pembatasanQuota/download_excel?') ?>dateFrom=" + dateFrom + "&dateTo=" + dateTo + "&shipClass=" + shipClass + "&portId=" + portId + "&vehicleClassId=" + vehicleClassId ;
        });

        $("#download_pdf").click(function(event) {
            let dateFrom = $("#dateFrom").val();
            let dateTo = $("#dateTo").val();
            let shipClass = $("#shipClass").val();
            let portId = $("#portId").val();
            let vehicleClassId = $("#vehicleClassId").val();
  
            window.location.href = "<?php echo site_url('master_data2/pembatasanQuota/download_pdf?') ?>dateFrom=" + dateFrom + "&dateTo=" + dateTo + "&shipClass=" + shipClass + "&portId=" + portId + "&vehicleClassId=" + vehicleClassId ;
        });
        
        $('.date').datepicker({
            format: 'yyyy-mm-dd',
            changeMonth: true,
            changeYear: true,
            autoclose: true,
            todayHighlight: true,           
        });
        
        $("#cari").on("click",function(){
            $(this).button('loading');
            myData.reload();
            // $("#cari").button('reset');
            $('#dataTables').on('draw.dt', function() {
                $("#cari").button('reset');
            });
        });
        
    });

</script>
