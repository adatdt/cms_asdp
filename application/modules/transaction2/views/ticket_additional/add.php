
<style type="text/css">
    .wajib{color: red}

    .my_border{
        border: 1px solid #e7ecf1;
        padding: 20px;
        /*margin: 10px 30px;*/
        min-height: 400px;
        -webkit-box-shadow: 11px 9px 14px -4px rgba(183,185,189,1);
        -moz-box-shadow: 11px 9px 14px -4px rgba(183,185,189,1);
        box-shadow: 11px 9px 14px -4px rgba(183,185,189,1);
    }    
    .scrolling{

        height: 320px;
        overflow-y: auto;
        overflow-x: hidden;
    } 

</style>
<div class="col-md-10 col-md-offset-1">
    <div class="portlet box blue" id="box">
        <?php echo headerForm($title) ?>
        <div class="portlet-body">
            <div class="row">
                <div class="col-md-12">
                        <!-- BEGIN EXAMPLE TABLE PORTLET-->
                       
                    <div class="portlet-body">

                        <div class="row">
                            <div class="col-sm-6 ">

                                 <!--  <?php echo date('Y-m-d',strtotime('2019-03-12 00:00:00')) ?> -->

                                <div class="input-group select2-bootstrap-prepend">
                                    <div class="input-group-addon">Nomer Tiket :</div>
                                        <input type="text" name="search" class="form-control" placeholder="Nomer Tiket" id="search">
                                    <span class="input-group-btn">
                                        <button class="btn btn-primary" id="cari"> cari</button>  
                                    </span>
                                </div>  
                                <font style="font-size:12px; color:red; font-style: italic ">Jika tiket kendaraan, harus diinpukan nomer tiket kendaraanya *</font>

                                
                            </div>

                            <div class="col-sm-6 ">

                            </div>

                            <div class="col-sm-12 "></div>
                            <div class="col-sm-12 "></div>
                            <div class="col-sm-12 "></div>
                            <div class="col-sm-3 " id="status"></div>

                        </div>

                    </div>
                </div>
                        <!-- END EXAMPLE TABLE PORTLET-->
            </div>
            <p></p>
            <?php echo form_open('transaction/refund_force_majeure/action_add', 'id="ff" autocomplete="off"'); ?>
            <div class="box-body">
                 <div class="form-group">

                    <div class="row" id="myDetail"></div>                    
                </div>

            </div>
            <?php echo form_close(); ?> 


        </div>
    </div>
</div>



<script src="<?php echo base_url() ?>assets/js/jquery-easyui-1.5.3/jquery.easyui.min.js"></script>
<script type="text/javascript">

    $(document).ready(function(){
        validateForm('#ff',function(url,data){
            postData(url,data);
        });


        $('.select2:not(.normal)').each(function () {
            $(this).select2({
                dropdownParent: $(this).parent()
            });
        });
    })
</script>

<?php include "fileJs.php"; ?>