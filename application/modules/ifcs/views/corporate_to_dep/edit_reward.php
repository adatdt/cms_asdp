
<link href="<?php echo base_url(); ?>assets/global/plugins/icheck/skins/all.css" rel="stylesheet" type="text/css" />

<style type="text/css">
    .wajib{color: red}
</style>
<div class="col-md-6 col-md-offset-3">
    <div class="portlet box blue" id="box">
        <?php echo headerForm($title) ?>
        <div class="portlet-body">
            <?php echo form_open('ifcs/corporate/action_edit_reward', 'id="ff" autocomplete="off"'); ?>
            <div class="box-body">
                 <div class="form-group">
                    <div class="row">

                        <div class="col-sm-6 form-group">
                            <label>Nama Corporate <span class="wajib">*</span></label>
                            <input type="text" name="name" class="form-control"  placeholder="Nama Corporate" required value="<?php echo $detail->corporate_name ?>" readonly >
                            <input type="hidden" name="id" value="<?php echo $id; ?>" >
                        </div>

                        <div class="col-sm-6 form-group">
                            <label>Reward Code <span class="wajib">*</span></label>
                            <input type="text" name="contract_number" class="form-control"  placeholder="Nomer Kontrak " required value="<?php echo $detail->reward_code ?>" readonly>

                        </div>                        
                        <div class="col-sm-12 "></div>

                        <div class="col-sm-6 form-group">
                            <label>Nomer Kontrak <span class="wajib">*</span></label>
                            <input type="text" name="contract_number" class="form-control"  placeholder="Nomer Kontrak " required value="<?php echo $detail->contract_number ?>" readonly>

                        </div>


                        <div class="col-sm-6 form-group">
                            <label>Priode Awal Reward  digunakan<span class="wajib">*</span></label>
                            <input type="text" name="start_date" class="form-control"  placeholder="Priode Awal Reward" required value="<?php echo $detail->start_date_reward ?>" readonly>
                        </div>
                        <div class="col-sm-12 "></div>

                        <div class="col-sm-6 form-group">
                            <label>Priode Akhir Reward digunakan<span class="wajib">*</span></label>
                            <input type="text" name="end_date" class="form-control"  placeholder="Priode Akhir Reward" required value="<?php echo $detail->end_date_reward ?>" readonly >
                        </div>


                        <div class="col-sm-6 form-group">
                            <label>Reward saat ini <span class="wajib">*</span></label>
                            <input type="text" name="current_reward" class="form-control"  placeholder="Reward saat ini" required value="<?php echo $detail->total_reward ?>"  readonly>
                        </div>
                        <div class="col-sm-12 "></div>

                        <div class="col-sm-6 form-group">
                            <label>Total Reward Tambahan/ Kurang <span class="wajib">*</span></label>
                            <input type="text" name="add_reward" class="form-control"  placeholder="Total Reward Tambahan/ Kurang" onkeypress="return isNumberKey(event)" required>
                        </div>

                        <div class="col-sm-6 form-group">
                            <label>Aksi<span class="wajib">*</span></label>
                            <?php echo form_dropdown("action_select",$action,""," class='form-control select2' required ") ?>
                        </div>

                        <div class="col-sm-12"></div>

                        <div class="col-sm-6 form-group">
                            <label>Estimasi <span class="wajib">*</span></label>
                            <input type="text" name="estimation" class="form-control"  placeholder="Estimasi" required value="0" onkeypress="return isNumberKey(event)" readonly>
                        </div>                                                       

                    </div>
                </div>
            </div>
            <?php echo createBtnForm('Edit'); ?>
            <?php echo form_close(); ?> 
        </div>
    </div>
</div>

<!-- <?php include "fileJs.php" ?> -->

<script src="<?php echo base_url() ?>assets/js/jquery-easyui-1.5.3/jquery.easyui.min.js"></script>
<script type="text/javascript">
    $(document).ready(function(){

        rules   = {email: "required email"};
        messages= {email: "Format email tidak valid"};

        validateForm('#ff',function(url,data){
            postData2(url,data);
        });

        $('.select2:not(.normal)').each(function () {
            $(this).select2({
                dropdownParent: $(this).parent()
            });
        });


        $("[name='action_select']").change(function(){

            var add_reward =$("[name='add_reward']").val(); 
            var current_reward =$("[name='current_reward']").val();

            a = current_reward==""?0:parseInt(current_reward);
            b = add_reward==""?0:parseInt(add_reward); 

            if ($("[name='action_select']").val()==1)
            {
                $("[name='estimation']").val(parseInt(a)+parseInt(b))
            }
            else if($("[name='action_select']").val()==2)
            {
                $("[name='estimation']").val(parseInt(a)-parseInt(b));
            }
            else
            {
                $("[name='estimation']").val(0);
            }

        });

        $("[name='add_reward']").keyup(function(){

            
            var add_reward =$(this).val(); 
            var current_reward =$("[name='current_reward']").val();

            a = current_reward==""?0:parseInt(current_reward);
            b = add_reward==""?0:parseInt(add_reward); 

            b==""?0:b;

            if ($("[name='action_select']").val()==1)
            {
                $("[name='estimation']").val(parseInt(a)+parseInt(b))
            }
            else if($("[name='action_select']").val()==2)
            {
                $("[name='estimation']").val(parseInt(a)-parseInt(b));
            }
            else
            {
                $("[name='estimation']").val(0);
            }


        })

    function postData2(url,data){
        $.ajax({
            url         : url,
            data        : data,
            type        : 'POST',
            dataType    : 'json',

            beforeSend: function(){
                unBlockUiId('box')
            },

            success: function(json) {
                if(json.code == 1)
                {
                    closeModal();
                    toastr.success(json.message, 'Sukses');
                    $('#t_reward').DataTable().ajax.reload( null, false );

                }
                else
                {
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




    })
</script>