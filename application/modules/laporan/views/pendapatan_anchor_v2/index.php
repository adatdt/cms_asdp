<div class="page-content-wrapper">
    <div class="page-content">
        <div class="page-bar">
            <ul class="page-breadcrumb">
                <li>
                    <?php echo '<a href="' . $url_home . '">' . $home . '</a>'; ?>
                    <i class="fa fa-circle"></i>
                </li>
                <li>
                    <span><?php echo $title; ?></span>
                </li>
            </ul>
            <div class="page-toolbar">
                <div id="dashboard-report-range" class="pull-right tooltips btn btn-sm" data-container="body" data-placement="bottom">
                    <span class="thin uppercase hidden-xs" id="datetime"></span>
                    <script type="text/javascript">window.onload = date_time('datetime');</script>
                </div>
            </div>
        </div>

        <?php $now=date("Y-m-d"); $last_month=date('Y-m-d',strtotime("-30 days"))?>
        <div class="my-div-body">
            <div class="portlet box blue-madison">
                <div class="portlet-title">

                    <div class="caption"><?php echo $title ?></div>
                </div>
                <div class="portlet-body">
                    <div class="row">
                        <div class="col-md-12">
                                <?php echo form_open($url, 'id="ff" autocomplete="off"'); ?>
                                    <div class="table-toolbar" style="margin-bottom: 0px">
                                        <div class="row">
                                            <div class="col-md-12 filter-trx">
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <div class="input-group">
                                                            <span class="input-group-addon">Pelabuhan</span>
                                                            <select id="port" name="port" class="form-control select2 in-group" required data-placeholder="Semua">
                                                                <option value=""></option>
                                                                <?php foreach($port as $key=>$value) {?>
                                                                    <option value="<?php echo $this->enc->encode($value->id); ?>"><?php echo strtoupper($value->name); ?></option>
                                                                <?php }?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>  
                                                <div class="col-md-5">
                                                    <div class="form-group">
                                                        <div class="input-group">
                                                            <span class="input-group-addon">Tanggal Shift</span>
                                                            <input type="text" name="start_date" class="form-control in-group" id="dateFrom" value="<?php echo $last_month; ?>" readonly required>
                                                            <span class="input-group-addon">s/d</span>
                                                            <input type="text" name="end_date" class="form-control in-group" id="dateTo" value="<?php echo $now; ?>" readonly required>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <div class="input-group">
                                                            <span class="input-group-addon">Kapal</span>
                                                            <select id="ship" name="ship" class="form-control select2 in-group" required data-placeholder="Pilih Kapal">
                                                                <option value=""></option>
                                                                <?php foreach($ship as $key=>$value) {?>
                                                                    <option value="<?php echo $this->enc->encode($value->id); ?>"><?php echo strtoupper($value->name); ?></option>
                                                                <?php }?>
                                                            </select>
                                                            <span class="input-group-btn">
                                                                <button class="btn btn-success" id="searching" type="submit" data-loading-text="<i class='fa fa-spinner fa-spin'></i>" title="Search">
                                                                    <i class="fa fa-search"></i>
                                                                </button>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <div class="input-group">
                                                            <span class="input-group-addon">Shift</span>
                                                            <select id="shift" name="shift" class="form-control select2 in-group" data-placeholder="Semua">
                                                                <option value=""></option>
                                                                <?php foreach($shift as $key=>$value) {?>
                                                                    <option value="<?php echo $this->enc->encode($value->id); ?>"><?php echo strtoupper($value->shift_name); ?></option>
                                                                <?php }?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>  
                                            </div>
                                        </div>
                                    </div>
                                <?php echo form_close(); ?>

                                <div class="pdf hidden" style="padding-bottom: 10px">
                                    <?php if($cek_download_pdf){ ?>
                                        <button id="printPdf" class="btn btn-sm btn-default"><i class="fa fa-file-pdf-o" style="color: #ea5460;"></i> PDF</button>
                                    <?php } ?>

                                    <?php if($cek_download_excel){ ?>
                                        <button id="printExcel" class="btn btn-sm btn-default"><i class="fa fa-file-excel-o" style="color: #ea5460"></i> EXCEL</button>
                                    <?php } ?>

                                </div>

                                <div id="box" class="boxer hidden">
                                    <table class="table table-bordered table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th rowspan="4" class="col-img"><center><img src="<?php echo base_url();?>assets/img/asdp.png" style="width:120px; height: auto"></center></th>
                                                <th rowspan="4" class="col-title">LAPORAN PRODUKSI DAN<br>PENDAPATAN KAPAL RORO JASA SANDAR ENGKER</th>
                                                <th class="text-left">No. Dokumen</th>
                                                <th class="col-titik">:</th>
                                            </tr>
                                            <tr>
                                                <th class="text-left">Revisi</th>
                                                <th class="col-titik">: </th>
                                            </tr>
                                            <tr>
                                                <th class="text-left">Berlaku Efektif</th>
                                                <th class="col-titik">: </th>
                                            </tr>
                                            <tr>
                                                <th class="text-left">Halaman</th>
                                                <th class="col-titik">:</th>
                                            </tr>
                                        </thead>
                                    </table>

                                    <table class="table table-bordered table-hover">
                                        <tr>
                                            <td class="right-none">CABANG</td>
                                            <td class="titik-2">:</td>
                                            <td class="detail left-none" id="cabang"></td>
                                            <td class="right-none">KAPAL</td>
                                            <td class="titik-2">:</td>
                                            <td class="detail left-none"id="kapal"></td>
                                        </tr>
                                        <tr>
                                            <td class="right-none">PELABUHAN</td>
                                            <td class="titik-2">:</td>
                                            <td class="detail left-none" id="pelabuhan"></td>
                                            <td class="right-none">PERUSAHAAN</td>
                                            <td class="titik-2">:</td>
                                            <td class="detail left-none" id="perusahaan"></td>
                                        </tr>
                                        <tr>
                                            <td class="right-none">LINTASAN</td>
                                            <td class="titik-2">:</td>
                                            <td class="detail left-none" id="lintasan"></td>
                                            <td class="right-none">GRT</td>
                                            <td class="titik-2">:</td>
                                            <td class="detail left-none" id="grt"></td>
                                        </tr>
                                        <tr>
                                            <td class="right-none">TANGGAL SHIFT</td>
                                            <td class="titik-2">:</td>
                                            <td class="detail left-none" id="tanggal"></td>
                                            <td class="right-none">SHIFT</td>
                                            <td class="titik-2">:</td>
                                            <td class="detail left-none" id="shiftku"></td>
                                        </tr>
                                    </table>

                                    <table class="table table-bordered table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th class="col-title">NO</th>
                                                <th class="col-title">TANGGAL ENGKER</th>
                                                <th class="col-title">DERMAGA</th>
                                                <th class="col-title">TARIF</th>
                                                <th class="col-title">CALL ENGKER</th>
                                                <th class="col-title">PRODUKSI<br>(GRT/CALL)</th>
                                                <th class="col-title">PENDAPATAN</th>
                                            </tr>
                                        </thead>
                                        <tbody id="list">
                                           <tr>
                                               <td align="center" colspan="7">Data tidak ditemukan</td>
                                           </tr>
                                        </tbody>
                                    </table>
                                </div>
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<form id="formDownload" target="_blank" method="POST"></form>

