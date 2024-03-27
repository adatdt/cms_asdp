<link href="<?php echo base_url(); ?>assets/global/plugins/icheck/skins/all.css" rel="stylesheet" type="text/css" />
<script src="<?php echo base_url(); ?>assets/global/plugins/ckeditor4/ckeditor.js" type="text/javascript"></script>

<!-- <script src="<?php echo base_url(); ?>assets/global/plugins/ckeditor4/ckeditor.js" type="text/javascript"></script> -->

<style type="text/css">
    .wajib {
        color: red
    }

    .scrolling {

        max-height: 500px;
        overflow-y: auto;
    }

</style>
<div class="col-md-10 col-md-offset-1">
    <div class="portlet box blue" id="box">
        <?php echo headerForm($title) ?>
        <div class="portlet-body">
            <?php echo form_open('master_data/assessmentParam/action_add', 'id="ff" autocomplete="off"'); ?>
            <!-- <div class="box-body">
                <div class="form-group">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Tipe <span class="wajib">*</span></label>
                                <input type="text" name="type" class="form-control" placeholder="Tipe" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label>Judul Pop-up<span class="wajib">*</span></label>
                                <input type="input" name="titleText" class="form-control" placeholder="Judul" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6 form-group">
                            <label>Instruksi <span class="wajib">*</span></label>
                            <textarea class=" wysihtml5 form-control" name="instructionText" placeholder="Instruksi" required id="instructionText" rows="20"></textarea>
                        </div>
                        <div class="col-sm-6 form-group">
                            <label>Info Peringatan (Alert) <span class="wajib">*</span></label>
                            <textarea class=" wysihtml5 form-control" name="info" placeholder="Info" required id="wysihtml5" name="wysihtml5" rows="20"></textarea>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 form-group">
                            <hr>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 form-group">
                            <div class="btn btn-warning" id="tambahData">Tambah Pertanyaan</div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 scrolling">
                            <div class="col-sm-2 form-group">
                                <div class="form-group">
                                    <label>Urutan <span class="wajib">*</span></label>
                                    <input type="number" name="ordering[0]" class="form-control" placeholder="urutan" required>
                                </div>
                            </div>
                            <div class="col-sm-10 form-group">
                                <label>Pertanyaan <span class="wajib">*</span></label>
                                <textarea class="wysihtml5 form-control" name="question[0]" placeholder="Info" id="question0" required id="tes" rows="3"></textarea>
                            </div>

                            <div class="col-sm-12">
                                <hr>
                            </div>
                            <div class="col-sm-12 form-group" id="bottomContent"></div>
                        </div>
                    </div>
                </div>
            </div> -->
            <div class="box-body">
                <div class="form-group">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Tipe <span class="wajib">*</span></label>
                                <input type="text" name="type" class="form-control" placeholder="Tipe" required>
                                <input type="hidden" name="id" class="form-control" placeholder="Tipe" required>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Grup Tipe <span class="wajib">*</span></label>
                                <select name="groupType" class="form-control select2" placeholder="Grup Tipe" required >
                                    <option value="" >Pilih</option>
                                    <option value="<?= $this->enc->encode('assesment_ppkm') ?>" >assesment_ppkm</option>
                                    <option value="<?= $this->enc->encode('assesment_delete_account') ?>" >assesment_delete_account</option>
                                    <option value="<?= $this->enc->encode('assesment_vaccine_covid_19') ?>" >assesment_vaccine_covid_19</option>
                                    <option value="<?= $this->enc->encode('assesment_test_covid_19') ?>" >assesment_test_covid_19</option>
                                </select>
                            </div>
                        </div>                        
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label>Judul <span class="wajib">*</span></label>
                                <input type="input" name="titleText" class="form-control" placeholder="Judul" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Instruksi <span class="wajib">*</span></label>
                                <textarea class="wysihtml5 form-control" name="instructionText" required placeholder="Instruksi" required id="instructionText" rows="20"></textarea>
                            </div>
                            <div class="form-group">
                                <label>Info Peringatan (Alert) </label>
                                <textarea class=" wysihtml5 form-control" name="info" placeholder="Info"  id="wysihtml5" name="wysihtml5" rows="20"></textarea>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <div style="display: flex; align-items:center; justify-content: space-between;">
                                    <label style="margin:0">Self Assessment</label>
                                    <div class="btn btn-warning pull-right" id="tambahData">Tambah Pertanyaan</div>
                                </div>
                                <hr style="margin-top:10px" />
                            </div>
                            <div class="row">
                                <div class="col-sm-12 scrolling">
                                    <div class="row">
                                        <div class="col-sm-2">
                                            <div class="form-group">
                                                <label>Urutan </label>
                                                <input type="number" name="ordering[0]" class="form-control" placeholder="urutan" min="1">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label>Pertanyaan </label>
                                                <textarea class="wysihtml5 form-control" name="question[0]" id="question0" placeholder="Info" required id="tes" rows="20"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <hr/>                                   
                                    <div class="col-sm-12 form-group" id="bottomContent"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php echo createBtnForm('Simpan'); ?>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>
</div>
<script src="<?php echo base_url() ?>assets/js/jquery-easyui-1.5.3/jquery.easyui.min.js"></script>
<script type="text/javascript">


    $(document).ready(function() {


        var indexCount = 1;
        validateForm('#ff', function(url, data) {
            data['info'] = replaceStyle(escapeHtml(CKEDITOR.instances.wysihtml5.getData()));
            data['instructionText'] = replaceStyle(escapeHtml(CKEDITOR.instances.instructionText.getData()));
            data['question[0]'] = replaceStyle(escapeHtml(CKEDITOR.instances['question0'].getData()));

            for (var i = 1; i < indexCount; i++) {

                data['question[' + i + ']'] = replaceStyle(escapeHtml(CKEDITOR.instances[`question${i}`].getData()))

            }

            postData(url, data);
        });


        CKEDITOR.config.extraPlugins = 'justify';
        CKEDITOR.config.height = '100px';
        CKEDITOR.replace('wysihtml5', {
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

        CKEDITOR.replace('question[0]', {
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


        CKEDITOR.replace('instructionText', {
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



        $("#tambahData").on("click", function() {

            myData.getDetailForm(indexCount);
            indexCount++;
        })

        $('.select2:not(.normal)').each(function() {
            $(this).select2({
                dropdownParent: $(this).parent()
            });
        });
    })
</script>