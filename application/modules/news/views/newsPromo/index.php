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

        <?php $now = date("Y-m-d");
        $last_week = date('Y-m-d', strtotime("-30 days")) ?>

        <div class="my-div-body">
            <div class="portlet box blue-madison">
                <div class="portlet-title">
                    <div class="caption"><?php echo $title ?></div>
                    <div class="pull-right btn-add-padding"><?php echo $btn_add; ?></div>

                </div>
                <div class="portlet-body">

                    <div class="row">
                        <div class="col-sm-12 form-inline">

                            <div class="input-group select2-bootstrap-prepend">
                                <div class="input-group-addon">Tanggal Dibuat</div>
                                <input type="text" class="form-control date input-small" id="dateFrom" value="<?php echo $last_week; ?>" readonly>
                                <div class="input-group-addon">s/d</div>
                                <input type="text" class="form-control date input-small" id="dateTo" value="<?php echo $now; ?>" readonly>
                            </div>                           

                            <div class="input-group select2-bootstrap-prepend">
                                <div class="input-group-addon">Tanggal Publikasi</div>
                                <input type="text" class="form-control date input-small" id="startPublish" placeholder="YYYY-MM-DD" readonly>
                            </div>                                                       

                            
                            <div class="input-group pad-top">
                                <button type="button" class="btn btn-danger mt-ladda-btn ladda-button" data-style="zoom-in" id="cari">
                                    <span class="ladda-label">Cari</span>
                                    <span class="ladda-spinner"></span>
                                </button>
                            </div>

                        </div>
                    </div>                    

                    <p></p>
                    <table class="table table-bordered table-hover" id="dataTables">
                        <thead>
                            <tr>
                                <th>NO</th>
                                <th>TANGGAL  DIBUAT</th>
                                <th>TIPE</th>
                                <th>
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                JUDUL BERITA
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                </th>
                                <th>THUMBNAIL</th>
                                <th>VIDEO</th>
                                <th style="padding-left:150px; padding-right:150px;">
                                LINK GAMBAR
                                </th>
                                <th>
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                SUB JUDUL BERITA
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                </th>
                                <th>PRIODE INFORMASI BERITA</th>
                                <th>ORDER</th>
                                <th>LAST EDITED USER <br>DAN TANGGAL UPDATED</th>
    							<th>STATUS</th>
                                <th>
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                AKSI
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                </th>
                            </tr>                        
                        </thead>


                        <tfoot></tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include "viewImage.php" ?>
<?php include "viewVideo.php" ?>
<?php include "fileJs.php" ?>
<script type="text/javascript">

    const myData= new Mydata();

    function showModalNew2(url) {
            if (!mfp.isOpen) {
                mfp.open({
                    items: {
                        src: url
                    },
                    modal: true,
                    type: 'ajax',
                    tLoading: '<i class="fa fa-refresh fa-spin"></i> Mohon tunggu...',
                    showCloseBtn: false,
                    callbacks: {
                        open: function () {
                            // $('.mfp-wrap').css("overflow", "initial")
                            $('.mfp-wrap').removeAttr('tabindex')
                        },
                    },
                });
            }
        }      
    $(document).ready(function () {        
        myData.init()
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
            // startDate: new Date()
        });


        $("#dateFrom").change(function() {

            var startDate = $(this).val();
            var someDate = new Date(startDate);

            someDate.getDate();
            someDate.setMonth(someDate.getMonth() + 1);
            someDate.getFullYear();
            let endDate = myData.formatDate(someDate);

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
        });

       setTimeout(function() {
            $('.menu-toggler').trigger('click');
        }, 1);

        $("#cari").on("click", function() {
            $(this).button('loading');
            myData.reload();
            $('#dataTables').on('draw.dt', function() {
                $("#cari").button('reset');
            });
        });      

        $('#startPublish').datepicker({
            format: 'yyyy-mm-dd',
            changeMonth: true,
            changeYear: true,
            autoclose: true,
            todayHighlight: true,
        });        
                      
    });
</script>
