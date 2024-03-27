
<style type="text/css">
    .my-img{

        height:50px;
        width: 100px        
    }

    .my-table {

      border-collapse: collapse;
      width: 100%;
    }

    .my-table td {
      border: 1px solid #ddd;
      /*padding: 5px;*/
    }


    .my-table th {
      padding-top: 5px;
      padding-bottom: 5px;
      padding-left: 5px;
      padding-right: 5px;
      text-align: left;
      background-color: #CED9DA;
      border: 1px solid white;
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

<!-- Setting Margin header/ kop -->
<page backtop="13mm" backbottom="0mm" backleft="0mm" backright="5mm" footer='page'>


    <!-- Setting Header -->
    <page_header>
         <div style="text-align: center;"><h3><?php $title='Booking Refund ' . $dateFrom . ' s/d ' . $dateTo; echo $title; ?></h3></div>
    </page_header>

    <table border="1"  class="my-table font-small">
            <!-- <thead> -->
                <tr>
                    <th  rowspan="2">NO</th>
                    <th  rowspan="2">KODE <br> BOOKING</th>
                    <th  rowspan="2">NAMA</th>
                    <th  rowspan="2">NO HP</th>
                    <th  rowspan="2">TANGGAL REFUND</th>
                    <th  rowspan="2">KODE REFUND</th>
                    <th  rowspan="2">JENIS REFUND</th>
                    <th  rowspan="2">ASAL</th>
                    <th  rowspan="2">TUJUAN</th>
                    <th  rowspan="2">LAYANAN</th>
                    <th  rowspan="2">JENIS PJ</th>
                    <th  rowspan="2">NO <br> POLISI <br> KENDA- <br>RAAN</th>
                    <th  rowspan="2">GOLONGAN</th>
                    <th  rowspan="2">NO. REKENING</th>
                    <th  rowspan="2">NAMA PEMILIK <br> REKENING</th>
                    <th  rowspan="2">BANK</th>
                    <th  rowspan="2">HARGA TIKET</th>
                    <th  rowspan="2">BIAYA <br> ADMINISTRASI</th>
                    <th  rowspan="2">BIAYA <br> REFUND</th>
                    <th  rowspan="2">BIAYA <br> TRANSFER</th>
                    <th  rowspan="2">JUMLAH <br> POTONGAN</th>
                    <th  rowspan="2">PENGEMBALIAN <br> DANA</th>
                    <th  rowspan="2">STATUS REFUND</th>
                    <th  colspan="6" style="text-align:center">PROSES APPROVAL CONTACT CENTER/ CUSTOMER SERVICE</th>
                    <th  colspan="6" style="text-align:center">PROSES APPROVAL DIVISI USAHA</th>
                    <th  colspan="6" style="text-align:center">PROSES APPROVAL DIVISI KEUANGAN</th>
                    <th  colspan="2" style="text-align:center">SLA PENYELAESAIAN</th>
                </tr>
                <tr>
                    <th >STATUS</th>
                    <th >USER</th>
                    <th >TANGGAL</th>
                    <th >SLA <br> HARI <br> KERJA</th>
                    <th >KETERANGAN</th>
                    <th >CATATAN</th>
                    <th >STATUS</th>
                    <th >USER</th>
                    <th >TANGGAL</th>
                    <th >SLA <br> HARI <br> KERJA </th>
                    <th >KETERANGAN</th>
                    <th >CATATAN</th>
                    <th >STATUS</th>
                    <th >USER</th>
                    <th >TANGGAL</th>
                    <th >SLA <br> HARI <br> KERJA</th>
                    <th >KETERANGAN</th>
                    <th >CATATAN</th>
                    <th >DURASI <br> SLA</th>
                    <th >KETERANGAN</th>
                </tr>                        
            <!-- </thead> -->

                
            <tbody> 
                <?php $no = 1;
                foreach ($data as $key => $value) {


                ?>
                <tr>
                    <td><?php echo  $no ?></td>
                    <td> <?php echo $value->booking_code ?> </td>
                    <td> <?php echo $value->name; ?> </td>
                    <td> <?php echo $value->phone; ?> </td>
                    <td> <?php echo $value->created_on; ?> </td>
                    <td> <?php echo $value->refund_code; ?> </td>
                    <td> <?php echo $value->refund_type; ?> </td>
                    <td> <?php echo $value->asal; ?> </td>
                    <td> <?php echo $value->tujuan; ?> </td>
                    <td> <?php echo $value->layanan ?> </td>
                    <td> <?php echo $value->jenis_pj; ?> </td>
                    <td style="width: 2%;"> <?php echo $value->id_number; ?> </td>
                    <td> <?php echo $value->golongan; ?> </td>
                    <td> <?php echo $value->account_number; ?> </td>
                    <td style="width: 3%;"> <?php echo $value->account_name; ?> </td>
                    <td> <?php echo $value->bank; ?> </td>
                    <td> <?php echo $value->amount; ?> </td>
                    <td> <?php echo $value->adm_fee ?> </td>
                    <td> <?php echo $value->refund_fee; ?> </td>
                    <td> <?php echo $value->bank_transfer_fee; ?> </td>
                    <td> <?php echo $value->jumlah_potongan; ?> </td>
                    <td> <?php echo $value->dana_pengembalian; ?> </td>
                    <td> <?php echo $value->status_refund; ?> </td>
                    <td> <?php echo $value->approved_status_cs; ?> </td>
                    <td> <?php echo $value->approved_by_cs; ?> </td>
                    <td style="width: 3%;"> <?php echo $value->approved_on_cs ?> </td>
                    <td> <?php echo $value->sla_cs; ?> </td>
                    <td> <?php echo $value->keterangan_cs; ?> </td>
                    <td style="width: 4%;"> <?php echo $value->catatan_cs; ?> </td>
                    <td> <?php echo $value->status_approved; ?> </td>
                    <td> <?php echo $value->approved_by; ?> </td>
                    <td style="width: 3%;"> <?php echo $value->approved_on; ?> </td>
                    <td> <?php echo $value->sla_usaha; ?> </td>
                    <td> <?php echo $value->keterangan_usaha; ?> </td>
                    <td> <?php echo $value->catatan_usaha; ?> </td>
                    <td> <?php echo $value->status_keuangan; ?> </td>
                    <td> <?php echo $value->approved_by_keuangan; ?> </td>
                    <td style="width: 3%;"> <?php echo $value->approved_on_keuangan; ?> </td>
                    <td> <?php echo $value->sla_keuangan; ?> </td>
                    <td> <?php echo $value->keterangan_keuangan; ?> </td>
                    <td> <?php echo $value->catatan_keuangan; ?> </td>
                    <td> <?php echo $value->durasi ?> </td>
                    <td> <?php echo $value->keterangan; ?> </td>
                </tr>

            <?php $no++;
            }?>
        </tbody>
    </table>

    <page_footer>
        <!-- <div style="text-align: right;">Halaman [[page_cu]]/[[page_nb]] </div> -->
    </page_footer>

</page>
<!-- Memanggil fungsi bawaan HTML2PDF -->
<?php
$content = ob_get_clean();
 // include 'html2pdf_v4.03/html2pdf.class.php';
 try
{

  // setting paper
    $html2pdf = new HTML2PDF('L', 'A1', 'en', false, 'UTF-8', array(10, 10, 10,10,10,10));
    $html2pdf->pdf->SetDisplayMode('fullpage');
    $html2pdf->pdf->SetTitle($title);
    $html2pdf->writeHTML($content);
    $html2pdf->Output('Refund.pdf');

}
catch(HTML2PDF_exception $e) {
    echo $e;
    exit;
}
?>
