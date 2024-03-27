<!-- Setting CSS bagian header/ kop -->
<style type="text/css">
    .my-img {

        height: 50px;
        width: 100px
    }

    .my-table {

        border-collapse: collapse;
        /* width: 100%; */
        margin-left: auto;
        margin-right: auto;
    }

    .my-table td,
    .my-table th {
        /*border: 1px solid #ddd;*/
        border: 1px solid grey;
        /*padding: 5px;*/
    }


    .my-table th {
        padding-top: 5px;
        padding-bottom: 5px;
        padding-left: 5px;
        padding-right: 5px;
        text-align: left;
        /*background-color: #4CAF50;*/
        /*color: white;*/
    }

    .my-table td {
        padding-left: 5px;
        padding-right: 5px;
    }

    .font-small {
        font-size: 10px;
    }

    .my-table-content td {
        padding: 5px 10px 5px 10px;

    }

    .my-table-content {
        padding-left: 50px;
    }

    .headerTable th {

        text-align: center;
        padding: 5px;
        background: #ddd;
        /*color: white;*/
    }
</style>
<!-- Setting Margin header/ kop -->
<page backtop="14mm" backbottom="14mm" backleft="-3mm" backright="10mm">
    <!--  -->

    <page_header>

        <p style=" text-align: center; ">

            <h4>
            Hapus Akun Member Tanggal <?php echo format_date($departDateFrom) . " s/d " . format_date($departDateTo) ?>
                
            </h4>
        </p>
        
    </page_header>
    <page_footer>
        Halaman [[page_cu]]/[[page_nb]]
    </page_footer>

    <table class="my-table font-small" border="1"> 
        <thead>
            <tr class="headerTable">
                <th>NO</th>
                <th>AKUN</th>
                <th>NAMA LENGKAP</th>
                <th>NO HP</th>
                <th>DATE CREATE AKUN</th>
                <th>DATE DELETE AKUN</th>
                <th>ALASAN DELETE AKUN</th>
            </tr>
        </thead>


        <tbody>
            <?php $no = 1;
            foreach ($data as $key => $value) { ?>
                <tr>
                    <td style="text-align: center; width:2%"><?php echo  $no ?></td>
                    <td style="width:20%"> <?= $value->email ; ?></td>
                    <td style="width:20%"> <?= wordwrap($value->fullname, 20, "<br>\n");  ?></td>
                    <td style="width:10%"> <?= $value->phone_number; ?></td>
                    <td style="width:10%"> <?= wordwrap($value->account_created_on, 15, "<br>\n"); ?> </td>
                    <td style="width:10%"> <?= wordwrap($value->account_delete_date, 15, "<br>\n"); ?> </td>
                    <td style="width:30%"> <?= wordwrap($value->reason_text_selected, 35, "<br>\n"); ?> </td>
                </tr>

            <?php $no++; } ?>
        </tbody>
    </table>
</page>


<!-- Memanggil fungsi bawaan HTML2PDF -->
<?php
$content = ob_get_clean();
// include 'html2pdf_v4.03/html2pdf.class.php';
try {
    // setting paper
    $html2pdf = new html2pdf('L', 'A4', 'en', false, 'UTF-8', array(10, 10, 10, 10, 10, 10));

    // menggunakan composer
    // $html2pdf =new Spipu\Html2Pdf\Html2Pdf('L', 'A4', 'en', false, 'UTF-8', array(10, 10, 10, 10, 10, 10));

    $html2pdf->pdf->SetDisplayMode('fullpage');
    $html2pdf->writeHTML($content);
    $html2pdf->Output('Member Ferizy.pdf');
} catch (HTML2PDF_exception $e) {
    echo $e;
    exit;
}
?>