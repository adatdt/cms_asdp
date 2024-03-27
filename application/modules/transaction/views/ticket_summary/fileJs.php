<script>
    class MyData{
     
        getMerchant()
        {
            $.ajax({
                url: "<?php echo site_url('transaction/ticket_summary/getMerchant') ?>",
                type: "POST",
                dataType: "json",
                success: function(data) {   
                    // console.log(data)                               
                    let optionData=`<option value='' >Pilih</option>`;
                    data.forEach(element => {
                        optionData += ` <option value='${element.merchant_id}' >${element.merchant_name}</option> `;
                    });

                    let html =`
                        <div class="input-group-addon">Merchant</div>
                        <select class='form-control select2 ' id="merchant" >
                            ${ optionData}
                        </select>`

                    $(".divMerchant").html(html);
                    $("#merchant").on("change",function(){ 
                        $(".divOutlet").show();                       
                        myData.getOutletId($(this).val())                        
                    })
                    $(".select2").select2();
                }
            })            
        }

        getOutletId(merchantId){            
            $.ajax({
                url: "<?php echo site_url('transaction/ticket_summary/getOutletId') ?>",
                type: "POST",
                dataType: "json",
                data:{merchantId:merchantId},
                success: function(data) {   
                    console.log(data)                               
                    let optionData=`<option value='' >Pilih</option>`;
                    data.forEach(element => {
                        optionData += ` <option value='${element.outlet_id}' >${element.outlet_id}</option> `;
                    });

                    let html =`
                        <div class="input-group-addon">Outlet Id</div>
                        <select class='form-control select2 ' id="outletId" >
                        ${optionData}
                        </select>                      
                    `

                    $(".divOutlet").html(html);                                    
                    $(".select2").select2();

                }
            })   

        }
    }
</script>