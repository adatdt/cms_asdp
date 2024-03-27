<style type="text/css">
    .wajib{ color:red; }
</style>

<div class="col-md-6 col-md-offset-3">
    <div class="portlet box blue" id="box">
        <?php echo headerForm($title) ?>
        <div class="portlet-body">
            <?php echo form_open('pelabuhan/port_etis/action_edit', 'id="ff" autocomplete="on"'); ?>
            <div class="box-body">
                 <div class="form-group">
                    <div class="row">
                        <div class="col-sm-6 form-group">
                            <label>Nama Pelabuhan <span class="wajib">*</span></label>
                            <input type="text" name="name" class="form-control" placeholder="Nama Pelabuhan" required value="<?php echo $row->name ?>">
                            <input type="hidden" name="id" value="<?php echo $id ?>">
                        </div>
                        
                        <div class="col-sm-6 form-group">
                            <label>Nama Provinsi <span class="wajib">*</span></label>
                            <input type="text" name="city" class="form-control" placeholder="Nama Provinsi" required value="<?php echo $row->city ?>">
                        </div>
                        <div class="col-sm-12"></div>
                        <div class="col-sm-6 form-group">
                            <label>Domain URL <span class="wajib">*</span></label>
                            <input type="url" id="url" name="url" class="form-control" placeholder="Domain URL" value="<?php echo $row->url ?>" required>
                        </div>
                                                                                                                                        
                    </div>
                </div>
            </div>
            <?php echo createBtnForm('Edit') ?>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        rules   = {url: "required url"};
        $("#url").on("keyup",function(){
            // console.log($(this).val())

            let link = $(this).val();
            if (link.indexOf("http://") == 0 || link.indexOf("https://") == 0) {
                // console.log("The link has http or https.");
                
                $(this).rules("add", {messages : { url : 'Please enter a valid URL.' }});                
            }
            else{
                // console.log("The link doesn't have http or https.");
                
                $(this).rules("add", {messages : { url : '(Domain url wajib diawali https://atau http://)' }});                
            }
            
        })

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
