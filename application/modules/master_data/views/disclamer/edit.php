<link href="<?php echo base_url(); ?>assets/global/plugins/icheck/skins/all.css" rel="stylesheet" type="text/css" />
<script src="<?php echo base_url(); ?>assets/global/plugins/ckeditor4/ckeditor.js" type="text/javascript"></script>
<style type="text/css">
    .wajib {
        color: red;
    }
</style>

<div class="col-md-8 col-md-offset-2">
    <div class="portlet box blue" id="box">
        <?php echo headerForm($title) ?>
        <div class="portlet-body">
            <?php echo form_open('master_data/disclamer/action_edit', 'id="ff" autocomplete="on"'); ?>
            <div class="box-body">
                <div class="form-group">
                    <div class="row">

                        <div class="col-sm-12 form-group">
                            <label>Nama <span class="wajib">*</span></label>
                            <input type="text" name="name" class="form-control" placeholder="Nama Info" required value="<?php echo $detail->name ?>">
                            <input type="hidden" name="id" required value="<?php echo $this->enc->encode($detail->id) ?>">
                        </div>

                        <div class="col-sm-12 form-group">
                            <label>Info <span class="wajib">*</span></label>
                            <textarea class="form-control wysihtml5" name="info" placeholder="Info" required id="info" rows="20"><?php echo $detail->info ?></textarea>
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
    $(document).ready(function() {
        validateForm('#ff', function(url, data) {
            // data['info'] = CKEDITOR.instances.info.getData();
            data['info'] = replaceStyle(escapeHtml(CKEDITOR.instances.info.getData()));
            postData(url, data);
        });

        $('.select2:not(.normal)').each(function() {
            $(this).select2({
                dropdownParent: $(this).parent()
            });
        });

        //  $('#wysihtml5').wysihtml5({
        //      "image": false,
        //      "link": false,
        //  });
        CKEDITOR.config.extraPlugins = 'justify';
        CKEDITOR.replace('info', {
            toolbarGroups: [{
                    name: 'clipboard',
                    groups: ['clipboard', 'undo']
                },
                {
                    name: 'editing',
                    groups: ['find', 'selection', 'spellchecker', 'editing']
                },
                {
                    name: 'forms',
                    groups: ['forms']
                },
                {
                    name: 'links',
                    groups: ['links']
                },
                {
                    name: 'insert',
                    groups: ['insert']
                },
                {
                    name: 'document',
                    groups: ['mode', 'document', 'doctools']
                },
                {
                    name: 'tools',
                    groups: ['tools']
                },
                '/',
                {
                    name: 'basicstyles',
                    groups: ['basicstyles', 'cleanup']
                },
                {
                    name: 'colors',
                    groups: ['colors']
                },
                {
                    name: 'paragraph',
                    groups: ['list', 'indent', 'blocks', 'align', 'bidi', 'paragraph']
                },
                {
                    name: 'styles',
                    groups: ['styles']
                },
                {
                    name: 'others',
                    groups: ['others']
                },
                {
                    name: 'about',
                    groups: ['about']
                }
            ],

            removeButtons: 'Print,Preview,ExportPdf,NewPage,Save,Templates,Find,Replace,SelectAll,Form,Checkbox,Radio,TextField,Textarea,Select,Button,ImageButton,HiddenField,About,Font,Flash,BidiRtl,Language,ShowBlocks,BidiLtr'
        });

        $('#saveBtn').click(function() {
            console.log($('#info').val())
            console.log(CKEDITOR.instances.info.getData())
        })
    });
</script>