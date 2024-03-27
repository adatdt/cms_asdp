<script type="text/javascript">

    class MyData{

        addForm(index){
            let html =`
            <div class="row" id="formId${index}">
                <div class="col-sm-12 form-group"><hr /></div>

                <div class="col-sm-12 form-group">
                    <label>Description<span class="wajib">*</span></label>
                    <input type="text" name="desc[${index}]" class="form-control" placeholder='Description' >
                </div>
                <div class="col-sm-12 form-group">
                    <label>URL</label>
                    <input type="text" name="url[${index}]" class="form-control" placeholder='URL'>
                </div>
                <div class="col-sm-12 form-group">
                <div class="fileinput fileinput-new" data-provides="fileinput">
                    <label>Pilih File Gambar</label>
                    <div class="input-group ">
                        <div class="form-control uneditable-input   input-fix" id="tempatfile${index}" data-trigger="fileinput">
                            <i class="fa fa-file fileinput-exists"></i>&nbsp;
                            <span class="fileinput-filename"> </span>
                        </div>
                        <span class="input-group-addon btn default btn-file">
                            <span class="fileinput-new"> Pilih File </span>
                            <span class="fileinput-exists"> Pilih File</span>
                            <input type="hidden"><input type="file" name="berkas[${index}]"> </span>
                        <a href="javascript:;" class="input-group-addon btn red fileinput-exists" data-dismiss="fileinput" > Hapus </a>
                    </div>
                    <span class="form-text text-muted">Recomendation resolution 1366 x 437 (*.jpg, *.jpeg, *.png) dan maksimal 300kb</span>
                </div> 
                </div> 
                <div class="col-sm-12 form-group">
                    <a hrf="#"  class="btn btn-sm btn-danger pull-right" title="Hapus" id="delete" onclick="myData.deleteForm('formId${index}')"  ></i> Hapus</a>
                </div> 
            </div>                          
            `

            return html;
        }

        deleteForm(id)
        {
            $(`#${id}`).remove();
            // alert(id)
        }
    }

</script>