<style type="text/css">
    .table>tbody>tr>td, .table>tbody>tr>th, .table>tfoot>tr>td, .table>tfoot>tr>th, .table>thead>tr>td, .table>thead>tr>th {
        padding: 1px 8px 1px 8px;
        line-height: 1.42857;
        vertical-align: top;
        border-top: 1px solid #e7ecf1;
    }

    .tr-detail {
        background:#ff9c00; 
        color:#FFFFFF;
    }

    .table {
        width: 100%;
        margin-bottom: 0px;
    }

    .input-group-sm>.form-control, .input-group-sm>.input-group-addon, .input-group-sm>.input-group-btn>.btn, .input-sm {
        height: 21px;
        padding: 5px 10px;
        font-size: 12px;
        line-height: 1.5;
        border-radius: 3px;
    }

    .mt-checkbox, .mt-radio {
        display: inline-block;
        position: relative;
        padding-left: 30px;
        margin-bottom: 0px;
        cursor: pointer;
        font-size: 14px;
        webkit-transition: all .3s;
        -moz-transition: all .3s;
        -ms-transition: all .3s;
        -o-transition: all .3s;
        transition: all .3s;
    }

    .span-border{
      border: 1px solid #000 !important;
    }

    .mt-checkbox>span:after {
        left: 6px;
        top: 3px;
        width: 5px;
        height: 10px;
        border: solid #2e6da4;
        border-width: 0 2px 2px 0;
        transform: rotate(45deg);
    }
