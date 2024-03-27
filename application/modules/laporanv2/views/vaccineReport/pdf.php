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
      font-size: 10px;
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
        font-size: 10px;
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
<page backtop="14mm" backbottom="14mm" backleft="-5mm" backright="10mm">
  <!--  -->

<page_header>

    <p style=" text-align: center; ">

        <h4>
            Validasi Vaksin tanggal Keberangkatan <?= $dateFrom ?> s/d  <?= $dateTo ?> 
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
            <th>KODE <br>BOOKING</th>
            <th>NOMOR <br>TIKET </th>
            <th>PELABUHAN</th>
            <th>JENIS<br> PJ</th>
            <th>LAYANAN</th>
            <th>GOLONGAN<br>KND</th>
            <th>NO POLISI</th>
            <th>JENIS<br>PNP</th>
            <th>NAMA</th>
            <th>JENIS<br>ID</th>
            <th>NOMOR<br>ID</th>
            <th>USIA</th>
            <th>JENIS <br> KELAMIN</th>
            <th>ALAMAT</th>
            <th>TAMBAH MANIFEST</th>
            <th>TANGGAL<br>BERANGKAT</th>
            <th>JAM<br>BERANGKAT</th>
            <th>STATUS</th>
            <th>NAMA<br> KAPAL</th>
            <th>STATUS<br> VALIDASI</th>
            <th>TES <br> COVID</th>
            <th>KETERANGAN</th>
        </tr>
    </thead>


    <tbody>
    <?php $no=1; foreach ($data as $key=>$value) { ?>

            <tr>
                <td><?php echo $no; ?></td>
                <td><?php echo wordwrap($value->booking_code,10, '<br />', true); ?></td>
                <td><?php echo wordwrap($value->ticket_number,10, '<br />', true); ?></td>
                <td><?php echo wordwrap($value->port_name,10, '<br />', true); ?></td>
                <td><?php echo wordwrap($value->service_name,10, '<br />', true); ?></td>
                <td><?php echo wordwrap($value->ship_class_name,10, '<br />', true); ?></td>
                <td><?php echo wordwrap($value->vehicle_class_name,10, '<br />', true); ?></td>
                <td><?php echo wordwrap($value->plat_no,10, '<br />', true); ?></td>
                <td><?php echo wordwrap($value->passanger_type_name,10, '<br />', true); ?></td>
                <td><?php echo wordwrap($value->name,10, '<br />', true); ?></td>
                <td><?php echo wordwrap($value->type_id_name,10, '<br />', true); ?></td>
                <td><?php echo $value->id_number ?></td>
                <td><?php echo wordwrap($value->age,10, '<br />', true); ?></td>
                <td><?php echo wordwrap($value->gender,10, '<br />', true); ?></td>
                <td><?php echo wordwrap($value->city,10, '<br />', true); ?></td>
                <td><?php echo wordwrap($value->add_manifest_channel,10, '<br />', true); ?></td>
                <td><?php echo wordwrap($value->depart_date,10, '<br />', true); ?></td>
                <td><?php echo wordwrap($value->depart_time_start,10, '<br />', true); ?></td>
                <td><?php echo wordwrap($value->description,10, '<br />', true); ?></td>
                <td><?php echo wordwrap($value->ship_name,10, '<br />', true); ?></td>
                <td><?php echo wordwrap($value->vaccine,10, '<br />', true); ?></td>
                <td><?php echo wordwrap($value->testCovid,20, '<br />', true); ?></td>
                <td><?php echo wordwrap(htmlspecialchars(strip_tags($value->reason)),20, '<br />', true); ?></td>                


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
	    $html2pdf = new html2pdf('L', 'A3', 'en', false, 'UTF-8', array(10, 10, 10,10,10,10));
	    $html2pdf->pdf->SetDisplayMode('fullpage');
	    $html2pdf->writeHTML($content);
	    $html2pdf->Output('Transaction date.pdf');
	}
	catch(HTML2PDF_exception $e) {
	    echo $e;
	    exit;
	}
	
?>
