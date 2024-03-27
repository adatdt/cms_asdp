<script type="text/javascript">

    var getData =
    {
        dataTicket: ()=>{
            $.ajax({
                data :"port="+$("[name='port']").val()+"&trx_date="+$("[name='trx_date']").val()+"&trx_date2="+$("[name='trx_date2']").val()+"&service="+$("[name='service']").val()+"&ship_class="+$("[name='search_ship_class']").val(),
                type :"post",
                url : "<?php echo site_url()?>transaction/boarding_ticket_manual/data_ticket",
                dataType: "json",
                beforeSend:function(){
                    unBlockUiId('box')
                },                
                success : (x)=>{
                    switch(x.code)
                    {
                        case 1 :

                            console.log(x.port);
                            if(x.service=='pnp')
                            {
                                var html= getData.dataListPnp(x.data)
                                var html2= getData.dataListTempPnp(x.schedule)
                                $("#list").html(html);
                                $("#list_temp").html(html2);

                                getData.getDock(x.port)                    
                            }
                            else
                            {
                                var html= getData.dataListKnd(x.data)
                                var html2= getData.dataListTempKnd(x.schedule)
                                $("#list").html(html);
                                $("#list_temp").html(html2);

                                getData.getDock(x.port)
                            }

                            // var scheduleHtml = getData.dataSchedule(x.schedule)
                            // $("#schedule").html(scheduleHtml);

                            $('#search_schedule_date').val(x.trx_date);
                            $('#search_schedule_date2').val(x.trx_date);

                            getData.tableSetting("#t_manual")
                            getData.tableSetting("#t_manual_temp")
                            getData.tableSetting("#t_schedule")

                        break;
                        default :
                        // console.log('data tidak di temukan')
                        toastr.error(x.massege, 'Gagal');

                    }

                    $('.date').datepicker({
                        format: 'yyyy-mm-dd',
                        changeMonth: true,
                        changeYear: true,
                        autoclose: true,
                        endDate: new Date(),
                        startDate: '<?= $startDate ?>'
                    }); 

                    $('.select2:not(.normal)').each(function () {
                        $(this).select2({
                            dropdownParent: $(this).parent()
                        });
                    });

                    $("#portSchedule").on("change",function(){

                        // alert("haloo");
                        getData.getDock($(this).val())
                    })                    

                },
                complete: function(){
                    $('#box').unblock(); 
                }                 
            });
        },
        dataListPnp:(data)=>{
            
            var html="<div class='portlet box blue'>\
            <div class='portlet-title'>\
                <div class='caption'>Data Ticket Manual Yang Belum Bording</div>\
                <div class='tools'>\
                </div>\
                <div class='actions'>\
                </div>\
            </div>\
            <div class='portlet-body' style='display: block;'>\
            <table class='table-bordered table-hover' id='t_manual' ><thead>\
                        <tr>\
                            <th>TANGGAL INPUT</th>\
                            <th>TANGGAL TRANSAKSI</th>\
                            <th>NO INVOICE</th>\
                            <th>NAMA PENUMPANG</th>\
                            <th>NO TICKET MANUAL</th>\
                            <th>NO TICKET BARU</th>\
                            <th>JENIS KELAMIN</th>\
                            <th>ALAMAT</th>\
                            <th>PENJUAL</th>\
                            <th>SHIFT</th>\
                            <th>TIPE KAPAL</th>\
                            <th>PELABUHAN</th>\
                            <th>AKSI</th>\
                        </tr>\
                    </thead>\
                    <tbody>"
            // console.log(data);
            for(var i=0; i<data.length; i++)
            {
                var id ="row"+no;
                html +="<tr id="+id+" >\
                    <td>"+convert.formatDateTime(data[i].created_on)+"</td>\
                    <td>"+convert.formatDate(data[i].trx_date)+"</td>\
                    <td>"+data[i].trans_number+"</td>\
                    <td>"+data[i].name+"</td>\
                    <td>"+data[i].ticket_number_manual+"</td>\
                    <td>"+data[i].ticket_number+"</td>\
                    <td>"+data[i].gender+"</td>\
                    <td>"+data[i].address+"</td>\
                    <td>"+data[i].username+"</td>\
                    <td>"+data[i].shift_name+"</td>\
                    <td>"+data[i].ship_class_name+"</td>\
                    <td>"+data[i].port_name+"</td>\
                    <td>\
                    <button onClick=removeRow('"+id+"')  class='btn btn-sm btn-warning' title='Tambah'><i class='fa fa-plus'></i> Tampung</button>\
                    </td>\
                </tr>"
                no++;
            }

            html +="</tbody></table>";
            html +="</div></div>"


            return html;   
        },
        dataListTempPnp:(data)=>{
            
            var service=$("[name='service']").val();
            var html=`<div class='portlet box blue'>
                <div class='portlet-title'>
                    <div class='caption'>Data Ticket Manual Yang akan Bording</div>
                    <div class='tools'>
                    </div>
                    <div class='actions'>
                    </div>
                </div>
                <div class='portlet-body' style='display: block;'>
                     <div class='row'>
                        <div class='col-sm-12 form-inline'>
                            <div class='input-group select2-bootstrap-prepend'>
                                <div class='input-group-addon'>Tanggal Jadwal</div>
                                <input type='text' class='form-control date input-small' id='search_schedule_date'  readonly placeholder='YYYY-MM-DD'>
                                <div class='input-group-addon'>S/d</div>
                                <input type='text' class='form-control date input-small' id='search_schedule_date2'  readonly placeholder='YYYY-MM-DD'>                                
                            </div>

                            <!--
                            <div class='input-group select2-bootstrap-prepend'>
                                <div class='input-group-addon'>Pelabuhan</div>
                                <?= form_dropdown("portSchedule",$port,"", " class='form-control date input-small select2' id='portSchedule' ") ?>
                            </div>
                            -->

                            <div class='input-group select2-bootstrap-prepend'>
                                <div class='input-group-addon'>Kategori</div>
                                <select name="categorySchedule" id="categorySchedule" class='form-control  input-small select2' >
                                    <option value="">Pilih</option>
                                    <option value="M">Jadwal Manual</option>
                                    <option value="B">Jadwal Berjalan</option>
                                </select>                   
                            </div>

                            <div class='input-group select2-bootstrap-prepend'>
                                <div class='input-group-addon'>Dermaga</div>
                                <select name="dockSchedule" id="dockSchedule" class='form-control date input-small select2' >
                                    <option value="">Pilih</option>
                                </select>                                
                                <span class='input-group-btn'>
                                    <button class='btn btn-primary' type='button' id='cari_jadwal' onClick=getData.schedule() >Cari</button>
                                </span>
                            </div>

                     </div> <p></p>`;
            html+="<div class='row'>\
            <div class='col-md-6 my_border form-group' id='mySchedule'>\
            <table class='table table-bordered table-hover' id='t_schedule' ><thead>\
                        <tr>\
                            <th>KODE JADWAL</th>\
                            <th>TANGGAL <br>SANDAR</th>\
                            <th>TANGGAL <br>BERLAYAR</th>\
                            <th>PELABUHAN</th>\
                            <th>DERMAGA</th>\
                            <th>KAPAL</th>\
                            <th>TIPE KAPAL</th>\
                            <th>AKSI</th>\
                        </tr>\
                    </thead>\
                    <tbody>"
                    // console.log(data);
        
                    for(var i=0; i<data.length; i++)
                    {  
                        // console.log(data[i].sail_date);
                        var trSceduleId="trSchedule"+no;              
                        html +="<tr id='"+trSceduleId+"' >\
                            <td>"+data[i].schedule_code+"</td>\
                            <td>"+convert.formatDateTime(data[i].docking_date)+"</td>\
                            <td>"+convert.formatDateTime(data[i].sail_date)+"</td>\
                            <td>"+data[i].port_name+"</td>\
                            <td>"+data[i].dock_name+"</td>\
                            <td>"+data[i].ship_name+"</td>\
                            <td>"+data[i].name+"</td>\
                            <td>\
                                <div class='mt-checkbox-list'>\
                                <label class='mt-checkbox mt-checkbox-outline'>\
                                    <input type='checkbox'  name='get_schedule' value='"+data[i].schedule_code+"' onClick=addSchedule('"+trSceduleId+"') >\
                                    <span></span>\
                                </label>\
                                </div>\
                            </td>\
                        </tr>"
                        no++;
                    }
            html +="</tbody>\
            </table>\
            </div>\
            <div class='col-md-1'></div>\
            <div class='col-md-5 my_border'>\
                        <legend>Form Jadwal</legend>\
                        <div class='row'>\
                            <div class='col-md-6 form-group'>\
                                <label>Kode Jadwal</label> <span class='wajib'>*</span>\
                                <input type='text' class='form-control' name='schedule_code' readonly placeholder='Kode Jadwal' required>\
                            </div>\
                            <div class='col-md-6 form-group'>\
                                <label>Tanggal Sandar</label>\
                                <input type='text' class='form-control' name='docking_date' readonly placeholder='YYYY-MM-DD'>\
                            </div>\
                            <div class='col-md-12 form-group'></div>\
                            <div class='col-md-6 form-group'>\
                                <label>Tanggal Berlayar</label>\
                                <input type='text' class='form-control' name='sail_date' readonly placeholder='YYYY-MM-DD'>\
                            </div>\
                            <div class='col-md-6 form-group'>\
                                <label>Dermaga</label>\
                                <input type='text' class='form-control' name='dock' readonly placeholder='Dermaga'>\
                            </div>\
                            <div class='col-md-12 form-group'></div>\
                            <div class='col-md-6 form-group'>\
                                <label>Nama Kapal</label>\
                                <input type='text' class='form-control' name='ship_name' readonly placeholder='Nama Kapal'>\
                            </div>\
                            <div class='col-md-6 form-group'>\
                                <label>Tipe Kapal</label>\
                                <input type='text' class='form-control' name='ship_class' readonly placeholder='Tipe Kapal'>\
                                <input type='hidden'  name='service' value="+service+">\
                            </div>\
                            <div class='col-md-12 form-group'></div>\
                            <div class='col-md-6 form-group'>\
                                <label>Pelabuhan</label>\
                                <input type='text' class='form-control' name='portSchedule' readonly placeholder='Pelabuhan'>\
                            </div>\
                        </div>\
            </div>\
            <div class='col-md-12 form-group' ></div>\
            <div class='col-md-12'>\
                    <table class='table-bordered table-hover' id='t_manual_temp' ><thead>\
                                <tr>\
                                    <th>TANGGAL INPUT</th>\
                                    <th>TANGGAL TRANSAKSI</th>\
                                    <th>NO INVOICE</th>\
                                    <th>NAMA PENUMPANG</th>\
                                    <th>NO TICKET MANUAL</th>\
                                    <th>NO TICKET BARU</th>\
                                    <th>JENIS KELAMIN</th>\
                                    <th>ALAMAT</th>\
                                    <th>PENJUAL</th>\
                                    <th>SHIFT</th>\
                                    <th>TIPE KAPAL</th>\
                                    <th>PELABUHAN</th>\
                                    <th>AKSI</th>\
                                </tr>\
                            </thead>\
                            <tbody>\
                    </table>\
                </div>\
                <div class='col-md-12 form-group' ></div>\
                <div class='col-md-12 form-group' id='form_input'>\
                    \
                </div>\
                <div class='col-md-12'>\
                    <div class='box-footer text-right'>\
                        <button type='button' class='btn btn-sm btn-default' onclick='closeModal()'><i class='fa fa-close'></i> Batal</button> \
                        <button type='submit' class='btn btn-sm btn-primary' id='saveBtn'><i class='fa fa-check'></i> Simpan</button>\
                    </div>\
                </div>\
                </div></div>";

            return html;   
        },
        dataListKnd:(data)=>{
            
            var html="<div class='portlet box blue'>\
            <div class='portlet-title'>\
                <div class='caption'>Data Ticket Manual Yang Belum Bording</div>\
                <div class='tools'>\
                </div>\
                <div class='actions'>\
                </div>\
            </div>\
            <div class='portlet-body' style='display: block;'>\
            <table class='table-bordered table-hover' id='t_manual' ><thead>\
                        <tr>\
                            <th>TANGGAL INPUT</th>\
                            <th>TANGGAL TRANSAKSI</th>\
                            <th>NO INVOICE</th>\
                            <th>NAMA PENUMPANG</th>\
                            <th>NO TICKET MANUAL</th>\
                            <th>NO TICKET BARU</th>\
                            <th>NO PLAT</th>\
                            <th>GOLONGAN</th>\
                            <th>PENJUAL</th>\
                            <th>SHIFT</th>\
                            <th>TIPE KAPAL</th>\
                            <th>PELABUHAN</th>\
                            <th>AKSI</th>\
                        </tr>\
                    </thead>\
                    <tbody>"
            // console.log(data);
         
            for(var i=0; i<data.length; i++)
            {
                var id ="row"+no;
                html +="<tr id="+id+" >\
                    <td>"+convert.formatDateTime(data[i].created_on)+"</td>\
                    <td>"+convert.formatDate(data[i].trx_date)+"</td>\
                    <td>"+data[i].trans_number+"</td>\
                    <td>"+data[i].name+"</td>\
                    <td>"+data[i].ticket_number_manual+"</td>\
                    <td>"+data[i].ticket_number+"</td>\
                    <td>"+data[i].id_number+"</td>\
                    <td>"+data[i].vehicle_class_name+"</td>\
                    <td>"+data[i].username+"</td>\
                    <td>"+data[i].shift_name+"</td>\
                    <td>"+data[i].ship_class_name+"</td>\
                    <td>"+data[i].port_name+"</td>\
                    <td>\
                    <button onClick=removeRow('"+id+"')  class='btn btn-sm btn-warning' title='Tambah'><i class='fa fa-plus'></i> Tampung</button>\
                    </td>\
                </tr>"
                no++;
            }

            html +="</tbody></table>";
            html +="</div></div>"


            return html;   
        },
        dataListTempKnd:(data)=>{
            var service=$("[name='service']").val();
            var html=`<div class='portlet box blue'>
                <div class='portlet-title'>
                    <div class='caption'>Data Ticket Manual Yang Yang akan Bording</div>
                    <div class='tools'>
                    </div>
                    <div class='actions'>
                    </div>
                </div>
                <div class='portlet-body' style='display: block;'>
                    <div class='row'>
                        <div class='col-sm-12 form-inline'>
                            <div class='input-group select2-bootstrap-prepend'>
                                <div class='input-group-addon'>Tanggal Jadwal</div>
                                <input type='text' class='form-control date input-small' id='search_schedule_date'  readonly placeholder='YYYY-MM-DD'>
                                <div class='input-group-addon'>S/d</div>
                                <input type='text' class='form-control date input-small' id='search_schedule_date2'  readonly placeholder='YYYY-MM-DD'>                                
                            </div>

                            <!--
                            <div class='input-group select2-bootstrap-prepend'>
                                <div class='input-group-addon'>Pelabuhan</div>
                                <?= form_dropdown("portSchedule",$port,"", " class='form-control date input-small select2' id='portSchedule' ") ?>
                            </div>
                            -->

                            <div class='input-group select2-bootstrap-prepend'>
                                <div class='input-group-addon'>Kategori</div>
                                <select name="categorySchedule" id="categorySchedule" class='form-control  input-small select2' >
                                    <option value="">Pilih</option>
                                    <option value="M">Jadwal Manual</option>
                                    <option value="B">Jadwal Berjalan</option>
                                </select>                   
                            </div>                            

                            <div class='input-group select2-bootstrap-prepend'>
                                <div class='input-group-addon'>Dermaga</div>
                                <select name="dockSchedule" id="dockSchedule" class='form-control date input-small select2' >
                                    <option value="">Pilih</option>
                                </select>                                
                                <span class='input-group-btn'>
                                    <button class='btn btn-primary' type='button' id='cari_jadwal' onClick=getData.schedule() >Cari</button>
                                </span>
                            </div>                            

                        </div>
                    </div> <p></p>`
            html +="<div class='row'>\
            <div class='col-md-6 my_border' id='mySchedule'>\
            <table class='table table-bordered table-hover' id='t_schedule' ><thead>\
                        <tr>\
                            <th>KODE JADWAL</th>\
                            <th>TANGGAL <br> SANDAR</th>\
                            <th>TANGGAL <br> BERLAYAR</th>\
                            <th>PELABUHAN</th>\
                            <th>DERMAGA</th>\
                            <th>KAPAL</th>\
                            <th>TIPE KAPAL</th>\
                            <th>AKSI</th>\
                        </tr>\
                    </thead>\
                    <tbody>"
                    // console.log(data);
        
                    for(var i=0; i<data.length; i++)
                    {  
                        var trSceduleId="trSchedule"+no;              
                        html +="<tr id='"+trSceduleId+"' >\
                            <td>"+data[i].schedule_code+"</td>\
                            <td>"+convert.formatDateTime(data[i].docking_date)+"</td>\
                            <td>"+convert.formatDateTime(data[i].sail_date)+"</td>\
                            <td>"+data[i].port_name+"</td>\
                            <td>"+data[i].dock_name+"</td>\
                            <td>"+data[i].ship_name+"</td>\
                            <td>"+data[i].name+"</td>\
                            <td>\
                                <div class='mt-checkbox-list'>\
                                <label class='mt-checkbox mt-checkbox-outline'>\
                                    <input type='checkbox' name='get_schedule'  value='"+data[i].schedule_code+"' onClick=addSchedule('"+trSceduleId+"') >\
                                    <span></span>\
                                </label>\
                                </div>\
                            </td>\
                        </tr>"
                        no++;
                    }
            html +="</tbody>\
            </table>\
            </div>\
            <div class='col-md-1'></div>\
            <div class='col-md-5 my_border'>\
                        <legend>Form Jadwal</legend>\
                        <div class='row'>\
                            <div class='col-md-6 form-group'>\
                                <label>Kode Jadwal</label> <span class='wajib'>*</span>\
                                <input type='text' class='form-control' name='schedule_code' readonly placeholder='Kode Jadwal' required>\
                            </div>\
                            <div class='col-md-6 form-group'>\
                                <label>Tanggal Sandar</label>\
                                <input type='text' class='form-control' name='docking_date' readonly placeholder='YYYY-MM-DD'>\
                            </div>\
                            <div class='col-md-12 form-group'></div>\
                            <div class='col-md-6 form-group'>\
                                <label>Tanggal Berlayar</label>\
                                <input type='text' class='form-control' name='sail_date' readonly placeholder='YYYY-MM-DD'>\
                            </div>\
                            <div class='col-md-6 form-group'>\
                                <label>Dermaga</label>\
                                <input type='text' class='form-control' name='dock' readonly placeholder='Dermaga'>\
                            </div>\
                            <div class='col-md-12 form-group'></div>\
                            <div class='col-md-6 form-group'>\
                                <label>Nama Kapal</label>\
                                <input type='text' class='form-control' name='ship_name' readonly placeholder='Nama Kapal'>\
                            </div>\
                            <div class='col-md-6 form-group'>\
                                <label>Tipe Kapal</label>\
                                <input type='text' class='form-control' name='ship_class' readonly placeholder='Tipe Kapal'>\
                                <input type='hidden'  name='service' value="+service+">\
                            </div>\
                            <div class='col-md-12 form-group'></div>\
                            <div class='col-md-6 form-group'>\
                                <label>Pelabuhan</label>\
                                <input type='text' class='form-control' name='portSchedule' readonly placeholder='Pelabuhan'>\
                            </div>\
                        </div>\
            </div>\
            <div class='col-md-12 form-group' ></div>\
            <div class='col-md-12'>\
                    <table class='table-bordered table-hover' id='t_manual_temp' ><thead>\
                                <tr>\
                                    <th>TANGGAL INPUT</th>\
                                    <th>TANGGAL TRANSAKSI</th>\
                                    <th>NO INVOICE</th>\
                                    <th>NAMA PENUMPANG</th>\
                                    <th>NO TICKET MANUAL</th>\
                                    <th>NO TICKET BARU</th>\
                                    <th>NO PLAT</th>\
                                    <th>GOLONGAN</th>\
                                    <th>PENJUAL</th>\
                                    <th>SHIFT</th>\
                                    <th>TIPE KAPAL</th>\
                                    <th>PELABUHAN</th>\
                                    <th>AKSI</th>\
                                </tr>\
                            </thead>\
                            <tbody>\
                    </table>\
                </div>\
                <div class='col-md-12 form-group' ></div>\
                <div class='col-md-12 form-group' id='form_input'>\
                <div class='col-md-12'>\
                    <div class='box-footer text-right'>\
                        <button type='button' class='btn btn-sm btn-default' onclick='closeModal()'><i class='fa fa-close'></i> Batal</button> \
                        <button type='submit' class='btn btn-sm btn-primary' id='saveBtn'><i class='fa fa-check'></i> Simpan</button>\
                    </div>\
                </div>\
                </div></div>";



            return html;   
        },
        schedule:()=>{

            $(document).ready(function(){

                var data={
                            schedule_date:$("#search_schedule_date").val(),
                            schedule_date2:$("#search_schedule_date2").val(),
                            categorySchedule:$("#categorySchedule").val(),
                            dockSchedule:$("#dockSchedule").val(),
                            ship_class:$("[name='search_ship_class']").val(),
                            port: $("[name='port']").val(),
                        } 

                $.ajax({
                    // data :"schedule_date="+$("#search_schedule_date").val()+"&ship_class="+$("[name='search_ship_class']").val()+"&port="+$("[name='port']").val(),
                    data : data,
                    type :"post",
                    url : "<?php echo site_url()?>transaction/boarding_ticket_manual/get_schedule",
                    dataType: "json",
                    beforeSend:function(){
                        unBlockUiId('mySchedule')
                    },                
                    success : (data)=>{
                        // console.log(data);
                    
                    var html ="<table class='table table-bordered table-hover' id='t_schedule' ><thead>\
                                    <tr>\
                                        <th>KODE JADWAL</th>\
                                        <th>TANGGAL <br>SANDAR</th>\
                                        <th>TANGGAL <br>BERLAYAR</th>\
                                        <th>PELABUHAN</th>\
                                        <th>DERMAGA</th>\
                                        <th>KAPAL</th>\
                                        <th>TIPE KAPAL</th>\
                                        <th>AKSI</th>\
                                    </tr>\
                                </thead>\
                                <tbody>"
                                // console.log(data);
                    
                                for(var i=0; i<data.length; i++)
                                {  
                                    var trSceduleId="trSchedule"+no;              
                                    html +="<tr id='"+trSceduleId+"' >\
                                        <td>"+data[i].schedule_code+"</td>\
                                        <td>"+convert.formatDateTime(data[i].docking_date)+"</td>\
                                        <td>"+convert.formatDateTime(data[i].sail_date)+"</td>\
                                        <td>"+data[i].port_name+"</td>\
                                        <td>"+data[i].dock_name+"</td>\
                                        <td>"+data[i].ship_name+"</td>\
                                        <td>"+data[i].name+"</td>\
                                        <td>\
                                            <div class='mt-checkbox-list'>\
                                            <label class='mt-checkbox mt-checkbox-outline'>\
                                                <input type='checkbox' name='get_schedule'  value='"+data[i].schedule_code+"' onClick=addSchedule('"+trSceduleId+"') >\
                                                <span></span>\
                                            </label>\
                                            </div>\
                                        </td>\
                                    </tr>"
                                    no++;
                                }
                        html +="</tbody>\
                        </table>"

                        $("#mySchedule").html(html); 
                        getData.tableSetting("#t_schedule");
                        $("[name='schedule_code']").val("");
                        $("[name='docking_date']").val("");
                        $("[name='sail_date']").val("");
                        $("[name='dock']").val("");
                        $("[name='ship_name']").val("");
                        $("[name='ship_class']").val("");                                          

                    },
                    complete: function(){
                        $('#mySchedule').unblock(); 
                    }                 
                });
            })                        
        },            
        tableSetting : (id)=>{
           
            $(id).dataTable({
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
                "lengthMenu": [
                    [10, 25, 50, 100],
                    [10, 25, 50, 100]
                ], 
                "pageLength": 10,
                "pagingType": "bootstrap_full_number",
                "order": [[ 0, "desc" ]],
            });

        },
        getDock:(id)=>{

            $.ajax({
                data :"id="+id,
                type :"post",
                url : "<?php echo site_url()?>transaction/boarding_ticket_manual/getDock",
                dataType: "json",
                beforeSend:function(){
                    unBlockUiId('box')
                },                
                success : (x)=>{

                    console.log(x);

                    var html=` <option value="" >Pilih</option>`

                    for(var i=0; i<x.length; i++)
                    {
                        html +=`<option value="${x[i].id}" >${x[i].name}</option>`
                    }

                    $("#dockSchedule").html(html);

                },
                complete: function(){
                    $('#box').unblock(); 
                }                 
            });            
        }


    }

    var removeRow = (x) =>
    {
        
        $(document).ready(function(){

            var id="#"+x;
            var newId='row_temp'+no;

            // console.log($(id).find('td:eq(3)').html());
                
            var table = $('#t_manual').DataTable();
            var table_temp = $('#t_manual_temp').DataTable();

            // add table from list to list temp
            var tgl_input           =$(id).find('td:eq(0)').html()
            var trx_date            =$(id).find('td:eq(1)').html()
            var trans_number        =$(id).find('td:eq(2)').html()
            var name                =$(id).find('td:eq(3)').html()
            var ticket_number_manual=$(id).find('td:eq(4)').html()
            var ticket_number       =$(id).find('td:eq(5)').html()
            var gender              =$(id).find('td:eq(6)').html()
            var address             =$(id).find('td:eq(7)').html()
            var seller              =$(id).find('td:eq(8)').html()
            var shift_name          =$(id).find('td:eq(9)').html()
            var shift_class_name    =$(id).find('td:eq(10)').html()
            var port_name           =$(id).find('td:eq(11)').html()

            // console.log(ticket_number);

            table_temp.row.add(
                [
                    tgl_input,
                    trx_date,
                    trans_number,
                    name,
                    ticket_number_manual,
                    ticket_number,
                    gender,
                    address,
                    seller,
                    shift_name,
                    shift_class_name,
                    port_name,
                      "<center><button onClick=addRow('"+newId+"')  class='btn btn-sm btn-danger' title='Hapus'><i class='fa fa-trash-o'></i></button></center>"
                ]
            ).node().id = newId;
            table_temp.draw();
            
            // remove table from list
            table.row(id).remove().draw(false);

            var inputTicketNumber="<div id='input_"+newId+"'' ><input type='hidden' name='ticket_number["+no+"]' value="+ticket_number+">\
            <input type='hidden' name='ticket_number_manual["+no+"]' value="+ticket_number_manual+">\
            <input type='hidden' name='port_name["+no+"]' value="+port_name+"></div>\
            ";
            
            $("#form_input").append(inputTicketNumber) 

            no++;
        })
    }

    var addRow = (x) =>
    {
        
        $(document).ready(function(){

            var id="#"+x;
            var newId='row'+no;
                
            var table = $('#t_manual').DataTable();
            var table_temp = $('#t_manual_temp').DataTable();
            // add table from list to list temp

            table.row.add(
                [ $(id).find('td:eq(0)').html(),
                  $(id).find('td:eq(1)').html(),
                  $(id).find('td:eq(2)').html(),
                  $(id).find('td:eq(3)').html(),
                  $(id).find('td:eq(4)').html(),
                  $(id).find('td:eq(5)').html(),
                  $(id).find('td:eq(6)').html(),
                  $(id).find('td:eq(7)').html(),
                  $(id).find('td:eq(8)').html(),
                  $(id).find('td:eq(9)').html(),
                  $(id).find('td:eq(10)').html(),
                  $(id).find('td:eq(11)').html(),
                  "<button onClick=removeRow('"+newId+"')  class='btn btn-sm btn-warning' title='Tambah'><i class='fa fa-plus'></i> Tampung</button>"
                ]
            ).node().id = newId;
            table.draw();


            // remove table from list
            table_temp.row(id).remove().draw(false);
            
            $("#input_"+x).remove(); 

            no++;
        })
    }

    var addSchedule =(x)=>
    {
        $(document).ready(function(){

            // if($("#radio_"+x).is(':checked')) 
            // {
            //     console.log("ini check");
            // }
            // else
            // {

            // }
            var schedule_code = $("#"+x).find('td:eq(0)').html();
            var docking_date = $("#"+x).find('td:eq(1)').html();
            var sail_date = $("#"+x).find('td:eq(2)').html();
            var dock = $("#"+x).find('td:eq(4)').html();
            var ship_name = $("#"+x).find('td:eq(5)').html();
            var ship_class = $("#"+x).find('td:eq(6)').html();
            var portSchedule = $("#"+x).find('td:eq(3)').html();

            $("[name='schedule_code']").val(schedule_code);
            $("[name='docking_date']").val(convert.formatDateTimeToDateTime(docking_date));
            $("[name='sail_date']").val(convert.formatDateTimeToDateTime(sail_date));
            $("[name='dock']").val(dock);
            $("[name='ship_name']").val(ship_name);
            $("[name='ship_class']").val(ship_class);
            $("[name='portSchedule']").val(portSchedule);


            $('input:checkbox').click(function() {
                $('input:checkbox').not(this).prop('checked', false);

                if($(this).is(':checked'))
                {

                    $("[name='schedule_code']").val(schedule_code);
                    $("[name='docking_date']").val(convert.formatDateTimeToDateTime(docking_date));
                    $("[name='sail_date']").val(convert.formatDateTimeToDateTime(sail_date));
                    $("[name='dock']").val(dock);
                    $("[name='ship_name']").val(ship_name);
                    $("[name='ship_class']").val(ship_class);
                    $("[name='portSchedule']").val(portSchedule);

                }
                else
                {
                    $("[name='schedule_code']").val("");
                    $("[name='docking_date']").val("");
                    $("[name='sail_date']").val("");
                    $("[name='dock']").val("");
                    $("[name='ship_name']").val("");
                    $("[name='ship_class']").val("");
                    $("[name='portSchedule']").val("");                    
                }

            });

        });
    }       

    var convert={

        formatDate:(x)=>{

            if(x==null || x=='')
            {
                return false
            }
            else
            {
                var bulan=['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni' , 'Juli', 'Agustus', 'September','Oktober','November', 'Desember']
                var d = new Date(x);
                var tgl= d.getDate()
                var yy= d.getYear()
                var bln= d.getMonth()
                var thn=(yy < 1000) ? yy + 1900 : yy;

                var myTanggal=tgl.toString().length<2?"0"+tgl:tgl;

                var result =myTanggal+" "+bulan[bln]+" "+thn

                return result;
            }
        },
        formatDateTime:(x)=>{
            if(x==null || x=='')
            {
                return '';
            }
            else
            {
                
                var bulan=['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni' , 'Juli', 'Agustus', 'September','Oktober','November', 'Desember']
                var d = new Date(x);
                var tgl= d.getDate()
                var yy= d.getYear()
                var bln= d.getMonth()
                var jm = d.getHours();
                var mn = d.getMinutes();
                var dt = d.getSeconds().length<2?"0"+d.getSeconds().toString():d.getSeconds().toString();
                var thn=(yy < 1000) ? yy + 1900 : yy;

                var detik = dt.toString().length<2?"0"+dt.toString():dt.toString();
                var menit = mn.toString().length<2?"0"+mn.toString():mn.toString();
                var jam = jm.toString().length<2?"0"+jm.toString():jm.toString();


                var myTanggal=tgl.toString().length<2?"0"+tgl:tgl;

                var result =myTanggal+" "+bulan[bln]+" "+thn+" "+jam+":"+menit+":"+detik


                return result;
            }            
        },
        formatDateToDate:(x)=>{

            if(x==null || x=='')
            {
                return '';
            }
            else
            {
                var str =x;

                var myBln=convert.myBln

                // mendapatkan tanggal
                var strTgl=str.substring(0, 2);

                var getLength=(str.length)-4
                // mendapatkan tahun
                var strThn=str.substring(getLength);

                // mendapatkan bulan;
                var strBln=str.substring(3,getLength);
                var bln =strBln.trim()
                var bulan = myBln[bln];

                var result=strThn+"-"+bulan+"-"+strTgl

                return result
            }

        },
        formatDateTimeToDateTime:(x)=>{

            var str =x;

            if(x==null || x=='')
            {
                return '';
            }
            else
            {
                // console.log(x);

                var myBln=convert.myBln

                // mendapatkan tanggal
                var strTgl=str.substring(0, 2);

                var getLength=(str.length)-8

                // mendapatkan jam
                var strJam=str.substring(getLength);

                // mendapatkan tahun
                var strThn=str.substring(getLength-(13-8));
                var getThn=strThn.substring(0,4);

                // mendapatkan bln;
                var strBln=str.substring(3,getLength-5);

                var bln =strBln.trim()
                var bulan = myBln[bln];

                var result=getThn+"-"+bulan+"-"+strTgl+" "+strJam

                return result                            

            }

        },

        myBln : {
            'Januari':'01',
           'Februari':'02',
            'Maret':'03',
            'April':'04',
            'Mei':'05',
            'Juni':'06',
            'Juli':'07',
            'Agustus':'08',
            'September':'09',
            'Oktober':'10',
            'November':'11',
            'Desember':'12'
        }

    }    

</script> 