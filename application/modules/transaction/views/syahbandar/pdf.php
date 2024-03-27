<!-- Setting CSS bagian header/ kop -->
<style type="text/css">


  .tabel {
        border-collapse: collapse;
        font-size: 11px;
    }
    .tabel th, .tabel td {
        padding: 1px 2px;
        border: 1px solid #000;
    }
    .tabel th {
        font-weight: normal;
    }
    .tabel-no-border tr {
        border: 1px solid #000;
    }

    .tabel-no-border th, .tabel-no-border td {
        padding: 5px 5px;
        border: 0px;
    }        
    .full-width {
        width: 100%;
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
    td.border-right {
        border-right: 1px solid #000
    }
</style>
<!-- Setting Margin header/ kop -->
<page backtop="0mm" backbottom="14mm" backleft="0mm" backright="5mm">
  <page_header>

    <br><br>
  </page_header>

  
  <!-- Setting Footer -->
  <page_footer>
    <table class="page_footer" align="center">
      
    </table>
  </page_footer>
  
  <!-- Setting CSS Tabel data yang akan ditampilkan -->
<style type="text/css">
  .tabel {
        border-collapse: collapse;
    }
    .tabel th, .tabel td {
        padding: 5px 5px;
        border: 1px solid #000;
    }
    .tabel th {
        font-weight: normal;
    }
    .tabel-no-border tr {
        border: 1px solid #000;
    }

    .tabel-no-border th, .tabel-no-border td {
        padding: 5px 5px;
        border: 0px;
    }        
    .full-width {
        width: 100%;
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
    td.border-right {
        border-right: 1px solid #000
    }
    .font {
          font-family:  Times, serif;
    }
</style>

  <!-- <page_header> -->
    <!-- Setting Header -->

    <!-- <h3 style="text-align: center" class="font bold">REKAPITULASI DATA PENUMPANG DAN KENDARAAN</h3> -->

    <!-- <br><br> -->
  <!-- </page_header> -->

    <!-- Setting Footer -->
  <page_footer>
    <div style="text-align: right;">Halaman [[page_cu]]/[[page_nb]]</div>
  </page_footer>
  <!-- Setting CSS Tabel data yang akan ditampilkan -->
    <table class="tabel full-width font " style="width: 100%" >
        <tr>
            <td colspan="10" style="text-align: center; width:100%">DAFTAR PENUMPANG PADA KENDARAAN</td>
        </tr>

        <tr>
            <td colspan="10">Tanggal : <?php echo format_date($detail->schedule_date) ?></td>
        </tr>

        <tr>
            <td colspan="10">Nama Kapal : <?php echo $detail->ship_name ?></td>
        </tr>

        <tr>
            <td  style="text-align: center">NO</td>
            <td  style="text-align: center">NAMA</td>
            <td  style="text-align: center">JK</td>
            <td  style="text-align: center">USIA</td>
            <td  style="text-align: center">ALAMAT <br>(DOMISILI)</td>
            <td  style="text-align: center" >NOMER IDENTITAS</td>
            <td  style="text-align: center" >NO <br>KENDARAAN</td>
            <td  style="text-align: center" >GOL</td>
            <td  style="text-align: center" >TOTAL <BR> PNP</td>
            <td  style="text-align: center" >KETERANGAN</td>
        </tr>
<!--         <tr>
            <td style="text-align: center">L</td>
            <td style="text-align: center">P</td>
        </tr> -->

        <?php $no1=1; foreach($detail_passanger_vehicle as $key=>$value) {?>
        <tr>
            <td><?php echo $no1; ?></td>
            <td><?php echo wordwrap( $value->name, 15, '<br />', true)?></td>
            <td><?php echo $value->gender ?></td>
            <!-- <td><?php echo $value->wanita ?></td> -->
            <td><?php echo $value->age ?></td>
            <td><?php echo wordwrap($value->city, 12, '<br />', true)?></td>
            <td><?php echo $value->id_number ?></td>
            <td><?php echo $value->plate_number ?></td>
            <td><?php echo $value->vehicle_class_name ?></td>
            <td><?php echo $value->total_passanger ?></td>
            <td><?php echo wordwrap( $value->manifest_data_from, 8, '<br />', true)?></td>

        </tr>
        <?php $no1++; } ?>

    </table>
    <br>
    <table class="full-width font" style="width: 100%">
        <tr>
            <td style="width: 80%; "></td>
            <td style="width: 20%; text-align: center; ">Petugas Kapal</td>
        </tr>

        <tr>
            <td></td>
            <td style="width: 20%; text-align: center; "><?php echo  wordwrap( strtoupper($detail->approve_name_ship), 20, '<br />', true); ?></td>
        </tr>
        <tr>
            <td></td>
            <td style="width: 20%; text-align: center; ">ttd</td>
        </tr>
        <tr>
            <td></td>
            <td style="width: 20%; text-align: center; "><br><br></td>
        </tr>
        <tr>
            <td></td>
            <td style="width: 20%; text-align: center; ">.................................</td>
        </tr>
    </table>

    <div style="page-break-after:always; clear:both"></div>

    <table class="tabel full-width font" style="width: 100%">
        <tr>
            <td colspan="8" style="text-align: center; width:100%">DAFTAR PENUMPANG PEJALAN KAKI</td>
        </tr>

        <tr>
            <td colspan="8">Tanggal : <?php echo format_date($detail->schedule_date) ?></td>
        </tr>
        <tr>
            <td colspan="8">Nama Kapal : <?php echo $detail->ship_name ?></td>
        </tr>

        <tr>
            <td  style="text-align: center">NO</td>
            <td  style="text-align: center">NAMA</td>
            <td  style="text-align: center">JK</td>
            <td  style="text-align: center">USIA</td>
            <td  style="text-align: center">ALAMAT <br>(DOMISILI)</td>
            <td  style="text-align: center" >NOMER IDENTITAS</td>
            <td  style="text-align: center" >KETERANGAN</td>
        </tr>
<!--         <tr>
            <td style="text-align: center">L</td>
            <td style="text-align: center">P</td>
        </tr> -->
        <?php $no=1; foreach($detail_passanger as $value ) { ?>
        <tr>
            <td><?php echo $no; ?></td>
            <td><?php echo wordwrap($value->passanger_name, 15, '<br />', true)?></td>
            <td><?php echo $value->gender ?></td>
            <!-- <td><?php echo $value->wanita ?></td> -->
            <td><?php echo $value->age ?></td>
            <td ><?php echo  wordwrap($value->city, 12, '<br />', true) ?> </td>
            <td><?php echo  wordwrap($value->id_number, 16, '<br />', true)?></td>
            <td><?php echo  wordwrap($value->manifest_data_from, 8, '<br />', true)?></td>
        </tr>
        <?php $no++; } ?>

    </table>

    <table class="full-width font" style="width: 100%">
        <tr>
            <td style="width: 80%; "></td>
            <td style="width: 20%; text-align: center; ">Petugas Kapal</td>
        </tr>

        <tr>
            <td></td>
            <td style="width: 20%; text-align: center; "><?php echo wordwrap(strtoupper($detail->approve_name_ship), 20, '<br />', true); ?></td>
        </tr>
        <tr>
            <td></td>
            <td style="width: 20%; text-align: center; ">ttd</td>
        </tr>
        <tr>
            <td></td>
            <td style="width: 20%; text-align: center; "><br><br></td>
        </tr>
        <tr>
            <td></td>
            <td style="width: 20%; text-align: center; ">.................................</td>
        </tr>
    </table>


<?php
            // $tot_pass=array();
            // foreach ($hitung_passanger_vehicle as $key => $value) {
            //     $tot_pass[]=$value->tot_pass;
            // } 

            //  $tot=array_sum($tot_pass);
                $tot=sizeof($hitung_passanger_vehicle);
            ?> 

        <!-- <br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br> -->
     <div style="page-break-after:always; clear:both"></div>

        <h3 style="text-align: center" class="font bold">REKAPITULASI DATA PENUMPANG DAN KENDARAAN</h3>
        
    <table class="tabel full-width font" style="width: 100%;">

        <tr>
            <th  style="width: 20%; border-right: none;" class="left" >Tanggal</th>
            <th colspan="4" style="width: 80%; border-left: none;" class="left" >: <?php echo format_date($detail->schedule_date) ?></th>
        </tr>
        <tr>
            <th  span="" class="left" style="border-right:none; width:25%">Nama Kapal</th>
            <th  span="" class="left" style="border-left:none; width:25%">: <?php echo $detail->ship_name ?></th>
            <th  span="" class="left" style="border-right:none; width:25%">Waktu Tiba</th>
            <th  span="" class="left" style="border-left:none; border-right:none;width:10%">: <?php echo empty($detail->jam_tiba)?"":date("H:i:s",strtotime ($detail->jam_tiba)); ?></th>
            <th  span="" class="left" style="border-left:none; width:15%"></th>
        </tr>
        <tr>
            <th  span="" class="left" style="border-right:none; width:25%">Dermaga</th>
            <th  span="" class="left" style="border-left:none;width:25%">: <?php echo $detail->dock_name ?></th>
            <th  span="" class="left" style="border-right:none;width:25%">Waktu Berangkat</th>
            <th  span="" class="left" style="border-left:none; border-right:none; width:10%">: <?php echo empty($detail->jam_berangkat)?"":date("H:i:s",strtotime ($detail->jam_berangkat));?></th>
            <th  span="" class="left" style="border-left:none; width:15%"></th>
        </tr>
        <tr>
            <th class="left" colspan="2" valign="top">
               <table >
                   <tr>
                       <td style="border: none;">Penumpang Dewasa</td>
                   </tr>
                    <tr>
                       <td style="border: none; padding-left: 20px">Laki-laki</td>
                       <td style="border: none;">: <?php echo $dewasa_l->total_penumpang; ?></td>
                   </tr>
                    <tr>
                       <td style="border: none; padding-left: 20px">Perempuan</td>
                       <td style="border: none;">: <?php echo $dewasa_p->total_penumpang; ?></td>
                   </tr>
               </table>

                <table >
                   <tr>
                       <td style="border: none;">Penumpang Balita</td>
                       <td style="border: none;">: <?php echo $anak->total_penumpang+$bayi->total_penumpang; ?></td>
                   </tr>
               </table>
            </th>


            <th class="left" colspan="3" valign="top">
               <table >
                   <tr>
                       <td style="border: none;">Kendaraan</td>
                   </tr>
                    <?php $total=array(); $pnp_knd=array(); foreach ($a as $a) { ?>
                    <tr>
                       <td style="border: none; padding-left: 20px"><?php echo strtoupper($a->tipe_name);  ?></td>
                       <td style="border: none;">: <?php echo $a->total_kendaraan;  $total[]=$a->total_kendaraan; ?>,</td>
                       <th style="border: none;">TOTAL PNP: <?php echo empty($a->total_penumpang)?"0":$a->total_penumpang; $pnp_knd[]=$a->total_penumpang; ?></th>
                   </tr>
                    <?php } ?>
               </table>

            </th>
        </tr>        
        <tr>
            <th class="left" style="border-right: : none;" >Jumlah penumpang</th>
            <th style="border-left: none;" >: <?php echo $dewasa_l->total_penumpang+$dewasa_p->total_penumpang+$anak->total_penumpang+$bayi->total_penumpang ?></th>
            <th class="left" style="border-right: : none;">Jumlah Kendaraan</th>
            <th style="border-left: none; border-right: none;">: <?php echo array_sum($total); ?></th>
            <th style="border-left: none;"></th>

        </tr>


        <tr>
            <th class="left" style="border-right: none;">Jumlah Penumpang<br> dan Penumpang <br>Kendaraan</th>
            <th class="left" style="border-left:  none;">:<?php echo $dewasa_l->total_penumpang+$dewasa_p->total_penumpang+$anak->total_penumpang+$bayi->total_penumpang+$tot ?></th>
            <th class="left" style="border-right: none;">Jumlah Penumpang <br>Kendaraan</th>
            <th style="border-left: none; border-right: none;">: <?php echo array_sum($pnp_knd) ?></th>
            <th style="border-left: none;"> </th>

        </tr>

        <tr>
            <th class="left" colspan="2" style="text-align: center; border-bottom: none;"></th>
            <th class="left" colspan="3" style="text-align: center; border-bottom: none;">Petugas Kapal</th>
        </tr>
        <tr>
            <th class="left" colspan="2" style="text-align: center;border-bottom: none; "></th>
            <th class="left" colspan="3" style="text-align: center;border-bottom: none; "><?php echo  wordwrap( strtoupper($detail->approve_name_ship), 20, '<br />', true);?></th>
        </tr>
        <tr>
            <th class="left" colspan="2" style="text-align: center;border-bottom: none; "></th>
            <th class="left" colspan="3" style="text-align: center;border-bottom: none; ">Ttd</th>
        </tr>
        <tr>
            <th class="left" colspan="2" style="border-bottom: none;"></th>
            <th class="left" colspan="3" style="border-bottom: none;"></th>
        </tr>
        <tr>
            <th class="left" colspan="2"> </th>


            <th class="left" colspan="3" style="text-align: center; ">..............................................</th>
        </tr>
    </table>





</page>
<!-- Memanggil fungsi bawaan HTML2PDF -->
<?php
$content = ob_get_clean();
 // include 'html2pdf_v4.03/html2pdf.class.php';
 try
{
  // setting paper
    $html2pdf = new HTML2PDF('P', 'A4', 'en', false, 'UTF-8', array(10, 10, 10,10,10,10));
    $html2pdf->pdf->SetDisplayMode('fullpage');
    $html2pdf->writeHTML($content);
    $html2pdf->Output('Tap_in.pdf');
}
catch(HTML2PDF_exception $e) {
    echo $e;
    exit;
}
?>
