$(document).ready(function(){
    var status = 1;
    var lateral = $("#lateral");
    var content = $("#content");
    var lateralWidth = lateral.width() + "px";
    //dimensiones disponibles para elementos del panel
    var windowHeight = 0;
    var renderHeight = 0;
    var windowWidth=0;
    calculateDimensions();
    applyDimensions();
    $(window).resize(function()
    {
        calculateDimensions();
        applyDimensions();
    });
    function calculateDimensions()
    {
        windowWidth = document.documentElement.clientWidth; //alto disponible en ventana del explorador        
        windowWidth=(windowWidth - 165)  +"px";        
        windowHeight = document.documentElement.clientHeight; //alto disponible en ventana del explorador
        renderHeight = (windowHeight - 125 )  +"px";
        /* �De donde salen esos valores a restar? Pues de:
         * 51: #top: 40px de height, 10px de padding-top, y 1px de border-bottom
         * 40: #content h2: 40px de height
         * 31: #footer: 30px de height y 1px de border-top
        */
    }
    function applyDimensions(){        
        content.css("height", renderHeight);
        content.css("width", windowWidth);
    }
//panel

});