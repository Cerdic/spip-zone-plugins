var mejsloader;
var mejsplugins={};
var mejscss={};
(function(){
	var mejs_counter = 0;
	function mejs_init(){
		(function($) {
			jQuery("audio.mejs,video.mejs").not('.done').each(function(){
				jQuery(this).addClass('done');
				//console.log(this);
				mejs_counter++;
				var id = "mejs-" + (jQuery(this).attr('data-id')) + "-" + mejs_counter;
				var autoplay = jQuery(this).attr('autoplay');
				jQuery(this).attr('id',id);
				var options = jQuery.parseJSON(jQuery(this).attr('data-mejsoptions'));
				var plugins = jQuery.parseJSON(jQuery(this).attr('data-mejsplugins'));
				var css = jQuery.parseJSON(jQuery(this).attr('data-mejscss'));
				function runthisplayer(){
					var run = true;
					//console.log(css);
					for(var c in css){
						if (typeof mejscss[css[c]]=="undefined"){
							mejscss[css[c]] = true;
							var stylesheet = document.createElement('link');
							stylesheet.href = css[c];
							stylesheet.rel = 'stylesheet';
							stylesheet.type = 'text/css';
							document.getElementsByTagName('head')[0].appendChild(stylesheet);
						}
					}
					for(var p in plugins){
						//console.log(p);
						//console.log(mejsplugins[p]);
						// load this plugin
						if (typeof mejsplugins[p]=="undefined"){
							//console.log("Load Plugin "+p);
							run = false;
							mejsplugins[p] = false;
							jQuery.getScript(plugins[p],function(){mejsplugins[p] = true;runthisplayer();});
						}
						// this plugin is loading
						else if(mejsplugins[p]==false){
							//console.log("Plugin "+p+" loading...");
							run = false;
						}
						else {
							//console.log("Plugin "+p+" loaded");
						}
					}
					if (run) {
						new MediaElementPlayer('#'+id,jQuery.extend(options,{
							"success": function(media) {
								function togglePlayingState(){
									jQuery(media).closest('.mejs-inner').removeClass(media.paused?'playing':'paused').addClass(media.paused?'paused':'playing');
								}
								togglePlayingState();
								media.addEventListener('play',togglePlayingState, false);
								media.addEventListener('playing',togglePlayingState, false);
								media.addEventListener('pause',togglePlayingState, false);
								media.addEventListener('paused',togglePlayingState, false);
								if (autoplay) media.play();
							}
						}));
					}
				}
				runthisplayer();
			})
		})(jQuery);
	}
	if (typeof mejsloader=="undefined"){
		mejsloader = jQuery.getScript(mejspath,function(){
			mejs_init(); // init immediate des premiers players dans la page
			jQuery(mejs_init); // init exhaustive de tous les players
			onAjaxLoad(mejs_init); // init lors d'un load ajax
		});
	}
})();
