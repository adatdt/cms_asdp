<?php


  if (!defined('BASEPATH'))
    exit('No direct script access allowed');
  ?>
  <page backtop="30mm" backbottom="45mm" backleft="5mm" backright="5mm">

    <style>
        html{
          font-family: Arial;
        }

        .text-center {
        text-align: center;
        }

        .text-right {
        text-align: right;
        }
        .text-left {
        text-align: left;
        }        

        .title {
        color: #ccc;
        }

        .header {
        border-collapse: collapse;
        width: 100%;
        }

        .header td, .header th {
        /*border: 1px solid #ddd;*/
        padding: 10px;
        /* vertical-align: top; */
        }

        /*.header tr:nth-child(even){background-color: #f2f2f2;}*/

        /*.header tr:hover {background-color: #ddd;}*/

        .header th {
        padding-top: 12px;
        padding-bottom: 12px;
        text-align: left;
        background-color: #4CAF50;
        color: white;
        }

        .logo{
          width: 100%;
          border-collapse: collapse;
          /* margin: 0 15px; */
        }

        span{
          color: black;
        }

        #passanger{
          border-collapse: collapse;
          width: 100%;
        }

        #passanger td, #passanger th {
        /*border: 1px solid #ddd;*/
        padding: 8px;
        vertical-align: top;
        }

        #passanger th {
        padding-top: 12px;
        padding-bottom: 12px;
        text-align: left;
        background-color: #0054a6;
        color: white;
        }
        .hr1{
            height: 1px;
            background-color: #f0b700;
            border: none;
        }

        @page{size: auto; margin: 0mm;}
        .clearfix::after {
          content: "";
          clear: both;
          display: table;
        }        

    </style>
  <page_header>
    <table style="width:100%" align="center" border="0">

        <tr>
          <td style="width:50%;vertical-align:middle;border-bottom: 0px solid #f58220;height: 70px;padding: 10px 250px 10px 20px;">
            <h3 style="font-size:28px;color: #f88f27;margin: 0px;">E-tiket</h3>
            <span style="font-size:16px;opacity: 0.5">Kapal Penyeberangan</span>
          </td>
          <td class="text-right" style="width:20%; vertical-align:middle;  border-bottom: 0px solid #f58220;height: 70px;  padding: 10px 10px 25px 0px">
            <span style="color: #f88f27; font-size:45px;"><b>SAB</b></span>
            
          </td>          
          <td class="text-left" style="width:30%;border-bottom: 0px solid #f58220;height: 70px; padding-left:10px  ">
            <img  src="assets/img/img/ferizy-logo.png" style="height:70px;width:auto;">
          </td>
        </tr>

    </table>
    <p></p>
  </page_header>

  <page_footer>
    <table  class="table table-detail full-width" style="width: 100%; margin:auto;" border="0">
      <tr>
        <td colspan="5" width="700"><hr /></td>
      </tr>
      <tr>
        <td colspan="5" style="padding: 2px 5px 0 8px;" ><span style="font-size: 11px;line-height: 1.5;"><b>Informasi, Syarat dan Ketentuan lebih lanjut dapat dilihat pada link berikut : www.ferizy.com/termsandconditions</b></span></td>
      </tr>
      <tr>
        <td colspan='5' ><hr style="max-height: 3px;" /></td>
      </tr>
      <tr>
        <td colspan='5' valign='top' style="font-size: 8;padding: 2px 5px 0 8px;">
          
           <b>Dikelola oleh:</b><br>
        </td>
      </tr>


      <tr>
        <td rowspan="4" style="width:17%;padding: 15px 5px 0px 0;">
          <img src="assets/img/img/LOGO_ASDP_Primary.png" style="width:100px;">
        </td>
        <td colspan='3'>
          <b>Hubungi kami melalui:</b>
        </td>
        <td>
            <p style="margin:2px 0 0 0; text-align: center"><b>Unduh Aplikasi Ferizy</b></p>
        </td>
      </tr>
      <tr>
        <td style="font-size: 11px;padding: 2px 4px"><img class="text-right" src="assets/img/img/mail.png" style="height:18px;width:auto;margin: 0 4px -2px 0"> <b>cs@indonesiaferry.co.id</b></td>
        <td style="font-size: 11px;padding: 2px 4px"><img class="text-right" src="assets/img/img/call-center.png" style="height:18px;width:auto;margin: 0 4px -2px 0"> <b>(021) 191</b></td>
        <td style="font-size: 11px;padding: 2px 4px"><img class="text-right" src="assets/img/img/whatsapp.png" style="height:14px;width:auto;margin: 0 4px -2px 0"> <b>08111021191</b></td>
        <td rowspan="4" style="text-align: center;">
          <img class="text-right" src="assets/img/img/google-play-ios.png" style="height:50px;width:auto;margin: 0 0 0 0">
        </td>
      </tr>
      <tr>
        <td style="font-size: 11px;padding: 2px 4px"><img class="text-right" src="assets/img/img/facebook.png" style="height:18px;width:auto;margin: 0 4px -2px 0"> <b>ASDP Indonesia Ferry</b></td>
        <td style="font-size: 11px;padding: 2px 4px"><img class="text-right" src="assets/img/img/instagram.png" style="height:18px;width:auto;margin: 0 4px -2px 0"> <b>@asdp191</b></td>
        <td style="font-size: 11px;padding: 2px 4px"><img class="text-right" src="assets/img/img/twitter.png" style="height:14px;width:auto;margin: 0 4px -2px 0"> <b>@asdp191</b></td>
      </tr> 
      <tr>
        <td>
          
        </td>
        <td>
          
        </td>
        <td>
          
        </td>
      </tr>
    </table>
    <br>        
  </page_footer>

        <p></p>
        <table style="width:100%; padding:0px;">
          <tr>
              <td style="width:20%; text-align:center">
                <span style="color:grey; font-size:18px;"><b>Kode Booking</b></span> <br />
                <hr style="color:grey;" />
                <span style="color: #f88f27; font-size:24px; "><b><?= $booking_code ?></b></span> <br /><br />
                <qrcode value="<?php echo $booking_qr; ?>" ec="H" style="width: 90%; background-color: white; color: black; border:none"></qrcode>
                <span style="color:grey; font-size:14px;">www.ferizy.com </span>
              </td>
              <td style="width:50%; padding-left:15px;">
              <p></p>
                <span style="color:grey; font-size:18px;  " ><b>Jadwal masuk Pelabuhan (Check In) </b></span>
                <hr style=" margin-top:-7px;border: none;
                              border-top: 0.8px dashed #00000;
                              color: grey;
                              background-color: #fff;
                              height: 1px;"> 
                <span style="color:grey; font-size:18px;"><?php echo hari_ini(date('D', strtotime($passanger[0]->depart_date))).", ".format_date($passanger[0]->depart_date)."<br />".format_time($passanger[0]->depart_time_start)." - ".format_time($passanger[0]->depart_time_end) ?>
                </span>
                <br>
                <table style="width:100%">
                    <tr>
                        <td style="width:20%; " valign="bottom" ><span style="color:grey; font-size:16px;"><?= $passanger[0]->origin ?></span></td>
                        <td style="width:50%; text-align:center; padding-left:30px;"><img class="text-right" src="assets/img/img/cruise-with-arrow.png" style="height:40px;width:auto;"></td>
                        <td style="width:20%; " valign="bottom" ><span style="color:grey; font-size:16px;"><?= $passanger[0]->destination ?></span></td>
                    </tr>
                    <tr valign="top">
                        <td ><span style="color:grey; font-size:10px;"><?= $passanger[0]->origin_city ?></span></td>
                        <td ></td>
                        <td ><span style="color:grey; font-size:10px;"><?= $passanger[0]->destination_city ?></span></td>
                    </tr>                    
                </table>
                <p></p>
                <p></p>
                <br />
                <i style="color:grey; font-size:12px;" >*Nama Kapal Akan diinformasikan Saat Tiba dipelabuhan </i>
              
              </td>
              <td style="width:30%; text-align:center; color:#034cab;">
                <h1><?= $passanger[0]->ship_class ?></h1>                
                <br /> <span style=" font-size:14px; color:grey;" > Penumpang </span> 
                <p></p>
                <p></p>
                <p></p>
                
                <br /><span style="color:grey; font-size:12px;" >Jadwal Yang dipilih </span>
                <br /><span style="color:grey; font-size:12px;" ><?php echo hari_ini(date('D', strtotime($passanger[0]->depart_date))).", ".format_date($passanger[0]->depart_date)."<br />".format_time($passanger[0]->depart_time_start)." - ".format_time($passanger[0]->depart_time_end) ?></span>
            </td>
          </tr>

        </table>
        <hr style="color: #ffeb99; font-size:18px;" />
        <table style="width:100%; ">
          <tr>
              <td style="width:5%;" valign="top">
              <img class="text-right" src="assets/img/img/id_card.png" style="height:24px;width:auto;margin: 5px 4px -32px 10px;display: block;">
              </td>
              <td style="width:28%; text-align:justify; font-size:12; padding-left:5px; padding-right:5px;  " valign="top">
                  <?= $eticket_alert_1->info ?>
              </td>
              <td style="width:5%;" valign="top">
              <img class="text-right" src="assets/img/img/printer2.png" style="height:24px;width:auto;margin: 5px 4px -32px 10px;display: block;">
              </td>
              <td style="width:28%; text-align:justify; font-size:12; padding-left:5px; padding-right:5px; " valign="top">
                <?= $eticket_alert_2->info ?>
              </td>
              <td style="width:5%;" valign="top">
              <img class="text-right" src="assets/img/img/expired_new.png" style="height:24px;width:auto;margin: 5px 4px -32px 10px;display: block;">
              </td>
              <td style="width:29%; text-align:justify; font-size:12; padding-left:5px; padding-right:5px; " valign="top">
                <?= $eticket_alert_3->info ?>                  
              </td>

          </tr>

        </table>
        <hr style="color:#ffeb99; font-size:18px;" />
        <p><span style="font-size:14px;" >Rincian data penumpang</span></p>
        <table style="width:100%;  border-collapse: collapse">
          <tr style="background-color: #ffffcc;  ">
              <th style="width:5%; text-align:justify;  padding:8px 0px 8px 0px; font-size:14px;" valign="middle" >
                
                  NO
              </th>
              <th style="width:45%; text-align:justify; padding:8px 0px 8px 0px; font-size:14px;" valign="middle">
                Nama Penumpang
              </th>
              <th style="width:30%; text-align:justify; padding:8px 0px 8px 0px; font-size:14px;" valign="middle">
                Nomor ID
              </th>
              <th style="width:20%; text-align:justify; padding:8px 0px 8px 0px; font-size:14px;" valign="middle">
                Jenis Penumpang
              </th>              

          </tr>
        <?php $no=1; foreach ($passanger as $key => $value) { 
          
          $bgColor="";
          if($no % 2 == 0 )
          {
            $bgColor="background-color:#f2f2f2;";
          }  
        ?>
          <tr style="<?= $bgColor; ?>" >
              <td style="text-align:justify;  padding:5px 0px 5px 0px; font-size:12px; " valign="top">
                  <?= $no++ ?>
              </td>
              <td style=" text-align:justify; padding:5px 0px 5px 0px; font-size:12px;" valign="top">
                <?= $value->penumpang ?>
              </td>
              <td style=" text-align:justify; padding:5px 0px 5px 0px; font-size:12px;" valign="top">
                <?= $value->id_number ?>
              </td>
              <td style=" text-align:justify; padding:5px 0px 5px 0px; font-size:12px;" valign="top">
                <?= $value->passanger_type ?>
              </td>
          </tr>

        <?php } ?>
      
        </table>
        <p><hr style="color: #ffeb99; font-size:18px;" /></p >
        <span style="font-size:14px;" >Informasi pemesanan</span>
        <p>
        <table style="width:100%;  border-collapse: collapse">

          <tr style="background-color:#f2f2f2; ">
              <td style="width:25%; text-align:justify;  padding:2px 0px 2px 0px; font-size:12px; " valign="top">
                  Nama
              </td>
              <td style="width:25%; text-align:justify; padding:2px 0px 2px 0px; font-size:12px;" valign="top">: <?= $passanger[0]->created_by ?>
              </td>

          </tr>

          <tr>
              <td style="width:25%; text-align:justify; padding:2px 0px 2px 0px; font-size:12px;" valign="top">
                  Instansi
              </td>
              <td style="width:75%; text-align:justify; padding:2px 0px 2px 0px; font-size:12px;" valign="top">: <?= $passanger[0]->instansi ?>
              </td>

          </tr>
          <tr  style="background-color:#f2f2f2; " >
              <td style="width:25%; text-align:justify; padding:2px 0px 2px 0px; font-size:12px;" valign="top">
                  Email
              </td>
              <td style="width:75%; text-align:justify; padding:2px 0px 2px 0px; font-size:12px;" valign="top">: <?= $passanger[0]->email ?>
              </td>
          </tr>    
          <tr>
              <td style="width:25%; text-align:justify; padding:2px 0px 2px 0px; font-size:12px;" valign="top">
                  No Telepon
              </td>
              <td style="width:75%; text-align:justify; padding:2px 0px 2px 0px; font-size:12px;" valign="top">: <?= $passanger[0]->phone_number ?>
              </td>

          </tr>                

        </table></p> 
                
  </page>
  <?php


    if($email == 0){
      $content = ob_get_clean();
      $date = date('ymdhis');
      $name = 'TIKET_SAB_ASDP_'.$booking_code.'_'.$date.'.pdf';
      try
      {
        $html2pdf = new HTML2PDF('P', 'A4', 'en', false, 'UTF-8', array(10, 10, 10,10,10,10));
        // $html2pdf = new Spipu\Html2Pdf\HTML2PDF('P', 'A4', 'en', false, 'UTF-8', array(10, 10, 10,10,10,10));
        $html2pdf->pdf->SetDisplayMode('fullpage');
        $html2pdf->pdf->SetTitle($name);
        $html2pdf->writeHTML($content);
        ob_end_clean();
        $html2pdf->Output($name, 'I');
      }
      catch(HTML2PDF_exception $e) {
          echo $e;
          exit;
      }
    }

    
  ?>

