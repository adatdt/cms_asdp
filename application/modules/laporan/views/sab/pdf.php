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
<!-- Setting Margin header/ kop -->
<page backtop="30mm" backbottom="10mm" backleft="8mm" backright="8mm">
	<page_header>
		<table class="tabel full-width" align="center" style="width: 100%">
            <tr style="width: 100%">
                <td rowspan="4" class="no-border-right" style="width: 10%">
                    <img src="assets/img/asdp.png" style="width:100px; height: auto">
                </td>
                <td rowspan="4" class="center bold" style="width: 42%;font-size:14px; line-height: 1.5; vertical-align: middle">
                    LAPORAN REKAPITULASI<br>SURAT ANGKUT BEBAS
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

	<table class="tabel full-width" align="center">
		<tr>
			<td style="border-right: none !important;width: 25%">CABANG</td>
			<td style="border-left: none !important;width: 25%">: <?=$cabang ?></td>
			<td style="border-right: none !important;width: 25%">LINTASAN</td>
			<td style="border-left: none !important;width:25%;">: <?=$lintasan?></td>
		</tr>
		<tr>
			<td style="border-right: none !important;width: 25%">PELABUHAN</td>
			<td style="border-left: none !important;width: 25%">: <?=$pelabuhan ?></td>
			<td style="border-right: none !important;width: 25%">TANGGAL</td>
			<td style="border-left: none !important;width: 25%">: <?=$tanggal ?></td>
		</tr>
	</table>

	<br>
	
	<table class="tabel full-width" style="width: 100% !important">
		<tr>
			<td style="text-align: center"> No </td>
			<td style="text-align: center"> Jenis Tiket SAB</td>
			<td style="text-align: center"> Produksi </td>
		</tr>
		<tr>
			<th colspan="5" style="width: 100%">1. PENUMPANG</th>
		</tr>
		<?php 
		$angka = 1;
		$produksi_penumpang = 0;

		foreach ($penumpang as $key => $value) { 
			$produksi_penumpang += $value->produksi;
		?>
		<tr>
			<td style="text-align: center"> <?=$angka++ ?> </td>
			<td style="text-align: left"> <?=$value->golongan ?> </td>
			<td style="text-align: right"> <?=idr_currency($value->produksi) ?> </td>
		</tr>
		<?php } ?>

		<tr>
			<td colspan="2"><b>Sub Total</b></td>
			<td style="text-align: right"><b><?=idr_currency($produksi_penumpang) ?></b></td>
		</tr>

		<tr>
			<th colspan="5" style="width: 100%">2. KENDARAAN</th>
		</tr>

		<?php 
		$angka = 1;
		$produksi_kendaraan = 0;
		foreach ($kendaraan as $key => $value) { 
			$produksi_kendaraan += $value->produksi;
		?>
		<tr>
			<td style="text-align: center"> <?=$angka++ ?> </td>
			<td style="text-align: left"> <?=$value->golongan ?> </td>
			<td style="text-align: right"> <?=idr_currency($value->produksi) ?> </td>
		</tr>
		<?php } ?>

		<tr>
			<td colspan="2"><b>Sub Total</b></td>
			<td style="text-align: right"><b><?=idr_currency($produksi_kendaraan) ?></b></td>
		</tr>

		<tr>
			<td colspan="2" style="border-right: 0px;"><b>Total </b></td>
			<td style="text-align: right"><b><?=idr_currency($produksi_penumpang + $produksi_kendaraan) ?></b></td>
		</tr>
	</table>

	<page_footer>
        <i> dicetak pada : <?= format_dateTime(date("Y-m-d H:i:s")) ?> </i>
    </page_footer>
</page>
<!-- Memanggil fungsi bawaan HTML2PDF -->
<?php
$content = ob_get_clean();
// $filename = "Laporan_tiket_terjual";
$filename = strtoupper("Laporan_sab_" . $pelabuhan . "_". $tanggal . "_" . $this->input->get("ship_classku"));
try
{
  // setting paper
	$html2pdf = new HTML2PDF('P', 'A4', 'en', false, 'UTF-8', array(8, 10, 8, 4));
	// $html2pdf->pdf->SetDisplayMode('fullpage');
	$html2pdf->setTestTdInOnePage(false);
	$html2pdf->writeHTML($content);
	$html2pdf->Output($filename.'.pdf');
}
catch(HTML2PDF_exception $e) {
	echo $e;
	exit;
}
?>
