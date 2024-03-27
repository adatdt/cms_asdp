
<script type="text/javascript">

class MyData {

	constructor(ticketNumber=null) {
    	this.ticketNumber = ticketNumber;
  	}

	get searchData(){

		$.ajax({
			type :"POST",
			data :"ticketNumber="+this.ticketNumber,
			dataType:"json",
			url:"<?php echo site_url()?>transaction2/ticket_additional/getData",
            beforeSend: ()=>{
                unBlockUiId('box')
            },            
			success: (x)=>{


                var dataForm ='<div class="row">\
                        <div class="col-sm-6 form-group">\
                            <label>Waktu Perpanjang /Jam<span class="wajib"> *</span></label>\
                            <input type="text" name="time" class="form-control" placeholder="Waktu Perpanjang" required>'

                var btnSave='<?php echo $btnSave ?>';
                

				if(x.code==1)
				{
					if(x.service=='pnp')
					{
						var html= this.detailPassanger(x.data);

                        dataForm +='<input type="hidden" value="'+x.data[0].ticket_number+'" name="ticket_number">\
                                    <input type="hidden" value="pnp" name="service">\
                                    </div></div>';

						$("#myDetail").html(html);
                        

					}
					else
					{
						var html= this.detailVehicle(x.data[0],x.data[1]);

                        dataForm +='<input type="hidden" value="'+x.data[1].ticket_number+'" name="ticket_number" > \
                                    <input type="hidden" value="knd" name="service">\
                                    </div></div>';

						$("#myDetail").html(html);

					}

                    $("#btnSave").html(btnSave);
                    $("#dataForm").html(dataForm);
				}
				else
				{
					$("#myDetail").html("");
				}

				console.log(x);
			},
            error: function() {
                toastr.error('Silahkan Hubungi Administrator', 'Gagal');
            },

            complete: function(){
                $('#box').unblock(); 
            }            
		})
	}

	detailPassanger(data)
	{

			console.log(data);
		    var html=   '<div class="col-md-6 ">\
		    				<label><b>Data Penumpang</b></label>\
                            <div class="form-group my_border"><p></p><div class="scrolling" >'

                            for(var i=0; i<data.length; i++) {

								var name = data[i].name==null?"-":data[i].name;
								var ticket_number = data[i].ticket_number==null?"-":data[i].ticket_number;
								var id_number = data[i].id_number==null?"-":data[i].id_number;
								var identitas_name = data[i].identitas_name==null?"-":data[i].identitas_name;
								var phone_number = data[i].phone_number==null?"-":data[i].phone_number;
								var booking_code = data[i].booking_code==null?"-":data[i].booking_code;
								var service_name = data[i].service_name==null?"-":data[i].service_name;
								var gender = data[i].gender==null?"-":data[i].gender;
								var passanger_type_name = data[i].passanger_type_name==null?"-":data[i].passanger_type_name;

							if(i>0)
							{
							html +='<div class="col-md-12">--------------------------------------------------------</div>'								
							}

                            html +='<label class="col-md-3 control-label">Nama</label>\
                                <div class="col-md-1">:</div>\
                                <div class="col-md-8">'+name+'</div>\
                                							\
                                <div class="col-md-12"></div>\
                                								\
                                <label class="col-md-3 control-label">Nomer Ticket</label>\
                                <div class="col-md-1">:</div>\
                                <div class="col-md-8">'+ticket_number+'</div>\
                                							\
                                <div class="col-md-12"></div>\
                                								\
                                <label class="col-md-3 control-label">No Identitas</label>\
                                <div class="col-md-1">:</div>\
                                <div class="col-md-8">'+id_number+'</div>\
                                							\
                                <div class="col-md-12"></div>\
                                								\
                                <label class="col-md-3 control-label">Jenis Identitas</label>\
                                <div class="col-md-1">:</div>\
                                <div class="col-md-8">'+identitas_name+'</div>\
                                							\
                                <div class="col-md-12"></div>\
                                <label class="col-md-3 control-label">Nomer Telpon</label>\
                                <div class="col-md-1">:</div>\
                                <div class="col-md-8">'+phone_number+'</div>\
                                							\
                                <div class="col-md-12"></div>\
                                								\
                                <label class="col-md-3 control-label">Kode Booking</label>\
                                <div class="col-md-1">:</div>\
                                <div class="col-md-8">'+booking_code+'</div>\
                                							\
                                <div class="col-md-12"></div>\
                                								\
                                <label class="col-md-3 control-label">Servis</label>\
                                <div class="col-md-1">:</div>\
                                <div class="col-md-8">'+service_name+'</div>\
                                							\
                                <div class="col-md-12"></div>\
                                								\
                                <label class="col-md-3 control-label">Jenis Kelamin</label>\
                                <div class="col-md-1">:</div>\
                                <div class="col-md-8">'+gender+'</div>  \
                                							\
                                <div class="col-md-12"></div>\
                                								\
                                <label class="col-md-3 control-label">Tipe Pnp</label>\
                                <div class="col-md-1">:</div>\
                                <div class="col-md-8">'+passanger_type_name+'</div>\
                                <div class="col-md-12"></div>'
                             }
                            html +='</div> </div>   \
                        </div><div class="col-md-6 " id="dataForm" ></div> <div class="col-md-12 " id="btnSave" ></div>'

		return html;                            
	}

