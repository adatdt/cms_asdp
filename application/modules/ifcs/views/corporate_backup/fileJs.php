<script type="text/javascript">
	
	function addBranch(index)
	{
		var html="<div class='row form-group' id='branch"+index+"'>\
					<div class=' col col-md-5' form-group>\
						<label>Cabang</label>\
						<input type='text' name='branch["+index+"]' class='form-control' required  placeholder='Cabang Perusahaan'>\
					</div>\
					<div class='col col-md-1 form-group'>\
					<label>&nbsp;</label>\
						<div style='margin-bottom:-5'><a href='#' class='btn btn-danger pull-left' title='Hapus' id='delete' onClick=deleteData('branch"+index+"')><i class='fa fa-trash-o'></i></a>\
					</div></div>\
				</div>";

		return html				
	}

	function dataModulus()
	{
		return html="<div class='col-md-12' form-group></div>";
	}

	function deleteData(id)
	{
		x=document.getElementById(id);
		x.remove();	
	}

	$(document).ready(function(){

		var no="<?php echo $key; ?>"

		if(no==0)
        {
            var index=0;
        }
        else
        {
            var index =no;
        }
        
		// var index =0;
		$("#tambah").click(function(){

			$("#branch").append(addBranch(index));

			index++;
		});
	});	

</script>