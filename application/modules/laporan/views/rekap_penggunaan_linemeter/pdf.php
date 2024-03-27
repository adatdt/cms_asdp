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
					LAPORAN REKAPITULASI PENGGUNAAN LINEMETER PER JAM
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
			<td style="text-align: center"> Ketersediaan Linemeter </td>
			<td style="text-align: center"> Penggunaan Linemeter </td>
			<td style="text-align: center"> Sisa Ketersediaan </td>
			<td style="text-align: center"> Persentase </td>
			<td style="text-align: center"> Status </td>
		</tr>
		<?php
		$angka = 1;
		$total_ketersediaan = 0;
		$total_pengguna = 0;
		$sisa = 0;
		$persen = 0;
		$kosong = "";

		foreach ($penggunaan as $key => $value) {
			$sisa = ($value->ketersediaan) - ($value->pengguna);
			$persen = ($value->pengguna) / ($value->ketersediaan);
			
			$total_ketersediaan += $value->ketersediaan;
			$total_pengguna += $value->pengguna;
			$total_sisa += $sisa;
			$total_persen = $total_pengguna / $total_ketersediaan ;

			if ($persen >= 100) {
				$status = "OVER";
			} else if ($persen <= 100) {
				$status = "UNDER";
			}
			

		?>
			<tr>
				<td style="text-align: center;"> <?= $angka++ ?> </td>
				<td style="text-align: center;"> <?= $value->depart_date ?> </td>
				<td style="text-align: center;"> <?= $value->depart_time ?> </td>
				<td style="text-align: center;"> <?= idr_currency($value->ketersediaan) ?> </td>
				<td style="text-align: center;"> <?= idr_currency($value->pengguna) ?> </td>
				<td style="text-align: center;"> <?= idr_currency($sisa) ?> </td>
				<td style="text-align: center;"> <?= $persen ?> % </td>
				<td style="text-align: center;"> <?= $status ?> </td>
			</tr>

		<?php } ?>
		<tr>
			<td colspan="2" style="border-right: 0px;"><b>TOTAL</b></td>
			<td style="text-align: center"><b><?= $kosong ?></b></td>
			<td style="text-align: center"><b><?= idr_currency($total_ketersediaan) ?></b></td>
			<td style="text-align: center"><b><?= idr_currency($total_pengguna) ?></b></td>
			<td style="text-align: center"><b><?= idr_currency($total_sisa) ?></b></td>
			<td style="text-align: center"><b><?= $total_persen ?> % </b></td>
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
$filename = strtoupper("Rekapitulasi_penggunaan_linemeter" . $pelabuhan . "_" . $tanggal . "_" . $ship_class);
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