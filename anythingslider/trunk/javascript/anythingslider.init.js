jQuery(function(){
	if (dir_anythingslider && jQuery('.slider-anythingslider').length){
		/**
		 * Fonction d'initialisation de chaque slider
		 */
		var my_anythinslider = {
			init : function(){
				var css_def = false;
				jQuery('.slider-anythingslider:not(.initialized)').each(function(){
					var me=jQuery(this);
					var settings = {
						navigationFormatter: my_anythinslider.navigationFormatter
					};
					var options=eval('options='+me.attr('data-slider')+';');
					if (options) {
						// charger les CSS du slider
						if (options.css){
							for (var k=0;k<options.css.length;k++) {
								if (options.css[k].length){
									my_anythinslider.addStylesheet(options.css[k]);
									var m;
									if (m = options.css[k].match(/\/theme-(.*)[.]css$/)){
										options.theme = m[1]; // definir le theme en fonction du nom de la css
									}
								}
							}
						} else {
							css_def = true; // charger les CSS par defaut
						}
						// charger les JS additionnels
						if (options.js){
							for (var k=0;k<options.js.length;k++) {
								if (options.css[k].length){
									my_anythinslider.addJavascript(options.js[k]);
								}
							}
						}
						settings = jQuery.extend(settings,options);
					} else {
						css_def = true; // charger les CSS par defaut
					}
					me.anythingSlider(settings).addClass('initialized');
				});
				if (css_def) { // faut-il charger les CSS par defaut ?
					my_anythinslider.addStylesheet(css_defaut_anythinslider);
				}
			},
			addStylesheet : function(url) {
				var stylesheet = document.createElement('link');
				stylesheet.rel = 'stylesheet';
				stylesheet.type = 'text/css';
				stylesheet.href =  url;
				document.getElementsByTagName('head')[0].appendChild(stylesheet);
			},
			addJavascript : function(url) {
				var javascript = document.createElement('script');
				javascript.type = 'text/javascript';
				javascript.src =  url;
				document.getElementsByTagName('head')[0].appendChild(javascript);
			},
			navigationFormatter : function(index, panel){ // Format navigation labels with text
				if (jQuery('.slider-nav',panel).length)
					return jQuery('.slider-nav',panel).html();
				else
					return index+""; // Il faut convertir en texte
			}
		};
		jQuery.getScript(dir_anythingslider+"js/jquery.anythingslider.min.js",function(){
			my_anythinslider.init();
			onAjaxLoad(my_anythinslider.init);
		});
	}
});

/* See http://css-tricks.github.com/AnythingSlider/ for options */