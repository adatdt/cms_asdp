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
      font-size: 8px;
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
<page backtop="14mm" backbottom="14mm" backleft="5mm" backright="10mm">
  <!--  -->

<page_header>

    <p style=" text-align: center; ">

        <h4 >
            <span style="padding-bottom:50px;" >Verifikator Tanggal Keberangkatan <?= $dateFrom ?> s/d  <?= $dateTo ?> </span>
            
        </h4>
    </p>    
  <!-- <div style="text-align: center; font-size: 20px;"></div> -->
</page_header>
<page_footer>
    Halaman [[page_cu]]/[[page_nb]]
</page_footer>
 <table class="my-table font-small" border="1" >
    <thead>
        <tr class="headerTable">
            <th>NO</th>
            <th>KODE <br />BOOKING</th>
            <th>NOMOR TIKET</th>
            <th>PELABUHAN <br />ASAL</th>
            <th>JENIS PJ</th>
            <th>LAYANAN</th>
            <th>GOLONGAN <br />KND</th>
            <th>GOLONGAN <br />PNP</th>
            <th>NO POLISI</th>
            <th>NAMA <br />PENUMPANG</th>
            <th>JENIS <br />IDENTITAS</th>
            <th>NO IDENTITAS</th>
            <th>UMUR</th>
            <th>JENIS <br />KELAMIN</th>
            <th>DOMISILI</th>
            <th>TANGGAL <br />MASUK <br />PELABUHAN</th>
            <th>JAM MASUK <br />PELABUHAN</th>
            <th>STATUS <br />TIKET</th>
            <th>WAKTU<br /> CHECKIN</th>
            <th>WAKTU <br />GATEIN</th>
            <th>WAKTU <br />BOARDING</th>
            <th>STATUS<br /> VERIFIKATOR</th>
            <th>USER <br />VERIFIKATOR</th>
            <th>WAKTU <br /> VERIFIKASI</th>
            <th>PERANGKAT <br /> VERIFIKASI</th>
            <th>ID <br /> PERANGKAT</th>
        </tr>
    </thead>


    <tbody>
    <?php $no=1; foreach ($data as $key=>$value) { ?>
        <tr>
            <td><?php echo $no; ?></td>
            <td><?php echo wordwrap($value->booking_code,10, '<br />', true); ?></td>
            <td><?php echo wordwrap($value->ticket_number,12, '<br />', true); ?></td>
            <td><?php echo wordwrap($value->origin_name,10, '<br />', true); ?></td>
            <td><?php echo wordwrap($value->service_name,10, '<br />', true); ?></td>
            <td><?php echo wordwrap($value->ship_class_name,10, '<br />', true); ?></td>
            <td><?php echo wordwrap($value->golongan_knd,10, '<br />', true); ?></td>
            <td><?php echo wordwrap($value->golongan_pnp,10, '<br />', true); ?></td>
            <td><?php echo wordwrap($value->plat_no,10, '<br />', true); ?></td>
            <td><?php echo wordwrap($value->passanger_name,10, '<br />', true); ?></td>
            <td><?php echo wordwrap($value->id_type_name,10, '<br />', true); ?></td>
            <td><?php echo wordwrap($value->no_identitas,16, '<br />', true); ?></td>
            <td><?php echo wordwrap($value->age,10, '<br />', true); ?></td>
            <td><?php echo wordwrap($value->gender,10, '<br />', true); ?></td>
            <td><?php echo wordwrap($value->city,10, '<br />', true); ?></td>
            <td><?php echo wordwrap($value->tanggal_masuk_pelabuhan,10, '<br />', true); ?></td>
            <td><?php echo wordwrap($value->depart_time_start,10, '<br />', true); ?></td>
            <td><?php echo wordwrap($value->status_ticket,10, '<br />', true); ?></td>
            <td><?php echo wordwrap($value->checkin_date,10, '<br />', true); ?></td>
            <td><?php echo wordwrap($value->gatein_date,10, '<br />', true); ?></td>
            <td><?php echo wordwrap($value->boarding_date,10, '<br />', true); ?></td>
            <td><?php echo wordwrap($value->approved_status,10, '<br />', true); ?></td>
            <td><?php echo wordwrap($value->user_verified,25, '<br />', true); ?></td>
            <td><?php echo wordwrap($value->approved_date,10, '<br />', true); ?></td>
            <td><?php echo wordwrap($value->terminal_name,10, '<br />', true); ?></td>
            <td><?php echo wordwrap($value->terminal_code,10, '<br />', true); ?></td>
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
