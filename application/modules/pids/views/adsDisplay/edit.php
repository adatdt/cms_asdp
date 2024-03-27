<link href="<?php echo base_url(); ?>assets/global/plugins/icheck/skins/all.css" rel="stylesheet" type="text/css" />

<style type="text/css">
    .wajib {
        color: red
    }

    .scrolling {

        max-height: 300px;
        overflow-y: auto;
    }
</style>
<div class="col-md-6 col-md-offset-3">
    <div class="portlet box blue" id="box">
        <?php echo headerForm($title) ?>
        <div class="portlet-body">
            <?php echo form_open('pids/adsDisplay/action_edit', 'id="ff" autocomplete="off"'); ?>
            <div class="box-body">
                <div class="form-group">
                    <div class="row">

                        <div class="col-sm-6 form-group">
                            <div class="form-group">
                                <label>Kode ADS <span class="wajib">*</span></label>
                                <input type="text" name="adsCode" class="form-control" placeholder="Kode ADS" required value="<?= $detail->ads_display_code ?>" readonly>
                                <input type="hidden" name="id" value="<?= $id ?>">
                            </div>

                        </div>

                        <div class="col-sm-6 form-group">
                            <div class="form-group">
                                <label>Pelabuhan <span class="wajib">*</span></label>
                                <?= form_dropdown("port", $port, $portSelected, ' class="form-control select2"  placeholder="Pelabuhan" required ') ?>
                            </div>

                        </div>
                        <div class="col-sm-12 "></div>

                        <div class="col-sm-6 form-group">
                            <div class="form-group">
                                <label>Nama<span class="wajib">*</span></label>
                                <input type="text" name="name" class="form-control" placeholder="Nama" required value="<?= $detail->ads_name ?>" readonly >
                            </div>
                        </div>


                        <div class="col-sm-6 form-group">
                            <div class="form-group">
                                <label>Durasi (Detik)<span class="wajib">*</span></label>
                                <input type="number" min="1" name="duration" class="form-control" placeholder="Durasi" required value="<?= $detail->duration ?>">
                            </div>
                        </div>

                        <div class="col-sm-12 "></div>
                        <div class="col-sm-6 form-group">
                            <div class="form-group">
                                <label>Urutan<span class="wajib">*</span></label>
                                <input type="number" name="ordering" class="form-control" placeholder="Urutan" required min=1 value="<?= $detail->order ?>">
                            </div>
                        </div>

                        <div class="col-sm-12 ">

                            <div class="btn btn-warning pull-right" id="addFile">Tambah File</div>

                        </div>
                        <!-- <div id="input_ticket"></div> -->
                        <div class="col-sm-12 scrolling">
                            <div class="col-md-12" id="fileInput"></div>
                            <div>



                            </div>
                        </div>
                    </div>
                    <?php echo createBtnForm('Simpan'); ?>
                    <?php echo form_close(); ?>
                </div>
            </div>
        </div>
        <script src="<?php echo base_url() ?>assets/js/jquery-easyui-1.5.3/jquery.easyui.min.js"></script>
        <script type="text/javascript">
            var indexData = 0;
            $(document).ready(function() {
                validateForm('#ff', function(url, data) {
                    postData2(url, data);
                });

                var dataParam = {
                    adsCode: "<?= $detail->ads_display_code ?>"
                }
                myData.getFileEdit(dataParam)



                $("#addFile").on("click", function() {
                    myData.addInputFile();
                    indexData++;
                });


                $('.select2:not(.normal)').each(function() {
                    $(this).select2({
                        dropdownParent: $(this).parent()
                    });
                });

                function postData2(url, data, y) {

                    form = $('form')[0];
                    formData = new FormData(form);

                    $.ajax({
                        url: url,
                        data: formData,
                        type: 'POST',
                        // enctype: 'multipart/form-data',
                        processData: false, // Important!
                        contentType: false,
                        cache: false,
                        dataType: 'json',

                        beforeSend: function() {
                            unBlockUiId('box')
                        },
                        success: function(json) {
                            if (json.code == 1) {
                                // unblockID('#form_edit');
                                closeModal();
                                toastr.success(json.message, 'Sukses');

                                $('#dataTables').DataTable().ajax.reload(null, false);
                                
                                socket.emit('pidsUpdateParams', parseInt(json.data['portId']));
                                // console.log(json.data)

                            } else {
                                toastr.error(json.message, 'Gagal');
                            }
                        },
                        error: function() {
                            toastr.error('Silahkan Hubungi Administrator', 'Gagal');
                        },

                        complete: function() {
                            $('#box').unblock();
                        }
                    });
                }
            })
        </script>