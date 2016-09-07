function setHeight( ) {
    var footerHeight = parseInt($("footer").height( ));
    var navHeight = parseInt($("nav").height( ));
    var docHeight = parseInt($(document).height( ));
    var screenHeight = 0;

    if (footerHeight > navHeight) {
        screenHeight = ((footerHeight + navHeight) + docHeight);

    } else {
        screenHeight = ((navHeight + footerHeight) + docHeight);
    }

    $("#wrapper > .container").css("min-height", docHeight + "px");

    $("#wrapper").css("min-height", screenHeight + "px");


    $(window).on("resize", function ( ) {

        $(".modal").height($(document).height( ));

        var footerHeight = parseInt($("footer").height( ));
        var navHeight = parseInt($("nav").height( ));
        var docHeight = parseInt($(document).height( ));
        var screenHeight = 0;

        if (footerHeight > navHeight) {
            screenHeight = ((footerHeight + navHeight) + docHeight);

        } else {
            screenHeight = ((navHeight + footerHeight) + docHeight);
        }

        $("#wrapper > .container").css("min-height", parseInt(docHeight) + "px");
    });
}

function adjustContainerHeight() {
    $('.main-content').css('min-height', 0);

    var docHeight = parseInt($(document).outerHeight());
    var conHeight = parseInt($('.main-content').outerHeight());
    var navHeight = parseInt($("nav").height());
    var footerHeight = parseInt($("footer").height());
    var sidebarLeft = $("#sidebar-left").outerHeight();
    var sidebarRight = $("#sidebar-right").outerHeight();

    conHeight = Math.max(conHeight, sidebarLeft, sidebarRight);
    var minHeight = 0;
    var pad = 40;


    if (docHeight > (conHeight + navHeight + footerHeight + pad)) {
        minHeight = docHeight - footerHeight - navHeight - pad;
    } else {
        minHeight = conHeight;
    }
    $('.main-content').css('min-height', minHeight);
}
function dotdot() {
    $('.news-desc, .news-title').dotdotdot({
        ellipsis: '… ',
        watch: true,
        wrap: 'letter',
        height: parseInt($('.truncate').css('line-height'), 10) * 2, // this is the number of lines
        lastCharacter: {
            remove: [' ', ',', ';', '.', '!', '?'],
            noEllipsis: []
        },
        after: 'a.read-more'
    });
}


jQuery(document).ready(function ($) {

    $('html').on('click', function (e) {
        if (typeof $(e.target).data('original-title') == 'undefined' && !$(e.target).parents().is('.popover.in')) {
            $('[data-original-title]').popover('hide');
        }
    });
});