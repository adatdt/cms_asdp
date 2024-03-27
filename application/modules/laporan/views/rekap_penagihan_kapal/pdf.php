<!-- Setting CSS bagian header/ kop -->
<?php ini_set('memory_limit', '-1'); ?>
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
<page backtop="32mm" backbottom="10mm" backleft="0mm" backright="0mm">
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
            <td style="border-right: none !important;width: 15%">PELABUHAN</td>
            <td style="border-left: none !important;width: 35%">:
                <?= strtoupper($port_name) ?>
            </td>
            <td style="border-right: none !important;width: 15%">TANGGAL</td>
            <td style="border-left: none !important;width: 35%">:
                <?= format_date($date) ?>
            </td>
        </tr>
        <tr>
            <td style="border-right: none !important;">SHIFT</td>
            <td style="border-left: none !important;">:
                <?= $shift_name ?>
            </td>
            <td style="border-right: none !important;">JAM</td>
            <td style="border-left: none !important;">:
                <?= $shift_time ?>
            </td>
        </tr>
    </table>

    <br>

    <table class="tabel full-width" align="center">
        <tr>
            <th class="center bold" style="width: 5%">NO</th>
            <th class="center bold" style="width: 25%">NAMA PERUSAHAAN /<br> NAMA KAPAL</th>
            <th class="center bold" style="width: 10%">JLH <br>TRIP</th>
            <th class="center bold" style="width: 15%">Penumpang</th>
            <th class="center bold" style="width: 15%">Kendaraan</th>
            <th class="center bold" style="width: 15%">KSO <br>PT. ASDP</th>
            <th class="center bold" style="width: 15%">JUMLAH</th>
        </tr>
        <tr>
            <td class="center" style="padding: 2px 8px">1</td>
            <td class="center" style="padding: 2px 8px">2</td>
            <td class="center" style="padding: 2px 8px">3</td>
            <td class="center" style="padding: 2px 8px">4</td>
            <td class="center" style="padding: 2px 8px">5</td>
            <td class="center" style="padding: 2px 8px">6</td>
            <td class="center" style="padding: 2px 8px">7</td>
        </tr>
        <?php 
            $totalTrip = 0;
            $totalPenumpang = 0;
            $totalVehicle = 0;
            $total = 0;

            foreach ($data as $x => $value) { 
                $totalTrip += $value->qty;
                $totalPenumpang += $value->penumpang;
                $totalVehicle += $value->vehicle;
                $total += ($value->penumpang + $value->vehicle);
        ?>
            <tr>
                <td class="center">
                    <?= ($x+1) ?>
                </td>
                <td class="left">
                    <?= $value->ship_name ?>
                </td>
                <td class="center">
                    <?= idr_currency($value->qty) ?>
                </td>
                <td class="right">
                    <?= ($value->penumpang == 0) ? '-' : idr_currency($value->penumpang) ?>
                </td>
                <td class="right">
                    <?= ($value->vehicle == 0) ? '-' : idr_currency($value->vehicle) ?>
                </td>
                <td class="right"> - </td>
                <td class="right">
                    <?= (($value->vehicle+$value->penumpang) == 0 ) ? '-' : idr_currency($value->vehicle+$value->penumpang) ?>
                </td>
            </tr>
        <?php } ?>
        <tr>
            <td class="center" style="padding: 2px 8px">
                
            </td>
            <td class="center bold" style="padding: 2px 8px">
                JUMLAH
            </td>
            <td class="center bold" style="padding: 2px 8px">
                <?= $totalTrip ?>
            </td>
            <td class="right bold" style="padding: 2px 8px">
                <?= idr_currency($totalPenumpang) ?>
            </td>
            <td class="right bold" style="padding: 2px 8px">
                <?= idr_currency($totalVehicle) ?>
            </td>
            <td class="right bold" style="padding: 2px 8px">
                -
            </td>
            <td class="right bold" style="padding: 2px 8px">
                <?= idr_currency($total) ?>
            </td>
        </tr>
    </table>
    
    <br>
    <br>
    <table class="table table-no-border full-width" align="center">
        <tr>
            <td class="center" style="padding-bottom: 0;width: 33%"></td>
            <th class="center" style="width: 33%"></th>
            <td class="center" style="padding-bottom: 0"><?php echo ucwords(strtolower($port_name)) . ", " . format_date($date); ?></td>
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
            <td class="center" style="text-decoration: underline; padding-bottom: 0">
                <?= $spv ?>
            </td>
        </tr>
        <tr>
            <td class="center"></td>
            <td class="center"></td>
            <td class="center">NIK. .................................</td>
        </tr>
    </table>

</page>
<!-- Memanggil fungsi bawaan HTML2PDF -->
<?php
$content = ob_get_clean();
$filename = 'rekapitulasi_rekap_kapal';
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