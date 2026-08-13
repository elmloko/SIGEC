$(document).ready(function() {
    $(".content_panel").hide(); //updated line, removing the #panel ID.

    $('#tab').toggle(function() { //adding a toggle function to the #tab
        $('#panel').stop().animate({width: "225px", opacity: 0.9}, 500, function() {//sliding the #panel to 400px
            $('.content_panel').fadeIn('slow'); //slides the content into view.
        });
    },
            function() { //when the #tab is next cliked
                $('.content_panel').fadeOut('slow', function() { //fade out the content 
                    $('#panel').stop().animate({width: "0", opacity: 0.1}, 500); //slide the #panel back to a width of 0
                });
            });
//theme control
    $('#colorpicker').farbtastic(function(color) {
        var rgb = hexToRgb(color);
        var theme = 'metro';
        var styl = '#modx-topbar{border-bottom: 2px solid ' + color + ';} #bos-main-blocks h2 a,h2.titulo v,.colorcito {color:' + color + ';}#menu-left ul li a:hover,#menu-left ul li:hover {color:#fff; background: ' + color +
                '; font-weight: bold; } html #modx-topnav ul.modx-subnav li a:hover {background-color:' + color + ';} input#searchsubmit:hover {background-color:' + color
                + ';} #icon-logo{background:' + color + ' url(/media/images/icon_user.png) scroll left no-repeat; }.button2{border: 1px solid' + color +
                ';background-color:' + color + ';}.button2:hover, .button2:focus {background:' + color + ';}.jOrgChart .node { background-color:' + color + ';}.widget .title {background: none repeat scroll 0 0 ' + color +
                ';} legend {border: 1px solid ' + color + ';}fieldset { border: 2px solid' + color + ';}.proveido {color:' + color + ';} span.dias4{ background: '+color+' url("/media/images/fondo_transparente.png") no-repeat top left; }' ;


//            var styl = '.color{color: '+color+'!important;}.background-color{background-color: '+color+'!important;}.background-color-trans {background-color: rgba('+rgb+',0.8)!important;}.background-color-darker{background-color: '+color+'!important;}.border-color{border-color: '+color+'!important;}.border-left-color{border-left-color: '+color+'!important;}.border-top-color{border-top-color: '+color+'!important;}.border-color-darker{border-color: '+color+'!important;}::selection {background: '+color+';}';
        $('#colorcodes').val(styl);
        $('html').find('head').append('<style>' + styl + '</style>');

    });
    $('#hhh').click(function() {
        var styles = $('#colorcodes').val();
        var request = $.ajax({
            url: "/ajax/theme",
            type: "POST",
            data: {theme: styles},
            dataType: "html"
        });
        request.done(function(msg) {
            alert('Datos cambiados correctamente');
        })
    });
    $('#cerrar').click(function() {
        $('.content_panel').fadeOut('slow', function() { //fade out the content 
            $('#panel').stop().animate({width: "0", opacity: 0.1}, 500); //slide the #panel back to a width of 0
        });
    });
    function hexToRgb(hex) {

        var result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
        var r = parseInt(result[1], 16);
        var g = parseInt(result[2], 16);
        var b = parseInt(result[3], 16);

        return r + ',' + g + ',' + b;

    }

    function shadeColor(color, porcent) {

        var R = parseInt(color.substring(1, 3), 16)
        var G = parseInt(color.substring(3, 5), 16)
        var B = parseInt(color.substring(5, 7), 16);

        R = parseInt(R * (100 + porcent) / 100);
        G = parseInt(G * (100 + porcent) / 100);
        B = parseInt(B * (100 + porcent) / 100);

        R = (R < 255) ? R : 255;
        G = (G < 255) ? G : 255;
        B = (B < 255) ? B : 255;

        var RR = ((R.toString(16).length == 1) ? "0" + R.toString(16) : R.toString(16));
        var GG = ((G.toString(16).length == 1) ? "0" + G.toString(16) : G.toString(16));
        var BB = ((B.toString(16).length == 1) ? "0" + B.toString(16) : B.toString(16));

        return "#" + RR + GG + BB;
    }
});