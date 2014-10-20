var nivosliderloader;
(function($){
	function load_next(slider){
		var vars = slider.data('nivo:vars');
		var $imgs = $("img",slider);
		for(var i=vars.currentSlide; i<vars.currentSlide+2; i++) {
			var $img = $imgs.eq(i);
			if ($img.length  && $img.is(':not(.loaded)[data-src]')){
				$img.attr('src',$img.attr('data-src')).attr('data-src','').addClass('loaded');
			}
		}
	}
	function init(){
		$('.nivoSlider').each(function(){
			var me=$(this);
			var options=eval('options='+me.attr('data-slider')+';');
			if (options){
				options = $.extend({
						afterLoad: function(){load_next(me)},
					  afterChange: function(){load_next(me)},
					  beforeChange: function(){load_next(me)}
					},options);
				me.nivoSlider(options);
			}
		});
	}
	if (typeof nivosliderloader=="undefined"){
		nivosliderloader = jQuery.getScript(nivosliderpath,function(){
			init(); // init immediate des premiers sliders dans la page
			$(init); // init exhaustive de tous les sliders
			onAjaxLoad(init); // init lors d'un load ajax
		});
	}
})(jQuery);
