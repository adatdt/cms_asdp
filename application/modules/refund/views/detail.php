
<style type="text/css">
    .wajib{color: red}

    .my-table td {
    	padding :5px 10px 5px 20px;
    	font-size: 18px;
    	font-family: "Times New Roman", Times, serif;

    } 

   	p {
   		padding-right: 20px;
   		font-size: 18px;
   		font-family: "Times New Roman", Times, serif;

   	}
    .my-table {
    	margin-left:  50px;
    } 

    .my-content{
    	padding-left: 100px;
    }

    .scrolling{

        height: 450px;
        overflow-y: auto;
        overflow-x: hidden;
    } 

    div.bottom-layout {
      position: absolute;
      width: 95%;
      bottom: 10px;
      text-align: right;
      font-size: 12px;
      color:red;
      font-style: italic;
    }        

    div.parent {
      position: relative;
      min-height: 100px;
      font-size: 18px;
      font-family: Times, "Times New Roman", serif;
    }

</style>
<div class="col-md-10 col-md-offset-1">
    <div class="portlet box blue" id="box">
        <?php echo headerForm($title) ?>
        <div class="portlet-body">


                <div class="portlet-body">
                    <div class="kt-portlet">
                        <div class="kt-portlet__head">
                            <div class="kt-portlet__head-label">
                                <h3 class="kt-portlet__head-title">
                                </h3>
                            </div>
                        </div>
                        <div class="kt-portlet__body">
                            <ul class="nav nav-tabs " role="tablist">
                                <li class="nav-item active">
                                    <a class="label label-primary" data-toggle="tab" href="#tab">Form Pengajuan </a>
                                </li>

                                <li class="nav-item ">
                                    <a class="label label-primary " data-toggle="tab" href="#tab2">History Refund</a>
                                </li>
                                
                                <li class="nav-item ">
                                    <a class="label label-primary " data-toggle="tab" href="#tab3">Bukti Nodin</a>
                                </li>

                                <li class="nav-item ">
                                    <a class="label label-primary " data-toggle="tab" href="#tab4">Bukti Transfer</a>
                                </li>
                            </ul>
          
                            <div class="tab-content " >
                                <div class="tab-pane active" id="tab" role="tabpanel" >
                                    <div class="box-body ">
                                         <div class="form-group">
                                            <div class="row">
                                                <div class="col-md-12 form-group">
                                                    <?php 
                                                    echo generate_button('refund', 'download_pdf','
                                                        <a target="_blank" href="'.site_url('refund/refund/download_pdf_detail/'.$booking_enc).'" class="btn btn-default pull-right" title="PDF" > <i class="fa fa-file-pdf-o" style="color: #ea5460"></i>&nbsp;&nbsp;PDF</a>
                                                    '); ?>

                                                </div>                                                
                                            </div>

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

                                            <div class="row mt-list-container list-simple max-height collapse in scrolling">                                                
                                                <div class="col-md-12">
                                                    <table class="table table-bordered">
                                                        <tr>
                                                            <th rowspan="4" style="padding:0px; border-right: none;"><img src="<?php echo base_url()?>assets/img/asdp-logo2.jpg" width="200px" height="100px" ></th>
                                                            <th rowspan="4" style="text-align: center; border-left: none;"><h4 style="font-weight: bold;">PROSEDUR PENJUALAN TIKET DI LINGKUNGAN ASDP</h4></th>
                                                            <th>No. Dokumen</th>
                                                            <th>: BK- 409.00.01</th>
                                                        </tr>
                                                        <tr>
                                                            <th>Edisi</th>
                                                            <th>: 0</th>
                                                        </tr>

                                                        <tr>
                                                            <th>Revisi</th>
                                                            <th>: 0</th>
                                                        </tr>

                                                        <tr>
                                                            <th>Berlaku Efektif</th>
                                                            <th></th>
                                                        </tr>

                                                        <tr>
                                                            <th colspan="4" style="text-align: center;">
                                                                <h4 style="font-weight: bold;">FORM PENGAJUAN PENGEMBALIAN DANA TRANSAKSI TIKET ONLINE</h4>                                     
                                                            </th>
                                                        </tr>                       

                                                    </table>
                                                </div>


                                                <div class="col-md-12 my-content">

                                                    <p>Saya pengguna jasa dengan data berikut :</p>

                                                    <p >
                                                        <table class="my-table">
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
                                                                <td>: <?php echo empty($detail->invoice_date)?"-":format_date($detail->invoice_date); ?></td>
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
                                                                    <?php echo !empty($detail->email_from_refund)?$detail->email_from_refund:$detail->email; ?>
                                                                    
                                                                </td>
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

                                                    <!-- <p>Mengajukan Refund atas data tiket karena alasan </p> -->

<!--                                                     <p>Dimohon Kepada pihak PT.ASDP Indonesia Ferry(Persero) untuk mengembalikan dana sesuai dengan data diatas dan sesuai dengan aturan yang berlaku di PT.ASDP Indonesia Ferry(Persero). Dengan ini saya lampirkan data pendukung berupa  </p> -->
                                                    <p>Dimohon kepada pihak PT. ASDP Indonesia Ferry (Persero) untuk mengembalikan dana sesuai dengan data diatas dan sesuai dengan aturan yang berlaku di PT. ASDP Indonesia Ferry (Persero).</p>
<!-- 
                                                    <p >
                                                        <table class="my-table">
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

                                                    <p>
                                                        Demikian Form pengajuan ini dibuat sebenar benarnya
                                                    </p>                            --> 
                                                </div>

<!--                                                 <div class="col-md-12 form-group"></div>
                                                
                                                 <div class="col-md-12 form-group"><legend></legend></div>
                                                <div class="col-md-12 form-group my-content">
                                                    <p>KTP</p>
                                                    <center>
                                                        <img src="data:image/png;base64,<?php echo $img ?>" alt="" width='500' height='250'>
                                                    </center>

                                                </div>

                                                <div class="col-md-12 form-group"></div>
                                                
                                                 <div class="col-md-12 form-group"><legend></legend></div>
                                                <div class="col-md-12 form-group my-content">
                                                    <p>STNK</p>
                                                    <center>
                                                        <img src="data:image/png;base64,<?php echo $stnk ?>" alt="" width='500' height='250'>
                                                    </center>

                                                </div>  -->     

                                                <?php echo $imgHtml; ?>                                           

                                            </div>

                                        </div>
                                    </div>

                                </div>

                                <div class="tab-pane" id="tab2" role="tabpanel" >
                                    <div class="box-body mt-list-container list-simple max-height collapse in scrolling">

                                        <?php if(!$historyTransfer){ ?>
                                        <div class="alert alert-danger">
                                            <center>Tidak Ada Riwayat</center>
                                        </div>
                                        <?php } ?>

                                        <?php foreach($historyTransfer as $key=>$value) { 
                                            $getComent = $value->transfer_status == 6 ? $value->komentar_cs : $value->transfer_description;
                                        ?>
                                        <div class="note note-info">
                                            <div class="row">
                                                <div class="col-md-2">
                                                    <h4 style="margin-top:5px"><i class="fa fa-user  fa-3x"></i></h4>
                                                    <p style="font-size: 18px;"> <?php echo $value->created_by; ?> </p>

                                                </div>

                                                <div class="col-md-8">
                                                    <div class="parent"> <?php echo $getComent."
                                                    <div style='height:5px'></div>  
                                                    <br>
                                                     <div class='bottom-layout'>". $value->status_tgl_edit ." pada tanggal ". format_dateTimeHis($value->created_on)."</div>" ?> </div>
                                                </div>

                                                <div class="col-md-2">
                                                    <p> 
                                                        <?php 
                                                            echo $value->transfer_status==2?success_label("Berhasil Transfer"):warning_label($value->status_refund)
                                                            // echo $value->transfer_status==2?success_label("Berhasil Transfer"):warning_label($value->description)
                                                         ?> 
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <?php } ?>

                                    </div>
                                        
                                </div>

                                <div class="tab-pane" id="tab3" role="tabpanel" >
                                    <div class="box-body mt-list-container list-simple max-height collapse in scrolling">
                                        <?php echo $imgBuktiNodin; ?>
                                    </div>
                                </div>

                                <div class="tab-pane" id="tab4" role="tabpanel" >
                                    <div class="box-body mt-list-container list-simple max-height collapse in scrolling">
                                        <?php echo $imgBuktiTrf; ?>
                                    </div>
                                </div>


                            </div>      
                        </div>
                    </div>
                </div>


        </div>
    </div>
</div>

<script type="text/javascript">
    noImg =(id)=>{
        document.getElementById(id).innerHTML = "Gambar Tidak Tersedia.";
    }

    $(document).ready(function(){

	    $("#btndpdf").click(function(event){

		    window.open("<?php echo site_url('refund/refund/download_pdf')?>");

	    });

    })
</script>