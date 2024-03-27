<!-- Setting CSS bagian header/ kop -->
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
</style>
<!-- Setting Margin header/ kop -->
<page backtop="14mm" backbottom="14mm" backleft="14mm" backright="14mm">
  <page_header>
    <!-- Setting Header -->
    <table class="tabel full-width" align="center" > 
        <tr>
            <td rowspan="4" class="no-border-right" style="width: 10%">
                <img src="assets/img/asdp-logo2.jpg" style="width:100px; height: auto"> 
            </td>
            <td rowspan="4" class="center bold" style="width: 60%;font-size: 14pt; line-height: 1.5">
                BERITA ACARA HASIL PENJUALAN TIKET<br/> LOKET PENUMPANG
            </td>    
            <td style="width: 10%" class="no-border-right">No Document</td>
            <td style="width: 20%">: BPL-102.00.03</td>
        </tr>
        <tr>  
            <td class="no-border-right">Revisi</td>
            <td>: 02</td>
        </tr>
        <tr>  
            <td class="no-border-right">Berlaku Efektif</td>
            <td>: 18 April 2018</td>
        </tr> 
        <tr>  
            <td class="no-border-right">Halaman</td>
            <td>: 1 dari 1</td>
        </tr>   
    </table>
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
</style>
    <br><br><br><br><br>
    
    <div class="full-width" style="width: 100%">
        <p style="font-size:12pt; line-height: 1.5">Pada hari ini <span class="bold"><?=hari_ini($hari) ?></span> tanggal <span class="bold"><?=$tanggal?></span> pada Loket Penumpang <span class="bold"><?=$team_name ?></span> Shift <span class="bold"><?=$shift_name ?></span>, kami yang bertanda tangan dibawah ini:</p>
    </div>
    <table class="tabel-no-border full-width" style="font-size:12pt; line-height: 1.5">
        <tr>
            <td colspan="3" class="bold" style="width: 50%">PIHAK PERTAMA :</td>
            <td colspan="3" class="bold" style="width: 50%">PIHAK KEDUA :</td>
        </tr>
        <tr>
            <td style="width: 2%">1.</td>
            <td style="width: 6%">Nama</td>
            <td>: <span class="bold"></span></td>
            <td style="width: 2%">2.</td>
            <td style="width: 6%">Nama</td>
            <td>: <span class="bold"></span></td>
        </tr>
         <tr>
            <td></td>
            <td>Jabatan</td>
            <td>: Petugas Loket</td>
            <td></td>
            <td>Jabatan</td>
            <td>: Administarsi Loket</td>
        </tr>
    </table>
  
    <div class="full-width" style="width: 100%; font-size:12pt; line-height: 1.5">
        <p>Dengan PIHAK PERTAMA telah menyerahkan uang sejumlah <span class="bold">Rp. <?=idr_currency($total_terbilang) ?></span><br>
        (Terbilang: <span class="italic"><?=function_terbilang($angka_terbilang) ?></span> ) <br>    
        sesuai dengan penjualan tiket pada shift ini, dengan rincian sebagai berikut: </p>
    </div>
    <br>
    <table class="tabel full-width" style="width: 100%">
        <tr>
            <th rowspan="3" class="center" style="width: 5%">NO</th>
            <th rowspan="3" class="center" style="width: 25%">JENIS TIKET</th>
            <th rowspan="3" class="center" style="width: 8%">TARIF</th>
            <th colspan="3" class="center" style="width: 20%">PRODUKSI</th>
            <th rowspan="3" class="center" style="width: 10%">SALDO AKHIR</th>
            <th colspan="3" class="center" style="width: 35%">PENDAPATAN</th>
        </tr>
        <tr>
            <th class="center" colspan="2">LOKET</th>
            <th class="center"  rowspan="2">ONLINE</th>
            <th class="center" rowspan="2">LOKET</th>
            <th class="center" rowspan="2">ONLINE</th>
            <th class="center" rowspan="2">TOTAL</th>
            <!-- <th class="center" rowspan="2">LOKET<br/>3x7</th> -->
            <!-- <th class="center" rowspan="2">ONLINE<br/>3x8</th> -->
            <!-- <th class="center" rowspan="2">TOTAL<br/>11+12</th> -->
        </tr>
            <tr>
            <th class="center">Tunai</th>
            <th class="center">Non Tunai</th>
        </tr>
       <!--  <tr>
            <th class="center">(1)</th>
            <th class="center">(2)</th>
            <th class="center">(3)</th>
            <th class="center">(4)</th>
            <th class="center">(5)</th>
            <th class="center">(6)</th>
            <th class="center">(7)</th>
            <th class="center">(8)</th>
            <th class="center">(9)</th>
            <th class="center">(10)</th>
        </tr> -->
        <tr>
            <td class="center">1</td>
            <td>Dewasa</td>
            <td class="right">Rp. <?=idr_currency($tarif_dewasa) ?></td>
            <td class="right"><?=idr_currency($loket_tunai_dewasa) ?></td>
            <td class="right"><?=idr_currency($loket_non_tunai_dewasa) ?></td>
            <td class="right"><?=idr_currency($loket_online_dewasa) ?></td>
            <td class="right"><?=idr_currency($saldo_akhir_loket_dewasa) ?></td>
            <td class="right">Rp. <?=idr_currency($tunai_dewasa) ?></td>
            <td class="right">Rp. <?=idr_currency($online_dewasa) ?></td>
            <td class="right">Rp. <?=idr_currency($total_dewasa) ?></td>
        </tr>
        <tr>
            <td class="center">2</td>
            <td>Anak</td>
            <td class="right">Rp. <?=idr_currency($tarif_anak) ?></td>
            <td class="right"><?=idr_currency($loket_tunai_anak) ?></td>
            <td class="right"><?=idr_currency($loket_non_tunai_anak) ?></td>
            <td class="right"><?=idr_currency($loket_online_anak) ?></td>
            <td class="right"><?=idr_currency($saldo_akhir_loket_anak) ?></td>
            <td class="right">Rp. <?=idr_currency($tunai_anak) ?></td>
            <td class="right">Rp. <?=idr_currency($online_anak) ?></td>
            <td class="right">Rp. <?=idr_currency($total_anak) ?></td>
        </tr>
        
       <!--  <tr>    
            <td>1</td>
            <td>Dewasa</td>
            <td class="right">15.000</td>
            <td class="right">341</td>
            <td class="right">453</td>
            <td class="right">234</td>
            <td class="right">16.700.000</td>
            <td class="right">12.300.000</td>
            <td class="right">8.100.000</td>
            <td class="right">20.400.000</td>
        </tr>
        <tr>
            <td>2</td>
            <td>Anak</td>
            <td class="right">8.000</td>
            <td class="right">240</td>
            <td class="right">130</td>
            <td class="right">140</td>
            <td class="right">16.700.000</td>
            <td class="right">12.300.000</td>
            <td class="right">8.100.000</td>
            <td class="right">20.400.000</td>
        </tr> -->
    </table>
    <br>
    <table class="tabel-no-border full-width" style="width: 100%; font-size: 12pt;line-height: 1.3">
        <tr>
            <td class="center" style="width: 33%">MENGETAHUI,<br>SUPERVISOR</td>
            <td class="center" style="width: 33%">PENERIMA SETORAN<br>PETUGAS ADMINISTRASI TIKET</td>
            <td class="center" style="width: 33%">PETUGAS LOKET<br><?=$team_name ?></td>          
        </tr>
        <tr>
            <td class="center"><br><br>( <strong><?=$spv_name ?></strong> )<br></td>
            <td class="center"><br><br></td>
            <td class="center"><br><br></td>
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
    $html2pdf = new HTML2PDF('L', 'A4', 'en', false, 'UTF-8', array(10, 10, 10,10,10,10));
    $html2pdf->pdf->SetDisplayMode('fullpage');
    $html2pdf->writeHTML($content);
    $html2pdf->Output('Tap_in.pdf');
}
catch(HTML2PDF_exception $e) {
    echo $e;
    exit;
}
?>
