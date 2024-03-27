<html>
        <head>
        <title><?= $title_pdf;?></title>
        <style>
            /** 
                Set the margins of the page to 0, so the footer and the header
                can be of the full height and width !
             **/
            @page {
                margin-top: 2cm;
                margin-left: 0cm;
                margin-right: 0cm;
                margin-bottom: 5cm;
                
            }

            /** Define now the real margins of every page in the PDF **/
            body {
                margin-top: 1cm;
                margin-left: 1cm;
                margin-right: 1cm;
                margin-bottom: 1cm;
                
                font-family: sans-serif;
            }

            /** Define the header rules **/
            header {
                position: fixed;
                /* top: -3cm; */
                top: -100px;;
                left: 0cm;
                right: 0cm;
                height: 1cm;

                margin-top: 1cm;
                margin-left: 1cm;
                margin-right: 1cm;
                margin-bottom: 1cm;


                /** Extra personal styles **/
                /* background-color: #03a9f4;
                color: white;
                text-align: center;
                line-height: 1.5cm; */
            }

             /** Define the footer rules **/
            footer {
                position: fixed; 
                bottom: -20px; 
                left: 0cm; 
                right: 0cm;
                height: 1cm;

                /* margin-top: 1cm; */
                margin-left: 1cm;
                margin-right: 1cm;
                margin-bottom: 10px;

                /** Extra personal styles **/
                /* background-color: #03a9f4;
                color: white;
                text-align: center;
                line-height: 1.5cm; */
            }
            /** Define the footer rules **/

            #footer tr {
                font-size:12px;
                text-align: right;
                padding: 4px 4px 4px 4px;
                margin: 0;
            }

            #footer td {
                text-align: left;
            }

            /** Define the footer rules **/
       
            .my-table{
                width:100%
            }

            .text-big
            {
                font-size:28px;
                color: #f88f27;
                margin: 0px;
            }

            .text-big2{
                font-size:20px;
            }

            .text-small-grey{
                color:#5b5c5b; 
                font-size:11px;
            }

            .text-bold {
                font-weight: 600;
                font-size: 13px;
      }

      .transform2{
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
        /* top: 16cm;
        left: 5%; */
    }


            

        </style>            
         <!-- ini hanya sementara nanti bisa di buat dinamis -->
         <?php 
        
        function hariku($hariku)
        {
            $hari = $hariku;

            switch ($hari) {
                case 'Sun':
                $hari_ini = "Minggu";
                break;

                case 'Mon':
                $hari_ini = "Senin";
                break;

                case 'Tue':
                $hari_ini = "Selasa";
                break;

                case 'Wed':
                $hari_ini = "Rabu";
                break;

                case 'Thu':
                $hari_ini = "Kamis";
                break;

                case 'Fri':
                $hari_ini = "Jumat";
                break;

                case 'Sat':
                $hari_ini = "Sabtu";
                break;

                default:
                $hari_ini = "Tidak di ketahui";
                break;
            }

            return $hari_ini;
            }

            
        ?>
        
        </head>
        <body>
            
            <!-- Define header and footer blocks before your content -->
            <header>
                <div width="100%">
                    <table class="my-table" >
                        <tbody>
                            <tr>
                                <td> <h3 class="text-big">E-tiket</h3>Kapal Penyeberangan</td>
                                <td class="text-big" style="font-size:45px; text-align:right; padding-right:20px; padding-bottom:20px;" >
                                    <b>SKPT</b>
                                </td>
                                <td  width="150px; "><img  src="data:image/png;base64,<?= $logo ?>" style="width:100%; height:70px;" ></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <?php 
                    /***
                     * length max 27 fontsize 54
                     * length 
                    */
                    $waterMark =$this->session->userdata("username")  ." (". date("Y-m-d") .")";

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
                <div class="transform2" style="font-size: <?= $fontSize; ?>px; " >
                    <!-- permintaan asdp untuk menampilkan tanggal saat di download -->
                    <?= $waterMark; ?>
                </div>
            </header>

            <footer>
                <table id="footer" cellspacing="0"> 
                    
                    <tr>
                        <td colspan="5" width="700"><hr style="height: 1px; background-color: #f0b700; border: none;" /></td>
                    </tr>
                    <tr>
                        <td colspan="5" style="padding: 2px 5px 0 8px;" ><span style="font-size: 11px;line-height: 1.5;"><b>Informasi, Syarat dan Ketentuan lebih lanjut dapat dilihat pada link berikut : www.ferizy.com/termsandconditions</b></span></td>
                    </tr>
                    <tr>
                        <td colspan='5' ><hr style="max-height: 3px; height: 1px; background-color: #f0b700; border: none;"  /></td>
                    </tr>
                    <!-- <tr>
                        <td colspan='5'  style="font-size: 8;padding: 2px 2px 0 8px;">                        
                            Dikelola oleh:<br>
                        </td>
                    </tr> -->

                    <tr>

                    <tr>
                        <td width="105" >
                            Dikelola oleh:
                        </td>
                        <td width="130">
                        Hubungi kami melalui:
                        </td>
                        <td width="95">
                        <p></p>
                        </td>
                        <td width="100">
                        <p></p>
                        </td>
                        <td width="100">
                        <p style="margin:2px 0 0 0; text-align: center; font-size:10px;"><b>Unduh Aplikasi Ferizy</b></p>
                        </td>
                    </tr>

                    <tr>  
                        <td rowspan="3" >
                            <img src="data:image/png;base64,'<?=$imgLogoPrimary;?>'" style="width:100px;">
                        </td>
                        <td style="font-size: 11px;padding: 2px 4px ;"><img class="text-right" src="data:image/png;base64,'<?=$imgMail;?>'"  style="height:18px;width:auto;margin: 0 4px -2px 0"> cs@asdp.id</td>
                        <td style="font-size: 11px;padding: 2px 4px"><img class="text-right" src="data:image/png;base64,'<?=$imgCallCenter;?>'" style="height:18px;width:auto;margin: 0 4px -2px 0"> (021) 191</td>
                        <td style="font-size: 11px;padding: 2px 4px"><img class="text-right" src="data:image/png;base64,'<?=$imgWhatsapp;?>'" style="height:14px;width:auto;margin: 0 4px -2px 0"> 08111021191</td>
                        <td rowspan="3" style="text-align: center; ">
                            <img src="data:image/png;base64,'<?=$imgGooglePlayIos;?>'"  style="height:70px;width:auto;margin: 0 0 0 0">
                        </td>  

                    </tr>

                    <tr>
                        <td style="font-size: 11px;padding: 2px 4px"><img class="text-right" src="data:image/png;base64,'<?=$imgFacebook;?>'"  style="height:18px;width:auto;margin: 0 4px -2px 0"> ASDP Indonesia Ferry</td>
                        <td style="font-size: 11px;padding: 2px 4px"><img class="text-right" src="data:image/png;base64,'<?=$imgInstagram;?>'"style="height:18px;width:auto;margin: 0 4px -2px 0"> @asdp191</td>
                        <td style="font-size: 11px;padding: 2px 4px"><img class="text-right" src="data:image/png;base64,'<?=$imgTwitter;?>'" style="height:14px;width:auto;margin: 0 4px -2px 0"> @asdp191</td>
                    </tr>

                    <tr>
                    </tr>

                    <tr>
                        <td colspan="4" >
                            <div style="width:200px; margin-top:-10px;">
                                <span style="font-size: 10px; font-weight: bold;"><?=$companyName->info;?></span>
                                <div style="font-size: 8px; width:100%">NPWP : <?=$eticketNpwp->info;?>
                                <br><?=$eticketAddress->info;?>
                                </div>
                            </div>
                        </td>
                    </tr>
                </table>
            </footer>
            
            <!-- Wrap the content of your PDF inside a main tag -->
            <main>
                <p style="page-break-after: never;">
                    <div width="100%">

                        <table class="my-table"  style="margin-top:-30px;" >
                            <tbody>
                                <!-- <tr>
                                    <td width="30%">&nbsp;</td>
                                    <td width="20%">&nbsp;</td>
                                    <td width="10%">&nbsp;</td>
                                    <td width="10%">&nbsp;</td>
                                    <td width="30%">&nbsp;</td>
                                </tr> -->

                                <tr >
                                    <td rowspan="3" width="20%" style="text-align:center; padding-right:10px;">
                                        <div style="margin-top:-35px;" >
                                            <span class="text-big2">Kode Booking</span>
                                            <hr>
                                            <span class="text-big2" style="color:#f88f27; font-weight:bold;"><?= $booking_code ?></span><br>
                                            <img   src="data:image/png;base64,<?= $booking_qr ?>" style="width:130px; height:130px; " ><br/>
                                            <span style=" font-size:14px; ">www.ferizy.com </span>

                                        <div>
                                    </td>
                                    <td colspan="3" rowspan="1" >
                                        <p></p>
                                        <div class="text-big2" style="font-weight:bold;  border-bottom:1px dashed black; width:345px; padding-bottom:5px;">
                                            Jadwal masuk Pelabuhan (Check In)
            
                                        </div>
                                        <?php 
                                            $date = format_date($passenger[0]->depart_date);
                                            $checkin_start = strtotime($passenger[0]->depart_time_start."-".$checkinStart." hours");
                                            $checkin_end   = strtotime($passenger[0]->depart_time_start."+".$checkinEnd." hours");
                                            $time_start    = date('H:i',$checkin_start);
                                            $time_end      = date('H:i',$checkin_end);
                                        ?>
                                        <div style="font-size:13px; font-weight: bold;  padding-top:5px;">
                                            <?php echo hariku(date('D', strtotime($passenger[0]->depart_date))).",$date<br />$time_start - $time_end" ?>
                                        </div> 
                                    </td>
                                    <td rowspan="3" width="30%" style="text-align:center; vertical-align: text-top; ">
                                        <p style="margin-top:10px;">
                                            <!-- <h1 style="color:#4B77BE;"><?= $passenger[0]->ship_class ?></h1> -->
                                            <?php 
                                                if($passenger[0]->ship_class_id == 2){                    
                                                    echo '<h1 style=" font-size:26px;color: #4B77BE;"><b>';
                                                    echo $passenger[0]->ship_class ;
                                                    echo '</b></h1>';
                                                }
                                                else{
                                                    echo '<h1 style=" font-size:26px;color: #f58220;"><b>';
                                                    echo $passenger[0]->ship_class ;
                                                    echo '</b></h1>';
                                                }
                                            ?>
                                            <span style=" font-size:14px; color:grey;" > Pejalan Kaki </span>                                         
                                        </p>
                                        <p></p>
                                        <p></p>
                                        <p></p>
                                        <p></p>
                                        <p></p>
                                        <?php 
                                            $date = format_date($passenger[0]->depart_date);
                                            $shipClass = $passenger[0]->ship_class_id;
                                            $schedule ="";
                                            if ($shipClass == 2){
                                                $schedule = $date ." ". format_time($passenger[0]->depart_date . " " . $passenger[0]->depart_time_start);
                                            }else{
                                                $schedule = $date ." ". format_time($passenger[0]->depart_date . " " . $passenger[0]->depart_time_start)." - ".format_time($passenger[0]->depart_time_end);
                                            }
                                        ?>    
                                        <p class="text-small-grey">Jadwal yang Dipilih<br>
                                        <?php echo  hariku(date('D', strtotime($passenger[0]->depart_date))).", ". $schedule?>
                                        </p>
                                    </td>
                                </tr>
                                <tr>
                                    <td >
                                        <?= $passenger[0]->origin ?><br>
                                        <span class="text-small-grey"><?= $passenger[0]->origin_city ?></span>
                                    </td>
                                    <td >
                                        <img   src="data:image/png;base64,<?= $imageShip ?>" style="height:40px;width:auto;" >    
                                    </td>
                                    <td >
                                        <?= $passenger[0]->destination ?><br />
                                        <span class="text-small-grey"><?= $passenger[0]->destination_city ?></span>

                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3" rowspan="1" class="text-small-grey" style="font-style:italic">
                                        <div style="margin-top:-15px;">*Nama Kapal Akan diinformasikan saat tiba di pelabuhan </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>    
                        <!-- <hr style="height: 1px; background-color: #f0b700; border: none;" />           -->
                        <table class="my-table" style="margin-top:-30px;">
                            <tbody>
                                <tr>
                                    <td colspan="6"  class="text-small-grey" style="font-style:italic">
                                        <hr style="height: 1px; background-color: #f0b700; border: none;" />          
                                    </td>
                                </tr>                                                                
                                <tr>
                                    <td style="width: 5%;font-size: 11px;padding: 6px 4px;vertical-align: top">
                                        <img class="text-right" src="data:image/png;base64,<?= $imageInfo1 ?>" style="height:24px;width:auto; display: block; ">
                                    </td>
                                    <td style="font-size: 11px;line-height: 1.2;text-align:justify;vertical-align: text-top;"  ><?= $eticket_alert_1->info ?>
                                        </td>
                                    <td style="width: 5%;font-size: 11px;padding: 6px 4px;vertical-align: top">
                                        <img class="text-right" src="data:image/png;base64,<?= $imageInfo2 ?>" style="height:24px;width:auto;display: block;">
                                    </td>
                                    <td style="width: 185px;font-size: 11px;line-height: 1.2;text-align:justify;vertical-align: text-top;" valign="top"><?= $eticket_alert_2->info ?></td>
                                    <td style="width: 5%;font-size: 11px;padding: 6px 4px;vertical-align: top">
                                        <img class="text-right" src="data:image/png;base64,<?= $imageInfo3 ?>" style="height:24px;width:auto;display: block;">
                                    </td>
                                    <td style="width: 185px;font-size: 11px;line-height: 1.2;text-align:justify;vertical-align: text-top;" valign="top"><?= $eticket_alert_3->info ?></td>
                                </tr>
                            </tbody>
                        </table>
                        <hr style="height: 1px; background-color: #f0b700; border: none;" />          
                        <div class="text-bold" style="padding-bottom:10px; padding-top:5px;"><b>Rincian data penumpang</b></div>
                        <table style="width:100%;  border-collapse: collapse; ">
                        <tr style="background-color: #ffeed1;  ">
                            <td style="width:5%; text-align:justify;  padding:8px 0px 8px 5px; font-size:13px;" valign="middle" >                
                                NO
                            </td>
                            <td style="width:45%; text-align:justify; padding:8px 0px 8px 0px; font-size:13px;" valign="middle">
                                Nama Penumpang
                            </td>
                            <td style="width:30%; text-align:justify; padding:8px 0px 8px 0px; font-size:13px;" valign="middle">
                                Nomor ID
                            </td>
                            <td style="width:20%; text-align:justify; padding:8px 0px 8px 0px; font-size:13px;" valign="middle">
                                Jenis Penumpang
                            </td>              

                        </tr>
                        <?php $no=1; foreach ($passenger as $key => $value) { 
                        
                        $bgColor="";
                        if($no % 2 == 0 )
                        {
                            $bgColor="background-color:#f2f2f2;";
                        }  
                        ?>
                        <tr style="<?= $bgColor; ?>" >
                            <td style="text-align:center;  padding:5px 0px 5px 0px; font-size:12px; " valign="top">
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
                        <p><hr style="height: 1px; background-color: #f0b700; border: none;" /></p >
                        <div class="text-bold" style="padding-bottom:10px; padding-top:5px;"><b>Informasi pemesan</b></div>
                        
                        <table style="width:100%;  border-collapse: collapse">

                            
                                <tr style="background-color:#f2f2f2; ">
                                    <td style="width:25%; text-align:justify;  padding:8px 0px 8px 0px; font-size:12px; " valign="top">
                                        Nama
                                    </td>
                                    <td style="width:25%; text-align:justify; padding:8px 0px 8px 0px; font-size:12px;" valign="top">: <?= $passenger[0]->created_by ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width:25%; text-align:justify; padding:8px 0px 8px 0px; font-size:12px;" valign="top">
                                        Instansi
                                    </td>
                                    <td style="width:75%; text-align:justify; padding:8px 0px 8px 0px; font-size:12px;" valign="top">: <?= $passenger[0]->instansi ?>
                                    </td>
                                </tr>
                                <tr style="background-color:#f2f2f2; " >
                                    <td style="width:25%; text-align:justify; padding:8px 0px 8px 0px; font-size:12px;" valign="top">
                                        Email
                                    </td>
                                    <td style="width:75%; text-align:justify; padding:8px 0px 8px 0px; font-size:12px;" valign="top">: <?= $passenger[0]->email ?>
                                    </td>
                                </tr> 
                                <tr>
                                    <td style="width:25%; text-align:justify; padding:8px 0px 8px 0px; font-size:12px;" valign="top">
                                    Nomor Telepon
                                    </td>
                                    <td style="width:75%; text-align:justify; padding:8px 0px 8px 0px; font-size:12px;" valign="top">: <?= $passenger[0]->phone_number ?>
                                    </td>
                                </tr>
                                                                  
                        </table>         

                        
                    </div>                    
                </p>           
            </main>

        </body>
    <html>


