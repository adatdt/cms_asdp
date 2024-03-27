<script type="text/javascript">
    var csfrData = {};
        csfrData[`<?php echo $this->security->get_csrf_token_name(); ?>`] =`<?php echo $this->security->get_csrf_hash(); ?>`;
        $.ajaxSetup({
            data: csfrData
    }); 
    
    class Mydata{
        loadData() 
        {
           const t= $('#dataTables').DataTable({
                "ajax": {
                    "url": "<?php echo site_url('news/newsMasterList') ?>",
                    "type": "POST",
                    "data": function(d) {
                        // d.dateTo = document.getElementById('dateTo').value;
                        // d.dateFrom = document.getElementById('dateFrom').value;
                        // d.startPublish =document.getElementById('startPublish').value;
                    },
                    "dataSrc": function ( json ) {
                        //Make your callback here.
                        let getTokenName = `<?php echo $this->security->get_csrf_token_name(); ?>`;
                        let getToken = json[getTokenName];
                        csfrData[getTokenName] = getToken;

                        if( json[getTokenName] == undefined )
                        {
                        csfrData[json.csrfName] = json.tokenHash;
                        }
                            
                        $.ajaxSetup({
                            data: csfrData
                        });
                        
                        
                        return json.data;
                    } 
                },
                // "serverSide": true,
                "processing": true,

                "columns": [
                    {
                        "data": "no",
                        "searchable": false,
                        "orderable": false,
                        "className": "text-left"
                    },
                    {
                        "data": "created_on",
                        "orderable": true,
                        "className": "text-left"
                    },                      
                    {
                        "data": "type_name",
                        "orderable": true,
                        "className": "text-left"
                    },                                                                   
                    {
                        "data": "title",
                        "orderable": true,
                        "className": "text-left"
                    },
                    {
                        "data": "sub_title",
                        "orderable": true,
                        "className": "text-left"
                    },                                                           
                    {
                        "data": "last_edited",
                        "orderable": true,
                        "className": "text-left"
                    },                                       
                    {
                        "data": "status",
                        "orderable": true,
                        "className": "text-center"
                    },
                    {
                        "data": "actions",
                        "orderable": true,
                        "className": "text-center"
                    }
                ],
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
                "searching": true,
                "pagingType": "bootstrap_full_number",
                "columnDefs": [
                        {
                            "searchable": false,
                            "orderable": false,
                            "targets": 0,
                        },
                    ],                
                "order": [
                    [1, "desc"]
                ],
                "initComplete": function() {
                    var $searchInput = $('div.dataTables_filter input');
                    var data_tables = $('#dataTables').DataTable();
                    $searchInput.unbind();
                    $searchInput.bind('keyup', function(e) {
                        if (e.keyCode == 13 || e.whiche == 13) {
                            data_tables.search(this.value).draw();
                        }
                    });
                },

            });

            t.on('order.dt search.dt', function () {
                let i = 1;
        
                t.cells(null, 0, { search: 'applied', order: 'applied' }).every(function (cell) {
                    this.data(i++);
                });
            }).draw();

        }

        reload() {
            $('#dataTables').DataTable().ajax.reload();
        }

        init() {
            if (!jQuery().DataTable) {
                return;
            }
            this.loadData();
        }
        
        changeSearch(x,name)
        {
            $("#btnData").html(`${x} <i class="fa fa-angle-down"></i>`);
            $("#searchData").attr('data-name', name);

        }
        formatDate(date) {
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
             
        replaceStyle(text)
        {
            return btoa(text); // encode ke base64
        }  

        validateForm(id, callback) {
            $(id).validate({
                ignore: '.select2-search__field',
                errorClass: 'validation-error-label',
                successClass: 'validation-valid-label',
                rules: rules,
                messages: messages,

                highlight: function (element, errorClass) {
                    $(element).addClass('val-error');
                },

                unhighlight: function (element, errorClass) {
                    $(element).removeClass('val-error');
                },

                errorPlacement: function (error, element) {

                    if (element.attr("name") == "startDate" )
                        error.insertAfter("#startDateError");
                    else if  (element.attr("name") == "endDate" )
                        error.insertAfter("#endDateError");
                    else if  (element.attr("name") == "thumbnail" )
                        error.insertAfter("#thumbnailError");                       
                    else if (element.parents('div').hasClass('has-feedback')) {
                        error.appendTo(element.parent());
                    }

                    else if (element.parents('div').hasClass('has-feedback') || element.hasClass('select2-hidden-accessible')) {
                        error.appendTo(element.parent());
                    }

                    else {
                        error.insertAfter(element);
                    }
                },

                submitHandler: function (form) {
                    if (typeof callback != 'undefined' && typeof callback == 'function') {
                        callback(form.action, getFormData($(form)));
                    }
                }
            });
        }     
        
        getDataNow()
        {
            var d = new Date();
            var month = d.getMonth() +1;
            var day = d.getDate();
            var year = d.getFullYear();
            var hour = d.getHours();

            let getDay="";
            let getMonth="";

            if(day.length>1)
            {
                getDay=day;
            }
            else
            {
                getDay=`0${day}`
            }

            if(month.length>1)
            {
                getMonth=month;
            }
            else
            {
                getMonth=`0${month}`
            }            

            const returnData = `${year}-${getMonth}-${getDay} ${hour}:00`;


            return returnData
        }
        showModal(url)
        {
            $('#modalViewImage').modal('show');
            const dataImage = `<img src="${url}" width="100%" /> `
            $("#imagePath").html(dataImage)
        }
                      

    }

</script>