jQuery(function($) {
    $("#loading").bind("ajaxSend", function(){
        $(this).show();
    }).bind("ajaxComplete", function(){
            $(this).hide();
        });

    $("a.fancy").fancybox({
        "ajax":{
            "data": {"isFancy":"true"}
        }
    });

    $('.searchField').change(function(event){
        changeSearch();
    });
});

function focusSubmit(elem) {
    elem.keypress(function(e) {
        if(e.which == 13) {
            $(this).blur();
            $("#btnleft").focus().click();
        }
    });
}

function reloadApartmentList(url){
    $.ajax({
        type: 'POST',
        url: url,
        data: {is_ajax: 1},
        ajaxStart: UpdatingProcess(resultBlock, updateText),
        success: function(msg){
            $('div.main-content-wrapper').html(msg);

            $('#update_div').remove();
            $('#update_text').remove();
            $('#update_img').remove();
        }
    });
}

function UpdatingProcess(resultBlock, updateText){
    $('#update_div').remove();
    $('#update_text').remove();
    $('#update_img').remove();

    var opacityBlock = $('#'+resultBlock);

    if (opacityBlock.width() != null){
        var width = opacityBlock.width();
        var height = opacityBlock.height();
        var left_pos = opacityBlock.offset().left;
        var top_pos = opacityBlock.offset().top;
        $('body').append('<div id=\"update_div\"></div>');

        var cssValues = {
            'z-index' : '5',
            'position' : 'absolute',
            'left' : left_pos,
            'top' : top_pos,
            'width' : width,
            'height' : height,
            'border' : '0px solid #FFFFFF',
            'background-image' : 'url('+bg_img+')'
        }

        $('#update_div').css(cssValues);

        var left_img = left_pos + width/2 - 16;
        var left_text = left_pos + width/2 + 24;
        var top_img = top_pos + height/2 -16;
        var top_text = top_img + 8;

        $('body').append("<img id='update_img' src='"+indicator+"' style='position:absolute;z-index:6; left: "+left_img+"px;top: "+top_img+"px;'>");
        $('body').append("<div id='update_text' style='position:absolute;z-index:6; left: "+left_text+"px;top: "+top_text+"px;'>"+updateText+"</div>");
    }
}

var searchLock = false;

function changeSearch(){
    if(params.change_search_ajax != 1){
        return false;
    }

    if(!searchLock){
        searchLock = true;

        $.ajax({
            url: BASE_URL + '/quicksearch/main/mainsearch/countAjax/1',
            data: $('#search-form').serialize(),
            dataType: 'json',
            type: 'get',
            success: function(data){
            console.dir(data);
            $('#btnleft').html(data.string);
            searchLock = false;
        },
        error: function(){
            searchLock = false;
        }
    })
}
}