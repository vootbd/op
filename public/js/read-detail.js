$(document).ready(function() {
    var maximum_value = $(".maximum-minimum-input").text();
    var result = $.trim(maximum_value).split('/');
    var maximum = result[0];
    var minimum = result[1];
    if(typeof result[0] === 'undefined'){
        maximum = '';
    }
    if(typeof result[1] === 'undefined'){
        minimum = '';
    }

    $(".maximum-minimum-input").text(' 最⼤: '+maximum +'  最⼩:  '+ minimum)

    var size = $(".size-section-input").text();
    var resultData = $.trim(size).split('/');
    var vertical = resultData[0];
    var side = resultData[1];
    var height = resultData[2];
    var weight = resultData[3];

    if(typeof resultData[0] === 'undefined'){
        vertical = '';
    }
    if(typeof resultData[1] === 'undefined'){
        side = '';
    }
    if(typeof resultData[2] === 'undefined'){
        height = '';
    }
    if(typeof resultData[3] === 'undefined'){
        weight = '';
    }
    if(resultData != 'undefined'){
        $(".size-section-input").html('<span> 縦: '+vertical +'</span><span>横:  '+side +'</span><span>高さ:  '+height +'</span><span>重量 :  '+weight+'</span>');
    }
});