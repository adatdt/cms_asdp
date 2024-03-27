<!DOCTYPE html>
<html lang="en">
  <!-- <page backtop="20mm" backbottom="45mm" backleft="-10mm" backright="5mm"> -->
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?= $title_pdf;?></title>
        <style>
        @page {
                margin-top: 8mm;
                margin-left:10mm;
                margin-right: 10mm;
                margin-bottom: 45mm;
                
            }
        html{
          font-family: Arial, Helvetica, sans-serif;
        }

        footer {
            position: fixed; 
            left: 0px; 
            right: 0px;
            width: 700px;
            top: 900px;
        }

        header {
            /* position: fixed;  */
        }

        .text-center {
        text-align: center;
        vertical-align: top;
        }

        .text-right {
        text-align: right;
        }
        
        .full-width {
            width: 1024px;
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
        .logo{
            width: 100%;
            border-collapse: collapse;
            /* margin: 0 15px; */
            }

        span{
            color: black;
        }

        .hr1{
            height: 1px;
            background-color: #f0b700;
            border: none;
        } 
        .border-radius {
            border-right: 1px solid #f0e8e8;
            border-bottom: 1px solid #f0e8e8;
            border-top: 1px solid #f0e8e8;
            border-left:1px solid #f0e8e8;
            border-radius: 6px;
            padding: 6px 6px 6px 12px;
        
        }

        #mytable th {
            border-right: 1px solid #555;
            border-bottom: 1px solid #555;
            border-top: 1px solid #555;
            font-size:12px;
            /* letter-spacing: 2px; */
            text-align: center;
            padding: 4px 4px 4px 4px;
        }

        #mytable th.spec {
            border-left: 1px solid #555;
        }

        #mytable td {
            border-right: 1px solid #555;
            border-bottom: 1px solid #555;
            padding: 6px 6px 6px 12px;
            background: #eee;
            font-size:12px;
        }
        #mytable td.spec {
            border-left: 1px solid #555;
        }

        #mytable td.alt {
            text-align: right;
        }

        #mytable-prince th {
            /* border-right: 1px solid #555;
            border-bottom: 1px solid #555;
            border-top: 1px solid #555; */
            font-size:12px;
            /* letter-spacing: 2px; */
            text-align: right;
            padding: 4px 4px 4px 4px;
            margin: 0;
            font-weight: bold;
        }

        #mytable-prince th.image {
            position: absolute;
            right: 0px;
            bottom: 0px; 
           
        }

        #mytable-total-payment th {
            /* border-right: 1px solid #555;
            border-bottom: 1px solid #555;
            border-top: 1px solid #555; */
            font-size:12px;
            /* letter-spacing: 2px; */
            text-align: right;
            padding: 4px 4px 4px 4px;
            margin: 0;
            font-weight: bold;
        }
        #mytable-total-payment th.spec  {
            background: #eee;
        }

        #watermark{
            transform: rotate(-19.54deg); 
            transform-origin: 0 0; 
            text-align: center; 
            color: rgba(128, 128, 128, 0.30); 
            /* font-size: 54px;  */
            font-family:
            Inter; font-weight: 700;
            word-wrap: break-word;
            position: absolute;
            width:100%; 
            /* border: 1px solid black; */
            padding: 14cm 0cm;
            margin-left:-4cm;
        }

        #image-paid{
            top : 660px;
            left : 5px;
            display:block;
            position : absolute;
           
        }

        #mytable-information td {
            padding: 6px 6px 6px 12px;
            text-align: left;
        }

        #mytable-information td.spec {
            font-size:14px;
        }

        #mytable-information td.alt {
            font-weight: bold;
            font-size:12px;
            /* border-bottom: 1px solid #555;
            border-right: 1px solid #555; */
            border-radius: 6px;
        }
        #border-booking {
            font-size:11px;
            background: #eee;
            text-align: right;
            padding: 1px 1px 1px 1px;
            margin-top: 5px;
            font-weight: bold;
            border-radius: 6px;
        }

        </style>
    </head>

<header>
    <table style="width:100%;" align="center" border="0" >
        <tr>
        <td style="width:50%;vertical-align:middle;border-bottom: 0px solid #f58220;height: 20px; padding: -30px 10px 10px 0px;">
            <h3 style="font-size:28px;color: #f88f27;margin: 0px; ">Bukti Pembelian (Receipt)</h3>
            <span style="font-size:18px; opacity: 0.5">Tiket Penyeberangan</span>
            <table id="border-booking" >
                <th><b>Kode Booking:</b></th>
                <th style="color: #f88f27;"><b><?= $booking_code ?></b></th>
            </table>
        </td>
        <td class="text-right" style="width:20%; vertical-align:middle;  border-bottom: 0px solid #f58220;height: 70px;  padding: -30px 0px 25px 30px">               
        </td>          
        <td class="text-left" style="width:30%;border-bottom: 0px solid #f58220;height: 70px; padding: -20px 10px 25px 50px  ">
            <img src="data:image/png;base64,'<?=$imgFeryzi;?>'" style="height:70px;width:auto;">            
        </td>
        </tr>
    </table>
    <p></p>   
