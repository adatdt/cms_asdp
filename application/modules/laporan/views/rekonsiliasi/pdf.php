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
	<!-- Setting Header -->
	<table class="tabel full-width" align="center">
		<tr>
			<td rowspan="4" class="no-border-right" style="width: 10%">
				<img src="<?php echo base_url();?>assets/img/asdp.png" style="width:100px; height: auto">
			</td>
			<td rowspan="4" class="center bold" style="width: 42%;font-size:14px; line-height: 1.5; vertical-align: middle">
				LAPORAN REKONSILIASI SHARING REVENUE
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
			<td>: </td>
		</tr>
	</table>
	</page_header>

	<!-- Setting Footer -->
	<page_footer>
	<table class="page_footer" align="center">

	</table>
	</page_footer>
	<br>

	<table class="tabel full-width" align="center">
		<tr>
			<td style="border-right: none !important;width: 25%">PELABUHAN</td>
			<td style="border-left: none !important;width: 25%">: <?=$port_name ?></td>
			<td style="border-right: none !important;width: 15%">TANGGAL</td>
			<td style="border-left: none !important;width: 35%;">: <?=$print_date?></td>
		</tr>
		<tr>
			<td style="border-right: none !important;width: 25%">KELAS LAYANAN</td>
			<td style="border-left: none !important;width: 25%;">: <?=$ship_classku?></td>
		</tr>
	</table>

	<br>

	<table class="tabel full-width" style="width: 100% !important">
		<tr>
			<th colspan="5" style="width: 100%">TIPE PENJUALAN : TUNAI</th>
		</tr>
		<tr>
			<td style="text-align: center"> No </td>
			<td style="text-align: center"> Jenis </td>
			<td style="text-align: center"> Tarif Pas Pelabuhan<br>(KD88 Tahun 2017) </td>
			<td style="text-align: center"> Produksi (Lbr) </td>
			<td style="text-align: center"> Pendapatan (Rp.) </td>
		</tr>
		<?php
		$angka = 1;
		$produksi_tunai = 0;
		$pendapatan_tunai = 0;

		foreach ($tunai as $key => $value) {
			$produksi_tunai += $value->produksi;
			$pendapatan_tunai += $value->pendapatan;
			?>
			<tr>
				<td style="text-align: center"> <?=$angka++ ?> </td>
				<td style="text-align: left"> <?=$value->golongan ?> </td>
				<td style="text-align: right;"> <?=idr_currency($value->entrance_fee) ?> </td>
				<td style="text-align: right;"> <?=idr_currency($value->produksi) ?> </td>
				<td style="text-align: right;"> <?=idr_currency($value->pendapatan) ?> </td>
			</tr>

		<?php } ?>
		<tr>
			<td colspan="3" style="border-right: 0px;"><b>Sub Total</b></td>
			<td style="text-align: right"><b><?=idr_currency($produksi_tunai) ?></b></td>
			<td style="text-align: right"><b><?=idr_currency($pendapatan_tunai) ?></b></td>
		</tr>
	</table><br>

	<table class="tabel full-width" style="width: 100% !important">
		<tr>
			<th colspan="5" style="width: 100%">TIPE PENJUALAN : PREPAID CASHLESS</th>
		</tr>
		<tr>
			<td style="text-align: center"> No </td>
			<td style="text-align: center"> Jenis </td>
			<td style="text-align: center"> Tarif (Rp.) </td>
			<td style="text-align: center"> Produksi (Lbr) </td>
			<td style="text-align: center"> Pendapatan (Rp.) </td>
		</tr>
		<?php
		$angka = 1;
		$produksi_cashless = 0;
		$pendapatan_cashless = 0;

		foreach ($cashless as $key => $value) {
			$produksi_cashless += $value->produksi;
			$pendapatan_cashless += $value->pendapatan;
			?>
			<tr>
				<td style="text-align: center"> <?=$angka++ ?> </td>
				<td style="text-align: left"> <?=$value->golongan ?> </td>
				<td style="text-align: right;"> <?=idr_currency($value->entrance_fee) ?> </td>
				<td style="text-align: right;"> <?=idr_currency($value->produksi) ?> </td>
				<td style="text-align: right;"> <?=idr_currency($value->pendapatan) ?> </td>
			</tr>
		<?php } ?>
		<tr>
			<td colspan="3"><b> Sub Total</b></td>
			<td style="text-align: right"><b><?=idr_currency($produksi_cashless) ?></b></td>
			<td style="text-align: right"><b><?=idr_currency($pendapatan_cashless) ?></b></td>
		</tr>
	</table><br>
	
	<table class="tabel full-width" style="width: 100% !important">
		<tr>
			<th colspan="5" style="width: 100%">TIPE PENJUALAN : ONLINE</th>
		</tr>
		<tr>
			<td style="text-align: center"> No </td>
			<td style="text-align: center"> Jenis </td>
			<td style="text-align: center"> Tarif (Rp.) </td>
			<td style="text-align: center"> Produksi (Lbr) </td>
			<td style="text-align: center"> Pendapatan (Rp.) </td>
		</tr>
		<?php 
		$angka = 1;
		$produksi_online = 0;
		$pendapatan_online = 0;

		foreach ($online as $key => $value) { 
			$produksi_online += $value->produksi;
			$pendapatan_online += $value->pendapatan;
			?>
			<tr>
				<td style="text-align: center"> <?=$angka++ ?> </td>
				<td style="text-align: left"> <?=$value->golongan ?> </td>
				<td style="text-align: right;"> <?=idr_currency($value->entrance_fee) ?> </td>
				<td style="text-align: right;"> <?=idr_currency($value->produksi) ?> </td>
				<td style="text-align: right;"> <?=idr_currency($value->pendapatan) ?> </td>
			</tr>
		<?php } ?>
		<tr>
			<td colspan="3" style="border-right: 0px;"><b>Sub Total</b></td>
			<td style="text-align: right"><b><?=idr_currency($produksi_online) ?></b></td>
			<td style="text-align: right"><b><?=idr_currency($pendapatan_online) ?></b></td>
		</tr>
		<!-- <tr>
			<td colspan="3" style="border-right: 0px;"><b>TOTAL JUMLAH</b></td>
			<td style="text-align: right"><b><?=idr_currency($produksi_tunai+$produksi_cashless+$produksi_online) ?></b></td>
			<td style="text-align: right"><b><?=idr_currency($pendapatan_tunai+$pendapatan_cashless+$pendapatan_online) ?></b></td>
		</tr> -->
	</table><br>


	<table class="tabel full-width" style="width: 100% !important">
		<tr>
			<th colspan="5" style="width: 100%">TIPE PENJUALAN : IFCS</th>
		</tr>
		<tr>
			<td style="text-align: center"> No </td>
			<td style="text-align: center"> Jenis </td>
			<td style="text-align: center"> Tarif (Rp.) </td>
			<td style="text-align: center"> Produksi (Lbr) </td>
			<td style="text-align: center"> Pendapatan (Rp.) </td>
		</tr>
		<?php 
		$angka = 1;
		$produksi_ifcs = 0;
		$pendapatan_ifcs = 0;

		foreach ($ifcs as $key => $value) { 
			$produksi_ifcs += $value->produksi;
			$pendapatan_ifcs += $value->pendapatan;
			?>
			<tr>
				<td style="text-align: center"> <?=$angka++ ?> </td>
				<td style="text-align: left"> <?=$value->golongan ?> </td>
				<td style="text-align: right;"> <?=idr_currency($value->entrance_fee) ?> </td>
				<td style="text-align: right;"> <?=idr_currency($value->produksi) ?> </td>
				<td style="text-align: right;"> <?=idr_currency($value->pendapatan) ?> </td>
			</tr>
		<?php } ?>
		<tr>
			<td colspan="3" style="border-right: 0px;"><b>Sub Total</b></td>
			<td style="text-align: right"><b><?=idr_currency($produksi_ifcs) ?></b></td>
			<td style="text-align: right"><b><?=idr_currency($pendapatan_ifcs) ?></b></td>
		</tr>
	</table><br>

	<table class="tabel full-width" style="width: 100% !important">
		<tr>
			<th colspan="5" style="width: 100%">TIPE PENJUALAN : B2B</th>
		</tr>
		<tr>
			<td style="text-align: center"> No </td>
			<td style="text-align: center"> Jenis </td>
			<td style="text-align: center"> Tarif (Rp.) </td>
			<td style="text-align: center"> Produksi (Lbr) </td>
			<td style="text-align: center"> Pendapatan (Rp.) </td>
		</tr>
		<?php 
		$angka = 1;
		$produksi_b2b = 0;
		$pendapatan_b2b = 0;

		foreach ($b2b as $key => $value) { 
			$produksi_b2b += $value->produksi;
			$pendapatan_b2b += $value->pendapatan;
			?>
			<tr>
				<td style="text-align: center"> <?=$angka++ ?> </td>
				<td style="text-align: left"> <?=$value->golongan ?> </td>
				<td style="text-align: right;"> <?=idr_currency($value->entrance_fee) ?> </td>
				<td style="text-align: right;"> <?=idr_currency($value->produksi) ?> </td>
				<td style="text-align: right;"> <?=idr_currency($value->pendapatan) ?> </td>
			</tr>
		<?php } ?>
		<tr>
			<td colspan="3" style="border-right: 0px;"><b>Sub Total</b></td>
			<td style="text-align: right"><b><?=idr_currency($produksi_b2b) ?></b></td>
			<td style="text-align: right"><b><?=idr_currency($pendapatan_b2b) ?></b></td>
		</tr>
		<tr>
			<td colspan="3" style="border-right: 0px;"><b>TOTAL JUMLAH</b></td>
			<td style="text-align: right"><b><?=idr_currency($produksi_tunai+$produksi_cashless+$produksi_online+$produksi_ifcs+$produksi_b2b) ?></b></td>
			<td style="text-align: right"><b><?=idr_currency($pendapatan_tunai+$pendapatan_cashless+$pendapatan_online+$pendapatan_ifcs+$pendapatan_b2b) ?></b></td>
		</tr>
	</table>
</page>
<!-- Memanggil fungsi bawaan HTML2PDF -->
<?php
$content = ob_get_clean();
$filename = "Rekon_" . $port_name;
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