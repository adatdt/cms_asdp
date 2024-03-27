<style type="text/css">

@page {
                margin-top: 2cm;
                margin-left: 10px;
                margin-right: 10px;
                margin-bottom: 0cm;
                
            }
    body{
        font-family: sans-serif;
    }
    table.blueTable 
    {
        /* border: 1px solid black;         */
        width: 100%;
        /* font-family: Arial, Helvetica, sans-serif; */
        text-align: left;
        border-collapse: collapse;                
        
    }
    table.blueTable td, table.blueTable th {
        padding: 2px 1px;
        /* font-size:9px; */
    }


    .transform{
        font-size: 34px;
        color: rgba(128, 128, 128, 0.30); 
        position: absolute;        
        font-weight: bold;
        z-index: -1;
        top: 180px;
        left: 15%;
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
        padding-top: 70%;
        /* border: 1px solid black; */
        width:100%;
        height:100%;
        margin-left:-50px;

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
        font-size: 14px;
        font-weight: bold;
        /* word-wrap: break-word */
    }
    .text-header3{
        color: black;
        font-size: 12px;
        font-weight: bold;
        /* word-wrap: break-word */
    }
    .text-body{
        color: black;
        font-size: 14px;
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
        font-size: 10px;
        text-align:center;
        /* word-wrap: break-word */
    }
    .text-footer3{
        color: black;
        font-size: 8px;
        text-align:center;
        /* word-wrap: break-word */
    }    
    .text-small{
        color: black;
        font-size: 8px;
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

    .lines-doted{        
        border-top: 1px black dotted;
    }

    header {
                position: fixed;
                top:-80px;
                left: 0cm;
                right: 0cm;
                height: 0cm;

                margin-top: 25px;
                margin-left: 1px;
                margin-right: 1px;
                margin-bottom: 1cm;

            }    
    .text-placeholder {
        font-size:5px;
        color:grey;
        font-style:italic;
        font-weight:bold;
        margin-top: -1px;
    }

    .tdrincian{
        padding:0px !important;
        margin:0px !important;
        font-size:8px;
    }

 

</style>
<?php 

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
        <div width="100%"   >
            
            <table class="blueTable"  >
                <tbody>
                    <tr>    
                    <td  width="60px;"><img  src="data:image/png;base64,<?= $logo ?>" style="width:100%; height:25px;" ></td>
                        <td class="text-header2" width="80%" style="padding-left:10px;" >Bukti Pembelian (Receipt)</td>
                        
                    </tr>
                    <tr>    
                        <td  ></td>
                        <td  ></td>
                    </tr>                    
                </tbody>
            </table>        
        </div>      
        
        <div class="lines"></div>     
                
    </header>
    <div class="transform2" style="font-size: <?= $fontSize; ?>px; " >
        <!-- permintaan asdp untuk menampilkan tanggal saat di download -->
        <?= $waterMark; ?>
        
    </div>
    <div width="50%"  style="padding-bottom: 10px;" >

        
        <table width="100%" style="margin-top: -20px !important" class="blueTable" class="text-footer" border=0>
            <tbody>
                <tr>
                    <td style="text-align:left" class="text-footer">KODE BOOKING</td> 
                    <td style="text-align:right" class="text-footer">LINTASAN</td>
                </tr> 
                <tr>
                    <td style="text-align:left" class="text-footer"><b><?= $vehicle[0]->booking_code; ?></b></td> 
                    <td style="text-align:right" class="text-footer"><b><?= $vehicle[0]->origin." - ".$vehicle[0]->destination; ?> </b></td>
                </tr> 
                <tr>
                    <td style="text-align:left"class="text-footer">TANGGAL PEMESANAN</td>
                     <td style="text-align:right" class="text-footer">JADWAL PERJALANAN</td>
                </tr> 
                <tr>
                    <td style="text-align:left" class="text-footer"><b> <?= date("d-m-Y H:i", strtotime($vehicle[0]->created_on)); ?></b></td>
                    <td style="text-align:right" class="text-footer"><div style="margin-left:-50px"><b><?php
                    $jadwalperjalanan = format_date($vehicle[0]->depart_date)." ".format_time($vehicle[0]->depart_time_start); 
                    if($vehicle[0]->ship_class_id == 1 )
                    {
                        $jadwalperjalanan = format_date($vehicle[0]->depart_date)." ".format_time($vehicle[0]->depart_time_start)." - ".format_time($vehicle[0]->depart_time_end); 
                    }
                     echo $jadwalperjalanan ; ?>
                     </b></div></td>
                </tr> 
                <tr>
                    <td style="text-align:left" class="text-footer">NAMA PETUGAS</td> 
                    <td style="text-align:right" class="text-footer">KELAS LAYANAN</td>
                </tr> 
                <tr>
                    <td style="text-align:left" class="text-footer"><b><?= $namaPetugas->username; ?></b></td> 
                    <td style="text-align:right" class="text-footer"><b><?= $vehicle[0]->ship_class ?> </b></td>
                </tr>           
                <tr>
                    <td ></td> 
                    <td ></td>
                </tr>             
            </tbody>
        </table>        
        <div style="padding-bottom:5px;" class="text-header3"><b>Rincian Pesanan</b></div>        
        <div class="lines"></div>
    </div>

    <div width="50%"  style="padding-bottom: 10px; margin-top: -5px;" >
        
        <table width="100%" class="blueTable" class="text-footer" border=0>
            <tbody>
                <tr style="text-align: center; text-decoration: underline;" class="text-small" >
                    <th style="text-align: center; padding-left:15px;" >Uraian</th> 
                    <th width="35%" style="text-align: center; padding-left:15px;" >Harga</th> 
                    <th width="20%">Total Harga</th> 
                </tr>  
                <?php
                    $no=1;
                    $subtotal = $detailRincianVehicle[0]->fare - $detailRincianVehicle[count($detailRincianVehicle)-1]->fare; 
                    foreach($detailRincianVehicle as $keyRincian => $valueRincian) 
                { ?>
                <tr style="text-align: center" class="text-small">                
                    <td align="left"><?= $no++ ?>. <?= @$masterVehicle[$valueRincian->vehicle_class] ?> <?= $keyRincian ==0?"(BARU)":"(x1)" ?> </td> 
                    <td align="center">@Rp<?= number_format($valueRincian->fare,0,',','.') ?></td> 
                    <td align="right">Rp<?=number_format($valueRincian->fare  * 1 ,0,',','.') ?></td> 
                </tr>       
                <?php
                } ?>
                <tr style="text-align: center" >
                    <td colspan=3><div class="lines-doted"></div></td> 
                </tr>   
                <tr >
                    <td colspan=2 class="tdrincian" align="right"><b>Subtotal</b></td>
                    <td class="tdrincian" align="right"><b>Rp<?=number_format($subtotal,0,',','.')?></b></td> 
                </tr>
                <tr >
                    <td colspan=2 class="tdrincian" align="right"><b>Reduksi</b></td>
                    <td align="right" class="tdrincian"><b>-</b></td> 
                </tr> 
                <tr >
                    <td colspan=2 align="right" class="tdrincian">
                        <b>Total Tagihan</b><br>
                        <div class="text-placeholder">*Sub Total - Reduksi</div>
                    </td>
                    <td align="right" class="tdrincian"><b>Rp<?=number_format($subtotal,0,',','.')?></b></td> 
                <tr>
                <tr >
                    <td colspan=2 align="right" class="tdrincian">
                        <b>PPN Fasilitas Yang Dibebaskan</b><br>
                        <div class="text-placeholder"><?= $ppnText ?></div>
                        
                    </td>
                    <td class="tdrincian" align="right"><b>Rp<?=number_format(($subtotal/100)*$ppnReceipt,0,',','.')?></b></td> 
                <tr>     
                <tr >
                    <td colspan=2 align="right" class="tdrincian">
                        <b>Biaya Administrasi</b><br>
                        <div class="text-placeholder"><?= $detailRincianVehicle[0]->getNamePayment; ?> </div>                   
                    </td>
                    <td align="right" class="tdrincian"><b>Rp<?=number_format($detailRincianVehicle[0]->amount_payment - $detailRincianVehicle[0]->amount_invoice,0,',','.')?></b></td> 
                <tr>                
                <tr >                 
                    <td colspan=2 align="right" class="tdrincian">
                        <img  src="data:image/png;base64,<?=$imgPaid;?>" style="width:45px; height:35px; float: left; background-color:white; padding:10px 0px 10px 40px; margin-top:-20px; margin-left:-30px;">
                        
                        <div style="background-color:#f2f2f2;  margin-right:-10px; padding:5px 10px; 5px 0px; ">
                            <b>Total Pembayaran</b><br>
                            <div class="text-placeholder" >* Total Tagihan + Biaya Administrasi</div>
                        </div>
                    </td>
                    <td class="tdrincian" align="right" style="background-color:#f2f2f2; border-top:1px solid white; border-bottom:1px solid white;">
                        <b>Rp<?=number_format($detailRincianVehicle[0]->amount_payment,0,',','.')?></b></td> 
                <tr>
                <?php if(strtoupper($detailRincianVehicle[0]->payment_type) == "CASH" ) { 
                    
                    $cashData = $detailRincianVehicle[0]->amount_invoice;
                    if(!empty($detailRincianVehicle[0]->total_cash))
                    {
                        $cashData = $detailRincianVehicle[0]->total_cash;
                    }
                ?>
                <tr >                 
                    <td class="tdrincian" colspan=2 align="right" > <b>Tunai</b><br></td>
                    <td class="tdrincian" align="right" ><b>Rp<?=number_format($cashData,0,',','.')?></b></td> 
                <tr>
                <tr >                 
                    <td class="tdrincian"  colspan=2 align="right" ><b>Kembali</b><br></td>
                    <td align="right" class="tdrincian" ><b>Rp<?=number_format($vehicle[0]->change_cash,0,',','.')?></b></td> 
                <tr>                
                <?php } ?>
                                                                                                                                                                       
            </tbody>
        </table>        
    </div>

    <div width="50%"   style="padding-bottom: 10px;" >        
        <table class="blueTable"  >
        <tbody>
            <tr>
                <td colspan="3"><div class="lines"></div></td>
            </tr>
            <tr>
                <td colspan="3"></td>
            </tr>            
            <tr>
                <td colspan="3" class="text-footer3" style="text-align: justify; "><b>Butuh Informasi Lebih Lanjut? Hubungi Call Center ASDP Di :</b></td>
            </tr>
            <tr>
                <td class="text-footer3" > 
                    <div style=" vertical-align: text-top; margin-top:5px; font-weight: bold;"><img   src="data:image/png;base64,<?= $phoneFooter ?>" style="width:25px; height:20px;"  >  (021) - 191</div>
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
                <td colspan="3" class="text-footer3"><b>www.ferizy.com</b></td>
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
    $dompdf->set_option("isPhpEnabled", true);
    // $dompdf->set_option('dpi', 72);
    $dompdf->set_paper( array( 0,0,204,430) );
    // $dompdf->set_paper( array( 0,0,295,480) );
    $dompdf->load_html( $content);
    $dompdf->render( );
    
    $page_count = $dompdf->get_canvas( )->get_page_number( );
    
    // echo "$page_count"; exit;
    unset( $dompdf );
    
    $dompdf = new \Dompdf\Dompdf();
    $this->load->library('dompdfadaptor');  
    $dompdf = $this->dompdfadaptor;
    $dompdf->loadHtml($content);
    
    // (Optional) Setup the paper size and orientation
    // $dompdf->setPaper('A4', 'landscape');
    // $customPaper = array(0,0,295,480* $page_count+20  );
    $customPaper = array(0,0,204,430* $page_count+20  );
    // $dompdf->set_option('dpi', 72);
    
    // $customPaper = array(0,0,295,450 );
    $dompdf->setPaper($customPaper);
    // $dompdf->set_option('dpi', 72);
    
    // Render the HTML as PDF
    $dompdf->render();
    
    // Output the generated PDF to Browser
    $dompdf->add_info('Title', 'Receipt Pembelian Goshow '.$vehicle[0]->booking_code);
    $dompdf->stream("Receipt_Pembelian_Goshow_".$vehicle[0]->booking_code.".pdf", array("Attachment" => false));
    
    exit(0);

?>