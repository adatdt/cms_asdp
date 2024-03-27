<!-- Setting CSS bagian header/ kop -->
  <style type="text/css">
  .tabel_header {
    border-collapse: collapse;
  }
  .tabel_header th, .tabel_header td {
      padding: 5px 5px;
      border: 1px solid #000;
  }

  .tabel2 {
    border-collapse: collapse;
    padding-top: 60px;
  }
  .tabel2 th, .tabel2 td {
      padding: 2px 5px;
      border: 1px solid #000;
  }
  </style>
<!-- Setting Margin header/ kop -->
<page backtop="14mm" backbottom="14mm" backleft="1mm" backright="10mm">
  <page_header>
    <!-- Setting Header -->
    <table class="tabel_header" align="center" >
      <tr>
        <td rowspan="4" style="border-right: none;"><img src="assets/img/asdp-logo2.jpg" style="width:100px; height: 50px">
        </td>
        <td rowspan="4" style="width:58%; text-align:center ">FORMULIR BERITA ACARA PENDAPATAN KAPAL </td>
        <td style="border-right: none;">No Document</td>
        <td style="border-left: none;">: BPL-102.00.12</td>
      </tr>
      <tr>
        <td style="border-right: none;">Revisi</td>
        <td style="border-left: none;" >: 01</td>
      </tr>
      <tr>
        <td style="border-right: none;">Berlaku Efektif</td>
        <td style="border-left: none;">: </td>
      </tr>
      <tr>
        <td style="border-right: none;">Halaman</td>
        <td style="border-left: none;">: Halaman [[page_cu]]/[[page_nb]]</td>
      </tr>
    </table>
    <br><br>
  </page_header>

  
  <!-- Setting Footer -->
