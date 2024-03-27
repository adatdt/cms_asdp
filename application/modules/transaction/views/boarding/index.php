<style>
    .toast .toast-time {
        float: right;
        font-size: 12px;
        line-height: 24px;
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

        <!-- 2 hari  -->
        <?php $now = date("Y-m-d");
        $last_week = date('Y-m-d', strtotime("-0 days")) ?>
        <div class="my-div-body">
            <div class="portlet box blue-madison">
                <div class="portlet-title">

                    <div class="caption"><?php echo $title ?></div>
                    <div class="pull-right btn-add-padding">
                        <?php if ($btn_excel) { ?>
                            <button class="btn btn-sm btn-warning download" id="download_excel" disabled>Excel</button>
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

                                            <div class="input-group select2-bootstrap-prepend">
                                                <div class="input-group-addon">Tanggal Boarding</div>
                                                <input type="text" class="form-control date input-small" id="dateFrom" value="<?php echo $last_week; ?>" readonly>
                                                <div class="input-group-addon">s/d</div>
                                                <input type="text" class="form-control date input-small" id="dateTo" value="<?php echo $now; ?>" readonly>
                                            </div>

                                            <!-- validasi untuk user yang mengikat pelabuhan -->

                                            <div class="input-group select2-bootstrap-prepend">
                                                <div class="input-group-addon">Pelabuhan</div>
                                                <select id="port_origin" class="form-control js-data-example-ajax select2 input-small" dir="" name="port_origin">
                                                    <?php if ($row_port != 0) {
                                                    } else { ?>
                                                        <option value="">Pilih</option>
                                                    <?php }
                                                    foreach ($port as $key => $value) { ?>
                                                        <option value="<?php echo $this->enc->encode($value->id); ?>"><?php echo strtoupper($value->name); ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>


                                            <div class="input-group select2-bootstrap-prepend">
                                                <div class="input-group-addon">Tujuan</div>
                                                <select id="port_destination" class="form-control js-data-example-ajax select2 input-small" dir="" name="port_destination">
                                                    <option value="">Pilih</option>
                                                    <?php foreach ($port_destination as $key => $value) { ?>
                                                        <option value="<?php echo $this->enc->encode($value->id); ?>"><?php echo strtoupper($value->name); ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>

                                            <div class="input-group pad-top">
                                                <div class="input-group-btn">
                                                    <button type="button" class="btn green dropdown-toggle" data-toggle="dropdown" aria-expanded="false" id='btnData'>Kode Boarding
                                                        <i class="fa fa-angle-down"></i>
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        <li>
                                                            <a href="javascript:;" onclick="myData.changeSearch('Kode Boarding','boardingCode')">Kode Boarding</a>
                                                        </li>
                                                        <li>
                                                            <a href="javascript:;" onclick="myData.changeSearch('Nama Kapal','shipName')">Nama Kapal</a>
                                                        </li>

                                                    </ul>
                                                </div>
                                                <!-- /btn-group -->
                                                <input type="text" class="form-control" placeholder="Cari Data" data-name="boardingCode" name="searchData" id="searchData" autocomplete="off">
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

                                <table class="table table-bordered table-striped   table-hover" id="dataTables">
                                    <thead>
                                        <tr>
                                            <th>NO</th>
                                            <th>TANGGAL BOARDING</th>
                                            <th>KODE BOARDING</th>
                                            <th>TANGGAL JADWAL</th>
                                            <th>KAPAL</th>
                                            <th>PELABUHAN</th>
                                            <th>DERMAGA</th>
                                            <th>TUJUAN</th>
                                            <th>TIPE KAPAL</th>
                                            <th>JAM BERANGKAT</th>
                                            <th>AKSI</th>


                                        </tr>
                                    </thead>
                                    <tfoot></tfoot>
                                </table>
                            </div>
                        </div>
                        <!-- END EXAMPLE TABLE PORTLET-->
                    </div>
                </div>
                <!-- </div>     -->
            </div>
        </div>
    </div>
</div>
<audio id="notification">
    <source src="<?php echo base_url('assets/stc/sounds/quite-impressed-565.ogg') ?>" type="audio/ogg">
    <source src="<?php echo base_url('assets/stc/sounds/quite-impressed-565.mp3') ?>" type="audio/mpeg">
    Your browser does not support the audio element.
</audio>
<?php include "fileJs.php" ?>
<script src="<?php echo base_url(); ?>assets/stc/js/socket.io/2.4.0/socket.io.min.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/stc/js/notifyMe.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/stc/js/crypto-js.min.js" type="text/javascript"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/ClientJS/0.1.8/client.min.js" integrity="sha512-JvsHPqNjBcO5/Cy+igkp0YYkWP4k3CO3NcmjkCY0x47wSA0RMDszO/iE0fvPBhvYdoG5QnDje/qprH8eJH/pcQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<script type="text/javascript">
    var csfrData = {};
    csfrData[`<?php echo $this->security->get_csrf_token_name(); ?>`] =`<?php echo $this->security->get_csrf_hash(); ?>`;
    $.ajaxSetup({
        data: csfrData
    });    
    var myData = new MyData();
    jQuery(document).ready(function() {
        myData.init();

        $("#download_excel").click(function(event) {
            var dateFrom = $("#dateFrom").val();
            var dateTo = $("#dateTo").val();
            var port_origin = $("#port_origin").val();
            var port_destination = $("#port_destination").val();
            var searchData = $('#searchData').val();
            var searchName = $("#searchData").attr('data-name');

            window.location.href = "<?php echo site_url('transaction/boarding/download_excel?') ?>dateFrom=" + dateFrom + "&dateTo=" + dateTo + "&port_origin=" + port_origin + "&port_destination=" + port_destination + "&searchData=" + searchData + "&searchName=" + searchName;
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

        // $("#dateTo").change(function(){
        //     myData.reload();
        // });

        // $("#dateFrom").change(function(){
        //     myData.reload();
        // });

        // $("#port_origin").change(function(){
        //     myData.reload();
        // });

        // $("#port_destination").change(function(){
        //     myData.reload();
        // });

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

    });


    // SOCKET.IO

    var socket;
    var deviceName;
    
    var client = new ClientJS();
    var browser_id = client.getFingerprint().toString();

    $(function() {
        var sound = document.getElementById("notification");
        requestPermissionNotification();

        const reconnectionDelayMax = 7000;
        const dashboardKey = "<?php echo $dashboard_socket_key ?>";
        const pwd = CryptoJS.MD5(dashboardKey).toString().toUpperCase();
        deviceName = "<?php echo strtoupper($this->session->userdata('username')); ?>_" + browser_id;
        const credentials = CryptoJS.enc.Utf8.parse(`${deviceName}:${pwd}`);
        const auth = CryptoJS.enc.Base64.stringify(credentials);
        const transport = '<?php echo $socket_transport ?>';
        const socketOpt = {
            reconnectionDelayMax: reconnectionDelayMax,
            randomizationFactor: 0,
            reconnectionDelay: 1000,
            withCredentials: true,
            "transportOptions": {
                "polling": {
                    "extraHeaders": {
                        "Authorization": auth,
                    },
                },
            },
        }

        const WebsocketOpt = {
            reconnectionDelayMax: reconnectionDelayMax,
            randomizationFactor: 0,
            reconnectionDelay: 1000,
            transports: ["websocket"],
            query: {
                auth
            }
        }

        const opt = (transport === "websocket") ? WebsocketOpt : socketOpt;

        try {
            socket = io('<?php echo $socket_protocol . $socket_url ?>', opt);

            socket.on('connect', function() {
                socket.emit("client_id", deviceName);
                toastr.clear();
            });

            socket.on('error', (error) => {
                toastr.error(error, 'Computer not connected');
            });

            socket.on('connect_error', (error) => {
                toastr.error(error, 'connect error', {
                    "timeOut": 5000,
                });
            });

            socket.on('reconnecting', (attemptNumber) => {
                toastr.warning('menguhubungkan ke server <span id="countdown"></span>', 'Trying to reach server<span class="loading_dots"><i></i><i></i><i></i></span>', {
                    "preventDuplicates": true,
                    "timeOut": 0,
                    "tapToDismiss": false,
                    "onclick": false,
                    "closeOnHover": false,
                });
            });

            socket.on('disconnect', function() {
                setTimeout(function() {
                    toastr.error('Tidak ada koneksi ke server, hubungi teknisi!', 'Computer not connected', {
                        "timeOut": 3000,
                    });
                }, 5000);
            });

            var toastCloseRampdoorOpt = {
                "closeButton": true,
                "newestOnTop": true,
                "positionClass": "toast-top-center",
                "timeOut": "0"
            }

            socket.on('close_ramp_door', function(data) {
                sound.play();
                var title = 'KMP. ' + data.ship_name.toUpperCase();
                toastr.info(textInfo(data, ' oleh petugas '), title, toastCloseRampdoorOpt).css("width", "450px");

                notifyMe(title, {
                    body: `Di ${data.dock_name} ${data.message}`,
                    tag: `<a href>${title}</a>`,
                    timeout: 10
                })
            });

            // socket.on('open_boarding', function(data) {
            //     sound.play();
            //     var title = 'KMP. ' + data.ship_name.toUpperCase();
            //     toastr.success(textInfo(data, ' oleh petugas '), title, toastCloseRampdoorOpt).css("width", "450px");

            //     notifyMe(title, {
            //         body: `Di ${data.dock_name} ${data.message}`,
            //         tag: `<a href>${title}</a>`,
            //         timeout: 10
            //     })
            // });
        } catch (error) {
            toastr.error(error, 'Computer not connected');
        }
    });
</script>