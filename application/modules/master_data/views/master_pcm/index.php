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
        
        <?php //$now=date("Y-m-d")." 23:00"; $last_week=date('Y-m-d',strtotime("-1 days"))." 00:00"?>

        <?php $now=date("Y-m-d"); $last_week=date('Y-m-d',strtotime("-1 days"))?>
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

                                <div class="input-group select2-bootstrap-prepend">
                                    <div class="input-group-addon">Tanggal Berlaku</div>
<!--                                     <input type="text" class="form-control date input-small" id="dateFrom" value="<?php echo $last_week; ?>" readonly>
                                    <div class="input-group-addon">s/d</div>
                                    <input type="text" class="form-control date input-small" id="dateTo" value="<?php echo $now; ?>" readonly>
 -->
                                    <input type="text" class="form-control date input-small" id="dateFrom"  readonly placeholder="yyyy-mm-dd">
                                    <div class="input-group-addon">s/d</div>
                                    <input type="text" class="form-control date input-small" id="dateTo" readonly placeholder="yyyy-mm-dd">                                    
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
                                <th>KELAS LAYANAN</th>
                                <th>QUOTA</th>
                                <th>TANGGAL BERLAKU</th>
                                <th>JAM BERLAKU</th>
                                <th>TOTAL LINE METER</th>
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
    var csfrData = {};
    csfrData[`<?php echo $this->security->get_csrf_token_name(); ?>`] =`<?php echo $this->security->get_csrf_hash(); ?>`;
    $.ajaxSetup({
        data: csfrData
    });    

    const myData = new MyData() 
    
    jQuery(document).ready(function () {

        // $('.date').datetimepicker({
        //     format: 'yyyy-mm-dd hh:ii',
        //     changeMonth: true,
        //     changeYear: true,
        //     autoclose: true,
        //     todayHighlight: true,
        //     minView:1
        // });

        $('.date').datepicker({
            format: 'yyyy-mm-dd',
            changeMonth: true,
            changeYear: true,
            autoclose: true,
            todayHighlight: true,
        });                

        myData.init();

        $("#port").on("change",function(){
            myData.reload();
        });

        $(".date").on("change",function(){
            myData.reload();
        });        

        $("#shipClass").on("change",function(){
            myData.reload();
        });

        $("#time").on("change",function(){
            myData.reload();
        });



        
    });

</script>
