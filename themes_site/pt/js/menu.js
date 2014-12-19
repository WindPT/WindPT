$(document).ready(function() {
    $(".qmenu").click(function() {
        var b = $(".qmenu");
        var p = $(".menup");
        if (p.is(":hidden")) {
            p.show(0).offset({
                top: 0,
                left: 0
            }).offset({
                top: b.offset().top + b.outerHeight(),
                left: b.offset().left - p.outerWidth() + b.outerWidth()
            });
            b.addClass("qmenu-a");
        } else {
            p.offset({
                top: 0,
                left: 0
            }).hide(0);
            b.removeClass("qmenu-a");
        }
    })
})