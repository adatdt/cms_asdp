<style type="text/css">
    .modal {
        left: 25% !important;
        /* width: 800px !important; */
        /* margin-left: -500px !important; */
        /* margin-left: -10% !important; */
        width: 80% !important;
    }

</style>
<div class="col-md-10 col-md-offset-1">
<div class="modal fade" id="modalMap" tabindex="-1" role="dialog" aria-labelledby="modalMapVideo" aria-hidden="true" >
    <div class="portlet box blue" id="box">        
        <div class="portlet-title">
            <div class="caption">View Map</div>
            <div class="tools"><button type="button" class="btn btn-box-tool btn-xs btn-primary" data-dismiss="modal"><i class="fa fa-times"></i></button>
            </div>
        </div>
        <div class="portlet-body">
            <div class="box-body">
                 <div class="form-group">
                    <div class="row">                        
                        <div class="col-sm-12 ">    

                                    <div id="map" style="width:100%; height: 650px;"></div>
                                                         
                        </div>
                        <div class="col-sm-10 form-group ">
                            <div style="margin-top: 20px; font-weight: bold;" id='infoView'>                            
                            </div>
                        </div>
                        <div class="col-sm-2 form-group pull-right">
                            <input type="hidden" id="rmsCodeView">
                            <button type="button" class="btn btn-primary mt-ladda-btn ladda-button add-url-image pull-right" style="margin-top: 20px" data-style="zoom-in" id="print" title="download"> <i class="fa fa-download" aria-hidden="true"></i> </button>
                        </div> 
                    </div>
                </div>
            </div>


        </div>
    </div>
</div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js" integrity="sha512-BNaRQnYJYiPSqHHDb58B0yaPfCu+Wgds8Gp/gU33kqBtgNS4tSPHuGibyoeqMV/TJlSKda6FXzoEyYGjTe+vXA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<script >


    $("#print").click(function() { 
        var target = document.getElementById("map");
        html2canvas(target,{useCORS:true}).then((canvas) => {
            let img    = canvas.toDataURL("image/png");
            let code = $(`#rmsCodeView`).val();
            downloadBase64File(img, `MAP_${code}.png`);
            // $("#img-out").append(canvas)
        })
        .catch(function (err) {
                // console.log(err);
            });
                
    });


</script>



