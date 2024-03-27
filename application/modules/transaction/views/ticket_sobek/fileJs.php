<script type="text/javascript">

    function sendData(url,data){
        $.ajax({
            url         : url,
            data        : data,
            type        : 'POST',
            dataType    : 'json',

            beforeSend: function(){
                unBlockUiId('box')
            },

            success: function(json) {
                if(json.code == 1){
                    // unblockID('#form_edit');
                    closeModal();
                    toastr.success(json.message, 'Sukses');

                    $('#dataTables').DataTable().ajax.reload( null, false );
                    $('#dataTables2').DataTable().ajax.reload( null, false );
                        
                }
                else
                {
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
    	
	function getShift()
	{
		$.ajax({
			url:"<?php echo site_url()?>transaction/ticket_sobek/get_shift",
			data:"port="+$("#port").val(),
			type:"post",
			dataType:"json",
			beforeSend:function()
			{
				unBlockUiId('box')
			},
			success:function(x)
			{
				// console.log(x);

				var html="<option value=''>Pilih</option>"
				if(x.length>0)
				{
					for(var i=0; i<x.length; i++ )
					{
						html +="<option value='"+x[i].shift_id+"'>"+x[i].shift_name+"</option>"
					}
				}
				else
				{
					html +="";
				}

				// console.log(html)
				$("#shift").html(html);
				$("#ob_code").html("<option value=''>Pilih</option>");
			},
			complete:function()
			{
				$('#box').unblock(); 
			}

		});
	}

	function getOb()
	{
		$.ajax({
			url:"<?php echo site_url()?>transaction/ticket_sobek/get_ob",
			data:"trx_date="+formatDate($("#trx_date").val())+"&shift="+$("#shift").val()+"&port="+$("#port").val(),
			type:"post",
			dataType:"json",
			beforeSend:function()
			{
				unBlockUiId('box')
			},
			success:function(x)
			{
				// console.log(x);

				var html="<option value=''>Pilih</option>"
				if(x.length>0)
				{
					for(var i=0; i<x.length; i++ )
					{
						html +="<option value='"+x[i].ob_code+"'>"+x[i].all_name+"</option>"
					}
				}
				else
				{
					html +="";
				}

				// console.log(html)
				$("#ob_code").html(html);
			},
			complete:function()
			{
				$('#box').unblock(); 
			}

		});
	}

	function getPort()
	{
		$.ajax({
			url:"<?php echo site_url()?>transaction/ticket_sobek/get_port",
			data:"route="+$("#route").val(),
			type:"post",
			dataType:"json",
			beforeSend:function()
			{
				unBlockUiId('box')
			},
			success:function(x)
			{
				// console.log(x);

				var route=x.route
				var shift=x.shift
				var gotRoute="<option value='"+route['origin']+"'>"+route['origin_name']+"</option>";
				

				var gotShift="<option value=''>Pilih</option>"
				if(shift.length>0)
				{
					for(var i=0; i<shift.length; i++ )
					{
						gotShift +="<option value='"+shift[i].shift_id+"'>"+shift[i].shift_name+"</option>"
					}
				}
				else
				{
					gotShift +="";
				}

				// console.log(gotShift);

				$("#port").html(gotRoute);	
				$("#shift").html(gotShift);	
				$("#ob_code").html("<option value=''>Pilih</option>");				

			},
			complete:function()
			{
				$('#box').unblock(); 
			}

		});
	}	

	function getService()
	{
		$.ajax({
			url:"<?php echo site_url()?>transaction/ticket_sobek/get_service",
			data:"service="+$("#service").val(),
			type:"post",
			dataType:"json",
			beforeSend:function()
			{
				unBlockUiId('box')
			},
			success:function(x)
			{
				switch(x) {
				  case 'p':
				  	var html= get_input_penumpang();
				  	$("#input_ticket").html(html);	
				    break;
				  case 'v':
				  	var html= get_input_kendaraan();
				  	$("#input_ticket").html(html);	
				    break;				    
				  default:
				  $("#input_ticket").html("");
				}

				$('.select2:not(.normal)').each(function () {
		            	$(this).select2({
		                dropdownParent: $(this).parent()
		            });
		        });		
			},
			complete:function()
			{
				$('#box').unblock(); 
			}

		});
	}


	function get_input_penumpang()
	{
		var html=""


		html+="<div class='col-sm-4 form-group'>"
		+"    <label>Nomer Ticket Manual<span class='wajib'>*</span></label>"
		+"    <input type='text' class='form-control' required name='ticket_manual' id='ticket_manual' placeholder='Tiket Manual'>"
		+"</div>"

		+"<div class='col-sm-4 form-group'>"
		+"    <label>Nama Penumpang<span class='wajib'>*</span></label>"
		+"    <input type='text' class='form-control' required name='passanger_name' id='passanger_name' placeholder='Nama Penumpang'>"
		+"</div>"

		+"<div class='col-sm-4 form-group'>"
		+"    <label>Jenis Kelamin<span class='wajib'>*</span></label>"
		+"    <select type='text' class='form-control select2' required name='gender' id='gender' >"
		+"    	<option value=''>Pilih</option>"
		+"    	<option value='L'>LAKI - LAKI</option>"
		+"    	<option value='P'>PEREMPUAN</option>"
		+"    </select>"
		+"</div>"
		+"<div class='col-sm-12 form-group'></div>"	
		+"<div class='col-sm-4 form-group'>"
		+"    <label>Tipe Penumpang<span class='wajib'>*</span></label>"
		+"    <select type='text' class='form-control select2' required name='passanger_type' id='passanger_type' >"
		+"    	<option value=''>Pilih</option><?php foreach($passanger_type as $key=>$value ) { ?>"
		+"<option value='<?php echo $this->enc->encode($value->id) ?>'><?php echo strtoupper($value->name) ?></option><?php } ?>"	
		+"    </select>"
		+"</div>		"	

		+"<div class='col-sm-4 form-group'>"
		+"    <label>Tipe<span class='wajib'>*</span></label>"
		+"    <select type='text' class='form-control select2' required name='ship_class' id='ship_class' >"
		+"    	<option value=''>Pilih</option><?php foreach($ship_class as $key=>$value ) { ?>"
		+"<option value='<?php echo $this->enc->encode($value->id) ?>'><?php echo strtoupper($value->name) ?></option><?php } ?>"	
		+"    </select>"
		+"</div>		"
		
		+"<div class='col-sm-4 form-group'>"
		+"    <label>Alamat <span class='wajib'>*</span></label>"
		+"    <input type='text' class='form-control' required name='address' id='address' placeholder='Alamat'>"
		+"</div>"


		  	
	  	return html;

	}	


	function get_input_kendaraan()
	{
		var html=""

		html+="<div class='col-sm-4 form-group'>"
		+"    <label>Nomer Ticket Manual<span class='wajib'>*</span></label>"
		+"    <input type='text' class='form-control' required name='ticket_manual' id='ticket_manual' placeholder='Tiket Manual'>"
		+"</div>"
		+"<div class='col-sm-4 form-group'>"
		+"    <label>Nama Pengemudi<span class='wajib'>*</span></label>"
		+"    <input type='text' class='form-control' required name='passanger_name' id='passanger_name' placeholder='Nama Penumpang'>"
		+"</div>"

		+"<div class='col-sm-4 form-group'>"
		+"    <label>Jenis Kelamin <span class='wajib'>*</span></label>"
		+"    <select type='text' class='form-control select2' required name='gender' id='gender' >"
		+"    	<option value=''>Pilih</option>"
		+"    	<option value='L'>LAKI - LAKI</option>"
		+"    	<option value='P'>PEREMPUAN</option>"
		+"    </select>"
		+"</div>"

		+"<div class='col-sm-12 form-group'></div>"				

		+"<div class='col-sm-4 form-group'>"
		+"    <label>Golongan Kendaraan<span class='wajib'>*</span></label>"
		+"    <select type='text' class='form-control select2' required name='vehicle_type' id='vehicle_type' >"
		+"    	<option value=''>Pilih</option><?php foreach($vehicle_class as $key=>$value ) { ?>"
		+"<option value='<?php echo $this->enc->encode($value->id) ?>'><?php echo strtoupper($value->name) ?></option><?php } ?>"	
		+"    </select>"
		+"</div>		"


		+"<div class='col-sm-4 form-group'>"
		+"    <label>Tipe<span class='wajib'>*</span></label>"
		+"    <select type='text' class='form-control select2' required name='ship_class' id='ship_class' >"
		+"    	<option value=''>Pilih</option><?php foreach($ship_class as $key=>$value ) { ?>"
		+"<option value='<?php echo $this->enc->encode($value->id) ?>'><?php echo strtoupper($value->name) ?></option><?php } ?>"	
		+"    </select>"
		+"</div>		"

		+"<div class='col-sm-4 form-group'>"
		+"    <label>Nomer Plat<span class='wajib'>*</span></label>"
		+"    <input type='text' class='form-control' required name='plat_no' id='plat_no' placeholder='Nomer Plat'>"
		+"</div>"		

		+"<div class='col-sm-12 form-group'></div>"		

		+"<div class='col-sm-4 form-group'>"
		+"    <label>Total Penumpang<span class='wajib'>*</span></label>"
		+"    <input type='text' class='form-control' required name='total_passanger' id='total_passanger' placeholder='Total Penumpang'  onkeypress='return isNumberKey(event)' >"
		+"</div>"				

		  	
	  	return html;

	}	

	function formatDate(dateString)
    {
		var date2 = dateString;
		date2 = date2.split("-").reverse().join("-");

		return date2
    }
	

</script>
