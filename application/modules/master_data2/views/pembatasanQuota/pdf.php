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
        <?= 'Pembatasan Kuota Restrict Tanggal Berlaku dan Akhir Berlaku ' . $dateFrom . ' s/d ' . $dateTo ?>
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
            <th>PELABUHAN</th>
            <th>LAYANAN</th>
            <th>JENIS PJ</th>
            <th>GOLONGAN</th>
            <th>BATAS <br />QUOTA</th>
            <th>BATAS <br />LINEMETER</th>
            <th>TANGGAL BERLAKU</th>
            <th>AKHIR BERLAKU</th>
            <th>JAM</th>
            <th>STATUS</th>
        </tr>
    </thead>


    <tbody>
    <?php $no=1; foreach ($data as $key=>$value) { ?>

            <tr>
                <td><?php echo $no; ?></td>
                <td><?= $value->port_name ?> </td>
                <td><?= $value->ship_class_name ?> </td>
                <td><?= $value->jenis_pj ?> </td>
                <td><?= $value->golongan ?> </td>
                <td><?= $value->quota ?> </td>
                <td><?= $value->total_lm ?> </td>
                <td><?= $value->start_date ?> </td>
                <td><?= $value->end_date ?> </td>
                <td><?= 
                    str_replace(",",", <br /> ",$value->depart_time);
                ?> 
                    
                </td>
                <td><?= $value->status ?> </td>

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
	    $html2pdf = new html2pdf('L', 'A3', 'en', false, 'UTF-8', array(10, 10, 10,10,10,10)); // tanpa menggunakan composer
        // $html2pdf = new Spipu\Html2Pdf\Html2Pdf('P', 'A4', 'en', false, 'UTF-8', array(10, 10, 10,10,10,10));

	    $html2pdf->pdf->SetDisplayMode('fullpage');
	    $html2pdf->writeHTML($content);
	    $html2pdf->Output('Transaction date.pdf');
	}
	catch(HTML2PDF_exception $e) {
	    echo $e;
	    exit;
	}
	
?>
