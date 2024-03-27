<!-- Setting CSS bagian header/ kop -->
<style type="text/css">
    .my-img{

        height:50px;
        width: 100px        
    }

    .my-table {

      border-collapse: collapse;
      width: 100%;
    }

    .my-table td, .my-table th {
      /*border: 1px solid #ddd;*/
      border: 1px solid grey;
      padding: 5px;
      /* font-size: 10px; */
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

    .font-small{
        font-size: 9px;
    }        
    .my-table-content td {
        padding :5px 10px 5px 10px;

    } 

    .my-table-content{
        padding-left: 50px;
    }

    .headerTable th{

        text-align: center;
        padding:5px;
        background: #ddd; 
        /*color: white;*/
    }
</style>
<!-- Setting Margin header/ kop -->
<page backtop="14mm" backbottom="14mm" backleft="2mm" backright="5mm">
  <!--  -->

<page_header>

    <p style=" text-align: center; ">

        <h4>
        <?= 'Detail Dashboard Reservasi Tanggal Keberangkatan ' . $dateFrom  ?>
        </h4></p>
  <!-- <div style="text-align: center; font-size: 20px;"></div> -->
</page_header>
<page_footer>
    Halaman [[page_cu]]/[[page_nb]]
</page_footer>

 <table class="my-table font-small" border="1" >
    <thead>
        <tr class="headerTable">
            <th>NO</th>
            <th>NO TIKET</th>
            <th>KODE BOOKING</th>
            <th>NAMA PEMESAN</th>
            <th>NO TELEPON</th>
            <th>NAMA PENUMPANG</th>
            <th>NIK</th>
            <th>ASAL</th>
            <th>LAYANAN</th>
            <th>TANGGAL DAN <br /> JAM MASUK <br />PELABUHAN</th>
            <th>GOLONGAN</th>
            <th>NO POLISI</th>
            <th>TIPE <br />PEMBAYARAN</th>
            <th>CHANNEL</th>
            <th>TARIF TIKET</th>
            <th>BIAYA ADMIN</th>
            <th>TOTAL BAYAR</th>
            <th>STATUS</th>
            <th>PEMESANAN</th>
            <th>PEMBAYARAN</th>
            <th>CETAK <br />BOARDING <br />PASS</th>
            <th>VALIDASI</th>
        </tr>
    </thead>


    <tbody>
    <?php $no=1; foreach ($data as $key=>$value) { ?>

            <tr>
                <td><?= wordwrap($value->number,10,"<br>\n"); ?></td>
                <td><?= wordwrap($value->ticket_number,10,"<br>\n"); ?></td>
                <td><?= wordwrap($value->booking_code,10,"<br>\n"); ?></td>
                <td><?= wordwrap($value->customer_name,10,"<br>\n"); ?></td>
                <td><?= wordwrap($value->phone_number,10,"<br>\n"); ?></td>
                <td><?= wordwrap($value->nama_penumpang,10,"<br>\n"); ?></td>
                <td><?= wordwrap($value->nik,10,"<br>\n"); ?></td>
                <td><?= wordwrap($value->asal,10,"<br>\n"); ?></td>
                <td><?= wordwrap($value->layanan,10,"<br>\n"); ?></td>
                <td><?= wordwrap($value->depart_date,10,"<br>\n"); ?></td>
                <td><?= wordwrap($value->vehicle_class_name,10,"<br>\n"); ?></td>
                <td><?= wordwrap($value->plat_number,10,"<br>\n"); ?></td>
                <td><?= wordwrap($value->tipe_pembayaran,10,"<br>\n"); ?></td>
                <td><?= wordwrap($value->channel,10,"<br>\n"); ?></td>
                <td align="right"><?= wordwrap($value->tarif_ticket_format,10,"<br>\n"); ?></td>
                <td align="right"><?= wordwrap($value->biaya_admin_format,10,"<br>\n"); ?></td>
                <td align="right"><?= wordwrap($value->total_bayar_format,10,"<br>\n"); ?></td>
                <td><?= wordwrap($value->status_ticket,10,"<br>\n"); ?></td>
                <td><?= wordwrap($value->pemesanan_date,10,"<br>\n"); ?></td>
                <td><?= wordwrap($value->pembayaran_date,10,"<br>\n"); ?></td>
                <td><?= wordwrap($value->cetak_boarding_date,10,"<br>\n"); ?></td>
                <td><?= wordwrap($value->validasi_date,10,"<br>\n"); ?></td>

            </tr>  


    <?php $no ++; } ?>
    </tbody>
 </table>
</page>


<!-- Memanggil fungsi bawaan HTML2PDF -->
<?php

	$content = ob_get_clean();
	 // include 'html2pdf_v4.03/html2pdf.class.php';
	 try
	{
	  // setting paper
	    // $html2pdf = new html2pdf('L', 'A3', 'en', false, 'UTF-8', array(10, 10, 10,10,10,10)); // tanpa menggunakan composer
        
        $html2pdf = new Spipu\Html2Pdf\Html2Pdf('L', 'A3', 'en', false, 'UTF-8', array(10, 10, 10,10,10,10));

	    $html2pdf->pdf->SetDisplayMode('fullpage');
	    $html2pdf->writeHTML($content);
	    $html2pdf->Output('Transaction date.pdf');
	}
	catch(HTML2PDF_exception $e) {
	    echo $e;
	    exit;
	}
	
?>
