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
      /* text-align: center; */
      text-align: left;
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
    <?php 

        if(strtolower($detailRefund->myStatus)=='refund berhasil')
        {
            $text="Pengajuan Refund Anda dinyatakan berhasil dengan rincian sebagai: ";
        }
        else
        {
            $text="Pengajuan Refund Anda dinyatakan gagal dengan rincian sebagai: ";
        }

     ?>

    <p> <?php echo $text; ?> </p>
  </div>

  <div id="booking" class="content">
    <table style="width: 75%">
      
      <tr>
          <td>Nama Pemesan</td>
          <td>: <?php echo $detailRefund->customer_name; ?></td>
      </tr>
      <tr>
          <td>No Telepon/HP</td>
          <td>: <?php echo $detailRefund->phone; ?></td>
      </tr>      
      <tr>
          <td>Email</td>
          <td>: <?php echo $detailRefund->email; ?></td>
      </tr>  
      <tr>
          <td>Kode Booking</td>
          <td>: <?php echo $detailRefund->booking_code; ?></td>
      </tr>
      <tr>
          <td>Kode Refund</td>
          <td>: <?php echo $detailRefund->refund_code; ?></td>
      </tr>      
      <tr>
          <td>Harga Tiket</td>
          <td>: Rp. <?php echo $detailRefund->amount; ?></td>
      </tr>            

      <tr>
          <td>Biaya Refund</td>
          <td>: Rp. <?php echo $detailRefund->charge_amount; ?></td>
      </tr>
      <tr>
          <td>Pengembalian dana</td>
          <td>: Rp. <?php echo $detailRefund->total_amount; ?></td>
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
    <?php // echo  $disclaimer; ?>
</div>

<div id="footer">
  <p>Terima Kasih</p>
  <p></p>
  <p></p>
  <p></p>
  <p>Salam Hormat</p>
  <p>PT ASDP Indonesia Ferry (Persero)</p>
  <?php echo date('Y') ?>
</div>
</body>
</html>