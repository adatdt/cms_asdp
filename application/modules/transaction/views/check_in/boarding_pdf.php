<style type="text/css">

@page {
                margin-top: 0cm;       
                margin-left: 0cm;
                margin-right: 0cm;
                margin-bottom: 0cm;             
            
            }
    table.blueTable 
    {
        /* border: 1px solid black;         */
        width: 100%;
        text-align: left;
        border-collapse: collapse;
        
    }
    table.blueTable td, table.blueTable th {
        padding: 3px 2px;
        /* padding:0px; */
    }

    body {
        font-family: Tahoma, sans-serif !important;
    }    

    .transform{
        font-size: 34px;
        color: rgba(128, 128, 128, 0.30); 
        position: absolute;        
        font-weight: bold;
        z-index: -1;
        top: 170px;
        left: 20%;;
    }
    .text-footer3{
        color: black;
        font-size: 8px;
        text-align:center;
        /* word-wrap: break-word */
    }    

    .transform2{
        transform: rotate(-19.54deg); 
        transform-origin: 0 0; 
        text-align: center; 
        color: rgba(128, 128, 128, 0.30); 
        font-family:
        Inter; font-weight: 700;
        word-wrap: break-word;
        position: absolute;
        padding-top: 100%;
        padding-bottom: 100%;
        /* border: 1px solid black; */
        width:100%;
        height:100%;
        margin-left:-110px;
        top: 50px;
    }


    .text-header{
        color: black;
        font-size: 14px;
        font-style: italic;
        font-weight: 400;
        word-wrap: break-word
    }
    .text-header2{
        color: black;
        font-size: 13px;
        font-weight: bold;
        /* word-wrap: break-word */
    }
    .text-body{
        color: black;
        font-size: 11px;
        /* word-wrap: break-word */
    }    

    .text-footer{
        color: black;
        font-size: 9px;
        text-align:center;
        /* word-wrap: break-word */
    }        

    .text-footer2{
        color: black;
        font-size: 10px ;
        text-align:center;
        /* word-wrap: break-word */
    }

    .text-big{
        color: black;
        font-size: 34px;
        font-weight: bold;
        word-wrap: break-word
    }

    .lines{        
        border-top: 1px black dashed;
    }

    header {
                position: fixed;
                top:0cm;
                left: 0cm;
                right: 0cm;
                height: 0cm;

                margin-top: 1cm;
                margin-left: 1cm;
                margin-right: 1cm;
                margin-bottom: 1cm;

            }    

            .parent-element {
  font-size: 34px;
}

.child-element {
  font-size: 0.8em;
}            

 

</style>

<?php

    $golongan = str_replace("GOLONGAN", "",strtoupper($data->passanger_type_id));

    /***
     * length max 27 fontsize 54
     * length 
     * untuk masa berlaku untuk penumpang baik reguler expres ngambil dari gatein expirid 
     * untuk masa berlaku untuk kebdaraan baik reguler expres ngambil dari boarding expirid 
    */
    $waterMark =$this->session->userdata("username")  ." (". date("d-m-Y") .")";

    if(strlen($waterMark) <= 11 )
    {
        $fontSize = 30;
    }
    else if(strlen($waterMark)<=15)
    {
        $fontSize = 23;
    }
    else if(strlen($waterMark)<=19)
    {
        $fontSize = 18;
    }    
    else if(strlen($waterMark)<=23)
    {
        $fontSize = 15;
    }
    else if(strlen($waterMark)<=26)
    {
        $fontSize = 13;
    }
    else if(strlen($waterMark)<=31)
    {
        $fontSize = 11;
    }
    else
    {
        $fontSize = 9;
    }                                          
?>