</style>
<div class="col-md-8 col-md-offset-2">
    <div class="portlet box blue" id="box">
        <div class="portlet-body">
        <?php echo form_open('schedule/action_add', 'id="ff" autocomplete="on"'); ?>
            <div class="row">
                <div class="col-lg-8">
                    <table class="table table-striped">
                        <thead>
                            <tr class="tr-detail">
                                <td colspan="2">Keberangkatan</td>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- <tr class="warning">
                                <td>Nama kapal</td>
                                <td></td>
                            </tr> -->
                        
                            <tr class="success">
                                <td>Pelabuhan Asal</td>
                                <td class="td-schedule"><?php echo form_dropdown('origin', $origin, '', 'class="form-control input-sm" data-placeholder="Pilih Pelabuhan Asal" id="origin_port" style="width: 100%" required'); ?></td>
                            </tr>
                            <tr class="success">
                                <td>Pelabuhan Tujuan</td>
                                <td class="td-schedule"><select class="form-control input-sm" name="destination" data-placeholder="Pilih Pelabuhan Tujuan" id="destination_port" style="width: 100%" required></select>
                            </tr>
                            
                            <!-- <tr class="warning">
                                <td>Kapasitas Kendaraan</td>
                                <td>/td>
                            </tr>
                            <tr class="warning">
                                <td>Kapasitas penumpang</td>
                                <td></td>
                            </tr> -->
                        </tbody>
                    </table>

                    <table class="table table-striped">
                        <thead >
                            <tr class="tr-detail">
                                <td colspan="2">Tarif Kendaraan</td>
                            </tr>
                            <tr class="warning">
                                <th>Golongan</th>
                                <th class="td-schedule">Tarif (Rp)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($vehicle_class as $row) { ?>
                            <tr class="success">
                                <td><?php echo $row->name ?></td>
                                <td align="right"><input type="text" class="form-control input-sm text-right rp" name="vehicle[<?php echo $row->id; ?>]" placeholder="<?php echo $row->name ?>" required value="0" required></td>
                            </tr>
                            <?php  } ?>
                        </tbody>
                    </table>

                    <table class="table table-striped">
                        <thead>
                            <tr class="tr-detail">
                                <td colspan="2">Tarif Perorangan</td>
                            </tr>
                            <tr class="warning">
                                <th>Golongan</th>
                                <th class="td-schedule">Tarif (Rp)</th>
                            </tr>
                        </thead> 
                        <tbody>
                            <?php foreach($passenger as $row) { ?>
                            <tr class="success">
                                <td><?php echo $row->name ?></td>
                                <td align="right"><input type="text" class="form-control input-sm text-right rp" name="<?php echo strtolower($row->name) ?>" placeholder="<?php echo $row->name ?>" required value="0" required></td>
                            </tr>
                            <?php  } ?>
                        </tbody>
                    </table>
                    <p style="color: red">*Untuk waktu keberangkatan jika ada silahkan cek waktu yang sesuai dengan jadwal</p>
                </div>
                <div class="col-lg-4">
                    <table class="table table-striped">
                        <tr style="background:#ff9c00; color:#FFFFFF;">
                            <td align="center"> 
                                <label class="mt-checkbox mt-checkbox-outline"> &ensp;
                                    <input type="checkbox" id="checkAll">
                                    <span class="span-border"></span>
                                </label></td>
                            <td align="center">Waktu Keberangkatan</td>
                        </tr>
                        <?php for ($i=0; $i<=23; $i++){ 
                            $val = $i < 10 ? "0$i:00:00" : "$i:00:00";
                            ?>
                            <tr class="success">
                                <td align="center">
                                    <label class="mt-checkbox mt-checkbox-outline"> &ensp;
                                        <input type="checkbox" value="<?php echo $val ?>" name="hours[]">
                                        <span class="span-border"></span>
                                    </label>
                                </td>
                                <td align="center">
                                    Pukul : <?php echo $val ?>
                                </td>
                            <tr>
                        <?php } ?>
                    </table>
                </div>
            </div>
            <div class="box-footer text-right">
                <button type="button" class="btn btn-sm btn-default" onclick="closeModal()"><i class="fa fa-close"></i> Batal</button> <button type="submit" class="btn btn-sm btn-primary" id="saveBtn"><i class="fa fa-check"></i> Simpan</button>
            </div>
            <?php echo form_close(); ?>            
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        $('.mfp-wrap').removeAttr('tabindex')
        $('#origin_port').select2()
        $('#destination_port').select2()

        $('#origin_port').change(function(){
            $(this).valid();
            val = $(this).val();
            $.ajax({
                url         : 'schedule/port_destination',
                data        : {id : val},
                type        : 'POST',
                dataType    : 'json',

                beforeSend: function(){},

                success: function(json) {
                    $("#destination_port").html('');
                    $('#destination_port').select2({
                        data: json.data
                    })

                    $('#destination_port').change(function(){
                        $(this).valid();
                    })

                    // $('.select2').removeClass('select2-container--bootstrap')
                    // $('.select2').addClass('select2-container--default')
                },

                error: function() {
                  toastr.error('Silahkan Hubungi Administrator', 'Gagal');
                },

                complete: function(){}
            });
        })

        $("#checkAll").click(function(){
            $('input:checkbox').not(this).prop('checked', this.checked);
        });

        $('.rp').keyup(function(e){
            this.value = formatRupiah(this.value);
        })

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
                if (element.parents('div').hasClass('has-feedback')) {
                    error.appendTo( element.parent() );
                }

                else if (element.parents('div').hasClass('has-feedback') || element.hasClass('select2-hidden-accessible')) {
                    error.appendTo( element.parent() );
                }

                else {
                    error.insertAfter(element);
                }
            },

            submitHandler: function(form) {
                $.ajax({
                    url         : form.action,
                    data        : $(form).serialize(),
                    type        : 'POST',
                    dataType    : 'json',

                    beforeSend: function(){
                        unBlockUiId('box')
                    },

                    success: function(json) {
                        if(json.code == 1){
                            closeModal();
                            toastr.success(json.message, 'Sukses');
                            $('#dataTables').DataTable().ajax.reload();
                        }else{
                            toastr.error(json.message, 'Gagal');
                        }
                    },

                    error: function() {
                        toastr.error('Silahkan Hubungi Administrator', 'Gagal');
                    },

                    complete: function(){
                        $('#box').unblock(); 
                    }
                });
            }
        });
    })
</script>