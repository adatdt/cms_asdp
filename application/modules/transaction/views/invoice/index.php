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
                            <button class="btn btn-sm btn-warning download" id="download_excel">Excel</button>
                        <?php } ?>

                    </div>
                </div>
                <div class="portlet-body">
                    <div class="table-toolbar">
                        <div class="row">
                            <div class="col-sm-12 form-inline">

                                <div class="input-group select2-bootstrap-prepend pad-top">
                                    <div class="input-group-addon">Tanggal Invoice</div>
                                    <input type="text" name="dateFrom" id="dateFrom" class="form-control date" autocomplete="off" placeholder="YYYY-MM-DD" value="<?php echo $last_week; ?>" readonly></input>
                                    <div class="input-group-addon">s/d</div>
                                    <input type="text" name="dateTo" id="dateTo" class="form-control date" autocomplete="off" placeholder="YYYY-MM-DD" value="<?php echo $now; ?>" readonly></input>
                                </div>

                                <div class="input-group select2-bootstrap-prepend pad-top">
                                    <div class="input-group-addon">Service</div>
                                    <select id="service" class="form-control js-data-example-ajax select2 input-small" dir="" name="service">
                                        <option value="">Pilih</option>
                                        <?php foreach ($service as $key => $value) { ?>
                                            <option value="<?php echo $this->enc->encode($value->id); ?>"><?php echo strtoupper($value->name); ?></option>
                                        <?php } ?>
                                    </select>
                                </div>

                                <div class="input-group select2-bootstrap-prepend pad-top">
                                    <div class="input-group-addon">Keberangkatan</div>
                                    <select id="port_origin" class="form-control js-data-example-ajax select2 input-small" dir="" name="port_origin">
                                        <?php if ($row_port != 0) {
                                        } else { ?>
                                            <option value="">Pilih</option>
                                        <?php } 
                                        foreach ($port as $key => $value) { ?>
                                            <option value="<?php echo $this->enc->encode($value->id); ?>" <?= $value->id==7?"selected":""; ?>><?php echo strtoupper($value->name); ?></option>
                                        <?php }  ?>
                                    </select>
                                </div>

                                <div class="input-group select2-bootstrap-prepend pad-top">
                                    <div class="input-group-addon">Tujuan</div>
                                    <select id="port_destination" class="form-control js-data-example-ajax select2 input-small" dir="" name="port_destination">
                                        <option value="">Pilih</option>
                                        <?php foreach ($destination as $key => $value) { ?>
                                            <option value="<?php echo $this->enc->encode($value->id); ?>"><?php echo strtoupper($value->name); ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="input-group select2-bootstrap-prepend pad-top">
                                    <div class="input-group-addon">Jenis Transaksi</div>
                                    <select id="transaction_type" class="form-control js-data-example-ajax select2 input-small" dir="" name="transaction_type">
                                        <option value="">Pilih</option>
                                        <?php foreach ($transaction_type as $key => $value) { ?>
                                            <option value="<?php echo $this->enc->encode($value->id); ?>"><?php echo strtoupper($value->name); ?></option>
                                        <?php } ?>
                                    </select>

                                </div>

                                <div class="input-group select2-bootstrap-prepend pad-top">
                                    <div class="input-group-addon">Status</div>
                                    <select id="status_type" class="form-control js-data-example-ajax select2 input-small" dir="" name="status_type">
                                        <option value="">Pilih</option>
                                        <?php foreach ($status_type as $key => $value) { ?>
                                            <option value="<?php echo $this->enc->encode($value->status); ?>"><?php echo strtoupper($value->description); ?></option>
                                        <?php } ?>
                                    </select>
                                </div>

                                <div class="input-group select2-bootstrap-prepend pad-top">
                                    <div class="input-group-addon">Channel</div>
                                    <?php echo form_dropdown('', $channel, '', 'id="channel" class="form-control select2"'); ?>
                                </div>

                                <div id="fMerchant" class="input-group select2-bootstrap-prepend pad-top hide">
                                    <div class="input-group-addon">Merchant</div>
                                    <select id="merchant" class="form-control select2  input-small" dir="" name="merchant">
                                    </select>
                                </div>

                                <div id="fOutletId" class="input-group select2-bootstrap-prepend pad-top hide">
                                    <div class="input-group-addon">Outlet Id</div>
                                    <select id="outletId" class="form-control select2  input-small" dir="" name="outletId">
                                    </select>
                                </div>                                

                               <div class="input-group pad-top">
                                    <div class="input-group-btn">
                                        <button type="button" class="btn green dropdown-toggle" data-toggle="dropdown" aria-expanded="false" id='btnData' >No. Invoice
                                            <i class="fa fa-angle-down"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <a href="javascript:;" onclick="myData.changeSearch('No. Invoice','transNumber')">No. Invoice</a>
                                            </li>
                                            <li>
                                                <a href="javascript:;" onclick="myData.changeSearch('Nama Pengguna Jasa','passName')">Nama Pengguna Jasa</a>
                                            </li>
                                            <li>
                                                <a href="javascript:;" onclick="myData.changeSearch('Nomer Telpon','phone')">Nomer Telpon</a>
                                            </li>
                                            <li>
                                                <a href="javascript:;" onclick="myData.changeSearch('Email','email')" >Email</a>
                                            </li>
                                            <li>
                                                <a href="javascript:;" onclick="myData.changeSearch('Nama Loket','device')" >Nama Loket</a>
                                            </li>                                            
                                        </ul>
                                    </div>
                                    <!-- /btn-group -->
                                    <input type="text" class="form-control" placeholder="Cari Data" data-name="transNumber" name="searchData" id="searchData"> 
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
                                <th>TANGGAL INVOICE</th>
                                <th>NOMER INVOICE</th>
                                <th>NAMA CUSTOMER</th>
                                <th>NOMER TELPON</th>
                                <th>EMAIL</th>
                                <th>HARGA (Rp.)</th>
                                <th>SERVIS</th>
                                <th>KEBERANGKATAN</th>
                                <th>TUJUAN</th>
                                <th>CHANNEL</th>
                                <th>NAMA LOKET</th>
                                <th>MERCHANT</th>
                                <th>OUTLET ID</th>
                                <th>JENIS TRANSAKSI</th>
                                <th>STATUS</th>
                                <th>KODE DISKON</th>
                                <th>NAMA DISKON</th>
                                <th>DUE DATE</th>
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
        myData.init();

        $("#download_excel").click(function(event) {
            var dateFrom = $("#dateFrom").val();
            var dateTo = $("#dateTo").val();
            var service = $("#service").val();
            var port_origin = $("#port_origin").val();
            var port_destination = $("#port_destination").val();
            var status_type = $("#status_type").val();
            var transaction_type = $("#transaction_type").val();
            var channel = $("#channel").val();
            var merchant = $("#merchant").val();
            var outletId = $("#outletId").val();
            // var search = $('.dataTables_filter input').val();
            var searchData = $('#searchData').val();
            var searchName = $("#searchData").attr('data-name');

            window.location.href = "<?php echo site_url('transaction/invoice/download_excel?') ?>dateFrom=" + dateFrom + "&dateTo=" + dateTo +"&outletId=" + outletId + "&service=" + service + "&port_origin=" + port_origin + "&port_destination=" + port_destination + "&channel=" + channel + "&merchant=" + merchant + "&status_type=" + status_type + "&transaction_type=" + transaction_type + "&searchData=" + searchData+ "&searchName=" + searchName;
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

        $("#channel").on("change", function() {
            var channel = $(this).val();
            $.ajax({
                url: "<?php echo site_url('transaction/booking/get_merchant') ?>",
                type: "POST",
                data: {
                    channel: channel
                },
                beforeSend: function() {
                    var valOption = $("#channel option:selected").html();
                    if (valOption.toLocaleLowerCase() == 'b2b') {
                        $("#fMerchant").removeClass("hide");
                    } else {
                        $("#fMerchant").addClass("hide");
                        $("#fOutletId").addClass("hide");
                    }
                },
                success: function(data) {
                    var d = JSON.parse(data),
                        merchant = $("#merchant"),
                        html = '';

                    if (d.length > 0) {
                        for (var r = 0; r < d.length; r++) {
                            var res = d[r];
                            html += `<option value="${res.id}">${res.name}</option>`;
                        }
                    }
                    merchant.html(html);
                }
            })
        });

        $("#cari").on("click",function(){
            $(this).button('loading');
            myData.reload();
            $('#dataTables').on('draw.dt', function() {
                $("#cari").button('reset');
            });
        });

        $("#merchant").on("change", function(){
            myData.getOutletId($(this).val())
        })


    });
</script>