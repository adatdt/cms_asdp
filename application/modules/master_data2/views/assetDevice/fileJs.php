<script>
    class MyData{
        fileInput(id)
        {
            let html =`
                <div class="col-sm-12 form-group row group-gambar" id="div-upload-${id}" style="display:none;">
                    <div class="col-sm-12 form-group">       
                        
                        <label class="input-group-text" for="inputGroupFile01">Upload File <span class="wajib">*</span></label>        
                        <div class="form-inline "  style="padding-bottom:10px;" >                                   
                            <input type="file" class="form-control file-upload group-upload" id="fileUpload_${id}" name="fileUpload[${id}]"  required style="width:90%" onchange="myData.viewImg(${id})"> 
                            <a class=" btn  btn-danger pull-right btn-hapus"  title="Hapus File" onclick="hapus(${id})">
                                <i class="fa fa-trash-o"></i>
                            </a>       
                        </div>                                                                                                                    
                    </div>                                                            
                    <div class="col-sm-6 form-group">
                        <label>Keterangan<span class="wajib">*</span></label>
                        <input type="text" name="desc[${id}]" class="form-control group-upload"  placeholder="Keterangan" required>
                    </div>
                    <div class="col-sm-6 img-detail" id="img-detail-${id}"> </div>                 
                    <div class="col-sm-12 form-group"><h1><hr /></h1></div>              


                </div>       

            `
            return html;
        }

	    changeSearch=(x,name)=>
	    {
	    	$("#btnData").html(`${x} <i class="fa fa-angle-down"></i>`);
	    	$("#searchData").attr('data-name', name);

	    }    
        formatDate=(date)=> {
            var d = new Date(date),
                month = '' + (d.getMonth() + 1),
                day = '' + d.getDate(),
                year = d.getFullYear();

            if (month.length < 2) 
                month = '0' + month;
            if (day.length < 2) 
                day = '0' + day;

            return [year, month, day].join('-');
        }
        viewImg(id)
        {
            const fileType = $(`#fileType`).find(':selected').attr('data-id');
            const file = document.querySelector(`#fileUpload_${id}`).files[0];
            var reader = new FileReader();
            reader.readAsDataURL(file);

            reader.onload = function () {
                let html =``
                if(fileType == 1)
                {
                    html = `<span id="file_${id}" style="display:none;"><img  src="${reader.result}" width="90%"; height="auto"  /></span>`
                }
                else
                {
                    // console.log(id)   
                    html = `<span id="file_${id}" style="display:none;" ><video  width="90%" height="auto" controls>
                                    <source src="${reader.result}" type="video/mp4" style="display:none;">
                                </video></span>`
                }            
                $(`#img-detail-${id}`).html(html)
                $(`#file_${id}`).fadeIn( 2000 )
                
            };
            reader.onerror = function (error) {
                console.log('Error: ', error);
            };

        }                    
    }
</script>