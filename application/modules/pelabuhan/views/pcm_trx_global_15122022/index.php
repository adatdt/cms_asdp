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
                        <?php 
                            if($editExcel) 
                            {
                                echo $btn_excel ;
                            }

                            if($downloadExcel) 
                            {
                                echo ' <button  class="btn btn-sm btn-warning download" id="download_excel">Excel</button> ';
                            }
                        ?>
                        
                    </div>

                </div>
                <div class="portlet-body">
                    <div class="table-toolbar">
                        <div class="row">
                            <div class="col-sm-12 form-inline">

                                <div class="input-group select2-bootstrap-prepend pad-top">
                                    <div class="input-group-addon">Keberangkatan</div>
                                    <input type="text" class="form-control date input-small" id="dateFrom" value="<?php echo $last_week; ?>" readonly>
                                    <div class="input-group-addon">s/d</div>
                                    <input type="text" class="form-control date input-small" id="dateTo" value="<?php echo $now; ?>" readonly>
                                </div>

                                <div class="input-group select2-bootstrap-prepend pad-top">
                                    <div class="input-group-addon">Pelabuhan</div>
                                    <?php echo form_dropdown('port', $port, '', 'id="port" class="form-control select2"'); ?>
                                </div>

                                <div class="input-group select2-bootstrap-prepend pad-top">
                                    <div class="input-group-addon">Kelas Layanan</div>
                                    <?php echo form_dropdown('shipClass', $shipClass, '', 'id="shipClass" class="form-control select2"'); ?>
                                </div>

                                <div class="input-group select2-bootstrap-prepend pad-top">
                                    <div class="input-group-addon">Jam</div>
                                    <?php echo form_dropdown('time', $time, '', 'id="time" class="form-control select2"'); ?>
                                </div>                                

                            </div>

                        </div>
                    </div>
                    <table class="table table-striped table-bordered table-hover table-checkable order-column" id="dataTables">
                        <thead>
                            <tr>
                                <th>NO</th>
                                <th>PELABUHAN</th>
                                <th>KELAS <br>LAYANAN</th>
                                <th>TANGGAL <br>KEBERANGKATAN</th>
                                <th>JAM <br>KEBERANGKATAN</th>
                                <th>QUOTA <br>DIINPUT</th>
                                <th>TOTAL QUOTA <br>TERSEDIA</th>
                                <th>QUOTA YANG <br>DI GUNAKAN</th>
                                <th>QUOTA KHUSUS <br>DI RESERVE</th>
                                <th>TOTAL <br>LINEMETER</th>
                                <th>LINEMETER <br>TERSEDIA</th>
                                <th>LINEMETER <br>DIGUNAKAN</th>
                                <th>AKSI</th>
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

    const myData = new MyData() 
    
    jQuery(document).ready(function () {
        
        $('.date').datepicker({
            format: 'yyyy-mm-dd',
            changeMonth: true,
            changeYear: true,
            autoclose: true,
            todayHighlight: true,           
        });

        myData.init();

        $("#download_excel").click(function(event){
            var dateFrom=$("#dateFrom").val();
            var dateTo=$("#dateTo").val();
            var time=$("#time").val();
            var port=$("#port").val();
            var shipClass=$("#shipClass").val();
            var search= $('.dataTables_filter input').val();

            const url="<?php echo site_url('pelabuhan/pcm_trx_global/downloadExcel?') ?>dateFrom="+dateFrom+"&dateTo="+dateTo+"&time="+time+"&port="+port+"&search="+search+"&shipClass="+shipClass;
            
            myData.directUrl(url);
        });        

        $("#port").on("change",function(){
            myData.reload();
        });

        $("#shipClass").on("change",function(){
            myData.reload();
        });

        $("#time").on("change",function(){
            myData.reload();
        });        

        $(".date").on("change",function(){
            myData.reload();
        });



        
    });

</script>
