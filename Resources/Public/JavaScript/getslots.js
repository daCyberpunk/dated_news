$(window).on('load', function(){
    //js-getslots
    $('.js-getslots').each(function(i, e){
        $.ajax({
            url: "?type=6660666&tx_news_pi1[news]=" + $(e).attr('data-uid'),
            dataType: "json",
            // type: 'POST',
            // data: "word="+"hello",
            success: function(data, s){
                $(e).html(data);
            }
        });
    });
});