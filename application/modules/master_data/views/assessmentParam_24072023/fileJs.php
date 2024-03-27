<script type="text/javascript">
	class MyData {

		getDetailForm(indexCount) {
			var html = `
							<div class="bg-secondary" id="contentQuestion${indexCount}">   
								<div class="row">                 
									<div class="col-sm-2">
										<div class="form-group">
												<label>Urutan </label>
												<input type="number" name="ordering[${indexCount}]" class="form-control"  placeholder="urutan"  min="1" >
										</div>	
									</div>
								</div>		  
								<div class="row">                 
									<div class="col-sm-12">  
										<div class="form-group">
											<label>Pertanyaan </label>
											<textarea class="wysihtml5 form-control" name="question[${indexCount}]" id="question${indexCount}" placeholder="Info" required id="tes" rows="20"></textarea>
										</div>        	
									</div>
								</div> 
								<div class="row">                 
									<div class="col-sm-12">
										<div class="btn btn-danger pull-right hapusData" data-id="${indexCount}" >Hapus</div>
									</div>
								</div>
                <hr/>
							</div>
			`;

			$(html).insertBefore($("#bottomContent"));
			CKEDITOR.config.extraPlugins = 'justify';
			CKEDITOR.config.height = '80px';
			CKEDITOR.replace(`question[${indexCount}]`, {
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

				removeButtons: 'Print,Preview,ExportPdf,NewPage,Save,Templates,Find,Replace,SelectAll,Form,Checkbox,Radio,TextField,Textarea,Select,Button,ImageButton,HiddenField,About,Font,Flash,BidiRtl,Language,ShowBlocks,BidiLtr'
			});

			$(".hapusData").on("click", function() {

				var id = `#contentQuestion${$(this).attr("data-id")}`;
				$(id).remove();


			})


		}
		getDetail(url, id) {
			$.ajax({
				url: url,
				data: "id=" + id,
				type: "POST",
				dataType: "json",
				// beforeSend:()=>{                    
				//     l.start();
				// },
				success: (x) => {

					console.log(x);
					var html = "";

					if(x.length>0)
					{
						for (var i in x) {
							html += myData.getDetailFormEdit(x[i]);

							indexCount++

						}

					}
					else
					{

						html += myData.getDetailFormEdit("");
						indexCount++

					}



					html += `<div class="col-sm-12 form-group" id="bottomContent"></div> `

					// console.log(html)
					$("#editSrolling").html(html);


					$(".hapusData").on("click", function() {

						var id = `#contentQuestion${$(this).attr("data-id")}`;

						$(id).remove();

					})

					//initial ckeditor

					if(x.length>0)
					{	
						for (var i2 in x) 
						{

							CKEDITOR.config.extraPlugins = 'justify';
							CKEDITOR.replace(`question[${i2}]`, {
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

								removeButtons: 'Print,Preview,ExportPdf,NewPage,Save,Templates,Find,Replace,SelectAll,Form,Checkbox,Radio,TextField,Textarea,Select,Button,ImageButton,HiddenField,About,Font,Flash,BidiRtl,Language,ShowBlocks,BidiLtr'
							});


						}
					}
					else
					{

							CKEDITOR.config.extraPlugins = 'justify';
							CKEDITOR.replace(`question[0]`, {
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

								removeButtons: 'Print,Preview,ExportPdf,NewPage,Save,Templates,Find,Replace,SelectAll,Form,Checkbox,Radio,TextField,Textarea,Select,Button,ImageButton,HiddenField,About,Font,Flash,BidiRtl,Language,ShowBlocks,BidiLtr'
							});

					}


				},
				error: function() {
					toastr.error('Silahkan Hubungi Administrator', 'Gagal');
				}
				// complete: function(){
				//     l.stop();
				// }                
			})
		}
		getDetailFormEdit(data) {
			var html = ""

			// console.log(data);

			if(data!="")
			{

				html +=`
		            <div class="bg-secondary" id="contentQuestion${indexCount}">   
						<div class="row">                 
							<div class="col-sm-2">
								<div class="form-group">
										<label>Urutan </label>
										<input type="number" name="ordering[${indexCount}]" class="form-control"  placeholder="urutan"  value="${data.ordering}" min="1">
								</div>	
							</div>
						</div>		  
						<div class="row">                 
							<div class="col-sm-12">  
								<div class="form-group">
									<label>Pertanyaan </label>
									<textarea class="wysihtml5 form-control" name="question[${indexCount}]" id="question${indexCount}" placeholder="Info" required id="tes" rows="20">${data.question_text}</textarea>
								</div>        	
							</div>
						</div> `

				if (indexCount > 0) {
					html += `<div class="btn btn-danger pull-right hapusData" data-id="${indexCount}" >Hapus</div>`;

				}





				html += `<div class="col-sm-12"><hr/></div></div>`;
			}
			else
			{

				html +=`
		            <div class="bg-secondary" id="contentQuestion${indexCount}">   
									<div class="row">                 
										<div class="col-sm-2">
											<div class="form-group">
													<label>Urutan </label>
													<input type="number" name="ordering[${indexCount}]" class="form-control"  placeholder="urutan"  value=""  min="1">
											</div>	
										</div>
									</div>		  
									<div class="row">                 
										<div class="col-sm-12">  
											<div class="form-group">
												<label>Pertanyaan </label>
												<textarea class="wysihtml5 form-control" name="question[${indexCount}]" id="question${indexCount}" placeholder="Info" required id="tes" rows="20"></textarea>
											</div>        	
										</div>
									</div> 
								</div>
								<div class="row">                 
									<div class="col-sm-12">
								`;

				html += `</div></div><hr/>`;				

			}

			return html;


		}

		changeSearch(x, name) {
			$("#btnData").html(`${x} <i class="fa fa-angle-down"></i>`);
			$("#searchData").attr('data-name', name);

		}
	}
</script>