    <html>
        <head>
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
                margin-bottom: 20px;

                /** Extra personal styles **/
                /* background-color: #03a9f4;
                color: white;
                text-align: center;
                line-height: 1.5cm; */
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
             
            if($passanger[0]->ticket_type == 2) //tipe tiket 2 itu SAB
            {
                $header ="<b>SKPT</b>";

                $dataPemesan = array(
                    array("label"=>"Nama","data"=> $passanger[0]->created_by),
                    array("label"=>"Instansi","data"=> $passanger[0]->customer_name),
                    array("label"=>"Email","data"=> $passanger[0]->email),
                    array("label"=>"Nomor Telepon","data"=> $passanger[0]->phone_number),
                );
                
            }
            else
            {
                if(strtoupper($passanger[0]->channel) =="IFCS" )
                {
                    $header = ' <img  src="data:image/png;base64,'. $imageIFCS .'" style="width:230px; height:50px; margin-top:30px" >';
                }
                else
                {
                    $header =" ";
                }

                $dataPemesan = array(
                    array("label"=>"Nama","data"=> $passanger[0]->customer_name),
                    array("label"=>"Email","data"=> $passanger[0]->email),
                    array("label"=>"Nomor Telepon","data"=> $passanger[0]->phone_number),
                );
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
                                <td> <h3 class="text-big">E-Tiket</h3>Kapal Penyeberangan</td>
                                <td class="text-big" style="font-size:45px; text-align:right; padding-right:20px; padding-bottom:20px;" >
                                    <?= $header; ?>
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
                    
                    $getDate=$passanger[0]->depart_time_start;
                    $departStartTime = date('H:i:s', strtotime($getDate." -".$time_checkin_start_information));
                    $departEndTime = date('H:i:s', strtotime($getDate." +".$time_checkin_end_information));

                    $scheduleSelectedTime = format_time($passanger[0]->depart_time_start)." - ".format_time($passanger[0]->depart_time_end);  
                    if($passanger[0]->ship_class_id==2)
                    {
                        $scheduleSelectedTime = format_time($passanger[0]->depart_time_start);  
                    }
                    
                ?>
                <div class="transform2" style="font-size: <?= $fontSize; ?>px; " >
                    <!-- permintaan asdp untuk menampilkan tanggal saat di download -->
                    <?= $waterMark; ?>
                </div>
            </header>

            <footer>
                <table  class="table table-detail full-width" style="width: 100%; margin:auto;" border="0">
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
                        <td rowspan="3" width="150px" style="font-size: 9;">
                        Dikelola oleh:
                        <img src="data:image/png;base64,<?= $imageFooter1 ?>" style="width:100px;  margin-bottom:-15px;" >
                        </td>
                        <td colspan='3'>
                        <span style="text-align: center; font-size:12px;">Hubungi kami melalui:</span>
                        </td>
                        <td style="width:20%; text-align: center; font-size:10px; ">
                            <span ><b>Unduh Aplikasi Ferizy</b></span>
                        </td>
                    </tr>
                    <tr>
                        <td style="font-size: 11px;" width="110"><img class="text-right" src="data:image/png;base64,<?= $imageFooter2 ?>"  style="height:18px;width:auto;margin: 0 4px -2px 0"> cs@asdp.id</td>
                        <td style="font-size: 11px;padding: 2px 4px"><img class="text-right" src="data:image/png;base64,<?= $imageFooter3 ?>" style="height:18px;width:auto;margin: 0 4px -2px 0"> (021) 191</td>
                        <td style="font-size: 11px;padding: 2px 4px"><img class="text-right" src="data:image/png;base64,<?= $imageFooter4 ?>" style="height:14px;width:auto;margin: 0 4px -2px 0"> 08111021191</td>
                        <td rowspan="2" style="text-align: center;">
                            <img class="text-right" src="data:image/png;base64,<?= $imageFooter5 ?>" style="height:70px;width:auto;margin: 0 0 0 0;">
                        </td>
                    </tr>
                    <tr>
                        <td style="font-size: 11px;padding: 2px 4px"><img class="text-right" src="data:image/png;base64,<?= $imageFooter6 ?>" style="height:18px;width:auto;margin: 0 4px -2px 0"> ASDP Indonesia Ferry</td>
                        <td style="font-size: 11px;padding: 2px 4px"><img class="text-right" src="data:image/png;base64,<?= $imageFooter7 ?>" style="height:18px;width:auto;margin: 0 4px -2px 0"> @asdp191</td>
                        <td style="font-size: 11px;padding: 2px 4px"><img class="text-right" src="data:image/png;base64,<?= $imageFooter8 ?>" style="height:14px;width:auto;margin: 0 4px -2px 0"> @asdp191</td>
                    </tr> 
                    <tr>
                        <td colspan=2>
                            <div style="width:200px; margin-top:-10px;">
                                <span style="font-size:10px; font-weight: bold;"><?= $company_name->info; ?></span>
                                <div style="font-size:8px; width:100%">
                                    NPWP: <?= $eticket_npwp->info; ?><br>
                                    <?= $eticket_address->info; ?>
                                </div>
                            </div>
                        </td>
                        
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                </table>
            </footer>

            <!-- Wrap the content of your PDF inside a main tag -->
            <main>
                <p style="page-break-after: never;">
                    <div width="100%">

                        <table class="my-table" >
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
                                            <span class="text-big2" style="color:#f88f27; font-weight:bold;"><?= $passanger[0]->booking_code; ?></span><br>
                                            <img   src="data:image/png;base64,<?= $booking_qr ?>" style="width:130px; height:130px; " ><br/>
                                            <span style=" font-size:14px; ">www.ferizy.com </span>

                                        <div>
                                    </td>
                                    <td colspan="3" rowspan="1" >
                                        <p></p>
                                        <div class="text-big2" style="font-weight:bold;  border-bottom:1px dashed black; width:345px; padding-bottom:5px;">
                                            Jadwal masuk Pelabuhan (Check In)
                                            
                                        </div>
                                        
                                        <div style="font-size:13px; font-weight: bold;  padding-top:5px;">
                                        <?php echo hari_ini(date('D', strtotime($passanger[0]->depart_date))).", ".format_date($passanger[0]->depart_date)."<br />".format_time($departStartTime)." - ".format_time($departEndTime) ?>
                                        </div> 
                                    </td>
                                    <td rowspan="3" width="30%" style="text-align:center; vertical-align: text-top; ">
                                        <p style="margin-top:-30px;">
                                            <h1 style="color:#4B77BE;"><?= $passanger[0]->ship_class ?></h1>
                                            <span style="color: #292929; font-size:14px; ">
                                                Pejalan Kaki<p></p>
                                                <!-- Kendaraan - <?= str_replace("GOLONGAN","Gol.",strtoupper($passanger[0]->vehicle_name))  ?> <br>
                                                Nomor Polisi - <?= $passanger[0]->id_number ?> -->
                                            </span>                                        
                                        </p>
                                        <p></p>
                                        <p></p>
                                        <p></p>
                                        <p></p>
                                        <p></p>
                                        <p class="text-small-grey">Jadwal yang Dipilih<br>
                                        <?php echo  hariku(date('D', strtotime($passanger[0]->depart_date))).", ".format_date($passanger[0]->depart_date)." ".$scheduleSelectedTime;  ?>
                                        </p>
                                    </td>
                                </tr>
                                <tr>
                                    <td >
                                        <?= $passanger[0]->origin ?><br>
                                        <span class="text-small-grey"><?= $passanger[0]->origin_city ?></span>
                                    </td>
                                    <td >
                                        <img   src="data:image/png;base64,<?= $imageShip ?>" style="height:40px;width:auto;" >    
                                    </td>
                                    <td >
                                        <?= $passanger[0]->destination ?><br />
                                        <span class="text-small-grey"><?= $passanger[0]->destination_city ?></span>

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
                        <?php $no=1; foreach ($passanger as $key => $value)  {
                        
                        $bgColor="";
                        if($no % 2 == 0 ){
                            $bgColor="background-color:#f2f2f2;"; }
                        
                            $rowspan ="";
                            $rowVaccine="";
                            $reasonVaccine ="";

                            $rowTest="";

                            if(!empty($dataVaccine[$value->ticket_number])){
                                $vaccine = $dataVaccine[$value->ticket_number];
                                                           
                                $rowVaccine = '
                                    <tr style="'.$bgColor.'  " >
                                        <td></td>
                                        <td  colspan=3 style="text-align:justify;  padding:5px 0px 5px 0px; font-size:12px; border-top:1px solid black;" valign="top">                            
                                            <table width="100%" >
                                                <tr>
                                                    <td width="150px">Vaksin </td>
                                                    <td  width="10px">:</td>
                                                    <td   >'.$vaccine->vaccineStatus.'</td>
                                                </tr>
                                            </table>
                                        </td>
                                        
                                    </tr>
                                    <tr style="'.$bgColor.'" >
                                        <td></td>
                                        <td  colspan=3 style="text-align:justify;  padding:5px 0px 5px 0px; font-size:12px; " valign="top">
                                            <table width="100%" >
                                                <tr>
                                                    <td width="150px">Keterangan </td>
                                                    <td  width="10px">:</td>
                                                    <td>'.
                                                            // str_replace(array("<p>","</p>"),"",$vaccine->vaccineReason)
                                                            strip_tags(html_entity_decode($vaccine->vaccineReason))
                                                            
                                                            .'</td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>';       
                                    
                                    if($vaccine->config_test == "t")
                                    {
                                        $textResult = "-";
                                        $resultTest ="";
                                        if(!empty($dataTestStatus[$value->ticket_number]))
                                        {
                                            $testCovid = $dataTestStatus[$value->ticket_number];
                                            $resultTest = $testCovid->result;
                                            $textResult = strtoupper($testCovid->type).", ".$testCovid->result." ".format_date($testCovid->date)." ".format_time($testCovid->date);
                                        }

                                        $textResultColor = $textResult;
                                        if(strtoupper($resultTest) == "POSITIF")
                                        {
                                            $textResultColor = "<span style='color:red'>{$textResult}</span>";
                                        }
                                        
                                        if(!empty($vaccine->reason_test))
                                        {
                                            $textResultColor = "<span style='color:red'>".$textResult."</span>";
                                        }
                                        
                                        if($vaccine->under_age == "t")
                                        {
                                            $textResultColor = "<span style='color:red'>-</span>";
                                        }


                                        $rowTest = '
                                            <tr style="'.$bgColor.'  " >
                                                <td></td>
                                                <td  colspan=3 style="text-align:justify;  padding:5px 0px 5px 0px; font-size:12px; " valign="top">                            
                                                    <table width="100%" >
                                                        <tr>
                                                            <td width="150px">Hasil Test Covid-19  </td>
                                                            <td  width="10px">:</td>
                                                            <td >'.$textResultColor.'</td>
                                                        </tr>
                                                    </table>
                                                </td>
                                                
                                            </tr>
                                            <tr style="'.$bgColor.'" >
                                                <td></td>
                                                <td  colspan=3 style="text-align:justify;  padding:5px 0px 5px 0px; font-size:12px; " valign="top">
                                                    <table width="100%" >
                                                        <tr>
                                                            <td width="150px">Keterangan </td>
                                                            <td  width="10px">:</td>
                                                            <td>'.$vaccine->testReason.'</td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>';                                        

                                    }

                            }

                        ?>
                        <tr style="<?= $bgColor; ?> " >
                            <td style="text-align:center;  padding:5px 0px 5px 0px; font-size:12px; " valign="top" <?= $rowspan; ?>>
                                <?= $no++ ?>
                            </td>
                            <td style=" text-align:justify; padding:5px 0px 5px 0px; font-size:12px;" >
                                <?= $value->penumpang ?>
                            </td>
                            <td style=" text-align:justify; padding:5px 0px 5px 0px; font-size:12px;" >
                                <?= $value->id_number ?>
                            </td>
                            <td style=" text-align:justify; padding:5px 0px 5px 0px; font-size:12px;" >
                                <?= $value->passanger_type ?>
                            </td>
                        </tr>
                        <?= $rowVaccine; ?>
                        <?= $rowTest; ?>

                        
                        <?php  } ?>
                    
                        </table>
                        <p><hr style="height: 1px; background-color: #f0b700; border: none;" /></p >
                        <div class="text-bold" style="padding-bottom:10px; padding-top:5px;"><b>Informasi pemesan</b></div>
                        
                        <table style="width:100%;  border-collapse: collapse">

                            <?php for($i=0; $i<count($dataPemesan); $i++)  { 
                                $backGroundColor ="";
                                if($i % 2 == 0)
                                {
                                    $backGroundColor = "background-color:#f2f2f2";
                                }
                            ?>

                                <tr style=" <?= $backGroundColor; ?> ">
                                <td style="width:150px; text-align:justify;  padding:8px 0px 8px 0px; font-size:12px; " valign="top">
                                    <?= $dataPemesan[$i]['label']; ?>
                                </td>
                                <td style=" text-align:justify; padding:8px 0px 8px 0px; font-size:12px;" valign="top">: <?= $dataPemesan[$i]['data']; ?>
                                </td>

                            </tr>
                            <?php } ?>
                                                 
                        </table>         

                        
                    </div>                    
                </p>           
            </main>

        </body>
    <html>

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

        $dompdf->add_info('Title', 'Tiket Online '.$passanger[0]->booking_code);
        $dompdf->stream('Tiket_Online_'.$passanger[0]->booking_code.".pdf", array("Attachment" => false));


        //         $output = $dompdf->output();
        // file_put_contents(__DIR__ .'/Brochure.pdf', $output);
      

        exit(0);


    
  ?>

