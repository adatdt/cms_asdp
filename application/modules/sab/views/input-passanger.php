<script>
 var csfrData = {};
    csfrData[`<?php echo $this->security->get_csrf_token_name(); ?>`] =`<?php echo $this->security->get_csrf_hash(); ?>`;
    $.ajaxSetup({
        data: csfrData
    });
    
var typeText = {
    adult : {
        singular: 'Dewasa', plural: 'Dewasa'
      },
    child : {
        singular: 'Anak', plural: 'Anak'
      },
    infant : {
        singular: 'Bayi', plural: 'Bayi'
      },
      elder : {
        singular: 'Lansia', plural: 'Lansia'
      }

}
// $(function () { 
    var $popover = $('.trigger').popover({
        html: true,
        placement: 'top',
        title: '<b>Penumpang</b>',
        trigger:'manual',
        content: function () {
            return $(this).parent().find('.content').html();
        }
    })
    .on('shown.bs.popover', function() { 
        $("#adult").TouchSpin({
            min: 0,
            max: max_cal_adult
            
        });
        $("#child").TouchSpin({
            min: 0,
            max: max_cal_child
        });
        $("#elder").TouchSpin({
            min: 0,
            max: max_cal_elder
        });        
        $("#infant").TouchSpin({
            min: 0,
            max: max
        });
    });

    

    $('.trigger').click(function () {
        const getService = $("#service").val();
        const origin = $("#portOrigin").val();
        const shipClass = $("#ship_class").val();

        if(getService !=="" && origin !=="" && shipClass !=="" )
        {
            $(this).popover('toggle');

            // let dataElder = isNaN($("#elder").val())?0:$("#elder").val();
            // let dataAdult = isNaN($("#adult").val())?0:$("#adult").val();

            let dataElder = $("#lansia").val();
            let dataAdult = $("#dewasa").val();

            // console.log(dataElder)
            // console.log(dataAdult)
            if (dataElder>0 || dataAdult>0 )
            {
                $("#child").prop("disabled", false);
                $("#child").css("background-color","#ffff")
            }


        }
    });

    // open popover & inital value in form
    var passengers = [0,0,0,0];
    $('.trigger').click(function (e) {
        e.stopPropagation();
        $(".popover-content input").each(function(i) {
            $(this).val(passengers[i]);
        });
    });
    // place text passengers info
    function passengersInfoText() {
        passengerInfoText = [];
        $(".popover-content input").each(function(i) {
            if(this.value > 0){
                //passengerInfoText.push(typeText[this.id][this.value>1?'plural':'singular'] + ': ' + this.value);
                passengerInfoText.push(this.value +' '+ typeText[this.id][this.value>1?'plural':'singular']);
            }
        });
        $('#passenger-info').val(passengerInfoText.join(', '))
    }
    // close popover
    $(document).click(function (e) {
        if ($(e.target).is('.demise')) {
            $('.trigger').popover('hide');
        }
    });
    
    $('body').on('click', function (e) {
        $('[data-original-title]').each(function () {
            // hide any open popovers when the anywhere else in the body is clicked
                if (!$(this).is(e.target) && $(this).has(e.target).length === 0 && $('.popover').has(e.target).length === 0) {
                    if($('.popover #adult').val() <= max_cal_adult && $('.popover #child').val() <= max_cal_child && $('.popover #infant').val() <= max && $('.popover #elder').val() <= max ){
                    $(this).popover('hide');
                } 
            }
        });
    });
    // store form value when popover closed
    $popover.on('hide.bs.popover', function () {
        $(".popover-content input").each(function(i) {
            passengers[i] = $(this).val();
        });
    });
    // spinner(+-btn to change value) & total to parent input 
    // $(document).on('click', '.number-spinner span.spinner', function () {
    $(document).on('click', '.bootstrap-touchspin .btn', function () {
        var btn = $(this),
        input = btn.closest('.number-spinner').find('input'),
        total = $('#passengers').val(),
        oldValue = input.val().trim();
        // adult = $('#adult').val();

        if (btn.attr('data-dir') == 'up') {
            if(oldValue < input.attr('max')){
                oldValue++;
                total++;

                if (input.attr('id') == 'adult') {
                    input.closest('.penumpang').find('.trigger > input.dewasa').val(oldValue)
                } else if (input.attr('id') == 'child') {
                    input.closest('.penumpang').find('.trigger > input.anak').val(oldValue)
                }else if (input.attr('id') == 'elder') {
                    input.closest('.penumpang').find('.trigger > input.lansia').val(oldValue)
                } else {
                    input.closest('.penumpang').find('.trigger > input.bayi').val(oldValue)
                } 
            }
        } else {
            if (oldValue > input.attr('min')) {
                oldValue--;
                total--;

                if (input.attr('id') == 'adult') {
                    input.closest('.penumpang').find('.trigger > input.dewasa').val(oldValue)
                } else if (input.attr('id') == 'child') {
                    input.closest('.penumpang').find('.trigger > input.anak').val(oldValue)
                }else if (input.attr('id') == 'elder') {
                    input.closest('.penumpang').find('.trigger > input.lansia').val(oldValue)
                } else {
                    input.closest('.penumpang').find('.trigger > input.bayi').val(oldValue)
                } 
            }
        }
        
        $('#passengers').val(total); 
        input.val(oldValue);

        passengersInfoText();
    });  

    function getPassangerType()
    {
        const getService = $("#service").val();
        const origin = $("#portOrigin").val();
        const shipClass = $("#ship_class").val();

        if(getService !=="" && origin !=="" )
        {
            const data = {origin : origin , service : getService , shipClass : shipClass, <?php echo $this->security->get_csrf_token_name(); ?>:$("input[name=" + csfrData.csrfName + "]").val()}
            $.ajax({
                url     : BASE_URL+'sab/getPassangerType',
                type    : 'post',
                data    : data,
                dataType: 'json',
                beforeSend:function(){
                    unBlockUiId('box')
                },
                success: function(json) {

                    $("input[name=" + json.csrfName + "]").val(json.tokenHash);
                    csfrData[json['csrfName']] = json['tokenHash'];
                    $.ajaxSetup({
                        data: csfrData
                    });

                    const data = json.data;
                    let html ="";
                    let enable ="";

                    data.forEach(element => {

                        if(element.code=='child' || element.code=='infant' )
                        {
                            enable = `disabled style="background-color: #eef1f5;" `;
                        }
                        else
                        {
                            enable = " ";
                        }

                        let maxMinAge = ``;
                        if(element.code == 'elder')
                        {
                            maxMinAge = `<br /> Usia ${element.min_age}+ th`
                        }
                        else if(element.code == 'infant')
                        {
                            let countAge = parseInt(element.max_age) + 1
                            maxMinAge = `<br /> Dibawah ${countAge} th`
                        }
                        else 
                        {
                            maxMinAge = `<br /> Usia ${element.min_age}-${element.max_age} th`
                        }
                        
                        html += `
                            <div class="form-group">
                                <label class="control-label col-md-5 col-xs-12" style="font-size:13px;">${element.name}<span style="color:red">*</span>
                                 <span class="passengers-desc" style="font-size:11px;  font-style: italic; color:grey; "> ${maxMinAge} </span></label>
                                <div class="input-group number-spinner col-md-7 col-xs-12">
                                    <input id="${element.code}" class="form-control text-center" type="text" name="${element.code}" value="0" ${enable}>
                                </div>
                            </div>
                        `
                    });

                    html += `<a class="btn btn-default my-btn my-btn-default btn-block demise" >Selesai</a>`

                    if(data.length >0 )
                    {

                        $("#contentTypePass").html(html);
                        
                    }
                    else
                    {
                        $("#contentTypePass").html("");
                        $('.trigger').popover("hide");
                    }

                    $("#passenger-info").val("")
                    $(".valData").val(0)
                    
                },
                complete: function(){
                    $('#box').unblock(); 
                },

                error: function() {
                    console.log('Silahkan Hubungi Administrator')
                },
                "fnDrawCallback": function(allRow) 
                {
                    let getTokenName = `<?php echo $this->security->get_csrf_token_name(); ?>`;
                    let getToken = allRow.json[getTokenName];
                    csfrData[getTokenName] = getToken;
                    $.ajaxSetup({
                        data: csfrData
                    });
                }

            });
            
        }
    }
    
   
// });

</script>
