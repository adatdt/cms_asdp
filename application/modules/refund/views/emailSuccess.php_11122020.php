<?php
/*
  Document   : email
  Created on : 13-02-2019
  Author     : adat adatdt@gmail.com
  Description: Purpose of the PHP File follows.
 */

  if (!defined('BASEPATH'))
    exit('No direct script access allowed');
  ?>
  <html>
  <head>
    <title>ASDP - Tagihan Pemesanan Tiket</title>
    <style>

    body {
      width: 100%;
      margin: 0 auto;
      font-family: arial;
      /*border: 1px solid #ccc;*/
      padding: 10px;
    }

    #header {
      text-align: center;
      /*min-height: 200px;*/
      /*background-color: blue;*/
      /*background-image: url('<?php echo base_url('assets/image/header.jpg') ?>');*/
      /*background-size: 100% 100%;*/
    }

    #opening {

    }

    #payment {
    }

    #booking {
    }

    #booking_detail {
    }

    .content {
      border-top: 3px solid #ccc;
    }

    #footer {
      min-height: 75px;
      border-top: 1px solid #ccc;
      text-align: center;
      font-size: 10pt;
      /*background-image:  url('<?php echo base_url('assets/image/footer.jpg') ?>');*/
      /*background-size: 100% 100%;*/
    }

    .text-right {
      text-align: right;
    }

    .text-left {
      text-align: left;
    }

    .text-center {
      text-align: center;
    }

    .line-bottom {
      border-bottom: 1px solid #ccc;
    }

    .line-top{
      border-top: 1px solid #ccc;
    }
  </style>
</head>
<body>
  <div id="header">
    <!-- <img src="<?php echo base_url('assets/image/footer.png'); ?>"> -->
  </div>

  <div id="opening">
    <p>Dear <?php echo strtoupper($detailRefund->customer_name); ?></p>
    <p> Anda telah <?php echo $detailRefund->myStatus; ?>, dengan kode Booking <?php echo $detailRefund->booking_code; ?> dan nomer Refund <?php echo $detailRefund->refund_code; ?> dengan rincian seperti hal di bawah ini </p>
  </div>

  <div id="booking" class="content">
    <table style="width: 75%">
      
      <tr>
          <td>Nama Pemesan</td>
          <td>: <?php echo $detailRefund->customer_name; ?></td>
      </tr>
      <tr>
          <td>Telpon</td>
          <td>: <?php echo $detailRefund->phone; ?></td>
      </tr>      
      <tr>
          <td>Email</td>
          <td>: <?php echo $detailRefund->email; ?></td>
      </tr>  
      <tr>
          <td>Nomer Booking</td>
          <td>: <?php echo $detailRefund->booking_code; ?></td>
      </tr>
      <tr>
          <td>Kode Refund</td>
          <td>: <?php echo $detailRefund->refund_code; ?></td>
      </tr>      
      <tr>
          <td>Amount</td>
          <td>: Rp. <?php echo $detailRefund->amount; ?></td>
      </tr>            

      <tr>
          <td>Nominal Pengembalian dana</td>
          <td>: Rp. <?php echo $detailRefund->total_amount; ?></td>
      </tr>

      <tr>
          <td>Biaya Refund</td>
          <td>: Rp. <?php echo $detailRefund->charge_amount; ?></td>
      </tr>

      <tr>
          <td>No Rekening</td>
          <td>: <?php echo $detailRefund->account_number; ?></td>
      </tr>      

      <tr>
          <td>Bank Tujuan</td>
          <td>: <?php echo $detailRefund->bank; ?></td>
      </tr>

      <tr>
          <td>Nama Pemilik Rekening </td>
          <td>: <?php echo $detailRefund->account_name; ?></td>
      </tr>                              

      <tr>
          <td>Status</td>
          <td>: <?php echo $detailRefund->statusRefund; ?></td>
      </tr>

      <tr>
          <td>Keterangan</td>
          <td>: <?php echo $detailRefund->transfer_description; ?></td>
      </tr>

      <?php echo $detailRefund->linkFailed ?>                        


    </table>

  </div>



