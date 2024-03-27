<style type="text/css">
	.tabel {
		border-collapse: collapse;
	}

	.tabel th,
	.tabel td {
		padding: 5px 5px;
		border: 1px solid #000;
	}

	.tabel th {
		font-weight: normal;
	}

	.tabel-no-border tr {
		border: 1px solid #000;
	}

	.tabel-no-border th,
	.tabel-no-border td {
		padding: 5px 5px;
		border: 0px;
	}

	.tabel-no-border-new tr {
		border: 0px solid #000;
	}

	.tabel-no-border-new th,
	.tabel-no-border td {
		padding: 5px 5px;
		border: 0px;
	}

	.full-width {
		width: 100%;
	}

	.center {
		text-align: center;
	}

	.right {
		text-align: right;
	}

	.bold {
		font-weight: bold;
	}

	.italic {
		font-style: italic;
	}

	.no-border-right {
		border-right: none;
	}

	td.border-right {
		border-right: 1px solid #000
	}
</style>
<div class="page-content-wrapper">
	<div class="page-content">
		<div class="portlet box blue-hoki">
			<div class="portlet-title">
				<div class="caption">
					<?php echo $title; ?><?= $this->session->userdata('port_id'); ?>

				</div>
			</div>
			<div class="portlet-body">
				<div class="row">
					<div class="col-md-12">
						<div class="table-toolbar" style="margin-bottom: 0px">
							<div class="row">
								<div class="col-md-3" style="padding-left: 15px;padding-right: 22px;">
									<div class="form-group">
										<div class="input-group">
											<span class="input-group-addon">Tipe Pembayaran</span>
											<select id="payment_type" class="form-control select2" dir="" name="payment_type">
												<option value="all">Semua</option>
												<option value="cash">Tunai</option>
												<option value="cashless">Prepaid Cashless</option>
												<option value="online">Online</option>
												<option value="ifcs">IFCS</option>
												<option value="ifcs_redeem">IFCS Redeem</option>
												<option value="b2b">B2B</option>
											</select>
										</div>
									</div>
								</div>
								<div class="col-md-3" style="padding-left: 0px;">
									<div class="form-group">
										<div class="input-group">
											<span class="input-group-addon">Pelabuhan</span>
											<select id="port" class="form-control select2" dir="">
												<option value="">Semua</option>
												<?php foreach ($port as $key => $value) { ?>
													<option value="<?= $this->enc->encode($value->id) ?>"><?= $value->name ?></option>
												<?php } ?>
											</select>
										</div>
									</div>
								</div>
								<div class="col-md-5" style="padding-right: 5px;padding-left: 0px;">
									<div class="form-group">
										<div class="input-group">
											<span class="input-group-addon">Tanggal Shift</span>
											<input type="text" autocomplete="off" id="datefrom" class="form-control" value="<?php echo date('Y-m-d'); ?>"></input>
											<div class="input-group-addon">s/d</div>
											<input type="text" autocomplete="off" id="dateto" class="form-control" value="<?php echo date('Y-m-d'); ?>"></input>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-12">
							<div class="table-toolbar" style="margin-bottom: 0px">
								<div class="row">
									<div class="col-md-3" style="padding-left: 30px;">
										<div class="form-group">
											<div class="input-group">
												<div class="input-group-addon">Kelas Layanan</div>
												<?php
												if ($this->session->userdata('ship_class_id') != '') {
													$selected = 'disabled="disabled"';
												} else {
													$selected = '';
												}
												?>
												<select id="ship_class" <?php echo $selected ?> class="form-control select2" dir="">
													<?php echo $class ?>
												</select>
											</div>
										</div>
									</div>
									<div class="col-md-2" style="padding-left: 8px;padding-right: 8px;">
										<div class="form-group">
											<div class="input-group">
												<div class="input-group-addon">SHIFT</div>
												<select id="shift" class="form-control select2" dir="">
													<option value="">Semua</option>
													<?php foreach ($shift as $key => $value) { ?>
														<option value="<?= $this->enc->encode($value->id) ?>"><?= $value->shift_name ?></option>
													<?php } ?>
												</select>
											</div>
										</div>
									</div>
									<div class="col-md-2" style="padding-left: 10px;">
										<div class="form-group">
											<div class="input-group select2-bootstrap-prepend">
												<div class="input-group-addon">Regu </div>
												<select id="regu" class="form-control select2" dir="">
													<option value="">Semua</option>
												</select>
											</div>
										</div>
									</div>
									<div class="col-md-3" style="padding-left: 0px;">
										<div class="form-group">
											<div class="input-group select2-bootstrap-prepend">
												<div class="input-group-addon">PETUGAS </div>
												<select id="petugas" class="form-control select2" dir="">
													<option value="">Semua</option>
													<?php foreach ($petugas as $key => $value) { ?>
														<option value="<?= $this->enc->encode($value->id) ?>"><?= $value->first_name . " " . $value->last_name ?></option>
													<?php } ?>
												</select>
											</div>
										</div>
									</div>
									<div class="col-md-3" style="padding-left: 30px;">
										<div class="form-group">
											<div class="input-group select2-bootstrap-prepend">
												<div class="input-group-addon">Vending Machine </div>
												<select id="vm" class="form-control select2" dir="">
													<option value="">Semua</option>
													<?php foreach ($vm as $key => $value) { ?>
														<option value="<?= $this->enc->encode($value->terminal_code) ?>"><?= $value->terminal_name ?></option>
													<?php } ?>
												</select>
											</div>
										</div>
									</div>
									<div class="col-md-3" style="padding-left: 10px;">
										<div class="form-group">
											<div class="input-group select2-bootstrap-prepend">
												<div class="input-group-addon">Tipe Tiket </div>
												<select id="ticket_type" class="form-control select2" dir="">
													<option value="">Semua</option>
													<option value="<?= $this->enc->encode(1) ?>">Tiket Normal</option>
													<option value="<?= $this->enc->encode(2) ?>">Tiket Manual</option>
												</select>
											</div>
										</div>
									</div>
									<div id="fMerchant" class="col-md-3 hide" style="padding-left: 0px;">
										<div class="form-group">
											<div class="input-group">
												<span class="input-group-addon">Merchant</span>
												<select id="merchant" class="form-control select2" dir=""></select>
											</div>
										</div>
									</div>
									<div class="col-md-2" style="padding-left: 8px;">
										<div class="form-group">
											<div class="input-group">
												<button type="button" class="btn btn-danger" id="cari" data-loading-text="<i class='fa fa-spinner fa-spin'></i> Mencari...">Cari</button>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div id="ini_replace"></div>

				<div id="master_tabel" style="display: none">

					<div class="form-inline">

						<?= generate_button('laporan/ticket_terpadu_terjual', 'download_pdf', '<button id="printerkita" class="btn btn-sm btn-default"><i class="fa fa-file-pdf-o" style="color: #ea5460"></i> PDF</button>') ?>
						<?= generate_button('laporan/ticket_terpadu_terjual', 'download_excel', '<button id="excelkita" class="btn btn-sm btn-default"><i class="fa fa-file-excel-o" style="color: #ea5460"></i> EXCEL</button>') ?>
												

						<!-- <button id="printerkita" class="btn btn-sm btn-default"><i class="fa fa-file-pdf-o" style="color: #ea5460"></i> PDF</button> -->

						<!-- <button id="excelkita" class="btn btn-sm btn-default"><i class="fa fa-file-excel-o" style="color: #ea5460"></i> EXCEL</button><br> -->
					
					
					</div><br>

					<table class="tabel full-width" align="center">
						<tr>
							<td rowspan="4" class="no-border-right" style="width: 10%">
								<img src="<?php echo base_url(); ?>assets/img/asdp-logo2.jpg" style="width:100px; height: auto">
							</td>
							<td rowspan="4" class="center bold" style="width: 60%;font-size: 14pt; line-height: 1.5">
								LAPORAN PRODUKSI DAN PENDAPATAN<br /> TIKET TERPADU TERJUAL PER-SHIFT</span>
							</td>
							<td style="width: 10%" class="no-border-right">No Dokumen</td>
							<td style="width: 20%"> </td>
						</tr>
						<tr>
							<td class="no-border-right">Revisi</td>
							<td> </td>
						</tr>
						<tr>
							<td class="no-border-right">Berlaku Efektif</td>
							<td> </td>
						</tr>
						<tr>
							<td class="no-border-right">Halaman</td>
							<td> </td>
						</tr>
					</table>

					<br>

					<table class="table table-333 table-bordered full-width" align="center">
						<tr>
							<td style="border-right: none !important;width: 15%">CABANG</td>
							<td style="border-left: none !important;width: 35%"><span id="cabangku"></span></td>
							<td style="border-right: none !important;width: 15%">REGU</td>
							<td style="border-left: none !important;"><span id="reguku"></span></td>
						</tr>
						<tr>
							<td style="border-right: none !important;">PELABUHAN</td>
							<td style="border-left: none !important;"><span id="pelabuhanku"></span></td>
							<td style="border-right: none !important;">PETUGAS</td>
							<td style="border-left: none !important;"><span id="petugasku"></span></td>
						</tr>
						<tr>
							<td style="border-right: none !important;">LINTASAN</td>
							<td style="border-left: none !important;"><span id="lintasanku"></span> </td>
							<td style="border-right: none !important;">TANGGAL</td>
							<td style="border-left: none !important;"><span id="tanggalku"></span></td>
						</tr>
						<tr>
							<td style="border-right: none !important;">SHIFT</td>
							<td style="border-left: none !important;"><span id="shiftku"></td>
							<td style="border-right: none !important;">VENDING MACHINE</td>
							<td style="border-left: none !important;">: <span id="vmkita"></span></td>
						</tr>
						<tr>
							<td style="border-right: none !important;">STATUS</td>
							<td style="border-left: none !important;">: <b><span id="status_report"></b></span></td>
							<td style="border-right: none !important;">TIPE TIKET</td>
							<td style="border-left: none !important;">: <span id="tipe_tiket"></span></td> 
						</tr>
						<tr id="ketBottom">
							<!-- <td style="border-right: none !important;">STATUS</td>
							<td style="border-left: none !important;" colspan="3">: <b><span id="status_report"></b></span></td> -->
						</tr>
					</table>

					<table class="tabel full-width">
						<tbody id="tr"></tbody>
					</table>
				</div>
				<br>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
	jQuery(document).ready(function() {

		$("#port").change(function() {
			$.ajax({
					method: "GET",
					url: "ticket_terpadu_terjual/get_regu/" + $("#port").val(),
					type: "html"
				})
				.done(function(msg) {
					$("#regu").html(msg);
				});
		});

		$('#datefrom').datepicker({
			format: 'yyyy-mm-dd',
			changeMonth: true,
			changeYear: true,
			autoclose: true,
			endDate: new Date(),
		}).on('changeDate', function(e) {
			$('#dateto').datepicker('setStartDate', e.date)
		});

		$('#dateto').datepicker({
			format: 'yyyy-mm-dd',
			changeMonth: true,
			changeYear: true,
			autoclose: true,
			startDate: $('#datefrom').val(),
			endDate: new Date(),
		}).on('changeDate', function(e) {
			$('#datefrom').datepicker('setEndDate', e.date)
		});

		$("#printerkita").on("click", function(e) {
			payment_type = $("#payment_type").val();
			port = $("#port").val();
			datefrom = $("#datefrom").val();
			dateto = $("#dateto").val();
			regu = $("#regu").val();
			petugas = $("#petugas").val();
			shift = $("#shift").val();
			ship_class = $("#ship_class").val();
			vm = $("#vm").val();
			ticket_type = $("#ticket_type").val();
			merchant = $("#merchant").val();

			window.location = "<?php echo site_url('laporan/ticket_terpadu_terjual/get_pdf?port=') ?>" + port +
				"&datefrom=" + datefrom +
				"&dateto=" + dateto +
				"&regu=" + regu +
				"&petugas=" + petugas +
				"&shift=" + shift +
				"&ship_class=" + ship_class +
				"&vm=" + vm +
				"&cabangku=" + $('#port').find(":selected").text() +
				"&pelabuhanku=" + $('#port').find(":selected").text() +
				"&ship_classku=" + $('#ship_class').find(":selected").text() +
				"&shiftku=" + $('#shift').find(":selected").text() +
				"&reguku=" + $('#regu').find(":selected").text() +
				"&petugasku=" + $('#petugas').find(":selected").text() +
				"&payment_type=" + payment_type +
				"&vmku=" + $('#vm').find(":selected").text() +
				"&ticket_type=" + ticket_type +
				"&ticket_typeku=" + $('#ticket_type').find(":selected").text() +
				"&merchant=" + merchant +
				"&merchantKu=" + $('#merchant').find(":selected").text();
		});

		$("#excelkita").on("click", function(e) {
			payment_type = $("#payment_type").val();
			port = $("#port").val();
			datefrom = $("#datefrom").val();
			dateto = $("#dateto").val();
			regu = $("#regu").val();
			petugas = $("#petugas").val();
			shift = $("#shift").val();
			ship_class = $("#ship_class").val();
			vm = $("#vm").val();
			ticket_type = $("#ticket_type").val();
			merchant = $("#merchant").val();

			window.location = "<?php echo site_url('laporan/ticket_terpadu_terjual/get_excel?port=') ?>" + port +
				"&datefrom=" + datefrom +
				"&dateto=" + dateto +
				"&regu=" + regu +
				"&petugas=" + petugas +
				"&shift=" + shift +
				"&ship_class=" + ship_class +
				"&vm=" + vm +
				"&cabangku=" + $('#port').find(":selected").text() +
				"&pelabuhanku=" + $('#port').find(":selected").text() +
				"&ship_classku=" + $('#ship_class').find(":selected").text() +
				"&shiftku=" + $('#shift').find(":selected").text() +
				"&reguku=" + $('#regu').find(":selected").text() +
				"&petugasku=" + $('#petugas').find(":selected").text() +
				"&vmku=" + $('#vm').find(":selected").text() +
				"&payment_type=" + payment_type +
				"&ticket_type=" + ticket_type +
				"&ticket_typeku=" + $('#ticket_type').find(":selected").text() + 
				"&merchant=" + merchant +
				"&merchantKu=" + $('#merchant').find(":selected").text();
		});

		$("#cari").on("click", function(e) {
			$(this).button('loading');
			e.preventDefault();

			$.ajax({
				type: "POST",
				url: "<?php echo site_url('laporan/ticket_terpadu_terjual') ?>",
				dataType: 'json',
				data: {
					payment_type: $("#payment_type").val(),
					port: $("#port").val(),
					datefrom: $("#datefrom").val(),
					dateto: $("#dateto").val(),
					regu: $("#regu").val(),
					petugas: $("#petugas").val(),
					shift: $("#shift").val(),
					ship_class: $("#ship_class").val(),
					vm: $("#vm").val(),
					ticket_type: $("#ticket_type").val(),
					merchant: $("#merchant").val()
				},

				success: function(json) {
					$("#cari").button('reset');
					if (json.code == 200) {
						if (json.payment_type == "all") {
							html = "<table class='tabel tabel-no-border'>";
							html += "<tr><th colspan='5'>TIPE PENJUALAN : TUNAI </th><tr>";
							html += "<tr>";
							html += "<td class='text-center'> No </td>";
							html += "<td class='text-center'> Jenis </td>";
							html += "<td class='text-center'> Tarif (Rp.) </td>";
							html += "<td class='text-center'> Produksi (Lbr) </td>";
							html += "<td class='text-center'> Pendapatan (Rp.)</td>";
							html += "</tr>";
							angka = 1;
							total_produksi_cash = 0;
							total_pendapatan_cash = 0;

							$.each(json.tunai, function(i, item) {
								total_produksi_cash += Number(item.produksi);
								total_pendapatan_cash += Number(item.pendapatan);

								hargaku = "";
								produksiku = 0;
								pendapatanku = 0;

								if (item.harga != null) {
									hargaku = formatIDR(item.harga)
								}

								if (item.produksi != null) {
									produksiku = formatIDR(item.produksi)
								}

								if (item.pendapatan != null) {
									pendapatanku = formatIDR(item.pendapatan)
								}

								$("#master_tabel").show();
								html += "<tr>";
								html += "<td class='text-center'>" + angka++ + "</td>";
								html += "<td class='text-left'>" + item.golongan + "</td>";
								html += "<td class='text-right'>" + hargaku + "</td>";
								html += "<td class='text-right'>" + produksiku + "</td>";
								html += "<td class='text-right'>" + pendapatanku + "</td>";
								html += "</tr>";
							});

							html += "<tr><td colspan='3'> <b>Sub Total</b></td><td class='text-right'>" + formatIDR(total_produksi_cash) + "</td><td class='text-right'>" + formatIDR(total_pendapatan_cash) + "</td></tr>";

							html += "<table style='tabel-no-border'>";
							html += "<tr><th colspan='5'>TIPE PENJUALAN : PREPAID CASHLESS</th><tr>";
							html += "<tr>";
							html += "<td class='text-center'> No </td>";
							html += "<td class='text-center'> Jenis </td>";
							html += "<td class='text-center'> Tarif (Rp.) </td>";
							html += "<td class='text-center'> Produksi (Lbr)</td>";
							html += "<td class='text-center'> Pendapatan (Rp.)</td>";
							html += "</tr>";
							angka = 1;
							total_produksi_cashless = 0;
							total_pendapatan_cashless = 0;

							$.each(json.cashless, function(i, item) {
								total_produksi_cashless += Number(item.produksi);
								total_pendapatan_cashless += Number(item.pendapatan);

								hargaku = "";
								produksiku = 0;
								pendapatanku = 0;

								if (item.harga != null) {
									hargaku = formatIDR(item.harga)
								}

								if (item.produksi != null) {
									produksiku = formatIDR(item.produksi)
								}

								if (item.pendapatan != null) {
									pendapatanku = formatIDR(item.pendapatan)
								}

								$("#master_tabel").show();
								html += "<tr>";
								html += "<td class='text-center'>" + angka++ + "</td>";
								html += "<td class='text-left'>" + item.golongan + "</td>";
								html += "<td class='text-right'>" + hargaku + "</td>";
								html += "<td class='text-right'>" + produksiku + "</td>";
								html += "<td class='text-right'>" + pendapatanku + "</td>";
								html += "</tr>";
							});

							html += "<tr><td colspan='3'> <b>Sub Total</b></td><td class='text-right'>" + formatIDR(total_produksi_cashless) + "</td><td class='text-right'>" + formatIDR(total_pendapatan_cashless) + "</td></tr>";

							html += "<table style='tabel-no-border'>";
							html += "<tr><th colspan='5'>TIPE PENJUALAN : TIKET ONLINE</th><tr>";
							html += "<tr>";
							html += "<td class='text-center'> No </td>";
							html += "<td class='text-center'> Jenis </td>";
							html += "<td class='text-center'> Tarif (Rp.) </td>";
							html += "<td class='text-center'> Produksi (Lbr) </td>";
							html += "<td class='text-center'> Pendapatan (Rp.)</td>";
							html += "</tr>";
							angka = 1;
							total_produksi_online = 0;
							total_pendapatan_online = 0;

							$.each(json.online, function(i, item) {
								total_produksi_online += Number(item.produksi);
								total_pendapatan_online += Number(item.pendapatan);

								hargaku = "";
								produksiku = 0;
								pendapatanku = 0;

								if (item.harga != null) {
									hargaku = formatIDR(item.harga)
								}

								if (item.produksi != null) {
									produksiku = formatIDR(item.produksi)
								}

								if (item.pendapatan != null) {
									pendapatanku = formatIDR(item.pendapatan)
								}

								$("#master_tabel").show();
								html += "<tr>";
								html += "<td class='text-center'>" + angka++ + "</td>";
								html += "<td class='text-left'>" + item.golongan + "</td>";
								html += "<td class='text-right'>" + hargaku + "</td>";
								html += "<td class='text-right'>" + produksiku + "</td>";
								html += "<td class='text-right'>" + pendapatanku + "</td>";
								html += "</tr>";
							});

							html += "<tr><td colspan='3'> <b>Sub Total</b></td><td class='text-right'>" + formatIDR(total_produksi_online) + "</td><td class='text-right'>" + formatIDR(total_pendapatan_online) + "</td></tr>";

							html += "<table style='tabel-no-border'>";
							html += "<tr><th colspan='5'>TIPE PENJUALAN : IFCS</th><tr>";
							html += "<tr>";
							html += "<td class='text-center'> No </td>";
							html += "<td class='text-center'> Jenis </td>";
							html += "<td class='text-center'> Tarif (Rp.) </td>";
							html += "<td class='text-center'> Produksi (Lbr) </td>";
							html += "<td class='text-center'> Pendapatan (Rp.)</td>";
							html += "</tr>";
							angka = 1;
							total_produksi_ifcs = 0;
							total_pendapatan_ifcs = 0;

							$.each(json.ifcs, function(i, item) {
								total_produksi_ifcs += Number(item.produksi);
								total_pendapatan_ifcs += Number(item.pendapatan);

								hargaku = "";
								produksiku = 0;
								pendapatanku = 0;

								if (item.harga != null) {
									hargaku = formatIDR(item.harga)
								}

								if (item.produksi != null) {
									produksiku = formatIDR(item.produksi)
								}

								if (item.pendapatan != null) {
									pendapatanku = formatIDR(item.pendapatan)
								}

								$("#master_tabel").show();
								html += "<tr>";
								html += "<td class='text-center'>" + angka++ + "</td>";
								html += "<td class='text-left'>" + item.golongan + "</td>";
								html += "<td class='text-right'>" + hargaku + "</td>";
								html += "<td class='text-right'>" + produksiku + "</td>";
								html += "<td class='text-right'>" + pendapatanku + "</td>";
								html += "</tr>";
							});

							html += "<tr><td colspan='3'> <b>Sub Total</b></td><td class='text-right'>" + formatIDR(total_produksi_ifcs) + "</td><td class='text-right'>" + formatIDR(total_pendapatan_ifcs) + "</td></tr>";

							html += "<table style='tabel-no-border'>";
							html += "<tr><th colspan='5'>TIPE PENJUALAN : IFCS REDEEM</th><tr>";
							html += "<tr>";
							html += "<td class='text-center'> No </td>";
							html += "<td class='text-center'> Jenis </td>";
							html += "<td class='text-center'> Tarif (Rp.) </td>";
							html += "<td class='text-center'> Produksi (Lbr) </td>";
							html += "<td class='text-center'> Pendapatan (Rp.)</td>";
							html += "</tr>";
							angka = 1;
							total_produksi_ifcs_redeem = 0;
							total_pendapatan_ifcs_redeem = 0;

							$.each(json.ifcs_redeem, function(i, item) {
								total_produksi_ifcs_redeem += Number(item.produksi);
								total_pendapatan_ifcs_redeem += Number(item.pendapatan);

								hargaku = "";
								produksiku = 0;
								pendapatanku = 0;

								if (item.harga != null) {
									hargaku = formatIDR(item.harga)
								}

								if (item.produksi != null) {
									produksiku = formatIDR(item.produksi)
								}

								if (item.pendapatan != null) {
									pendapatanku = formatIDR(item.pendapatan)
								}

								$("#master_tabel").show();
								html += "<tr>";
								html += "<td class='text-center'>" + angka++ + "</td>";
								html += "<td class='text-left'>" + item.golongan + "</td>";
								html += "<td class='text-right'>" + hargaku + "</td>";
								html += "<td class='text-right'>" + produksiku + "</td>";
								html += "<td class='text-right'>" + pendapatanku + "</td>";
								html += "</tr>";
							});



							html += "<tr><td colspan='3'> <b>Sub Total</b></td><td class='text-right'>" + formatIDR(total_produksi_ifcs_redeem) + "</td><td class='text-right'>" + formatIDR(total_pendapatan_ifcs_redeem) + "</td></tr>";

							html += "<table style='tabel-no-border'>";
							html += "<tr><th colspan='5'>TIPE PENJUALAN : B2B</th><tr>";
							html += "<tr>";
							html += "<td class='text-center'> No </td>";
							html += "<td class='text-center'> Jenis </td>";
							html += "<td class='text-center'> Tarif (Rp.) </td>";
							html += "<td class='text-center'> Produksi (Lbr) </td>";
							html += "<td class='text-center'> Pendapatan (Rp.)</td>";
							html += "</tr>";
							angka = 1;
							total_produksi_b2b = 0;
							total_pendapatan_b2b = 0;

							$.each(json.b2b, function(i, item) {
								total_produksi_b2b += Number(item.produksi);
								total_pendapatan_b2b += Number(item.pendapatan);

								hargaku = "";
								produksiku = 0;
								pendapatanku = 0;

								if (item.harga != null) {
									hargaku = formatIDR(item.harga)
								}

								if (item.produksi != null) {
									produksiku = formatIDR(item.produksi)
								}

								if (item.pendapatan != null) {
									pendapatanku = formatIDR(item.pendapatan)
								}

								$("#master_tabel").show();
								html += "<tr>";
								html += "<td class='text-center'>" + angka++ + "</td>";
								html += "<td class='text-left'>" + item.golongan + "</td>";
								html += "<td class='text-right'>" + hargaku + "</td>";
								html += "<td class='text-right'>" + produksiku + "</td>";
								html += "<td class='text-right'>" + pendapatanku + "</td>";
								html += "</tr>";
							});

							jumlah_produksi = Number(total_produksi_cash + total_produksi_cashless + total_produksi_online + total_produksi_ifcs + total_produksi_ifcs_redeem + total_produksi_b2b);
							jumlah_pendapatan = Number(total_pendapatan_cash + total_pendapatan_cashless + total_pendapatan_online + total_pendapatan_ifcs + total_pendapatan_ifcs_redeem + total_pendapatan_b2b);

							html += "<tr><td colspan='3'> <b>Sub Total</b></td><td class='text-right'>" + formatIDR(total_produksi_b2b) + "</td><td class='text-right'>" + formatIDR(total_pendapatan_b2b) + "</td></tr>";

							html += "<tr><td colspan='3'> <b>TOTAL JUMLAH (Tunai + Cashless + Online + IFCS + IFCS Redeem + B2B)</b></td><td class='text-right'>" + formatIDR(jumlah_produksi) + "</td><td class='text-right'>" + formatIDR(jumlah_pendapatan) + "</td></tr>";

							html += "</table>";
							$("#tr").html(html);
							var cabangku = ": " + $('#port').find(":selected").text();
							var shiftku = ": " + $('#shift').find(":selected").text();
							var reguku = ": " + $('#regu').find(":selected").text();
							var petugasku = ": " + $('#petugas').find(":selected").text();
							var tanggalku = ": " + $.datepicker.formatDate("d M yy", new Date($('#datefrom').val())) + " - " + $.datepicker.formatDate("d M yy", new Date($('#dateto').val()));

							var kelaskuu = $('#ship_class').find(":selected").text();
							var vmku = $('#vm').find(":selected").text();
							var ticketType = $("#ticket_type").find(":selected").text();

							var lintasanku = json.lintasan;
							var destination = json.lintasan.destination;
							var status_report = json.status_approve;

							$("#cabangku").html(cabangku);
							$("#pelabuhanku").html(cabangku);
							$("#shiftku").html(shiftku);
							$("#reguku").html(reguku);
							$("#petugasku").html(petugasku);
							$("#tanggalku").html(tanggalku);
							$("#vmkita").html(vmku);
							$('#status_report').html(status_report.toUpperCase());
							$('#tipe_tiket').html(ticketType);

							// $("#status_report").html(status_report.toUpperCase());
							var inMerchant = $("#merchant").find(":selected").text();
							var typePayment = $("#payment_type").find(":selected").text();
							var remCol, ketMer;

							if (typePayment.toUpperCase() == "B2B") {
								remCol = ``;
								ketMer = `<td style="border-right: none !important;">MERCHANT</td>
										<td style="border-left: none !important;" colspan="3">: ${inMerchant.toUpperCase()}</td>`;
							} else {
								ketMer = ``;
							}

							var hkb = `${ketMer}`;
							$("#ketBottom").html(hkb);
							$("#kelaskuu").html(kelaskuu.toUpperCase());
							$("#lintasanku").html(": " + lintasanku.toUpperCase());
						} else if (json.payment_type == "cash") {
							html = "<table class='tabel tabel-no-border'>";
							html += "<tr><th colspan='5'>TIPE PENJUALAN : TUNAI</th><tr>";
							html += "<tr>";
							html += "<td class='text-center'> No </td>";
							html += "<td class='text-center'> Jenis </td>";
							html += "<td class='text-center'> Tarif (Rp.) </td>";
							html += "<td class='text-center'> Produksi (Lbr) </td>";
							html += "<td class='text-center'> Pendapatan (Rp.)</td>";
							html += "</tr>";
							angka = 1;
							total_produksi_cash = 0;
							total_pendapatan_cash = 0;

							$.each(json.tunai, function(i, item) {
								total_produksi_cash += Number(item.produksi);
								total_pendapatan_cash += Number(item.pendapatan);

								hargaku = "";
								produksiku = 0;
								pendapatanku = 0;

								if (item.harga != null) {
									hargaku = formatIDR(item.harga)
								}

								if (item.produksi != null) {
									produksiku = formatIDR(item.produksi)
								}

								if (item.pendapatan != null) {
									pendapatanku = formatIDR(item.pendapatan)
								}

								$("#master_tabel").show();
								html += "<tr>";
								html += "<td class='text-center'>" + angka++ + "</td>";
								html += "<td class='text-left'>" + item.golongan + "</td>";
								html += "<td class='text-right'>" + hargaku + "</td>";
								html += "<td class='text-right'>" + produksiku + "</td>";
								html += "<td class='text-right'>" + pendapatanku + "</td>";
								html += "</tr>";
							});

							html += "<tr><td colspan='3'> <b>Sub Total</b></td><td class='text-right'>" + formatIDR(total_produksi_cash) + "</td><td class='text-right'>" + formatIDR(total_pendapatan_cash) + "</td></tr>";

							jumlah_produksi = Number(total_produksi_cash);
							jumlah_pendapatan = Number(total_pendapatan_cash);

							html += "<tr><td colspan='3'> <b>TOTAL JUMLAH ( Tunai (Normal) )</b></td><td class='text-right'>" + formatIDR(jumlah_produksi) + "</td><td class='text-right'>" + formatIDR(jumlah_pendapatan) + "</td></tr>";

							html += "</table>";
							$("#tr").html(html);
							var cabangku = ": " + $('#port').find(":selected").text();
							var shiftku = ": " + $('#shift').find(":selected").text();
							var reguku = ": " + $('#regu').find(":selected").text();
							var petugasku = ": " + $('#petugas').find(":selected").text();
							var tanggalku = ": " + $.datepicker.formatDate("d M yy", new Date($('#datefrom').val())) + " - " + $.datepicker.formatDate("d M yy", new Date($('#dateto').val()));

							var kelaskuu = $('#ship_class').find(":selected").text();
							var vmku = $('#vm').find(":selected").text();
							var ticketType = $("#ticket_type").find(":selected").text();

							var lintasanku = json.lintasan;
							var destination = json.lintasan.destination;
							var status_report = json.status_approve;

							$("#cabangku").html(cabangku);
							$("#pelabuhanku").html(cabangku);
							$("#shiftku").html(shiftku);
							$("#reguku").html(reguku);
							$("#petugasku").html(petugasku);
							$("#tanggalku").html(tanggalku);
							$("#vmkita").html(vmku);
							$('#status_report').html(status_report.toUpperCase());
							$('#tipe_tiket').html(ticketType);

							// $("#status_report").html(status_report.toUpperCase());
							var inMerchant = $("#merchant").find(":selected").text();
							var typePayment = $("#payment_type").find(":selected").text();
							var remCol, ketMer;

							if (typePayment.toUpperCase() == "B2B") {
								remCol = ``;
								ketMer = `<td style="border-right: none !important;">MERCHANT</td>
										<td style="border-left: none !important;" colspan="3">: ${inMerchant.toUpperCase()}</td>`;
							} else {
								ketMer = ``;
							}

							var hkb = `${ketMer}`;
							$("#ketBottom").html(hkb);
							$("#kelaskuu").html(kelaskuu.toUpperCase());
							$("#lintasanku").html(": " + lintasanku.toUpperCase());
						} else if (json.payment_type == "cash_sobek") {
							html = "<table class='tabel tabel-no-border'>";
							html += "<tr><th colspan='5'>TIPE PENJUALAN : TUNAI (TIKET SOBEK) </th><tr>";
							html += "<tr>";
							html += "<td class='text-center'> No </td>";
							html += "<td class='text-center'> Jenis </td>";
							html += "<td class='text-center'> Tarif (Rp.) </td>";
							html += "<td class='text-center'> Produksi (Lbr) </td>";
							html += "<td class='text-center'> Pendapatan (Rp.)</td>";
							html += "</tr>";
							angka = 1;
							total_produksi_cash_sobek = 0;
							total_pendapatan_cash_sobek = 0;

							$.each(json.tunai_sobek, function(i, item) {
								total_produksi_cash_sobek += Number(item.produksi);
								total_pendapatan_cash_sobek += Number(item.pendapatan);

								hargaku = "";
								produksiku = 0;
								pendapatanku = 0;

								if (item.harga != null) {
									hargaku = formatIDR(item.harga)
								}

								if (item.produksi != null) {
									produksiku = formatIDR(item.produksi)
								}

								if (item.pendapatan != null) {
									pendapatanku = formatIDR(item.pendapatan)
								}

								$("#master_tabel").show();
								html += "<tr>";
								html += "<td class='text-center'>" + angka++ + "</td>";
								html += "<td class='text-left'>" + item.golongan + "</td>";
								html += "<td class='text-right'>" + hargaku + "</td>";
								html += "<td class='text-right'>" + produksiku + "</td>";
								html += "<td class='text-right'>" + pendapatanku + "</td>";
								html += "</tr>";
							});

							html += "<tr><td colspan='3'> <b>Sub Total</b></td><td class='text-right'>" + formatIDR(total_produksi_cash_sobek) + "</td><td class='text-right'>" + formatIDR(total_pendapatan_cash_sobek) + "</td></tr>";

							jumlah_produksi = Number(total_produksi_cash_sobek);
							jumlah_pendapatan = Number(total_pendapatan_cash_sobek);

							html += "<tr><td colspan='3'> <b>TOTAL JUMLAH ( Tunai (Tiket Sobek) )</b></td><td class='text-right'>" + formatIDR(jumlah_produksi) + "</td><td class='text-right'>" + formatIDR(jumlah_pendapatan) + "</td></tr>";

							html += "</table>";
							$("#tr").html(html);
							var cabangku = ": " + $('#port').find(":selected").text();
							var shiftku = ": " + $('#shift').find(":selected").text();
							var reguku = ": " + $('#regu').find(":selected").text();
							var petugasku = ": " + $('#petugas').find(":selected").text();
							var tanggalku = ": " + $.datepicker.formatDate("d M yy", new Date($('#datefrom').val())) + " - " + $.datepicker.formatDate("d M yy", new Date($('#dateto').val()));

							var kelaskuu = $('#ship_class').find(":selected").text();
							var vmku = $('#vm').find(":selected").text();
							var ticketType = $("#ticket_type").find(":selected").text();

							var lintasanku = json.lintasan;
							var destination = json.lintasan.destination;
							var status_report = json.status_approve;

							$("#cabangku").html(cabangku);
							$("#pelabuhanku").html(cabangku);
							$("#shiftku").html(shiftku);
							$("#reguku").html(reguku);
							$("#petugasku").html(petugasku);
							$("#tanggalku").html(tanggalku);
							$("#vmkita").html(vmku);
							$('#status_report').html(status_report.toUpperCase());
							$('#tipe_tiket').html(ticketType);

							// $("#status_report").html(status_report.toUpperCase());
							var inMerchant = $("#merchant").find(":selected").text();
							var typePayment = $("#payment_type").find(":selected").text();
							var remCol, ketMer;

							if (typePayment.toUpperCase() == "B2B") {
								remCol = ``;
								ketMer = `<td style="border-right: none !important;">MERCHANT</td>
										<td style="border-left: none !important;" colspan="3">: ${inMerchant.toUpperCase()}</td>`;
							} else {
								ketMer = ``;
							}

							var hkb = `${ketMer}`;
							$("#ketBottom").html(hkb);
							$("#kelaskuu").html(kelaskuu.toUpperCase());
							$("#lintasanku").html(": " + lintasanku.toUpperCase());
						} else if (json.payment_type == "cashless") {
							html = "<table class='tabel tabel-no-border'>";
							html += "<table style='tabel-no-border'>";
							html += "<tr><th colspan='5'>TIPE PENJUALAN : PREPAID CASHLESS</th><tr>";
							html += "<tr>";
							html += "<td class='text-center'> No </td>";
							html += "<td class='text-center'> Jenis </td>";
							html += "<td class='text-center'> Tarif (Rp.) </td>";
							html += "<td class='text-center'> Produksi (Lbr)</td>";
							html += "<td class='text-center'> Pendapatan (Rp.)</td>";
							html += "</tr>";
							angka = 1;
							total_produksi_cashless = 0;
							total_pendapatan_cashless = 0;

							$.each(json.cashless, function(i, item) {
								total_produksi_cashless += Number(item.produksi);
								total_pendapatan_cashless += Number(item.pendapatan);

								hargaku = "";
								produksiku = 0;
								pendapatanku = 0;

								if (item.harga != null) {
									hargaku = formatIDR(item.harga)
								}

								if (item.produksi != null) {
									produksiku = formatIDR(item.produksi)
								}

								if (item.pendapatan != null) {
									pendapatanku = formatIDR(item.pendapatan)
								}

								$("#master_tabel").show();
								html += "<tr>";
								html += "<td class='text-center'>" + angka++ + "</td>";
								html += "<td class='text-left'>" + item.golongan + "</td>";
								html += "<td class='text-right'>" + hargaku + "</td>";
								html += "<td class='text-right'>" + produksiku + "</td>";
								html += "<td class='text-right'>" + pendapatanku + "</td>";
								html += "</tr>";
							});

							html += "<tr><td colspan='3'> <b>Sub Total</b></td><td class='text-right'>" + formatIDR(total_produksi_cashless) + "</td><td class='text-right'>" + formatIDR(total_pendapatan_cashless) + "</td></tr>";

							jumlah_produksi = Number(total_produksi_cashless);
							jumlah_pendapatan = Number(total_pendapatan_cashless);

							html += "<tr><td colspan='3'> <b>TOTAL JUMLAH ( Cashless )</b></td><td class='text-right'>" + formatIDR(jumlah_produksi) + "</td><td class='text-right'>" + formatIDR(jumlah_pendapatan) + "</td></tr>";

							html += "</table>";
							$("#tr").html(html);
							var cabangku = ": " + $('#port').find(":selected").text();
							var shiftku = ": " + $('#shift').find(":selected").text();
							var reguku = ": " + $('#regu').find(":selected").text();
							var petugasku = ": " + $('#petugas').find(":selected").text();
							var tanggalku = ": " + $.datepicker.formatDate("d M yy", new Date($('#datefrom').val())) + " - " + $.datepicker.formatDate("d M yy", new Date($('#dateto').val()));

							var kelaskuu = $('#ship_class').find(":selected").text();
							var vmku = $('#vm').find(":selected").text();
							var ticketType = $("#ticket_type").find(":selected").text();

							var lintasanku = json.lintasan;
							var destination = json.lintasan.destination;
							var status_report = json.status_approve;

							$("#cabangku").html(cabangku);
							$("#pelabuhanku").html(cabangku);
							$("#shiftku").html(shiftku);
							$("#reguku").html(reguku);
							$("#petugasku").html(petugasku);
							$("#tanggalku").html(tanggalku);
							$("#vmkita").html(vmku);
							$('#status_report').html(status_report.toUpperCase());
							$('#tipe_tiket').html(ticketType);

							// $("#status_report").html(status_report.toUpperCase());
							var inMerchant = $("#merchant").find(":selected").text();
							var typePayment = $("#payment_type").find(":selected").text();
							var remCol, ketMer;

							if (typePayment.toUpperCase() == "B2B") {
								remCol = ``;
								ketMer = `<td style="border-right: none !important;">MERCHANT</td>
										<td style="border-left: none !important;" colspan="3">: ${inMerchant.toUpperCase()}</td>`;
							} else {
								ketMer = ``;
							}

							var hkb = `${ketMer}`;
							$("#ketBottom").html(hkb);
							$("#kelaskuu").html(kelaskuu.toUpperCase());
							$("#lintasanku").html(": " + lintasanku.toUpperCase());
						} else if (json.payment_type == "online") {
							html = "<table class='tabel tabel-no-border'>";
							html += "<tr><th colspan='5'>TIPE PENJUALAN : TIKET ONLINE</th><tr>";
							html += "<tr>";
							html += "<td class='text-center'> No </td>";
							html += "<td class='text-center'> Jenis </td>";
							html += "<td class='text-center'> Tarif (Rp.) </td>";
							html += "<td class='text-center'> Produksi (Lbr) </td>";
							html += "<td class='text-center'> Pendapatan (Rp.)</td>";
							html += "</tr>";
							angka = 1;
							total_produksi_online = 0;
							total_pendapatan_online = 0;

							$.each(json.online, function(i, item) {
								total_produksi_online += Number(item.produksi);
								total_pendapatan_online += Number(item.pendapatan);

								hargaku = "";
								produksiku = 0;
								pendapatanku = 0;

								if (item.harga != null) {
									hargaku = formatIDR(item.harga)
								}

								if (item.produksi != null) {
									produksiku = formatIDR(item.produksi)
								}

								if (item.pendapatan != null) {
									pendapatanku = formatIDR(item.pendapatan)
								}

								$("#master_tabel").show();
								html += "<tr>";
								html += "<td class='text-center'>" + angka++ + "</td>";
								html += "<td class='text-left'>" + item.golongan + "</td>";
								html += "<td class='text-right'>" + hargaku + "</td>";
								html += "<td class='text-right'>" + produksiku + "</td>";
								html += "<td class='text-right'>" + pendapatanku + "</td>";
								html += "</tr>";
							});

							jumlah_produksi = Number(total_produksi_online);
							jumlah_pendapatan = Number(total_pendapatan_online);

							html += "<tr><td colspan='3'> <b>Sub Total</b></td><td class='text-right'>" + formatIDR(total_produksi_online) + "</td><td class='text-right'>" + formatIDR(total_pendapatan_online) + "</td></tr>";

							html += "<tr><td colspan='3'> <b>TOTAL JUMLAH ( Online )</b></td><td class='text-right'>" + formatIDR(jumlah_produksi) + "</td><td class='text-right'>" + formatIDR(jumlah_pendapatan) + "</td></tr>";

							html += "</table>";
							$("#tr").html(html);
							var cabangku = ": " + $('#port').find(":selected").text();
							var shiftku = ": " + $('#shift').find(":selected").text();
							var reguku = ": " + $('#regu').find(":selected").text();
							var petugasku = ": " + $('#petugas').find(":selected").text();
							var tanggalku = ": " + $.datepicker.formatDate("d M yy", new Date($('#datefrom').val())) + " - " + $.datepicker.formatDate("d M yy", new Date($('#dateto').val()));

							var kelaskuu = $('#ship_class').find(":selected").text();
							var vmku = $('#vm').find(":selected").text();
							var ticketType = $("#ticket_type").find(":selected").text();

							var lintasanku = json.lintasan;
							var destination = json.lintasan.destination;
							var status_report = json.status_approve;

							$("#cabangku").html(cabangku);
							$("#pelabuhanku").html(cabangku);
							$("#shiftku").html(shiftku);
							$("#reguku").html(reguku);
							$("#petugasku").html(petugasku);
							$("#tanggalku").html(tanggalku);
							$("#vmkita").html(vmku);
							$('#status_report').html(status_report.toUpperCase());
							$('#tipe_tiket').html(ticketType);

							// $("#status_report").html(status_report.toUpperCase());
							var inMerchant = $("#merchant").find(":selected").text();
							var typePayment = $("#payment_type").find(":selected").text();
							var remCol, ketMer;

							if (typePayment.toUpperCase() == "B2B") {
								remCol = ``;
								ketMer = `<td style="border-right: none !important;">MERCHANT</td>
										<td style="border-left: none !important;" colspan="3">: ${inMerchant.toUpperCase()}</td>`;
							} else {
								ketMer = ``;
							}

							var hkb = `${ketMer}`;
							$("#ketBottom").html(hkb);
							$("#kelaskuu").html(kelaskuu.toUpperCase());
							$("#lintasanku").html(": " + lintasanku.toUpperCase());
						} else if (json.payment_type == "ifcs") {
							html = "<table class='tabel tabel-no-border'>";
							html += "<tr><th colspan='5'>TIPE PENJUALAN : IFCS</th><tr>";
							html += "<tr>";
							html += "<td class='text-center'> No </td>";
							html += "<td class='text-center'> Jenis </td>";
							html += "<td class='text-center'> Tarif (Rp.) </td>";
							html += "<td class='text-center'> Produksi (Lbr) </td>";
							html += "<td class='text-center'> Pendapatan (Rp.)</td>";
							html += "</tr>";
							angka = 1;
							total_produksi_ifcs = 0;
							total_pendapatan_ifcs = 0;

							$.each(json.ifcs, function(i, item) {
								total_produksi_ifcs += Number(item.produksi);
								total_pendapatan_ifcs += Number(item.pendapatan);

								hargaku = "";
								produksiku = 0;
								pendapatanku = 0;

								if (item.harga != null) {
									hargaku = formatIDR(item.harga)
								}

								if (item.produksi != null) {
									produksiku = formatIDR(item.produksi)
								}

								if (item.pendapatan != null) {
									pendapatanku = formatIDR(item.pendapatan)
								}

								$("#master_tabel").show();
								html += "<tr>";
								html += "<td class='text-center'>" + angka++ + "</td>";
								html += "<td class='text-left'>" + item.golongan + "</td>";
								html += "<td class='text-right'>" + hargaku + "</td>";
								html += "<td class='text-right'>" + produksiku + "</td>";
								html += "<td class='text-right'>" + pendapatanku + "</td>";
								html += "</tr>";
							});

							jumlah_produksi = Number(total_produksi_ifcs);
							jumlah_pendapatan = Number(total_pendapatan_ifcs);

							html += "<tr><td colspan='3'> <b>Sub Total</b></td><td class='text-right'>" + formatIDR(total_produksi_ifcs) + "</td><td class='text-right'>" + formatIDR(total_pendapatan_ifcs) + "</td></tr>";

							html += "<tr><td colspan='3'> <b>TOTAL JUMLAH ( IFCS )</b></td><td class='text-right'>" + formatIDR(jumlah_produksi) + "</td><td class='text-right'>" + formatIDR(jumlah_pendapatan) + "</td></tr>";

							html += "</table>";
							$("#tr").html(html);
							var cabangku = ": " + $('#port').find(":selected").text();
							var shiftku = ": " + $('#shift').find(":selected").text();
							var reguku = ": " + $('#regu').find(":selected").text();
							var petugasku = ": " + $('#petugas').find(":selected").text();
							var tanggalku = ": " + $.datepicker.formatDate("d M yy", new Date($('#datefrom').val())) + " - " + $.datepicker.formatDate("d M yy", new Date($('#dateto').val()));

							var kelaskuu = $('#ship_class').find(":selected").text();
							var ticketType = $("#ticket_type").find(":selected").text();

							var lintasanku = json.lintasan;
							var destination = json.lintasan.destination;
							var status_report = json.status_approve;

							$("#cabangku").html(cabangku);
							$("#pelabuhanku").html(cabangku);
							$("#shiftku").html(shiftku);
							$("#reguku").html(reguku);
							$("#petugasku").html(petugasku);
							$("#tanggalku").html(tanggalku);
							$('#status_report').html(status_report.toUpperCase());
							$('#tipe_tiket').html(ticketType);

							// $("#status_report").html(status_report.toUpperCase());
							var inMerchant = $("#merchant").find(":selected").text();
							var typePayment = $("#payment_type").find(":selected").text();
							var remCol, ketMer;

							if (typePayment.toUpperCase() == "B2B") {
								remCol = ``;
								ketMer = `<td style="border-right: none !important;">MERCHANT</td>
										<td style="border-left: none !important;" colspan="3">: ${inMerchant.toUpperCase()}</td>`;
							} else {
								ketMer = ``;
							}

							var hkb = `${ketMer}`;
							$("#ketBottom").html(hkb);
							$("#kelaskuu").html(kelaskuu.toUpperCase());
							$("#lintasanku").html(": " + lintasanku.toUpperCase());
						} else if (json.payment_type == "ifcs_redeem") {
							html = "<table class='tabel tabel-no-border'>";
							html += "<tr><th colspan='5'>TIPE PENJUALAN : IFCS Redeem</th><tr>";
							html += "<tr>";
							html += "<td class='text-center'> No </td>";
							html += "<td class='text-center'> Jenis </td>";
							html += "<td class='text-center'> Tarif (Rp.) </td>";
							html += "<td class='text-center'> Produksi (Lbr) </td>";
							html += "<td class='text-center'> Pendapatan (Rp.)</td>";
							html += "</tr>";
							angka = 1;
							total_produksi_ifcs = 0;
							total_pendapatan_ifcs = 0;

							$.each(json.ifcs_redeem, function(i, item) {
								total_produksi_ifcs += Number(item.produksi);
								total_pendapatan_ifcs += Number(item.pendapatan);

								hargaku = "";
								produksiku = 0;
								pendapatanku = 0;

								if (item.harga != null) {
									hargaku = formatIDR(item.harga)
								}

								if (item.produksi != null) {
									produksiku = formatIDR(item.produksi)
								}

								if (item.pendapatan != null) {
									pendapatanku = formatIDR(item.pendapatan)
								}

								$("#master_tabel").show();
								html += "<tr>";
								html += "<td class='text-center'>" + angka++ + "</td>";
								html += "<td class='text-left'>" + item.golongan + "</td>";
								html += "<td class='text-right'>" + hargaku + "</td>";
								html += "<td class='text-right'>" + produksiku + "</td>";
								html += "<td class='text-right'>" + pendapatanku + "</td>";
								html += "</tr>";
							});

							jumlah_produksi = Number(total_produksi_ifcs);
							jumlah_pendapatan = Number(total_pendapatan_ifcs);

							html += "<tr><td colspan='3'> <b>Sub Total</b></td><td class='text-right'>" + formatIDR(total_produksi_ifcs) + "</td><td class='text-right'>" + formatIDR(total_pendapatan_ifcs) + "</td></tr>";

							html += "<tr><td colspan='3'> <b>TOTAL JUMLAH ( IFCS Redeem )</b></td><td class='text-right'>" + formatIDR(jumlah_produksi) + "</td><td class='text-right'>" + formatIDR(jumlah_pendapatan) + "</td></tr>";

							html += "</table>";
							$("#tr").html(html);
							var cabangku = ": " + $('#port').find(":selected").text();
							var shiftku = ": " + $('#shift').find(":selected").text();
							var reguku = ": " + $('#regu').find(":selected").text();
							var petugasku = ": " + $('#petugas').find(":selected").text();
							var tanggalku = ": " + $.datepicker.formatDate("d M yy", new Date($('#datefrom').val())) + " - " + $.datepicker.formatDate("d M yy", new Date($('#dateto').val()));

							var kelaskuu = $('#ship_class').find(":selected").text();
							var ticketType = $("#ticket_type").find(":selected").text();

							var lintasanku = json.lintasan;
							var destination = json.lintasan.destination;
							var status_report = json.status_approve;

							$("#cabangku").html(cabangku);
							$("#pelabuhanku").html(cabangku);
							$("#shiftku").html(shiftku);
							$("#reguku").html(reguku);
							$("#petugasku").html(petugasku);
							$("#tanggalku").html(tanggalku);
							$('#status_report').html(status_report.toUpperCase());
							$('#tipe_tiket').html(ticketType);

							// $("#status_report").html(status_report.toUpperCase());
							var inMerchant = $("#merchant").find(":selected").text();
							var typePayment = $("#payment_type").find(":selected").text();
							var remCol, ketMer;

							if (typePayment.toUpperCase() == "B2B") {
								remCol = ``;
								ketMer = `<td style="border-right: none !important;">MERCHANT</td>
										<td style="border-left: none !important;" colspan="3">: ${inMerchant.toUpperCase()}</td>`;
							} else {
								ketMer = ``;
							}

							var hkb = `${ketMer}`;
							$("#ketBottom").html(hkb);
							$("#kelaskuu").html(kelaskuu.toUpperCase());
							$("#lintasanku").html(": " + lintasanku.toUpperCase());
						} else if (json.payment_type == "b2b") {
							html = "<table class='tabel tabel-no-border'>";
							html += "<tr><th colspan='5'>TIPE PENJUALAN : TIKET B2B</th><tr>";
							html += "<tr>";
							html += "<td class='text-center'> No </td>";
							html += "<td class='text-center'> Jenis </td>";
							html += "<td class='text-center'> Tarif (Rp.) </td>";
							html += "<td class='text-center'> Produksi (Lbr) </td>";
							html += "<td class='text-center'> Pendapatan (Rp.)</td>";
							html += "</tr>";
							angka = 1;
							total_produksi_b2b = 0;
							total_pendapatan_b2b = 0;

							$.each(json.b2b, function(i, item) {
								total_produksi_b2b += Number(item.produksi);
								total_pendapatan_b2b += Number(item.pendapatan);

								hargaku = "";
								produksiku = 0;
								pendapatanku = 0;

								if (item.harga != null) {
									hargaku = formatIDR(item.harga)
								}

								if (item.produksi != null) {
									produksiku = formatIDR(item.produksi)
								}

								if (item.pendapatan != null) {
									pendapatanku = formatIDR(item.pendapatan)
								}

								$("#master_tabel").show();
								html += "<tr>";
								html += "<td class='text-center'>" + angka++ + "</td>";
								html += "<td class='text-left'>" + item.golongan + "</td>";
								html += "<td class='text-right'>" + hargaku + "</td>";
								html += "<td class='text-right'>" + produksiku + "</td>";
								html += "<td class='text-right'>" + pendapatanku + "</td>";
								html += "</tr>";
							});

							jumlah_produksi = Number(total_produksi_b2b);
							jumlah_pendapatan = Number(total_pendapatan_b2b);

							html += "<tr><td colspan='3'> <b>Sub Total</b></td><td class='text-right'>" + formatIDR(total_produksi_b2b) + "</td><td class='text-right'>" + formatIDR(total_pendapatan_b2b) + "</td></tr>";

							html += "<tr><td colspan='3'> <b>TOTAL JUMLAH ( B2B )</b></td><td class='text-right'>" + formatIDR(jumlah_produksi) + "</td><td class='text-right'>" + formatIDR(jumlah_pendapatan) + "</td></tr>";

							html += "</table>";
							$("#tr").html(html);
							var cabangku = ": " + $('#port').find(":selected").text();
							var shiftku = ": " + $('#shift').find(":selected").text();
							var reguku = ": " + $('#regu').find(":selected").text();
							var petugasku = ": " + $('#petugas').find(":selected").text();
							var tanggalku = ": " + $.datepicker.formatDate("d M yy", new Date($('#datefrom').val())) + " - " + $.datepicker.formatDate("d M yy", new Date($('#dateto').val()));

							var kelaskuu = $('#ship_class').find(":selected").text();
							var vmku = $('#vm').find(":selected").text();
							var ticketType = $("#ticket_type").find(":selected").text();

							var lintasanku = json.lintasan;
							var destination = json.lintasan.destination;
							var status_report = json.status_approve;

							$("#cabangku").html(cabangku);
							$("#pelabuhanku").html(cabangku);
							$("#shiftku").html(shiftku);
							$("#reguku").html(reguku);
							$("#petugasku").html(petugasku);
							$("#tanggalku").html(tanggalku);
							$("#vmkita").html(vmku);
							$('#status_report').html(status_report.toUpperCase());
							$('#tipe_tiket').html(ticketType);

							// $("#status_report").html(status_report.toUpperCase());
							var inMerchant = $("#merchant").find(":selected").text();
							var typePayment = $("#payment_type").find(":selected").text();
							var remCol, ketMer;

							if (typePayment.toUpperCase() == "B2B") {
								remCol = ``;
								ketMer = `<td style="border-right: none !important;">MERCHANT</td>
										<td style="border-left: none !important;" colspan="3">: ${inMerchant.toUpperCase()}</td>`;
							} else {
								ketMer = ``;
							}

							var hkb = `${ketMer}`;
							$("#ketBottom").html(hkb);
							$("#kelaskuu").html(kelaskuu.toUpperCase());
							$("#lintasanku").html(": " + lintasanku.toUpperCase());
						}
					} else {
						toastr.warning(json.message, 'Peringatan');
						document.getElementById('master_tabel').style.display = 'none';
					}
				},

				error: function(result) {
					alert('error');
				}

			});
		});

		setTimeout(function() {
			$('.menu-toggler').trigger('click');
		}, 1);

		$(".menu-toggler").click(function() {
			$('.select2').css('width', '100%');
		});

		$("#payment_type").on("change", function() {
			var channel = $(this).val();
			$.ajax({
				url: "<?php echo site_url('laporan/ticket_terpadu_terjual/get_merchant') ?>",
				type: "POST",
				data: {
					channel: channel
				},
				beforeSend: function() {
					var valOption = $("#payment_type option:selected").html();
					if (valOption.toLocaleLowerCase() == 'b2b') {
						$("#fMerchant").removeClass("hide");
					} else {
						$("#fMerchant").addClass("hide");
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
		})
	});
</script>