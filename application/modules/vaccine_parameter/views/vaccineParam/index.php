
<style>
    td.details-control {
        cursor: pointer;

    }

    .datetimepicker-minutes {
      max-height: 200px;
      overflow: auto;
      display:inline-block;
    }

    .scrolling 
    {
        max-height: 500px;
        overflow-y: auto;
    }

    .scrolling::-webkit-scrollbar {
        width: 10px;
    }

    .scrolling::-webkit-scrollbar-track {
    box-shadow: inset 0 0 5px grey; 
    border-radius: 10px;
    }    

    .scrolling::-webkit-scrollbar-thumb {
    background: #c1c1c1; 
    border-radius: 10px;
    }

    .scrolling::-webkit-scrollbar-thumb:hover {
        background: #c1c1c1;
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
                                    <input type="text" class="form-control waktu input-small" id="dateFrom" placeholder="YYYY-MM-DD HH:II" readonly>
                                    <div class="input-group-addon">s/d</div>
                                    <input type="text" class="form-control waktu input-small" id="dateTo" placeholder="YYYY-MM-DD HH:II" readonly>
                                </div>

                                <div class="input-group pad-top">
                                    <div class="input-group-btn">
                                        <button type="button" class="btn green dropdown-toggle" data-toggle="dropdown" aria-expanded="false" id='btnData' >Tipe Assessmen <i class="fa fa-angle-down"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <a href="javascript:;" onclick="myData.changeSearch('Tipe Assessmen','assessmentType')">Tipe Assessmen</a>
                                            </li>
                                            <li>
                                                <a href="javascript:;" onclick="myData.changeSearch('Tipe Assessmen Test','assessmentTestType')">Tipe Assessmen Test</a>
                                            </li>                                                                                                                                    
                                        </ul>
                                    </div>
                                    <!-- /btn-group -->
                                    <input type="text" class="form-control" placeholder="Cari Data" data-name="assessmentType" name="searchData" id="searchData"> 
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
                                <th>TIPE ASSESMENT</th>
                                <th>TIPE ASSESMENT TES</th>
                                <th>TANGGAL MULAI</th>
                                <th>TANGGAL AKHIR</th>
                                <th style="padding-left:150px; padding-right:150px;">
                                    MINIMAL USIA

                                </th>
                                <th>ALASAN DIBAWAH UMUR</th>
                                <th>PEJALAN KAKI</th>
                                <th>KENDARAAN</th>   
                                <th>WEB</th>
                                <th>MOBILE RESERVASI</th>
                                <th>IFCS</th>
                                <th>B2B</th>
                                <th>POS KND</th>
                                <th>POS PNP</th>
                                <th>MPOS</th>
                                <th>VM</th>
                                <th>VERIFIKATOR</th>
                                <th>WEB CS</th>
                                <!-- <th>VAKSIN AKTIF</th> -->
                                <th>TES COVID AKTIF</th>
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
    const getMaxVaccine = parseInt(`<?= $getMaxVaccine ?>`);
    const getTestCovid = JSON.parse(`<?= $getTestCovid ?>`) ;
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

        
    });

</script>
