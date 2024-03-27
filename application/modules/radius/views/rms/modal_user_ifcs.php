<style type="text/css">
    #modalUserIfcs {
        left: 35% !important; 
        margin-left: -15% !important; 
        width: 60% !important;
    }

</style>
<!-- Modal -->
<div class="modal fade  " id="modalUserIfcs" role="dialog" aria-labelledby="modalUserIfcsTitle" aria-hidden="true">
  <!-- <div class="modal-dialog modal-dialog-centered" role="document"> -->

    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLongTitle">Tambah User IFCS dikecualikan</h5>
        </div>
        <div class="modal-body">
           <div class="form-group">
                <div class="row">
                    <div class="col-sm-12 form-group">     
                        <input type="text" class="form-control in-group" data-role="tagsinput"  name="getEmailIfcsAdd" id="getEmailIfcsAdd" placeholder="Masukan email">                        
                                         
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>&nbsp;
            <button type="button " id="searchEmailBtnIfcs" class="btn btn-primary btn-md " >Simpan</button>           
        </div>
    </div>
        
    <!-- </div> -->
</div>    
<script>
    let dataEmailIfcs =  new Set();   
//    console.log(dataEmailIfcs)

    $(document).ready(function(){     

        const tableIfcs = $('#tableIfcs').DataTable({
            "language": {
                "aria": {
                    "sortAscending": ": activate to sort column ascending",
                    "sortDescending": ": activate to sort column descending"
                },
                    "processing": "Proses.....",
                    "emptyTable": "Tidak ada data",
                    "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    "infoEmpty": "Menampilkan 0 sampai 0 dari 0 entri",
                    "infoFiltered": "(disaring dari _MAX_ entri keseluruhan)",
                    "lengthMenu": "Menampilkan _MENU_",
                    "search": "Pencarian :",
                    "zeroRecords": "Tidak ditemukan data yang sesuai",
                    "paginate": {
                    "previous": "Sebelumnya",
                    "next": "Selanjutnya",
                    "last": "Terakhir",
                    "first": "Pertama"
                }
            },
            'columnDefs': [
                    {
                        "targets": 2, // your case first column
                        "className": "text-center",
                        "width": "2%"
                }
            ],
            "lengthMenu": [
                [10, 25, 50, 100],
                [10, 25, 50, 100]
            ],
            "pageLength": 10,
            "searching":true,
            "pagingType": "bootstrap_full_number",
            "order": [[ 0, "desc" ]],
            "initComplete": function () {
                var $searchInput = $('div #tableIfcs_filter input');
                var data_tables = $('#tableIfcs').DataTable();
                $searchInput.unbind();
                $searchInput.bind('keyup', function (e) {
                    if (e.keyCode == 13 || e.whiche == 13) {
                        data_tables.search(this.value).draw();
                    }
                });
            },
        });

        // seting numbering
        tableIfcs.on( 'draw.dt', function () {
            var PageInfo = $('#tableIfcs').DataTable().page.info();
                tableIfcs.column(0, { page: 'current' }).nodes().each( function (cell, i) {
                    cell.innerHTML = i + 1 + PageInfo.start;
                } );
            } );     
        $(`#searchEmailBtnIfcs`).on("click", function(){
        let  tagEmail = $("#getEmailIfcsAdd").tagsinput('items');
        // console.log(tagEmail)
            $.ajax({
                url : `<?= site_url("radius/rms/getUserIfcs") ?>`,
                dataType :`json`,
                type:`post`,
                data: `tagEmail=${tagEmail}`,
                beforeSend: function() {
                    unBlockUiId('modalUserIfcs')
                },
                success:function(x)
                {
                    if(x.code == 1)
                    {
                        toastr.success(x.message, 'Sukses');
                        getEmailIfcs = x.data['email'];

                        getEmailIfcs.forEach(element => {
                            dataEmailIfcs.add(element);
                        });
                        
                        $('#modalUserIfcs').modal('toggle');
                        $("#getEmailIfcs").tagsinput('removeAll');

                        let email = myData.getDataExeption(dataEmailIfcs,'ifcs');
                        tableIfcs.clear();
                        tableIfcs.rows.add(email).draw();

                    }
                    else
                    {
                        console.log(x.data['massage'])                        
                        toastr.error(x.data['massage'], 'Gagal');
                        // console.log(dataEmailIfcs);
                    }  
                        // console.log(dataEmailIfcs);

                },
                error: function() {
                    toastr.error('Silahkan Hubungi Administrator', 'Gagal');
                },

                complete: function() {
                    $('#modalUserIfcs').unblock();
                }                     
            })
        });
    });

    function deleteUserIfcs(data){
        $(document).ready(function(){
            dataEmailIfcs.delete(data);
            let email = myData.getDataExeption(dataEmailIfcs, 'ifcs');
            const tableIfcs2 = $('#tableIfcs').DataTable();
            tableIfcs2.clear();
            tableIfcs2.rows.add(email).draw();
        })
    }   

</script>