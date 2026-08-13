/**
 * @author Remy Sharp
 * @date 2008-02-25
 * @url http://remysharp.com/2007/09/18/auto-populate-multiple-select-boxes/
 * @license Creative Commons License - ShareAlike http://creativecommons.org/licenses/by-sa/3.0/
 */

(function ($) {
    $.fn.selectChain = function (options) {
        var defaults = {
            key: "id",
            value: "label"
        };
        
        var settings = $.extend({}, options, defaults);
        
        if (!(settings.target instanceof $)) settings.target = $(settings.target);
        
        return this.each(function () {
            var $$ = $(this);
            
            $$.change(function () {
                var data = null;
                if (typeof settings.data == 'string') {
                    data = settings.data + '&' + this.name + '=' + $$.val();
                } else if (typeof settings.data == 'object') {
                    data = settings.data;
                    data[this.name] = $$.val();
                }
                
                settings.target.empty();
                settings.target.parent('div').append('<img src="/media/img/spinner.gif">');                
                $.ajax({
                    url: settings.url,
                    data: data,
                    type: (settings.type || 'get'),
                    dataType: 'json',
                    success: function (j) {
                        var options = [], i = 0, o = null;
                        $.each(j,function(i,e){
                            o = document.createElement("OPTION");
                            o.value = j[i].value;
                            o.text =  j[i].text;
                            settings.target.get(0).options[i] = o;
			});
			// hand control back to browser for a moment
                        settings.target.parent('div').find('img').remove(); 
			setTimeout(function () {
			    settings.target
                                .find('option:first')
                                .attr('selected', 'selected')
                                .parent('select')
                                .trigger('change');
			}, 0);
                        //agreagare esta function 
                       settings.target.select2(); 
                    },
                    error: function (xhr, desc, er) {
                        // add whatever debug you want here.
			alert("an error occurred");
                    }
                });
                               
            });
        });
    };
})(jQuery);