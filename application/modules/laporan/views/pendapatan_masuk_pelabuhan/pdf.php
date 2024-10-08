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
<page backtop="10mm" backbottom="10mm" backleft="8mm" backright="8mm">
    <page_header>
    </page_header>
        <!-- Setting Header -->
        <table class="tabel full-width" align="center">
            <tr>
                <td rowspan="4" class="no-border-right" style="width: 10%">
                    <img src="assets/img/asdp.png" style="width:100px; height: auto">
                </td>
                <td rowspan="4" class="center bold" style="width: 48.5%;font-size:14px; line-height: 1.5; vertical-align: middle">
                    <?php echo $title ?>
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
        <br>

    <!-- Setting Footer -->
    <page_footer>
        <table class="page_footer" align="center">

        </table>
    </page_footer>
    
    <table class="tabel full-width" align="center">
        <tr>
            <td style="border-right: none !important;width: 15%">CABANG</td>
            <td style="border-left: none !important;width: 35%">:
                <?php echo strtoupper($param[3])  ?>
            </td>
            <td style="border-right: none !important;width: 15%">SHIFT</td>
            <td style="border-left: none !important;width: 35%">:
                <?php echo strtoupper($param[7])  ?>
            </td>
        </tr>
        <tr>
            <td style="border-right: none !important;">PELABUHAN</td>
            <td style="border-left: none !important;">:
                <?php echo strtoupper($param[4])  ?>
            </td>
            <td style="border-right: none !important;">REGU</td>
            <td style="border-left: none !important;">:
                <?php echo $param[8] ?>
            </td>
        </tr>
        <tr>
            <td style="border-right: none !important;">LINTASAN</td>
            <td style="border-left: none !important;">:
                <?php echo strtoupper($param[5]).' - '.strtoupper($param[6])  ?>
            </td>
            <td style="border-right: none !important;">TANGGAL</td>
            <td style="border-left: none !important;">:
                <?php echo format_date($param[0]) ?>
            </td>
        </tr>
    </table>

    <br>
    <table class="tabel full-width" align="center">
        <tr>
            <th class="center bold" style="width: 5%">NO</th>
            <th class="center bold" style="width: 35%">JENIS TIKET</th>
            <th class="center bold" style="width: 15%">TARIF
                <br/>(PAS)</th>
            <th class="center bold" style="width: 15%">PRODUKSI
                <br/>(Lbr)</th>
            <th class="center bold" style="width: 15%">PENDAPATAN
                <br/>(Rp)</th>
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
        <?php foreach ($pass['data'] as $pnp) { ?>
            <tr>
                <td></td>
                <td>
                    <?php echo $pnp->name?>
                </td>
                <td class="right">
                    <?php echo idr_currency($pnp->entry_fee) ?>
                </td>
                <td class="right">
                    <?php echo idr_currency($pnp->ticket_count) ?>
                </td>
                <td class="right">
                    <?php echo idr_currency($pnp->total_amount) ?>
                </td>
                <td class="right"></td>
            </tr>
        <?php } ?>
        <tr>
            <th class="center"></th>
            <th colspan="2" class="bold">Sub Jumlah</th>
            <th class="right bold"><?php echo idr_currency($pass['produksi']) ?></th>
            <th class="right bold"><?php echo idr_currency($pass['pendapatan']) ?></th>
            <th></th>
        </tr>
        <tr>
            <td class="center">2</td>
            <td colspan="5">KENDARAAN</td>
        </tr>
        <?php foreach ($veh['data'] as $det) { ?>
            <tr>
                <td></td>
                <td>
                    <?php echo $det->name?>
                </td>
                <td class="right">
                    <?php echo idr_currency($det->entry_fee) ?>
                </td>
                <td class="right">
                    <?php echo idr_currency($det->ticket_count) ?>
                </td>
                <td class="right">
                    <?php echo idr_currency($det->total_amount) ?>
                </td>
                <td class="right"></td>
            </tr>
        <?php } ?>
        <tr>
            <th class="center"></th>
            <th colspan="2" class="bold">Sub Jumlah</th>
            <th class="right bold"><?php echo idr_currency($veh['produksi']) ?></th>
            <th class="right bold"><?php echo idr_currency($veh['pendapatan']) ?></th>
            <th></th>
        </tr>
        <tr>
            <th colspan="3" class="bold center">Jumlah</th>
            <th class="right bold"><?php echo idr_currency($pass['produksi']+$veh['produksi']) ?></th>
            <th class="right bold"><?php echo idr_currency($pass['pendapatan']+$veh['pendapatan']) ?></th>
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
            <td class="center" style="text-decoration: underline; padding-bottom: 0">
                <?php echo $param[9] ?>
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
$filename = 'Pendapatan_pas_masuk_pelabuhan_'.$param[0].'_'.$param[8].'_'.$param[7];
try
{
  // setting paper
    $html2pdf = new HTML2PDF('P', 'A4', 'en', false, 'UTF-8', array(8, 10, 8, 8));
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
