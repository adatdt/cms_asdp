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
								<div class="col-md-3" style="padding-right: 0px;">
									<div class="form-group">
										<div class="input-group">
											<span class="input-group-addon">Pelabuhan</span>
											<select id="port" class="form-control js-data-example-ajax select2" dir="">
												<option value="">Semua</option>
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
											<div class="input-group-addon">SHIFT</div>
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
							<span id="judul_header"></span></span>
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
						<td style="border-right: none !important;width: 15%">TANGGAL</td>
						<td style="border-left: none !important;"><span id="tanggalku"></span></td>
					</tr>
					<tr>
						<td style="border-right: none !important;">SHIFT</td>
						<td style="border-left: none !important;"><span id="shiftku"></span></td>
						<td style="border-right: none !important;">JAM</td>
						<td style="border-left: none !important;"><span id="jamku"></span></td>
					</tr>
					<tr>
						<td style="border-right: none !important;">STATUS</td>
						<td style="border-left: none !important;">: <b><span id="status_approve"></span></b></td>
						<td style="border-right: none !important;"></td>
						<td style="border-left: none !important;"></td>
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

		$("#cari").on("click",function(e){
			$(this).button('loading');
			e.preventDefault();

			$.ajax({
				type: "GET",
				url: "<?php echo site_url('laporan/rekap_penagihan_kapal/detail') ?>",
				dataType: 'json',
				data: { 
					port: $("#port").val(),
					datefrom: $("#datefrom").val(),
					dateto: $("#dateto").val(),
					shift: $("#shift").val(),
				},

			success: function(json) {
				$("#cari").button('reset');
				if (json.code == 200) {
					html = "<table style='tabel tabel-no-border'>";
					html += "<tr>";
					html += "<td class='text-center' style='width:5%'> No </td>";
					html += "<td class='text-center' style='width:29%'> NAMA PERUSAHAAN</td>";
					html += "<td class='text-center' style='width:23%'> NAMA KAPAL </td>";
					html += "<td class='text-center'> JLH<br>TRIP </td>";
					html += "<td class='text-center'> Penumpang </td>";
					html += "<td class='text-center'> Kendaraan</td>";
					html += "<td class='text-center'> JUMLAH</td>";
					html += "</tr>";
					angka = 1;

					jumlah_qty = 0;
					penumpang_bawah = 0;
					kendaraan_bawah = 0;

					$.each(json.data, function(i, item) {
						jumlah_qty += Number(item.qty);
						penumpang_bawah += Number(item.penumpang);
						kendaraan_bawah += Number(item.vehicle);

						angka_penumpang = item.penumpang;
						angka_kendaraan = item.vehicle;

						jumlah_angka = Number(angka_penumpang)+Number(angka_kendaraan);

						jumlah_penumpang = "-";
						jumlah_kendaraan = "-";
						jumlah_kanan = "-";
						ship_company = "-";

						if (item.penumpang != 0) {jumlah_penumpang = formatIDR(item.penumpang)};
						if (item.company != null) {ship_company = item.company};
						if (item.penumpang+item.vehicle != 0) {jumlah_kanan = formatIDR(jumlah_angka)};
						if (item.vehicle != 0) {jumlah_kendaraan = formatIDR(item.vehicle)};

						$("#master_tabel").show();
						html += "<tr>";
						html += "<td class='text-center'>" + angka++ + "</td>";
						html += "<td class='text-left'>" + ship_company + "</td>";
						html += "<td class='text-left'>" + item.ship_name + "</td>";
						html += "<td class='text-center'>" + item.qty + "</td>";
						html += "<td class='text-right'>" + jumlah_penumpang + "</td>";
						html += "<td class='text-right'>" + jumlah_kendaraan + "</td>";
						html += "<td class='text-right'>" + jumlah_kanan + "</td>";
						html += "</tr>";
					});

					html += "<td class='text-center' colspan='3'><b>JUMLAH</b></td><td class='text-center'>"+jumlah_qty+"</td><td class='text-right'>"+formatIDR(penumpang_bawah)+"</td><td class='text-right'>"+formatIDR(kendaraan_bawah)+"</td><td class='text-right'>"+formatIDR(penumpang_bawah+kendaraan_bawah)+"</td></tr>";

					html += "</table>";
					$( "#tr" ).html(html);
					var cabangku = ": " + $('#port').find(":selected").text();
					var shiftku = ": " + $('#shift').find(":selected").text();
					var jamku = ": " + json.header.shift_time;

					var tanggalku = ": " + $.datepicker.formatDate("d M yy", new Date($('#datefrom').val())) + " - " + $.datepicker.formatDate("d M yy", new Date($('#dateto').val()));

					var judul_header = json.header.title;
					var status_approve = json.status_approve;

					$( "#judul_header" ).html(judul_header);
					$( "#status_approve" ).html(status_approve);

					$( "#pelabuhanku" ).html(cabangku);
					$( "#shiftku" ).html(shiftku);
					$( "#jamku" ).html(jamku);
					$( "#tanggalku" ).html(tanggalku);
				}else{
					toastr.warning(json.message, 'Peringatan');
					document.getElementById('master_tabel').style.display = 'none';
				}
			},

			error: function(result) {
				alert('error');
			},

			complete: function(a,b){
				if (a.status == 200) {
					port = a.responseJSON.get.port;
					datefrom = a.responseJSON.get.datefrom;
					dateto = a.responseJSON.get.dateto;
					shift = a.responseJSON.get.shift;

					$("#printerkita").on("click",function(e){
						window.location = "<?php echo site_url('laporan/rekap_penagihan_kapal/get_pdf?port=') ?>"+port+
						"&datefrom="+datefrom+
						"&dateto=" +dateto+
						"&shift="+shift
					});

					$("#excelkita").on("click",function(e){
						window.location = "<?php echo site_url('laporan/rekap_penagihan_kapal/get_excel?port=') ?>"+port+
						"&datefrom="+datefrom+
						"&dateto=" +dateto+
						"&shift="+shift
					});

				}
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