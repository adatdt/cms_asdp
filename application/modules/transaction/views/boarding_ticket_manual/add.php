<style type="text/css">
    .wajib{ color:red; }

    .mt-checkbox-list, .mt-radio-list {
        padding: 10px 0px;
        margin-left: 10px;
        text-align: center;
    }

    .my_border{
        border: 1px solid #e7ecf1;
        padding: 20px;
        /*margin: 10px 30px;*/
        min-height: 500px;
        -webkit-box-shadow: 11px 9px 14px -4px rgba(183,185,189,1);
        -moz-box-shadow: 11px 9px 14px -4px rgba(183,185,189,1);
        box-shadow: 11px 9px 14px -4px rgba(183,185,189,1);
    }

</style>
<div class="col-md-12 col-md-offset-0">
    <div class="portlet box blue" id="box">
        <?php echo headerForm($title) ?>
        <div class="portlet-body">
            <div class="box-body">
                <div class="form-group">
                    <div class="row">

                         <div class="col-sm-12 form-inline">
                            <div class="input-group select2-bootstrap-prepend ">
                                <div class="input-group-addon">Jenis PJ</div>
                                <?php echo form_dropdown("service",$service,""," class='form-control select2' required ") ?>
                            </div>                            


                            <div class="input-group select2-bootstrap-prepend">
                                <div class="input-group-addon">Layanan</div>
                                <?php echo form_dropdown("search_ship_class",$ship_class,"","class='form-control select2' required") ?>                                
                            </div>                            
                                         


                            <div class="input-group select2-bootstrap-prepend">
                                <div class="input-group-addon">Tanggal Transaksi</div>
                                <input type="text" name="trx_date" placeholder="YYYY-MM-DD" class="form-control date" readonly value="<?php echo date('Y-m-d')?>">
                                <div class="input-group-addon">S/d</div>
                                <input type="text" name="trx_date2" placeholder="YYYY-MM-DD" class="form-control date" readonly value="<?php echo date('Y-m-d')?>">
                            </div>                            

                            <div class="input-group select2-bootstrap-prepend">
                                <div class="input-group-addon">pelabuhan</div>
                                <?php echo form_dropdown("port",$port,"","class='form-control select2' required id='portHeader' ") ?>
                                <span class="input-group-btn">
                                    <button class="btn btn-primary" type="button" id="cari">Cari</button>
                                </span>
                            </div>                            
                        </div>

                    </div>
                </div>

                <div class="form-group">
                    <div class="row">
                        <div class="col-sm-12 " id="list"></div>
                    </div>
                </div>                

                <?php echo form_open('transaction/boarding_ticket_manual/action_add', 'id="ff" autocomplete="on"'); ?>
                <div class="form-group">
                    <div class="row">
                        <div class="col-sm-12 " id="schedule"></div>
                    </div>
                </div>                                

                <div class="form-group">
                    <div class="row">
                        <div class="col-sm-12 " id="list_temp"></div>
                    </div>
                </div>                                                    
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">

    var no=0;

    $(document).ready(function(){
        validateForm('#ff',function(url,data){
            postData(url,data);
        });


        $('.select2:not(.normal)').each(function () {
            $(this).select2({
                dropdownParent: $(this).parent()
            });
        });

        $("#cari").click(()=>{
            getData.dataTicket();
        })

        $('.date').datepicker({
            format: 'yyyy-mm-dd',
            changeMonth: true,
            changeYear: true,
            autoclose: true,
            endDate: new Date(),
            startDate: '<?= $startDate ?>'
        }); 

        function postData(url,data){
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

                        closeModal();
                        toastr.success(json.message, 'Sukses');

                        $('#dataTables').DataTable().ajax.reload( null, false );
                        $('#dataTables2').DataTable().ajax.reload( null, false );
                        
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

<?php include "fileJs.php"?>
