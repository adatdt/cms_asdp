<style type="text/css">
	.judul-tabel-atas tr {
		border-collapse: collapse;
	}

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
								<div class="col-md-3" style="padding-right: 0px;">
									<div class="form-group">
										<div class="input-group">
											<span class="input-group-addon">Pelabuhan</span>
											<select id="port" class="form-control js-data-example-ajax select2" dir="">
												<option value="">Semua</option>
												<?php foreach ($port as $key => $value) { ?>
													<option value="<?= $this->enc->encode($value->id) ?>"><?= $value->name ?></option>
												<?php } ?>
											</select>
										</div>
									</div>
								</div>
								<div class="col-md-5">
									<div class="form-group">
										<div class="input-group">
											<span class="input-group-addon">Tanggal Shift</span>
											<input type="text" autocomplete="off" id="datefrom" class="form-control" value="<?php echo date('Y-m-d'); ?>">
											</input>
											<div class="input-group-addon">s/d</div>
											<input type="text" autocomplete="off" id="dateto" class="form-control" value="<?php echo date('Y-m-d'); ?>">
											</input>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-3" style="padding-right: 0px;">
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
										<select id="ship_class" <?php echo $selected ?> class="form-control js-data-example-ajax select2" dir="">
											<?php echo $class ?>
										</select>
									</div>
								</div>
							</div>
							<div class="col-md-2" style="padding-right: 0px;">
								<div class="form-group">
									<div class="input-group">
										<div class="input-group-addon">SHIFT </div>
										<select id="shift" class="form-control js-data-example-ajax select2" dir="">
											<option value="">Semua</option>
											<?php foreach ($shift as $key => $value) { ?>
												<option value="<?= $this->enc->encode($value->id) ?>"><?= $value->shift_name ?></option>
											<?php } ?>
										</select>
									</div>
								</div>
							</div>
							
							<!-- ini terkait laporan tiket manual, penambahan filter tiket manual belum naik dan belum jelas tiket manual -->
							<!-- <div class="col-md-3" >
								<div class="form-group">
									<div class="input-group">
										<div class="input-group-addon">TIPE TIKET </div>
										<?= form_dropdown("ticketType",$ticketType,"",' id="ticketType" class="form-control js-data-example-ajax select2"') ?>
									</div>
								</div>
							</div>		 -->
															
							<div class="col-md-2" style="padding-left: 5px;">
								<div class="form-group">
									<div class="input-group">
										<button type="button" class="btn btn-danger" id="cari" data-loading-text="<i class='fa fa-spinner fa-spin'></i> Mencari...">Cari</button>
									</div>
								</div>
							</div>
						</div>

						<div id="ini_replace"></div>

						<div id="master_tabel" style="display: none">

						<?= generate_button('laporan/pendapatan_asuransi', 'download_pdf', '<button id="printerkita" class="btn btn-sm btn-default"><i class="fa fa-file-pdf-o" style="color: #ea5460"></i> PDF</button>') ?>
						<?= generate_button('laporan/pendapatan_asuransi', 'download_excel', '<button id="excelkita" class="btn btn-sm btn-default"><i class="fa fa-file-excel-o" style="color: #ea5460"></i> EXCEL</button>') ?>
							
							<br><br>

							<table class="tabel full-width" align="center">
								<tr>
									<td rowspan="4" class="no-border-right" style="width: 10%">
										<img src="<?php echo base_url(); ?>assets/img/asdp-logo2.jpg" style="width:100px; height: auto">
									</td>
									<td rowspan="4" class="center bold" style="width: 60%;font-size: 14pt; line-height: 1.5">
										FORMULIR LAPORAN PENDAPATAN ASURANSI<br>IURAN WAJIB PER-SHIFT</span>
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
									<td style="border-right: none !important;">SHIFT</td>
									<td style="border-left: none !important;"><span id="shiftku"></td>
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
									<td style="border-right: none !important;">STATUS</td>
									<td style="border-left: none !important;">: <b><span id="status_approve"></span></b></td>
									<td style="border-right: none !important;">TIPE TIKET</td>
									<td style="border-left: none !important;"><span id="ticketTypeku"></span></td>
								</tr>
							</table>

							<br>

							<table class="tabel full-width">
								<tbody id="tr"></tbody>
							</table>
						</div>
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
				shift = $("#shift").val();
				ship_class = $("#ship_class").val();
				pelabuhan = $('#port').find(":selected").text();
				shift_name = $('#shift').find(":selected").text();
				
				ticketType = $("#ticketType").val();
				ticketTypeku = $('#ticketType').find(":selected").text();

				window.location = "<?php echo site_url('laporan/pendapatan_asuransi/get_pdf?port=') ?>" + port +
					"&datefrom=" + datefrom +
					"&dateto=" + dateto +
					"&shift=" + shift +
					"&ship_class=" + ship_class +
					"&pelabuhan=" + pelabuhan +
					"&shift_name=" + shift_name+
					"&ticketType="+ticketType+
					"&ticketTypeku="+ticketTypeku
			});

			$("#excelkita").on("click", function(e) {
				port = $("#port").val();
				datefrom = $("#datefrom").val();
				dateto = $("#dateto").val();
				shift = $("#shift").val();
				ship_class = $("#ship_class").val();
				pelabuhan = $('#port').find(":selected").text();
				shift_name = $('#shift').find(":selected").text();
				ticketType = $("#ticketType").val();
				ticketTypeku = $('#ticketType').find(":selected").text();

				window.location = "<?php echo site_url('laporan/pendapatan_asuransi/get_excel?port=') ?>" + port +
					"&datefrom=" + datefrom +
					"&dateto=" + dateto +
					"&shift=" + shift +
					"&ship_class=" + ship_class +
					"&pelabuhan=" + pelabuhan +
					"&shift_name=" + shift_name+
					"&ticketType="+ticketType+
					"&ticketTypeku="+ticketTypeku
			});

			$("#cari").on("click", function(e) {
				$(this).button('loading');
				e.preventDefault();

				$.ajax({
					type: "POST",
					url: "<?php echo site_url('laporan/pendapatan_asuransi/detail') ?>",
					dataType: 'json',
					data: {
						port: $("#port").val(),
						datefrom: $("#datefrom").val(),
						dateto: $("#dateto").val(),
						shift: $("#shift").val(),
						ticketType: $("#ticketType").val(),
						ship_class: $("#ship_class").val(),
						pelabuhan: $('#port').find(":selected").text(),
						shift_name: $('#shift').find(":selected").text(),
						ticketTypeku : $('#ticketType').find(":selected").text()
					},

					success: function(json) {
						$("#cari").button('reset');
						if (json.code == 200) {
							html = "<table class='table table-no-border'>";
							html += "<tr>";
							html += "<td class='text-center no-border-right'> NO </td>";
							html += "<td class='text-center'> JENIS TIKET </td>";
							html += "<td class='text-center'> TARIF (JR) </td>";
							html += "<td class='text-center'> PRODUKSI (Lbr)</td>";
							html += "<td class='text-center'> PENDAPATAN (Rp.)</td>";
							html += "<td class='text-center'> KETERANGAN</td>";
							html += "</tr>";
							html += "<tr><th colspan='6'>1. PENUMPANG</th><tr>";
							angka = 65;
							total_produksi_penumpang = 0;
							total_pendapatan_penumpang = 0;

							$.each(json.penumpang, function(i, item) {
								$("#master_tabel").show();
								total_produksi_penumpang += Number(item.produksi);
								total_pendapatan_penumpang += Number(item.insurance_fee);

								hargaku = "";
								produksiku = 0;
								pendapatanku = 0;

								if (item.harga != null) {
									hargaku = formatIDR(item.harga)
								}

								if (item.produksi != null) {
									produksiku = formatIDR(item.produksi)
								}

								if (item.insurance_fee != null) {
									pendapatanku = formatIDR(item.insurance_fee)
								}

								html += "<tr>";
								html += "<td class='text-center'>" + String.fromCharCode(angka++).toLowerCase() + "</td>";
								html += "<td class='text-left'>" + item.golongan + "</td>";
								html += "<td class='text-right'>" + hargaku + "</td>";
								html += "<td class='text-right'>" + produksiku + "</td>";
								html += "<td class='text-right'>" + pendapatanku + "</td>";
								html += "<td class='text-right'></td>";
								html += "</tr>";
							});

							html += "</table>";
							html += "<table>";
							html += "<tr><td colspan='3'> <b>Sub Total</b></td><td class='text-right'>" + formatIDR(total_produksi_penumpang) + "</td><td class='text-right'>" + formatIDR(total_pendapatan_penumpang) + "</td><td></td></tr>";

							html += "<tr><th colspan='6'>2. KENDARAAN</th><tr>";
							total_produksi_kendaraan = 0;
							total_pendapatan_kendaraan = 0;

							angka = 65;

							$.each(json.kendaraan, function(i, item) {
								$("#master_tabel").show();
								total_produksi_kendaraan += Number(item.produksi);
								total_pendapatan_kendaraan += Number(item.insurance_fee);

								hargaku = "";
								produksiku = 0;
								pendapatanku = 0;

								if (item.harga != null) {
									hargaku = formatIDR(item.harga)
								}

								if (item.produksi != null) {
									produksiku = formatIDR(item.produksi)
								}

								if (item.insurance_fee != null) {
									pendapatanku = formatIDR(item.insurance_fee)
								}

								html += "<tr>";
								html += "<td class='text-center'>" + String.fromCharCode(angka++).toLowerCase() + "</td>";
								html += "<td class='text-left'>" + item.golongan + "</td>";
								html += "<td class='text-right'>" + hargaku + "</td>";
								html += "<td class='text-right'>" + produksiku + "</td>";
								html += "<td class='text-right'>" + pendapatanku + "</td>";
								html += "<td class='text-right'></td>";
								html += "</tr>";
							});

							jumlah_produksi = Number(total_produksi_penumpang + total_produksi_kendaraan);
							jumlah_pendapatan = Number(total_pendapatan_penumpang + total_pendapatan_kendaraan);

							html += "<tr style='font-size:1em;'><td colspan='3'> <b>Sub Total</b></td><td class='text-right'>" + formatIDR(total_produksi_kendaraan) + "</td><td class='text-right'>" + formatIDR(total_pendapatan_kendaraan) + "</td><td></td></tr>";

							html += "<tr><td colspan='3'> <b>TOTAL JUMLAH (Penumpang + Kendaraan)</b></td><td class='text-right'>" + formatIDR(jumlah_produksi) + "</td><td class='text-right'>" + formatIDR(jumlah_pendapatan) + "</td><td></td></tr>";
							html += "</table>";

							$("#tr").html(html);

							var pelabuhan = ": " + json.pelabuhan;
							var cabangku = ": " + json.cabang;
							var status_approve = json.status_approve;
							var shiftku = ": " + json.shift;
							var tanggalku = ": " + $.datepicker.formatDate("d M yy", new Date($('#datefrom').val())) + " - " + $.datepicker.formatDate("d M yy", new Date($('#dateto').val()));

							var team_name = ": " + json.regu;
							var ticketTypeku = ": " + json.ticketTypeku;

							$("#status_approve").html(status_approve);
							$("#cabangku").html(cabangku);
							$("#pelabuhanku").html(pelabuhan);
							$("#shiftku").html(shiftku);
							$("#tanggalku").html(tanggalku);
							$("#reguku").html(team_name);
							$("#lintasanku").html(": " + json.lintasan);
							$( "#ticketTypeku" ).html(ticketTypeku);
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