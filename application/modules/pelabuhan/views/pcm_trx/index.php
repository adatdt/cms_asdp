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
                    <div class="pull-right btn-add-padding"><?php echo $btn_add; ?></div>
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
                                    <div class="input-group-addon">Golongan</div>
                                    <?php echo form_dropdown('vehicleClass', $vehicleClass, '', 'id="vehicleClass" class="form-control select2"'); ?>
                                </div>                                                                                        
                                <div class="input-group select2-bootstrap-prepend pad-top">
                                    <div class="input-group-addon">Jam</div>
                                    <?php echo form_dropdown('time', $time, '', 'id="time" class="form-control select2"'); ?>
                                </div>              


<!--                                <div class="input-group pad-top">
                                    <div class="input-group-btn">
                                        <button type="button" class="btn green dropdown-toggle" data-toggle="dropdown" aria-expanded="false" id='btnData' >No. Tiket
                                            <i class="fa fa-angle-down"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <a href="javascript:;" onclick="myData.changeSearch('No. Tiket','ticketNumber')">No. Tiket</a>
                                            </li>                          
                                        </ul>
                                    </div>

                                    <input type="text" class="form-control" placeholder="Cari Data" data-name="ticketNumber" name="searchData" id="searchData"> 
                                </div>                          
 -->
                                <div class="input-group pad-top">
                                    <button type="button" class="btn btn-danger mt-ladda-btn ladda-button" data-style="zoom-in" id="cari">
                                        <span class="ladda-label">Cari</span>
                                        <span class="ladda-spinner"></span>
                                    </button>
                                </div>                                                                                                                                            

                            </div>

                        </div>
                    </div>
                    <table class="table table-striped table-bordered table-hover table-checkable order-column" id="dataTables">
                        <thead>
                            <tr>
                                <th>NO</th>
                                <th>PELABUHAN</th>
                                <th>GOLONGAN</th>
                                <th>KELAS LAYANAN</th>
                                <th>TANGGAL KEBERANGKATAN</th>
                                <th>JAM KEBERANGKATAN</th>
                                <th>QUOTA DIINPUT</th>
                                <th>TOTAL QUOTA TERSEDIA</th>
                                <th>QUOTA YANG DI GUNAKAN</th>
                                <!-- <th>STATUS</th> -->
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

        // $("#port").on("change",function(){
        //     myData.reload();
        // });

        // $("#shipClass").on("change",function(){
        //     myData.reload();
        // });

        // $("#vehicleClass").on("change",function(){
        //     myData.reload();
        // });        

        // $(".date").on("change",function(){
        //     myData.reload();
        // });

        // $("#time").on("change",function(){
        //     myData.reload();
        // });        

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
