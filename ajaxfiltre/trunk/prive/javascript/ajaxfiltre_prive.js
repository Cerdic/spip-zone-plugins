(function($) {

	$.ajaxFiltre = function(el, options) {
		var base = this;

		base.init = function() {
			base.$el = $(el);
			base.el = el;
			base.options = $.extend({}, $.ajaxFiltre.defaultOptions, options);

			base.$el.find('select, input[type=checkbox], input[type=radio]').on('change', function() {
				
				var formData = base.$el.serializeArray().reduce(function(obj, item) {
					// si le name comporte des [] on construit un tableau
					if(item.name.indexOf('[]') !== -1) {
						var name = item.name.replace(/[\[\]]+/g, '');
						if(!obj.hasOwnProperty(name)) {
							obj[name] = [];
						}
						obj[name].push(item.value);
					} else {
						obj[item.name] = item.value;
					}
					return obj;
				}, {});

				// passer une valeur vide explicite pour les checkbox dont le name comporte des [] et dans lesquels rien n'est coché
				var $checkradio = base.$el.find('input[type=checkbox][name*="[]"]');
				$.each($checkradio,function(){
					var checkRadioName = $(this).attr('name');
					// si rien n'est coché
					if(!base.$el.find('input[name="'+checkRadioName+'"]:checked').length){
						// supprimer les données du nom avec []
						delete formData[checkRadioName];
						// ajouter un tableau vide sur le nom sans [] 
						formData[checkRadioName.replace(/[\[\]]+/g, '')] = [];
					}
				});
				
				console.log(formData);
				
				ajaxReload(base.options.ajaxTarget, {args: formData});
			});

		};

		base.init();
	};

	$.ajaxFiltre.defaultOptions = {
		ajaxTarget: "liste-objets"
	};

	$.fn.ajaxFiltre = function(options) {
		return this.each(function() {
			(new $.ajaxFiltre(this, options));
		});
	};

})(jQuery);

$(function() {

	$('.formulaire_navigation_filtre form').ajaxFiltre({ajaxTarget: 'liste-objets'});

});