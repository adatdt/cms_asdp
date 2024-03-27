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
<page backtop="30mm" backbottom="8mm" backleft="0mm" backright="0mm">
	<page_header>
       <table class="tabel full-width" align="center">
            <tr>
                <td rowspan="4" class="no-border-right" style="width: 20%">
                    <img src="assets/img/asdp.png" style="width:100px; height: auto">
                </td>
                <td rowspan="4" class="center bold" style="width: 50%;font-size:14px; line-height: 1.5; vertical-align: middle">
                    <?= $report_title ?>
                </td>
                <td style="width: 15%" class="no-border-right no-border-left">No. Dokumen</td>
                <td style="width: 15%">:</td>
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
			<td style="border-right: none !important;width: 25%">PELABUHAN</td>
			<td style="border-left: none !important;width: 25%">:
				<?= $pelabuhan  ?>
			</td>
			<td style="border-right: none !important;width: 25%">SHIFT</td>
			<td style="border-left: none !important;width: 25%">:
				<?=$shift ?>
			</td>
		</tr>
		<tr>
			<td style="border-right: none !important;">REGU</td>
			<td style="border-left: none !important;">:
				<?=$regu ?>
			</td>
			<td style="border-right: none !important;">TANGGAL</td>
			<td style="border-left: none !important;">:
				<?= format_date($this->input->get('datefrom')) ?> - <?= format_date($this->input->get('dateto')) ?>
			</td>
		</tr>
	</table>

	<br>
	<table class="tabel full-width" align="center" style="width: 100%">
		<tr>
			<th class="center bold" style="width: 5%">NO</th>
			<th class="center bold" style="width: 25%">NAMA PERUSAHAAN</th>
			<th class="center bold" style="width: 20%">NAMA KAPAL</th>
			<th class="center bold" style="width: 9%">TARIF</th>
			<th class="center bold" style="width: 13%">PRODUKSI<br/>(Lbr)</th>
			<th class="center bold" style="width: 15%">PENDAPATAN<br/>(Rp)</th>
			<th class="center bold" style="width: 10%">KET</th>
		</tr>
			<?php
			$produksi = 0;
			$pendapatan = 0;
			$angka = 1;

			foreach ($data['data'] as $key_pnp => $pnp) {
				$produksi += $pnp->produksi;
				$pendapatan += $pnp->pendapatan;
				?>
				<tr>
					<td class="center"><?=$angka++ ?></td>
					<td class="left"><?= $pnp->company?></td>
					<td class="left"><?= $pnp->ship_name ?></td>
					<td class="right"><?= idr_currency($pnp->harga) ?></td>
					<td class="right"><?= idr_currency($pnp->produksi) ?></td>
					<td class="right"><?= idr_currency($pnp->pendapatan) ?></td>
					<td class="right"></td>
				</tr>
			<?php } ?>

			<tr>
				<th colspan="4" class="center bol"><b>JUMLAH</b></th>
				<th class="right bold"><?= idr_currency($produksi) ?></th>
				<th class="right bold"><?= idr_currency($pendapatan) ?></th>
				<th></th> 
			</tr>
		</table>
		<br>

				<br>
				<table class="table table-no-border full-width" align="center">
					<tr>
						<td class="center" style="padding-bottom: 0;width: 33%"></td>
						<th class="center" style="width: 33%"></th>
						<td class="center" style="padding-bottom: 0">.................., ...................................</td>
					</tr>
					<tr>
						<th class="center"></th>
						<th class="center"></th>
						<th class="center">Supervisor</th>
					</tr>
					<tr>
						<th class="center"></th>
						<th class="center"></th>
						<th class="center">
							<br>
							<br>
							<br>
							<br>
						</th>
					</tr>
					<tr>
						<td class="center" style="text-decoration: underline; padding-bottom: 0"></td>
						<td class="center" style="text-decoration: underline; padding-bottom: 0"></td>
						<td class="center" style="text-decoration: underline; padding-bottom: 0"></td>
					</tr>
					<tr>
						<td class="center"></td>
						<td class="center"></td>
						<td class="center">NIK. .................................</td>
					</tr>
				</table>

				<page_footer>
					<i> dicetak pada : <?= format_dateTime(date("Y-m-d H:i:s")) ?> </i>
				</page_footer>

			</page>
			<?php
			$content = ob_get_clean();
			$filename = strtoupper("Pendapatan_cetak_tiket_" . $pelabuhan . "_" . $this->input->get('datefrom') . "_" . $this->input->get('dateto'));
			try
			{
				$html2pdf = new HTML2PDF('L', 'A4', 'en', false, 'UTF-8', array(8, 10, 8, 8));
				$html2pdf->setTestTdInOnePage(false);
				$html2pdf->writeHTML($content);
				$html2pdf->Output($filename.'.pdf');
			}
			catch(HTML2PDF_exception $e) {
				echo $e;
				exit;
			}
			?>