<!-- Setting CSS bagian header/ kop -->
<style type="text/css">
    .my-img {

        height: 50px;
        width: 100px
    }

    .my-table {

        border-collapse: collapse;
        width: 100%;
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
<page backtop="14mm" backbottom="14mm" backleft="-5mm" backright="10mm">
    <!--  -->

    <page_header>

        <p style=" text-align: center; ">

            <h4>
                Tiket Refund Kendaraan <?php echo format_date($departDateFrom) . " s/d " . format_date($departDateTo) ?>
                <?php echo empty($port) ? "di Semua Pelabuhan" : "di Pelabuhan " . $port; ?>
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
                <th>KODE BOOKING</th>
                <th>NO TIKET</th>
                <th>PANJANG PADA <br />PEMESANAN <br /> (METER)</th>
                <th>GOLONGAN</th>
                <th>KELAS<br /> LAYANAN</th>
                <th>TARIF<br /> GOLONGAN (Rp.)</th>
                <th>WAKTU<br /> PEMBAYARAN</th>
                <th>JADWAL<br /> KEBERANGKATAN</th>
                <th>LINTASAN DIPESAN</th>
                <th>STATUS TIKET <br />REFUND</th>
                <th>TOTAL BIAYA <br />ADMINISTRASI</th>
                <th>BIAYA<br /> ADMINISTRASI<br /> REFUND</th>
                <th>BIAYA REFUND</th>
                <th>NO REKENING</th>
                <th>BANK TUJUAN</th>
                <th>PENGEMBALIAN REFUND <br />(Tidak dikurangi Charge<br /> Transfer Bank)</th>
            </tr>
        </thead>


        <tbody>
            <?php $no = 1;
            foreach ($data as $key => $value) {

                empty($value->payment_date) ? $paymentDate = "" : $paymentDate = format_date($value->payment_date) . " " . format_time($value->payment_date);

                empty($value->keberangkatan) ? $departDate = "" : $departDate = format_date($value->keberangkatan);
            ?>
                <tr>
                    <td style="text-align: center"><?php echo  $no ?></td>
                    <td><?php echo  $value->booking_code ?></td>
                    <td><?php echo  $value->ticket_number ?></td>
                    <td><?php echo  $value->length_vehicle ?></td>
                    <td><?php echo  $value->vehicle_class_name ?></td>
                    <td><?php echo  $value->ship_class_name ?></td>
                    <td style="text-align: right;"><?php echo  idr_currency($value->fare) ?></td>
                    <td><?php echo  wordwrap($paymentDate, 20, '<br />', true)  ?></td>
                    <td><?php echo  wordwrap($departDate, 20, '<br />', true) ?></td>
                    <td><?php echo  $value->route_name ?></td>
                    <td><?php echo  wordwrap($value->status_refund, 18, '<br />', true)  ?></td>
                    <td style="text-align: right;"><?php echo  idr_currency($value->charge_amount) ?></td>
                    <td style="text-align: right;"><?php echo  idr_currency($value->adm_fee) ?></td>
                    <td style="text-align: right;"><?php echo  idr_currency($value->refund_fee) ?></td>
                    <td><?php echo  $value->account_number ?></td>
                    <td><?php echo  $value->bank ?></td>
                    <td style="text-align: right;"><?php echo  idr_currency($value->total_amount) ?></td>
                </tr>


            <?php $no++;
            } ?>
        </tbody>
    </table>
</page>


<!-- Memanggil fungsi bawaan HTML2PDF -->
<?php
$content = ob_get_clean();
// include 'html2pdf_v4.03/html2pdf.class.php';
try {
    // setting paper
    $html2pdf = new html2pdf('L', 'A3', 'en', false, 'UTF-8', array(10, 10, 10, 10, 10, 10));
    $html2pdf->pdf->SetDisplayMode('fullpage');
    $html2pdf->writeHTML($content);
    $html2pdf->Output('Transaction date.pdf');
} catch (HTML2PDF_exception $e) {
    echo $e;
    exit;
}
?>