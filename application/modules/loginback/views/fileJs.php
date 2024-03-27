<script type="text/javascript">
	class MyData{

		getChaptcha=()=>{
			var l = Ladda.create(document.getElementById("refresh")); 
			$.ajax({
				type:"post",
				url:"<?php echo site_url()?>/login/getChaptcha",
				dataType:"json",
				beforeSend:()=>{
					
					l.start();
				},
				success:(x)=>{
					// console.log(x)
					$("#myChapt").html(x);
				},
	        	complete:()=>{
	        		l.stop();
	        	}				
			})
		}
	}
</script>