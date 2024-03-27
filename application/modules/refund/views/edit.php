<style type="text/css">
    .wajib{color: red}
    .select2-container {
        width: unset !important;
    }
</style>

<div class="col-md-8 col-md-offset-2">
    <div class="portlet box blue" id="box">
        <?php echo headerForm($title) ?>
        <div class="portlet-body">
            <?php echo form_open('refund/refund/action_edit', 'id="ff" autocomplete="on"'); ?>
            <div class="box-body">
                <div class="form-group">
                    <div class="row">

                        <div class="col-sm-6">
                            <label>Nomer Booking <span class="wajib">*</span></label>
                            <input type="text" name="bookingCode" class="form-control" placeholder="Nomer Booking" required value="<?php echo $detail->booking_code ?>" readonly>
                            <input type="hidden" value="<?php echo $id ?>" name="id">
                        </div>

                        <div class="col-sm-6">
                            <label>Nomer Refund <span class="wajib">*</span></label>
                            <input type="text" name="refundCode" class="form-control" placeholder="Nomer Refund" required value="<?php echo $detail->refund_code ?>" readonly>
                        </div>
                    </div>

                </div>

                <div class="form-group">
                    <div class="row">

                        <div class="col-sm-6">
                            <label>Nama <span class="wajib">*</span></label>
                            <input type="text" name="name" class="form-control" placeholder="Nama" required value="<?php echo $detail->name ?>" readonly>
                        </div>

                        <div class="col-sm-6">
                            <label>Nomer Rekening<span class="wajib">*</span></label>
                            <input type="text" name="accountNumber" class="form-control " placeholder="Nomer Rekening" required value="<?php echo $detail->account_number ?>" readonly>
                        </div>
                    </div>

                </div>

                <div class="form-group">
                    <div class="row">

                        <div class="col-sm-6">
                            <label>Nama Bank<span class="wajib">*</span></label>
                            <input type="text" name="bankName" class="form-control angka" placeholder="Nama Bank" required value="<?php echo $detail->bank ?>" readonly>
                        </div>

                        <div class="col-sm-6">
                            <label>Status Transfer<span class="wajib">*</span></label>
                            <?php echo form_dropdown("transfer",$transfer,$selectedTransfer,' class="form-control select2" placeholder="Status Transfer" id="status-transfer" required ') ?>
                        </div>                        
                    </div>

                </div>   

                <div class="form-group">
                    <div class="row">

                        <div class="col-sm-6">
                            <label>Keterangan<span class="wajib">*</span></label>
                            <input type="text" name="transferDescription" class="form-control " placeholder="Keterangan" required value="<?php echo $detail->transfer_description ?>">
                        </div>

                        <div class="col-sm-6 upload-bukti hidden" id="upload-bukti">
                            <label>Bukti Transfer<span class="wajib">*</span></label>
                            <input type="file" name="buktiTransfer" id="buktiTransfer" class="form-control bukti-transfer" data-max="<?php echo $max_size ?>" placeholder="Bukti Transfer">
                            <label for="buktiTransfer">File harus berupa Gambar atau PDF</label>
                        </div>
        
                    </div>

                </div>                                                                

            </div>
            <?php echo createBtnForm('Edit') ?>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        
        var max_file = $(".bukti-transfer").data('max');


        $('.bukti-transfer').change(function (e) {
            var img = e.target.files[0];
            var formData = new FormData();
            var url = '<?php echo base_url(); ?>';
            new Compressor(img, {
                quality: 0.8,
                maxWidth: 1000,
                mimeType: 'image/jpeg',
                convertSize: max_file,
                success(result) {

                    formData.append('bukti-transfer', result, result.name);
                    formData.append('refund-code', '<?php echo $detail->refund_code ?>');
                    // formData.append('nama-file', result, result.id);
                    // console.log(formData);

                    fetch(`${url}refund/process2`, {
                    method: 'POST',
                    headers: new Headers({
                        'X-Requested-With': 'XMLHttpRequest'
                    }),
                    body: formData
                    })
                    .then(response => {
                        if (!response.ok) {
                        throw new Error(response.statusText)
                        }

                        return response.json()
                    })
                    .then((result) => {
                        if (result.code !== 1) {
                            Swal.fire({
                                type: 'error',
                                title: 'Error!',
                                text: result.message
                            })
                            $('#buktiTransfer').val("");
                        }
                    })
                    .catch(error => {
                        Swal.fire({
                            type: 'error',
                            title: 'Error!',
                            text: error
                        })
                        $('#buktiTransfer').val("");
                    })
                },
            });
        });

        
        $.validator.addMethod('filesize', function (value, element, param) {
            return this.optional(element) || (element.files[0].size <= param)
        }, function (size) {
            return "Maksimal ukuran file " + filesize(size);
        });

        // validateForm('#ff', postDataRefund(url, data));
        $("#ff")
            .submit(function (event) {
                event.preventDefault();
            })
            .validate({
                lang: "id",
                errorElement: "span",
                
                rules: {
                    buktiTransfer: {
                        // required: true,
                        accept: 'image/*|application/pdf',
                        filesize: 200000
                        // integer: true
                    },
                },
                messages: {
                    buktiTransfer: {
                        accept: "File harus berupa Gambar atau PDF!",
                        filesize: "Ukuran file harus kurang dari 200 KB!"

                    }
                },
                ignore: 'input[type=hidden], .select2-search__field',
                errorClass: 'validation-error-label',
                successClass: 'validation-valid-label',
                highlight: function (element, errorClass) {
                    $(element).addClass('val-error');
                },

                unhighlight: function (element, errorClass) {
                    $(element).removeClass('val-error');
                },

                errorPlacement: function (error, element) {
                    if (element.parents('div').hasClass('has-feedback')) {
                        error.appendTo(element.parent());
                    }

                    else if (element.parents('div').hasClass('has-feedback') || element.hasClass('select2-hidden-accessible')) {
                        error.appendTo(element.parent());
                    }

                    else {
                        error.insertAfter(element);
                    }
                },

                submitHandler: function (form) {
                    var formData = new FormData($('form')[0]);
                    // formData.delete('buktiTransfer');
                    $.ajax({
                        url: form.action,
                        data: formData,
                        type: 'POST',
                        dataType: 'json',
                        contentType: false,
                        processData: false,

                        beforeSend: function () {
                            unBlockUiId('box')
                        },

                        success: function (json) {
                            if (json.code == 1) {
                                // unblockID('#form_edit');
                                closeModal();
                                toastr.success(json.message, 'Sukses');
                                $('#dataTables').DataTable().ajax.reload(null, false);
                            } else {
                                toastr.error(json.message, 'Gagal');
                            }
                        },

                        error: function () {
                            toastr.error('Silahkan Hubungi Administrator', 'Gagal');
                        },

                        complete: function () {
                            $('#box').unblock();
                        }
                    });
                }
            });

        // function postDataRefund(url,data){
        //     // postData(url,data);
        //     var formData = new FormData($('form')[0]);
        //     formData.delete('buktiTransfer');
        //     $.ajax({
        //         url: url,
        //         data: formData,
        //         type: 'POST',
        //         dataType: 'json',
        //         contentType: false,
        //         processData: false,

        //         beforeSend: function () {
        //             unBlockUiId('box')
        //         },

        //         success: function (json) {
        //             if (json.code == 1) {
        //                 // unblockID('#form_edit');
        //                 closeModal();
        //                 toastr.success(json.message, 'Sukses');
        //                 $('#dataTables').DataTable().ajax.reload(null, false);
        //             } else {
        //                 toastr.error(json.message, 'Gagal');
        //             }
        //         },

        //         error: function () {
        //             toastr.error('Silahkan Hubungi Administrator', 'Gagal');
        //         },

        //         complete: function () {
        //             $('#box').unblock();
        //         }
        //     });
        // }

        $('.select2:not(.normal)').each(function () {
            $(this).select2({
                dropdownParent: $(this).parent()
            });
        });
    });

    $('#status-transfer').on('select2:select', function(){
        // console.log($('#status-transfer option:selected').text());
        if ($('#status-transfer option:selected').text() == 'Transfer') {
            $('.upload-bukti').removeClass("hidden");
            $('#buktiTransfer').prop('required', true);
        }
        else {
            $('.upload-bukti').addClass("hidden");
            $('#buktiTransfer').prop('required', false);
        }
    })
</script>