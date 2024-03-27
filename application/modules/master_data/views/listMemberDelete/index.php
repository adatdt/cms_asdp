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

        <?php $now=date("Y-m-d"); $last_month=date('Y-m-d',strtotime("-1 month"))?>

        <div class="my-div-body">
            <div class="portlet box blue-madison">
                <div class="portlet-title">
                    
                    <div class="caption"><?php echo $title ?></div>
                    <div class="pull-right btn-add-padding">
                        <?= $excel ." ".$pdf ?>
                    </div>
                   
                </div>

                <div class="portlet-body">
                    <div class="table-toolbar">
                        <div class="row">
                            <div class="col-sm-12 form-inline">

                                <div class="input-group select2-bootstrap-prepend pad-top">
                                    <div class="input-group-addon">Tanggal Delete Akun</div>
                                    <input type="text" class="form-control  input-small" id="dateFrom" value="<?php echo $last_month; ?>" readonly>
                                    <div class="input-group-addon">s/d</div>
                                    <input type="text" class="form-control  input-small" id="dateTo" value="<?php echo $now; ?>" readonly>
                                </div>

                               <div class="input-group pad-top">
                                    <div class="input-group-btn">
                                        <button type="button" class="btn green dropdown-toggle" data-toggle="dropdown" aria-expanded="false" id='btnData' >Akun
                                            <i class="fa fa-angle-down"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <a href="javascript:;" onclick="myData.changeSearch('Akun','account')">Akun</a>
                                            </li>

                                            <li>
                                                <a href="javascript:;" onclick="myData.changeSearch('Nama','name')">Nama</a>
                                            </li>

                                            <li>
                                                <a href="javascript:;" onclick="myData.changeSearch('No. Telepon','telpon')">No. Telepon</a>
                                            </li>
                                        </ul>
                                    </div>
                                    <!-- /btn-group -->
                                    <input type="text" class="form-control" placeholder="Cari Data" data-name="account" name="searchData" id="searchData"> 
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

                    <table class="table table-bordered table-hover" id="dataTables">
                        <thead>
                            <tr>
                                <th>NO</th>
                                <th>AKUN</th>
                                <th>NAMA LENGKAP</th>
                                <th>NO HP</th>
                                <th>TANGGAL CREATE AKUN</th>
                                <th>TANGGAL DELETE AKUN</th>
                                <th>ALASAN DELETE AKUN</th>
                            </tr>
                        </thead>
                        <tfoot></tfoot>
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
const myData = new MyData();
jQuery(document).ready(function () {
    myData.init();

    $("#download_excel").click(function(event) {
            var dateFrom = document.getElementById('dateFrom').value;
            var dateTo = document.getElementById('dateTo').value;
            var searchName=$("#searchData").attr('data-name');
            var searchData=document.getElementById('searchData').value;

            window.location.href = "<?php echo site_url('master_data/listMemberDelete/downloadExcel?') ?>dateFrom=" + dateFrom + "&dateTo=" + dateTo + "&searchData=" + searchData + "&searchName=" + searchName;
    });

    $("#download_pdf").click(function(event) {
            var dateFrom = document.getElementById('dateFrom').value;
            var dateTo = document.getElementById('dateTo').value;
            var searchName=$("#searchData").attr('data-name');
            var searchData=document.getElementById('searchData').value;

            window.open("<?php echo site_url('master_data/listMemberDelete/downloadPdf?') ?>dateFrom=" + dateFrom + "&dateTo=" + dateTo +  "&searchData=" + searchData + "&searchName=" + searchName);
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
            // endDate: "+1m",
            startDate: new Date()
        });        


        $("#dateFrom").change(function() {            
            
            var startDate = $(this).val();
            var someDate = new Date(startDate);

            someDate.getDate();
            someDate.setMonth(someDate.getMonth()+6);
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
