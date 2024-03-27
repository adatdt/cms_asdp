<!-- Modal -->
<div class="modal fade  " id="modalUserWebDetail" role="dialog" aria-labelledby="modalUserWebDetailTitle" aria-hidden="true">
  <!-- <div class="modal-dialog modal-dialog-centered" role="document"> -->

    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLongTitle">Tambah User WEB dan Mobile dikecualikan</h5>
        </div>
        <div class="modal-body">
           <div class="form-group">
                <div class="row">
                    <div class="col-sm-12 form-group">     
                        <input type="text" class="form-control in-group" data-role="tagsinput"  name="getEmail" id="getEmail" placeholder="Masukan email">                        
                                           
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <input type="hidden"  id="detail_code_rms">
            <input type="hidden"  id="code_rms">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button> &nbsp;
            <button type="button " id="searchEmailBtnDetail" class="btn btn-primary btn-md  " >Simpan</button>         
        </div>
    </div>
        
    <!-- </div> -->
</div>    
<script>
    // let dataEmail =  new Set();    
    $(document).ready(function(){
            $(`#searchEmailBtnDetail`).on("click", function(){
            let  tagEmail = $("#getEmail").tagsinput('items');
            let detail_code_rms = $(`#detail_code_rms`).val();
            let code_rms = $(`#code_rms`).val();
            
                $.ajax({
                    url : `<?= site_url("radius/rms/action_add_user_web_exp") ?>`,
                    dataType :`json`,
                    type:`post`,
                    data: `tagEmail=${tagEmail}&rmsCode=${detail_code_rms}`,
                    beforeSend: function() {
                        unBlockUiId('modalUserWebDetail')
                    },
                    success:function(x)
                    {
                        // console.log(x)                        
                        if(x.code == 1)
                        {
                            toastr.success(x.message, 'Sukses');                            
                            $('#modalUserWebDetail').modal('toggle');
                            $("#getEmail").tagsinput('removeAll');

                            $(`#detailDataTables_${code_rms}`).DataTable().ajax.reload(null, false);                            

                        }
                        else
                        {
                            // console.log(x.data['massage'])    
                            let message = x.message
                            // console.log(x.data)
                            if(x.data != undefined)
                            {
                                message = x.data['message']
                            }                    
                            toastr.error(message, 'Gagal');
                            // console.log(dataEmail);
                        }

                        
                    },
                    error: function() {
                        toastr.error('Silahkan Hubungi Administrator', 'Gagal');
                    },

                    complete: function() {
                        $('#modalUserWebDetail').unblock();
                    }                
                })

        });
    })





</script>