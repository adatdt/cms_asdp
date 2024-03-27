<style type="text/css">
  .tabel {
		border-collapse: collapse;
	}
	.tabel th, .tabel td {
		padding: 5px 5px;
		border: 1px solid #000;
	}
	.tabel th {
		font-weight: normal;
	}
	.tabel-no-border tr {
		border: 1px solid #000;
	}

	.tabel-no-border th, .tabel-no-border td {
		padding: 5px 5px;
		border: 0px;
	}     
	.tabel-no-border-new tr {
		border: 0px solid #000;
	}

	.tabel-no-border-new th, .tabel-no-border td {
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
													<option value="<?=$this->enc->encode($value->id) ?>"><?=$value->name ?></option>
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

					<!-- <div class="row">
						<div class="col-md-12">
							<div class="table-toolbar" style="margin-bottom: 0px">
								<div class="row">
									<div class="col-md-3" style="padding-left: 30px;padding-right: 8px;">
										<div class="form-group">
											<div class="input-group">
												<div class="input-group-addon">SHIFT</div>
												<select id="shift" class="form-control select2" dir="">
													<option value="">Semua</option>
													<?php foreach ($shift as $key => $value) { ?>
														<option value="<?=$this->enc->encode($value->id) ?>"><?=$value->shift_name ?></option>
													<?php } ?>
												</select>
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
						</div> -->
					<!-- </div> -->
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
							<img src="<?php echo base_url();?>assets/img/asdp-logo2.jpg" style="width:100px; height: auto"> 
						</td>
						<td rowspan="4" class="center bold" style="width: 60%;font-size: 14pt; line-height: 1.5">
							LAPORAN REKONSILIASI SHARING REVENUE</span>
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
						<td style="border-right: none !important;">PELABUHAN</td>
						<td style="border-left: none !important;"><span id="print_port"></span></td>
						<td style="border-right: none !important;">TANGGAL</td>
						<td style="border-left: none !important;"><span id="print_date"></span></td>
						<td style="border-right: none !important;">KELAS LAYANAN</td>
						<td style="border-left: none !important;"><span id="sc_name"></span></td>
					</tr>
				</table>

				<br>

				<table class="tabel full-width">
					<tbody id="tr"></tbody>
				</table>
			</div>
			<br></div>
		</div>        
	</div>
</div>
<script type="text/javascript">
	jQuery(document).ready(function () {

		$('#datefrom').datepicker({
			format: 'yyyy-mm-dd',
			changeMonth: true,
			changeYear: true,
			autoclose: true,
			endDate: new Date(),
		}).on('changeDate',function(e) {
			$('#dateto').datepicker('setStartDate', e.date)
		});

		$('#dateto').datepicker({
			format: 'yyyy-mm-dd',
			changeMonth: true,
			changeYear: true,
			autoclose: true,
			startDate: $('#datefrom').val(),
			endDate: new Date(),
		}).on('changeDate',function(e) {
			$('#datefrom').datepicker('setEndDate', e.date)
		});

		$("#printerkita").on("click",function(e){
			port = $("#port").val();
			datefrom = $("#datefrom").val();
			dateto = $("#dateto").val();
			ship_class = $("#ship_class").val();

			window.location = "<?php echo site_url('laporan/rekonsiliasi/get_pdf?port=') ?>"+port+
			"&datefrom=" +datefrom+
			"&dateto=" +dateto+
			"&ship_class=" +ship_class+
			"&port_name="+$('#port').find(":selected").text()+
			"&ship_classku="+$('#ship_class').find(":selected").text();
		});

		$("#excelkita").on("click",function(e){
			port = $("#port").val();
			datefrom = $("#datefrom").val();
			dateto = $("#dateto").val();
			ship_class = $("#ship_class").val();

			window.location = "<?php echo site_url('laporan/rekonsiliasi/get_excel?port=') ?>"+port+
			"&datefrom=" +datefrom+
			"&dateto=" +dateto+
			"&ship_class=" +ship_class+
			"&port_name="+$('#port').find(":selected").text()+
			"&ship_classku="+$('#ship_class').find(":selected").text();
		});

		$("#cari").on("click",function(e){
			$(this).button('loading');
			e.preventDefault();

			$.ajax({
				type: "POST",
				url: "<?php echo site_url('laporan/rekonsiliasi') ?>",
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
					html = "<table style='tabel tabel-no-border'>";
					html += "<tr><th colspan='5'>TIPE PENJUALAN : TUNAI</th><tr>";
					html += "<tr>";
					html += "<td class='text-center'> No </td>";
					html += "<td class='text-center'> Jenis </td>";
					html += "<td class='text-center'> Tarif Pas Pelabuhan<br>(KD88 Tahun 2017) </td>";
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

						if (item.entrance_fee != null) {
							hargaku = formatIDR(item.entrance_fee)
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

					html += "<tr><td colspan='3'> <b>Sub Total</b></td><td class='text-right'>" +formatIDR(total_produksi_cash)+ "</td><td class='text-right'>" +formatIDR(total_pendapatan_cash)+ "</td></tr>";

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

						if (item.entrance_fee != null) {
							hargaku = formatIDR(item.entrance_fee)
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

					html += "<tr><td colspan='3'> <b>Sub Total</b></td><td class='text-right'>" +formatIDR(total_produksi_cashless)+ "</td><td class='text-right'>" +formatIDR(total_pendapatan_cashless)+ "</td></tr>";

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

						if (item.entrance_fee != null) {
							hargaku = formatIDR(item.entrance_fee)
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

					html += "<tr><td colspan='3'> <b>Sub Total</b></td><td class='text-right'>" +formatIDR(total_produksi_online)+ "</td><td class='text-right'>" +formatIDR(total_pendapatan_online)+ "</td></tr>";

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

						if (item.entrance_fee != null) {
							hargaku = formatIDR(item.entrance_fee)
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

					html += "<tr><td colspan='3'> <b>Sub Total</b></td><td class='text-right'>" +formatIDR(total_produksi_ifcs)+ "</td><td class='text-right'>" +formatIDR(total_pendapatan_ifcs)+ "</td></tr>";

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

						if (item.entrance_fee != null) {
							hargaku = formatIDR(item.entrance_fee)
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

					html += "<tr><td colspan='3'> <b>Sub Total</b></td><td class='text-right'>" +formatIDR(total_produksi_b2b)+ "</td><td class='text-right'>" +formatIDR(total_pendapatan_b2b)+ "</td></tr>";
					
					jumlah_produksi = Number(total_produksi_cash+total_produksi_cashless+total_produksi_online+total_produksi_ifcs+total_produksi_b2b);
					jumlah_pendapatan = Number(total_pendapatan_cash+total_pendapatan_cashless+total_pendapatan_online+total_pendapatan_ifcs+total_pendapatan_b2b);
					
					html += "<tr><td colspan='3'> <b>TOTAL JUMLAH (Tunai + Cashless + Online + IFCS + B2B)</b></td><td class='text-right'>" +formatIDR(jumlah_produksi)+ "</td><td class='text-right'>" +formatIDR(jumlah_pendapatan)+ "</td></tr>";

					html += "</table>";
					$( "#tr" ).html(html);

					var sc_name = ": " + $('#ship_class').find(":selected").text();
					var port_name = ": " + $('#port').find(":selected").text();
					var date_print = ": " + $.datepicker.formatDate("d M yy", new Date($('#datefrom').val())) + " - " + $.datepicker.formatDate("d M yy", new Date($('#dateto').val()));

					$( "#print_port" ).html(port_name);
					$( "#print_date" ).html(date_print);
					$( "#sc_name" ).html(sc_name);
				}else{
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