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
                    <div class="pull-right btn-add-padding"><?php echo $btn_add; ?></div>
                </div>
                <div class="portlet-body">
                <div class="table-toolbar">
                        <div class="row">
                            <div class="col-sm-12 form-inline">

                                <!-- 2 hari  -->
                                <?php $now = date("Y-m-d H:i");
                                $last_week = date('Y-m-d H:i', strtotime("-0 days")) ?>                            

                                <div class="input-group select2-bootstrap-prepend">
                                    <div class="input-group-addon">Tanggal Mulai</div>
                                    <input type="text" class="form-control waktu input-small" id="dateFrom" placeholder="YYYY-MM-DD HH:II" readonly style="background-color:#ffff">
                                    <div class="input-group-addon">s/d</div>
                                    <input type="text" class="form-control waktu input-small" id="dateTo" placeholder="YYYY-MM-DD HH:II" readonly style="background-color:#ffff" >
                                </div>

                                <div class="input-group pad-top">

                                    <!-- /btn-group -->
                                    <?= form_dropdown("shipClass",$shipClass,"",' class="form-control select2"  id="shipClass" ') ?>     
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
                    <p></p>
                    <table class="table table-striped table-bordered table-hover table-checkable order-column" id="dataTables">
                        <thead>
                            <tr>
                                <th>NO</th>
                                <th>TANGGAL MULAI</th>
                                <th>TANGGAL AKHIR</th>
                                <th>LAYANAN</th>
                                <th>NOMINAL PEMBATASAN</th>
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

    const myData = new MyData();
    jQuery(document).ready(function () {
        myData.init();

        $("#cari").on("click",function(){
            $(this).button('loading');
            myData.reload();
            $('#dataTables').on('draw.dt', function() {
                $("#cari").button('reset');
            });
        });        

        $('.waktu').datetimepicker({
            format: 'yyyy-mm-dd hh:ii',
            minuteStep:1,
            changeMonth: true,
            changeYear: true,
            autoclose: true,
            todayHighlight: true,
            // endDate: "<?php echo date('Y-m-d H:i',strtotime('-5 minutes')) ?>",
        });                
        
    });

</script>
