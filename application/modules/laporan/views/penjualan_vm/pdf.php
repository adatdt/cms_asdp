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

    .tabel-min td, .tabel-min th {
        padding: 3px 2px;
        border: 1px solid #000;
        font-size: 8px;
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

    <table class="tabel tabel-min full-width" align="center">
        <tr>
            <th class="center bold" style="width: 2%">NO</th>
            <th class="center bold" style="">NAMA PETUGAS</th>
            <th class="center bold" style="">USERNAME</th>
            <th class="center bold" style="">LOKET</th>
            <th class="center bold" style="">KODE BOOKING</th>
            <th class="center bold" style="">NOMOR TIKET</th>
            <th class="center bold" style="">GOLONGAN</th>
            <th class="center bold" style="">METODE BAYAR</th>
            <th class="center bold" style="">KELAS</th>
            <th class="center bold" style="">TGL. SHIFT</th>
            <th class="center bold" style="">SHIFT</th>
            <th class="center bold" style="">REGU</th>
            <?php if($service == 'knd'){ ?>
                <th class="center bold" style="">NO POLISI</th>
            <?php } ?>
            <th class="center bold" style="">PENGGUNA JASA</th>
            <th class="center bold" style="">NO IDENTITAS</th>
            <th class="center bold" style="">KAPAL</th>
            <th class="center bold" style="">TANGGAL KLAIM</th>
            <th class="center bold" style="">TARIF (Rp)</th>
        </tr>
        <?php
            $total = 0;
            foreach ($data as $x => $value) { 
                $total += $value->tarif;
                // if($x==5){
                //     exit();
                // }
        ?>
            <tr>
                <td class="center">
                    <?= ($x+1) ?>
                </td>
                <td class="left">
                    <?= $value->first_name ?>
                </td>
                <td class="left">
                    <?= $value->username ?>
                </td>
                <td class="left">
                    <?= $value->loket ?>
                </td>
                <td class="left">
                    <?= $value->booking_code ?>
                </td>
                <td class="left">
                    <?= $value->ticket_number ?>
                </td>
                <td class="left">
                    <?= $value->golongan ?>
                </td>
                <td class="left">
                    <?= $value->payment_type ?>
                </td>
                <td class="left">
                    <?= $value->kelas ?>
                </td>
                <td class="left">
                    <?= $value->trans_date ?>
                </td>
                <td class="left">
                    <?= $value->shift ?>
                </td>
                <td class="left">
                    <?= $value->regu ?>
                </td>
                <?php if($service == 'knd'){ ?>
                    <td class="left">
                        <?= $value->plat ?>
                    </td>
                <?php } ?>
                <td class="left">
                    <?= $value->customer_name ?>
                </td>
                <td class="left">
                    <?= $value->id_number ?>
                </td>
                <td class="left">
                    <?= $value->ship ?>
                </td>
                <td class="left">
                    <?= $value->naik_kapal ?>
                </td>
                <td class="right">
                    <?= idr_currency($value->tarif) ?>
                </td>

            </tr>
        <?php } ?>
        <tr>
            <?php if($service == 'pnp'){ ?>
                <td colspan="15" style="padding: 2px 8px"></td>
            <?php } else if($service == 'knd'){ ?>
                <td colspan="16" style="padding: 2px 8px"></td>
            <?php } ?>
            <td>
                Total
            </td>
            <td class="right bold" style="padding: 2px 8px">
                <?= idr_currency($total) ?>
            </td>
        </tr>
    </table>

</page>
<!-- Memanggil fungsi bawaan HTML2PDF -->
<?php
$content = ob_get_clean();
if($service == 'pnp'){
    $filename = 'Penjualan petugas loket Pejalan Kaki_'.$date->dateFrom.'_'.$date->dateTo;
} else if($service == 'knd'){
    $filename = 'Penjualan petugas loket Kendaraan_'.$date->dateFrom.'_'.$date->dateTo;
}
try
{
  // setting paper
    $html2pdf = new HTML2PDF('L', 'A4', 'en', false, 'UTF-8', array(12, 10, 12, 8));
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
