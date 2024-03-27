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
				<div class="form-inline">
					<div class="row">
						<div class="col-md-12">
							<div class="input-group select2-bootstrap-prepend">
								<div class="input-group-addon">PELABUHAN </div>
								<select id="port" class="form-control js-data-example-ajax select2" dir="">
									<option value="">All</option>
									<?php foreach ($port as $key => $value) { ?>
										<option value="<?=$this->enc->encode($value->id) ?>"><?=$value->name ?></option>
									<?php } ?>
								</select>
							</div>

							<div class="input-group select2-bootstrap-prepend">
								<div class="input-group-addon">Tanggal </div>
								<input type="text" autocomplete="off" id="datefrom" class="form-control" value="<?php echo date('Y-m-d'); ?>">
								</input>
							</div>

							<div class="input-group select2-bootstrap-prepend">
								<div class="input-group-addon">Kelas Layanan</div>
								<select id="ship_class" class="form-control js-data-example-ajax select2" dir="">
									<option value="">All</option>
									<?php foreach ($class as $key => $value) { ?>
										<option value="<?=$this->enc->encode($value->id) ?>"><?=$value->name ?></option>
									<?php } ?>
								</select>
							</div>

							<div class="input-group select2-bootstrap-prepend">
								<div class="input-group-addon">SHIFT </div>
								<select id="shift" class="form-control js-data-example-ajax select2" dir="">
									<option value="">All</option>
									<?php foreach ($shift as $key => $value) { ?>
										<option value="<?=$this->enc->encode($value->id) ?>"><?=$value->shift_name ?></option>
									<?php } ?>
								</select>
							</div>

							<button type="button" class="btn btn-danger" id="cari" data-loading-text="<i class='fa fa-spinner fa-spin'></i> Mencari...">Cari</button>

						</div>

					</div>
				</div><br>

				<div id="ini_replace"></div>

				<div id="master_tabel" style="display: none">
				<table class="tabel full-width" align="center"> 
					<tr>
						<td rowspan="4" class="no-border-right" style="width: 10%">
							<img src="<?php echo base_url();?>assets/img/asdp-logo2.jpg" style="width:100px; height: auto"> 
						</td>
						<td rowspan="4" class="center bold" style="width: 60%;font-size: 14pt; line-height: 1.5">
							LAPORAN HARIAN PENDAPATAN<br/> TIKET TERPADU TERJUAL PER-SHIFT <span id="kelaskuu"></span>
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

				<table class="tabel-no-border-new full-width" border="0">
					<tr>
						<td style="width: 10%">CABANG</td>
						<td id="cabangku">: </td>
					</tr>
					<tr>
						<td style="width: 10%">PELABUHAN</td>
						<td id="pelabuhanku"></td>
					</tr>
					<tr>
						<td style="width: 10%">LINTASAN</td>
						<td id="lintasanku"></td>
					</tr>
					<tr>
						<td style="width: 10%">SHIFT</td>
						<td id="shiftku"></td>
					</tr>
					<tr>
						<td style="width: 10%">REGU</td>
						<td id="reguku"></td>
					</tr>
					<tr>
						<td style="width: 10%">TANGGAL</td>
						<td id="tanggalku"></td>
					</tr>
				</table>

				<br>

				<table class="tabel full-width">
					<tbody id="tr"></tbody>
				</table>
				<br><br>

				<div class="col col-md-12">
					<div class="col col-md-6">
						<center><span><b>SUPERVISI <span class="spv_regu"></span></b></span></center><br><br>
						<center><span>(............................)</span></center>
					</div>
					<div class="col col-md-6">
						<center><span><b>SUPERVISI USAHA <span class="spv_regu"></span></b></span></center><br><br>
						<center><span>(............................)</span></center>
					</div>
				</div>
				<br><br><br>
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
		})

		$("#cari").on("click",function(e){
			$(this).button('loading');
			e.preventDefault();

			$.ajax({
				type: "POST",
				url: "<?php echo site_url('laporan/pendapatan_harian') ?>",
				dataType: 'json',
				data: { 
            		port: $("#port").val(),
            		tanggal: $("#datefrom").val(),
            		regu: $("#regu").val(),
            		petugas: $("#petugas").val(),
            		shift: $("#shift").val(),
            		ship_class: $("#ship_class").val(),
            	},

            success: function(json) {
            	$("#cari").button('reset');
            	if (json.code == 200) {
            		html = "<table class='table table-no-border'>";
            		html += "<tr>";
            		html += "<td class='text-center no-border-right'> NO </td>";
            		html += "<td class='text-center'> JENIS </td>";
            		html += "<td class='text-center'> TARIF (Rp.) </td>";
            		html += "<td class='text-center'> PRODUKSI (Lbr)</td>";
            		html += "<td class='text-center'> PENDAPATAN (Rp.)</td>";
            		html += "</tr>";
            		html += "<tr><th colspan='5'>1. PENUMPANG</th><tr>";
            		angka = 65;
            		total_produksi_penumpang = 0;
            		total_pendapatan_penumpang = 0;

            		$.each(json.penumpang, function(i, item) {
        				$("#master_tabel").show();
	            		total_produksi_penumpang += Number(item.produksi);
	            		total_pendapatan_penumpang += Number(item.pendapatan);
	            		spv_regu = item.team_name;
	            		regukita = ": " + item.team_name;

            			html += "<tr>";
	            		html += "<td class='text-center'>" + String.fromCharCode(angka++).toLowerCase() + "</td>";
	            		html += "<td class='text-left'>" + item.golongan + "</td>";
	            		html += "<td class='text-right'>" + formatIDR(item.harga) + "</td>";
	            		html += "<td class='text-right'>" + formatIDR(item.produksi) + "</td>";
	            		html += "<td class='text-right'>" + formatIDR(item.pendapatan) + "</td>";
	            		html += "</tr>";
            		});

            		html += "</table>";
            		html += "<table>";
            		html += "<tr><td colspan='3'> <b>Sub Total</b></td><td class='text-right'>" +formatIDR(total_produksi_penumpang)+ "</td><td class='text-right'>" +formatIDR(total_pendapatan_penumpang)+ "</td></tr>";

            		html += "<tr><th colspan='5'>2. KENDARAAN</th><tr>";
            		total_produksi_kendaraan = 0;
            		total_pendapatan_kendaraan = 0;

            		angka = 65;

            		$.each(json.kendaraan, function(i, item) {
            			$("#master_tabel").show();
            			total_produksi_kendaraan += Number(item.produksi);
            			total_pendapatan_kendaraan += Number(item.pendapatan);

            			html += "<tr>";
            			html += "<td class='text-center'>" + String.fromCharCode(angka++).toLowerCase() + "</td>";
            			html += "<td class='text-left'>" + item.golongan + "</td>";
            			html += "<td class='text-right'>" + formatIDR(item.harga) + "</td>";
            			html += "<td class='text-right'>" + formatIDR(item.produksi) + "</td>";
            			html += "<td class='text-right'>" + formatIDR(item.pendapatan) + "</td>";
            			html += "</tr>";
            		});

            		jumlah_produksi = Number(total_produksi_penumpang+total_produksi_kendaraan);
            		jumlah_pendapatan = Number(total_pendapatan_penumpang+total_pendapatan_kendaraan);

            		html += "<tr style='font-size:1em;'><td colspan='3'> <b>Sub Total</b></td><td class='text-right'>" +formatIDR(total_produksi_kendaraan)+ "</td><td class='text-right'>" +formatIDR(total_pendapatan_kendaraan)+ "</td></tr>";

            		html += "<tr><td colspan='3'> <b>TOTAL JUMLAH</b></td><td class='text-right'>" +formatIDR(jumlah_produksi)+ "</td><td class='text-right'>" +formatIDR(jumlah_pendapatan)+ "</td></tr>";
            		html += "</table>";

            		$( "#tr" ).html(html);

            		var cabangku = ": " +$('#port').find(":selected").text();
            		var shiftku = ": " + $('#shift').find(":selected").text();
            		var reguku = ": " + $('#regu').find(":selected").text();
            		var petugasku = ": " + $('#petugas').find(":selected").text();
            		var tanggalku = ": " + $('#datefrom').val();

            		var kelaskuu = $('#ship_class').find(":selected").text();
            		var origin = json.lintasan.origin;
            		var destination = json.lintasan.destination;

            		$( "#cabangku" ).html(cabangku);
            		$( "#pelabuhanku" ).html(cabangku);
            		$( "#shiftku" ).html(shiftku);
            		$( "#reguku" ).html(regukita);
            		$( ".spv_regu" ).html(spv_regu.toUpperCase());
            		$( "#petugasku" ).html(petugasku);
            		$( "#tanggalku" ).html(tanggalku);

            		$( "#lintasanku" ).html(": " + origin.toUpperCase() + " " +kelaskuu.toUpperCase() + " - " + destination.toUpperCase() + " " + kelaskuu.toUpperCase());
            	}else{
            		toastr.warning(json.message, 'Peringatan');
            		document.getElementById('master_tabel').style.display = 'none';
            	}

            	// $.each(d.tunai, function(i, item) {
            	// 	console.log(item.golongan);

            	// 	// for (i = 0; i < item.length; i++) {
            	// 	// 	$('#ini_replace').html(item.golongan);
            	// 	// 	console.log(1)
            	// 	// }
            	// });

            	// console.log(d.tunai);

            	// $.each(result, function(i, item) {
            	// 	alert(result[i].cash);
            	// });​
            	// $( "#ini_replace" ).replaceWith( "#joss" );
            },

            error: function(result) {
            	alert('error');
            }

        });
	});

		setTimeout(function() {
			$('.menu-toggler').trigger('click');
		}, 1);
	});
</script>