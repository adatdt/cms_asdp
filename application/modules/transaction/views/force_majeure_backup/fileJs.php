<script type="text/javascript">

	function getDataTicket()
	{
		$.ajax({
			type:"post",
			url:"<?php echo site_url() ?>transaction/force_majeure/get_data_ticket",
			data:"search="+$("#search").val(),
			dataType:"json",
			beforeSend:function(){
                unBlockUiId('box')
            },
			success:function(x)
			{
				// console.log(x);

				if(x[1].code == 1){

					$("[name='ticket_number']").val(x[0].ticket_number);
					$("[name='booking_code']").val(x[0].booking_code);
					$("[name='name']").val(x[0].name);
					$("[name='gender']").val(x[0].gender);
					$("[name='service']").val(x[0].service);
					$("[name='passanger_type']").val(x[0].type);


					if(x[0].plat_no!="")
					{
						var appen="<label> No Plat</label><input type='text' value='"+x[0].plat_no+"' name='plat_no' class='form-control' placeholder='Tipe Penumpang' readonly >";

						$("#plat").html(appen);
						$("#label").html("Golongan");
					}
					else
					{
						$("#plat").html("");
						$("#label").html("Tipe Penumpang");
					}
				}
				else
				{
					toastr.error(x[1].message, 'Gagal');
					console.log(x[1].message);

				}
			},
			complete: function(){
                $('#box').unblock(); 
            }
		});
	}

	$(document).ready(function(){
		$("#cari").on("click",function(){
			getDataTicket();
		});

		$("[name='search']").on('keypress',function(e) {
    		if(e.which == 13) {
        		getDataTicket();
    		}
		});
	});
	
</script>