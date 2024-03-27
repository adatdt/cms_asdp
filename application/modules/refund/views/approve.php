<style type="text/css">
    .wajib{color: red}
    .select2-container {
        width: unset !important;
    }
</style>

<div class="col-md-4 col-md-offset-4">
    <div class="portlet box blue" id="box">
        <?php echo headerForm($title) ?>
        <div class="portlet-body">
            <?php echo form_open('refund/actionApprove', 'id="ff" autocomplete="on"'); ?>
            <div class="box-body">
                <div class="form-group">
                    <div class="row">
                        <div class="col-sm-12" id="upload-bukti">
                            <label>Bukti Nodin<span class="wajib">*</span></label>
                            <input type="file" name="buktiNodin" id="buktiNodin" class="form-control" placeholder="Bukti Nodin" required>
                            <label for="buktiNodin">File harus bertipe pdf/excel</label>
                        </div>
                        <?php for ($i = 0; $i < count($id); $i++) { ?>
                            <input type="hidden" value="<?php echo $id[$i] ?>" name="id[]">
                        <?php } ?>
                    </div>

                </div>                                                                

            </div>
            <?php echo createBtnForm('Approve') ?>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        // validateForm('#ff',function(url,data){
        //     // postData(url,data);
        //     $.ajax({
        //         url: url,
        //         data: new FormData($('form')[0]),
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
        // });


        $("#ff")
            .submit(function (event) {
                event.preventDefault();
            })
            .validate({
                lang: "id",
                errorElement: "span",
                rules: {
                    buktiNodin: {
                        required: true,
                        accept: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel, application/pdf',
                        // filesize: max_file,
                    },
                },
                messages: {
                    buktiNodin: {
                        required: "Tidak boleh kosong",
                        accept: 'File harus berformat excel (xlsx, xls) atau pdf'
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