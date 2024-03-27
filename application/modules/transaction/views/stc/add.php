 <link href="<?php echo base_url(); ?>assets/global/plugins/icheck/skins/all.css" rel="stylesheet" type="text/css" />

<div class="col-md-6 col-md-offset-3">
    <div class="portlet box blue" id="box">
        <?php echo headerForm($title) ?>
        <div class="portlet-body">
            <?php echo form_open('transaction/opening_balance/action_add', 'id="ff" autocomplete="off"'); ?>
            <div class="box-body">
                 <div class="form-group">
                    <div class="row">

                        <div class="col-sm-4 form-group">
                            <label>Shift</label>
                            <select class="form-control select2" required name="shift" id="shift">
                                    <option value="">Pilih</option>
                                <?php foreach($shift as $key=>$value ) { ?>
                                    <option value="<?php echo $this->enc->encode($value->id) ?>"><?php echo strtoupper($value->shift_name); ?></option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="col-sm-4 form-group">
                            <label>Tanggal Transaksi</label>
                            <div class="input-group">
                                <input class="form-control  date" id="date" placeholder="yyyy-mm-dd" value="<?php echo date('Y-m-d'); ?>" required name="assignment_date">
                                <div class="input-group-addon"><i class="icon-calendar"></i></div>
                            </div> 
                        </div>

                        <div class="col-sm-4 form-group">
                            <label>Assignment_code</label>
                            <select class="form-control select2" required name="assignment" id="assignment">
                                <option value="">Pilih</option>
                                <?php foreach($assignment as $key=>$value ) { ?>
                                    <option value="<?php echo $value->assignment_code ?>"><?php echo strtoupper($value->assignment_code); ?></option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="col-sm-6 form-group">
                            <label>Pelabuhan</label>
                            <input type="text" class="form-control" required name="port" id="port" readonly>
                            <input type="hidden"  required name="port_id" id="port_id" readonly>
                        </div>

                        <div class="col-sm-6 form-group">
                            <label>Nama Regu</label>
                            <input type="text" class="form-control" required name="team" id="team" readonly>
                        </div>

                        <div class="col-sm-12" ></div>

                        <div class="col-sm-12 form-group" id="list_user"></div>
                    </div>
                </div>
            </div>
            <?php echo createBtnForm('Add'); ?>
            <?php echo form_close(); ?> 
        </div>
    </div>
</div>


<script src="<?php echo base_url() ?>assets/js/jquery-easyui-1.5.3/jquery.easyui.min.js"></script>
<script type="text/javascript">

    function assignment_code(){

        $("#date").on("changeDate",function(){

            var date=$("#date").val();
            $.ajax({
                type:"post",
                data:"date="+date,
                url:"<?php echo site_url()?>transaction/opening_balance/assignment_code",
                dataType:"json",
                success:function(x){

                    var isi="<option value=''>Pilih</option>";

                    for(var i=0; i<x.length; i++)
                    {
                        isi +="<option valu='"+x[i].assignment_code+"'>"+x[i].assignment_code+"</option>";   
                    }

                    $("#assignment").html(isi);
                    $("#list_user").html("");
                    $("#port").html("");
                    $("#team").html("");
                    // console.log(isi);   
                }
            });

        });
    }

    function user_list(){

        $("#assignment").on("change",function(){

            var assignment_code=$("#assignment").val();
            $.ajax({
                type:"post",
                data:"assignment_code="+assignment_code,
                url:"<?php echo site_url()?>transaction/opening_balance/user_list",
                dataType:"json",
                success:function(x){

                    var isi="<label>Pilih User</label>"+
                              "<table class='table table-striped'>"+
                              "<tbody>";

                    for(var i=0; i<x.length; i++)
                    {
                        isi +="<td> <div class='icheck-list'>"+
                               "<label><input type='checkbox' class='allow' data-checkbox='icheckbox_flat-grey' id='check_data["+i+"]' value='"+x[i].user_id+"' name='check_user["+i+"]'>"+ 
                               x[i].username+"</label></div></td>"+
                               "<td><input type='number' class='form-control' name='total_cash["+i+"]'  placeholder='Nominal Cash'  ></td>"+
                               "</tr>";   
                    }

                    isi +="</tbody></table>"
                    $("#list_user").html(isi);
                    $('.allow').iCheck({
                        checkboxClass: 'icheckbox_square-blue',
                        radioClass: 'icheckbox_square-blue',
                    });
                    // console.log(x);   
                }
            });

        });


    }

    function get_port (){

        $("#assignment").on("change",function(){

            var assignment_code=$("#assignment").val();
            $.ajax({
                type:"post",
                data:"assignment_code="+assignment_code,
                url:"<?php echo site_url()?>transaction/opening_balance/get_port",
                dataType:"json",
                success:function(x){

                    $("#port").val(x.port_name);
                    $("#team").val(x.team_name);
                    $("#port_id").val(x.port_id);
                    // console.log(x);   
                }
            });

        });
    }

    $(document).ready(function(){
        validateForm('#ff',function(url,data){
            postData(url,data);
        });


        $('.select2:not(.normal)').each(function () {
            $(this).select2({
                dropdownParent: $(this).parent()
            });
        });

        $('#date').datepicker({
            format: 'yyyy-mm-dd',
            changeMonth: true,
            changeYear: true,
            autoclose: true,
        });

        $('.allow').iCheck({
            checkboxClass: 'icheckbox_square-blue',
            radioClass: 'icheckbox_square-blue',
        });


        assignment_code();

        user_list();
        get_port();



    })
</script>