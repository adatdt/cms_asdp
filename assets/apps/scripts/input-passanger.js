
var typeText = {
    adult : {
        singular: 'Dewasa', plural: 'Dewasa'
      },
    child : {
        singular: 'Anak', plural: 'Anak'
      },
    infant : {
        singular: 'Bayi', plural: 'Bayi'
      }
}
// $(function () { 
    var $popover = $('.trigger').popover({
        html: true,
        placement: 'top',
        title: 'Penumpang',
        trigger:'manual',
        content: function () {
            return $(this).parent().find('.content').html();
        }
    })
    .on('shown.bs.popover', function() { 
        $("#adult").TouchSpin({
            min: 1,
            max: max_cal_adult
        });
        $("#child").TouchSpin({
            min: 0,
            max: max_cal_child
        });
        $("#infant").TouchSpin({
            min: 0,
            max: max
        });
    });

    

    $('.trigger').click(function () {
        $(this).popover('toggle');
    });

    // open popover & inital value in form
    var passengers = [1,0,0];
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
                    if($('.popover #adult').val() <= max_cal_adult && $('.popover #child').val() <= max_cal_child && $('.popover #infant').val() <= max){
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
                } else {
                    input.closest('.penumpang').find('.trigger > input.bayi').val(oldValue)
                } 
            }
        }
        
        $('#passengers').val(total); 
        input.val(oldValue);

        passengersInfoText();
    });   
    
    $('body').on('change','#adult', function(){
        $('#dewasa').val(this.value);

        if ($('#service option:selected').val() == 1){
            max = 999;
        }else{
            max = 999;
        }

        max_cal_adult  = max - $('#anak').val();
        max_cal_child  = max - $('#dewasa').val();
        $("#adult").trigger("touchspin.updatesettings", {max: max_cal_adult});               
        $("#child").trigger("touchspin.updatesettings", {max: max_cal_child});
        $("#infant").trigger("touchspin.updatesettings", {max: max});

        passengersInfoText();

    });

    $('body').on('change','#child', function(){
        $('#anak').val(this.value);

        if ($('#service option:selected').val() == 1){
            max = 999;
        }else{
            max = 999;
        }

        max_cal_adult  = max - $('#anak').val();
        max_cal_child  = max - $('#dewasa').val();
        $("#adult").trigger("touchspin.updatesettings", {max: max_cal_adult});               
        $("#child").trigger("touchspin.updatesettings", {max: max_cal_child});
        $("#infant").trigger("touchspin.updatesettings", {max: max});

        passengersInfoText();
    });

    $('body').on('change','#infant', function(){
        $('#bayi').val(this.value);
        passengersInfoText();
    });
// });
