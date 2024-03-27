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
<page backtop="27mm" backbottom="8mm" backleft="0mm" backright="0mm">
    <page_header>
       <table class="tabel full-width" align="center">
            <tr>
                <td rowspan="4" class="no-border-right" style="width: 20%">
                    <img src="assets/img/asdp.png" style="width:100px; height: auto">
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
            <td style="border-right: none !important;">REGU</td>
            <td style="border-left: none !important;">:
                <?= $team_name ?>
            </td>
            <td style="border-right: none !important;">JAM</td>
            <td style="border-left: none !important;">:
                <?= $shift_time ?>
            </td>
        </tr>
    </table>

    <br>

    <table class="tabel full-width" align="left">
        <tr>
            <th class="center bold" rowspan=2 style="width: 3%">NO</th>
            <th class="center bold" rowspan=2 style="width: 15%">NAMA KAPAL</th>
            <th class="center bold" rowspan=2 style="width: 7%">TRIP</th>
            <th class="center bold" colspan=<?= count($dock) ?> >PENDAPATAN JASA TAMBAT PER DERMAGA</th>
            <th class="center bold" rowspan=2 style="width: 10%">JUMLAH</th>
        </tr>
        <tr>
            <?php 
                $width = 65/(count($dock));
                foreach($dock as $x => $value) {
                    echo '<td class="center" style="padding: 2px 8px; width:'.$width.'%">'.$value->name.'</td>';
                }
            ?>
        </tr>
        
            <?php
                $total_trip = 0;
                $total_duit_bawah =0;
                foreach($all_data as $y => $value) {
                    $total_trip += $value->trip;
                   
                    $total_tambat = 0;

                    echo '<tr>';
                    echo '<td class="center">'.($y+1).'</td>';
                    echo '<td class="left">'.$value->ship_name.'</td>';

                    echo '<td class="right">'.$value->trip.'</td>';

                    $data_dock = json_decode($value->dock, true);
                        $jumlah_kanan = 0;
                        foreach ($dock as $key => $value) {
                            if(array_key_exists($value->id, $data_dock)){
                                $dock[$key]->total += $exp[1];
                                $exp = explode("-", $data_dock[$value->id]);
                                $jumlah_kanan += $exp[1];
                                echo '<td class="right">'.idr_currency($exp[1]).'</td>';
                            }else{
                                echo '<td class="right">-</td>';
                            }
                        }
                        $total_duit_bawah += $jumlah_kanan;

                    echo '<td class="right">'.idr_currency($jumlah_kanan).'</td>';
                    echo '</tr>';
                }
            ?>

        <tr>
            <th class="center bold" rowspan=2 style="width: 3%"></th>
            <th class="center bold" rowspan=2 style="width: 15%">JUMLAH</th>
            <th class="right bold" rowspan=2 style="width: 7%"><?=$total_trip ?></th>
                <?php 
                    $width = 65/(count($dock));
                   
                    foreach($dock as $x => $value) {
                        echo '<td class="center" style="padding: 2px 8px; width:'.$width.'%">'.idr_currency($value->total).'</td>';  
                    }
                ?>
            <th class="right bold" rowspan=2 style="width: 10%"><?= idr_currency($total_duit_bawah) ?></th>
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

</page>
<!-- Memanggil fungsi bawaan HTML2PDF -->
<?php
$content = ob_get_clean();
$filename = 'laporan_jasa_tambat';
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