	detailVehicle(dataPassanger, dataVehicle)
	{
		var driver_name = dataVehicle.driver_name==null?"-":dataVehicle.driver_name;
		var booking_code = dataVehicle.booking_code==null?"-":dataVehicle.booking_code;
		var ticket_number = dataVehicle.ticket_number==null?"-":dataVehicle.ticket_number;
		var id_number = dataVehicle.id_number==null?"-":dataVehicle.id_number;
		var vehicle_class_name = dataVehicle.vehicle_class_name==null?"-":dataVehicle.vehicle_class_name;
		var ship_class_name = dataVehicle.ship_class_name==null?"-":dataVehicle.ship_class_name;
		var total_passanger = dataVehicle.total_passanger==null?"-":dataVehicle.total_passanger;
		

	    var html=   '<div class="col-md-6 ">\
	    				<label><b>Data Kendaraan</b></label>\
                        <div class="form-group my_border">\
                            <label class="col-md-3 control-label">Nama Driver</label>\
                            <div class="col-md-1">:</div>\
                            <div class="col-md-8">'+driver_name+'</div>\
                            							\
                            <div class="col-md-12"></div>\
                            								\
                            <label class="col-md-3 control-label">Kode Booking</label>\
                            <div class="col-md-1">:</div>\
                            <div class="col-md-8">'+booking_code+'</div>\
                            							\
                            <div class="col-md-12"></div>\
                            								\
                            <label class="col-md-3 control-label">Nomer Ticket</label>\
                            <div class="col-md-1">:</div>\
                            <div class="col-md-8">'+ticket_number+'</div>\
                            							\
                            <div class="col-md-12"></div>\
                            								\
                            <label class="col-md-3 control-label">Plat Nomer</label>\
                            <div class="col-md-1">:</div>\
                            <div class="col-md-8">'+id_number+'</div>\
                            							\
                            <div class="col-md-12"></div>\
                            								\
                            <label class="col-md-3 control-label">Golongan</label>\
                            <div class="col-md-1">:</div>\
                            <div class="col-md-8">'+vehicle_class_name+'</div>\
                            							\
                            <div class="col-md-12"></div>\
                            <label class="col-md-3 control-label">Kelas Kapal</label>\
                            <div class="col-md-1">:</div>\
                            <div class="col-md-8">'+ship_class_name+'</div>\
                            <div class="col-md-12"></div>\
                            <label class="col-md-3 control-label">Total PNP</label>\
                            <div class="col-md-1">:</div>\
                            <div class="col-md-8">'+total_passanger+'</div>\
                        </div>        \
                    </div>'

			// for(var i=0; i<dataPassanger.length; i++)
			// {
				html +=this.detailPassanger(dataPassanger);
			// }


		return html;		
	}

}	


$(document).ready(()=>{

	$("#cari").click(()=>{
		var search = $("#search").val();
		let myData= new MyData(search);
		myData.searchData;
	})

})

</script>