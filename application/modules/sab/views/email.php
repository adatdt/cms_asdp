<?php
/*
  Document   : email
  Created on : Sep 7, 2018 10:34:19 AM
  Author     : Andedi
  Description: Purpose of the PHP File follows.
 */

if (!defined('BASEPATH'))
  exit('No direct script access allowed');
?>
<html>
  <head>
    <title><?php echo $title; ?></title>
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
      <p>Dear <?php echo $booking->customer_name; ?>,</p>
      <p>
        Permintaan cetak boarding pass dengan Kode booking <b><?php echo $booking->booking_code; ?></b>  telah kami terima. Langkah selanjutnya silahkan cetak boarding pass.
      </p>
     
    </div>

    <div id="booking" class="content">
      
      <table style="width: 75%">
        <tr>
          <td>Nama Pemesan</td>
          <td>: <?php echo $booking->customer_name; ?></td>
        </tr>
        <tr>
          <td>Nomor Telfon</td>
          <td>: <?php echo $booking->phone_number; ?></td>
        </tr>
        <tr>
          <td>Email</td>
          <td>: <?php echo $booking->email; ?></td>
        </tr>
       
        
        <tr>
          <td>Tanggal keberangkatan</td>
          <td>: <?php echo format_date($booking->depart_date); ?></td>
        </tr>
        <tr>
          <td>Jenis Layanan</td>
          <td>: <?php echo $booking->name; ?></td>
        </tr>
        <tr>
          <td>Pelabuhan Keberangkatan</td>
          <td>: <?php echo $booking->origin_name; ?></td>
        </tr>
        <tr>
          <td>Pelabuhan Kedatangan</td>
          <td>: <?php echo $booking->destination_name; ?></td>
        </tr>
        
      </table>
      <h2>Informasi Booking</h2>
      
    <div class="content">
      <h2>Syarat dan Ketentuan</h2>
      <ol>
        <li>Waktu tertera adalah waktu pelabuhan setempat.</li>        
        <li>Tiket yang sudah dibeli, tidak dapat dilakukan pengembalian/refund.</li>
        <li>Harga tiket sudah termasuk asuransi.</li>
      </ol>
    </div>

    <div id="footer">
      <p>PT ASDP Indonesia Ferry</p>
      <?php echo date('Y') ?>
    </div>
  </body>
</html>