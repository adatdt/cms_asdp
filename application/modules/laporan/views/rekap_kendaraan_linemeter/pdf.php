<!-- Setting CSS bagian header/ kop -->
<style type="text/css">
	.tabel {
		border-collapse: collapse;
	}

	.tabel th,
	.tabel td {
		padding: 5px 5px;
		border: 1px solid #000;
		font-size: 12px;
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
					LAPORAN REKAPITULASI RINCIAN GOLONGAN KENDARAAN TERHADAP PENGGUNAAN LINEMETER
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
		<!-- <tr>
			<td style="border-right: none !important;width: 25%">CABANG</td>
			<td style="border-left: none !important;width: 25%">: <?= $cabang ?></td>
			<td style="border-right: none !important;width: 25%">SHIFT</td>
			<td style="border-left: none !important;width: 25%">: <?= $shift ?></td>
		</tr> -->
		<tr>
			<td style="border-right: none !important;width: 25%">PELABUHAN</td>
			<td style="border-left: none !important;width: 25%">: <?= $pelabuhan ?></td>
			<td style="border-right: none !important;width: 25%">TANGGAL</td>
			<td style="border-left: none !important;width: 25%">: <?= $tanggal ?></td>
		</tr>
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
			<td style="text-align: center"> No </td>
			<td style="text-align: center"> Tanggal </td>
			<td style="text-align: center"> Waktu </td>
			<td style="text-align: center; width: 25% "> Golongan </td>
			<td style="text-align: center"> Produksi </td>
			<td style="text-align: center"> Lebar (m) </td>
			<td style="text-align: center"> Panjang (m) </td>
			<td style="text-align: center"> Luas (m2) </td>
			<td style="text-align: center"> Jumlah (m2) </td>
		</tr>
		<?php
		$angka = 1;
		$produksiku = 0;
		$pendapatanku = 0;
		$lebarku = 0;
		$panjangku = 0;
		$jumlahku = 0;
		$luasku = 0;
		$total_ketersediaan_linemeter = 0;
		$total_penggunaan_linemeter = 0;
		$persentase = 0;
		$luasku = 0;
		$kosong = "";

		foreach ($kendaraan as $key => $value) {
			$sisa 								= ($value->total_lm) - ($value->jumlah);
			$total_ketersediaan_linemeter  		+= $value->total_lm;
			$total_penggunaan_linemeter    		+= $value->jumlah;
			$total_sisa_ketersediaan_linemeter 	= ($total_ketersediaan_linemeter) - ($total_penggunaan_linemeter);
			$persentase 						= ($total_penggunaan_linemeter) / ($total_ketersediaan_linemeter);

			if ($persentase >= 100) {
				$status = "OVER";
			} else if ($persentase <= 100) {
				$status = "UNDER";
			}


		?>
			<tr>
				<td style="text-align: center;"> <?= $angka++ ?> </td>
				<td style="text-align: center;"> <?= $value->depart_date ?> </td>
				<td style="text-align: center;"> <?= $value->depart_time ?> </td>
				<td style="text-align: center;"> <?= $value->golongan ?> </td>
				<td style="text-align: center;"> <?= idr_currency($value->produksi) ?> </td>
				<td style="text-align: center;"> <?= idr_currency($value->lebar) ?> </td>
				<td style="text-align: center;"> <?= idr_currency($value->panjang) ?> </td>
				<td style="text-align: center;"> <?= idr_currency($value->luas) ?></td>
				<td style="text-align: center;"> <?= idr_currency($value->jumlah) ?></td>
			</tr>

		<?php } ?>
		<tr>
			<td colspan="7" style="border-right: 0px;"><b>TOTAL PENGGUNAAN LINEMETER PER JAM</b></td>
			<td style="text-align: center"><b><?= $kosong ?></b></td>
			<td style="text-align: center"><b><?= idr_currency($total_penggunaan_linemeter) ?></b></td>
		</tr>
		<tr>
			<td colspan="7" style="border-right: 0px;"><b>TOTAL KETERSEDIAAN LINEMETER PER JAM</b></td>
			<td style="text-align: center"><b><?= $kosong ?></b></td>
			<td style="text-align: center"><b><?= idr_currency($total_ketersediaan_linemeter) ?></b></td>
		</tr>
		<tr>
			<td colspan="7" style="border-right: 0px;"><b>SISA KETERSEDIAAN LINEMETER</b></td>
			<td style="text-align: center"><b><?= $kosong ?></b></td>
			<td style="text-align: center"><b><?= idr_currency($total_ketersediaan_linemeter - $total_penggunaan_linemeter ) ?></b></td>
		</tr>
		<tr>
			<td colspan="7" style="border-right: 0px;"><b>PERSENTASE</b></td>
			<td style="text-align: center"><b><?= $kosong ?></b></td>
			<td style="text-align: center"><b><?= ($persentase) ?> % </b></td>
		</tr>
		<tr>
			<td colspan="7" style="border-right: 0px;"><b>STATUS</b></td>
			<td style="text-align: center"><b><?= $kosong ?></b></td>
			<td style="text-align: center"><b><?= $status ?></b></td>
		</tr>
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
$filename = strtoupper("Rekapitulasi_rincian_golongan_kendaraan_terhadap_penggunaan_linemeter" . "_" . $pelabuhan . "_" . $tanggal . "_" . $ship_class);
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