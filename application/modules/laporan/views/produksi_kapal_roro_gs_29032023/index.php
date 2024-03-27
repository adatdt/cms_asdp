<style type="text/css">
	.judul-tabel-atas tr{
		border-collapse: collapse;
	}

	.tabel {
		border-collapse: collapse;
	}
	.tabel th, .tabel td {
		padding: 5px 5px;
		border: 1px solid #000;
		/* white-space:nowrap; */
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
								<div class="col-md-3" style="padding-right: 0px;">
									<div class="form-group">
										<div class="input-group">
											<span class="input-group-addon">Pelabuhan</span>
											<select id="port" class="form-control js-data-example-ajax select2" dir="">
												<option value="">Pilih</option>
												<?php foreach ($port as $key => $value) { ?>
													<option value="<?=$this->enc->encode($value->id) ?>"><?=$value->name ?></option>
												<?php } ?>
											</select>
										</div>
									</div>
								</div>
								<div class="col-md-5" style="padding-right: 0px;">
									<div class="form-group">
										<div class="input-group">
											<span class="input-group-addon">Tanggal Shift</span>
											<input type="text" autocomplete="off" id="datefrom" class="form-control" value="<?php echo date('Y-m-d'); ?>"></input>
											<div class="input-group-addon">s/d</div>
											<input type="text" autocomplete="off" id="dateto" class="form-control" value="<?php echo date('Y-m-d'); ?>"></input>
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
													<option value="<?=$this->enc->encode($value->id) ?>"><?=$value->shift_name ?></option>
												<?php } ?>
											</select>
										</div>
									</div>
								</div>
								<div class="col-md-2" style="padding-left: 5px;">
									<div class="form-group">
										<div class="input-group">
											<button type="button" class="btn btn-danger" id="cari" data-loading-text="<i class='fa fa-spinner fa-spin'></i> Mencari...">Cari</button>
										</div>
									</div>
								</div>
							</div>
						</div>

					<div id="ini_replace"></div>

					<div id="master_tabel" style="display: none;">

						<button id="printerkita" class="btn btn-sm btn-default"><i class="fa fa-file-pdf-o" style="color: #ea5460"></i> PDF</button>

						<button id="excelkita" class="btn btn-sm btn-default"><i class="fa fa-file-excel-o" style="color: #ea5460"></i> EXCEL</button><br><br>

						<table class="tabel full-width" align="center"> 
							<tr>
								<td rowspan="4" class="no-border-right" style="width: 10%">
									<img src="<?php echo base_url();?>assets/img/asdp-logo2.jpg" style="width:100px; height: auto"> 
								</td>
								<td rowspan="4" class="center bold" style="width: 60%;font-size: 14pt; line-height: 1.5">
									LAPORAN PRODUKSI KAPAL RO-RO GS</span>
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
								<td style="border-right: none !important;width: 15%">PELABUHAN</td>
								<td style="border-left: none !important;width: 35%"><span id="pelabuhanku"></span></td>
								<td style="border-right: none !important;">TANGGAL</td>
								<td style="border-left: none !important;"><span id="tanggalku"></td>
							</tr>
							<tr>
								<td style="border-right: none !important;">REGU</td>
								<td style="border-left: none !important;"><span id="reguku"></span></td>
								<td style="border-right: none !important;">JAM</td>
								<td style="border-left: none !important;"><span id="jamku"></span></td>
							</tr>
							<tr>
								<td style="border-right: none !important;">SHIFT</td>
								<td style="border-left: none !important;"><span id="shiftku"></span> </td>
								<td style="border-right: none !important;"></td>
								<td style="border-left: none !important;"></td>
							</tr>
						</table>

							<br>
							<div style="width: 100%; overflow-x:scroll;">
								<table class="tabel full-width">
									<tbody id="tr"></tbody>
								</table>
							</div>
						</div>
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
			shift = $("#shift").val();
			// ship_class = $("#ship_class").val();
			pelabuhanku = $('#port').find(":selected").text();
			shiftku = $('#shift').find(":selected").text();

			window.location = "<?php echo site_url('laporan/produksi_kapal_roro_gs/get_pdf?port=') ?>"+port+
			"&datefrom=" +datefrom+
			"&dateto=" +dateto+
			"&shift="+shift+
			// "&ship_class="+ship_class+
			"&pelabuhanku="+pelabuhanku+
			"&shiftku="+shiftku
		});

		$("#excelkita").on("click",function(e){
			port = $("#port").val();
			datefrom = $("#datefrom").val();
			dateto = $("#dateto").val();
			shift = $("#shift").val();
			// ship_class = $("#ship_class").val();
			pelabuhanku = $('#port').find(":selected").text();
			shiftku = $('#shift').find(":selected").text();

			window.location = "<?php echo site_url('laporan/produksi_kapal_roro_gs/get_excel?port=') ?>"+port+
			"&datefrom=" +datefrom+
			"&dateto=" +dateto+
			"&shift="+shift+
			// "&ship_class="+ship_class
			"&pelabuhanku="+pelabuhanku+
			"&shiftku="+shiftku
		});

		$("#cari").on("click",function(e){
			$(this).button('loading');
			e.preventDefault();

			$.ajax({
				type: "POST",
				url: "<?php echo site_url('laporan/produksi_kapal_roro_gs/detail') ?>",
				dataType: 'json',
				data: { 
					port: $("#port").val(),
					datefrom: $("#datefrom").val(),
					dateto: $("#dateto").val(),
					shift: $("#shift").val(),
					// ship_class: $("#ship_class").val(),
				},

				success: function(json) {
					$("#cari").button('reset');
					// console.log(json)
					if (json.code == 200) {
						html = "<table class='table table-no-border'>";
						html += "<tr>";
						html += "<td class='text-center' rowspan='2'> NO </td>";
						html += "<td class='text-center' rowspan='2'> NAMA PERUSAHAAN </td>";
						html += "<td class='text-center' rowspan='2'> NAMA KAPAL </td>";
						html += "<td class='text-center' rowspan='2'> GRT </td>";
						html += "<td class='text-center' rowspan='2'> TIBA </td>";
						html += "<td class='text-center' rowspan='2'> BRKT </td>";
						html += "<td class='text-center' rowspan='2'> DURASI </td>";
						html += "<td class='text-center' rowspan='2'> DRMG </td>";
						html += "<td class='text-center' rowspan='2'> CALL </td>";
						html += "<td class='text-center' colspan='3'> PENUMPANG </td>";
						html += "<td class='text-center' rowspan='2'> JML PNP </td>";
						html += "<td class='text-center' colspan='12'> KENDARAAN GOLONGAN </td>";
						html += "<td class='text-center' rowspan='2'> JML KND </td>";
						html += "<td class='text-center' rowspan='2'> TOTAL (Rp)</td>";
						html += "</tr>";

						html += "<tr>";
						html += "<td class='text-center'> DEWASA </td>";
						html += "<td class='text-center'> ANAK </td>";
						html += "<td class='text-center'> BAYI </td>";
						html += "<td class='text-center'> I </td>";
						html += "<td class='text-center'> II </td>";
						html += "<td class='text-center'> III </td>";
						html += "<td class='text-center'> IVA </td>";
						html += "<td class='text-center'> IVB </td>";
						html += "<td class='text-center'> VA </td>";
						html += "<td class='text-center'> VB </td>";
						html += "<td class='text-center'> VIA </td>";
						html += "<td class='text-center'> VIB </td>";
						html += "<td class='text-center'> VII </td>";
						html += "<td class='text-center'> VIII </td>";
						html += "<td class='text-center'> IX </td>";
						html += "</tr>";
						angka = 1;

						total_amount = 0;
						total_bayi=0;
						total_dewasa = total_anak = total_pnp = total_knd = 0;
						total_gol1 = total_gol2 = total_gol3 = total_gol4A = total_gol4B = total_gol5A = total_gol5B = total_gol6A = total_gol6B = total_gol7 = total_gol8 = total_gol9 = 0;

						$.each(json.data.all_data, function(i, item) {
							total_dewasa += item.dewasa;
							total_anak += item.anak;
							total_bayi += item.bayi;
							total_pnp += item.totalP;
							total_gol1 += item.gol1;
							total_gol2 += item.gol2;
							total_gol3 += item.gol3;
							total_gol4A += item.gol4A;
							total_gol4B += item.gol4B;
							total_gol5A += item.gol5A;
							total_gol5B += item.gol5B;
							total_gol6A += item.gol6A;
							total_gol6B += item.gol6B;
							total_gol7 += item.gol7;
							total_gol8 += item.gol8;
							total_gol9 += item.gol9;
							total_knd += item.totalK;
							total_amount += item.amount;
							$("#master_tabel").show();
							company = "-";

							if (item.company != null) {
								company = item.company;
							}

							html += "<tr>";
							html += "<td class='text-center'>" + angka++ + "</td>";
							html += "<td class='text-left'>" + company + "</td>";
							html += "<td class='text-left'>" + item.ship + "</td>";
							html += "<td class='text-right'>" + format_thousand(item.ship_grt) + "</td>";
							html += "<td class='text-center'>" + item.docking_date + "<br>" + item.docking_time + "</td>";
							html += "<td class='text-center'>" + item.sail_date + "<br>" + item.sail_time + "</td>";
							html += "<td class='text-center'>" + item.duration + "</td>";
							html += "<td class='text-center'>" + item.dermaga + "</td>";
							html += "<td class='text-right'>" + item.call + "</td>";
							html += "<td class='text-right'>" + format_thousand(item.dewasa) + "</td>";
							html += "<td class='text-right'>" + format_thousand(item.anak) + "</td>";
							html += "<td class='text-right'>" + format_thousand(item.bayi) + "</td>";
							html += "<td class='text-right'>" + format_thousand(item.totalP) + "</td>";
							html += "<td class='text-right'>" + format_thousand(item.gol1) + "</td>";
							html += "<td class='text-right'>" + format_thousand(item.gol2) + "</td>";
							html += "<td class='text-right'>" + format_thousand(item.gol3) + "</td>";
							html += "<td class='text-right'>" + format_thousand(item.gol4A) + "</td>";
							html += "<td class='text-right'>" + format_thousand(item.gol4B) + "</td>";
							html += "<td class='text-right'>" + format_thousand(item.gol5A) + "</td>";
							html += "<td class='text-right'>" + format_thousand(item.gol5B) + "</td>";
							html += "<td class='text-right'>" + format_thousand(item.gol6A) + "</td>";
							html += "<td class='text-right'>" + format_thousand(item.gol6B) + "</td>";
							html += "<td class='text-right'>" + format_thousand(item.gol7) + "</td>";
							html += "<td class='text-right'>" + format_thousand(item.gol8) + "</td>";
							html += "<td class='text-right'>" + format_thousand(item.gol9) + "</td>";
							html += "<td class='text-right'>" + format_thousand(item.totalK) + "</td>";
							html += "<td class='text-right'>" + format_thousand(item.amount) + "</td>";

							html += "</tr>";
						});

						html += "<tr><td colspan='9' class='text-center'><b>JUMLAH</b></td>";
						html += "<td class='text-right'>" + format_thousand(total_dewasa) + "</td>";
						html += "<td class='text-right'>" + format_thousand(total_anak) + "</td>";
						html += "<td class='text-right'>" + format_thousand(total_bayi) + "</td>";
						html += "<td class='text-right'>" + format_thousand(total_pnp) + "</td>";
						html += "<td class='text-right'>" + format_thousand(total_gol1) + "</td>";
						html += "<td class='text-right'>" + format_thousand(total_gol2) + "</td>";
						html += "<td class='text-right'>" + format_thousand(total_gol3) + "</td>";
						html += "<td class='text-right'>" + format_thousand(total_gol4A) + "</td>";
						html += "<td class='text-right'>" + format_thousand(total_gol4B) + "</td>";
						html += "<td class='text-right'>" + format_thousand(total_gol5A) + "</td>";
						html += "<td class='text-right'>" + format_thousand(total_gol5B) + "</td>";
						html += "<td class='text-right'>" + format_thousand(total_gol6A) + "</td>";
						html += "<td class='text-right'>" + format_thousand(total_gol6B) + "</td>";
						html += "<td class='text-right'>" + format_thousand(total_gol7) + "</td>";
						html += "<td class='text-right'>" + format_thousand(total_gol8) + "</td>";
						html += "<td class='text-right'>" + format_thousand(total_gol9) + "</td>";
						html += "<td class='text-right'>" + format_thousand(total_knd) + "</td>";
						html += "<td class='text-right'>" + format_thousand(total_amount) + "</td>";

						html += "</tr>";
						html += "</table>";
						html += "<table>";
						// html += "<tr><td colspan='3'> <b>Sub Total</b></td><td class='text-right'>" +total_produksi_penumpang+ "</td><td class='text-right'>" +total_pendapatan_penumpang+ "</td><td></td></tr>";

						html += "</table>";

						$( "#tr" ).html(html);

						var pelabuhanku = ": " + $('#port').find(":selected").text();
						var shiftku = ": " + $('#shift').find(":selected").text();

						var dateFrom = $.datepicker.formatDate("d M yy", new Date($('#datefrom').val()));
						var dateTo = $.datepicker.formatDate("d M yy", new Date($('#dateto').val()));
						if(dateFrom == dateTo){
							var tanggalku = ": " + dateFrom;
						} else {
							var tanggalku = ": " + dateFrom + " - " + dateTo;
						}
						// var tanggalku = ": " + $.datepicker.formatDate("d M yy", new Date($('#datefrom').val())) + " - " + $.datepicker.formatDate("d M yy", new Date($('#dateto').val()));

						var team_name = ": " + json.regu;
						var jamku = ": " + json.jam;

						$( "#pelabuhanku" ).html(pelabuhanku);
						$( "#shiftku" ).html(shiftku);
						$( "#tanggalku" ).html(tanggalku);
						$( "#reguku" ).html(team_name);
						$( "#jamku" ).html(jamku);
					}else{
						toastr.warning(json.message, 'Peringatan');
						document.getElementById('master_tabel').style.display = 'none';
					}
				},

				error: function(result) {
					$("#cari").button('reset');
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

	function format_thousand(number){
		return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
	}
</script>