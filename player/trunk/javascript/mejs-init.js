var mejsloader;
var mejsplugins={};
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
				function runthisplayer(){
					var run = true;
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
								jQuery(media).closest('.mejs-inner').addClass('paused');
								media.addEventListener('play',function() {jQuery(media).closest('.mejs-inner').removeClass('paused').addClass('playing');}, false);
								media.addEventListener('playing',function() {jQuery(media).closest('.mejs-inner').removeClass('paused').addClass('playing');}, false);
								media.addEventListener('pause',function() {jQuery(media).closest('.mejs-inner').removeClass('playing').addClass('paused');}, false);
								media.addEventListener('paused',function() {jQuery(media).closest('.mejs-inner').removeClass('playing').addClass('paused');}, false);
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
