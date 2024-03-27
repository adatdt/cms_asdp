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
            <td style="border-left: none !important;">: <?= $kapal  ?></td>
            <td style="border-right: none !important;width: 80px">LINTASAN</td>
            <td style="border-left: none !important;width:225px;">: <?= $lintasan ?></td>
        </tr>
        <tr>
            <td style="border-right: none !important;">PERUSAHAAN</td>
            <td style="border-left: none !important;word-break: break-all;width: 230px">: <?= $perusahaan ?></td>
            <td style="border-right: none !important;">DERMAGA</td>
            <td style="border-left: none !important;">: <?= $dermaga ?></td>
        </tr>
        <tr>
            <td style="border-right: none !important;">CABANG</td>
            <td style="border-left: none !important;">: <?= $pelabuhan ?></td>
            <td style="border-right: none !important;">TANGGAL</td>
            <td style="border-left: none !important;">: <?= format_date($this->input->get('datefrom')) . " - " . format_date($this->input->get('dateto')) ?></td>
        </tr>
        <tr>
            <td style="border-right: none !important;">PELABUHAN</td>
            <td style="border-left: none !important;">: <?= $pelabuhan ?></td>
            <td style="border-right: none !important;">JAM</td>
            <td style="border-left: none !important;">: <?= $jam ?></td>
        </tr>
        <tr>
            <td style="border-right: none !important;">JUMLAH TRIP</td>
            <td style="border-left: none !important;">: <?= $jumlah_trip ?></td>
            <td style="border-right: none !important;">STATUS</td>
            <td style="border-left: none !important;">: <b><?= $status_approve ?></b></td>
        </tr>
    </table>

    <br>
    <table class="tabel full-width" align="center">
        <tr>
          <th class="center bold" style="width: 5%">NO</th>
          <th class="center bold" style="width: 35%">JENIS TIKET</th>
          <th class="center bold" style="width: 15%">TARIF</th>
          <th class="center bold" style="width: 15%">PRODUKSI</th>
          <th class="center bold" style="width: 15%">PENDAPATAN</th>
          <th class="center bold" style="width: 15%">KETERANGAN</th>
        </tr>
        <tr>
            <td class="center">1</td>
            <td colspan="5">PENUMPANG</td>
        </tr>
        <?php 

        $produksi_penumpang = 0;
        $pendapatan_penumpang = 0;
        $totalAdmFee = 0;

        foreach ($penumpang as $key_pnp => $pnp) { 
          $produksi_penumpang += $pnp->produksi;
          $pendapatan_penumpang += $pnp->pendapatan;
          $totalAdmFee += $pnp->adm_fee;
        ?>
        <tr>
            <td></td>
            <td><?= $pnp->golongan?></td>
            <td class="right"><?= idr_currency($pnp->harga) ?></td>
            <td class="right"><?= idr_currency($pnp->produksi) ?></td>
            <td class="right"><?= idr_currency($pnp->pendapatan) ?></td>
            <td class="right"></td>
        </tr>
       <?php } ?>
       <tr>
           <th class="center"></th>
           <th colspan="2" class="bold">Sub Jumlah</th>
           <th class="right bold"><?= idr_currency($produksi_penumpang) ?></th>
           <th class="right bold"><?= idr_currency($pendapatan_penumpang) ?></th>
           <th></th>
       </tr>
        <tr>
           <td class="center">2</td>
           <td colspan="5">KENDARAAN</td>
       </tr>
       <?php 
        $produksi_kendaraan = 0;
        $pendapatan_kendaraan = 0;
        $totalAdmFeeVehicle = 0;

        foreach ($kendaraan as $key_vhc => $vhc) { 
          $produksi_kendaraan += $vhc->produksi;
          $pendapatan_kendaraan += $vhc->pendapatan;
          $totalAdmFeeVehicle += $vhc->adm_fee;
        ?>
        <tr>
            <td></td>
            <td><?= $vhc->golongan?></td>
            <td class="right"><?= idr_currency($vhc->harga) ?></td>
            <td class="right"><?= idr_currency($vhc->produksi) ?></td>
            <td class="right"><?= idr_currency($vhc->pendapatan) ?></td>
            <td class="right"></td>
        </tr>
       <?php } ?>
       <tr>
           <th class="center"></th>
           <th colspan="2" class="bold">Sub Jumlah</th>
           <th class="right bold"><?= idr_currency($produksi_kendaraan) ?></th>
           <th class="right bold"><?= idr_currency($pendapatan_kendaraan) ?></th>
           <th></th>
       </tr>
       <tr>
           <th colspan="3" class="center bold">Jumlah (Penumpang + Kendaraan)</th>
           <th class="right bold"><?= idr_currency($produksi_penumpang+$produksi_kendaraan) ?></th>
           <th class="right bold"><?= idr_currency($pendapatan_penumpang+$pendapatan_kendaraan) ?></th>
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
           <td colspan="3" class="right"><?= idr_currency($dock_fare) ?></td>
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
           <th colspan="3" class="right bold"><?= idr_currency($bea = ($dock_fare + $adm_tiket+$jasa_kepil)) ?></th>
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
$filename = strtoupper("Rekap_muatan_perkapal_v2" . "_" . $this->input->get('datefrom') . "_" .  $this->input->get('dateto'));
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
