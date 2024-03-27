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
					LAPORAN TIKET REFUND
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

	<table class="tabel full-width" align="center" style="width: 1000px !important">
		<tr>
			<td style="width: 100%; border-left: none; border-right: none; border-top: none" colspan="3"></td>
		</tr>
		<tr style="width: 100%">
			<td style="border-right:none; width: 30% !important">Tanggal</td>
			<td style="border-right:none; border-left:none; width: 1%">:</td>
			<td><?php echo format_date($datefrom) . " - " . format_date($dateto) ?></td>
		</tr>
		<tr>
			<td style="border-right:none">Pelabuhan</td>
			<td style="border-right:none; border-left:none">:</td>
			<td style="border-left: none" id="tPelabuhan"><?php echo strtoupper($portname) ?></td>
		</tr>
		<tr>
			<td style="border-right:none">Kelas Layanan</td>
			<td style="border-right:none; border-left:none">:</td>
			<td style="border-left: none" id="tKLayanan"><?php echo strtoupper($ship_classku)?></td>
		</tr>
		<tr>
			<td style="border-right:none">Sales Channel</td>
			<td style="border-right:none; border-left:none">:</td>
			<td style="border-left: none" id="tChannel"><?php echo ($channel) ? strtoupper($channel) : 'SEMUA' ?></td>
		</tr>
		<tr>
			<td style="border-right:none">Payment Channel</td>
			<td style="border-right:none; border-left:none">:</td>
			<td style="border-left: none">(Semua/VA Permata/VA BNI/Alfamart/Yo-Mart/PT Pos/...)</td>
		</tr>
	</table>


	<br />

	<table class="tabel full-width" style="width: 100% !important; min-width: 100% !important">
		<?php
		foreach ($data as $row) {
			$noRow + $no;
			echo '
				<tr class="bold"><td colspan="8" style="width:100%">' . $row['title'] . '</td></tr>';
			$noRow++;

			//Header
			echo '
				<tr class="bold center">
					<td rowspan="2">Uraian</td>
					<td rowspan="2">Tarif</td>
					<td rowspan="2">Produksi</td>
					<td colspan="3">Biaya Administrasi + Refund</td>
					<td rowspan="2">Charge Bank</td>
					<td rowspan="2">Pengembalian</td>
				</tr>
				<tr class="bold center">
					<td>Biaya Admin</td>
					<td>Biaya Refund</td>
					<td>Total</td>
				</tr>';
			$noRow++;


			$noRow++;

			$cFare2 = 0;
			$cProduksi2 = 0;
			$cAdmFee2 = 0;
			$cRefundFee2 = 0;
			$cChargeAmount2 = 0;
			$cBRF2 = 0;
			$cTotalAmount2 = 0;

			foreach ($row['data'] as $rowA) {
				echo '<tr class="bold"><td colspan="8">' . $rowA['title'] . '</td></tr>';
				$noRow++;

				$cFare1 = 0;
				$cProduksi1 = 0;
				$cAdmFee1 = 0;
				$cRefundFee1 = 0;
				$cChargeAmount1 = 0;
				$cBRF1 = 0;
				$cTotalAmount1 = 0;
				foreach ($rowA['data'] as $k => $v) {
					$cFare1 += $v->fare;
					$cProduksi1 += $v->produksi;
					$cAdmFee1 += $v->adm_fee;
					$cRefundFee1 += $v->refund_fee;
					$cChargeAmount1 += $v->charge_amount;
					$cBRF1 += $v->bank_transfer_fee;
					$cTotalAmount1 += $v->total_amount;


					echo '
						<tr>
							<td>' . $v->golongan . '</td>
							<td class="right">' . $v->fare . '</td>
							<td>' . $v->produksi . '</td>
							<td class="right">' . $v->adm_fee . '</td>
							<td class="right">' . $v->refund_fee . '</td>
							<td class="right">' . $v->charge_amount . '</td>
							<td class="right">' . $v->bank_transfer_fee . '</td>
							<td class="right">' . $v->total_amount . '</td>
						</tr>';
					$noRow++;
				}

				echo '
				<tr class="bold">
					<td>Subtotal</td>
					<td class="right">' . $cFare1 . '</td>
					<td>' . $cProduksi1 . '</td>
					<td class="right">' . $cAdmFee1 . '</td>
					<td class="right">' . $cRefundFee1 . '</td>
					<td class="right">' . $cChargeAmount1 . '</td>
					<td class="right">' . $cBRF1 . '</td>
					<td class="right">' . $cTotalAmount1 . '</td>
				</tr>';
				$noRow++;

				$cFare2 += $cFare1;
				$cProduksi2 += $cProduksi1;
				$cAdmFee2 += $cAdmFee1;
				$cRefundFee2 += $cRefundFee1;
				$cChargeAmount2 += $cChargeAmount1;
				$cBRF2 += $cBRF1;
				$cTotalAmount2 += $cTotalAmount1;
			}


			echo '
			<tr class="bold">
				<td>Total ' . $row['title'] . '</td>
				<td class="right">' . $cFare2 . '</td>
				<td>' . $cProduksi2 . '</td>
				<td class="right">' . $cAdmFee2 . '</td>
				<td class="right">' . $cRefundFee2 . '</td>
				<td class="right">' . $cChargeAmount2 . '</td>
				<td class="right">' . $cBRF2 . '</td>
				<td class="right">' . $cTotalAmount2 . '</td>
			</tr>
			';
			$noRow++;

			$cFare3 += $cFare2;
			$cProduksi3 += $cProduksi2;
			$cAdmFee3 += $cAdmFee2;
			$cRefundFee3 += $cRefundFee2;
			$cChargeAmount3 += $cChargeAmount2;
			$cBRF3 += $cBRF2;
			$cTotalAmount3 += $cTotalAmount2;


			$no++;
		}

		echo '
		<tr class="bold">
			<td>Total Refund</td>
			<td class="right">' . $cFare3 . '</td>
			<td>' . $cProduksi3 . '</td>
			<td class="right">' . $cAdmFee3 . '</td>
			<td class="right">' . $cRefundFee3 . '</td>
			<td class="right">' . $cChargeAmount3 . '</td>
			<td class="right">' . $cBRF3 . '</td>
			<td class="right">' . $cTotalAmount3 . '</td>
		</tr>';
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
$filename = strtoupper("Laporan_tiket_refund_" . $portname . "_" . format_date($datefrom) . "_" . format_date($dateto) . "_" . $ship_classku);
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