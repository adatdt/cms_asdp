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
                            <a  class="btn btn-sm btn-warning download" id="download_pdf" target="_blank">Pdf</a>
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
                                                <div class="input-group-addon">Keberangkatan</div>
                                                <input type="text" class="form-control date input-small" id="dateFrom" value="<?php echo $last_week; ?>" readonly>
                                                <div class="input-group-addon">s/d</div>
                                                <input type="text" class="form-control date input-small" id="dateTo" value="<?php echo $now; ?>" readonly>
                                            </div>

                                            <div class="input-group select2-bootstrap-prepend pad-top">
                                                <div class="input-group-addon">Pembayaran</div>
                                                <input type="text" class="form-control date input-small" id="paymentDateFrom"  readonly placeholder="YYYY-MM-DD">
                                                <div class="input-group-addon">s/d</div>
                                                <input type="text" class="form-control date input-small" id="paymentDateTo" readonly placeholder="YYYY-MM-DD">
                                            </div>


                                            <div class="input-group select2-bootstrap-prepend pad-top">
                                                <div class="input-group-addon">Pelabuhan</div>
                                                <?php echo form_dropdown('port', $port, '', 'id="port" class="form-control select2"'); ?>
                                            </div>            

                                            <div class="input-group select2-bootstrap-prepend pad-top ">
                                                <div class="input-group-addon">Lintasan</div>
                                                <?php echo form_dropdown('route',$route, '', 'id="route" class="form-control select2 input-small"'); ?>
                                            </div>                                                                                   
                                            <div class="input-group select2-bootstrap-prepend pad-top">
                                                <div class="input-group-addon">Kelas Layanan</div>
                                                <?php echo form_dropdown('sipClass',$shipClass, '', 'id="shipClass" class="form-control select2"'); ?>
                                            </div>

                                            <div class="input-group select2-bootstrap-prepend pad-top">
                                                <div class="input-group-addon">Channel</div>
                                                <?php echo form_dropdown('channel', $channel, '', 'id="channel" class="form-control select2"'); ?>
                                            </div>


                                        </div>

                                    </div>
                                </div>


                                <table class="table table-bordered table-hover table-striped" id="dataTables">
                                    <thead>
                                        <tr>
                                            <th>NO</th>
                                            <th>KODE BOOKING</th>
                                            <th>NO TIKET</th>
                                            <th>PANJANG PADA <br />PEMESANAN (METER)</th>
                                            <th>GOLONGAN</th>
                                            <th>KELAS LAYANAN</th>
                                            <th>TARIF GOLONGAN (Rp.)</th>
                                            <th>WAKTU PEMBAYARAN</th>
                                            <th>JADWAL KEBERANGKATAN</th>
                                            <th>LINTASAN DIPESAN</th>
                                            <th>STATUS TIKET</th>
                                            <th>NO INVOICE</th>
                                            <th>SALES CHANNEL</th>
                                            <th>TRANSFER DANA (Rp.)</th>
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
            var search=$("div #dataTables_filter input").val();

            window.location.href="<?php echo site_url('transaction2/vehicleReservation/download_excel?') ?>dateFrom="+dateFrom+"&dateTo="+dateTo+"&port="+port+"&route="+route+"&shipClass="+shipClass+"&channel="+channel+"&search="+search;
        });

        $("#download_pdf").click(function(event){
            var dateFrom=$("#dateFrom").val();
            var dateTo=$("#dateTo").val();
            var port=$("#port").val();
            var route=$("#route").val();
            var shipClass=$("#shipClass").val();
            var channel=$("#channel").val();
            var search=$("div #dataTables_filter input").val();

            window.open("<?php echo site_url('transaction2/vehicleReservation/download_pdf?') ?>dateFrom="+dateFrom+"&dateTo="+dateTo+"&port="+port+"&route="+route+"&shipClass="+shipClass+"&channel="+channel+"&search="+search);
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
            myData.route({"port":$("#port").val()});
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


    });
</script>
