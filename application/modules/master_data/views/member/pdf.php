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
                Member Ferizy Tanggal Daftar <?php echo format_date($departDateFrom) . " s/d " . format_date($departDateTo) ?>
                <?php //echo empty($port) ? "di Semua Pelabuhan" : "di Pelabuhan " . $port; ?>
            </h4>
        </p>
        <!-- <div style="text-align: center; font-size: 20px;"></div> -->
    </page_header>
    <page_footer>
        Halaman [[page_cu]]/[[page_nb]]
    </page_footer>

    <table class="my-table font-small" border="1">
        <thead>
            <tr class="headerTable">

                <th>NO</th>
                <th>NIK</th>
                <th>NAMA</th>
                <th>TANGGAL LAHIR</th>
                <th>ALAMAT</th>
                <th>NO. TELEPON</th>
                <th>EMAIL</th>
                <th>TANGGAL PENDAFTARAN</th>
                <th>STATUS</th>                 
            </tr>
        </thead>


        <tbody>
            <?php $no = 1;
            foreach ($data as $key => $value) {


            ?>
                <tr>
                    <td style="text-align: center; width:2%"><?php echo  $no ?></td>
                    <td style="width:12%"> <?php echo $value->nik ?> </td>
                    <td style="width:18%"> <?php echo $value->full_name; ?> </td>
                    <td style="width:12%"> <?php echo $value->date_of_birth; ?> </td>
                    <td style="width:14%"> <?php echo $value->address; ?> </td>
                    <td style="width:9%"> <?php echo $value->phone_number; ?> </td>
                    <td style="width:16%"> <?php echo $value->email; ?> </td>
                    <td style="width:14%"> <?php echo $value->created_on; ?> </td>
                    <td style="text-align: center;width:7%"> <?php echo $value->status; ?> </td>
                </tr>

            <?php $no++;
            }?>
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
    $html2pdf->pdf->SetDisplayMode('fullpage');
    $html2pdf->writeHTML($content);
    $html2pdf->Output('Member Ferizy.pdf');
} catch (HTML2PDF_exception $e) {
    echo $e;
    exit;
}
?>