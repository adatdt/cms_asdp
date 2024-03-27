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
            <td rowspan="4" class="center bold" style="width: 42%;font-size:14px; line-height: 1.5; vertical-align: middle">
                <?= $report_title ?>
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

    <!-- Setting Footer -->
    <page_footer>
    <table class="page_footer" align="center">

    </table>
    </page_footer>
    <br>
    <table class="tabel full-width" align="center">
        <tr>
            <td style="border-right: none !important;width: 15%">CABANG</td>
            <td style="border-left: none !important;width: 35%">:
                <?= strtoupper($header->branch_name)  ?>
            </td>
            <td style="border-right: none !important;width: 15%">SHIFT</td>
            <td style="border-left: none !important;width: 35%">:
                <?=$header->shift_name ?>
            </td>
        </tr>
        <tr>
            <td style="border-right: none !important;">PELABUHAN</td>
            <td style="border-left: none !important;">:
                <?=$header->port ?>
            </td>
            <td style="border-right: none !important;">REGU</td>
            <td style="border-left: none !important;">:
                <?=$header->team_name ?>
            </td>
        </tr>
        <tr>
            <td style="border-right: none !important;">LINTASAN</td>
            <td style="border-left: none !important;">:
                <?=$header->origin ?> - <?=$header->destination ?>
            </td>
            <td style="border-right: none !important;">TANGGAL</td>
            <td style="border-left: none !important;">:
                <?= format_date($header->shift_date) ?>
            </td>
        </tr>
    </table>

    <br>
    <table class="tabel full-width" align="center">
        <tr>
            <th class="center bold" style="width: 5%">NO</th>
            <th class="center bold" style="width: 35%">JENIS TIKET</th>
            <th class="center bold" style="width: 15%">TARIF
                <br/>(JRP)</th>
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
                    <?php
                    $produksi_penumpang = 0;
                    $pendapatan_penumpang = 0;

                    foreach ($penumpang as $key_pnp => $pnp) {
                        $produksi_penumpang += $pnp->produksi;
                        $pendapatan_penumpang += $pnp->responsibility_fee;
                        ?>
                        <tr>
                            <td></td>
                            <td><?= $pnp->golongan?></td>
                            <td class="right"><?= idr_currency($pnp->harga) ?></td>
                            <td class="right"><?= idr_currency($pnp->produksi) ?></td>
                            <td class="right"><?= idr_currency($pnp->responsibility_fee) ?></td>
                            <td class="right"></td>
                        </tr>
                    <?php } ?>

                    <tr>
                        <th class="center"></th>
                        <th colspan="2" class="bold">Sub Jumlah</th>
                        <th class="right bold"><?=idr_currency($produksi_penumpang) ?></th>
                        <th class="right bold"><?=idr_currency($pendapatan_penumpang) ?></th>
                        <th></th>
                    </tr>
                    <tr>
                        <td class="center">2</td>
                        <td colspan="5">KENDARAAN</td>
                    </tr>
                    <?php
                    $produksi_kendaraan = 0;
                    $pendapatan_kendaraan = 0;
                    foreach ($kendaraan as $key_vhc => $vhc) {
                        $produksi_kendaraan += $vhc->produksi;
                        $pendapatan_kendaraan += $vhc->responsibility_fee;
                        ?>
                        <tr>
                            <td></td>
                            <td><?= $vhc->golongan?></td>
                            <td class="right"><?= idr_currency($vhc->harga) ?></td>
                            <td class="right"><?= idr_currency($vhc->produksi) ?></td>
                            <td class="right"><?= idr_currency($vhc->responsibility_fee) ?></td>
                            <td class="right"></td>
                        </tr>
                    <?php } ?>
                    <tr>
                        <th class="center"></th>
                        <th colspan="2" class="bold">Sub Jumlah</th>
                        <th class="right bold"><?=idr_currency($produksi_kendaraan) ?></th>
                        <th class="right bold"><?=idr_currency($pendapatan_kendaraan) ?></th>
                        <th></th>
                    </tr>

                    <tr>
                        <th colspan="3" class="center bol">Jumlah</th>
                        <th class="right bold"><?= idr_currency($produksi_penumpang+$produksi_kendaraan) ?></th>
                        <th class="right bold"><?= idr_currency($pendapatan_penumpang+$pendapatan_kendaraan) ?></th>
                        <th></th> 
                    </tr>
                </table>
                <br>
                <div>Fee Administrasi Asuransi Jasa Raharja Putra 15,4% = <?= idr_currency($pendapatan_penumpang+$pendapatan_kendaraan) ?></div>

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

            </page>
            <?php
            $content = ob_get_clean();
            $filename = "Asuransi_jrp";
            try
            {
                $html2pdf = new HTML2PDF('P', 'A4', 'en', false, 'UTF-8', array(8, 10, 8, 8));
                $html2pdf->setTestTdInOnePage(false);
                $html2pdf->writeHTML($content);
                $html2pdf->Output($filename.'.pdf');
            }
            catch(HTML2PDF_exception $e) {
                echo $e;
                exit;
            }
            ?>