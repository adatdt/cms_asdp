<div class="page-content-wrapper">
    <div class="page-content">
        <div class="page-bar">
            <ul class="page-breadcrumb">
                <li>
                    <?php echo '<a href="' . $url_home . '">' . $home . '</a>'; ?>
                    <i class="fa fa-circle"></i>
                </li>
                <li>
                    <span><?php echo $title; ?></span>
                </li>
            </ul>
            <div class="page-toolbar">
                <div id="dashboard-report-range" class="pull-right tooltips btn btn-sm" data-container="body" data-placement="bottom">
                    <span class="thin uppercase hidden-xs" id="datetime"></span>
                    <script type="text/javascript">window.onload = date_time('datetime');</script>
                </div>
            </div>
        </div>

        <?php $now=date("Y-m-d"); $last_week=date('Y-m-d',strtotime("-7 days"))?>
        <div class="my-div-body">
            <div class="row">
                <div class="col-md-12 col-padding">
                    <div class="portlet box blue-madison portlet-padding">
                        <div class="portlet-title">                    
                            <div class="caption"><?php echo $title ?></div>
                        </div>
                        <div class="portlet-body">
                            <div class="row">
                                <div class="col-md-12 filter-trx">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <div class="input-group">
                                                    <div class="input-group-addon">Filter</div>
                                                    <select class="form-control select2" id="date_type">
                                                        <option value="3">Shift Date</option>
                                                        <option value="1">Transaction Date</option>
                                                        <option value="2">Settlement Date</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <div class="input-group">
                                                    <div class="input-group-addon">Tanggal</div>
                                                    <input class="form-control input-small date" id="start_date_input" placeholder="yyyy-mm-dd" value="<?php echo date('Y-m-d') ?>">
                                                    <div class="input-group-addon"> s/d </div>
                                                    <input class="form-control input-small date" id="end_date_input" placeholder="yyyy-mm-dd" value="<?php echo date('Y-m-d') ?>">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3" id="form-shift">
                                            <div class="form-group">
                                                <div class="input-group">
                                                    <div class="input-group-addon">Shift</div>
                                                    <select id="shift" class="form-control js-data-example-ajax select2 input-small" dir="" name="shift">
                                                        <option value="">All Shift</option>
                                                        <?php foreach($shift as $key=>$value) {?>
                                                        <option value="<?php echo $this->enc->encode($value->id); ?>"><?php echo strtoupper($value->shift_name) ?></option>
                                                        <?php }?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>                                    
                                </div>
                                <div class="col-md-12 filter-trx">
                                    <div class="row">
                                    <?php if ($this->session->userdata('port_id') == '') { ?>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <div class="input-group">
                                                    <div class="input-group-addon">Pelabuhan</div>
                                                    <select id="port" class="form-control select2" dir="" name="port">
                                                        <option value="">All Port</option>
                                                        <?php foreach($port as $key=>$value) {?>
                                                        <option value="<?php echo $this->enc->encode($value->id); ?>"><?php echo strtoupper($value->name) ?></option>
                                                        <?php }?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    <?php } ?>
                                    <?php if ($dropdownBank == 0) { ?>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <div class="input-group">
                                                    <span class="input-group-addon">Bank</span>
                                                    <?php echo form_dropdown('', $bank, '', 'id="bank" class="form-control select2"'); ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php } ?>
                                        <div class="col-md-3" id="form-pencarian">
                                            <div class="form-group">
                                                <div class="input-group">
                                                    <div class="input-group-addon">Search</div>
                                                    <input type="text" name="pencarian" id="pencarian" class="form-control" placeholder="Search filename">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <button class="btn btn-success" id="searching" type="button" data-loading-text="Searching... <i class='fa fa-spinner fa-spin'></i>" title="Search">
                                                    Search <i class="fa fa-search"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    
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
                                    <?php echo $this->load->view('rekap_settlement/'.$v.''); ?>
                                </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<form id="formDownload" target="_blank" method="POST"></form>
