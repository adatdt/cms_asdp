<script type="text/javascript">
    var csfrData = {};
        csfrData[`<?php echo $this->security->get_csrf_token_name(); ?>`] =`<?php echo $this->security->get_csrf_hash(); ?>`;
        $.ajaxSetup({
            data: csfrData
    });


    class MyData {
        getShift (data)
        {
            $.ajax({
                url: "<?= site_url() ?>shift_management/generateQr/getShift",
                data: data,
                type: 'POST',
                dataType: 'json',
                beforeSend: function () {
                    $("#shift").attr("disabled", true);
                },
                success: function (x) 
                {
                    let html =`<option value="">Pilih</option> `;
                    if(x.length>0)
                    {
                        
                        x.forEach(data => {
                            
                            html += `<option value="${data.id}">${data.name}</option> `;

                        });
                        
                    }

                    document.querySelector("#shift").innerHTML=html;
                    $('[name="<?php echo $this->security->get_csrf_token_name(); ?>"]').val(x[0].tokenHash);
                },
                error: function () {
                    toastr.error('Silahkan Hubungi Administrator', 'Gagal');
                    $("#shift").attr("disabled", false);
                },

                complete: function () {
                    $("#shift").attr("disabled", false);
                }
            });
        }

        generateQr(data)
        {
            $.ajax({
                url: "<?= site_url() ?>shift_management/generateQr/getQr",
                data: data,
                type: 'POST',
                dataType: 'json',

                beforeSend: function () {
                    $("#cari").button('loading');
                    $("#port").attr("disabled", true);
                    $("#userGroup").attr("disabled", true);
                    $("#shift").attr("disabled", true);
                },
                success: function (x) 
                {
                    let html = "";
                    switch (x.code) {
                        case 1:
                            let nilaiIndex=0;
                            x.data.forEach(element => {
                                html += myData.dataQr(element, nilaiIndex);
                                nilaiIndex++;
                            });

                            $("#showQr").html(html);
                            document.querySelector("#showPdf").innerHTML=`<?php echo $btn_pdf; ?>`

                            $("#download_pdf").on("click", function(){
                                myData.downloadPdf(x.data)
                            })

                            break;
                    
                        default:
                            $("#showQr").html("");
                            document.querySelector("#showPdf").innerHTML=""                           
                                toastr.error(x.message, '');
                            break;
                    }
                    $('[name="<?php echo $this->security->get_csrf_token_name(); ?>"]').val(x.tokenHash);

                },
                error: function () {
                    toastr.error('Silahkan Hubungi Administrator', 'Gagal');
                    $("#cari").button('reset')
                    $("#port").attr("disabled", false);
                    $("#userGroup").attr("disabled", false);
                    $("#shift").attr("disabled", false);
                },

                complete: function () {
                    $("#cari").button('reset')
                    $("#port").attr("disabled", false);
                    $("#userGroup").attr("disabled", false);
                    $("#shift").attr("disabled", false);
                }
            });            
        }

        downloadPdf(data)
        {
            const url = `<?= site_url() ?>shift_management/generateQr/download_pdf`;

            let form = `<form action="${url}" method="post" id="formPdf" target="_blank" >`  
            
            form += `<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="`+data[0].tokenHash+`" />`                                    
            

            for (let i = 0; i < data.length; i++) {
                
                form +=`<input type="hidden" name="name[${i}]" value="${$(`input[name='name[${i}]']`).val()}" />
                    <input type="hidden" name="group[${i}]" value="${$(`input[name='group[${i}]']`).val()}" />
                    <input type="hidden" name="portName[${i}]" value="${$(`input[name='portName[${i}]']`).val()}" />
                    <input type="hidden" name="shift[${i}]" value="${$(`input[name='shift[${i}]']`).val()}" />
                    <input type="hidden" name="assignmentDate[${i}]" value="${$(`input[name='assignmentDate[${i}]']`).val()}" />  
                    <input type="hidden" name="baseCode[${i}]" value="${$(`input[name='baseCode[${i}]']`).val()}" />`                                    
            }


            form += `</form>`;

            $('#showPdf').append($(form));
            $("#formPdf").submit();               
        }

        dataQr(data, index)
        {
            let html =`
                <div class="col-md-4 " style="padding-top:10px; ">
                    <div class="mt-card-item" style="background-color:#f6f6f9; padding:50px 0px;">
                        <div class="mt-card-avatar mt-overlay-1 " style="text-align:center;  " >
                            <img src="data:image/png;base64,${data.baseCode}" width="250" height="250" style="border: 1px solid black;">
                        </div>
                        <div class="mt-card-content" style="text-align:center">
                            <br />
                            <div class="mt-card-desc font-grey-mint">Tanggal Penugasan : ${data.assignment_date}</div>
                            <div class="mt-card-desc font-grey-mint">Nama : ${data.full_name}</div>
                            <div class="mt-card-desc font-grey-mint">Grup : ${data.group_name}</div>
                            <div class="mt-card-desc font-grey-mint">Pelabuhan : ${data.port_name}</div>
                            <div class="mt-card-desc font-grey-mint">Shift : ${data.shift_name}</div>

                            <input type="hidden" name="name[${index}]" value="${data.full_name}" >
                            <input type="hidden" name="group[${index}]" value="${data.group_name}" >
                            <input type="hidden" name="portName[${index}]" value="${data.port_name}" >
                            <input type="hidden" name="shift[${index}]" value="${data.shift_name}" >
                            <input type="hidden" name="assignmentDate[${index}]" value="${data.assignment_date}" >
                            <input type="hidden" name="baseCode[${index}]" value="${data.baseCode}" >

                        </div>
                    </div>
                </div>                     
            `
            
            return html
        }  
    } 

</script>