$(function(){
           setInterval("mostrar()",15000);
           mostrar=function()
           {
               $.ajax({
		  type: "POST",
		  url: "/ajax/mensaje",
		  dataType: "json",
		  success: function(data)
		  {
                      $.each(data, function(i,v){
                          var texto='<a href="/correspondence/"><b>'+data[i].hr+'</b></a><br/>'+data[i].remitente+'<br/>'+data[i].cargo;
                          $().toastmessage('showSuccessToast', texto);
                      });
                    //$('embed').remove();
                    //$('body').append('<embed src="door.mp3" autostart="true" hidden="true" loop="false">');     
		  }
		});
           }
        });