<script type="text/javascript">
    jQuery(document).ready(function() {
       
        setTimeout(function(){
            $('#ctrl').trigger('click');
            $('.menu-toggler').trigger('click');
            $('.select2').select2();
        },1);

        $(".menu-toggler").click(function() {
            $('.select2').css('width', '100%');
        });

        // $('ul.nav-tabs li a').click(function(){
        //     text = $(this).html().toLowerCase();

        //     if(text == 'detail all'){
        //         $('#form-status').removeClass('hide');
        //         $('.select2').select2();
        //     }else{
        //         $('#form-status').addClass('hide');
        //     }

        //     if(text == 'rekap file settlement'){
        //         $('#form-pencarian').removeClass('hide');
        //         $('.select2').select2();
        //     }else{
        //         $('#form-pencarian').addClass('hide');
        //     }
        // });

        $('#date_type').on('select2:select', function(e) {
            var data = e.params.data.id;
            if (data == 3) {
                $('#form-shift').removeClass('hide');
                $('.select2').select2();
            } else {
                $('#form-shift').addClass('hide');
            }
        });


        $('#start_date_input').datepicker({
            format: 'yyyy-mm-dd',
            changeMonth: true,
            changeYear: true,
            autoclose: true,
            endDate: new Date(),
        }).on('changeDate',function(e) {
            $('#end_date_input').datepicker('setStartDate', e.date)
        });

        $('#end_date_input').datepicker({
            format: 'yyyy-mm-dd',
            changeMonth: true,
            changeYear: true,
            autoclose: true,
            startDate: $('#start_date_input').val(),
            endDate: new Date(),
        }).on('changeDate',function(e) {
            $('#start_date_input').datepicker('setEndDate', e.date)
        });

        var dSend = null;
        
        var listSettlement = function(){
            $.ajax({
                url         : 'rekap_settlement/listSettlement',
                data        : {
                    start_date: $('#start_date_input').val(),
                    end_date: $('#end_date_input').val(),
                    status: $('#status').val(),
                    bank: $('#bank').val(),
                    bank_name: $("#bank option:selected").text(),
                    port: $('#port').val(),
                    shift: $('#shift').val(),
                    shift_name: $("#shift option:selected").text(),
                    port_name: $("#port option:selected").text(),
                    date_type: $('#date_type').val(),
                    date_type_name: $("#date_type option:selected").text(),
                    search: $("#pencarian").val(),
                },
                type        : 'POST',
                dataType    : 'json',

                beforeSend: function(){
                    $('#searching').button('loading');
                    // unBlockUiId('.box');
                },

                success: function(json) {
                    if(json.code == 1){
                        detailRekapFS(json);
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
        // TableDatatablesResponsive.init();

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
            <input type="text" name="bank" value="'+dSend.bank+'">\
            <input type="text" name="bank_name" value="'+dSend.bank_name+'">\
            <input type="text" name="date_type" value="'+dSend.date_type+'">\
            <input type="text" name="date_type_name" value="'+dSend.date_type_name+'">\
            <input type="text" name="status" value="'+dSend.status+'">\
            <input type="text" name="port" value="'+dSend.port+'">\
            <input type="text" name="port_name" value="'+dSend.port_name+'">\
            <input type="text" name="shift" value="'+dSend.shift+'">\
            <input type="text" name="shift_name" value="'+dSend.shift_name+'">\
            <input type="text" name="search" value="'+dSend.search+'">\
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
            <input type="text" name="bank" value="'+dSend.bank+'">\
            <input type="text" name="date_type" value="'+dSend.date_type+'">\
            <input type="text" name="status" value="'+dSend.status+'">\
            <input type="text" name="port" value="'+dSend.port+'">\
            <input type="text" name="port_name" value="'+dSend.port_name+'">\
            <input type="text" name="shift" value="'+dSend.shift+'">\
            <input type="text" name="shift_name" value="'+dSend.shift_name+'">\
            <input type="text" name="date_type_name" value="'+dSend.date_type_name+'">\
            <input type="text" name="search" value="'+dSend.search+'">\
            <input type="text" name="tab_name" value="'+$(aActive).html()+'">\
            <input type="text" name="type" value="'+a[1]+'">';

            $('#formDownload').attr('action',ahref);
            $('#formDownload').html(addForm);
            $('#formDownload').submit();
            $('#formDownload input').remove();
        })
    })
</script>