</header>

<?php 
    /*
        * length max 27 fontsize 54
        * length 
    */
    $waterMark =$this->session->userdata("username")  ." (". date("d-m-Y") .")";

    if(strlen($waterMark)<=34)
    {
        $fontSize = 44;
    }
    else if(strlen($waterMark)<=43)
    {
        $fontSize = 34;
    }
    else if(strlen($waterMark)<=61)
    {
        $fontSize = 24;
    }
    else if(strlen($waterMark)<=75)
    {
        $fontSize = 19;
    }
    else
    {
        $fontSize = 14;
    }                                                           
    
?>
<div id="watermark" style="font-size:<?=$fontSize?>px;">    
<?=$waterMark?>
</div>

<div id="image-paid">    
    <img  src="data:image/png;base64,'<?=$imgPaid;?>'"  style="width:120px;">
</div>

    <p><span class="text-bold" ><b>Informasi Pemesan</b></span></p>
    <div class="border-radius" >
        <table id="mytable-information" cellspacing="0"> 
            <tr> 
                <td class="spec" width="130" >Nama Pemesan</td>  
                <td class="spec" width="240">Email</td>  
                <td class="spec" width="100">Nomor Telepon</td>   

            </tr> 
            <tr> 
                <td class="alt"><?= $vehicle[0]->instansi ?></td> 
                <td class="alt"><?= $vehicle[0]->email ?></td> 
                <td class="alt"><?= $vehicle[0]->phone_number ?></td>
            </tr> 
        </table>
    </div>

    <hr style="height: 1px; background-color: #f0b700; border: none; margin-top: 25px;"  />
    <p><span class="text-bold" ><b>Informasi Perjalanan</b></span></p>
    <div class="border-radius" >
        <table id="mytable-information" cellspacing="0"> 
            <tr> 
                <td class="spec" width="100">Pelabuhan Asal</td>  
                <td class="spec" width="120">Pelabuhan Tujuan</td>  
                <td class="spec" width="100">Kelas Layanan</td>
                <td class="spec" width="150">Jadwal Perjalan</td>   
            </tr> 
            <tr> 
                <td class="alt"><?= $vehicle[0]->origin ?>, <?= $vehicle[0]->origin_city ?></td> 
                <td class="alt"><?= $vehicle[0]->destination ?>, <?= $vehicle[0]->destination_city ?></td> 
                <td class="alt"><?= $vehicle[0]->ship_class ?></td>
                <?php 
                    $jadwalPerjalanan = format_date($vehicle[0]->depart_date)." ".format_time($vehicle[0]->depart_time_start);
                    if($vehicle[0]->ship_class==1)
                    {
                        $jadwalPerjalanan = format_date($vehicle[0]->depart_date)." ".format_time($vehicle[0]->depart_time_start)." - ".format_time($vehicle[0]->depart_time_end);
                    }                
                ?>
                <td class="alt"><?php echo $jadwalPerjalanan; ?></td>
            </tr> 
        </table>
    </div>

    <hr style="height: 1px; background-color: #f0b700; border: none; margin-top: 25px;"  />
    <p><span class="text-bold" ><b>Rincian Pesanan</b><br></span>
            
    <p style="font-size:10px; margin-top: -15px;">Tanggal Pemesanan : <?php echo date("Y-m-d  H:i", strtotime($vehicle[0]->created_on) )?></p></p>
    
    <table id="mytable" cellspacing="0"> 
        <tr> 
            <th width="10" scope="col" class="spec">No</th> 
            <th width="140">Uraian</th>  
            <th width="50">Jumlah</th>  
            <th width="170">Harga</th>  
            <th width="135">Total Harga</th>  

        </tr> 
        <tr> 
            <td scope="row" class="spec">1</td> 
            <td><?= $vehicleClassName; //$vehicle[0]->vehicle_name ?></td> 
            <td class="alt">1</td> 
            <td class="alt">Rp<?=number_format($vehicle[0]->amount_invoice,0,',','.')?></td> 
            <td class="alt">Rp<?=number_format($vehicle[0]->amount_invoice,0,',','.')?></td> 

        </tr> 
        
    </table>

    <table id="mytable-prince" cellspacing="0" style=" margin-top: 5px;"> 
        <tr> 
            <th width="393">Sub Total</th>  
            <th width="135">Rp<?=number_format($vehicle[0]->amount_invoice,0,',','.')?></th>  
        </tr> 
        <tr> 
            <th>Reduksi</th>  
            <th>-</th>  
        </tr> 
        <tr> 
            <th>Total Tagihan <br> <i style="color:#5b5c5b; font-size:10px; " >*Sub Total - Reduksi</i></th>  
            <th>Rp<?=number_format($vehicle[0]->amount_invoice,0,',','.')?></th>  
        </tr> 
        <tr> 
            <th>PPN Fasilitas Yang Dibebaskan <br> <i style="color:#5b5c5b; font-size:10px; " ><?= $ppnText; ?></i></th>  
            <th>Rp<?=number_format(($vehicle[0]->amount_invoice/100)*$ppnReceipt,0,',','.')?></th>  
        </tr> 
        <tr> 
            <th>Biaya Administrasi <br> <i style="color:#5b5c5b; font-size:10px; " ><?= $vehicle[0]->getNamePayment; ?></i></th>  
            <th >Rp<?=number_format($vehicle[0]->amount_payment-$vehicle[0]->amount_invoice,0,',','.')?></th>  
        </tr>   
    </table>

    <table id="mytable-total-payment" cellspacing="0"> 
        <tr> 
            <th width="218"><p></p></th>  
            <th width="168" class="spec">Total Pembayaran <br> <i style="color:#5b5c5b; font-size:10px; " >*Total Tagihan + Biaya Administrasi</i></th>  
            <th width="135" class="spec">Rp<?=number_format($vehicle[0]->amount_payment,0,',','.')?></th>  
        </tr>
    </table>

