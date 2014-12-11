var mejsloader;
(function(){
	var mejs_counter = 0;
	function mejs_init(){
		(function($) {
			jQuery("audio.mejs,video.mejs").each(function(){
				//console.log(this);
				mejs_counter++;
				var id = "mejs-" + (jQuery(this).attr('data-id')) + "-" + mejs_counter;
				var autoplay = jQuery(this).attr('autoplay');
				jQuery(this).attr('id',id);
				var options = jQuery(this).attr('data-mejsoptions');
				if (options)
					eval("options="+options+";");
				else
					options = {};
				new MediaElementPlayer('#'+id,jQuery.extend(options,{
					"success": function(media) {
						if (autoplay) media.play();
					}
				}));
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
