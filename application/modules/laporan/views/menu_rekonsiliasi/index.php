<style type="text/css">
    .pad-top{padding-top: 5px;}
    .textboxid
    {
        height:100%;
        width:160px;
        font-size:12pt;
        padding-left: 5px;
        margin-left: 10px;
    }
    .hidden {
        display: none;
    }
    .pd-5 {
        padding: 10px;
    }
    .line-space {
        border-left: grey solid 1px;
    }
    .container-brdr {
        padding: 5px;
        margin:5px;
        border: 1px solid grey;
        border-radius: 4px;
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

        <?php $now=date("Y-m-d"); $last_week=date('Y-m-d',strtotime("-0 days")); $mingdep=date('Y-m-d',strtotime("+7 days"))?>
        <div class="my-div-body">
            <div class="portlet box blue-madison">
                <div class="portlet-title">
                    
                    <div class="caption"><?php echo $title ?></div>
                    <div class="pull-right btn-add-padding" style="padding-left: 5px"><?php echo $btn_add; ?>
                        <?php if ($btn_excel) {?>
                        <button  class="btn btn-sm btn-warning download" style="padding-left: 5px" id="download_excel" disabled>Download Excel</button>
                        <?php } ?>
                    </div>
                    <div class="pull-right btn-add-padding" id="import-excel" style="padding-left: 5px;display:none;"><?php echo $import_excel; ?></div>
                </div>
                <div class="portlet-body">
                    <div class="table-toolbar">
                        <div class="row">
                            <div class="col-sm-12 form-inline">
                                <div class="input-group select2-bootstrap-prepend pad-top">
                                    <div class="input-group-addon">Merchant</div>
                                    <select id="merchant" class="form-control js-data-example-ajax select2 input-small" dir="" name="merchant" required>
                                        <option value="">Pilih</option>
                                        <?php foreach($merchant as $key=>$value) {?>
                                            <option value="<?php echo $this->enc->encode($value->merchant_id); ?>"><?php echo strtoupper($value->merchant_name); ?></option>
                                        <?php }  ?>
                                    </select>
                                </div>

                                <div class="input-group select2-bootstrap-prepend pad-top">
                                    <div class="input-group-addon">Pembelian</div>
                                    <input type="text" name="dateFrom" id="dateFrom" class="form-control date" autocomplete="off" placeholder="YYYY-MM-DD" value="<?php echo $last_week; ?>" readonly></input>
                                    <div class="input-group-addon">s/d</div>
                                    <input type="text" name="dateTo" id="dateTo" class="form-control date" autocomplete="off" placeholder="YYYY-MM-DD" value="<?php echo $now; ?>" readonly ></input>
                                </div>

                                
                                <div class="input-group select2-bootstrap-prepend pad-top">
                                    <div class="input-group-addon">Status</div>
                                    <select id="status_type" class="form-control js-data-example-ajax select2 input-small" dir="" name="status_type">
                                        <option value="">Semua (Pilih Merchant terlebih dahulu)</option>
                                    </select>
                                </div>
                                
                                <div class="input-group select2-bootstrap-prepend pad-top">
                                    <div class="input-group-addon">Service</div>
                                    <select id="service" class="form-control js-data-example-ajax select2 input-small" dir="" name="service">
                                        <option value="">Pilih</option>
                                        <?php foreach($service as $key=>$value) {?>
                                            <option value="<?php echo $this->enc->encode($value->id); ?>"><?php echo strtoupper($value->name); ?></option>
                                        <?php }?>
                                    </select>
                                </div>
                                                
                            </div>

                        </div>
                        <div class="row">
                            <div class="col-sm-12 form-inline">
                
                                <div class="input-group select2-bootstrap-prepend pad-top">
                                    <div class="input-group-addon">Settlement</div>
                                    <input type="text" name="dateFrom2" id="dateFrom2" class="form-control date" autocomplete="off" placeholder="YYYY-MM-DD"  readonly></input>
                                    <div class="input-group-addon">s/d</div>
                                    <input type="text" name="dateTo2" id="dateTo2" class="form-control date" autocomplete="off" placeholder="YYYY-MM-DD"  readonly ></input>
                                </div>

                                <div class="input-group select2-bootstrap-prepend pad-top">
                                    <div class="input-group-addon">Keberangkatan</div>
                                    <input type="text" name="dateFrom3" id="dateFrom3" class="form-control date" autocomplete="off" placeholder="YYYY-MM-DD" readonly></input>
                                    <div class="input-group-addon">s/d</div>
                                    <input type="text" name="dateTo3" id="dateTo3" class="form-control date" autocomplete="off" placeholder="YYYY-MM-DD"   readonly ></input>
                                </div>

                                <div class="input-group pad-top">
                                    <div class="input-group-btn">
                                        <button type="button" class="btn green dropdown-toggle" data-toggle="dropdown" aria-expanded="false" id='btnData' >Kode Booking
                                            <i class="fa fa-angle-down"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <a href="javascript:;" onclick="table.changeSearch('Kode Booking','bookingCode')">Kode Booking</a>
                                            </li>

                                            <li>
                                                <a href="javascript:;" onclick="table.changeSearch('Nomor Tiket','ticketNumber')">Nomor Tiket</a>
                                            </li>

                                            <li>
                                                <a href="javascript:;" onclick="table.changeSearch('Invoice Number','transNumber')">Invoice Number</a>
                                            </li>

                                            <li>
                                                <a href="javascript:;" onclick="table.changeSearch('Ref No','refNo')">Ref No</a>
                                            </li>
                                        </ul>
                                    </div>
                                    <!-- /btn-group -->
                                    <input type="text" class="form-control" placeholder="Cari Data" data-name="bookingCode" name="searchData" id="searchData"> 
                                </div>

                                <div class="input-group pad-top">
                                    <button type="submit" class="btn btn-danger mt-ladda-btn ladda-button" data-style="zoom-in" id="cari">
                                        <span class="ladda-label">Cari</span>
                                        <span class="ladda-spinner"></span>
                                    </button>
                                </div>

                            </div>
                            
                        </div>
                        <br>
                        <!-- <div class="row">
                            <div class="col-sm-12 form-inline" id="hidden1" style="display:none">
                                <div class="input-group select2-bootstrap-prepend pad-top" >\
                                    <span style="float: left;" class="borr">Jumlah Transaksi </span><div style="float:left;">
                                    <input type="text" class="textboxid" id="jumlah" name="jumlah" disabled> </div>                                   
                                </div>
                                <div class="input-group select2-bootstrap-prepend pad-top" >
                                    <span style="float: left;" class="borr">Jumlah Dibayar </span><div style="float:left;">
                                    <input type="text" class="textboxid" id="jumlahdibayar" name="jumlahdibayar" disabled> </div>                                   
                                </div>
                                <div class="input-group select2-bootstrap-prepend pad-top" >
                                    <span style="float: left;" class="borr2" >Jumlah Belum Dibayar </span><div style="float:left;">
                                    <input type="text" class="textboxid" id="jumlahbelum" name="jumlahbelum" disabled> </div>                                   
                                </div>
                                <div class="input-group select2-bootstrap-prepend pad-top" id="inves1">
                                    <span style="float: left;" class="borr2" >Jumlah Investigasi </span><div style="float:left;">
                                    <input type="text" class="textboxid" id="jumlahinves" name="jumlahinves" disabled> </div>                                   
                                </div>
                            </div>
                        </div> -->
                        <!-- <div class="row">
                            <div class="col-sm-12 form-inline" id="hidden" style="display:none">
                                <div class="input-group select2-bootstrap-prepend pad-top">
                                    <span style="float: left;" class="borr">Total Transaksi </span><div style="float:left;">
                                    <input type="text" class="textboxid" id="total" name="total"  disabled> </div>                                    
                                </div>
                                <div class="input-group select2-bootstrap-prepend pad-top">
                                    <span style="float: left;" class="borr">Total Dibayar </span><div style="float:left;">
                                    <input type="text" class="textboxid" id="totaldibayar" name="totaldibayar"  disabled> </div>                                    
                                </div>
                                <div class="input-group select2-bootstrap-prepend pad-top">
                                    <span style="float: left;" class="borr2" >Total Belum Dibayar </span><div style="float:left;">
                                    <input type="text" class="textboxid" id="totalbelum" name="totalbelum"  disabled> </div>                                    
                                </div>
                                <div class="input-group select2-bootstrap-prepend pad-top" id="inves2">
                                    <span style="float: left;" class="borr2" >Total Investigasi </span><div style="float:left;">
                                    <input type="text" class="textboxid" id="totalinves" name="totalinves"  disabled> </div>                                    
                                </div>
                            </div>
                        </div> -->
                        <div class="row totalan" style="display:none">
                            <div class="col-sm-12 form-inline" style="display:flex;">
                                <div class="col-xs-6 trigg container-brdr">
                                    <div class="col-xs-4 rmv pd-5">
                                        <div>Jumlah Transaksi </div>
                                        <label id="jumlah1" > </label>
                                        <input type="text" class="textboxid hidden" id="jumlah" name="jumlah" disabled>
                                    </div>
                                    <div class="col-xs-4 rmv pd-5 alfa">
                                        <div>Jumlah Dibayar </div>
                                        <label id="jumlahdibayar1"> </label>
                                        <input type="text" class="textboxid hidden" id="jumlahdibayar" name="jumlahdibayar" disabled>
                                    </div>
                                    <div class="col-xs-4 rmv pd-5 alfa">
                                        <div>Jumlah Belum Dibayar </div>
                                        <label id="jumlahbelum1" > </label>
                                        <input type="text" class="textboxid hidden" id="jumlahbelum" name="jumlahbelum" disabled>
                                    </div>
                                    <div class="col-xs-4 rmv invs pd-5">
                                        <div>Jumlah Investigasi </div>
                                        <label id="jumlahinves1"> </label>
                                        <input type="text" class="textboxid hidden" id="jumlahinves" name="jumlahinves" disabled>
                                    </div>
                                </div>
                                <div class="col-xs-6 trigg container-brdr">
                                    <div class="col-xs-4 rmv pd-5">
                                        <div>Total Transaksi </div>
                                        <label id="total1" > </label>
                                        <input type="text" class="textboxid hidden" id="total" name="total"  disabled>
                                    </div>
                                    <div class="col-xs-4 rmv pd-5 alfa">
                                        <div>Total Dibayar </div>
                                        <label id="totaldibayar1"> </label>
                                        <input type="text" class="textboxid hidden" id="totaldibayar" name="totaldibayar"  disabled>
                                    </div>
                                    <div class="col-xs-4 rmv pd-5 alfa">
                                        <div>Total Belum Dibayar </div>
                                        <label id="totalbelum1" > </label>
                                        <input type="text" class="textboxid hidden" id="totalbelum" name="totalbelum"  disabled>
                                    </div>
                                    <div class="col-xs-4 rmv invs pd-5">
                                        <div>Total Investigasi </div>
                                        <label id="totalinves1"> </label>
                                        <input type="text" class="textboxid hidden" id="totalinves" name="totalinves"  disabled>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <table class="table table-bordered table-hover" id="dataTables" style="display:none;">
                        <thead>
                            <tr>
                                <th>NO</th>
                                <th>INVOICE NUMBER</th>
                                <th>REF NO</th>
                                <th>KODE BOOKING</th>
                                <th>NO TIKET</th>
                                <th>MITRA ID</th>
                                <th>WAKTU TRANSAKSI</th>
                                <th>WAKTU KEBERANGKATAN</th>
                                <th>WAKTU SETTLEMENT</th>
                                <th>ASAL</th>
                                <th>TUJUAN</th>
                                <th>LAYANAN</th>
                                <th>JENIS PENGGUNA JASA</th>
                                <th>GOLONGAN</th>
                                <th>KODE TOKO</th>
                                <th>NAMA TOKO</th>
                                <th>STATUS</th>
                                <th>TARIF PER JENIS</th>
                                <th>ADMIN FEE</th>
                                <th>DISKON</th>
                                <th>TRANSFER ASDP</th>
                                <th>KODE PROMO</th>
                                <th>UPDATE SETTLEMENT</th>                            
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

        function totalrow(){
            service = document.getElementById('service').value;
            dateFrom = document.getElementById('dateFrom').value;
            dateTo = document.getElementById('dateTo').value;
            dateFrom2 = document.getElementById('dateFrom2').value;
            dateTo2 = document.getElementById('dateTo2').value;
            dateFrom3 = document.getElementById('dateFrom3').value;
            dateTo3 = document.getElementById('dateTo3').value;
            merchant = document.getElementById('merchant').value;
            status_type = document.getElementById('status_type').value;
            searchName=$("#searchData").attr('data-name');
            searchData=document.getElementById('searchData').value;
            $.ajax({
                url: "<?php echo site_url('laporan/menu_rekonsiliasi/get_total') ?>",
                type: "POST",
                dataType : "JSON",
                data: {
                    service: service,
                    dateFrom: dateFrom,
                    dateTo: dateTo,
                    dateFrom2: dateFrom2,
                    dateTo2: dateTo2,
                    dateFrom3: dateFrom3,
                    dateTo3: dateTo3,
                    merchant: merchant,
                    status_type: status_type,
                    searchName: searchName,
                    searchData: searchData
                },
                success: function (json) {
                    data = json.data
                    $("input[name='jumlah']").val(data[0][0].jumlah_transaksi);
                    $("input[name='total']").val("Rp. "+data[0][0].total_transaksi);
                    $("input[name='jumlahdibayar']").val(data[0][1].jumlah_dibayar);
                    $("input[name='totaldibayar']").val("Rp. "+data[0][1].total_dibayar);
                    $("input[name='jumlahbelum']").val(data[0][2].jumlah_belum);
                    $("input[name='totalbelum']").val("Rp. "+data[0][2].total_belum);
                    $("input[name='jumlahinves']").val(data[0][3].jumlah_inves);
                    $("input[name='totalinves']").val("Rp. "+data[0][3].total_inves);
                    // console.log($("#jumlah").val())

                    document.getElementById("jumlah1").innerText = data[0][0].jumlah_transaksi;
                    document.getElementById("total1").innerText = "Rp. "+data[0][0].total_transaksi;
                    document.getElementById("jumlahdibayar1").innerText = data[0][1].jumlah_dibayar;
                    document.getElementById("totaldibayar1").innerText = "Rp. "+data[0][1].total_dibayar;
                    document.getElementById("jumlahbelum1").innerText = data[0][2].jumlah_belum;
                    document.getElementById("totalbelum1").innerText = "Rp. "+data[0][2].total_belum;
                    document.getElementById("jumlahinves1").innerText = data[0][3].jumlah_inves;
                    document.getElementById("totalinves1").innerText = "Rp. "+data[0][3].total_inves;
                }
            });
        }


        var table =new myData();
    $(document).ready(function () {
        // table.init();
        $("#merchant").change(function() {
            $('.download').prop('disabled',true)
            $.ajax({
                    method: "GET",
                    url: "menu_rekonsiliasi/get_status/" + $("#merchant").val(),
                    type: "html"
            })
            .done(function(msg) {
                $("#status_type").html(msg);
            });
        });

        $("#download_excel").click(function(event){
            var dateFrom=$("#dateFrom").val();
            var dateTo=$("#dateTo").val();
            var dateFrom2=$("#dateFrom2").val();
            var dateTo2=$("#dateTo2").val();
            var dateFrom3=$("#dateFrom3").val();
            var dateTo3=$("#dateTo3").val();
            var service=$("#service").val();
            var merchant=$("#merchant").val();
            var status_type=$("#status_type").val();
            var jumlah=$("#jumlah").val();
            var total=$("#total").val();
            var jumlahdibayar=$("#jumlahdibayar").val();
            var totaldibayar=$("#totaldibayar").val();
            var jumlahbelum=$("#jumlahbelum").val();
            var totalbelum=$("#totalbelum").val();
            var jumlahinves=$("#jumlahinves").val();
            var totalinves=$("#totalinves").val();
            var searchName=$("#searchData").attr('data-name');
            var searchData=document.getElementById('searchData').value;
            // console.log(jumlah)
            // console.log(search)
            window.location.href="<?php echo site_url('laporan/menu_rekonsiliasi/download_excel?') ?>dateFrom="+dateFrom+"&dateTo="+dateTo+"&dateFrom2="+dateFrom2+"&dateTo2="+dateTo2+"&dateFrom3="+dateFrom3+"&dateTo3="+dateTo3+"&service="+service+"&merchant="+merchant+"&jumlah="+jumlah+"&jumlahdibayar="+jumlahdibayar+"&jumlahbelum="+jumlahbelum+"&jumlahinves="+jumlahinves+"&status_type="+status_type+"&total="+total+"&totaldibayar="+totaldibayar+"&totalbelum="+totalbelum+"&totalinves="+totalinves+"&searchName="+searchName+"&searchData="+searchData;
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
            someDate.setMonth(someDate.getMonth()+1);
            someDate.getFullYear();
            let endDate=table.formatDate(someDate);
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

        $("#cari").on("click",function(){
            $(this).button('loading');
            merchant = document.getElementById("merchant").value;
            nama = $( "#merchant option:selected" ).text()
            if (merchant === "") {
                toastr.error("Field Merchant harus diisi!", 'Gagal');
                $("#cari").button('reset');
            }
            else {
                if ( ! $.fn.DataTable.isDataTable( '#dataTables' ) ) {
                    table.init();
                    totalrow();
                    $('#hidden1').show();
                    $('#hidden').show();
                    $('.totalan').show();
                    $("#dataTables").show();
                    if (nama === 'BRILINK'){
                        document.getElementById('import-excel').style.display = "none";
                        $('.invs').show();
                        $(".trigg .rmv").removeClass("col-xs-4");
                        $(".trigg .rmv").addClass("col-xs-6");
                    }
                    else {
                        document.getElementById('import-excel').style.display = "block";
                        $('.invs').hide();
                        $(".trigg .rmv").removeClass("col-xs-6");
                        $(".trigg .rmv").addClass("col-xs-4");
                        $(".trigg .alfa").addClass("line-space");
                    }
                }
                else {
                    table.reload();
                    totalrow();
                    $('#hidden1').show();
                    $('#hidden').show();
                    $('.totalan').show();
                    $("#dataTables").show();
                    $(".trigg .alfa").removeClass("line-space");
                    if (nama === 'BRILINK') {
                        document.getElementById('import-excel').style.display = "none";
                        $('#inves1').show();
                        $('#inves2').show();
                        $('.invs').show();
                        $(".trigg .rmv").removeClass("col-xs-4");
                        $(".trigg .rmv").addClass("col-xs-6");
                    }
                    else {
                        document.getElementById('import-excel').style.display = "block";
                        $('#inves1').hide();
                        $('#inves2').hide();
                        $('.invs').hide();
                        $(".trigg .rmv").removeClass("col-xs-6");
                        $(".trigg .rmv").addClass("col-xs-4");
                        $(".trigg .alfa").addClass("line-space");
                    }
                }
            }
            $('#dataTables').on('draw.dt', function() {
                $("#cari").button('reset');
            });
            $("#cari").button('reset');
        });

        $('.date').datepicker({
            format: 'yyyy-mm-dd',
            changeMonth: true,
            changeYear: true,
            autoclose: true,
            // endDate: new Date(),
        });
        
        setTimeout(function() {
            $('.menu-toggler').trigger('click');
        }, 1);

        // $(".menu-toggler").click(function() {
        //     $('.select2').css('width', '100%');
        // });
    });
</script>