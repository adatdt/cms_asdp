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
<page backtop="14mm" backbottom="14mm" backleft="0mm" backright="0mm">
  <!--  -->

    <page_header>

        <p style=" text-align: center; ">

            <!-- <h4>
                Kode QR Approval 
            </h4> -->
        </p>
    <!-- <div style="text-align: center; font-size: 20px;"></div> -->
    </page_header>
    <page_footer>
        Halaman [[page_cu]]/[[page_nb]]
    </page_footer>


        <?php foreach ($data as $key => $value) { ?>
            
            <table align="center" style="background-color:#f6f6f9; padding:0px 50px; " border="1px">
                <tr>
                    <td align="center" style=" border:none;" >
                        <h4>
                            Kode QR Approval 
                        </h4>
                    </td>
                </tr>
                <tr>
                    <td align="center" >
                    <img src="data:image/png;base64,<?= $value['baseCode'] ?>" width="300" height="300" >
                    </td>
                </tr>
                <tr>
                    <td align="center" style="padding-bottom:30px; border:none; ">
                    <br />Tanggal Penugasan : <?= $value['assignmentDate'] ?>
                    <br />Nama : <?= $value['name'] ?> 
                    <br />Grup : <?= $value['group'] ?>
                    <br />Pelabuhan : <?= $value['portName'] ?>
                    <br />Shift : <?= $value['shift'] ?>
                    </td>
                </tr>
            </table>
            <p style="padding:200px;"></p>
            
        <?php } ?>
        
        

</page>


<!-- Memanggil fungsi bawaan HTML2PDF -->
<?php


	$content = ob_get_clean();
	 // include 'html2pdf_v4.03/html2pdf.class.php';
	 try
	{
	  // setting paper
	    $html2pdf = new html2pdf('P', 'A4', 'en', false, 'UTF-8', array(10, 10, 10,10,10,10));
	    $html2pdf->pdf->SetDisplayMode('fullpage');
	    $html2pdf->writeHTML($content);
	    $html2pdf->Output('Transaction date.pdf');
	}
	catch(HTML2PDF_exception $e) {
	    echo $e;
	    exit;
	}

	
?>
