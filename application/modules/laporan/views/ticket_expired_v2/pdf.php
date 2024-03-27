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

	.tabel-data tr td {
		font-size: 8px;
	}
</style>
<?php
$status_data == 'kendaraan' ? $ds = 'KENDARAAN' : $ds = 'PENUMPANG';
?>
<!-- Setting Margin header/ kop -->
<page backtop="30mm" backbottom="10mm" backleft="8mm" backright="8mm">
	<page_header>
		<table class="tabel full-width" align="center" style="width: 94%; margin-bottom: 10px;">
			<tr>
				<td style="width: 100%; border-left: none; border-right: none; border-top: none" colspan="4"></td>
			</tr>
			<tr style="width: 100%">
				<td rowspan="4" class="no-border-right" style="width: 10%">
					<img src="assets/img/asdp.png" style="width:100px; height: auto">
				</td>
				<td rowspan="4" class="center bold" style="width: 42%;font-size:14px; line-height: 1.5; vertical-align: middle">
					LAPORAN TIKET EXPIRED <?php echo $ds ?>
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
	<br />
	<table class="tabel full-width" align="center" style="width: 1000px !important">
		<tr>
			<td style="width: 100%; border-left:none; border-right:none; border-top:none" colspan="6"></td>
		</tr>
		<tr>
			<td style="width: 15%; border-right:none">Waktu Keberangkatan</td>
			<td style="width: 1%; border-right:none; border-left:none">:</td>
			<td style="width: 33%; border-left: none"><?php echo $waktu_keberangkatan ?></td>
			<td style="width: 15%; border-right:none">Waktu Pembayaran</td>
			<td style="width: 1%; border-right:none; border-left:none">:</td>
			<td style="width: 33%; border-left: none"><?php echo $waktu_pembayaran ?></td>
		</tr>
		<tr>
			<td style="border-right:none">Pelabuhan</td>
			<td style="border-right:none; border-left:none">:</td>
			<td style="border-left: none" id="tPelabuhan"><?php echo $port ?></td>
			<td style="border-right:none">Kelas Layanan</td>
			<td style="border-right:none; border-left:none">:</td>
			<td style="border-left: none" id="tKLayanan"><?php echo $ship_class_name ?></td>
		</tr>
		<tr>
			<td style="border-right:none">Lintasan</td>
			<td style="border-right:none; border-left:none">:</td>
			<td style="border-left: none" id="tLintasan"><?php echo $route_name ?></td>
			<td style="border-right:none">Status Expired</td>
			<td style="border-right:none; border-left:none">:</td>
			<td style="border-left: none" id="tStatus"><?php echo $status_expired_name ?></td>
		</tr>
	</table>


	<br />

	<table class="tabel full-width tabel-data" style="width: 100% !important; min-width: 100% !important">
		<tr>
			<td style="width: 100%; border-left:none; border-right:none; border-top:none" colspan="13"></td>
		</tr>
		<tr class="bold center">
			<td>KODE <br /> BOOKING</td>
			<td>NO TIKET</td>
			<?php if ($ds == 'KENDARAAN') {
				echo '<td>PANJANG PADA <br />PEMESANAN <br />(METER)</td>';
			} ?>
			<td>GOLONGAN</td>
			<td>KELAS <br />LAYANAN</td>
			<td>TARIF <br />GOLONGAN (Rp.)</td>
			<td>WAKTU <br /> PEMBAYARAN</td>
			<td>JADWAL <br /> KEBERANGKATAN</td>
			<td>LINTASAN DIPESAN</td>
			<td>STATUS <br />TIKET</td>
			<td>STATUS EXPIRED</td>
			<td>WAKTU PENGAKUAN <br />PENDAPATAN</td>
			<td>JUMLAH PENDAPATAN <br /> EXPIRED</td>
		</tr>

		<?php
		foreach ($data as $row) {
			$rFare = idr_currency($row->fare);
			$rPaymentdate = empty($row->payment_date) ? "" : format_date($row->payment_date) . " " . format_time($row->payment_date);
			$rKeberangkatan = empty($row->keberangkatan) ? "" : format_date($row->keberangkatan);
			$rTanggalpengakuan = empty($row->tanggal_pengakuan) ? "" : format_date($row->tanggal_pengakuan);
			$rPendapatanExpired = $row->pendapatan_expired == 0 ? "" : idr_currency($row->pendapatan_expired);
			$lv = $ds == 'KENDARAAN' ? '<td>' . $row->length_vehicle . '</td>' : '';
			echo '
			<tr>
				<td>' . $row->booking_code . '</td>
				<td>' . $row->ticket_number . '</td>
				' . $lv . '
				<td>' . $row->golongan . '</td>
				<td>' . $row->ship_class_name . '</td>
				<td>' . $rFare . '</td>
				<td>' . $rPaymentdate . '</td>
				<td>' . $rKeberangkatan . '</td>
				<td>' . $row->route_name . '</td>
				<td>' . $row->description . '</td>
				<td>' . $row->description_expired . '</td>
				<td>' . $rTanggalpengakuan . '</td>
				<td>' . $rPendapatanExpired . '</td>
			</tr>
			';
		}
		?>

	</table>

	<page_footer>
		<i> dicetak pada : <?= format_dateTime(date("Y-m-d H:i:s")) ?> </i>
	</page_footer>
</page>
<!-- Memanggil fungsi bawaan HTML2PDF -->
<?php
$content = ob_get_clean();
// $filename = "Laporan_tiket_terjual";
$filename = strtoupper("Laporan_tiket_expired_" . $ds . "_" . $port . "_" . $waktu_keberangkatan . "_" . $ship_class_name);
try {
	// setting paper
	$html2pdf = new HTML2PDF('L', 'A4', 'en', false, 'UTF-8', array(8, 20, 8, 4));
	// $html2pdf->pdf->SetDisplayMode('fullpage');
	$html2pdf->setTestTdInOnePage(false);
	$html2pdf->writeHTML($content);
	$html2pdf->Output($filename . '.pdf');
} catch (HTML2PDF_exception $e) {
	echo $e;
	exit;
}
?>