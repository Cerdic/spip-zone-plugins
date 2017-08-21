jQuery(function($){
	coloriagePalette = function() {
		var options = {
			doRender: 'div div',
			opacity: false,
			cssAddon:
				'.cp-color-picker { z-index:10000; }'
		}
		window.myColorPicker = $('.palette').each(function() {
			var me = $(this);
			var opt = options;
			if (me.hasClass('withalpha') || me.data('palette-withalpha')) {
				opt.opacity = true;
			}
			me.colorPicker(opt);
		});
		
	}
	coloriagePalette();
	onAjaxLoad(coloriagePalette);
});
