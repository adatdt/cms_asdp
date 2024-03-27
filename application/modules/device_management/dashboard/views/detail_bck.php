<link href="<?php echo base_url(); ?>assets/global/plugins/icheck/skins/all.css" rel="stylesheet" type="text/css" />

<style type="text/css">
    .wajib{ color:red; }
    .switch {
        position: relative;
        display: block;
        width: 90px;
        height: 34px;
    }

    .switch input {display:none;}

    .slidertes {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc; /*#ca2222;*/
        -webkit-transition: .4s;
        transition: .4s;
        border-radius: 34px !important;
    }

    .slidertes:before {
        position: absolute;
        content: "";
        height: 26px;
        width: 26px;
        left: 4px;
        bottom: 4px;
        background-color: white;
        -webkit-transition: .4s;
        transition: .4s;
        border-radius: 50% !important;
    }

    input:checked + .slidertes {
        background-color: #3598dc;
    }

    input:focus + .slidertes {
        box-shadow: 0 0 1px #2196F3;
    }

    input:checked + .slidertes:before {
        -webkit-transform: translateX(26px);
        -ms-transform: translateX(26px);
        transform: translateX(55px);
    }

    /*------ ADDED CSS ---------*/
    .slidertes:after
    {
        content:'OFF';
        color: white;
        display: block;
        position: absolute;
        transform: translate(-50%,-50%);
        top: 50%;
        left: 50%;
        font-size: 12px;
        font-family: "Open Sans", sans-serif;
    }

    input:checked + .slidertes:after
    {  
        content:'ON';
    }

    input::-webkit-outer-spin-button,
    input::-webkit-inner-spin-button {
      -webkit-appearance: none;
      margin: 0;
    }

    .mfp-bg {
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 99999999;
    overflow: hidden;
    position: fixed;
    background: #0b0b0b;
    opacity: .8;
    }

   .mfp-wrap {
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 99999999;
    position: fixed;
    outline: none !important;
    -webkit-backface-visibility: hidden;
    }

</style>

<div class="col-md-10 col-md-offset-1">
    <div class="portlet box blue" id="box">
        <?php echo headerForm($title) ?>
        <div class="portlet-body">

                    <div class="row">
                        <div class="col-sm-12 form-inline">

                            <div class="input-group select2-bootstrap-prepend">
                                <div class="input-group-addon">Tanggal Dibuat</div>
                                <input type="text" class="form-control date input-small" id="dateFrom" value="" readonly>
                                <div class="input-group-addon">s/d</div>
                                <input type="text" class="form-control date input-small" id="dateTo" value="" readonly>
                            </div>                           

                            <div class="input-group select2-bootstrap-prepend">
                                <div class="input-group-addon">Tanggal Publikasi</div>
                                <input type="text" class="form-control date input-small" id="startPublish" placeholder="YYYY-MM-DD" readonly>
                            </div>                                                       

                            
                            <div class="input-group pad-top">
                                <button type="button" class="btn btn-danger mt-ladda-btn ladda-button" data-style="zoom-in" id="cari">
                                    <span class="ladda-label">Cari</span>
                                    <span class="ladda-spinner"></span>
                                </button>
                            </div>

                        </div>
                    </div>                    

                    <p></p>
                    <table class="table table-bordered table-hover" id="dataTables">
                        <thead>
                            <tr>
                                <th>NO</th>
                                <th>NOMOR TIKET</th>
                                <th>KODE BOOKING</th>
                                <th>NAMA PEMESAN</th>
                                <th>NO TELEPON</th>  
                                <th>NAMA PENUMPANG</th> 
                                <th>NIK</th>
                                <th>ASAL</th> 
                                <th>LAYANAN</th> 
                                <th>TANGGAL & JAM MASUK PELABUHAN</th> 
                                <th>GOLONGAN</th> 
                                <th>NO POLISI</th> 
                                <th>TIPE PEMBAYARAN</th> 
                                <th>CHANNEL</th> 
                                <th>TARIF TIKET</th> 
                                <th>BIAYA ADMIN</th> 
                                <th>TOTAL BAYAR</th> 
                                <th>STATUS</th> 
                                <th>PEMESANAN </th> 
                                <th>PEMBAYARAN </th> 
                                <th>CETAK BOARDING PASS</th> 
                                <th>VALIDASI</th>
                                <th>
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                AKSI LIHAT DETAIL
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                </th>
                            </tr>                        
                        </thead>


                        <tfoot></tfoot>
                    </table>
                </div>
    </div>
</div>