<!--   <page_footer>
    <table class="page_footer" align="center">
      <tr>
        <td style="width: 33%; text-align: left">

        </td>
        <td style="width: 34%; text-align: center">
          
        </td>
        <td style="width: 33%; text-align: right">
          Halaman [[page_cu]]/[[page_nb]]
        </td>
      </tr>
    </table>
  </page_footer> -->
  <!-- Setting CSS Tabel data yang akan ditampilkan -->


  <table class="tabel2" style="margin-left: 20px;">
      <tr>
        <td colspan="6" >Pada Hari ini .............. tanggal <b> <?php echo date("Y-m-d",strtotime ($trx_date)); ?></b> pukul <b><?php echo date("H:i",strtotime ($trx_date)); ?></b> Telah dilakukan penjualan 
          <br>
          <br>
          Tiket terpadu penumpang <b ><?php echo $total_ticket; ?></b> dan kendaraan untuk kapal : nama(KMP <b><?php echo $nama_kapal; ?></b>) GRT(<b>900</b>)<br><br> PERUSAHAAN(..................) DERMAGA (.................) dengan rincian sebagai berikut :
        </td>

      </tr>

      <tr>
        <td style="width: 5%">NO</td>
        <td style="width: 20%">URAIAN</td>
        <td style="width: 15%">TARIF</td>
        <td style="width: 12%">PRODUKSI</td>
        <td style="width: 20%">PENDAPATAN</td>
        <td style="width: 20%">KETERANGAN</td>
      </tr>


      <tr>
        <td>1</td>
        <td>PNP KAPAL RO-RO</td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
      </tr>
  

    <?php foreach($penumpang as $penumpang) { 
      
      if($penumpang->income=="")
      {
        $total=0;
      }
      else
      {
        $total=$penumpang->income/$penumpang->production;
      }
      ?>

      <tr>
        <td></td>
        <td><?php echo $penumpang->name; ?></td>
        <td align="right"><?php echo  number_format($total,0,",",".");?></td>
        <td><?php echo $penumpang->production; ?></td>
        <td align="right"><?php echo $penumpang->income==''?0:number_format($penumpang->income,0,",","."); ?></td>
        <td></td>
      </tr>
    <?php 
      $sum_tiket_penumpang[]=$penumpang->production;
      $sum_income_penumpang[]=$penumpang->income;
    } ?>

      <tr>
        <td></td>
        <td colspan="2"><b>Sub Jumlah</b></td>
        <td><?php echo array_sum($sum_tiket_penumpang); ?></td>
        <td style="text-align: right;"><?php echo number_format(array_sum($sum_income_penumpang),0,",","."); ?></td>
        <td></td>
      </tr>

      <tr>
        <td>2</td>
        <td>KENDARAAN</td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
      </tr>

    <?php foreach($kendaraan as $kendaraan) { 

      if($kendaraan->income=="")
      {
        $total=0;
      }
      else
      {
        $total=$kendaraan->income/$kendaraan->production;
      }

      ?>
      <tr>
        <td></td>
        <td ><?php echo $kendaraan->name; ?></td>
        <td align="right"><?php echo  number_format($total,0,",","."); ?></td>
        <td ><?php echo $kendaraan->production; ?></td>
        <td align="right"><?php echo $kendaraan->income==''?0:number_format($kendaraan->income,0,",","."); ?> </td>
        <td></td>
      </tr>
    <?php  
      $sum_tiket_kendaraan[]=$kendaraan->production;
      $sum_income_kendaraan[]=$kendaraan->income;
     } ?>

      <tr>
        <td></td>
        <td colspan="2"><b>Sub Jumlah</b></td>
        <td><?php echo array_sum($sum_tiket_kendaraan); ?></td>
        <td style="text-align: right;"><?php echo number_format(array_sum($sum_income_kendaraan),0,",","."); ?></td>
        <td></td>
      </tr>

    <tr>
        <td colspan="3" style="text-align: center;"><b>Jumlah (1+2) </b></td>
        <td><?php echo array_sum($sum_tiket_kendaraan)+array_sum($sum_tiket_penumpang); ?></td>
        <td style="text-align: right;"><?php echo number_format(array_sum($sum_income_kendaraan)+array_sum($sum_income_penumpang),0,",","."); ?></td>
        <td></td>
    </tr>

    <tr>
        <td>3</td>
        <td colspan="2" style="text-align: center;"><b>Cetak Tiket </b></td>
        <td><?php echo array_sum($sum_tiket_kendaraan)+array_sum($sum_tiket_penumpang); ?></td>
        <td style="text-align: right;"></td>
        <td></td>
    </tr>

    <tr>
        <td rowspan="2" style="text-align: center;">4</td>
        <td style="text-align: center;">
            Realisasi Waktu Tiba
            <p></p>
            WIB
        </td>
        <td colspan="2" style="text-align: center;">
          Realisasi Waktu Keberangkatan
            <p></p>
            WIB
        </td>
        <td style="text-align: center;" colspan="2">Waktu Sandar 
          <p></p>
          WIB
        </td>
    </tr>

    <tr>
        <td style="text-align: center;">
            Renc. Jadwal Tiba
            <p></p>
            WIB
        </td>
        <td colspan="2" style="text-align: center;">
          Renc Jadwal Berangkat
            <p></p>
            WIB
        </td>
        <td style="text-align: center;" colspan="2">Kelebihan Jadwal Berangkat 
          <p></p>
          WIB
        </td>
    </tr>

    <tr>
      <td rowspan="9" style="text-align: center;">5</td>
      <td style="border-bottom: none"><b>Jasa Sandar Dari Engker</b></td>
      <td style="border-bottom: none"></td>
      <td colspan="2" rowspan="9" style="border-right: none;"></td>
      <td rowspan="2" style="border-bottom: none; "></td>
    </tr>

    <tr>
      <td style="border-top: none"><b>Sandar di dermaga</b></td>
      <td style="border-bottom: none; border-top: none; text-align: center;"><b>Call</b></td>

    </tr>

    <tr>
      <td style="border-bottom: none; border-top: none">Dermaga I</td>
      <td style="border-bottom: none; border-top: none"></td>
      <td style="border-bottom: none; border-top: none">Rp</td>
    </tr>

    <tr>
      <td style="border-bottom: none; border-top: none">Dermaga II</td>
      <td style="border-bottom: none; border-top: none"></td>
      <td style="border-bottom: none; border-top: none">Rp</td>
    </tr>

    <tr>
      <td style="border-bottom: none; border-top: none">Dermaga III</td>
      <td style="border-bottom: none; border-top: none"></td>
      <td style="border-bottom: none; border-top: none">Rp</td>
    </tr>

    <tr>
      <td style="border-bottom: none; border-top: none">Dermaga IV</td>
      <td style="border-bottom: none; border-top: none"></td>
      <td style="border-bottom: none; border-top: none">Rp</td>
    </tr>

    <tr>
      <td style="border-bottom: none; border-top: none">Dermaga V</td>
      <td style="border-bottom: none; border-top: none"></td>
      <td style="border-bottom: none; border-top: none">Rp</td>
    </tr>

    <tr>
      <td style="border-bottom: none; border-top: none">Dermaga VI</td>
      <td style="border-bottom: none; border-top: none"></td>
      <td style="border-bottom: none; border-top: none">Rp</td>
    </tr>

    <tr>
      <td style=" border-top: none">Total Sandar Engker</td>
      <td style="border-top: none"></td>
      <td style="border-top: none">Rp</td>
    </tr>

    <!-- jasa masa tambat -->

    <tr>
      <td rowspan="9" style="text-align: center;">6</td>
      <td style="border-bottom: none"><b>Jasa Sandar Masa Tambat :</b></td>
      <td style="border-bottom: none"></td>
      <td colspan="2" rowspan="9" style="border-right: none;"></td>
      <td rowspan="2" style="border-bottom: none; "></td>
    </tr>

    <tr>
      <td style="border-top: none"></td>
      <td style="border-bottom: none; border-top: none; text-align: center;"><b>Call</b></td>

    </tr>

    <tr>
      <td style="border-bottom: none; border-top: none">Dermaga I</td>
      <td style="border-bottom: none; border-top: none"></td>
      <td style="border-bottom: none; border-top: none">Rp</td>
    </tr>

    <tr>
      <td style="border-bottom: none; border-top: none">Dermaga II</td>
      <td style="border-bottom: none; border-top: none"></td>
      <td style="border-bottom: none; border-top: none">Rp</td>
    </tr>

    <tr>
      <td style="border-bottom: none; border-top: none">Dermaga III</td>
      <td style="border-bottom: none; border-top: none"></td>
      <td style="border-bottom: none; border-top: none">Rp</td>
    </tr>

    <tr>
      <td style="border-bottom: none; border-top: none">Dermaga IV</td>
      <td style="border-bottom: none; border-top: none"></td>
      <td style="border-bottom: none; border-top: none">Rp</td>
    </tr>

    <tr>
      <td style="border-bottom: none; border-top: none">Dermaga V</td>
      <td style="border-bottom: none; border-top: none"></td>
      <td style="border-bottom: none; border-top: none">Rp</td>
    </tr>

    <tr>
      <td style="border-bottom: none; border-top: none">Dermaga VI</td>
      <td style="border-bottom: none; border-top: none"></td>
      <td style="border-bottom: none; border-top: none">Rp</td>
    </tr>

    <tr>
      <td style=" border-top: none">Total Sandar Masa Tambat</td>
      <td style="border-top: none"></td>
      <td style="border-top: none">Rp</td>
    </tr>
    <!-- jasa tambat end -->

    <tr>
      <td colspan="2" style="text-align: center;"> <b>Total jasa sandar ( 5 + 6 )</b></td>
      <td></td>
      <td colspan="2" style="border-right: none;"></td>
      <td>Rp </td>
    </tr>

    <tr>
      <td>7</td>
      <td  style="text-align: center;"> <b>Pendapatan sebelum <br>jasa sandar ((1+2)-3)</b></td>
      <td></td>
      <td colspan="2" style="border-right: none;"></td>
      <td>Rp </td>
    </tr>

    <tr>
      <td>8</td>
      <td colspan="4" style="border-right: none;"> <b>Jumlah jasa sandar yang harus di bayarkan <br> 
      (5+6)+PPN 10% </b>
      </td>
      <td>Rp </td>
    </tr>
  </table>

  <p></p>
  <table style="width: auto;">
      <tr>
          <td >Petugas Pelayaran </td>
          <td style="width: 500px"></td>
          <td>.......................... <br>Supervisor</td>
      </tr>

      <tr>
          <td>PT.................</td>
          <td style="width: 500px"></td>
          <td></td>
      </tr>
  </table>
<p></p>
<p></p>
<p></p>
<table>
  <tbody>
      <tr>
          <td>.................</td>
          <td style="width: 550px"></td>
          <td>NIK...................</td>
      </tr>

  </tbody>
  </table>
  <div id="tes">
    
  </div>

 

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
    $html2pdf->Output('Tap_in.pdf');
}
catch(HTML2PDF_exception $e) {
    echo $e;
    exit;
}
?>

