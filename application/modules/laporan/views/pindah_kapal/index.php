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
					<?php echo $title; ?>

				</div>
			</div>
			<div class="portlet-body">
				<div class="row">
					<div class="col-md-12">
						<div class="table-toolbar" style="margin-bottom: 0px">
							<div class="row">
								<div class="col-md-3" style="padding-left: 15px;">
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
								<div class="col-md-3" style="padding-left: 5px;">
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
											<select id="ship_class" <?php echo $selected ?> class="form-control js-data-example-ajax select2" dir=""><?php echo $class ?></select>
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
									<div class="col-md-3" style="padding-left: 30px;padding-right: 7px;">
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
									<div class="col-md-3" style="padding-left: 10px;padding-right: 25px;">
										<div class="form-group">
											<div class="input-group select2-bootstrap-prepend">
												<div class="input-group-addon">Regu </div>
												<select id="regu" class="form-control select2" dir="">
													<option value="">Semua</option>
												</select>
											</div>
										</div>
									</div>
									<div class="col-md-2" style="padding-left: 0px;">
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

						<?= generate_button('laporan/pindah_kapal', 'download_pdf', '<button id="printerkita" class="btn btn-sm btn-default"><i class="fa fa-file-pdf-o" style="color: #ea5460"></i> PDF</button>') ?>
						<?= generate_button('laporan/pindah_kapal', 'download_excel', '<button id="excelkita" class="btn btn-sm btn-default"><i class="fa fa-file-excel-o" style="color: #ea5460"></i> EXCEL</button>') ?>

						<!-- <button id="printerkita" class="btn btn-sm btn-default"><i class="fa fa-file-pdf-o" style="color: #ea5460"></i> PDF</button> -->

						<!-- <button id="excelkita" class="btn btn-sm btn-default"><i class="fa fa-file-excel-o" style="color: #ea5460"></i> EXCEL</button><br> -->
					</div><br>

					<table class="tabel full-width" align="center">
						<tr>
							<td rowspan="4" class="no-border-right" style="width: 10%">
								<img src="<?php echo base_url(); ?>assets/img/asdp-logo2.jpg" style="width:100px; height: auto">
							</td>
							<td rowspan="4" class="center bold" style="width: 60%;font-size: 14pt; line-height: 1.5">
								LAPORAN REKAPITULASI PINDAH KAPAL</span>
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
							<td style="border-right: none !important;width: 15%">SHIFT</td>
							<td style="border-left: none !important;"><span id="shiftku"></span></td>
						</tr>
						<tr>
							<td style="border-right: none !important;">PELABUHAN</td>
							<td style="border-left: none !important;"><span id="pelabuhanku"></span></td>
							<td style="border-right: none !important;">REGU</td>
							<td style="border-left: none !important;"><span id="reguku"></span></td>
						</tr>
						<tr>
							<td style="border-right: none !important;">LINTASAN</td>
							<td style="border-left: none !important;"><span id="lintasanku"></span> </td>
							<td style="border-right: none !important;">TANGGAL</td>
							<td style="border-left: none !important;"><span id="tanggalku"></span></td>
						</tr>
						<tr>
							<td style="border-right: none !important;">KAPAL AWAL</td>
							<td style="border-left: none !important;">: <span id="ship_origin"></td>
							<td style="border-right: none !important;">KAPAL TUJUAN</td>
							<td style="border-left: none !important;">: <span id="ship_destination"></span></td>
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
					url: "pindah_kapal/get_regu/" + $("#port").val(),
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

			window.location = "<?php echo site_url('laporan/pindah_kapal/get_pdf?port=') ?>" + port +
				"&datefrom=" + datefrom +
				"&dateto=" + dateto +
				"&ship_class=" + ship_class +
				"&shift=" + shift +
				"&regu=" + regu +
				"&cabangku=" + $('#port').find(":selected").text() +
				"&pelabuhanku=" + $('#port').find(":selected").text() +
				"&ship_classku=" + $('#ship_class').find(":selected").text() +
				"&shiftku=" + $('#shift').find(":selected").text() +
				"&reguku=" + $('#regu').find(":selected").text();
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

			window.location = "<?php echo site_url('laporan/pindah_kapal/get_excel?port=') ?>" + port +
				"&datefrom=" + datefrom +
				"&dateto=" + dateto +
				"&ship_class=" + ship_class +
				"&shift=" + shift +
				"&regu=" + regu +
				"&cabangku=" + $('#port').find(":selected").text() +
				"&pelabuhanku=" + $('#port').find(":selected").text() +
				"&ship_classku=" + $('#ship_class').find(":selected").text() +
				"&shiftku=" + $('#shift').find(":selected").text() +
				"&reguku=" + $('#regu').find(":selected").text();
		});

		$("#cari").on("click", function(e) {
			$(this).button('loading');
			e.preventDefault();

			$.ajax({
				type: "POST",
				url: "<?php echo site_url('laporan/pindah_kapal') ?>",
				dataType: 'json',
				data: {
					port: $("#port").val(),
					datefrom: $("#datefrom").val(),
					dateto: $("#dateto").val(),
					ship_class: $("#ship_class").val(),
					shift: $("#shift").val(),
					regu: $("#regu").val(),
				},

				success: function(json) {
					$("#cari").button('reset');
					if (json.code == 200) {
						// alert(1)
						html = "<table class='tabel tabel-no-border'>";
						html += "<tr><th colspan='5'>1. Penumpang</th><tr>";
						html += "<tr>";
						html += "<td class='text-center'> No </td>";
						html += "<td class='text-center'> Jenis </td>";
						html += "<td class='text-center'> Tarif (Rp.) </td>";
						html += "<td class='text-center'> Produksi (Lbr) </td>";
						html += "<td class='text-center'> Pendapatan (Rp.)</td>";
						html += "</tr>";
						angka = 1;
						total_produksi_penumpang = 0;
						total_pendapatan_penumpang = 0;

						$.each(json.penumpang, function(i, item) {
							total_produksi_penumpang += Number(item.produksi);
							total_pendapatan_penumpang += Number(item.pendapatan);

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

						html += "<tr><td colspan='3'> <b>Sub Total</b></td><td class='text-right'>" + formatIDR(total_produksi_penumpang) + "</td><td class='text-right'>" + formatIDR(total_pendapatan_penumpang) + "</td></tr>";

						html += "<table style='tabel-no-border'>";
						html += "<tr><th colspan='5'>2. KENDARAAN</th><tr>";
						html += "<tr>";
						html += "<td class='text-center'> No </td>";
						html += "<td class='text-center'> Jenis </td>";
						html += "<td class='text-center'> Tarif (Rp.) </td>";
						html += "<td class='text-center'> Produksi (Lbr)</td>";
						html += "<td class='text-center'> Pendapatan (Rp.)</td>";
						html += "</tr>";
						angka = 1;
						total_produksi_kendaraan = 0;
						total_pendapatan_kendaraan = 0;

						$.each(json.kendaraan, function(i, item) {
							total_produksi_kendaraan += Number(item.produksi);
							total_pendapatan_kendaraan += Number(item.pendapatan);

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

						html += "<tr><td colspan='3'> <b>Sub Total</b></td><td class='text-right'>" + formatIDR(total_produksi_kendaraan) + "</td><td class='text-right'>" + formatIDR(total_pendapatan_kendaraan) + "</td></tr>";

						jumlah_produksi = total_produksi_penumpang + total_produksi_kendaraan;
						jumlah_pendapatan = total_pendapatan_penumpang + total_pendapatan_kendaraan;

						html += "<tr><td colspan='3'> <b>TOTAL JUMLAH (Penumpang + Kendaraan)</b></td><td class='text-right'>" + formatIDR(jumlah_produksi) + "</td><td class='text-right'>" + formatIDR(jumlah_pendapatan) + "</td></tr>";

						html += "</table>";
						$("#tr").html(html);
						var cabangku = ": " + $('#port').find(":selected").text();
						var shiftku = ": " + $('#shift').find(":selected").text();
						var reguku = ": " + $('#regu').find(":selected").text();
						var petugasku = ": " + $('#petugas').find(":selected").text();
						var tanggalku = ": " + $.datepicker.formatDate("d M yy", new Date($('#datefrom').val())) + " - " + $.datepicker.formatDate("d M yy", new Date($('#dateto').val()));

						var kelaskuu = $('#ship_class').find(":selected").text();

						var lintasanku = json.lintasan;
						var ship_originku = json.ship_origin;
						var ship_destinationku = json.ship_destination;
						var destination = json.lintasan.destination;

						$("#cabangku").html(cabangku);
						$("#pelabuhanku").html(cabangku);
						$("#shiftku").html(shiftku);
						$("#reguku").html(reguku);
						$("#petugasku").html(petugasku);
						$("#tanggalku").html(tanggalku);
						$("#ship_origin").html(ship_originku);
						$("#ship_destination").html(ship_destinationku);

						$("#kelaskuu").html(kelaskuu.toUpperCase());
						$("#lintasanku").html(": " + lintasanku.toUpperCase());
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
	});
</script>