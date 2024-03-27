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
											<span class="input-group-addon">Tanggal Berangkat</span>
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
											<select id="ship_class" <?php echo $selected ?> class="form-control select2" dir="">
												<?php echo $class ?>
											</select>
										</div>
									</div>
								</div>
								<div class="col-md-1" style="padding-left: 0px;">
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

				<div id="ini_replace"></div>

				<div id="master_tabel" style="display: none">

					<div class="form-inline">
						<button id="printerkita" class="btn btn-sm btn-default"><i class="fa fa-file-pdf-o" style="color: #ea5460"></i> PDF</button>

						<button id="excelkita" class="btn btn-sm btn-default"><i class="fa fa-file-excel-o" style="color: #ea5460"></i> EXCEL</button><br>
					</div><br>

					<table class="tabel full-width" align="center">
						<tr>
							<td rowspan="4" class="no-border-right" style="width: 10%">
								<img src="<?php echo base_url(); ?>assets/img/asdp-logo2.jpg" style="width:100px; height: auto">
							</td>
							<td rowspan="4" class="center bold" style="width: 60%;font-size: 14pt; line-height: 1.5">
								LAPORAN REKAPITULASI SURAT ANGKUT BEBAS</span>
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
							<td style="border-right: none !important;width: 15%">LINTASAN</td>
							<td style="border-left: none !important;"><span id="lintasanku"></span></td>
						</tr>
						<tr>
							<td style="border-right: none !important;">PELABUHAN</td>
							<td style="border-left: none !important;"><span id="pelabuhanku"></span> </td>
							<td style="border-right: none !important;">TANGGAL</td>
							<td style="border-left: none !important;"><span id="tanggalku"></span></td>
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
			port = $("#port").val();
			datefrom = $("#datefrom").val();
			dateto = $("#dateto").val();
			ship_class = $("#ship_class").val();

			window.location = "<?php echo site_url('laporan/sab/get_pdf?port=') ?>" + port +
				"&datefrom=" + datefrom +
				"&dateto=" + dateto +
				"&ship_class=" + ship_class +
				"&cabangku=" + $('#port').find(":selected").text() +
				"&pelabuhanku=" + $('#port').find(":selected").text() +
				"&ship_classku=" + $('#ship_class').find(":selected").text()
		});

		$("#excelkita").on("click", function(e) {
			port = $("#port").val();
			datefrom = $("#datefrom").val();
			dateto = $("#dateto").val();
			ship_class = $("#ship_class").val();

			window.location = "<?php echo site_url('laporan/sab/get_excel?port=') ?>" + port +
				"&datefrom=" + datefrom +
				"&dateto=" + dateto +
				"&ship_class=" + ship_class +
				"&cabangku=" + $('#port').find(":selected").text() +
				"&pelabuhanku=" + $('#port').find(":selected").text() +
				"&ship_classku=" + $('#ship_class').find(":selected").text()
		});

		$("#cari").on("click", function(e) {
			$(this).button('loading');
			e.preventDefault();

			$.ajax({
				type: "POST",
				url: "<?php echo site_url('laporan/sab/') ?>",
				dataType: 'json',
				data: {
					port: $("#port").val(),
					datefrom: $("#datefrom").val(),
					dateto: $("#dateto").val(),
					ship_class: $("#ship_class").val(),
				},

				success: function(json) {
					$("#cari").button('reset');
					if (json.code == 200) {
						html = "<table class='tabel tabel-no-border'>";
						html += "<tr>";
						html += "<td class='text-center'> No </td>";
						html += "<td class='text-center'> Jenis Tiket SAB </td>";
						html += "<td class='text-center'> Produksi </td>";
						html += "</tr>";
						html += "<tr><th colspan='5'>1. PENUMPANG</th><tr>";
						angka = 1;
						total_produksi_penumpang = 0;

						$.each(json.penumpang, function(i, item) {
							total_produksi_penumpang += Number(item.produksi);

							produksiku = 0;

							if (item.produksi != null) {
								produksiku = formatIDR(item.produksi)
							}

							$("#master_tabel").show();
							html += "<tr>";
							html += "<td class='text-center'>" + angka++ + "</td>";
							html += "<td class='text-left'>" + item.golongan + "</td>";
							html += "<td class='text-right'>" + produksiku + "</td>";
							html += "</tr>";
						});

						html += "<tr><td colspan='2'> <b>Sub Total</b></td><td class='text-right'>" + formatIDR(total_produksi_penumpang) + "</tr>";
						html += "<tr><th colspan='5'>2. KENDARAAN</th><tr>";

						angka = 1;
						total_produksi_kendaraan = 0;

						$.each(json.kendaraan, function(i, item) {
							total_produksi_kendaraan += Number(item.produksi);

							produksiku = 0;

							if (item.produksi != null) {
								produksiku = formatIDR(item.produksi)
							}

							$("#master_tabel").show();
							html += "<tr>";
							html += "<td class='text-center'>" + angka++ + "</td>";
							html += "<td class='text-left'>" + item.golongan + "</td>";
							html += "<td class='text-right'>" + produksiku + "</td>";
							html += "</tr>";
						});

						jumlah_produksi = Number(total_produksi_penumpang + total_produksi_kendaraan);

						html += "<tr><td colspan='2'> <b>Sub Total</b></td><td class='text-right'>" + formatIDR(total_produksi_kendaraan) + "</tr>";
						html += "<tr><td colspan='2'> <b>Total</b></td><td class='text-right'>" + formatIDR(jumlah_produksi) + "</tr>";

						html += "</table>";
						$("#tr").html(html);
						var cabangku = ": " + $('#port').find(":selected").text();
						var shiftku = ": " + $('#shift').find(":selected").text();
						var reguku = ": " + $('#regu').find(":selected").text();
						var petugasku = ": " + $('#petugas').find(":selected").text();
						var tanggalku = ": " + $.datepicker.formatDate("d M yy", new Date($('#datefrom').val())) + " - " + $.datepicker.formatDate("d M yy", new Date($('#dateto').val()));

						var kelaskuu = $('#ship_class').find(":selected").text();

						var lintasanku = json.lintasan;
						var destination = json.lintasan.destination;

						$("#cabangku").html(cabangku);
						$("#pelabuhanku").html(cabangku);
						$("#shiftku").html(shiftku);
						$("#reguku").html(reguku);
						$("#petugasku").html(petugasku);
						$("#tanggalku").html(tanggalku);

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