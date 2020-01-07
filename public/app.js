window.onload=scroolFunction ;

function scroolFunction(){
    var element = document.getElementById('alert');
    var headerOffset = 130;
    var elementPosition = element.getBoundingClientRect().top;
    var offsetPosition = elementPosition - headerOffset;

    window.scrollTo({
        top: offsetPosition,
        behavior: "smooth"
    });
}

$(function() {
    var header = $(".fixed-top");
    $(window).scroll(function() {
        var scroll = $(window).scrollTop();

        if (scroll >= 50) {
            header.addClass("header-shadow");
        } else {
            header.removeClass("header-shadow");
        }
    });
});

$(function() {
    var header = $(".tagline");
    $(window).scroll(function() {
        var scroll = $(window).scrollTop();

        if (scroll >= 100) {
            header.addClass("tagline-visible");
        } else {
            header.removeClass("tagline-visible");
        }
    });
});