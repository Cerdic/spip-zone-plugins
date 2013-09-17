function nivoslider_load_next_img(slider){
	var toload = jQuery("img:not(.loaded)[data-src^=]",slider);
	if (toload.length) { toload = toload.eq(0); toload.attr('src',toload.attr('data-src')).attr('data-src','').addClass('loaded');}
}
jQuery(function() {
	jQuery('.nivoSlider').each(function(){
		var me=jQuery(this);
		var options=eval('options='+me.attr('data-slider')+';');
		if (options){
			options = jQuery.extend({
					afterLoad: function(){nivoslider_load_next_img(me)},
				  afterChange: function(){nivoslider_load_next_img(me)}
				},options);
			me.nivoSlider(options);
		}
	});
});