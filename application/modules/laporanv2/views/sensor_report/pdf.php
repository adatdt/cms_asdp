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

	.no-border {
		border: none !important;
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
				<td rowspan="4" class="center bold no-border" style="width: 42%;font-size:18px; line-height: 1.5; vertical-align: middle; padding-top:25px">
					LAPORAN BERITA ACARA HASIL PENGUKURAN NOMOR POLISI, DIMENSI KENDARAAN, DAN BERAT KENDARAAN
				</td>
			</tr>
		</table>
	</page_header>

	<table class="tabel no-border" align="left" style="">
		<tr>
			<td style="border-left: none; border-right: none; border-top: none; border-bottom: none" colspan="10"></td>
		</tr>
		<tr class="no-border">
			<td style="border: none; width:5%; font-size:16px;">Cabang</td>
			<td style="border: none; width:10%; font-size:16px;">: <?= !empty($port_name)?strtoupper($port_name):"Semua" ?></td>
			<td style="border: none; width:5%; font-size:16px;">Tanggal</td>
			<td style="border: none; width:10%; font-size:16px;">: <?= format_date($dateFrom)." - ".format_date($dateTo) ?></td>
		</tr>
		<tr class="no-border">
			<td style="border: none; width:5%; font-size:16px;">Pelabuhan</td>
			<td style="border: none; width:10%; font-size:16px;">: <?= !empty($port_name)?strtoupper($port_name):"Semua" ?></td>
			<td style="border: none; width:5%; font-size:16px;">Petugas</td>
			<td style="border: none; width:10%; font-size:16px;">: <?= !empty($petugas_name)?strtoupper($petugas_name):"Semua" ?></td>
		</tr>
		<tr class="no-border">
			<td style="border: none; width:5%; font-size:16px;">Lintasan</td>
			<td style="border: none; width:10%; font-size:16px;">: <?= !empty($lintasanku)?strtoupper($lintasanku):"Semua" ?></td>
			<td style="border: none; width:5%; font-size:16px;">Nama loket</td>
			<td style="border: none; width:10%; font-size:16px;">: <?= !empty($loket_name)?strtoupper($loket_name):"Semua" ?></td>
		</tr>
		<tr class="no-border">
			<td style="border: none; width:5%; font-size:16px;">Shift</td>
			<td style="border: none; width:10%; font-size:16px;">: <?= !empty($shift_name)?strtoupper($shift_name):"Semua" ?></td>
			<td style="border: none; width:5%; font-size:16px;">Status</td>
			<td style="border: none; width:10%; font-size:16px;">: <?= !empty($status_name)?strtoupper($status_name):"Semua" ?></td>
		</tr>
		<tr class="no-border">
			<td style="border: none; width:5%; font-size:16px;">Regu</td>
			<td style="border: none; width:10%; font-size:16px;">: <?= !empty($regu_name)?strtoupper($regu_name):"Semua" ?></td>
			<td style="border: none; width:5%; font-size:16px;">Keterangan</td>
			<td style="border: none; width:10%; font-size:16px;">: <?= !empty($keter)?strtoupper($keter):"Semua" ?></td>
		</tr>
		<tr class="no-border">
			<td style="border: none; width:5%; font-size:16px;">Layanan</td>
			<td style="border: none; width:10%; font-size:16px;">: <?= !empty($shipclass_name)?strtoupper($shipclass_name):"Semua" ?></td>
		</tr>
		<!-- <tr style="" class="no-border">
			<td style="border-right:none">Cabang</td>
			<td style="border-right:none; border-left:none">: <?= !empty($port)?strtoupper($port):"Merak" ?></td>
			<td style="border-left: none" id="tPelabuhan"></td>
			<td class="no-border" style="width:10%; border-top: none !important;"></td>
			<td></td>
			<td></td>
			<td style="border-right:none;">Tanggal</td>
			<td style="border-right:none; border-left:none;">: <?= format_date($dateFrom)." - ".format_date($dateTo) ?></td>
			<td></td>
		</tr>
		<tr>
			<td style="border-right:none">Pelabuhan</td>
			<td style="border-right:none; border-left:none">: <?= !empty($port)?strtoupper($port):"-" ?></td>
			<td style="border-left: none" id="tPelabuhan"></td>
		</tr>
		<tr>
			<td style="border-right:none">Payment Channel</td>
			<td style="border-right:none; border-left:none">: <?= !empty($paymentChannel)?$paymentChannel:"-" ?></td>
			<td style="border-left: none" id="tChannel"></td>
		</tr> -->
	</table>


	<br />

	<?php  
		// $alphabet = range('a', 'z');

    //     $total_produksi=0;
    //     $total_tarif_lintasan_tujuan=0;
    //     $total_tarif_lintasan_awal=0;
    //     $total_selisih_tarif=0;
    //     $total_admin_fee=0;
    //     $total_reroute_fee=0;
    //     $total_charge_fee=0;

		// $i=count((array)$data[0]); 
		// $index=1; 
		// foreach ($data[0] as $key=> $getMahal ) {

		 ?>

		<table class="tabel full-width" style="width: 100% !important; min-width: 100% !important">

			<tr>
	        	<td colspan="39" style="height:50px; border-left:none; border-right:none; border-top:none;"></td>
	        </tr>

					<tr class="bold center">
							<th rowspan="3">NO</th>
							<th rowspan="3">KODE BOOKING</th>
							<th rowspan="3">NOMOR TIKET</th>
							<th rowspan="3">LAYANAN</th>
							<th colspan="4" rowspan="2">NOMOR POLISI</th>
							<th colspan="19">HASIL PENGUKURAN</th>
							<th colspan="3" rowspan="2">IDENTITAS PETUGAS</th>
							<th colspan="5" rowspan="2">INFORMASI TIKET</th>
							<th colspan="4" rowspan="2">STATUS APPROVAL NAIK/TURUN GOLONGAN</th>
					</tr>
					<tr class="bold center">
							<th colspan="4">PANJANG</th>
							<th colspan="4">LEBAR</th>
							<th colspan="4">TINGGI</th>
							<th colspan="3">BERAT</th>
							<th colspan="4">GOLONGAN</th>
					</tr>

					<!-- <tr>
							<td>1</td>
							<td>kode</td>
							<td>tiket</td>
							<td>reg</td>
							<td>b12</td>
							<td>10</td>
							<td>11</td>
							<td>12</td>
							<td>20</td>
							<td>VA</td>
					</tr> -->
					<tr class="bold center">
							<th>RESERVASI</th>
							<th>SENSOR</th>
							<th>MANUAL</th>
							<th>KOMPARASI <br> MANUAL <br> DAN SENSOR</th>

							<th>RESERVASI</th>
							<th>SENSOR</th>
							<th>MANUAL</th>
							<th>MANUAL <br> - SENSOR</th>

							<th>RESERVASI</th>
							<th>SENSOR</th>
							<th>MANUAL</th>
							<th>MANUAL <br> - SENSOR</th>

							<th>RESERVASI</th>
							<th>SENSOR</th>
							<th>MANUAL</th>
							<th>MANUAL <br> - SENSOR</th>

							<th>BATASAN</th>
							<th>HASIL <br> TIMBANG</th>
							<th>STATUS</th>

							<th>RESERVASI</th>
							<th>SENSOR</th>
							<th>MANUAL</th>
							<th>KOMPARASI <br> MANUAL <br> DAN SENSOR</th>

							<th>USER PETUGAS <br> LOKET</th>
							<th>NAMA LOKET</th>
							<th>USER SUPERVISI</th>

							<th>STATUS</th>
							<th>DERMAGA</th>
							<th>KAPAL</th>
							<th>WAKTU</th>
							<th>KETERANGAN</th>

							<th>STATUS</th>
							<th>USER</th>
							<th>TANGGAL</th>
							<th>AKSI</th>
					</tr>
					
	        <tbody>
            <?php $no = 1;
            foreach ($data as $key => $value) {


            ?>
                <tr>
                    <td><?php echo  $no ?></td>
                    <td> <?php echo $value->booking_code ?> </td>
                    <td> <?php echo $value->ticket_number; ?> </td>
                    <td> <?php echo $value->ship_class; ?> </td>
                    <td> <?php echo $value->nopol_bok; ?> </td>
                    <td> <?php echo $value->nopol_cek; ?> </td>
                    <td> <?php echo $value->nopol_man; ?> </td>
                    <td> <?php echo $value->nopol_comp; ?> </td>
                    <td> <?php echo $value->panjang_bok; ?> </td>
										<td> <?php echo $value->panjang_cek ?> </td>
                    <td> <?php echo $value->panjang_man; ?> </td>
                    <td> <?php echo $value->panjang; ?> </td>
                    <td> <?php echo $value->lebar_bok; ?> </td>
                    <td> <?php echo $value->lebar_cek; ?> </td>
                    <td> <?php echo $value->lebar_man; ?> </td>
                    <td> <?php echo $value->lebar; ?> </td>
                    <td> <?php echo $value->tinggi_bok; ?> </td>
										<td> <?php echo $value->tinggi_cek ?> </td>
                    <td> <?php echo $value->tinggi_man; ?> </td>
                    <td> <?php echo $value->tinggi; ?> </td>
                    <td> <?php echo $value->batasan; ?> </td>
                    <td> <?php echo $value->hasil_timbang; ?> </td>
                    <td> <?php echo $value->berat_status; ?> </td>
                    <td> <?php echo $value->gol_bok; ?> </td>
                    <td style="width:3%"> <?php echo $value->gol_cek; ?> </td>
										<td> <?php echo $value->gol_man ?> </td>
                    <td> <?php echo $value->gol_comp; ?> </td>
                    <td style="width:4%"> <?php echo $value->user_petugas_loket; ?> </td>
                    <td style="width:3%"> <?php echo $value->nama_loket; ?> </td>
                    <td> <?php echo $value->nama_spv; ?> </td>
                    <td> <?php echo $value->status; ?> </td>
                    <td style="width:4%"> <?php echo $value->dermaga; ?> </td>
                    <td> <?php echo $value->nama_kapal; ?> </td>
										<td> <?php echo $value->waktu; ?> </td>
                    <td> <?php echo $value->keterangan; ?> </td>
                    <td> <?php echo $value->appr_status; ?> </td>
                    <td> <?php echo $value->appr_user; ?> </td>
                    <td> <?php echo $value->appr_tanggal; ?> </td>
                    <td> <?php echo $value->appr_aksi; ?> </td>
                </tr>

            <?php $no++;
            }?>
        </tbody>
		</table>
	<?php  	//} ?>


	<?php

        $total2_produksi=0;
        $total2_tarif_lintasan_tujuan=0;
        $total2_tarif_lintasan_awal=0;
        $total2_selisih_tarif=0;
        $total2_admin_fee=0;
        $total2_reroute_fee=0;
        $total2_charge_fee=0;
    ?>
	<page_footer>
		<i> dicetak pada : <?= format_dateTime(date("Y-m-d H:i:s")) ?> </i>
	</page_footer>
</page>
<!-- Memanggil fungsi bawaan HTML2PDF -->
<?php
$content = ob_get_clean();
// $filename = "Laporan_tiket_terjual";
$filename = strtoupper("Laporan_tiket_refund_");
try {
	// setting paper
	$html2pdf = new HTML2PDF('L', 'A1', 'en', false, 'UTF-8', array(8, 10, 8, 4));
	// $html2pdf->pdf->SetDisplayMode('fullpage');
	$html2pdf->setTestTdInOnePage(false);
	$html2pdf->writeHTML($content);
	$html2pdf->Output($filename="tes" . '.pdf');
} catch (HTML2PDF_exception $e) {
	echo $e;
	exit;
}
?>