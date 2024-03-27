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
                    <div class="pull-right btn-add-padding"><?php echo $btn_add; ?>
                        <?php if ($btn_excel) { ?>
                            <button class="btn btn-sm btn-warning download" id="download_excel" disabled>Excel</button>
                        <?php } ?>

                    </div>
                </div>
                <div class="portlet-body">
                    <div class="table-toolbar">
                        <div class="row">
                            <div class="col-sm-12 form-inline">
                                <div class="input-group">
								<div class="input-group select2-bootstrap-prepend pad-top">
                                    <div class="input-group-addon">Pelabuhan</div>
                                    <select id="port_origin" class="form-control js-data-example-ajax select2 input-small" dir="" name="port_origin">
                                        <?php if ($row_port != 0) {
                                        } else { ?>
                                            <option value="">Pilih</option>
                                        <?php }
                                        foreach ($port as $key => $value) { ?>
                                            <option value="<?php echo $this->enc->encode($value->id); ?>"><?php echo strtoupper($value->name); ?></option>
                                        <?php }  ?>
                                    </select>
                                </div>
                                <p id="validasi1" style="color:red; margin:0;padding:0;">&nbsp;</p>
                                </div>

                                <div class="input-group">
                                <div class="input-group select2-bootstrap-prepend pad-top">
                                    <div class="input-group-addon">Tanggal Berangkat</div>
                                    <input type="text" name="dateFrom" id="dateFrom" class="form-control date" autocomplete="off" placeholder="YYYY-MM-DD" value="<?php echo $last_week; ?>" readonly></input>
                                    <div class="input-group-addon">s/d</div>
                                    <input type="text" name="dateTo" id="dateTo" class="form-control date" autocomplete="off" placeholder="YYYY-MM-DD" value="<?php echo $now; ?>" readonly></input>
                                </div>
                                <p id="validasi1" style="color:red; margin:0;padding:0;">&nbsp;</p>
                                </div>

                                <div class="input-group">
                                <div class="input-group select2-bootstrap-prepend pad-top">
                                    <div class="input-group-addon">Layanan</div>
                                    <select id="ship_class" class="form-control js-data-example-ajax select2 input-small" dir="" name="service">
                                        <option value="">Pilih</option>
                                        <?php foreach ($ship_class as $key => $value) { ?>
                                            <option value="<?php echo $this->enc->encode($value->id); ?>"><?php echo strtoupper($value->name); ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <p id="validasi2" style="color:red; margin:0;padding:0;">&nbsp;</p>
                                </div>

                                <div class="input-group">
                                <div class="input-group pad-top">
                                    <div class="input-group-btn">
                                        <button type="button" class="btn green dropdown-toggle" data-toggle="dropdown" aria-expanded="false" id='btnData' >Kode Booking
                                            <i class="fa fa-angle-down"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <a href="javascript:;" onclick="myData.changeSearch('Kode Booking','bookingCode')">Kode Booking</a>
                                            </li>
                                        </ul>
                                    </div>
                                    <!-- /btn-group -->
                                    <input type="text" class="form-control" placeholder="Cari Data" data-name="bookingCode" name="searchData" id="searchData"> 
                                </div>   
                                <div class="input-group pad-top">
                                    <button type="button" class="btn btn-danger mt-ladda-btn ladda-button" data-style="zoom-in" id="cari">
                                        <span class="ladda-label">Cari</span>
                                        <span class="ladda-spinner"></span>
                                    </button>
                                </div>
                                <p style="margin:0;padding:0;">&nbsp;</p>
                                </div>
                            </div>

                        </div>
                    </div>
                    <table class="table table-bordered table-hover" id="dataTables" style="display:none;">
                        <thead>
                            <tr>
                                <th>NO</th>
                                <th>KODE BOOKING</th>
                                <th>PELABUHAN</th>
                                <th>KELAS LAYANAN</th>
                                <th>TANGGAL BERANGKAT</th></th>
                                <th>JAM BERANGKAT</th>
                                <th>GOLONGAN KENDARAAN</th>
                                <th>TANGGAL RESERVASI</th>
                                <th>JAM RESERVASI</th>
                                <th>TANGGAL CHECKIN</th>
                                <th>JAM CHECKIN</th>                                
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

    myData =new MyData();
    $(document).ready(function() {
        // myData.init();

        $("#download_excel").click(function(event) {
            var x, text1;
            ori = document.getElementById("port_origin").value;
            ship = document.getElementById("ship_class").value;
            if (ori === "" && ship === "") 
            {
                text1 = "Field Wajib Dipilih";
                $("#cari").button('reset');
                document.getElementById("validasi1").innerHTML = text1;
                document.getElementById("validasi2").innerHTML = text1;
                $('.download').prop('disabled', true);
            }
            else if (ship !== "" && ori === ""){
                text1 = "Field Wajib Dipilih";
                $("#cari").button('reset');
                document.getElementById("validasi1").innerHTML = text1;
                document.getElementById("validasi2").innerHTML = "&nbsp";
                $('.download').prop('disabled', true);
            }
            else if (ship === "" && ori !== "")
            {
                text1 = "Field Wajib Dipilih";
                $("#cari").button('reset');
                document.getElementById("validasi1").innerHTML = "&nbsp;";
                document.getElementById("validasi2").innerHTML = text1;
                $('.download').prop('disabled', true);
            }
            else {
                var dateFrom = $("#dateFrom").val();
                var dateTo = $("#dateTo").val();
                var ship_class = $("#ship_class").val();
                var port_origin = $("#port_origin").val();
                var searchData = $("#searchData").val();
                var searchName=$("#searchData").attr('data-name');

                window.location.href = "<?php echo site_url('transaction/keberangkatan/download_excel?') ?>dateFrom=" + dateFrom + "&dateTo=" + dateTo + "&ship_class=" + ship_class + "&port_origin=" + port_origin + "&searchData=" + searchData + "&searchName=" + searchName;
            }
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
        });


        setTimeout(function() {
            $('.menu-toggler').trigger('click');
        }, 1);

        
        $("#cari").on("click",function(){
            $(this).button('loading');
            var x, text1;
            ori = document.getElementById("port_origin").value;
            ship = document.getElementById("ship_class").value;
            if (ori === "" && ship === "") 
            {
                text1 = "Field Wajib Dipilih";
                $("#cari").button('reset');
                document.getElementById("validasi1").innerHTML = text1;
                document.getElementById("validasi2").innerHTML = text1;
                $('.download').prop('disabled', true);
            }
            else if (ship !== "" && ori === ""){
                text1 = "Field Wajib Dipilih";
                $("#cari").button('reset');
                document.getElementById("validasi1").innerHTML = text1;
                document.getElementById("validasi2").innerHTML = "&nbsp";
                $('.download').prop('disabled', true);
            }
            else if (ship === "" && ori !== "")
            {
                text1 = "Field Wajib Dipilih";
                $("#cari").button('reset');
                document.getElementById("validasi1").innerHTML = "&nbsp;";
                document.getElementById("validasi2").innerHTML = text1;
                $('.download').prop('disabled', true);
            }
            else {
                if ( ! $.fn.DataTable.isDataTable( '#dataTables' ) ) {
							// $('#example').dataTable();
							myData.init();
                            $("#dataTables").show();
						}
						else {
							myData.reload();
                            $("#dataTables").show();
						}
                        document.getElementById("validasi1").innerHTML = "&nbsp;";
                        document.getElementById("validasi2").innerHTML = "&nbsp";
            }
            $('#dataTables').on('draw.dt', function() {
                $("#cari").button('reset');
            });
        });

    });
</script>