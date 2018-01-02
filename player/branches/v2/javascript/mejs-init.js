var mejsloader;
(function(){
	var L=mejsloader;
	if (typeof L == "undefined")
		mejsloader = L = {gs:null,plug:{},css:{},init:null,c:0,cssload:null,to_run:{}};
	if (!L.init){
		L.cssload = function (f){
			if (typeof L.css[f]=="undefined"){
				L.css[f] = true;
				var stylesheet = document.createElement('link');
				stylesheet.href = f;
				stylesheet.rel = 'stylesheet';
				stylesheet.type = 'text/css';
				document.getElementsByTagName('head')[0].appendChild(stylesheet);
			}
		}
		L.runplayers = function(infos){
			if (infos) {
				L.to_run[infos.id] = infos;
				L.to_run[infos.id].cssLoaded = false;
				L.to_run[infos.id].toRun = true;
				//console.log(L.to_run);
			}
			for (var id in L.to_run){
				var opt = L.to_run[id];
				var run = L.to_run[id].toRun;
				//console.log(id);
				//console.log(opt);
				if (!L.to_run[id].cssLoaded) {
					for (var c in opt.css){
						L.cssload(opt.css[c]);
					}
					L.to_run[id].cssLoaded = true;
				}
				for (var p in opt.plugins){
					//console.log(p);
					//console.log(L.plug[p]);
					// load this plugin
					if (typeof L.plug[p]=="undefined"){
						//console.log("Load Plugin "+p);
						run = false;
						L.plug[p] = false;
						jQuery.getScript(opt.plugins[p], function (){
							L.plug[p] = true;
							L.runplayers(false);
						});
					}
					// this plugin is loading
					else if (L.plug[p]==false){
						//console.log("Plugin "+p+" loading...");
						run = false; // will be run on next call of runplayer
					}
					else {
						//console.log("Plugin "+p+" loaded");
					}
				}
				if (run){
					mejs.MediaFeatures.isChromium = false; // bugfix Chromium : can now play mpx sound files
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
							if (jQuery('#'+id).attr('autoplay')) media.play();
						}
					}));
					L.to_run[id].toRun = false;
				}
			}
		}
		L.init = function (){
			if (!(L.gs===true)) return;
			(function ($){
				jQuery("audio.mejs,video.mejs").not('.done').each(function (){
					var me = jQuery(this).addClass('done');
					//console.log(this);
					var id;
					if (!(id = me.attr('id'))){
						id = "mejs-"+(me.attr('data-id'))+"-"+(L.c++);
						me.attr('id', id);
					}
					var opt = {id:id, options: {}, plugins: {}, css: []}, i, v;
					for (i in opt){
						if (v = me.attr('data-mejs'+i)) opt[i] = jQuery.parseJSON(v);
					}

					L.runplayers(opt);
				})
			})(jQuery);
		}
	}
	if (!L.gs){
		if (typeof mejscss !== "undefined"){
			L.cssload(mejscss);
		}
		L.gs = jQuery.getScript(mejspath,function(){
			L.gs = true;
			L.init(); // init immediate des premiers players dans la page
			jQuery(L.init); // init exhaustive de tous les players
			onAjaxLoad(L.init); // init lors d'un load ajax
		});
	}
})();
