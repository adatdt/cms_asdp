
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

        <table class="my-table font-small" border="1">
            <tr>
                <th rowspan="4" style="border-right:none;"><img src="<?php echo base_url()?>assets/img/asdp-logo2.jpg" class="my-img" /></th>
                <th rowspan="4" style="width:65%; text-align:center;"><h5 style="font-weight: bold;">PROSEDURE PENJUALAN TIKET DI LINGKUNGAN ASDP</h5></th>
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
                    <td>No Plat Kendaraan</td>
                    <td>: <?php echo empty($detail->plat_no)?"-":$detail->plat_no; ?></td>
                </tr>
                
                <tr>
                    <td>Tanggal Pembelian Tiket</td>
                    <td>: <?php echo empty($detail->invoice_date)?"-":format_date($detail->invoice_date); ?> </td>
                </tr>

                <tr>
                    <td>Tanggal Masuk Pelabuhan</td>
                    <td>: <?php echo empty($detail->depart_date)?"-":format_date($detail->depart_date); ?></td>
                </tr>  

                <tr>
                    <td>Pelabuhan Keberangkatan</td>
                    <td>: <?php echo empty($detail->port_name)?"-":strtoupper($detail->port_name); ?></td>
                </tr>                           

                <tr>
                    <td>No Hp/ Email</td>
                    <td>: <?php echo empty($detail->phone_number)?"":$detail->phone_number; ?> <?php echo empty($detail->email)?"":$detail->email; ?></td>
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
                    <td>Nama Penerima Dana</td>
                    <td>: <?php echo empty($detail->account_name)?"-":strtoupper($detail->account_name); ?></td>
                </tr>                                                            

            </table>
        </p>

        <!-- <p>Mengajukan Refund atas data tiket karena alesan </p> -->
         <p>Dimohon Kepada pihak PT. ASDP Indonesia Ferry (Persero) untuk mengembalikan dana sesuai dengan data diatas dan sesuai dengan aturan yang berlaku di PT. ASDP Indonesia Ferry (Persero).</p>    

<!--         <p>Dimohon Kepada pihak PT.ASDP Indonesia Ferry(Persero) untuk mengembalikan dana sesuai dengan data diatas dan sesuai dengan aturan yang berlaku di PT.ASDP Indonesia Ferry(Persero). Dengan ini saya lampirkan data pendukung berupa  </p>

        <p >
            <table class="my-table-content">
                <tr>
                    <td>Foto copy KTP</td>
                </tr>

                <tr>
                    <td>Foto copy STNK (Bila refund Kendaraan)</td>
                </tr>

                <tr>
                    <td>Foto copy Tiket</td>
                </tr>                                   

            </table>
        </p>

        <p />
            Demikian Form pengajuan ini dibuat sebenar benarnya -->

         <page_footer>
            <div style="text-align: right;">Halaman [[page_cu]]/[[page_nb]]  <?php echo empty($detail->booking_code)?"-":$detail->booking_code; ?></div>
        </page_footer>
<!--         <div style="page-break-after:always; clear:both"></div>

        <p><hr />KTP</p>                          
        <p style="text-align: center;"></p> -->

        <?php echo $img ?>

    <!-- Setting Footer -->
    <!--     <page_footer>
            <div style="text-align: right;">Halaman [[page_cu]]/[[page_nb]]</div>
        </page_footer> -->
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
