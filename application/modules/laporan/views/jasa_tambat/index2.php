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

					<div id="master_tabel" style="display: none">

						<button id="printerkita" class="btn btn-sm btn-default"><i class="fa fa-file-pdf-o" style="color: #ea5460"></i> PDF</button>

						<button id="excelkita" class="btn btn-sm btn-default"><i class="fa fa-file-excel-o" style="color: #ea5460"></i> EXCEL</button><br><br>

						<table class="tabel full-width" align="center"> 
							<tr>
								<td rowspan="4" class="no-border-right" style="width: 10%">
									<img src="<?php echo base_url();?>assets/img/asdp-logo2.jpg" style="width:100px; height: auto"> 
								</td>
								<td rowspan="4" class="center bold" style="width: 60%;font-size: 14pt; line-height: 1.5">
									LAPORAN PENDAPATAN JASA TAMBAT KAPAL PER - SHIFT</span>
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

							<table class="tabel full-width">
								<tbody id="tr"></tbody>
							</table>
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

			window.location = "<?php echo site_url('laporan/jasa_tambat/get_pdf?port=') ?>"+port+
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

			window.location = "<?php echo site_url('laporan/jasa_tambat/get_excel?port=') ?>"+port+
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
				url: "<?php echo site_url('laporan/jasa_tambat/detail') ?>",
				dataType: 'json',
				data: { 
					port: $("#port").val(),
					datefrom: $("#datefrom").val(),
					dateto: $("#dateto").val(),
					shift: $("#shift").val(),
					ship_class: $("#ship_class").val(),
				},

			success: function(json) {
				$("#cari").button('reset');
				console.log(json)
				if (json.code == 200) {
					html = "<table class='table table-no-border'>";
					html += "<tr>";
					html += "<td class='text-center'> NO </td>";
					html += "<td class='text-center'> NAMA PERUSAHAAN </td>";
					html += "<td class='text-center'> NAMA KAPAL </td>";
					html += "<td class='text-center'> TRIP </td>";

					varkosong = {};
					$.each(json.data.dock, function(i, item) {
						html += "<td class='text-center'>"+ item.name+"</td>";
						varkosong[item.name] = 0;
					});

					html += "<td class='text-center'> JUMLAH</td>";
					html += "</tr>";
					angka = 1;

					total_trip = 0;
					total_duit_bawah = 0;

					$.each(json.data.all_data, function(i, item) {
						$("#master_tabel").show();
						total_trip += Number(item.trip);

						company = "-";

						if (item.company != null) {
							company = item.company;
						}

						html += "<tr>";
						html += "<td class='text-center'>" + angka++ + "</td>";
						html += "<td class='text-left'>" + company + "</td>";
						html += "<td class='text-left'>" + item.ship_name + "</td>";
						html += "<td class='text-center'>" + item.trip + "</td>";

						data_dock = jQuery.parseJSON(item.dock);
						// keys_data= Object.keys(data_dock);
						// console.log(keys_data)

						penjumlahan = 0;
						jumlah_kanan = 0;

						item_dock = json.data.dock;
						// item_dock.total = [];
						// console.log(json.data.dock);

						// $.each(json.data.dock, function(i, dd) {
						// 	console.log(dd.name)
						// })

						// for(dd in json.data.dock){
						// 	// console.log(dd.name)
						// }
						// tmp = json.data.dock.name;
						for (a in item_dock){
							// console.log(item_dock[a].id)
							
							// tmp[data_dock[item_dock[a].name]] = {};

							if(data_dock[item_dock[a].id] != undefined){
								text_json = data_dock[item_dock[a].id];
								exp = text_json.split("-");
								jumlah_kanan += Number(exp[1]);

								ndermaga = item_dock[a].name;
								varkosong[ndermaga] += Number(exp[1]);

								// console.log(varkosong[ndermaga]);

								// tmp[data_dock[item_dock[a].name]] += Number(exp[1]);
								// penjumlahan += Number(exp[1]);

								// json.data.dock[i].push({'total' : penjumlahan});

								html += '<td class="right">'+formatIDR(exp[1])+'</td>';
							}else{
								html += '<td class="right">-</td>';
								// item_dock[a].total = 0;
							}

							// item_dock[a].total = total
							// console.log(total)

							// item_dock[a].total = total;

							// $.each(keys_data, function(k, kitem){
							// 	// console.log(keys_data)

							// 	if (kitem == item_dock[a].id) {
							// 		// console.log(kitem);
							// 		html += '<td class="right">1</td>';
							// 	}
							// })

							// console.log(a)

							// if(keys_data == item_dock[a].id) {
							// 	// json.data.dock[i] = new Array();
							// 	text_json = data_dock[item_dock[a].id];
							// 	exp = text_json.split("-");

							// 	// total = isNaN(exp[1]);

							// 	// console.log(exp[0]);
							// 	// console.log(exp[1]);
							// 	// console.log(exp[3]);

							// 	// let penjumlahan;
							// 	// penjumlahan += parseInt(exp[1]);
							// 	// json_penjumlahan = [{'total' : penjumlahan}];

							// 	// console.log(penjumlahan);

							// 	// json.data.dock[i].push({'total' : penjumlahan});
							// 	jumlah_kanan += Number(exp[1]);
							// 	total[exp[0]] += Number(exp[1]);

							// 	// json.data.dock[z].total += total ? total : 0;
							// 	// z++;
							// 	// alert(angka_dijumlah)

							// 	json.data.dock[a].total = total;	
							// 	// html += '<td class="right">'+formatIDR(exp[1])+'</td>';
							// } else {
							// 	total[a] = 0;
							// 	// json.data.dock[i].total = "-";

							// 	html += '<td class="right">'+total+'</td>';
							// }
// console.log(total);
						}
						// $.each(json.data.dock, function(i, item_dock) {
						// 	// json.data.dock[i].total = 0;

						// 	if(keys_data == item_dock.id) {
						// 		text_json = data_dock[ item_dock.id];
						// 		exp = text_json.split("-");
						// 		json.data.dock[i].total += Number(exp[1]);
						// 		// console.log(exp[1]);

						// 		// json.data.dock[i].total += Number(exp[1]);
						// 		jumlah_kanan += Number(exp[1]);
						// 		// alert(angka_dijumlah)

						// 	    html += '<td class="right">'+formatIDR(exp[1])+'</td>';
						// 	} else {
						// 		// json.data.dock[i].total = "-";

						// 	    html += '<td class="right">-</td>';
						// 	}
						// });

						// console.log(item_dock);

						total_duit_bawah += jumlah_kanan;

						html += '<td class="right">'+formatIDR(jumlah_kanan)+'</td>';

						html += "</tr>";
					});

					html += "<tr><td colspan='3' class='text-center'><b>JUMLAH</b></td><td class='text-center'>"+total_trip+"</td>";
					// console.log(json.data.dock);
					$.each(item_dock, function(i, item_dock2) {
						// console.log(item_dock2.name);return false;
						html += '<td class="right">'+formatIDR(varkosong[item_dock2.name])+'</td>';
					});
						
					html += '<td class="right">'+formatIDR(total_duit_bawah)+'</td>';

					html += "</tr>";
					html += "</table>";
					html += "<table>";
					// html += "<tr><td colspan='3'> <b>Sub Total</b></td><td class='text-right'>" +total_produksi_penumpang+ "</td><td class='text-right'>" +total_pendapatan_penumpang+ "</td><td></td></tr>";

					html += "</table>";

					$( "#tr" ).html(html);

					var pelabuhanku = ": " + $('#port').find(":selected").text();
					var shiftku = ": " + $('#shift').find(":selected").text();
					var tanggalku = ": " + $.datepicker.formatDate("d M yy", new Date($('#datefrom').val())) + " - " + $.datepicker.formatDate("d M yy", new Date($('#dateto').val()));

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