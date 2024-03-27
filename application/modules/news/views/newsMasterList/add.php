
<style type="text/css">
    .wajib{ color:red; }

</style>
<div class="col-md-6 col-md-offset-3">
    <div class="portlet box blue" id="box">
        <?php echo headerForm($title) ?>
        <div class="portlet-body">
            <?php echo form_open('news/newsMasterList/action_add', 'id="ff" autocomplete="on"'); ?>
            <div class="box-body">
                 <div class="form-group">
                    <div class="row">
                        
                        <div class="col-sm-6 form-group">
                            <label>Tipe<span class="wajib">*</span></label>
                            <?=  form_dropdown("type",$getDataType,"",' class="form-control select2" required  ') ?>
                        </div>

                        <div class="col-sm-6 form-group">
                            <label>Judul <span class="wajib">*</span></label>
                            <input type="text" name="title" class="form-control" placeholder="Judul Berita" required autocomplete="off" maxlength="255">                            
                        </div>
                        <div class="col-sm-12 form-group"></div>
                        <div class="col-sm-12 form-group">
                            <label>Sub Judul <span class="wajib">*</span></label>
                            <textarea name="contentData" id="contentData" class="form-control" placeholder="Konten" required autocomplete="off"></textarea>
                        </div>    
                        <div class="col-sm-12 form-group">
                            <span class="wajib"><i>Tulis <b style="color:blue">#transnum</b> untuk menampilkan kode Transnumber secara dinamis pada text Input Judul dan Sub Judul*</i></span>
                        </div>                                            
                    </div>
                </div>
            </div>
            <?php echo createBtnForm('Simpan') ?>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        rules =  {
            title: { maxlength: 255},
            thumbnail: { required: true}
        }

        messages= {
            title: { 
                maxlength: jQuery.validator.format("Maximal {0} Karater")
            }
        }

        myData.validateForm('#ff',function(url,data){
            postData2(url,data);
        });


        $('.select2:not(.normal)').each(function () {
            $(this).select2({
                dropdownParent: $(this).parent()
            });
        });        

        $('#startDate').datetimepicker({
            format: 'yyyy-mm-dd hh:ii',
            changeMonth: true,
            changeYear: true,
            autoclose: true,
            todayHighlight: true,
            minuteStep:60,
            // startDate: new Date()
            startDate: myData.getDataNow()
        });

        $('#endDate').datetimepicker({
            format: 'yyyy-mm-dd hh:ii',
            changeMonth: true,
            changeYear: true,
            autoclose: true,
            todayHighlight: true,
            endDate: "+1m",
            minuteStep:60,
            // startDate: new Date()
            startDate: myData.getDataNow()
        });


        $("#startDate").change(function() {

            var startDate = $(this).val();
            var someDate = new Date(startDate);

            someDate.getDate();
            someDate.setMonth(someDate.getMonth() + 1);
            someDate.getFullYear();
            let endDate = myData.formatDate(someDate);

            // destroy ini firts setting
            $('#endDate').datetimepicker('remove');

            // Re-int with new options
            $('#endDate').datetimepicker({
                format: 'yyyy-mm-dd hh:ii',
                changeMonth: true,
                changeYear: true,
                autoclose: true,
                todayHighlight: true,
                minuteStep:60,
                endDate: endDate,
                startDate: startDate
            });

            $('#endDate').val(startDate).datetimepicker("update")
        });
        function postData2(url,data){
        
            form = $('form')[0];
            formData = new FormData(form);            
            formData.set('contentData', btoa($("#contentData").val()));

            $.ajax({
                url         : url,
                data        :formData,
                <?php echo $this->security->get_csrf_token_name(); ?>:$("input[name=" + csfrData.csrfName + "]").val(),
                // data        :data,
                type        : 'POST',
                // enctype: 'multipart/form-data',
                processData: false,  // Important!
                contentType: false,
                cache:false,
                dataType    : 'json',
            
                beforeSend: function(){
                    unBlockUiId('box')
                },
            
                success: function(json) {
                    $("input[name=" + json.csrfName + "]").val(json.tokenHash);
                    csfrData[json['csrfName']] = json['tokenHash'];
                    $.ajaxSetup({
                        data: csfrData
                    });
                    
                    if(json.code == 1){
                        // unblockID('#form_edit');
                        closeModal();
                        toastr.success(json.message, 'Sukses');
            
                        $('#dataTables').DataTable().ajax.reload( null, false )
            
                    }else{
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
