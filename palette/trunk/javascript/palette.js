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
			} else if (!me.val()) {
				// #hex par défaut si aucune valeur à l’origine, et pas de couche alpha
				opt.renderCallback = function($elm, toggled) {
					if (!this.color.options.opacity) {
						$elm.val('#' + this.color.colors.HEX);
					}
				};
			}
			me.colorPicker(opt);
		});
	}
	coloriagePalette();
	onAjaxLoad(coloriagePalette);
});
