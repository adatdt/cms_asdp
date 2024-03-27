
<style>
    td.details-control {
        cursor: pointer;

    }

    .datetimepicker-minutes {
      max-height: 200px;
      overflow: auto;
      display:inline-block;
    }    
    .form-control[disabled], .form-control[readonly], fieldset[disabled] .form-control {
        background-color: #eef1f5;
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
                                    <input type="text" class="form-control waktu input-small" id="dateFrom" placeholder="YYYY-MM-DD HH:II" readonly style="background-color: #ffffff;" >
                                    <div class="input-group-addon">s/d</div>
                                    <input type="text" class="form-control waktu input-small" id="dateTo" placeholder="YYYY-MM-DD HH:II" readonly style="background-color: #ffffff;" >
                                </div>

                                <div class="input-group pad-top">
                                    <div class="input-group-btn">
                                        <button type="button" class="btn green dropdown-toggle" data-toggle="dropdown" aria-expanded="false" id='btnData' >Kode                                            <i class="fa fa-angle-down"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <a href="javascript:;" onclick="myData.changeSearch('Kode','limitTransactionCode')">Kode</a>
                                            </li>                                                                                                 
                                        </ul>
                                    </div>
                                    <!-- /btn-group -->
                                    <input type="text" class="form-control" placeholder="Cari Data" data-name="limitTransactionCode" name="searchData" id="searchData"> 
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
                                <th></th>
                                <th>NO</th>
                                <th>KODE</th>
                                <th>TANGGAL MULAI</th>
                                <th>TANGGAL AKHIR</th>
                                <th>RANGE WAKTU PEMBATASAN</th>
                                <th>BATAS JUMLAH  <br> TRX</th> 
                                <th>CUSTOM RANGE WAKTU</th> 
                                <th>CUSTOM NOMINAL <br>JENIS PEMBATASAN</th>                                                            
                                <th>STATUS</th>
                                <!-- <th>WAKTU(DETIK)</th> -->
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

    let myData = new MyData();
    
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

        $("#btnTmbh").on("click",function(){
            arrayUserIdExcept=[]; // untuk menghapus data semntara di variable ini
        })
    });

</script>
