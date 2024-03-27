 <link href="<?php echo base_url(); ?>assets/global/plugins/icheck/skins/all.css" rel="stylesheet" type="text/css" />

 <div class="col-md-4 col-md-offset-4">
    <div class="portlet box blue box-edit">
        <?php echo headerForm($title) ?>
        <div class="portlet-body">
            <div class="box-body">
               <div class="form-group">
                    <div class="row">
                        <div class="col-md-12 form-group">
                            <label>Nama Kapal</label>
                            <select class="form-control select2" data-placeholder="Pilih Kapal" id="ship" style="width: 100% !important">
                                <option></option>
                                <?php foreach($ship as $value) {?>
                                    <option value="<?php echo $this->enc->encode($value->id);?>"><?php echo strtoupper($value->name); ?></option>
                                <?php } ?>
                            </select>
                        </div>                    
                    </div>
                </div>
                <div class="form-group">
                    <div class="row">
                        <div class="col-md-12 form-group">
                            <label>Dermaga</label>
                            <select class="form-control select2" data-placeholder="Pilih Dermaga" id="dock" style="width: 100% !important">
                                <option></option>
                                <?php foreach($dock as $value) {?>
                                    <option value="<?php echo $this->enc->encode($value->id);?>"><?php echo strtoupper($value->name); ?></option>
                                <?php } ?>
                            </select>
                        </div>                    
                    </div>
                </div>
                <div class="actions">
                    <button href="javascript:;" class="btn blue btn-sm" id="set_probelm" type="button" data-loading-text="<i class='fa fa-spinner fa-spin'></i>" title="Proses...">Tambah</button> 
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    var client = new ClientJS(),    
        fingerprint = client.getFingerprint(), // Get Client's Fingerprint
      
        socket = io('<?php echo $socket_url ?>');

    $(document).ready(function(){
        $('.box-edit').block({
            message: '<h4><i class="fa fa-spinner fa-spin"></i> Menuggu</h4>',
        });

        $('.select2').select2();
        socket.on('status', function (data) {
            if(data.connected){
                $('.box-edit').unblock();
            }
        });

        $('#set_probelm').click(function(){
            var idProblem = '<?php echo $problem_id ?>',
                btn = $(this),
                ship_id = $("#ship").val(),
                ship_name = $("#ship option:selected").text(),
                dock_id = $("#dock").val();

            if(idProblem == '' || idProblem == 0){
                toastr.error('Harus pilih dulu tipe masalahnya.!', 'Gagal');
                $(this).button('reset');
            }else{
                alertify.confirm('Apakah yakin kapal mengalami masalah', function (e) {
                    if(e){
                        $.ajax({
                            url         : 'stc/set_problem2',
                            data        : {
                                type: idProblem,
                                ship_id: ship_id,
                                dock_id: dock_id
                            },
                            type        : 'POST',
                            dataType    : 'json',

                            beforeSend: function(){
                                btn.button('loading');
                            },

                            success: function(json) {
                                if(json.code == 1){
                                    closeModal();
                                    $("#myModal").modal('hide')
                                    toastr.success(json.message, 'Sukses');
                                    $('#searching').trigger('click');

                                }else{
                                    toastr.error(json.message, 'Gagal');
                                }
                            },

                            error: function() {
                                toastr.error('Silahkan Hubungi Administrator', 'Gagal');
                            },

                            complete: function(){
                                btn.button('reset');
                                return false;
                            }
                        });
                    }
                })
            }
        });
    });
</script>