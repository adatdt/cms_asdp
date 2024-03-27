<style type="text/css">
@media screen and (min-width: 768px){
    .form-horizontal .control-label {
        text-align: left; 
        margin-bottom: 0;
        padding-top: 7px;
    }
}

.table>tfoot>tr>th {
    border-bottom: 0;
    padding: 10px 10px;
    border-top: 1px solid #e7ecf1;
    font-weight: 600;
}

.row{
    margin-left: -15px;
    margin-right: 0px !important;
}

.my-div{
    margin: 5px -15px 0 0;
}
</style>

<div class="my-div">
    <div class="row">
        <div class="col-md-12 col-padding">
            <div class="portlet light bordered portlet-padding margin-bottom-0">
                <?php echo $title; ?>
                <div class="portlet-body">
                    <div class="row">
                        <div class="col-md-12 filter-trx">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <div class="input-group">
                                        <span class="input-group-addon">Date Type</span>
                                        <select class="form-control select2" id="date_type">
                                            <option value="1">Transaction Date</option>
                                            <option value="2">Settlement Date</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <div class="input-group date" id="start_date" data-date-format="yyyy-mm-dd" data-date-end-date="+0d">
                                        <span class="input-group-addon">Start Date</span>
                                        <input type="text" class="form-control" placeholder="Date" value="<?php echo date('Y-m-d') ?>" readonly id="start_date_input">
                                        <span class="input-group-btn">
                                            <button class="btn default" type="button" id="btn_start">
                                                <i class="fa fa-calendar"></i>
                                            </button>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <div class="input-group date" id="end_date" data-date-format="yyyy-mm-dd" data-date-end-date="+0d">
                                        <span class="input-group-addon">End Date</span>
                                        <input type="text" class="form-control" placeholder="Date" value="<?php echo date('Y-m-d') ?>" readonly id="end_date_input">
                                        <span class="input-group-btn">
                                            <button class="btn default" type="button" id="btn_end">
                                                <i class="fa fa-calendar"></i>
                                            </button>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 filter-trx">
                            <div class="col-md-3 hide" id="form-status">
                                <div class="form-group">
                                    <div class="input-group">
                                        <span class="input-group-addon">Status</span>
                                        <?php echo form_dropdown('', $dropdown_status, '', 'id="status" class="form-control select2"'); ?>
                                    </div>
                                </div>
                            </div>
                            <!-- <div class="col-md-3">
                                <div class="form-group">
                                    <div class="input-group">
                                        <span class="input-group-addon">Settlement Type</span>
                                        <select class="form-control select2" id="st">
                                            <option value="1">Nutech</option>
                                            <option value="0">Aino</option>
                                        </select>
                                    </div>
                                </div>
                            </div> -->
                            <!-- <div class="col-md-3">
                                <div class="form-group">
                                    <div class="input-group">
                                        <span class="input-group-addon">Corridor</span>
                                        <?php echo form_dropdown('', $corridor, '', 'id="corridor" class="form-control select2"'); ?>
                                    </div>
                                </div>
                            </div> -->
                            <!-- <div class="col-md-3">
                                <div class="form-group">
                                    <div class="input-group">
                                        <span class="input-group-addon">Card</span>
                                        <?php //echo form_dropdown('', $card_type, '', 'id="card" class="form-control select2"'); ?>
                                        <span class="input-group-btn">
                                            <button class="btn btn-success" id="searching" type="button" data-loading-text="<i class='fa fa-spinner fa-spin'></i>" title="Search">
                                                <i class="fa fa-search"></i>
                                            </button>
                                        </span>
                                    </div>
                                </div>
                            </div> -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12 col-padding">
            <div class="portlet light bordered portlet-padding margin-bottom-0">
                <div class="portlet-body">
                    <?php echo $btnDownload ?>
                    <ul class="nav nav-tabs">
                        <?php foreach ($tab as $key => $value) { 
                            $no = $key+1; 
                            $active = '';
                            if($key == 0){
                                $active = 'class="active"';
                            } 
                        ?>
                        <li <?php echo $active; ?>>
                            <a href="#<?php echo $no; ?>" data-toggle="tab"><?php echo $value; ?></a>
                        </li>
                        <?php } ?>
                    </ul>
                    <div class="tab-content">
                        <?php foreach ($fill_tab as $k => $v) { 
                            $no_ = $k+1; 
                            $active2 = '';
                            if($k == 0){
                                $active2 = 'active in"';
                            } 
                        ?>
                        <div class="tab-pane fade <?php echo $active2; ?>" id="<?php echo $no_; ?>">
                            <?php echo $this->load->view('settlement/'.$v.''); ?>
                        </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<form id="formDownload" target="_blank" method="POST"></form>
<script type="text/javascript">
    jQuery(document).ready(function() {
        var disabledDropdown = function(){
            if($('#st').val() == 1){
                $('#card').attr('disabled', false);
                // $('#corridor').attr('disabled', false);
            }else{
                $('#card').attr('disabled', true);
                // $('#corridor').attr('disabled', true);
            }
        }

        // disabledDropdown();

        $('#st').change(function(){
            disabledDropdown();            
        })

        setTimeout(function(){
            $('#ctrl').trigger('click');
            $('.select2').select2();
        },1);

        $(".menu-toggler").click(function() {
            $('.select2').css('width', '100%');
        });

        $('ul.nav-tabs li a').click(function(){
            text = $(this).html().toLowerCase();

            if(text == 'detail all'){
                $('#form-status').removeClass('hide');
                $('.select2').select2();
            }else{
                $('#form-status').addClass('hide');
            }
        });

        var datePickerFilter = function(){
            $.datepicker.setDefaults({
                changeMonth: true,
                changeYear: true,
                dateFormat: 'yy-mm-dd',
                maxDate: new Date()
            });

            $('#start_date_input').datepicker({
                onSelect: function(dateText) {
                    $("#end_date_input").datepicker( "option", "minDate", dateText);
                }
            });

            $('#end_date_input').datepicker({
                onSelect: function(dateText) {
                    $("#start_date_input").datepicker( "option", "maxDate", dateText);
                }
            });

            $('#btn_start').click(function(){
                $("#start_date_input").datepicker("show");
            })

            $('#btn_end').click(function(){
                $("#end_date_input").datepicker("show");
            })
        }

        datePickerFilter();

        var dSend = null;
        
        var listSettlement = function(){
            $.ajax({
                url         : 'settlement/listSettlement',
                data        : {
                    start_date: $('#start_date_input').val(),
                    end_date: $('#end_date_input').val(),
                    status: $('#status').val(),
                    card: $('#card').val(),
                    card_name: $("#card option:selected").text(),
                    // corridor: $('#corridor').val(),
                    // corridor_name: $("#corridor option:selected").text(),
                    date_type: $('#date_type').val(),
                    date_type_name: $("#date_type option:selected").text(),
                    st: $('#st').val(),
                    st_name: $("#st option:selected").text()
                },
                type        : 'POST',
                dataType    : 'json',

                beforeSend: function(){
                    $('#searching').button('loading');
                    // unBlockUiId('.box');
                },

                success: function(json) {
                    if(json.code == 1){
                        listSummary(json);
                        detailSettlement(json);
                        detailBankSettlement(json);
                    }
                    
                    dSend = json.data.post;
                },

                error: function() {
                    toastr.error('Please contact the administrator');
                },

                complete: function(json){
                    $("#searching").button('reset');
                    $('.box').unblock();
                }
            });
        }

        listSettlement();
        TableDatatablesResponsive.init();

        $("#searching").on("click",function(){
            listSettlement();
            $('#dataTables').DataTable().ajax.reload();
        });

        //download pdf
        $('.pdf').click(function(){
            var aActive = $('ul[class="nav nav-tabs"] li[class="active"] a')[0]
            a = aActive.href.split('#');
            ahref = a[0]+'/download_pdf';

            addForm = '<input type="text" name="start_date" value="'+dSend.start_date+'">\
            <input type="text" name="end_date" value="'+dSend.end_date+'">\
            <input type="text" name="card" value="'+dSend.card+'">\
            <input type="text" name="card_name" value="'+dSend.card_name+'">\
            <input type="text" name="corridor" value="'+dSend.corridor+'">\
            <input type="text" name="corridor_name" value="'+dSend.corridor_name+'">\
            <input type="text" name="date_type" value="'+dSend.date_type+'">\
            <input type="text" name="date_type_name" value="'+dSend.date_type_name+'">\
            <input type="text" name="status" value="'+dSend.status+'">\
            <input type="text" name="tab_name" value="'+$(aActive).html()+'">\
            <input type="text" name="type" value="'+a[1]+'">';

            $('#formDownload').attr('action',ahref);
            $('#formDownload').html(addForm);
            $('#formDownload').submit();
            $('#formDownload input').remove();
        })

        // download excel
        $('.excel').click(function(){
            var aActive = $('ul[class="nav nav-tabs"] li[class="active"] a')[0]
            a = aActive.href.split('#');
            ahref = a[0]+'/download_excel';

            addForm = '<input type="text" name="start_date" value="'+dSend.start_date+'">\
            <input type="text" name="end_date" value="'+dSend.end_date+'">\
            <input type="text" name="card" value="'+dSend.card+'">\
            <input type="text" name="card_name" value="'+dSend.card_name+'">\
            <input type="text" name="corridor" value="'+dSend.corridor+'">\
            <input type="text" name="corridor_name" value="'+dSend.corridor_name+'">\
            <input type="text" name="date_type" value="'+dSend.date_type+'">\
            <input type="text" name="status" value="'+dSend.status+'">\
            <input type="text" name="date_type_name" value="'+dSend.date_type_name+'">\
            <input type="text" name="tab_name" value="'+$(aActive).html()+'">\
            <input type="text" name="type" value="'+a[1]+'">';

            $('#formDownload').attr('action',ahref);
            $('#formDownload').html(addForm);
            $('#formDownload').submit();
            $('#formDownload input').remove();
        })
    })
</script>
