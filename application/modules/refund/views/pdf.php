
<style type="text/css">
    .my-img{

        height:50px;
        width: 100px        
    }

    .my-table {

      border-collapse: collapse;
      width: 100%;
    }

    .my-table td, .my-table th {
      border: 1px solid #ddd;
      /*padding: 5px;*/
    }


    .my-table th {
      padding-top: 5px;
      padding-bottom: 5px;
      padding-left: 5px;
      padding-right: 5px;
      text-align: left;
      /*background-color: #4CAF50;*/
      /*color: white;*/
    }

    .font-small{
        font-size: 10px;
    }        
    .my-table-content td {
        padding :5px 10px 5px 10px;

    } 

    .my-table-content{
        padding-left: 50px;
    }

</style>
<?php $title="REFUND"; ?>
<!-- Setting Margin header/ kop -->
<page backtop="0mm" backbottom="14mm" backleft="0mm" backright="5mm">

    <!-- Setting Header -->
    <page_header></page_header>
    <p>
        <table class="my-table font-small" border="1">
            <tr>
                <th rowspan="4" style="border-right:none;"><img src="<?php echo base_url()?>assets/img/asdp-logo2.jpg" class="my-img" /></th>
                <th rowspan="4" style="width:65%; text-align:center;"><h5 style="font-weight: bold;">PROSEDUR PENJUALAN TIKET DI LINGKUNGAN ASDP</h5></th>
                <th style="border-right:none;" >No. Dokumen</th>
                <th style="border-left:none;" >: BK- 409.00.01</th>
            </tr>
            <tr class="font-small">
                <th style="border-right:none;" >Edisi</th>
                <th style="border-left:none;" >: 0</th>
            </tr>

            <tr class="font-small">
                <th style="border-right:none;" >Revisi</th>
                <th style="border-left:none;" >: 0</th>
            </tr>

            <tr class="font-small">
                <th style="border-right:none;" >Berlaku Efektif</th>
                <th style="border-left:none;" ></th>
            </tr>

            <tr>
                <th colspan="4" style="text-align: center;">
                    <h5 style="font-weight: bold;">FORM PENGAJUAN PENGEMBALIAN DANA TRANSAKSI TIKET ONLINE</h5>                                     
                </th>
            </tr>                       

        </table>
        <p></p>
        <?php 
            
            if($detail->ship_class==1)
            {
                $rangeTime=$detail->depart_time_start." - ".$detail->depart_time_end;
            }
            else
            {
                $rangeTime=$detail->depart_time_start;
            }
        ?>        
        <p>Saya pengguna jasa dengan data berikut :</p>


        <p >
            <table class="my-table-content">
                <tr>
                        <td>Kode Booking</td>
                        <td>: <?php echo empty($detail->booking_code)?"-":$detail->booking_code; ?></td>
                </tr>

                <tr>
                    <td>Nama</td>
                    <td>: <?php echo empty($detail->customer_name)?"-":$detail->customer_name; ?></td>
                </tr>

                <tr>
                    <td>Nomor Polisi Kendaraan</td>
                    <td>: <?php echo empty($detail->plat_no)?"-":$detail->plat_no; ?></td>
                </tr>
                
                <tr>
                    <td>Tanggal Pembelian Tiket</td>
                    <td>: <?php echo empty($detail->invoice_date)?"-":format_date($detail->invoice_date); ?> </td>
                </tr>

                <tr>
                    <td>Jadwal Masuk Pelabuhan</td>
                    <td>: <?php echo empty($detail->depart_date)?"-":format_date($detail->depart_date)." ".$rangeTime; ?></td>
                </tr>  

                <tr>
                    <td>Pelabuhan Asal</td>
                    <td>: <?php echo empty($detail->port_name)?"-":strtoupper($detail->port_name); ?></td>
                </tr>                           

                <tr>
                    <td>No Hp/ Email</td>
                    <td>: <?php echo empty($detail->phone_number)?"":$detail->phone_number; ?> 
                            <?php echo !empty($detail->email_from_refund)?$detail->email_from_refund:$detail->email; ?></td>
                </tr>                                                               

                <tr>
                    <td>No Rekening</td>
                    <td>: <?php echo empty($detail->account_number)?"-":strtoupper($detail->account_number); ?></td>
                </tr>

                <tr>
                    <td>Bank Penerima Dana</td>
                    <td>: <?php echo empty($detail->bank)?"-":strtoupper($detail->bank); ?></td>
                </tr>

                <tr>
                    <td>Nama Pemilik Rekening</td>
                    <td>: <?php echo empty($detail->account_name)?"-":strtoupper($detail->account_name); ?></td>
                </tr>                                                            

            </table>
        </p>

         <p>Dimohon Kepada pihak PT. ASDP Indonesia Ferry (Persero) untuk mengembalikan dana sesuai dengan data diatas dan sesuai dengan aturan yang berlaku di PT. ASDP Indonesia Ferry (Persero).</p>    

         <page_footer>
            <div style="text-align: right;">Halaman [[page_cu]]/[[page_nb]]  <?php echo empty($detail->booking_code)?"-":$detail->booking_code; ?></div>

              <table  class="table table-detail full-width" style="width: 100%; margin:auto;" border="0">
      <tr>
        <td colspan="5" width="700"><hr class="hr1"></td>
      </tr>
      <tr>
        <td colspan="5" style="padding: 2px 5px 0 8px;" ><span style="font-size: 11px;line-height: 1.5;"><b>Informasi, Syarat dan Ketentuan lebih lanjut dapat dilihat pada link berikut : www.ferizy.com/termsandconditions</b></span></td>
      </tr>
      <tr>
        <td colspan='5' ><hr class="hr1"></td>
      </tr>
      <tr>
        <td colspan='5' valign='top' style="font-size: 9;padding: 2px 5px 0 8px;">
          
           <b>Dikelola oleh:</b><br>
        </td>
      </tr>


      <tr>
        <td rowspan="4" style="width:17%;padding: 15px 5px 0px 8px;">
        
        </td>
        <td colspan='3'>
          <b>Hubungi kami melalui:</b>
        </td>
        <td>
            <p style="margin:2px 0 0 0; text-align: center"><b>Unduh Aplikasi Ferizy</b></p>
        </td>
      </tr>
      <tr>
        <td style="font-size: 11px;padding: 2px 4px"><b>cs@indonesiaferry.co.id</b></td>
        <td style="font-size: 11px;padding: 2px 4px"> <b>(021) 191</b></td>
        <td style="font-size: 11px;padding: 2px 4px"> <b>08111021191</b></td>
        <td rowspan="4" style="text-align: center;">
       
        </td>
      </tr>
      <tr>
        <td style="font-size: 11px;padding: 2px 4px"><b>ASDP Indonesia Ferry</b></td>
        <td style="font-size: 11px;padding: 2px 4px"><b>@asdp191</b></td>
        <td style="font-size: 11px;padding: 2px 4px"><b>@asdp191</b></td>
      </tr> 
      <tr>
        <td>
          
        </td>
        <td>
          
        </td>
        <td>
          
        </td>
      </tr>
    </table>
        </page_footer>

        <?php echo $img ?>
    </p>
</page>

<!-- Memanggil fungsi bawaan HTML2PDF -->
<?php
$content = ob_get_clean();
 // include 'html2pdf_v4.03/html2pdf.class.php';
 try
{
  // setting paper
    $html2pdf = new HTML2PDF('P', 'A4', 'en', false, 'UTF-8', array(10, 10, 10,10,10,10));
    $html2pdf->pdf->SetTitle($title);
    $html2pdf->pdf->SetDisplayMode('fullpage');
    $html2pdf->writeHTML($content);
    $html2pdf->Output('Refund.pdf');

}
catch(HTML2PDF_exception $e) {
    echo $e;
    exit;
}
?>
