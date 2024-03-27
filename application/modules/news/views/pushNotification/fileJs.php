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
                    "url": "<?php echo site_url('news/pushNotification') ?>",
                    "type": "POST",
                    "data": function(d) {
                        d.dateTo = document.getElementById('dateTo').value;
                        d.dateFrom = document.getElementById('dateFrom').value;
                        d.startPublish =document.getElementById('startPublish').value;
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
                        "data": "typeData",
                        "orderable": true,
                        "className": "text-left"
                    },                                               
                    {
                        "data": "title",
                        "orderable": true,
                        "className": "text-left"
                    },                    
                    {
                        "data": "image",
                        "orderable": true,
                        "className": "text-left"
                    },
                    {
                        "data": "sub_title",
                        "orderable": true,
                        "className": "text-left"
                    },
                    {
                        "data": "priode",
                        "orderable": true,
                        "className": "text-left"
                    },    
                    {
                        "data": "time_published",
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
                }

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
                    else if  (element.attr("name") == "frekuensi" )
                        error.insertAfter("#frekuensiError");                       
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
            // console.log(returnData);
            // console.log(month);
            // console.log(day);

            return returnData
        }
        showModal(url)
        {
            $('#modalViewImage').modal('show');
            const dataImage = `<img src="${url}" width="100%" /> `
            $("#imagePath").html(dataImage)
        }

        resizeFile(img, maxFile)
        {
            unBlockUiId('box');
            let formData = new FormData();
            // const maxFile =50000 // parameter 
            const url = '<?php echo base_url(); ?>';
            new Compressor(img, {
                // quality: 0.8,
                // maxWidth: 1000,
                mimeType: 'image/jpeg',
                convertSize: maxFile,
                success(result) {
                    // console.log(result)

                    let reader = new FileReader();
                    reader.readAsDataURL(result);
                    reader.onloadend = function()
                    {
                        let base64data = reader.result;
                        $('#fileHide').val(base64data);   
                    }    
                                       
                },
            });

            $('#box').unblock();
            
             
        } 

        ckEditorConfig(inputName)
        {
            CKEDITOR.config.extraPlugins = ['justify','embed','font']
            CKEDITOR.config.skin = 'office2013';
            CKEDITOR.config.embed_provider = '//ckeditor.iframe.ly/api/oembed?url={url}&callback={callback}'
            CKEDITOR.replace(inputName, {
                toolbarGroups: [{
                        name: 'clipboard',
                        groups: ['clipboard', 'undo']
                    },
                    {
                        name: 'editing',
                        groups: ['find', 'selection', 'spellchecker', 'editing']
                    },
                    {
                        name: 'forms',
                        groups: ['forms']
                    },
                    {
                        name: 'links',
                        groups: ['links']
                    },
                    {
                        name: 'insert',
                        groups: ['insert']
                    },
                    {
                        name: 'document',
                        groups: ['mode', 'document', 'doctools']
                    },
                    {
                        name: 'tools',
                        groups: ['tools']
                    },
                    '/',
                    {
                        name: 'basicstyles',
                        groups: ['basicstyles', 'cleanup']
                    },
                    {
                        name: 'colors',
                        groups: ['colors']
                    },
                    {
                        name: 'paragraph',
                        groups: ['list', 'indent', 'blocks', 'align', 'bidi', 'paragraph']
                    },
                    {
                        name: 'styles',
                        groups: ['styles']
                    },
                    {
                        name: 'others',
                        groups: ['others']
                    },
                    {
                        name: 'about',
                        groups: ['about']
                    }
                ],

                removeButtons: 'Print,Preview,ExportPdf,NewPage,Save,Templates,Find,Replace,SelectAll,Form,Checkbox,Radio,TextField,Textarea,Select,Button,ImageButton,HiddenField,About,Font,Flash,BidiRtl,Language,ShowBlocks,BidiLtr,Image'
            });
        }               

    }

</script>