<script type="text/javascript">

	function hanyaAngka(evt) {
		  var charCode = (evt.which) ? evt.which : event.keyCode
		   if (charCode > 31 && (charCode < 48 || charCode > 57))
		    return false;
		  return true;
	}

	function postData2(url, data, y) {
		$.ajax({
			url: url,
			data: data,
			type: 'POST',
			dataType: 'json',

			beforeSend: function () {
				unBlockUiId('box')
			},

			success: function (json) {
				if (json.code == 1) {
					// unblockID('#form_edit');
					closeModal();
					toastr.success(json.message, 'Sukses');
					if (y) {
						$('#grid').treegrid('reload');
						// ambil_data();
					}
					else {
						$('#dataTables').DataTable().ajax.reload(null, false);
						$('#dataTables2').DataTable().ajax.reload(null, false);
						// ambil_data();

					}
				} else {
					toastr.error(json.message, 'Gagal');
				}
			},

			error: function () {
				toastr.error('Silahkan Hubungi Administrator', 'Gagal');
			},

			complete: function () {
				$('#box').unblock();
			}
		});
	}	
	function getData()
	{
		$.ajax({
			data:"ticket_number="+encodeURIComponent($("[name='search']").val()),
			type:"post",
			url:"<?php echo site_url() ?>transaction/extend_ticket/search_data",
			dataType:"json",
			beforeSend: function(){
				unBlockUiId('box')
			},
			success:function (x)
			{
				switch(x.code){
					case 1 :
						console.log(x);

						$("#statusExpired").remove();

						$("[name='ticket_number']").val(x.data.ticket_number);
						$("[name='booking_code']").val(x.data.booking_code);
						$("[name='name']").val(x.data.name);
						$("[name='service']").val(x.data.service_name);
						$("[name='gender']").val(x.data.gender);
						$("[name='passanger_type']").val(x.data.passanger_type_name);
						$("[name='id_number']").val(x.data.id_number);
						$("[name='ship_class']").val(x.data.ship_class_name);
						$("[name='ticket']").val(x.data.ticket_number);

						if(x.service=='kendaraan')
						{
							$("#id_number").html("Nomer Plat");
						}
						else
						{
							$("#id_number").html("Id Number");
						}


						const expiredType= `<div class="col-sm-4 form-group" id="statusExpired"> <label >${x.status}</label>
                            <input type="text" name="expired" class="form-control" placeholder="yyyy-mm-dd" readonly value="${x.expiredDate}"> <div>`

                        $(expiredType).insertAfter("#shipType");

					break;
					default:
					toastr.error(x.message, 'Gagal');

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
 
	$(document).ready(function(){

		$("#cari").click(function(){
			getData()
		});

		$("#search").on('keypress',function(e) {
		    if(e.which == 13) {
		        getData()
		    }
		});

	});
</script>