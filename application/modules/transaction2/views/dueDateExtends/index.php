<link href="<?php echo base_url()?>assets/global/plugins/ladda/ladda-themeless.min.css" rel="stylesheet" type="text/css" />

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
                    <div class="pull-right btn-add-padding"><?php echo $btn_add; ?></div>
<!--                     <div class="pull-right btn-add-padding">
                        <?php if ($btn_excel) {?>
                            <button  class="btn btn-sm btn-warning download" id="download_excel">Excel</button>
                        <?php } ?>

                        <?php if ($btn_pdf) {?>
                            <button  class="btn btn-sm btn-warning download" id="download_pdf" >Pdf</button>
                        <?php } ?>
                    </div> -->
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
                                                <div class="input-group-addon">Tanggal</div>
                                                <input type="text" class="form-control date input-small" id="dateFrom" value="<?php echo $last_week; ?>" readonly>
                                                <div class="input-group-addon">s/d</div>
                                                <input type="text" class="form-control date input-small" id="dateTo" value="<?php echo $now; ?>" readonly>
                                            </div>

                                        </div>

                                    </div>
                                </div>

                                <table class="table table-bordered table-hover table-striped" id="dataTables">
                                    <thead>
                                        <tr>
                                            <th>NO</th>
                                            <th>TANGGAL</th>
                                            <th>NOMER INVOICE</th>
                                            <th>KODE BOOKING</th>
                                            <th>RUTE</th>
                                            <th>JAM PERPANJANGAN</th>
                                            <th>DUE DATE LAMA</th>
                                            <th>DUE DATE BARU</th>
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

<script src="<?php echo base_url() ?>assets/global/plugins/ladda/spin.min.js" type="text/javascript"></script>
<script src="<?php echo base_url() ?>assets/global/plugins/ladda/ladda.min.js" type="text/javascript"></script>

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

            window.location.href="<?php echo site_url('transaction2/vehicleUnderPaid/download_excel?') ?>dateFrom="+dateFrom+"&dateTo="+dateTo+"&port="+port+"&route="+route+"&shipClass="+shipClass+"&search="+search;
        });

        $("#download_pdf").click(function(event){
            var dateFrom=$("#dateFrom").val();
            var dateTo=$("#dateTo").val();
            var port=$("#port").val();
            var route=$("#route").val();
            var shipClass=$("#shipClass").val();
            var channel=$("#channel").val();
            var search=$('div #dataTables_filter input').val();

            window.open("<?php echo site_url('transaction2/vehicleUnderPaid/download_pdf?') ?>dateFrom="+dateFrom+"&dateTo="+dateTo+"&port="+port+"&route="+route+"&shipClass="+shipClass+"&search="+search);
        });        

        $('.date').datepicker({
            format: 'yyyy-mm-dd',
            changeMonth: true,
            changeYear: true,
            autoclose: true,
            todayHighlight: true,           
        });

        setTimeout(function() {
            $('.menu-toggler').trigger('click');
        }, 1);

        $(".date").change(()=>{
            // myData.route({"port":$("#port").val()});
            myData.reload;
        })

        $("#port").change(()=>{
            myData.route({"port":$("#port").val()});
            myData.reload;
        })

        $("#shipClass").change(()=>{
            myData.reload;
        })

        $("#channel").change(()=>{
            myData.reload;
        })

        $("#route").change(()=>{
            myData.reload;
        })                        

        // $("#cari").click(function(){
        //     var l = Ladda.create(this);
        //     l.start();

        //    setTimeout(function(){l.stop(); }, 1500);
        // });        



    });
</script>
