<!-- Setting CSS bagian header/ kop -->
<style type="text/css">
	.tabel {
		border-collapse: collapse;
	}

	.tabel th,
	.tabel td {
		padding: 5px 5px;
		border: 1px solid #000;
		font-size: 11px;
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

	.no-border-left {
		border-left: none;
	}

	td.border-right {
		border-right: 1px solid #000
	}
</style>
<page backtop="30mm" backbottom="8mm" backleft="7mm" backright="7mm">
	<page_header>
		<table class="tabel full-width" align="center">
			<tr>
				<td rowspan="4" class="no-border-right" style="width: 10%">
					<img src="assets/img/asdp.png" style="width:100px; height: auto">
				</td>
				<td rowspan="4" class="center bold" style="width: 42%;font-size:14px; line-height: 1.5; vertical-align: middle">
					LAPORAN PERBANDINGAN LINEMETER
				</td>
				<td style="width: 15%" class="no-border-right no-border-left">No. Dokumen</td>
				<td style="width: 20%">:</td>
			</tr>
			<tr>
				<td class="no-border-right no-border-left">Revisi</td>
				<td>: </td>
			</tr>
			<tr>
				<td class="no-border-right no-border-left">Berlaku Efektif</td>
				<td>: </td>
			</tr>
			<tr>
				<td class="no-border-right no-border-left">Halaman</td>
				<td>: [[page_cu]] dari [[page_nb]]</td>
			</tr>
		</table>
	</page_header>

	<table class="tabel full-width" align="center" id="cekcontent">
		<tr>
			<td style="border-right: none !important;width: 25%">PELABUHAN</td>
			<td style="border-left: none !important;width: 25%">: <?= $pelabuhan ?></td>
			<td style="border-right: none !important;width: 25%">TANGGAL</td>
			<td style="border-left: none !important;width: 25%">: <?= $tanggal ?></td>
		</tr>
		<tr>
			<td style="border-right: none !important;width: 25%">KETERANGAN</td>
			<td style="border-left: none !important;width: 25%">: <?= $keterangan?></td>
			<td style="border-right: none !important;width: 25%">GOLONGAN</td>
			<td style="border-left: none !important;width: 25%">: <?= $golongan ?></td>
		</tr>
		<tr>
			<td style="border-right: none !important;width: 25%">KELAS LAYANAN</td>
			<td style="border-left: none !important;width: 25%">: <?= $ship_class ?></td>
			<td style="border-right: none !important;width: 25%"></td>
			<td style="border-left: none !important;width: 25%"></td>
		</tr>
		<!-- <tr>
			<td style="border-right: none !important;width: 25%">CABANG</td>
			<td style="border-left: none !important;width: 25%">: <?= $cabang ?></td>
			<td style="border-right: none !important;width: 25%">SHIFT</td>
			<td style="border-left: none !important;width: 25%">: <?= $shift ?></td>
		</tr> -->
		<!-- <tr>
			<td style="border-right: none !important;width: 25%">LINTASAN</td>
			<td style="border-left: none !important;width: 25%">: <?= $lintasan ?></td>
			<td style="border-right: none !important;width: 25%">TANGGAL</td>
			<td style="border-left: none !important;width: 25%">: <?= $tanggal ?></td>
		</tr> -->
	</table>

	<br>


	<table class="tabel full-width" style="width: 100% !important">
		<tr>
			<td style="text-align: center" rowspan="2"> No. </td>
			<td style="text-align: center" rowspan="2"> Tanggal </td>
			<td style="text-align: center" rowspan="2"> Waktu </td>
			<td style="text-align: center" rowspan="2"> Golongan</td>
			<td style="text-align: center" rowspan="2"> Kode Booking</td>
			<td style="text-align: center" colspan="2"> Default Linemeter </td>
			<td style="text-align: center" colspan="2"> Pengisian Linemeter </td>
			<td style="text-align: center" colspan="2"> Realisasi Linemeter </td>
			<td style="text-align: center" rowspan="2"> Keterangan</td>
		</tr>
		<tr>
			<td style="text-align: center"> Panjang </td>
			<td style="text-align: center"> Lebar </td>
			<td style="text-align: center"> Panjang </td>
			<td style="text-align: center"> Lebar </td>
			<td style="text-align: center"> Panjang </td>
			<td style="text-align: center"> Lebar </td>
		</tr>
		<?php
		$angka = 1;
		$lebarD = 0;
		$panjangD = 0;
		$lebarP = 0;
		$panjangP = 0;
		$lebarR = 0;
		$panjangR = 0;

		foreach ($perbandingan as $key => $value) {
			
			if ($value->keterangan == '1') {
				$keterangan = "Overpaid";
			} else if ($value->keterangan == '2') {
				$keterangan = "Underpaid";
			} else (
				$keterangan = "Normal"
			);

			if ($value->name != '') {
				$golongan = $value->name;
			} else {
				$golongan = '-';
			}

			if ($value->panjang_pengisian != '') {
				$panjangP = $value->panjang_pengisian;
			}

			if ($value->panjang_real != '') {
				$panjangR = $value->panjang_real;
			}

			if ($value->lebar_real != '') {
				$lebarR = $value->lebar_real;
			}


		?>
			<tr>
				<td style="text-align: center;"> <?= $angka++ ?> </td>
				<td style="text-align: center;"> <?= $value->depart_date ?> </td>
				<td style="text-align: center;"> <?= $value->depart_time_start ?> </td>
				<td style="text-align: center;"> <?= $golongan ?> </td>
				<td style="text-align: center;"> <?= $value->kode_booking ?> </td>
				<td style="text-align: center;"> <?= $value->panjang_default?> </td>
				<td style="text-align: center;"> <?= $value->lebar_default ?> </td>
				<td style="text-align: center;"> <?= $panjangP ?> </td>
				<td style="text-align: center;"> <?= $value->lebar_pengisian ?></td>
				<td style="text-align: center;"> <?= $panjangR ?></td>
				<td style="text-align: center;"> <?= $lebarR ?></td>
				<td style="text-align: center;"> <?= $keterangan ?></td>
			</tr>

		<?php } ?>
	</table><br><br>

	<table class="tabel tabel-no-border full-width" style="width:100%;border: 0px solid white;">
		<tr style="text-align: center">
			<td style="width: 50%"><b>SUPERVISI</b></td>
			<td style="width: 50%"><b>SUPERVISI USAHA</b></td>
		</tr>
		<tr style="text-align: center">
			<td style="width: 50%"><b></b></td>
			<td style="width: 50%"><b></b></td>
		</tr>
		<tr style="text-align: center">
			<td style="width: 50%"><b></b></td>
			<td style="width: 50%"><b></b></td>
		</tr>
		<tr style="text-align: center">
			<td style="width: 50%"><b></b></td>
			<td style="width: 50%"><b></b></td>
		</tr>
		<tr style="text-align: center">
			<td style="width: 50%"><b>(.......................)</b></td>
			<td style="width: 50%"><b>(.......................)</b></td>
		</tr>
	</table>



</page>
<!-- Memanggil fungsi bawaan HTML2PDF -->
<?php
$content = ob_get_clean();
// $filename = "Pendapatan_harian";
$filename = strtoupper("laporan_perbandingan_linemeter" . "_" . $pelabuhan . "_" . $tanggal . "_" . $ship_class);
try {
	// setting paper
	$html2pdf = new HTML2PDF('P', 'A4', 'en', false, 'UTF-8', array(8, 10, 8, 4));
	// $html2pdf->pdf->SetDisplayMode('fullpage');
	$html2pdf->setTestTdInOnePage(false);
	$html2pdf->writeHTML($content);
	$html2pdf->Output($filename . '.pdf');
} catch (HTML2PDF_exception $e) {
	echo $e;
	exit;
}
?>