<footer>
    <table  class="table table-detail full-width" style="width: 100%; margin:auto;" border="0">
        <tr>
            <td width="180" >
                Dikelola oleh:
            </td>
            <td width="120">
            Hubungi kami melalui:
            </td>
            <td width="120">
            <p></p>
            </td>
            <td width="150">
            <p style="margin:2px 0 0 0; text-align: center; font-size:10px;"><b>Unduh Aplikasi Ferizy</b></p>
            </td>
        </tr>

        <tr>  
            <td rowspan="3" >
                <img src="data:image/png;base64,'<?=$imgLogoPrimary;?>'" style="width:100px;">
            </td>
            <td style="font-size: 11px;padding: 2px 4px ;"><img class="text-right" src="data:image/png;base64,'<?=$imgMail;?>'"  style="height:18px;width:auto;margin: 0 4px -2px 0"> cs@asdp.id</td>
            <td style="font-size: 11px;padding: 2px 4px"><img class="text-right" src="data:image/png;base64,'<?=$imgInstagram;?>'"style="height:18px;width:auto;margin: 0 4px -2px 0"> @asdp191</td>
            <td rowspan="3" style="text-align: center; ">
                <img src="data:image/png;base64,'<?=$imgGooglePlayIos;?>'"  style="height:70px;width:auto;margin: 0 0 0 0">
            </td>  
        </tr>

        <tr>
            <td style="font-size: 11px;padding: 2px 4px"><img class="text-right" src="data:image/png;base64,'<?=$imgFacebook;?>'"  style="height:18px;width:auto;margin: 0 4px -2px 0"> ASDP Indonesia Ferry</td>
            <td style="font-size: 11px;padding: 2px 4px"><img class="text-right" src="data:image/png;base64,'<?=$imgWhatsapp;?>'" style="height:14px;width:auto;margin: 0 4px -2px 0"> 08111021191</td>
        </tr>

        <tr>
            <td style="font-size: 11px;padding: 2px 4px"><img class="text-right" src="data:image/png;base64,'<?=$imgCallCenter;?>'" style="height:18px;width:auto;margin: 0 4px -2px 0"> (021) 191</td>
            <td style="font-size: 11px;padding: 2px 4px"><img class="text-right" src="data:image/png;base64,'<?=$imgTwitter;?>'" style="height:14px;width:auto;margin: 0 4px -2px 0"> @asdp191</td>
        </tr>

      <tr>
            <td colspan=2>
                <div style="width:200px">
                    <span style="font-size:10px; font-weight: bold;"><?= $company_name->info; ?></span>
                    <div style="font-size:8px; width:100%">
                        NPWP: <?= $eticket_npwp->info; ?><br>
                        <?= $eticket_address->info; ?>
                    </div>
                </div>
            </td>            
            <td></td>
            <td></td>

        </tr>

    </table>
</footer>
</html>
<?php

    $content = ob_get_clean();
    
    $dompdf = new \Dompdf\Dompdf();
    $dompdf->set_option('isHtml5ParserEnabled', true); 

    $dompdf->loadHtml($content);

    // (Optional) Setup the paper size and orientation
    $dompdf->setPaper('A4', 'portrait');

    // Render the HTML as PDF
    $dompdf->render();


    // Output the generated PDF to Browser
    $dompdf->add_info('Title', 'E-Receipt '.$vehicle[0]->booking_code);
    $dompdf->stream('E-Receipt_'.$vehicle[0]->booking_code.".pdf", array("Attachment" => false));

    exit(0);

    
  ?>