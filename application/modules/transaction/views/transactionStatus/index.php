<style type="text/css">
    .pad-top {
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
                    <script type="text/javascript">
                        window.onload = date_time('datetime');
                    </script>
                </div>
            </div>
        </div>

        <?php $now = date("Y-m-d");
        $last_week = date('Y-m-d', strtotime("-0 days")) ?>
        <div class="my-div-body">
            <div class="portlet box blue-madison">
                <div class="portlet-title">

                    <div class="caption"><?php echo $title ?></div>
                    <div class="pull-right btn-add-padding"></div>
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
                                                <div class="input-group-addon">Tanggal Transaksi</div>
                                                <input type="text" class="form-control  input-small" id="dateFrom" value="<?php echo $last_week; ?>" readonly>
                                                <div class="input-group-addon">s/d</div>
                                                <input type="text" class="form-control  input-small" id="dateTo" value="<?php echo $now; ?>" readonly>
                                            </div>

                                           <div class="input-group pad-top">
                                                <div class="input-group-btn">
                                                    <button type="button" class="btn green dropdown-toggle" data-toggle="dropdown" aria-expanded="false" id='btnData' >Nama Tabel
                                                        <i class="fa fa-angle-down"></i>
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        <li>
                                                            <a href="javascript:;" onclick="myData.changeSearch('Nama Tabel','tblName')">Nama Tabel</a>
                                                        </li>

                                                        <li>
                                                            <a href="javascript:;" onclick="myData.changeSearch('Kode Transaksi','transactionCode')">Kode Transaksi</a>
                                                        </li>

<!--                                                         <li>
                                                            <a href="javascript:;" onclick="myData.changeSearch('Kode Status','statusCode')">Kode Status</a>
                                                        </li> --> 
                                                        <li>
                                                            <a href="javascript:;" onclick="myData.changeSearch('Dibuat Oleh','createdBy')">Dibuat Oleh</a>
                                                        </li>                                                                      
                                                    </ul>
                                                </div>
                                                <!-- /btn-group -->
                                                <input type="text" class="form-control" placeholder="Cari Data" data-name="tblName" name="searchData" id="searchData"> 
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


                                <table class="table table-bordered table-hover table-striped" id="dataTables">
                                    <thead>
                                        <tr>
                                            <th>NO</th>
                                            <th>NAMA TABEL</th>
                                            <th>KODE TRANSAKSI</th>
                                            <th>KODE STATUS</th>
                                            <th>TANGGAL TRANSAKSI</th>
                                            <th>DI BUAT OLEH</th>
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

<?php include "fileJs.php"?>
<script type="text/javascript">
    myData= new MyData();
    jQuery(document).ready(function() {
        myData.init();

        $("#download_excel").click(function(event) {
            var dateFrom = $("#dateFrom").val();
            var dateTo = $("#dateTo").val();
            var service = $("#service").val();
            var port_origin = $("#port_origin").val();
            var port_destination = $("#port_destination").val();
            var depart_date = $("#depart_date").val();
            var channel = $("#channel").val();
            // var search = $('.dataTables_filter input').val();
            var merchant = $("#merchant").val();
            var searchData = $('#searchData').val();
            var searchName = $("#searchData").attr('data-name');

            window.location.href = "<?php echo site_url('transaction/booking/download_excel?') ?>dateFrom=" + dateFrom + "&dateTo=" + dateTo + "&service=" + service + "&port_origin=" + port_origin + "&port_destination=" + port_destination + "&searchData=" + searchData + "&depart_date=" + depart_date + "&channel=" + channel + "&merchant=" + merchant+ "&searchName=" + searchName;
        });

        $('.date').datepicker({
            format: 'yyyy-mm-dd',
            changeMonth: true,
            changeYear: true,
            autoclose: true,
            todayHighlight: true,           
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
            someDate.setMonth(someDate.getMonth()+1);
            someDate.getFullYear();
            let endDate=myData.formatDate(someDate);

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
            // myData.reload();
        });


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