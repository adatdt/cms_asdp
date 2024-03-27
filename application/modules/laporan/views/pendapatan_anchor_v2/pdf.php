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

    .col-title {
        text-align: center; 
        vertical-align: middle;
    }
</style>
<!-- Setting Margin header/ kop -->
<?php
    $detail = $data['detail'];
?>

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
                <?php echo $detail->origin ?>
            </td>
            <td style="border-right: none !important;width: 15%">KAPAL</td>
            <td style="border-left: none !important;width: 35%">:
                <?php echo $detail->ship_name  ?>
            </td>
        </tr>
        <tr>
            <td style="border-right: none !important;">PELABUHAN</td>
            <td style="border-left: none !important;">:
                <?php echo $detail->origin.' '.$detail->class_name  ?>
            </td>
            <td style="border-right: none !important;">PERUSAHAAN</td>
            <td style="border-left: none !important;">:
                <?php echo wordwrap( $detail->company_name, 25, '<br />', true) ?>
            </td>
        </tr>
        <tr>
            <td style="border-right: none !important;">LINTASAN</td>
            <td style="border-left: none !important;">:
                <?php echo $detail->origin.' - '.$detail->destination ?>
            </td>
            <td style="border-right: none !important;">GRT</td>
            <td style="border-left: none !important;">:
                <?php echo $detail->ship_grt ?>
            </td>
        </tr>
        <tr>
            <td style="border-right: none !important;">TANGGAL</td>
            <td style="border-left: none !important;">:
                <?php echo $data['tanggal'] ?>
            </td>
            <td style="border-right: none !important;">SHIFT</td>
            <td style="border-left: none !important;">:
                <?php echo $data['shift_name'] ?>
            </td>
        </tr>
    </table>

    <br>
    <table class="tabel full-width" align="center">
        <!-- <tr> -->
            <!-- <td style="text-align: center; width: 5% !important;">NO</td>
            <td style="text-align: center; width: 95% !important;">TANGGAL</td>-->

            <!-- <th align="center" style="width: 10%">DERMAGA</th>
            <th align="center" style="width: 10%">TARIF</th>
            <th align="center" style="width: 10%">CALL ENGKER</th>
            <th align="center" style="width: 50%">PRODUKSI<br>(GRT/CALL)</th>
            <th align="center" style ="width: 50%">PENDAPATAN</th> -->
        <!-- </tr> -->
         <tr>
            <td class="col-title bold" style="width: 5%">NO</td>
            <td class="col-title bold" style="width: 20%">TANGGAL ENGKER</td>
            <td class="col-title bold" style="width: 15%">DERMAGA</td>
            <td class="col-title bold" style="width: 15%">TARIF</td>
            <td class="col-title bold" style="width: 15%">CALL ENGKER</td>
            <td class="col-title bold" style="width: 15%">PRODUKSI<br>(GRT/CALL)</td>
            <td class="col-title bold" style="width: 15%">PENDAPATAN</td>
        </tr>

        <?php foreach ($data['data'] as $row) { ?>
            <tr>
                <td class="center"><?php echo $row->no; ?></td>
                <td class="center"><?php echo $row->date; ?></td>
                <td class="center"><?php echo $row->dock_name; ?></td>
                <td class="right"><?php echo idr_currency($row->dock_fare); ?></td>
                <td class="right"><?php echo idr_currency($row->call_anchor); ?></td>
                <td class="right"><?php echo idr_currency($row->ship_grt); ?></td>
                <td class="right"><?php echo idr_currency($row->total); ?></td>
            </tr>
        <?php } ?>
        <tr>
            <td class="right bold" colspan="4">SUB TOTAL</td>
            <td class="right bold"><?php echo idr_currency($data['total_anchor']) ?></td>
            <td class="right bold"><?php echo idr_currency($data['total_grt']) ?></td>
            <td class="right bold"><?php echo idr_currency($data['sub_total']) ?></td>
        </tr>
    </table>
    <br>
    <table class="table table-no-border full-width" align="center">
        <tr>
            <td class="center" style="padding-bottom: 0;width: 50%"></td>
            <td class="center" style="padding-bottom: 0;width: 50%"></td>
        </tr>
        <tr>
            <th class="center">Petugas Pelayaran</th>
            <th class="center">Petugas GS</th>
        </tr>
        <tr>
            <th class="center"><br><br><br><br></th>
            <th class="center"></th>
        </tr>
        <tr>
            <td class="center" style="text-decoration: underline; padding-bottom: 0">.........................................</td>
            <td class="center" style="text-decoration: underline; padding-bottom: 0">.........................................</td>
        </tr>
        <tr>
            <td class="center">NIK. .................................</td>
            <td class="center">NIK. .................................</td>
        </tr>
    </table>
    </page>

    <!-- Memanggil fungsi bawaan HTML2PDF -->
    <?php
        $content = ob_get_clean();
        $filename = strtoupper('Laporan_engker_'. $detail->ship_name . "_" . $data['tanggal']);
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
