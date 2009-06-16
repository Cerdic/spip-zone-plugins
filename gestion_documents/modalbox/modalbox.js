/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


;(function ($) {

	$.modalbox = function (data, options) {
		if ($.isFunction(options.onClose)){
			var onClosefunc = options.onClose;
			options.onClose = function(dialog){
				$.modal.close();
				onClosefunc.apply($.modal, [dialog])
			}
		}
		return $.modal(data, options);
	};

	$.modalboxload = function (url, options) {
		$.get(url,function(data){
			$.modalbox(data,options);
		},"html");
	};

	$.modalboxclose = function () {
		$.modal.close();
	};

})(jQuery);