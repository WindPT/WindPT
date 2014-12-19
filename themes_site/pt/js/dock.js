$(document).ready(function() {
    var logo = $("[alt='forumlogo']");
    logo.each(function() {
        var html = $(this).parent().html();
        $(this).parent().html('<div class="fl mr10 forumlogo">' + html + '<img class="shadow" src="' + TIMG + '/shadow.png"></div>').children().children(".fl").removeClass().addClass('flogo');
    });
    $(".forumlogo").hover(function() {
        $(this).children('.flogo').stop().animate({
            bottom: "11px"
        }, 250);
        $(this).find('.shadow').stop().animate({
            opacity: 0.4,
            width: "60px"
        }, 250);
    }, function() {
        $(this).children('.flogo').stop().animate({
            bottom: "-6px"
        }, 250, function(e) {
            $(this).animate({
                bottom: "0px"
            }, 250);
        });
        $(this).find('.shadow').stop().animate({
            opacity: 1,
            width: "80px"
        }, 250);
    })
})