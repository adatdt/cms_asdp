<style>
    .fixSearch{
        float: right;
        margin-top: 3px;
    }
    .fixTitle{
        position: fixed;
        top: 50px;
        z-index: 999;
        width: 100%;
        /* right: 0; */
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
                <div class="portlet-title" id="title">
                    <div class="caption"><?php echo $title ?></div>
                </div>
                <div class="portlet-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="portlet-body">
                                <div class="table-toolbar">
                                    <div class="row" id="wrap">
                                        <form action="javascript:void(0)" id="formSearch">
                                            <div class="col-sm-12 form-inline">
                                                <div class="input-group">
                                                    <!-- <div class="input-group-addon">Nomer Tiket Kode Booking</div> -->
                                                    <!-- <input type="text" class="form-control" id="cari" name="cari" autofocus placeholder="Nomer Tiket"> -->
                                                </div>

                                                <div class="input-group pad-top">

                                                    <div class="input-group-btn">
                                                        <button type="button" class="btn green dropdown-toggle" data-toggle="dropdown" aria-expanded="false" id='btnData' >Nomer Tiket
                                                            <i class="fa fa-angle-down"></i>
                                                        </button>
                                                        <ul class="dropdown-menu">
                                                            <li>
                                                                <a href="javascript:;" onclick="changeSearch('Nomer Tiket','ticketNumber')">Nomer Tiket</a>
                                                            </li>
                                                            <!-- ini di comment karena pencaroiaan berdasarlan kode booking belum naik -->
                                                            <!-- <li>
                                                                <a href="javascript:;" onclick="changeSearch('Kode Booking','bookingCode')">Kode Booking</a>
                                                            </li>                                             -->
                                                        </ul>
                                                    </div>

                                                    <input type="text" class="form-control" id="cari" name="cari" autofocus data-name="ticketNumber">

                                                </div>

                                                <button type="submit" class="btn btn-danger" id="btnSearch" data-loading-text="<i class='fa fa-spinner fa-spin'></i> Mencari...">Cari</button>
                                            </div>
                                        </form>
                                    </div>
								</div>
								<div>
                                    <div id="notifBox" style="border-color: transparent;" hidden>
                                        <div class="panel-heading" style="background-color:transparent;">
                                            <center><img src="<?= base_url('assets/img/exclamation.jpg'); ?>" alt="warning" width="300" height="300"></center>
                                            <!-- <center><i class="fa fa-exclamation-triangle fa-5x" style="color: gold;"></i></center> -->
                                        </div>
                                        <div>
                                            <center><span class="panel-title bold" id="notifMsg"></span></center>
                                        </div>
                                    </div>
                                    <!-- header -->
                                    <div class="panel panel-default" id="dataTicket" hidden>
                                        <div class="panel-heading">
                                            <span id="titleHeader" class="panel-title bold">DATA TIKET</span>
                                            <i id="iconHeaderCar" class="fa fa-car fa-2x" style="float:right; margin-left: 5px;" hidden></i>
                                            <i id="iconHeaderMale" class="fa fa-male fa-2x" style="float:right" hidden></i>
                                        </div>
                                        <div class="panel-body">
                                            <div class="col-md-3 form-group">
                                                <label class="bold" for="trans_date">TANGGAL TRANSAKSI</label>
                                                <span class="form-control" id="trans_date" style="border:0;padding-left:0;"></span>
                                            </div>
                                            
                                            <div class="col-md-3 form-group">
                                                <label class="bold" for="ticket_number">NOMOR TIKET</label>
                                                <span class="form-control" id="ticket_number" style="border:0;padding-left:0;"></span>
                                            </div>
                                            <div class="col-md-3 form-group">
                                                <label class="bold" for="depart_date">TANGGAL KEBERANGKATAN</label>
                                                <span class="form-control" id="depart_date" style="border:0;padding-left:0;"></span>
                                            </div>
                                            <div class="col-md-3 form-group">
                                                <label class="bold" for="checkin_expired">BATAS CHECK IN</label>
                                                <span class="form-control" id="checkin_expired" style="border:0;padding-left:0;"></span>
                                            </div>
                                            <div class="col-md-3 form-group">
                                                <label class="bold" for="trans_number">NOMOR TRANSAKSI</label>
                                                <span class="form-control" id="trans_number" style="border:0;padding-left:0;"></span>
                                            </div>
                                            <div class="col-md-3 form-group">
                                                <label class="bold" for="ticket_type">TIPE TIKET</label>
                                                <span class="form-control" id="ticket_type" style="border:0;padding-left:0;"></span>
                                            </div>
                                            <div class="col-md-3 form-group">
                                                <label class="bold" for="origin">ASAL</label>
                                                <span class="form-control" id="origin" style="border:0;padding-left:0;"></span>
                                            </div>
                                            <div class="col-md-3 form-group">
                                                <label class="bold" for="gatein_expired">BATAS GATE IN</label>
                                                <span class="form-control" id="gatein_expired" style="border:0;padding-left:0;"></span>
                                            </div>
                                            <div class="col-md-3 form-group">
                                                <label class="bold" for="booking_code">KODE BOOKING</label>
                                                <span class="form-control" id="booking_code" style="border:0;padding-left:0;"></span>
                                            </div>
                                            <div class="col-md-3 form-group">
                                                <label class="bold" for="status">STATUS</label>
                                                <span class="form-control" id="status" style="border:0;padding-left:0;"></span>
                                            </div>
                                            <div class="col-md-3 form-group">
                                                <label class="bold" for="destination">TUJUAN</label>
                                                <span class="form-control" id="destination" style="border:0;padding-left:0;"></span>
                                            </div>
                                            <div class="col-md-3 form-group">
                                                <label class="bold" for="boarding_expired">BATAS BOARDING</label>
                                                <span class="form-control" id="boarding_expired" style="border:0;padding-left:0;"></span>
                                            </div>
                                            <div class="col-md-3 form-group">
                                                <label class="bold" for="service">LAYANAN</label>
                                                <span class="form-control" id="service" style="border:0;padding-left:0;"></span>
                                            </div>
                                            <div class="col-md-3 form-group">
                                                <label class="bold" for="ship_class">KELAS KAPAL</label>
                                                <span class="form-control" id="ship_class" style="border:0;padding-left:0;"></span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="panel panel-default" id="dataBooking" hidden></div>

                                    <div style="padding: 10px" id="detailBookingVehicle" hidden></div>

                                    <div style="padding: 10px" id="detailBookingPassanger" hidden></div>

                                    <!-- tarif -->
                                    <div class="panel panel-default" id="dataFare" hidden>
                                        <div class="panel-heading">
                                            <span class="panel-title bold">DATA TARIF</span>
                                        </div>
                                        <div class="panel-body">
                                            <div class="col-md-3 form-group">
                                                <label class="bold" for="fare">TIKET TERPADU</label>
                                                <span class="form-control" id="fare" style="border:0;padding-left:0;"></span>
                                            </div>
                                            <div class="col-md-3 form-group">
                                                <label class="bold" for="entry_fee">PAS MASUK</label>
                                                <span class="form-control" id="entry_fee" style="border:0;padding-left:0;"></span>
                                            </div>
                                            <div class="col-md-3 form-group">
                                                <label class="bold" for="insurance_fee">ASURANSI JASA RAHARJA</label>
                                                <span class="form-control" id="insurance_fee" style="border:0;padding-left:0;"></span>
                                            </div>
                                            <div class="col-md-3 form-group">
                                                <label class="bold" for="ifpro_fee">IFPRO</label>
                                                <span class="form-control" id="ifpro_fee" style="border:0;padding-left:0;"></span>
                                            </div>
                                            <div class="col-md-3 form-group">
                                                <label class="bold" for="trip_fee">JASA PENYEBRANGAN KAPAL</label>
                                                <span class="form-control" id="trip_fee" style="border:0;padding-left:0;"></span>
                                            </div>
                                            <div class="col-md-3 form-group">
                                                <label class="bold" for="dock_fee">PEMELIHARAAN DERMAGA</label>
                                                <span class="form-control" id="dock_fee" style="border:0;padding-left:0;"></span>
                                            </div>
                                            <div class="col-md-3 form-group">
                                                <label class="bold" for="responsibility_fee">ASURANSI JASA RAHARJA PUTERA</label>
                                                <span class="form-control" id="responsibility_fee" style="border:0;padding-left:0;"></span>
                                            </div>
                                            <div class="col-md-3 form-group">
                                                <label class="bold" for="adm_fee">ADMINISTRASI TIKET</label>
                                                <span class="form-control" id="adm_fee" style="border:0;padding-left:0;"></span>
                                            </div>
                                            <div class="col-md-3 form-group" id="divGolongan" hidden>
                                                <label class="bold" for="golongan">GOLONGAN</label>
                                                <span class="form-control" id="golongan" style="border:0;padding-left:0;"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- tab data penumpang -->
                                    <div class="panel panel-default" id="penumpang" hidden>
                                        <div class="panel-heading">
                                            <span class="panel-title bold">DATA PENUMPANG/DRIVER</span>
                                        </div>
                                        <div class="panel-body">
                                            <div class="col-md-3 form-group">
                                                <label class="bold" for="customer">NAMA</label>
                                                <span class="form-control" id="customer" style="border:0;padding-left:0;"></span>
                                            </div>
                                            <div class="col-md-3 form-group">
                                                <label class="bold" for="type">JENIS IDENTITAS</label>
                                                <span class="form-control" id="type" style="border:0;padding-left:0;"></span>
                                            </div>
                                            <div class="col-md-3 form-group">
                                                <label class="bold" for="id_number">NOMOR IDENTITAS</label>
                                                <span class="form-control" id="id_number" style="border:0;padding-left:0;"></span>
                                            </div>
                                            <div class="col-md-3 form-group">
                                                <label class="bold" for="birth_date">TANGGAL LAHIR</label>
                                                <span class="form-control" id="birth_date" style="border:0;padding-left:0;"></span>
                                            </div>
                                            <div class="col-md-3 form-group">
                                                <label class="bold" for="gender">JENIS KELAMIN</label>
                                                <span class="form-control" id="gender" style="border:0;padding-left:0;"></span>
                                            </div>
                                            <div class="col-md-3 form-group">
                                                <label class="bold" for="city">KOTA ASAL</label>
                                                <span class="form-control" id="city" style="border:0;padding-left:0;"></span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Data Kendaraan -->
                                    <div class="panel panel-default" id="kendaraan" hidden>
                                        <div class="panel-heading">
                                            <span class="panel-title bold">DATA KENDARAAN</span>
                                        </div>
                                        <div class="panel-body">
                                            <div class="col-md-3 form-group">
                                                <label class="bold" for="plat">NOMOR KENDARAAN</label>
                                                <span class="form-control" id="plat" style="border:0;padding-left:0;"></span>
                                            </div>
                                            <div class="col-md-3 form-group">
                                                <label class="bold" for="length">PANJANG (m)</label>
                                                <span class="form-control" id="length" style="border:0;padding-left:0;"></span>
                                            </div>
                                            <div class="col-md-3 form-group">
                                                <label class="bold" for="height">TINGGI (m)</label>
                                                <span class="form-control" id="height" style="border:0;padding-left:0;"></span>
                                            </div>
                                            <div class="col-md-3 form-group">
                                                <label class="bold" for="weight">BERAT (kg)</label>
                                                <span class="form-control" id="weight" style="border:0;padding-left:0;"></span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- input service -->
                                    <input type="hidden" class="form-control" id="service" name="service">

                                    <div style="padding: 10px" id="tracker" hidden>
										<table class="table table-bordered table-striped table-hover" id="tracker_table">
											<thead>
												<tr>
													<th colspan="3" style="text-align: left;background-color: #F5F5F5;" id="riwayatTracker">RIWAYAT TIKET</th>
												</tr>
												<tr>
													<!-- <th>NO</th> -->
                                                    <th></th>
                                                    <th>TANGGAL TRANSAKSI</th>
													<th>STATUS</th>
												</tr>
											</thead>
											<tfoot></tfoot>
										</table>
                                    </div>

                                    <div style="padding: 10px" id="booking" hidden>
										<table class="table table-bordered table-striped table-hover" id="booking_table">
											<thead>
												<tr>
													<th colspan="5" style="text-align: left;background-color: #F5F5F5;">RINCIAN BOOKING</th>
												</tr>
												<tr>
                                                    <th>TANGGAL BOOKING</th>
                                                    <th>NAMA PEMESAN/PETUGAS</th>
													<th>CHANNEL</th>
                                                    <th>KODE PERANGKAT</th>
                                                    <th>LOKET</th>
												</tr>
											</thead>
											<tfoot></tfoot>
										</table>
                                    </div>

                                    <div style="padding: 10px" id="payment" hidden>
										<table class="table table-bordered table-striped table-hover" id="payment_table">
											<thead>
												<tr>
													<th colspan="5" style="text-align: left;background-color: #F5F5F5;">RINCIAN PEMBAYARAN</th>
												</tr>
												<tr>
													<th>TANGGAL PEMBAYARAN</th>
                                                    <th>NAMA PEMESAN</th>
                                                    <th>TIPE PEMBAYARAN</th>
                                                    <th>KODE PEMBAYARAN/KODE TRANSAKSI</th>
                                                    <th>NOMINAL</th>
												</tr>
											</thead>
											<tfoot></tfoot>
										</table>
                                    </div>

                                    <div style="padding: 10px" id="check_in" hidden>
										<table class="table table-bordered table-striped table-hover" id="check_in_table">
											<thead>
												<tr>
													<th colspan="5" style="text-align: left;background-color: #F5F5F5;">RINCIAN CETAK BOARDING PASS</th>
												</tr>
												<tr>
                                                    <th>TANGGAL CETAK BOARDING PASS</th>
                                                    <th>KODE PERANGKAT</th>
                                                    <th>LOKET</th>
                                                    <th>REPRINT</th>
                                                    <th>TANGGAL REPRINT</th>
												</tr>
											</thead>
											<tfoot></tfoot>
										</table>
                                    </div>


                                    <div style="padding: 10px" id="reschedule" hidden>
                                        <table class="table table-bordered table-striped table-hover" id="reschedule_table">
                                            <thead>
                                                <tr>
                                                    <th colspan="4" style="text-align: left;background-color: #F5F5F5;">RINCIAN RESCHEDULE</th>
                                                </tr>
                                                <tr>
                                                    <th>TANGGAL RESCHEDULE</th>
                                                    <th>KODE BOOKING</th>
                                                    <th>KODE BOOKING BARU</th>
                                                    <th>KODE RESCHEDULE</th>
                                                </tr>
                                            </thead>
                                            <tfoot></tfoot>
                                        </table>
                                    </div>


                                    <div style="padding: 10px" id="refund" hidden>
                                        <table class="table table-bordered table-striped table-hover" id="refund_table">
                                            <thead>
                                                <tr>
                                                    <th colspan="3" style="text-align: left;background-color: #F5F5F5;">RINCIAN REFUND</th>
                                                </tr>
                                                <tr>
                                                    <th>TANGGAL REFUND</th>
                                                    <th>KODE BOOKING</th>
                                                    <th>KODE REFUND</th>
                                                </tr>
                                            </thead>
                                            <tfoot></tfoot>
                                        </table>
                                    </div>


                                    <div style="padding: 10px" id="gate_in" hidden>
										<table class="table table-bordered table-striped table-hover" id="gate_in_table">
											<thead>
												<tr>
													<th colspan="3" style="text-align: left;background-color: #F5F5F5;">RINCIAN GATE IN</th>
												</tr>
												<tr>
                                                    <th>TANGGAL GATE IN</th>
                                                    <th>KODE PERANGKAT</th>
                                                    <th>GATE</th>
												</tr>
											</thead>
											<tfoot></tfoot>
										</table>
                                    </div>

                                    <div style="padding: 10px" id="muntah" hidden>
                                        <table class="table table-bordered table-striped table-hover" id="muntah_table">
                                            <thead>
                                                <tr>
                                                    <th colspan="17" style="text-align: left;background-color: #F5F5F5;">RINCIAN MUNTAH KAPAL</th>
                                                </tr>
                                                <tr>
                                                    <th></th>
                                                    <th colspan="8">SEBELUM</th>
                                                    <th colspan="8">SESUDAH</th>
                                                </tr>
                                                <tr>
                                                    <th>TANGGAL MUNTAH KAPAL</th>
                                                    <th>KODE BOARDING</th>
                                                    <th>TIKET BOARDING</th>
                                                    <th>KAPAL BOARDING</th>
                                                    <th>TANGGAL SHIFT</th>
                                                    <th>SHIFT</th>
                                                    <th>PETUGAS</th>
                                                    <th>NAMA KAPAL</th>
                                                    <th>PERUSAHAAN KAPAL</th>
                                                    <th>KODE BOARDING</th>
                                                    <th>TIKET BOARDING</th>
                                                    <th>KAPAL BOARDING</th>
                                                    <th>TANGGAL SHIFT</th>
                                                    <th>SHIFT</th>
                                                    <th>PETUGAS</th>
                                                    <th>NAMA KAPAL</th>
                                                    <th>PERUSAHAAN KAPAL</th>
                                                </tr>
                                            </thead>
                                            <tfoot></tfoot>
                                        </table>
                                    </div>

                                    <div style="padding: 10px" id="pindah" hidden>
                                        <table class="table table-bordered table-striped table-hover" id="pindah_table">
                                            <thead>
                                                <tr>
                                                    <th colspan="17" style="text-align: left;background-color: #F5F5F5;">RINCIAN PINDAH KAPAL</th>
                                                </tr>
                                                <tr>
                                                    <th></th>
                                                    <th colspan="8">SEBELUM</th>
                                                    <th colspan="8">SESUDAH</th>
                                                </tr>
                                                <tr>
                                                    <th>TANGGAL MUNTAH KAPAL</th>
                                                    <th>KODE BOARDING</th>
                                                    <th>TIKET BOARDING</th>
                                                    <th>KAPAL BOARDING</th>
                                                    <th>TANGGAL SHIFT</th>
                                                    <th>SHIFT</th>
                                                    <th>PETUGAS</th>
                                                    <th>NAMA KAPAL</th>
                                                    <th>PERUSAHAAN KAPAL</th>
                                                    <th>KODE BOARDING</th>
                                                    <th>TIKET BOARDING</th>
                                                    <th>KAPAL BOARDING</th>
                                                    <th>TANGGAL SHIFT</th>
                                                    <th>SHIFT</th>
                                                    <th>PETUGAS</th>
                                                    <th>NAMA KAPAL</th>
                                                    <th>PERUSAHAAN KAPAL</th>
                                                </tr>
                                            </thead>
                                            <tfoot></tfoot>
                                        </table>
                                    </div>

                                    <div style="padding: 10px" id="boarding" hidden>
                                        <table class="table table-bordered table-striped table-hover" id="boarding_table">
                                            <thead>
                                                <tr>
                                                    <th colspan="12" style="text-align: left;background-color: #F5F5F5;">RINCIAN BOARDING</th>
                                                </tr>
                                                <tr>
                                                    <th>TANGGAL JADWAL</th>
                                                    <th>TANGGAL BOARDING</th>
                                                    <th>PETUGAS</th>
                                                    <th>KODE BOARDING</th>
                                                    <th>TANGGAL SHIFT</th>
                                                    <th>SHIFT</th>
                                                    <th>DERMAGA</th>
                                                    <th>NAMA KAPAL</th>
                                                    <th>KELAS KAPAL</th>
                                                    <th>PERUSAHAN KAPAL</th>
                                                    <th>KODE PERANGKAT</th>
                                                    <th>NAMA PERANGKAT</th>
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
    </div>
</div>

<script type="text/javascript">
var initDT= false;
var dtRSC= dtR= dtP =  dtK =  dtB =  dtP =  dtC =  dtG =  dtBr = dtM = dtS = false;
var titleWidth = $("#title").width();
var tracker = {
    loadData: function() {
        $('#tracker_table').DataTable({
            "ajax": {
                "url": "<?php echo site_url('transaction/ticket_tracking/track') ?>",
                "type": "POST",
                "data": function(d) {
                    d.cari = document.getElementById('cari').value;
                    d.searchCari = $("#cari").attr('data-name');
				},
				complete: function(data){
					$("#btnSearch").button('reset');
                    // console.log(data);
                    if(data.responseJSON.recordsTotal != 0)
                    {
                        service_text = data.responseJSON.data[0].service;
                        service_id = data.responseJSON.data[0].service_id;
                        $("#tracker").show();

                        // ini buat ganti label riwayat
                        var searchCari=$("#cari").attr('data-name');
                        if (searchCari=='ticketNumber')
                        {
                            $("#riwayatTracker").html("RIWAYAT TIKET");
                        }   
                        else
                        {
                            $("#riwayatTracker").html("RIWAYAT BOOKING");
                        }  

                        if(service_text == 'passanger'){
                            get_data_ticket(service_text, service_id);
                            $("#service").val(service_text);
                            data.responseJSON.data.forEach( element => {
                                if(element.table_name == 'BOOKING'){
                                    if(dtB == false){
                                        bookingT.init();
                                        dtB = true;
                                    }else{
                                        bookingT.reload();
                                    }
                                }else if(element.table_name == 'PAYMENT'){
                                    if(dtP == false){
                                        paymentT.init();
                                        dtP = true;
                                    }else{
                                        paymentT.reload();
                                    }
                                }else if(element.table_name == 'CETAK BOARDING PASS'){
                                    if(dtC == false){
                                        checkInT.init();
                                        dtC = true;
                                    }else{
                                        checkInT.reload();
                                    }
                                }
                                else if(element.table_name == 'REFUND'){
                                    if(dtR == false){
                                        refundT.init();
                                        dtR = true;
                                    }else{
                                        refundT.reload();
                                    }
                                }
                                else if(element.table_name == 'RESCHEDULE'){
                                    if(dtRSC == false){
                                        rescheduleT.init();
                                        dtRSC= true;
                                    }else{
                                        rescheduleT.reload();
                                    }
                                }                                
                                else if(element.table_name == 'GATE IN'){
                                    if(dtG == false){
                                        gateInT.init();
                                        dtG = true;
                                    }else{
                                        gateInT.reload();
                                    }
                                }else if(element.table_name == 'BOARDING'){
                                    if(dtBr == false){
                                        boardingT.init();
                                        dtBr = true;
                                    }else{
                                        boardingT.reload();
                                    }
                                }else if(element.table_name == 'MUNTAH KAPAL'){
                                    if(dtM == false){
                                        muntahT.init();
                                        dtM = true;
                                    }else{
                                        muntahT.reload();
                                    }
                                }else if(element.table_name == 'PINDAH KAPAL'){
                                    if(dtS == false){
                                        pindahT.init();
                                        dtS = true;
                                    }else{
                                        pindahT.reload();
                                    }
                                }
                            });
                        }else if(service_text == 'vehicle'){
                            get_data_ticket(service_text, service_id);
                            $("#service").val(service_text);
                            data.responseJSON.data.forEach( element => {
                                if(element.table_name == 'BOOKING'){
                                    if(dtB == false){
                                        bookingT.init();
                                        dtB = true;
                                    }else{
                                        bookingT.reload();
                                    }
                                }else if(element.table_name == 'PAYMENT'){
                                    if(dtP == false){
                                        paymentT.init();
                                        dtP = true;
                                    }else{
                                        paymentT.reload();
                                    }
                                }else if(element.table_name == 'CETAK BOARDING PASS'){
                                    if(dtC == false){
                                        checkInT.init();
                                        dtC = true;
                                    }else{
                                        checkInT.reload();
                                    }
                                }
                                else if(element.table_name == 'REFUND'){
                                    if(dtR == false){
                                        refundT.init();
                                        dtR = true;
                                    }else{
                                        refundT.reload();
                                    }
                                }
                                else if(element.table_name == 'RESCHEDULE'){
                                    if(dtRSC == false){
                                        rescheduleT.init();
                                        dtRSC= true;
                                    }else{
                                        rescheduleT.reload();
                                    }
                                }                                
                                else if(element.table_name == 'GATE IN'){
                                    if(dtG == false){
                                        gateInT.init();
                                        dtG = true;
                                    }else{
                                        gateInT.reload();
                                    }
                                }else if(element.table_name == 'BOARDING'){
                                    if(dtBr == false){
                                        boardingT.init();
                                        dtBr = true;
                                    }else{
                                        boardingT.reload();
                                    }
                                }else if(element.table_name == 'MUNTAH KAPAL'){
                                    if(dtM == false){
                                        muntahT.init();
                                        dtM = true;
                                    }else{
                                        muntahT.reload();
                                    }
                                }else if(element.table_name == 'PINDAH KAPAL'){
                                    if(dtS == false){
                                        pindahT.init();
                                        dtS = true;
                                    }else{
                                        pindahT.reload();
                                    }
                                }
                            });
                        }
                    }
                    else
                    {
                        $("#notifMsg").html('NOMOR TIKET TIDAK DITEMUKAN');
                        $("#notifBox").show();
                        
                    }
				}
            },
            "serverSide": true,
            "processing": true,
            "searching": false,
            "paginate": false,
            "info": false,
            "columns": [
                    // {"data": "number", "orderable": false, "className": "text-center" , "width": 5},
                    {"data": "dot", "orderable": false, "className": "text-center"},
                    {"data": "created_on", "orderable": false, "className": "text-center"},
                    {"data": "table_name", "orderable": false, "className": "text-center"},
            ],
            "lengthMenu": [
                [10, 25, 50, 100],
                [10, 25, 50, 100]
            ],
            "pageLength": 10,
            "pagingType": "bootstrap_full_number",
            // "order": [[ 2, "asc" ]],
            "initComplete": function () {
                var searchInput = $('div.tracker_table_filter input');
                var data_tables = $('#tracker_table').DataTable();
                searchInput.unbind();
                searchInput.bind('keyup', function (e) {
                    if (e.keyCode == 13 || e.whiche == 13) {
                        data_tables.search(this.value).draw();
                    }
                });
            },
        });
    },

    reload: function() {
        $('#tracker_table').DataTable().ajax.reload();
    },

    init: function() {
        if (!jQuery().DataTable) {
            return;
        }

        this.loadData();
    }
};

function get_data_ticket(service) {
    var cari = $("#cari").val();
    var searchCari =  $("#cari").attr('data-name')
    $.ajax({
        url: "<?php echo site_url('transaction/ticket_tracking/data_ticket') ?>",
        method: 'POST',
        data: {cari: cari, service: service, searchCari: searchCari },
        beforeSend: function(){
            $('#dataTicket').show();
            $('#dataTicket').block({ message: '<i class="fa fa-spinner fa-pulse fa-3x fa-fw" style="color:white"></i>'});
            $('#dataFare').show();
            $('#dataFare').block({ message: '<i class="fa fa-spinner fa-pulse fa-3x fa-fw" style="color:white"></i>'});
            $('#penumpang').show();
            $('#penumpang').block({ message: '<i class="fa fa-spinner fa-pulse fa-3x fa-fw" style="color:white"></i>'});
            if(service == 'vehicle'){
                $('#kendaraan').show();
                $('#kendaraan').block({ message: '<i class="fa fa-spinner fa-pulse fa-3x fa-fw" style="color:white"></i>'});
            }
        },
    }).done(function (data) {

        var getData = JSON.parse(data);
        data = getData.data;

        if(getData.searchCari=='ticketNumber')
        { 
            $("#dataBooking").hide();
            $("#detailBookingPassanger").hide();
            $("#detailBookingVehicle").hide();


            // data tiket
            $("#trans_date").html(data.created_on);
            $("#trans_number").html(data.trans_number);
            $("#booking_code").html(data.booking_code);
            $("#ticket_number").html(data.ticket_number);
            $("#service").html(data.service);
            $("#ship_class").html(data.ship_class);
            $("#origin").html(data.origin);
            $("#destination").html(data.destination);
            $("#depart_date").html(data.depart_date);
            $("#ticket_type").html(data.ticket_type);
            $("#status").html(data.status);
            $("#checkin_expired").html(data.checkin_expired);
            $("#gatein_expired").html(data.gatein_expired);
            $("#boarding_expired").html(data.boarding_expired);

            // data tarif
            $("#golongan").html(data.golongan);
            $("#fare").html('Rp'+data.fare);
            $("#trip_fee").html('Rp'+data.trip_fee);
            $("#entry_fee").html('Rp'+data.entry_fee);
            $("#dock_fee").html('Rp'+data.dock_fee);
            $("#insurance_fee").html('Rp'+data.insurance_fee);
            $("#responsibility_fee").html('Rp'+data.responsibility_fee);
            $("#ifpro_fee").html('Rp'+data.ifpro_fee);
            $("#adm_fee").html('Rp'+data.adm_fee);

            //data penumpang
            $("#customer").html(data.customer);
            $("#type").html(data.type);
            $("#id_number").html(data.id_number);
            $("#birth_date").html(data.birth_date);
            $("#gender").html(data.gender);
            $("#city").html(data.city);

            //data kendaraan
            $("#plat").html(data.plat);
            $("#height").html(data.height);
            $("#weight").html(data.weight);
            $("#length").html(data.length);

            $("#dataTicket").unblock();
            $("#dataFare").unblock();
            $("#penumpang").unblock();

            if(service == 'vehicle'){
                $('#divGolongan').show();
                $('#kendaraan').unblock();

                if(service_id == 2){
                    $('#iconHeaderMale').hide();
                    $('#iconHeaderCar').show();
                    $('#titleHeader').html('DATA TIKET KENDARAAN');
                }
            }else{
                if(service_id == 2){
                    $('#iconHeaderMale').show();
                    $('#iconHeaderCar').show();
                    $('#titleHeader').html('DATA TIKET PENUMPANG KENDARAAN');
                }else{
                    $('#iconHeaderMale').show();
                    $('#iconHeaderCar').hide();
                    $('#titleHeader').html('DATA TIKET PEJALAN KAKI');
                }
            }
        }
        else // jika yang di pilih pencarian berdasarkan booking number
        {

            $('#dataBooking').show();
            $("#dataTicket").unblock();
            $("#dataTicket").hide();

            $("#penumpang").unblock();
            $("#penumpang").hide();

            $("#kendaraan").unblock();
            $("#kendaraan").hide();

            $("#dataFare").unblock();
            $("#dataFare").hide();



            var bookingHtml=`
                <div class="panel-heading">
                    <span id="titleHeader" class="panel-title bold">DETAIL PEMESANAN</span>
                    <i id="iconHeaderCar" class="fa fa-car fa-2x" style="float:right; margin-left: 5px;" hidden></i>
                    <i id="iconHeaderMale" class="fa fa-male fa-2x" style="float:right" hidden></i>
                </div>
                <div class="panel-body">
                    <div class="col-md-3 form-group">
                        <label class="bold" for="trans_date">TANGGAL TRANSAKSI</label>
                        <span class="form-control" id="trans_date2" style="border:0;padding-left:0;">${data.created_on}</span>
                    </div>
                    
                    <div class="col-md-3 form-group">
                        <label class="bold" for="ticket_number">KODE BOOKING</label>
                        <span class="form-control" id="booking_code2" style="border:0;padding-left:0;">${data.booking_code}</span>
                    </div>
                    <div class="col-md-3 form-group">
                        <label class="bold" for="depart_date">TANGGAL KEBERANGKATAN</label>
                        <span class="form-control" id="depart_date2" style="border:0;padding-left:0;">${data.depart_date}</span>
                    </div>

                    <div class="col-md-3 form-group">
                        <label class="bold" for="checkin_expired">BATAS CHECK IN</label>
                        <span class="form-control" id="checkin_expired2" style="border:0;padding-left:0;">${data.checkin_expired}</span>
                    </div>                                            

                    <div class="col-md-3 form-group">
                        <label class="bold" for="trans_number">NOMOR TRANSAKSI</label>
                        <span class="form-control" id="trans_number2" style="border:0;padding-left:0;">${data.trans_number}</span>
                    </div>

                    <div class="col-md-3 form-group">
                        <label class="bold" for="origin">ASAL</label>
                        <span class="form-control" id="origin2" style="border:0;padding-left:0;">${data.origin}</span>
                    </div>
                    <div class="col-md-3 form-group">
                        <label class="bold" for="destination">TUJUAN</label>
                        <span class="form-control" id="destination2" style="border:0;padding-left:0;">${data.destination}</span>
                    </div>
                    <div class="col-md-3 form-group">
                        <label class="bold" for="gatein_expired">BATAS GATE IN</label>
                        <span class="form-control" id="gatein_expired2" style="border:0;padding-left:0;">${data.gatein_expired}</span>
                    </div>
                    <div class="col-md-3 form-group">
                        <label class="bold" for="status">STATUS</label>
                        <span class="form-control" id="status2" style="border:0;padding-left:0;">${data.status}</span>
                    </div>
                    <div class="col-md-3 form-group">
                        <label class="bold" for="service">LAYANAN</label>
                        <span class="form-control" id="service2" style="border:0;padding-left:0;">${data.service}</span>
                    </div>
                    <div class="col-md-3 form-group">
                        <label class="bold" for="ship_class">KELAS KAPAL</label>
                        <span class="form-control" id="ship_class2" style="border:0;padding-left:0;">${data.ship_class}</span>
                    </div>
                    <div class="col-md-3 form-group "  >
                        <label class="bold" for="total_amount2">TOTAL HARGA</label>
                        <span class="form-control" id="total_amount2" style="border:0;padding-left:0;">Rp. ${numberWithCommas(data.fare)}</span>
                    </div>                    
                </div>

            `
            $("#dataBooking").html(bookingHtml)

            var dataPassanger=getData.dataPassanger;
            var dataVehicle=getData.dataVehicle;

            var detailBookingPassanger=`
                <table class="table table-bordered table-striped table-hover" id="bookingPassanger">
                    <thead>
                        <tr>
                            <th colspan="11" style="text-align: left;background-color: #F5F5F5;">TIKET PENUMPANG DI DALAM BOOKING</th>
                        </tr>
                        <tr>
                            <th>NAMA</th>
                            <th>KODE BOOKING</th>
                            <th>NOMER TIKET</th>
                            <th>JENIS KELAMIN</th>
                            <th>ALAMAT</th>
                            <th>NO. IDENTITAS</th>
                            <th>TIPE IDENTITAS</th>
                            <th>USIA</th>
                            <th>JENIS PENUMPANG</th>
                            <th>SERVIS</th>
                            <th>TARIF</th>
                        </tr>
                    </thead>`

                for(var i=0; i<dataPassanger.length; i++)
                {
                    detailBookingPassanger +=`
                        <tr>
                            <td>${dataPassanger[i].customer}</td>
                            <td>${dataPassanger[i].booking_code}</td>
                            <td><a target="_blank" href="<?= base_url() ?>transaction/ticket_tracking/index/${dataPassanger[i].ticket_number}">${dataPassanger[i].ticket_number}</a>
                            <td>${dataPassanger[i].gender}</td>
                            <td>${dataPassanger[i].city}</td>
                            <td>${dataPassanger[i].id_number}</td>
                            <td>${dataPassanger[i].type}</td>
                            <td>${dataPassanger[i].age}</td>
                            <td>${dataPassanger[i].golongan_pnp}</td>
                            <td>${dataPassanger[i].service}</td>
                            <td align='right' >${numberWithCommas(dataPassanger[i].fare)}</td>
                        </tr>
                        `
                }
            
            detailBookingPassanger +=`
                    <tfoot></tfoot>
                </table>
            `
            $("#detailBookingPassanger").html(detailBookingPassanger);
            $("#detailBookingPassanger").show();

            $("#detailBookingVehicle").hide(); // hide dulu nanti jika vehicle bakal ke show lagi

            // jika booking tipenya adalah kendaraan
            if(dataVehicle.length>0)
            {

                var detailBookingVehicle=`
                    <table class="table table-bordered table-striped table-hover" id="bookingPassanger">
                        <thead>
                            <tr>
                                <th colspan="9" style="text-align: left;background-color: #F5F5F5;">TIKET KENDARAAN </th>
                            </tr>
                            <tr>
                                <th>NAMA DRIVER</th>
                                <th>KODE BOOKING</th>
                                <th>NOMER TIKET</th>
                                <th>GOLONGAN</th>
                                <th>NOMER PLAT</th>
                                <th>TARIF</th>
                            </tr>
                        </thead>`

                    for(var i=0; i<dataVehicle.length; i++)
                    {
                        detailBookingVehicle +=`
                            <tr>
                                <td>${dataVehicle[i].customer}</td>
                                <td>${dataVehicle[i].booking_code}</td>
                                <td><a target="_blank" href="<?= base_url() ?>transaction/ticket_tracking/index/${dataVehicle[i].ticket_number}">${dataVehicle[i].ticket_number}</a>
                                </td>
                                <td>${dataVehicle[i].golongan}</td>
                                <td>${dataVehicle[i].plat}</td>
                                <td>${numberWithCommas(dataVehicle[i].fare)}</td>
                            </tr>
                            `
                    }
                
                detailBookingVehicle +=`
                        <tfoot></tfoot>
                    </table>
                `
                $("#detailBookingVehicle").html(detailBookingVehicle);
                $("#detailBookingVehicle").show();

            }
        }

        $("#btnSearch").button('reset');
    });
}

var bookingT = {
    loadData: function() {
        $('#booking_table').DataTable({
            "ajax": {
                "url": "<?php echo site_url('transaction/ticket_tracking/booking') ?>",
                "type": "POST",
                "data": function(d) {
                    d.cari = document.getElementById('cari').value;
                    d.service = $("#service").val();
                    d.searchCari = $("#cari").attr('data-name');
                },
                beforeSend: function(){
                    $('#booking').show();
                    // $('#booking').block({ message: '<i class="fa fa-spinner fa-pulse fa-2x fa-fw" style="color:white"></i>'});
                },
				complete: function(data){
					// $("#booking").unblock();
				}
            },
            "serverSide": true,
            "processing": true,
            "searching": false,
            "paginate": false,
            "info": false,
            "columns": [
                    {"data": "created_on", "orderable": false, "className": "text-center"},
                    {"data": "pemesan", "orderable": false, "className": "text-center"},
                    {"data": "channel", "orderable": false, "className": "text-center"},
                    {"data": "terminal_code", "orderable": false, "className": "text-center"},
                    {"data": "terminal_name", "orderable": false, "className": "text-center"},
            ],
            "lengthMenu": [
                [10, 25, 50, 100],
                [10, 25, 50, 100]
            ],
            "pageLength": 10,
            "pagingType": "bootstrap_full_number",
            // "order": [[ 1, "asc" ]],
            "initComplete": function () {
                var searchInput = $('div.booking_table_filter input');
                var data_tables = $('#booking_table').DataTable();
                searchInput.unbind();
                searchInput.bind('keyup', function (e) {
                    if (e.keyCode == 13 || e.whiche == 13) {
                        data_tables.search(this.value).draw();
                    }
                });
            },
        });
    },

    reload: function() {
        $('#booking_table').DataTable().ajax.reload();
    },

    init: function() {
        if (!jQuery().DataTable) {
            return;
        }

        this.loadData();
    }
};

var paymentT = {
    loadData: function() {
        $('#payment_table').DataTable({
            "ajax": {
                "url": "<?php echo site_url('transaction/ticket_tracking/payment') ?>",
                "type": "POST",
                "data": function(d) {
                    d.cari = document.getElementById('cari').value;
                    d.service = $("#service").val();
                    d.searchCari = $("#cari").attr('data-name');
				},
                beforeSend: function(){
                    $('#payment').show();
                },
            },
            "serverSide": true,
            "processing": true,
            "searching": false,
            "paginate": false,
            "info": false,
            "columns": [
                    {"data": "created_on", "orderable": false, "className": "text-center"},
                    {"data": "customer_name", "orderable": false, "className": "text-center"},
                    {"data": "payment_type", "orderable": false, "className": "text-center"},
                    {"data": "payment_code", "orderable": false, "className": "text-center"},
                    {"data": "amount", "orderable": false, "className": "text-center"},
            ],
            "lengthMenu": [
                [10, 25, 50, 100],
                [10, 25, 50, 100]
            ],
            "pageLength": 10,
            "pagingType": "bootstrap_full_number",
            // "order": [[ 1, "asc" ]],
            "initComplete": function () {
                var searchInput = $('div.payment_table_filter input');
                var data_tables = $('#payment_table').DataTable();
                searchInput.unbind();
                searchInput.bind('keyup', function (e) {
                    if (e.keyCode == 13 || e.whiche == 13) {
                        data_tables.search(this.value).draw();
                    }
                });
            },
        });
    },

    reload: function() {
        $('#payment_table').DataTable().ajax.reload();
    },

    init: function() {
        if (!jQuery().DataTable) {
            return;
        }

        this.loadData();
    }
};

var checkInT = {
    loadData: function() {
        $('#check_in_table').DataTable({
            "ajax": {
                "url": "<?php echo site_url('transaction/ticket_tracking/check_in') ?>",
                "type": "POST",
                "data": function(d) {
                    d.cari = document.getElementById('cari').value;
                    d.service = $("#service").val();
				},
                beforeSend: function(){
                    $('#check_in').show();
				}
            },
            "serverSide": true,
            "processing": true,
            "searching": false,
            "paginate": false,
            "info": false,
            "columns": [
                    {"data": "created_on", "orderable": false, "className": "text-center"},
                    {"data": "terminal_code", "orderable": false, "className": "text-center"},
                    {"data": "terminal_name", "orderable": false, "className": "text-center"},
                    {"data": "reprint", "orderable": false, "className": "text-center"},
                    {"data": "updated_on", "orderable": false, "className": "text-center"},
            ],
            "lengthMenu": [
                [10, 25, 50, 100],
                [10, 25, 50, 100]
            ],
            "pageLength": 10,
            "pagingType": "bootstrap_full_number",
            // "order": [[ 1, "asc" ]],
            "initComplete": function () {
                var searchInput = $('div.check_in_table_filter input');
                var data_tables = $('#check_in_table').DataTable();
                searchInput.unbind();
                searchInput.bind('keyup', function (e) {
                    if (e.keyCode == 13 || e.whiche == 13) {
                        data_tables.search(this.value).draw();
                    }
                });
            },
        });
    },

    reload: function() {
        $('#check_in_table').DataTable().ajax.reload();
    },

    init: function() {
        if (!jQuery().DataTable) {
            return;
        }

        this.loadData();
    }
};

var refundT = {
    loadData: function() {

        $('#refund_table').DataTable({
            "ajax": {
                "url": "<?php echo site_url('transaction/ticket_tracking/refund') ?>",
                "type": "POST",
                "data": function(d) {
                    d.cari = document.getElementById('cari').value;
                    d.service = $("#service").val();
                    d.searchCari = $("#cari").attr('data-name');
                },
                beforeSend: function(){
                    $('#refund').show();
                },
            },
            "serverSide": true,
            "processing": true,
            "searching": false,
            "paginate": false,
            "info": false,
            "columns": [
                    {"data": "created_on", "orderable": false, "className": "text-center"},
                    {"data": "booking_code", "orderable": false, "className": "text-center"},
                    {"data": "refund_code", "orderable": false, "className": "text-center"}
            ],
            "lengthMenu": [
                [10, 25, 50, 100],
                [10, 25, 50, 100]
            ],
            "pageLength": 10,
            "pagingType": "bootstrap_full_number",
            // "order": [[ 1, "asc" ]],
            "initComplete": function () {
                var searchInput = $('div.payment_table_filter input');
                var data_tables = $('#payment_table').DataTable();
                searchInput.unbind();
                searchInput.bind('keyup', function (e) {
                    if (e.keyCode == 13 || e.whiche == 13) {
                        data_tables.search(this.value).draw();
                    }
                });
            },
        });
    },

    reload: function() {
        $('#refund_table').DataTable().ajax.reload();
    },

    init: function() {
        if (!jQuery().DataTable) {
            return;
        }

        this.loadData();
    }
};

var rescheduleT = {
    loadData: function() {

        $('#reschedule_table').DataTable({
            "ajax": {
                "url": "<?php echo site_url('transaction/ticket_tracking/reschedule') ?>",
                "type": "POST",
                "data": function(d) {
                    d.cari = document.getElementById('cari').value;
                    d.service = $("#service").val();
                    d.searchCari = $("#cari").attr('data-name');
                },
                beforeSend: function(){
                    $('#reschedule').show();
                },
            },
            "serverSide": true,
            "processing": true,
            "searching": false,
            "paginate": false,
            "info": false,
            "columns": [
                    {"data": "created_on", "orderable": false, "className": "text-center"},
                    {"data": "booking_code", "orderable": false, "className": "text-center"},
                    {"data": "new_booking_code", "orderable": false, "className": "text-center"},
                    {"data": "reschedule_code", "orderable": false, "className": "text-center"}
            ],
            "lengthMenu": [
                [10, 25, 50, 100],
                [10, 25, 50, 100]
            ],
            "pageLength": 10,
            "pagingType": "bootstrap_full_number",
            // "order": [[ 1, "asc" ]],
            "initComplete": function () {
                var searchInput = $('div.payment_table_filter input');
                var data_tables = $('#payment_table').DataTable();
                searchInput.unbind();
                searchInput.bind('keyup', function (e) {
                    if (e.keyCode == 13 || e.whiche == 13) {
                        data_tables.search(this.value).draw();
                    }
                });
            },
        });
    },

    reload: function() {
        $('#reschedule_table').DataTable().ajax.reload();
    },

    init: function() {
        if (!jQuery().DataTable) {
            return;
        }

        this.loadData();
    }
};


var gateInT = {
    loadData: function() {
        $('#gate_in_table').DataTable({
            "ajax": {
                "url": "<?php echo site_url('transaction/ticket_tracking/gate_in') ?>",
                "type": "POST",
                "data": function(d) {
                    d.cari = document.getElementById('cari').value;
                    d.service = $("#service").val();
				},
                beforeSend: function(){
                    $('#gate_in').show();
				}
            },
            "serverSide": true,
            "processing": true,
            "searching": false,
            "paginate": false,
            "info": false,
            "columns": [
                    {"data": "created_on", "orderable": false, "className": "text-center"},
                    {"data": "terminal_code", "orderable": false, "className": "text-center"},
                    {"data": "terminal_name", "orderable": false, "className": "text-center"},
            ],
            "lengthMenu": [
                [10, 25, 50, 100],
                [10, 25, 50, 100]
            ],
            "pageLength": 10,
            "pagingType": "bootstrap_full_number",
            // "order": [[ 1, "asc" ]],
            "initComplete": function () {
                var searchInput = $('div.gate_in_table_filter input');
                var data_tables = $('#gate_in_table').DataTable();
                searchInput.unbind();
                searchInput.bind('keyup', function (e) {
                    if (e.keyCode == 13 || e.whiche == 13) {
                        data_tables.search(this.value).draw();
                    }
                });
            },
        });
    },

    reload: function() {
        $('#gate_in_table').DataTable().ajax.reload();
    },

    init: function() {
        if (!jQuery().DataTable) {
            return;
        }

        this.loadData();
    }
};

var boardingT = {
    loadData: function() {
        $('#boarding_table').DataTable({
            "ajax": {
                "url": "<?php echo site_url('transaction/ticket_tracking/boarding') ?>",
                "type": "POST",
                "data": function(d) {
                    d.cari = document.getElementById('cari').value;
                    d.service = $("#service").val();
				},
                beforeSend: function(){
                    $('#boarding').show();
				}
            },
            "serverSide": true,
            "processing": true,
            "searching": false,
            "paginate": false,
            "info": false,
            "columns": [
                    {"data": "schedule_date", "orderable": false, "className": "text-center"},
                    {"data": "created_on", "orderable": false, "className": "text-center"},
                    {"data": "petugas", "orderable": false, "className": "text-center"},
                    {"data": "boarding_code", "orderable": false, "className": "text-center"},
                    {"data": "shift_date", "orderable": false, "className": "text-center"},
                    {"data": "shift", "orderable": false, "className": "text-center"},
                    {"data": "dock", "orderable": false, "className": "text-center"},
                    {"data": "ship", "orderable": false, "className": "text-center"},
                    {"data": "ship_class", "orderable": false, "className": "text-center"},
                    {"data": "company", "orderable": false, "className": "text-center"},
                    {"data": "terminal_code", "orderable": false, "className": "text-center"},
                    {"data": "terminal_name", "orderable": false, "className": "text-center"},
            ],
            "lengthMenu": [
                [10, 25, 50, 100],
                [10, 25, 50, 100]
            ],
            "pageLength": 10,
            "pagingType": "bootstrap_full_number",
            // "order": [[ 1, "asc" ]],
            "initComplete": function () {
                var searchInput = $('div.boarding_table_filter input');
                var data_tables = $('#boarding_table').DataTable();
                searchInput.unbind();
                searchInput.bind('keyup', function (e) {
                    if (e.keyCode == 13 || e.whiche == 13) {
                        data_tables.search(this.value).draw();
                    }
                });
            },
        });
    },

    reload: function() {
        $('#boarding_table').DataTable().ajax.reload();
    },

    init: function() {
        if (!jQuery().DataTable) {
            return;
        }

        this.loadData();
    }
};

var muntahT = {
    loadData: function() {
        $('#muntah_table').DataTable({
            "ajax": {
                "url": "<?php echo site_url('transaction/ticket_tracking/muntah') ?>",
                "type": "POST",
                "data": function(d) {
                    d.cari = document.getElementById('cari').value;
                    d.service = $("#service").val();
				},
                beforeSend: function(){
                    $('#muntah').show();
				}
            },
            "serverSide": true,
            "processing": true,
            "searching": false,
            "paginate": false,
            "info": false,
            "columns": [
                    {"data": "created_on", "orderable": false, "className": "text-center"},
                    {"data": "boarding_before", "orderable": false, "className": "text-center"},
                    {"data": "ticket_boarding_before", "orderable": false, "className": "text-center"},
                    {"data": "kapal_boarding_before", "orderable": false, "className": "text-center"},
                    {"data": "shift_date_before", "orderable": false, "className": "text-center"},
                    {"data": "shift_before", "orderable": false, "className": "text-center"},
                    {"data": "petugas_before", "orderable": false, "className": "text-center"},
                    {"data": "ship_before", "orderable": false, "className": "text-center"},
                    {"data": "company_before", "orderable": false, "className": "text-center"},
                    {"data": "boarding_after", "orderable": false, "className": "text-center"},
                    {"data": "ticket_boarding_after", "orderable": false, "className": "text-center"},
                    {"data": "kapal_boarding_after", "orderable": false, "className": "text-center"},
                    {"data": "shift_date_after", "orderable": false, "className": "text-center"},
                    {"data": "shift_after", "orderable": false, "className": "text-center"},
                    {"data": "petugas_after", "orderable": false, "className": "text-center"},
                    {"data": "ship_after", "orderable": false, "className": "text-center"},
                    {"data": "company_after", "orderable": false, "className": "text-center"},
            ],
            "lengthMenu": [
                [10, 25, 50, 100],
                [10, 25, 50, 100]
            ],
            "pageLength": 10,
            "pagingType": "bootstrap_full_number",
            // "order": [[ 1, "asc" ]],
            "initComplete": function () {
                var searchInput = $('div.muntah_table_filter input');
                var data_tables = $('#muntah_table').DataTable();
                searchInput.unbind();
                searchInput.bind('keyup', function (e) {
                    if (e.keyCode == 13 || e.whiche == 13) {
                        data_tables.search(this.value).draw();
                    }
                });
            },
        });
    },

    reload: function() {
        $('#muntah_table').DataTable().ajax.reload();
    },

    init: function() {
        if (!jQuery().DataTable) {
            return;
        }

        this.loadData();
    }
};

var pindahT = {
    loadData: function() {
        $('#pindah_table').DataTable({
            "ajax": {
                "url": "<?php echo site_url('transaction/ticket_tracking/pindah') ?>",
                "type": "POST",
                "data": function(d) {
                    d.cari = document.getElementById('cari').value;
                    d.service = $("#service").val();
				},
                beforeSend: function(){
                    $('#pindah').show();
				}
            },
            "serverSide": true,
            "processing": true,
            "searching": false,
            "paginate": false,
            "info": false,
            "columns": [
                    {"data": "created_on", "orderable": false, "className": "text-center"},
                    {"data": "boarding_before", "orderable": false, "className": "text-center"},
                    {"data": "ticket_boarding_before", "orderable": false, "className": "text-center"},
                    {"data": "kapal_boarding_before", "orderable": false, "className": "text-center"},
                    {"data": "shift_date_before", "orderable": false, "className": "text-center"},
                    {"data": "shift_before", "orderable": false, "className": "text-center"},
                    {"data": "petugas_before", "orderable": false, "className": "text-center"},
                    {"data": "ship_before", "orderable": false, "className": "text-center"},
                    {"data": "company_before", "orderable": false, "className": "text-center"},
                    {"data": "boarding_after", "orderable": false, "className": "text-center"},
                    {"data": "ticket_boarding_after", "orderable": false, "className": "text-center"},
                    {"data": "kapal_boarding_after", "orderable": false, "className": "text-center"},
                    {"data": "shift_date_after", "orderable": false, "className": "text-center"},
                    {"data": "shift_after", "orderable": false, "className": "text-center"},
                    {"data": "petugas_after", "orderable": false, "className": "text-center"},
                    {"data": "ship_after", "orderable": false, "className": "text-center"},
                    {"data": "company_after", "orderable": false, "className": "text-center"},
            ],
            "lengthMenu": [
                [10, 25, 50, 100],
                [10, 25, 50, 100]
            ],
            "pageLength": 10,
            "pagingType": "bootstrap_full_number",
            // "order": [[ 1, "asc" ]],
            "initComplete": function () {
                var searchInput = $('div.pindah_table_filter input');
                var data_tables = $('#pindah_table').DataTable();
                searchInput.unbind();
                searchInput.bind('keyup', function (e) {
                    if (e.keyCode == 13 || e.whiche == 13) {
                        data_tables.search(this.value).draw();
                    }
                });
            },
        });
    },

    reload: function() {
        $('#pindah_table').DataTable().ajax.reload();
    },

    init: function() {
        if (!jQuery().DataTable) {
            return;
        }

        this.loadData();
    }
};

function loadContent() {
    $("#tracker").hide();
    $("#booking").hide();
    $("#payment").hide();
    $("#refund").hide();
    $("#reschedule").hide();
    $("#gate_in").hide();
    $("#check_in").hide();
    $("#boarding").hide();
    $("#muntah").hide();
    $("#pindah").hide();
    $("#kendaraan").hide();
    $("#penumpang").hide();
    $("#notifBox").hide();
    $('#divGolongan').hide();
    $('#dataTicket').hide();
    $('#dataFare').hide();
    $('#dataBooking').hide();
    $('#detailBookingPassanger').hide();
    var cari = $("#cari").val();
    if(cari){
        $("#btnSearch").button('loading');
        if(initDT == false){
            tracker.init();
            initDT = true;
        }else{
            tracker.reload();
        }
    }else{
        $("#btnSearch").button('reset');
        $("#notifMsg").html('NOMOR TIKET HARUS DIISI');
        $("#notifBox").show();
    }
}


    function changeSearch(x,name)
    {
        $(document).ready(function()
        {        
            $("#btnData").html(`${x} <i class="fa fa-angle-down"></i>`);
            $("#cari").attr('data-name', name);
        })

    }

    function numberWithCommas(x) {
        return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

jQuery(document).ready(function () {



    $("#cari").focus();
    $.blockUI.defaults.css = {};
    var req = '<?php echo $cari ?>';
    if(req){
        $("#cari").val(req);
        loadContent();
    }

    $("#btnSearch").on("click",function(){
        loadContent();
    });

    window.onscroll = function() {myFunction()};
    function myFunction() {
        var header = document.getElementById("formSearch");
        var form_search = header.offsetTop;
        if (window.pageYOffset > form_search) {
            $('#formSearch').addClass("fixSearch");
            $('#title').addClass("fixTitle");
            $("#title").append($("#formSearch"));
            $("#title").css("width", titleWidth);
        } else {
            $('#formSearch').removeClass("fixSearch");
            $('#title').removeClass("fixTitle");
            $("#wrap").append($("#formSearch"));
        }
    };
});
</script>