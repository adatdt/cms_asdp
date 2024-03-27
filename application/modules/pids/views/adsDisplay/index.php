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
                    <span class="uppercase thin hidden-xs" id="datetime"></span>
                    <script type="text/javascript">
                        window.onload = date_time('datetime');
                    </script>
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

                    <div class="row">
                        <div class="col-sm-12 form-inline">

                            <div class="input-group select2-bootstrap-prepend pad-top">
                                <div class="input-group-addon">Pelabuhan</div>
                                <?php echo form_dropdown('port', $port, '', 'id="port" class="form-control select2"'); ?>
                            </div>


                            <div class="input-group pad-top">
                                <div class="input-group-btn">
                                    <button type="button" class="btn green dropdown-toggle" data-toggle="dropdown" aria-expanded="false" id='btnData'>Nama
                                        <i class="fa fa-angle-down"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="javascript:;" onclick="myData.changeSearch('Nama','name')">Nama</a>
                                        </li>
                                        <li>
                                            <a href="javascript:;" onclick="myData.changeSearch('Kode Ads Display','adsDisplayCode')">Kode Ads Display</a>
                                        </li>
                                    </ul>
                                </div>
                                <!-- /btn-group -->
                                <input type="text" class="form-control" placeholder="Cari Data" data-name="name" name="searchData" id="searchData">
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
                    <table class="table table-striped table-bordered table-hover table-checkable order-column" id="dataTables">
                        <thead>
                            <tr>
                                <th>NO</th>
                                <th>KODE ADS</th>
                                <th>NAMA </th>
                                <th>DURASI (DETIK)</th>
                                <th>PELABUHAN</th>
                                <th>URUTAN</th>
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
<?php //include "pids/sioConnect.php" ?>
<?php include(APPPATH.'modules/pids/views/sioConnect.php'); ?>
<script type="text/javascript">
    let myData = new MyData();

    function confirmationAction2(message, url) {
        alertify.confirm(message, function (e) {
            if (e) {
                $(document).ready(function(){
                    $.ajax({
                        url: url,
                        type: 'GET',
                        dataType: 'json',

                        beforeSend: function () {
                            $.blockUI({ message: '<h4><i class="fa fa-spinner fa-spin"></i> Loading</h4>' });
                        },

                        success: function (json) {
                            // $("input[name=" + json.csrfName + "]").val(json.tokenHash);
                            // console.log(json)
                            // let csfrData = {};
                            // csfrData[json.csrfName] = json.tokenHash;
                            // $.ajaxSetup({
                            //     data: csfrData,
                            // });
                            if (json.code == 1) {
                                toastr.success(json.message, 'Sukses');
                                $('#dataTables').DataTable().ajax.reload(null, false);
                                socket.emit('pidsUpdateParams', parseInt(json.data['portId']));
                                // console.log(json.data['portId'])

                            } else {
                                toastr.error(json.message, 'Gagal');
                            }
                        },

                        error: function () {
                            toastr.error('Silahkan Hubungi Administrator', 'Gagal');
                        },

                        complete: function () {
                            $.unblockUI();
                        }
                    });

                })
            }
        });
    }

    jQuery(document).ready(function() {
        myData.init();

        $("#cari").on("click", function() {
            $(this).button('loading');
            myData.reload();
            $('#dataTables').on('draw.dt', function() {
                $("#cari").button('reset');
            });
        })



    });
</script>