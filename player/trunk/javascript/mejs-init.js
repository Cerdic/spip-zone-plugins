var mejsloader;
(function(){
	if (typeof mejsloader == "undefined")
		mejsloader = {gs:null,plug:{},css:{},init:null,c:0};
	if (!mejsloader.init){
		mejsloader.init = function (){
			if (!(mejsloader.gs===true)) return;
			(function ($){
				jQuery("audio.mejs,video.mejs").not('.done').each(function (){
					var me = jQuery(this).addClass('done');
					//console.log(this);
					var id;
					if (!(id = me.attr('id'))){
						id = "mejs-"+(me.attr('data-id'))+"-"+(mejsloader.c++);
						me.attr('id', id);
					}
					var opt = {options: {}, plugins: {}, css: []}, i, v;
					for (i in opt){
						if (v = me.attr('data-mejs'+i)) opt[i] = jQuery.parseJSON(v);
					}
					function runthisplayer(){
						var run = true;
						//console.log(css);
						for (var c in opt.css){
							if (typeof mejsloader.css[opt.css[c]]=="undefined"){
								mejsloader.css[opt.css[c]] = true;
								var stylesheet = document.createElement('link');
								stylesheet.href = opt.css[c];
								stylesheet.rel = 'stylesheet';
								stylesheet.type = 'text/css';
								document.getElementsByTagName('head')[0].appendChild(stylesheet);
							}
						}
						for (var p in opt.plugins){
							//console.log(p);
							//console.log(mejsloader.plug[p]);
							// load this plugin
							if (typeof mejsloader.plug[p]=="undefined"){
								//console.log("Load Plugin "+p);
								run = false;
								mejsloader.plug[p] = false;
								jQuery.getScript(opt.plugins[p], function (){
									mejsloader.plug[p] = true;
									runthisplayer();
								});
							}
							// this plugin is loading
							else if (mejsloader.plug[p]==false){
								//console.log("Plugin "+p+" loading...");
								run = false;
							}
							else {
								//console.log("Plugin "+p+" loaded");
							}
						}
						if (run){
							new MediaElementPlayer('#'+id, jQuery.extend(opt.options, {
								"success": function (media){
									function togglePlayingState(){
										jQuery(media).closest('.mejs-inner').removeClass(media.paused ? 'playing' : 'paused').addClass(media.paused ? 'paused' : 'playing');
									}

									togglePlayingState();
									media.addEventListener('play', togglePlayingState, false);
									media.addEventListener('playing', togglePlayingState, false);
									media.addEventListener('pause', togglePlayingState, false);
									media.addEventListener('paused', togglePlayingState, false);
									if (me.attr('autoplay')) media.play();
								}
							}));
						}
					}

					runthisplayer();
				})
			})(jQuery);
		}
	}
	if (!mejsloader.gs){
		mejsloader.gs = jQuery.getScript(mejspath,function(){
			mejsloader.gs = true;
			jQuery(mejsloader.init); // init exhaustive de tous les players
			onAjaxLoad(mejsloader.init); // init lors d'un load ajax
		});
	}
})();
