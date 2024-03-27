<!-- Setting CSS bagian header/ kop -->
<style type="text/css">
    .tabel {
        border-collapse: collapse;
    }
    
    .tabel th,
    .tabel td {
        padding: 5px 5px;
        border: 0.5px solid #000;
        font-size: 10px;
    }
    
    .tabel th {
        font-weight: normal;
    }
    
    .tabel-no-border tr {
        border: 0.5px solid #000;
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
        border-right: 0.5px solid #000
    }
</style>
<!-- Setting Margin header/ kop -->
<page backtop="22mm" backbottom="-5mm" backleft="-4mm" backright="-4.5mm">
    <page_header>
       <table class="tabel full-width" align="center" style="margin-left:-15px;padding-right:-35px;margin-top:-20px;">
            <tr>
                <td rowspan="4" class="no-border-right" style="width: 20%">
                    <img src="assets/img/asdp-min.png" style="width:100px; height: auto">
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
                <?= $pelabuhan ?>
            </td>
            <td style="border-right: none !important;width: 15%">TANGGAL</td>
            <td style="border-left: none !important;width: 35%">:
                <?= $tanggal ?>
            </td>
        </tr>
        <tr>
            <td style="border-right: none !important;">REGU</td>
            <td style="border-left: none !important;">:
                <?= $regu ?>
            </td>
            <td style="border-right: none !important;">JAM</td>
            <td style="border-left: none !important;">:
                <?= $jam ?>
            </td>
        </tr>
        <tr>
            <td style="border-right: none !important;">SHIFT</td>
            <td style="border-left: none !important;">:
                <?= $shift ?>
            </td>
            <!-- belum naik tiket manual -->
            <!-- <td style="border-right: none !important;">TIPE TIKET</td>
            <td style="border-left: none !important;">: <?= $tipeTiket ?></td> -->

            <td style="border-right: none !important;"></td>
            <td style="border-left: none !important;"></td>
        </tr>
    </table>

    <br>

    <table class="tabel full-width" align="left">
        <tr>
            <col width="10"><th class="center bold" rowspan="2">NO</th>
            <col ><th class="center bold pad-0" rowspan="2">NAMA <br> PERUSAHAAN</th>
            <col ><th class="center bold pad-0" rowspan="2">NAMA <br> KAPAL</th>
            <th class="center bold" rowspan="2">GRT</th>
            <th class="center bold" rowspan="2">TIBA</th>
            <th class="center bold" rowspan="2">BRKT</th>
            <th class="center bold" rowspan="2">DURASI</th>
            <th class="center bold" rowspan="2">DRMG</th>
            <th class="center bold" rowspan="2">CALL</th>
            <th class="center bold" colspan="4">PENUMPANG</th>
            <th class="center bold" rowspan="2">JML <br> PNP</th>
            <th class="center bold" colspan="12">GOLONGAN KENDARAAN</th>
            <th class="center bold" rowspan="2">JML <br> KND</th>
            <th class="center bold" rowspan="2">TOTAL</th>
        </tr>
        <tr>
            <th class="center">DEWASA</th>
            <th class="center">LANSIA</th>
            <th class="center">ANAK</th>
            <th class="center">BAYI</th>
            <th class="center">I</th>
            <th class="center">II</th>
            <th class="center">III</th>
            <th class="center">IVA</th>
            <th class="center">IVB</th>
            <th class="center">VA</th>
            <th class="center">VB</th>
            <th class="center">VIA</th>
            <th class="center">VIB</th>
            <th class="center">VII</th>
            <th class="center">VIII</th>
            <th class="center">IX</th>
            
        </tr>

            <?php
                $total_amount = 0;
                $total_bayi = 0;
                $total_lansia = 0;
                $total_dewasa = $total_anak = $total_pnp = $total_knd = 0;
                $total_gol1 = $total_gol2 = $total_gol3 = $total_gol4A = $total_gol4B = $total_gol5A = $total_gol5B = $total_gol6A = $total_gol6B = $total_gol7 = $total_gol8 = $total_gol9 = 0;
                foreach($data['all_data'] as $y => $value) {
                    $total_dewasa += $value->dewasa;
                    $total_lansia += $value->lansia;
                    $total_anak += $value->anak;
                    $total_bayi += $value->bayi;
                    $total_pnp += $value->totalP;

                    $total_gol1 += $value->gol1;
                    $total_gol2 += $value->gol2;
                    $total_gol3 += $value->gol3;
                    $total_gol4A += $value->gol4A;
                    $total_gol4B += $value->gol4B;
                    $total_gol5A += $value->gol5A;
                    $total_gol5B += $value->gol5B;
                    $total_gol6A += $value->gol6A;
                    $total_gol6B += $value->gol6B;
                    $total_gol7 += $value->gol7;
                    $total_gol8 += $value->gol8;
                    $total_gol9 += $value->gol9;
                    $total_knd += $value->totalK;
                    $total_amount += $value->amount;
                    echo '<tr>';
                    echo '<td class="center">'.($y+1).'</td>';
                    echo '<td class="left">'. wordwrap($value->company,10, '<br />', true)  .'</td>';
                    echo '<td class="left">'. wordwrap($value->ship,10, '<br />', true).'</td>';
                    echo '<td class="right">'.idr_currency($value->ship_grt).'</td>';
                    echo '<td class="center">'.$value->docking_date.'<br>'.$value->docking_time.'</td>';
                    echo '<td class="center">'.$value->sail_date.'<br>'.$value->sail_time.'</td>';
                    echo '<td class="left">'.$value->duration.'</td>';
                    echo '<td class="center">'.$value->dermaga.'</td>';
                    echo '<td class="right">'.$value->call.'</td>';

                    echo '<td class="right">'.idr_currency($value->dewasa).'</td>';
                    echo '<td class="right">'.idr_currency($value->lansia).'</td>';
                    echo '<td class="right">'.idr_currency($value->anak).'</td>';
                    echo '<td class="right">'.idr_currency($value->bayi).'</td>';
                    echo '<td class="right">'.idr_currency($value->totalP).'</td>';

                    echo '<td class="right">'.idr_currency($value->gol1).'</td>';
                    echo '<td class="right">'.idr_currency($value->gol2).'</td>';
                    echo '<td class="right">'.idr_currency($value->gol3).'</td>';
                    echo '<td class="right">'.idr_currency($value->gol4A).'</td>';
                    echo '<td class="right">'.idr_currency($value->gol4B).'</td>';
                    echo '<td class="right">'.idr_currency($value->gol5A).'</td>';
                    echo '<td class="right">'.idr_currency($value->gol5B).'</td>';
                    echo '<td class="right">'.idr_currency($value->gol6A).'</td>';
                    echo '<td class="right">'.idr_currency($value->gol6B).'</td>';
                    echo '<td class="right">'.idr_currency($value->gol7).'</td>';
                    echo '<td class="right">'.idr_currency($value->gol8).'</td>';
                    echo '<td class="right">'.idr_currency($value->gol9).'</td>';
                    echo '<td class="right">'.idr_currency($value->totalK).'</td>';
                    echo '<td class="right">'.idr_currency($value->amount).'</td>';
                    echo '</tr>';
                }
            ?>

        <tr>
            <td class="center bold" colspan=9>JUMLAH</td>
                <?php
                    echo '<td class="right">'.idr_currency($total_dewasa).'</td>';
                    echo '<td class="right">'.idr_currency($total_lansia).'</td>';
                    echo '<td class="right">'.idr_currency($total_anak).'</td>';
                     echo '<td class="right">'.idr_currency($total_bayi).'</td>';
                    echo '<td class="right">'.idr_currency($total_pnp).'</td>';

                    echo '<td class="right">'.idr_currency($total_gol1).'</td>';
                    echo '<td class="right">'.idr_currency($total_gol2).'</td>';
                    echo '<td class="right">'.idr_currency($total_gol3).'</td>';
                    echo '<td class="right">'.idr_currency($total_gol4A).'</td>';
                    echo '<td class="right">'.idr_currency($total_gol4B).'</td>';
                    echo '<td class="right">'.idr_currency($total_gol5A).'</td>';
                    echo '<td class="right">'.idr_currency($total_gol5B).'</td>';
                    echo '<td class="right">'.idr_currency($total_gol6A).'</td>';
                    echo '<td class="right">'.idr_currency($total_gol6B).'</td>';
                    echo '<td class="right">'.idr_currency($total_gol7).'</td>';
                    echo '<td class="right">'.idr_currency($total_gol8).'</td>';
                    echo '<td class="right">'.idr_currency($total_gol9).'</td>';
                    echo '<td class="right">'.idr_currency($total_knd).'</td>';
                    echo '<td class="right">'.idr_currency($total_amount).'</td>';
                ?>
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
            <td class="center" style="text-decoration: underline; padding-bottom: 0">
                <?= $detail_trip->spv ?>
            </td>
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
<!-- Memanggil fungsi bawaan HTML2PDF -->
<?php

$content = ob_get_clean();
$filename = strtoupper('laporan_produksi_kapal_roro_gs_'. $pelabuhan . "_" . trim($tanggal));
try
{
  // setting paper
    $html2pdf = new HTML2PDF('L', 'A4', 'en', false, 'UTF-8', array(8, 10, 8, 8));
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