<body>
    <header>
        <div class="transform">
            <?= $data->ship_class; ?>
            </div>
            <div class="transform2 parent-element" style="width:300px; font-size: <?= $fontSize ?>px " >
                <!-- permintaan asdp untuk menampilkan tanggal saat di download -->
                <?= $waterMark; ?>
                    <!-- <div class="child-element">
                    </div> -->
            </div>
        </div>
    </header>
    

    <div width="100%"   style="padding:10px;">
        
        <table class="blueTable"  border=0 >
        <tbody>
            <tr style="font-style:italic;">
                <td colspan="3" class="text-body" >
                    <?=  $data->terminal_code_name ." - (". $data->terminal_code.")"; ?>
                </td>
            </tr>
            <tr style="font-style:italic;">
                <td colspan="3" class="text-body" ><?= date("d-m-Y H:i:s");  ?> </td>
            </tr>
            <tr>
                
                <td width="60px;" ><img  src="data:image/png;base64,<?= $logo ?>" style="width:100%; height:25px;" ></td>
                
                <!-- <td rowspan="2"><img  src="./assets/img/img/ferizy-logo.png" style="width:30%; height:50px;" ></td> -->
                
                <td class="text-header2" width="100px" >Boarding Pass Untuk Petugas</td>
                <td class="text-big" style="text-align:right; " >A</td>

            </tr>

            <tr>
                <td colspan="3"><div class="lines"></div></td>
            </tr>
            <tr>
                <td colspan="2" class="text-body" >
                        <div style="padding-bottom:5px;"><b>Keberangkatan <?= ucfirst(substr($data->ship_class,0,3)); ?></b> <br /></div>
                        <div style="padding-bottom:5px;"><?= $data->origin; ?> - <?= $data->destination; ?> <br /></div>
                        <div style="padding-bottom:5px;"><?= format_date($data->depart_date) ?></div>
                </td>
                <td rowspan="2" style="vertical-align: text-top; text-align:left;" >
                    <img   src="data:image/png;base64,<?= $booking_qr ?>" style="width:80px; height:80px; margin-top:-6px; margin-left:-15px;" >
                    <div class="text-body" style="margin-top:8px;" >
                        <?= $data->booking_code; ?>
                    </div>
                </td>
            </tr>
            <tr>
                <!-- <td   colspan="2" class="text-big"><div style="float:left;  margin-top:-10px; font-size: 22px; width:150px;">PEJALAN KAKI</div></td> -->
                

            </tr>
            
            <!-- <tr>
                <td></td>
                <td></td>
                <td class="text-body" style="vertical-align: text-top; text-align:right;" > <div style="margin-top:-15px; margin-right:20px;" ><?= $data->booking_code; ?></div></td>
            </tr> -->
            <tr>
                <td class="text-body" ><div style="margin-right:-30; width:110px;" >MASA BERLAKU</div></td>
                <td colspan=2 class="text-body" ><div style="padding-left:50px;" >: <?= date("d - m - Y H:i:s", strtotime($data->gatein_expired))?> </div></td>
            </tr>
            <tr>
                <td class="text-body"><div style="margin-right:-50">KD. BOOKING</div></td>
                <td colspan=2 class="text-body"><div style="padding-left:50px;" >: <?= $data->booking_code; ?></div></td>
            </tr>
            <tr>
                <td class="text-body">NO. TIKET</td>
                <td colspan=2 class="text-body"><div style="padding-left:50px;" >: <?= $data->ticket_number; ?></div></td>
            </tr>
            <tr>
                <td class="text-body">NAMA</td>
                <td colspan=2 class="text-body"><div style="padding-left:50px;" >: <?= $data->name; ?></div></td>
            </tr>
            <tr>
                <td class="text-body"><div style="margin-right:-30" >TIPE PENUMPANG</div></td>
                <td colspan=2 class="text-body"><div style="padding-left:50px;" >:  <?= $golongan; ?></div></td>
            </tr>
            <tr>
                <td class="text-body">TARIF</td>
                <td colspan=2 class="text-body"><div style="padding-left:50px;" >: Rp<?= number_format($data->fare ,0,',','.'); ?></div></td>
            </tr>
            <tr>
                <td colspan="3"></td>
            </tr>            
            <tr>
                <td colspan="3"><div class="lines"></div></td>
            </tr>
            <tr>
                <td colspan="3" class="text-footer3" style="text-align: justify; "><b>Butuh Informasi Lebih Lanjut? Hubungi Call Center ASDP Di :</b></td>
            </tr>
            <tr>
                <td class="text-footer3" > 
                    <div style=" vertical-align: text-top; margin-top:5px; margin-left:-5px; font-weight: bold;"><img   src="data:image/png;base64,<?= $phoneFooter ?>" style="width:25px; height:20px;"  >  (021) - 191</div>
                </td>
                <td class="text-footer3">
                    <div style=" vertical-align: text-top; margin-top:5px; font-weight: bold;">
                        <img   src="data:image/png;base64,<?= $waFooter ?>" style="width:20px; height:20px;"  >  0811 1021 191
                    </div>
                </td>
                <td class="text-footer3">
                    <div style=" vertical-align: text-top; margin-top:5px; font-weight: bold;">
                        <img   src="data:image/png;base64,<?= $mailFooter ?>" style="width:20px; height:20px;" >  cs@asdp.id
                    </div>
                </td>
            </tr>
            <tr>
                <td colspan="3" class="text-footer"><b>www.ferizy.com</b></td>
            </tr>
            <tr>
                <td colspan="3"><div class="lines"></div></td>
            </tr>
    
            <tr>
                <td colspan=3 class="text-footer3" ><b><?= $company_name->info; ?></b><br>
                        <?= $eticket_address->info; ?>
                    <br>
                    NPWP: <?= $eticket_npwp->info; ?>
                </td>
            </tr>
        </tbody>
        </table>
    
    <div style="page-break-before: always; "></div>
    <div width="100%"   style="padding-bottom: 10px;" >
        
        <table class="blueTable" >
        <tbody>
            <tr style="font-style:italic;">
                <td colspan="3" class="text-body" ><?= $data->terminal_code_name ." - (". $data->terminal_code.")"; ?></td>
            </tr>
            <tr style="font-style:italic;">
                <td colspan="3" class="text-body" ><?= date("d-m-Y H:i:s");  ?> </td>
            </tr>
            <tr>
                
                <td  width="60px;"><img  src="data:image/png;base64,<?= $logo ?>" style="width:100%; height:25px;" ></td>
                
                <!-- <td rowspan="2"><img  src="./assets/img/img/ferizy-logo.png" style="width:30%; height:50px;" ></td> -->
                
                <td class="text-header2" width="120px" >Boarding Pass <br>Untuk Penumpang</td>
                <td class="text-big" style="text-align:right; " >B</td>
            </tr>

            <tr>
                <td colspan="3"><div class="lines"></div></td>
            </tr>
            <tr>
                <td colspan="3" class="text-body" >
                    <div style="padding-bottom:5px;"><b>Keberangkatan <?= ucfirst(substr($data->ship_class,0,3)); ?></b> <br /></div>
                        <div style="padding-bottom:5px;"><?= $data->origin; ?> - <?= $data->destination; ?> <br /></div>
                        <div style="padding-bottom:3px;"><?= format_date($data->depart_date) ?></div>
                </td>
            </tr>
            <tr>
                <td class="text-body" ><div style="margin-right:-30; " >MASA BERLAKU</div></td>
                <td colspan=2 class="text-body" ><div style="padding-left:50px;" >: <?= date("d - m - Y H:i:s", strtotime($data->gatein_expired))?> </div></td>
            </tr>
            <tr>
                <td class="text-body"><div style="margin-right:-50">KD. BOOKING</div></td>
                <td colspan=2 class="text-body"><div style="padding-left:50px;" >: <?= $data->booking_code; ?></div></td>
            </tr>            
            <tr>
                <td class="text-body">NO. TIKET</td>
                <td colspan=2 class="text-body"><div style="padding-left:50px;" >: <?= $data->ticket_number; ?></div></td>
            </tr>
            <tr>
                <td class="text-body">NAMA</td>
                <td colspan=2 class="text-body"><div style="padding-left:50px;" >: <?= $data->name; ?></div></td>
            </tr>
            <tr>
                <td class="text-body"><div style="margin-right:-30" >TIPE PENUMPANG</div></td>
                <td colspan=2 class="text-body"><div style="padding-left:50px;" >:  <?= $golongan; ?></div></td>
            </tr>
            <tr>
                <td class="text-body">TARIF</td>
                <td colspan=2 class="text-body"><div style="padding-left:50px;" >: Rp<?= number_format($data->fare ,0,',','.'); ?></div></td>
            </tr>
            <tr>
                <td colspan="3"><div class="lines"></div></td>
            </tr>
            <tr>
                <td colspan="3" class="text-footer2" style="text-align:left !important; ">
                    <!-- <ul style="list-style-type:none; margin-left:-40px; margin-top:-1px;"> -->
                        <!-- <li> -->
                            Keterangan: 
                            <div style=" margin-left:-20px; margin-top:-8px;">
                            <?= $eticket_description->info; ?>

                            </div>
                        <!-- </li> -->
                    </ul>
                </td>
            </tr>
            <tr>
                <td colspan="3"><div class="lines"></div></td>
            </tr>
            <tr>
                <td colspan="3" class="text-footer3" style="text-align: justify; "><b>Butuh Informasi Lebih Lanjut? Hubungi Call Center ASDP Di :</b></td>
            </tr>
            <tr>
                <td class="text-footer3" > 
                    <div style=" vertical-align: text-top; margin-top:5px; margin-left:-7px; font-weight: bold;"><img   src="data:image/png;base64,<?= $phoneFooter ?>" style="width:25px; height:20px;"  >  (021) - 191</div>
                </td>
                <td class="text-footer3">
                    <div style=" vertical-align: text-top; margin-top:5px; font-weight: bold;">
                        <img   src="data:image/png;base64,<?= $waFooter ?>" style="width:20px; height:20px;"  >  0811 1021 191
                    </div>
                </td>
                <td class="text-footer3">
                    <div style=" vertical-align: text-top; margin-top:5px; margin-left:-15px; font-weight: bold;">
                        <img   src="data:image/png;base64,<?= $mailFooter ?>" style="width:20px; height:20px;" >  cs@asdp.id
                    </div>
                </td>
            </tr>
            <tr>
                <td colspan="3" class="text-footer"><b>www.ferizy.com</b></td>
            </tr>
            <tr>
                <td colspan="3"><div class="lines"></div></td>
            </tr>
    
            <tr>
                <td colspan=3 class="text-footer3" ><b><?= $company_name->info; ?></b><br>
                        <?= $eticket_address->info; ?>
                    <br>
                    NPWP: <?= $eticket_npwp->info; ?>
                </td>
            </tr>
        </tbody>
        </table>
    </div>
</body>

<?php  

$content = ob_get_clean();

$dompdf = new \Dompdf\Dompdf();
$dompdf->set_paper( array( 0,0,204,425) );
$dompdf->load_html( $content);
$dompdf->render( );

$page_count = $dompdf->get_canvas( )->get_page_number( );
unset( $dompdf );


$dompdf = new \Dompdf\Dompdf();
$dompdf->loadHtml($content);

// (Optional) Setup the paper size and orientation
// $dompdf->setPaper('A4', 'landscape');
$customPaper = array(0,0,204,200  * $page_count + 20 );
$dompdf->setPaper($customPaper);

// Render the HTML as PDF
$dompdf->render();


// Output the generated PDF to Browser
// $dompdf->stream("dompdf_out.pdf", array("Attachment" => 0));

$dompdf->add_info('Title', 'Boarding pass '.$data->booking_code);
$dompdf->stream('Boarding_pass_'.$data->booking_code.".pdf", array("Attachment" => 0));
exit(0);


?>