<div class="content">
  <h2>SYARAT DAN KETENTUAN</h2>
  <ol>
    <li>Dengan ini Anda menyatakan persetujuan terhadap Persyaratan dan Ketentuan Angkutan Penumpang Kapal Penyebrangan termasuk tidak terbatas ketentuan reservasi dan akan mematuhi persyaratan dan ketentuan reservasi yang termasuk pembayaran, mematuhi semua aturan dan pembatasan mengenai ketersediaan tarif serta bertanggung jawab untuk semua biaya yang timbul dari penggunaan fasilitas Reservasi Online Tiket Kapal Penyebrangan.</li>
    <li>PT ASDP Indonesia Ferry (Persero) berhak atas kebijaksanaan untuk mengubah, menyesuaikan, menambah atau menghapus salah satu syarat dan kondisi yang tercantum di sini, dan/atau mengubah, menangguhkan atau menghentikan setiap aspek dari Reservasi Online Tiket Kapal Penyebrangan. PT ASDP Indonesia Ferry (Persero) tidak diwajibkan untuk menyediakan pemberitahuan sebelum memasukkan salah satu perubahan di atas dan/atau modifikasi ke dalam Reservasi Online Tiket Kapal Penyebrangan. </li>
    <li>PT ASDP Indonesia Ferry (Persero) akan menggunakan informasi pribadi yang Anda berikan melalui fasilitas ini hanya untuk tujuan reservasi dan pencatatan di dalam database.</li>
    <li>Anda tidak dibenarkan menggunakan fasilitas ini untuk tujuan yang melanggar hukum atau dilarang, termasuk tetapi tidak terbatas untuk membuat reservasi yang tidak sah, spekulatif, palsu atau penipuan atau menjualnya kembali secara tidak sah. PT ASDP Indonesia Ferry (Persero) dapat membatalkan atau menghentikan penggunaan atas fasilitas ini setiap saat tanpa pemberitahuan jika dicurigai.</li>
    <li>PT ASDP Indonesia Ferry (Persero) tidak menjamin bahwa Reservasi Online Tiket penyebrangan akan bebas kesalahan, bebas dari virus atau elemen lain yang berbahaya.</li>
    <li>Syarat dan ketentuan fasilitas ini diatur dan ditafsirkan sesuai peraturan internal PT ASDP Indonesia Ferry (Persero) dan peraturan perundang-undangan yang berlaku di Republik Indonesia.</li>
    

  </ol>

  <h2>KETENTUAN UMUM</h2>
  <ol>
    <li>Tarif tiket sudah termasuk asuransi.</li>
    <li>Tarif yang disajikan terdiri dari beberapa tingkat tarif yang berbeda, tarif dibedakan ke dalam 9 (sembilan) golongan jenis kendaraan di luar jenis untuk penumpang pejalan kaki, penumpang yang melakukan reservasi memiliki keleluasaan untuk memilih tingkat golongan disesuaikan oleh jumlah penumpang dan atau jenis kendaraan yang akan melakukan penyebrangan.</li>
    <li>Penumpang berusia diatas 2 (dua) tahun sampai 5 (lima) tahun akan dikenakan tarif tiket anak.</li>
    <li>Penumpang berusia dibawah 2 (dua) tahun (infant) menjadi satu kesatuan dengan satu penumpang dengan tarif dewasa.</li>
    <li>Penumpang berusia diatas 5 tahun  akan dikenakan tarif dewasa.</li>
    <li>Tiket hanya berlaku untuk pengangkutan dari pelabuhan keberangkatan ke pelabuhan kedatangan sebagaimana tercantum dalam tiket yang telah direservasi.</li>
    <li>Tiket berlaku dan sah apabila:
        <ol  type="a">
        <li>Dipergunakan oleh penumpang yang namanya tercantum pada tiket dibuktikan dengan kartu identitas penumpang yang bersangkutan dan tidak dapat dipindah tangankan.</li>
        <li>Jenis layanan, tanggal dan jam keberangkatan, golongan tiket akan tercantum di dalam tiket telah sesuai dengan kapal yang akan dinaiki.</li>
        <ol>
    </li>
    <li>Apabila penumpang kedapatan tidak memiliki tiket yang sah (jenis layanan berbeda, golongan tidak sesuai, dan sebagainya) maka penumpang tidak akan bisa naik ke dalam kapal.</li>
    <li>Waktu tertera adalah waktu pelabuhan setempat.</li>
    <li>Harap melakukan check in di pelabuhan paling cepat  jam sebelum jadwal.</li>    
    <li>Harga tiket sudah termasuk asuransi.</li>
    
  </ol>

</div>

<div id="footer">
  <p>PT ASDP Indonesia Ferry</p>
  <?php echo date('Y') ?>
</div>
</body>
</html>