<style type="text/css">
    .table>tbody>tr>td, .table>tbody>tr>th, .table>tfoot>tr>td, .table>tfoot>tr>th, .table>thead>tr>td, .table>thead>tr>th {
        padding: 5px;
        line-height: 1.42857;
        vertical-align: top;
        border-top: 1px solid #e7ecf1;
    }

    .col-img {
        text-align: left !important; 
        padding: 25px 5px 5px 30px !important;
    }

    .col-title {
        text-align: center !important; 
        vertical-align: middle !important;
    }

    .text-left {
        text-align: left !important;
    }

    .col-titik {
        text-align: left !important; 
        width: 20% !important;
    }

    .table-bordered, .table-bordered>tbody>tr>td, .table-bordered>tbody>tr>th, .table-bordered>tfoot>tr>td, .table-bordered>tfoot>tr>th, .table-bordered>thead>tr>td, .table-bordered>thead>tr>th {
        border: 1px solid black;
    }

    .right-none {
        border-right: none !important;
        width: 15%
    }

    .left-none {
        border-left: none !important;
        width: 35%
    }

    .titik-2 {
        border-left: none !important; 
        border-right: none !important; 
        width: 2%
    }

    .bold {
        font-weight: bold;
    }
</style>

<script type="text/javascript">    
    jQuery(document).ready(function () {
        setTimeout(function() {
            $('.menu-toggler').trigger('click');
            $('.select2').select2();
        }, 1);

        $(".menu-toggler").click(function() {
            $('.select2').css('width', '100%');
        });

        $('.select2').change(function(){
            $(this).valid();
        })

        $('#dateFrom').datepicker({
            format: 'yyyy-mm-dd',
            changeMonth: true,
            changeYear: true,
            autoclose: true,
            endDate: new Date(),
        }).on('changeDate',function(e) {
            $('#dateTo').datepicker('setStartDate', e.date);
        });

        $('#dateTo').datepicker({
            format: 'yyyy-mm-dd',
            changeMonth: true,
            changeYear: true,
            autoclose: true,
            starthate: $('#dateFrom').val(),
            endDate: new Date(),
        }).on('changeDate',function(e) {
            $('#dateFrom').datepicker('setEndDate', e.date);
        });

        var dSend = null;
        $('#ff').validate({
            ignore      : 'input[type=hidden], .select2-search__field', 
            errorClass  : 'validation-error-label',
            successClass: 'validation-valid-label',
            rules       : rules,
            messages    : messages,

            highlight   : function(element, errorClass) {
                $(element).addClass('val-error');
            },

            unhighlight : function(element, errorClass) {
                $(element).removeClass('val-error');
            },

            errorPlacement: function(error, element) {
                if(element.hasClass('in-group')) {
                    error.appendTo( element.parent().parent() );
                }

                else {
                    error.insertAfter(element);
                }
            },

            submitHandler: function(form) {
                data = getFormData($(form));
                data.port_name = $("#port option:selected").text();
                data.ship_name = $("#ship option:selected").text();
                data.shift_name = $("#shift option:selected").text();

                $.ajax({
                    url         : form.action,
                    data        : data,
                    type        : 'POST',
                    dataType    : 'json',

                    beforeSend: function(){
                        $('#box').block({
                            message: '<h4><i class="fa fa-spinner fa-spin"></i> Loading</h4>',
                            overlayCSS: { backgroundColor: '#FFFFFF' },
                            css: { 
                                border: 'none', 
                                padding: '15px', 
                                backgroundColor: '#000', 
                                '-webkit-border-radius': '10px', 
                                '-moz-border-radius': '10px', 
                                opacity: .5, 
                                color: '#fff' 
                            }
                        });

                        $('#searching').button('loading');
                    },

                    success: function(json) {
                        dSend = json.data.post;

                        if(json.code == 1){
                            d = json.data.data;
                            det = json.data.detail;

                            if(d.length){
                                html = '';
                                for(i in d){
                                    html += '<tr>\
                                        <td class="text-center">'+d[i].no+'</td>\
                                        <td class="text-center">'+d[i].date+'</td>\
                                        <td class="text-center">'+d[i].dock_name+'</td>\
                                        <td class="text-right">'+formatIDR(d[i].dock_fare)+'</td>\
                                        <td class="text-right">'+formatIDR(d[i].call_anchor)+'</td>\
                                        <td class="text-right">'+formatIDR(d[i].ship_grt)+'</td>\
                                        <td class="text-right bold">'+formatIDR(d[i].total)+'</td>\
                                     </tr>';
                                }

                                html += '<tr>\
                                    <td colspan="4" class="text-right bold">SUBTOTAL</td>\
                                    <td class="text-right bold">'+formatIDR(json.data.total_anchor)+'</td>\
                                    <td class="text-right bold">'+formatIDR(json.data.total_grt)+'</td>\
                                    <td class="text-right bold">'+formatIDR(json.data.sub_total)+'</td>\
                                </tr>'

                                $('#list').html(html);

                                $('#cabang').html(det.origin);
                                $('#pelabuhan').html(det.origin+' '+det.class_name);
                                $('#lintasan').html(det.origin+' - '+det.destination);
                                $('#kapal').html(det.ship_name);
                                $('#perusahaan').html(det.company_name);
                                $('#grt').html(det.ship_grt);
                                $('#tanggal').html(json.data.tanggal);
                                $('#shiftku').html(json.data.shift_name);
                                $('.boxer').removeClass('hidden');
                                $('.pdf').removeClass('hidden');
                            }else{
                                $('#list').html('<tr><td colspan="7" align="center">Data tidak ditemukan</td></tr>');
                                $('.detail').html('');
                                $('.boxer').addClass('hidden');
                                $('.pdf').addClass('hidden');

                                toastr.warning('Data tidak ditemukan', 'Peringatan');
                            }
                        }else{
                            toastr.error(json.message, 'Gagal');
                        }
                    },

                    error: function() {
                        toastr.error('Silahkan Hubungi Administrator', 'Gagal');
                    },

                    complete: function(){
                        $('#box').unblock();
                        $('#searching').button('reset');
                    }
                });
            }
        })

        $('#printPdf').click(function(){
            addForm = '<input type="text" name="start_date" value="'+dSend.start_date+'">\
            <input type="text" name="end_date" value="'+dSend.end_date+'">\
            <input type="text" name="port" value="'+dSend.port+'">\
            <input type="text" name="port_name" value="'+dSend.port_name+'">\
            <input type="text" name="ship" value="'+dSend.ship+'">\
            <input type="text" name="shift" value="'+dSend.shift+'">\
            <input type="text" name="shift_name" value="'+dSend.shift_name+'">\
            <input type="text" name="ship_name" value="'+dSend.ship_name+'">\
            <input type="text" name="shift" value="'+dSend.shift+'">';

            $('#formDownload').attr('action','<?php echo $urlDownload; ?>');
            $('#formDownload').html(addForm);
            $('#formDownload').submit();
            $('#formDownload input').remove();
        })

         $('#printExcel').click(function(){
            addForm = '<input type="text" name="start_date" value="'+dSend.start_date+'">\
            <input type="text" name="end_date" value="'+dSend.end_date+'">\
            <input type="text" name="port" value="'+dSend.port+'">\
            <input type="text" name="port_name" value="'+dSend.port_name+'">\
            <input type="text" name="ship" value="'+dSend.ship+'">\
            <input type="text" name="shift" value="'+dSend.shift+'">\
            <input type="text" name="shift_name" value="'+dSend.shift_name+'">\
            <input type="text" name="ship_name" value="'+dSend.ship_name+'">';

            $('#formDownload').attr('action','<?php echo $urlDownload_excel; ?>');
            $('#formDownload').html(addForm);
            $('#formDownload').submit();
            $('#formDownload input').remove();
        })
    })
</script>