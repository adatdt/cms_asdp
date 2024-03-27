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
                <!-- <img src="http://172.16.0.11/asdp-admin/assets/img/asdp.png" style="width:100px; height: auto"> -->
            </td>
            <td rowspan="4" class="center bold" style="width: 50%;font-size:14px; line-height: 1.5; vertical-align: middle">
                <?= $title ?>
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
            <td style="border-right: none !important;width: 80px">NAMA KAPAL</td>
            <td style="border-left: none !important;">: KMP. <?= strtoupper($detail_trip->ship_name)  ?></td>
            <td style="border-right: none !important;width: 80px">LINTASAN</td>
            <td style="border-left: none !important;width:225px;">: <?= $detail_trip->trip." (".$detail_trip->ship_class.")"  ?></td>
        </tr>
        <tr>
            <td style="border-right: none !important;">PERUSAHAAN</td>
            <td style="border-left: none !important;word-break: break-all;width: 230px">: <?= $detail_trip->company_name  ?></td>
            <td style="border-right: none !important;">DERMAGA</td>
            <td style="border-left: none !important;">: <?= $detail_trip->dock_name ?></td>
        </tr>
        <tr>
            <td style="border-right: none !important;">CABANG</td>
            <td style="border-left: none !important;">: <?= $detail_trip->port_name." (".$detail_trip->ship_class.")"  ?></td>
            <td style="border-right: none !important;">TANGGAL</td>
            <td style="border-left: none !important;">: <?= format_date(($detail_trip->sail_date == '') ? date('Y-m-d H:i:s'):$detail_trip->sail_date) ?></td>
        </tr>
        <tr>
            <td style="border-right: none !important;">PELABUHAN</td>
            <td style="border-left: none !important;">: <?= $detail_trip->port_name ?></td>
            <td style="border-right: none !important;">JAM</td>
            <td style="border-left: none !important;">: <?= format_time(($detail_trip->sail_date == '') ? date('Y-m-d H:i:s'):$detail_trip->sail_date) ?></td>
        </tr>
    </table>

    <br>
    <table class="tabel full-width" align="center">
        <tr>
          <th class="center bold" style="width: 5%">NO</th>
          <th class="center bold" style="width: 35%">JENIS TIKET</th>
          <th class="center bold" style="width: 15%">TARIF<br></th>
          <th class="center bold" style="width: 15%">PRODUKSI<br>(Lbr)</th>
          <th class="center bold" style="width: 15%">PENDAPATAN<br>(Rp)</th>
          <th class="center bold" style="width: 15%">KETERANGAN</th>
        </tr>
        <tr>
            <td class="center" style="padding: 2px 8px">1</td>
            <td class="center" style="padding: 2px 8px">2</td>
            <td class="center" style="padding: 2px 8px">3</td>
            <td class="center" style="padding: 2px 8px">4</td>
            <td class="center" style="padding: 2px 8px">5</td>
            <td class="center" style="padding: 2px 8px">6</td>
        </tr>
        <tr>
            <td class="center">1</td>
            <td colspan="5">PENUMPANG</td>
        </tr>
        <?php 

        $totalTrip = 0;
        $totalAmount = 0;
        $totalAdmFee = 0;

        foreach ($detail_passenger as $key_pnp => $pnp) { 
          $totalTrip += $pnp->ticket_count;
          $totalAmount += $pnp->total_amount;
          $totalAdmFee += $pnp->adm_fee;
        ?>
        <tr>
            <td></td>
            <td><?= $pnp->passanger_type_name?></td>
            <td class="right"><?= idr_currency($pnp->trip_fee) ?></td>
            <td class="right"><?= idr_currency($pnp->ticket_count) ?></td>
            <td class="right"><?= idr_currency($pnp->total_amount) ?></td>
            <td class="right"></td>
        </tr>
       <?php } ?>
       <tr>
           <th class="center"></th>
           <th colspan="2" class="bold">Sub Jumlah</th>
           <th class="right bold"><?= idr_currency($totalTrip) ?></th>
           <th class="right bold"><?= idr_currency($totalAmount) ?></th>
           <th></th>
       </tr>
        <tr>
           <td class="center">2</td>
           <td colspan="5">KENDARAAN</td>
       </tr>
       <?php 
        $totalTripVehicle = 0;
        $totalAmountVehicle = 0;
        $totalAdmFeeVehicle = 0;
        foreach ($detail_vehicle as $key_vhc => $vhc) { 
          $totalTripVehicle += $vhc->ticket_count;
          $totalAmountVehicle += $vhc->total_amount;
          $totalAdmFeeVehicle += $vhc->adm_fee;
        ?>
        <tr>
            <td></td>
            <td><?= $vhc->vehicle_type_name?></td>
            <td class="right"><?= idr_currency($vhc->trip_fee) ?></td>
            <td class="right"><?= idr_currency($vhc->ticket_count) ?></td>
            <td class="right"><?= idr_currency($vhc->total_amount) ?></td>
            <td class="right"></td>
        </tr>
       <?php } ?>
       <tr>
           <th class="center"></th>
           <th colspan="2" class="bold">Sub Jumlah</th>
           <th class="right bold"><?= idr_currency($totalTripVehicle) ?></th>
           <th class="right bold"><?= idr_currency($totalAmountVehicle) ?></th>
           <th></th>
       </tr>
       <tr>
           <th colspan="3" class="center bold">Jumlah (Penumpang + Kendaraan)</th>
           <th class="right bold"><?= idr_currency($totalTrip+$totalTripVehicle) ?></th>
           <th class="right bold"><?= idr_currency($totalAmount+$totalAmountVehicle) ?></th>
           <th></th> 
       </tr>
       <tr>
           <td class="center">3</td>
           <td colspan="5">BEA JASA PELABUHAN</td>
       </tr>
       <tr>
           <td></td>
           <td>a. Jasa Adm. Tiket </td>
           <td colspan="3" class="right"><?php echo idr_currency($adm_tiket = $totalAdmFee+$totalAdmFeeVehicle) ?></td>
           <td></td>
       </tr>
       <tr>
           <td></td>
           <td>b. Jasa Sandar</td>
           <td colspan="3" class="right"><?= idr_currency($dock_fare->dock_service) ?></td>
           <td></td>
       </tr>
       <tr>
           <td></td>
           <td>c. Jasa Kepil</td>
           <td colspan="3" class="right"><?php echo idr_currency($jasa_kepil) ?></td>
           <td></td>
       </tr>
       <tr>
           <th class="center bold" colspan="2">Jumlah</th>
           <th colspan="3" class="right bold"><?= idr_currency($bea = ($dock_fare->dock_service + $adm_tiket+$jasa_kepil)) ?></th>
           <th></th>
       </tr>
    </table>
    <br>
    <br>
    <table class="table table-no-border full-width" align="center">
        <tr>
            <td class="center" style="padding-bottom: 0;width: 33%">Dibuat oleh,</td>
            <th class="center" style="width: 33%"></th>
            <td class="center" style="padding-bottom: 0;width: 33%">Mengetahui,</td>
        </tr>
        <tr>
            <th class="center">Petugas Klaim</th>
            <th class="center">Operator Pelayaran</th>
            <th class="center">Supervisor</th>
        </tr>
        <tr>
            <th class="center"></th>
            <th class="center"><br><br></th>
            <th class="center"></th>
        </tr>
        <tr>
            <td class="center" style="text-decoration: underline; padding-bottom: 0">.........................................</td>
            <td class="center" style="text-decoration: underline; padding-bottom: 0">.........................................</td>
            <td class="center" style="text-decoration: underline; padding-bottom: 0">.........................................</td>
        </tr>
        <tr>
            <td class="center">NIK. .................................</td>
            <td class="center">NIK. .................................</td>
            <td class="center">NIK. .................................</td>
        </tr>
    </table>

    <page_footer>
      <i> dicetak pada : <?= format_dateTime(date("Y-m-d H:i:s")) ?> </i>
    </page_footer>

</page>
<!-- Memanggil fungsi bawaan HTML2PDF -->
<?php
$content = ob_get_clean();
$filename = strtoupper("Rekap_muatan_perkapal_" . $detail_trip->sail_date);
try
{
  // setting paper
    $html2pdf = new HTML2PDF('P', 'A4', 'en', false, 'UTF-8', array(12, 10, 12, 8));
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
