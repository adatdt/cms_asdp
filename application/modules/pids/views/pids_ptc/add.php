 <link href="<?php echo base_url(); ?>assets/global/plugins/icheck/skins/all.css" rel="stylesheet" type="text/css" />

<div class="col-md-6 col-md-offset-3">
    <div class="portlet box blue" id="box">
        <?php echo headerForm($title) ?>
        <div class="portlet-body">
            <?php echo form_open('pids/pids_ptc/action_add', 'id="ff" autocomplete="off"'); ?>
            <div class="box-body">
                 <div class="form-group">
                    <div class="row">

                        <div class="col-md-6">
                            <div class="form-group ">
                                <label for="form_control_1">Pelabuhan
                                    <span class="required" aria-required="true">*</span>
                                </label>
                                <?php echo form_dropdown("port",$port,"",' class="form-control select2"  id="port" required ') ?>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group ">
                                <label for="form_control_1">Dermaga
                                    <span class="required" aria-required="true">*</span>
                                </label>
                                <?php echo form_dropdown("dock",$dock,"",' class="form-control select2"  id="dock" required ') ?>
                            </div>
                        </div>    

                        <div class="col-md-6">
                            <div class="form-group ">
                                <label for="form_control_1">Tanggal
                                    <span class="required" aria-required="true">*</span>
                                </label>
                                <input type="text" class="form-control date " name="date" id="date" placeholder="YYYY-MM-DD" required readonly value="<?php echo date("Y-m-d") ?>">
                            </div>
                        </div>    

                        <div class="col-md-6">
                            <div class="form-group ">
                                <label>Nama Kapal
                                    <span class="required" aria-required="true">*</span>
                                </label>
                                <?php echo form_dropdown("ship_pairing",$ship_pairing,"",' class="form-control select2"  id="ship_pairing" required ') ?>
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
<script type="text/javascript">


    function getData()
    {
        $.ajax({
            type : "post",
            dataType : "json",
            url :"<?php echo site_url()?>pids/pids_ptc/get_data",
            data:"port="+$("[name='port']").val(),
            beforeSend : function ()
            {
                unBlockUiId('box')            
            },
            success : function (x)
            {

                var dock=x.dock;
                var ship_pairing=x.ship_pairing;

                console.log(ship_pairing);

                var selectDock ="<option value=''>Pilih</option>";
                var selectShipPairing ="<option value=''>Pilih</option>";

                
                if(dock.length>0)
                {
                    for(var i=0; i<dock.length; i ++)
                    {
                        selectDock +="<option value='"+dock[i].id+"'>"+dock[i].name+"</option>"
                    }

                }

                if(ship_pairing.length>0)
                {
                    for(var j=0; j<ship_pairing.length; j ++)
                    {
                        selectShipPairing +="<option value='"+ship_pairing[j].id+"'>"+ship_pairing[j].name+"</option>"
                    }

                }                

                $("[name='dock']").html(selectDock);
                $("[name='ship_pairing']").html(selectShipPairing);

            },
            complete: function(){
                $('#box').unblock(); 
            }
        });
    }


    $(document).ready(function(){
        validateForm('#ff',function(url,data){
            postData2(url,data);
        });


        $('.select2:not(.normal)').each(function () {
            $(this).select2({
                dropdownParent: $(this).parent()
            });
        });

        $("[name='port']").change(function(){
            getData();
        });

        $('#date').datepicker({
            format: 'yyyy-mm-dd',
            changeMonth: true,
            changeYear: true,
            autoclose: true,
            todayHighlight:true,
        });

        function postData2(url,data,y){
            $.ajax({
                url         : url,
                data        : data,
                type        : 'POST',
                dataType    : 'json',

                beforeSend: function(){
                    unBlockUiId('box')
                },

                success: function(json) {
                    if(json.code == 1){
                        // unblockID('#form_edit');
                        closeModal();
                        toastr.success(json.message, 'Sukses');
                        listDermaga();
                        // $('#dataTables').DataTable().ajax.reload( null, false );

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