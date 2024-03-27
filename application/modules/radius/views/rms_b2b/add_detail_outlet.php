
<link href="<?php echo base_url(); ?>assets/global/plugins/icheck/skins/all.css" rel="stylesheet" type="text/css" />
<link href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-tagsinput/bootstrap-tagsinput.css" rel="stylesheet" type="text/css" />

<style type="text/css">
    .wajib{color: red}
</style>
<div class="col-md-10 col-md-offset-1">
    <div class="portlet box blue" id="box">
        <?php echo headerForm($title) ?>
        <div class="portlet-body">
            <?php echo form_open('radius/rms_b2b/action_add_detail_outlet', 'id="ff" autocomplete="off"'); ?>
            <div class="box-body">
                 <div class="form-group">
                    <div class="row">

                        <div class="col-sm-12 form-group"  >   
                            <input type="hidden" id="idData" name="idData" value="1">
                            <input type="hidden" id="rmsCode" name="rmsCode" value="<?= $rmsCode ; ?>">                         
                            <div id="inputImpactUserDiv"></div>
                        </div>   
                        <?php echo form_close(); ?>        

                        <div class="col-sm-6 form-group" id='selectUser' >
                            
                            <div class="portlet box blue-madison">
                                <div class="portlet-title">
                                    
                                    <div class="caption">Outlet dibatasi</div>
                                    <div class="pull-right btn-add-padding"></div>
                                </div>
                                <div class="portlet-body">                        
                                    <table class="table" id="tableUserLimited">
                                        <thead >
                                            <tr>
                                                <th>MERCHANT</th>
                                                <th>OUTLET ID</th>
                                                <th>
                                                    <div class='btn btn-danger transferData pull-right' title='Pindah Ke Pengecualian'  id="toAllExcept" >
                                                        Semua Data <i class='fa fa-arrow-right ' aria-hidden='true'></i>
                                                    </div>
                                                    <!-- AKSI -->
                                                </th>
                                            </tr>
                                        </thead>

                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6 form-group"  >
                            <div class="portlet box blue-madison">
                                <div class="portlet-title">
                                    
                                    <div class="caption">Outlet pengecualian</div>
                                    <div class="pull-right btn-add-padding"></div>
                                </div>
                                <div class="portlet-body">                        
                                    <table class="table" id="tableUserLimitedExcept">
                                        <thead id="headerPengecualian" >                                                                      
                                            <tr>
                                                <!-- AKSI -->
                                                <th>
                                                    <div class='btn btn-danger transferData pull-left' title='Pindah Ke Pengecualian'  id="toAllLimit" >
                                                    <i class='fa fa-arrow-left ' aria-hidden='true'></i> Semua Data
                                                    </div>

                                                </th>
                                                <th>OUTLET ID</th>
                                                <th>MERCHANT</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                        </tbody>
                                    </table>
                                </div>
                                
                            </div>
                        </div>                            

                    </div>
                </div>
            </div>
            <?php echo createBtnForm('Simpan'); ?>
            <?php echo form_close(); ?> 
        </div>
    </div>
</div>
<script src="<?php echo base_url() ?>assets/js/jquery-easyui-1.5.3/jquery.easyui.min.js"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-tagsinput/bootstrap-tagsinput.js"></script>
<script type="text/javascript">
    arrayUserIdImpact=[];
    const rsmCode = `<?= $this->enc->decode($rmsCode) ; ?>`
    $(document).ready(function(){
        validateForm('#ff',function(url,data){
            postData2(url,data);
        });

        $(`#saveBtn`).on("click", function(event){
            const isTrue = $('#ff').valid();
            if(isTrue == true)
            {
                $('#ff').submit();
            }
        })          

    function postData2(url, data, y) {
        $.ajax({
            url: url,
            data: data,
            type: 'POST',
            dataType: 'json',

            beforeSend: function () {
                unBlockUiId('box')
            },

            success: function (json) {
                
                $("input[name=" + json.csrfName + "]").val(json.tokenHash);
                // console.log(json)
                let csfrData = {};
                csfrData[json.csrfName] = json.tokenHash;
                $.ajaxSetup({
                        data: csfrData,
                });
                if (json.code == 1) {
                    // unblockID('#form_edit');				
                    closeModal();
                    

                    toastr.success(json.message, 'Sukses');
                    if (y) {
                        $('#grid').treegrid('reload');
                        // ambil_data();
                    }
                    else {
                        $(`#detailDataTables_${rsmCode}`).DataTable().ajax.reload(null, false);
                        $(`#detailDataTables4_${rsmCode}`).DataTable().ajax.reload(null, false);
                        // ambil_data();
                    }

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


        $('.select2:not(.normal)').each(function () {
            $(this).select2({
                dropdownParent: $(this).parent()
            });
        });

        myData.tableUserLimited();
        myData.tableUserExcept();

        $("#toAllExcept").on("click", function(){
            arrayUserIdImpact=[];
            $("#idData").val(1)

            myData.reloadTableUserLimited(); 
            myData.reloadTableUserExcept(); 

            $(`#inputImpactUserDiv`).html("");
            

        })   
        
        $("#toAllLimit").on("click", function(){
            arrayUserIdImpact=[];
            $("#idData").val(0)

            myData.reloadTableUserLimited(); 
            myData.reloadTableUserExcept(); 

            $(`#inputImpactUserDiv`).html("");
        }) 
        
        
    })
</script>