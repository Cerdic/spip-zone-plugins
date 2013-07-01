jQuery(document).ready(function($) {
	
	$('.toolbar-block .toolbar-icon').on({
		mouseover: function(){
			$(this).next('.toolbar-info').css('display','block');
		},
		mouseout: function(){
			$(this).next('.toolbar-info').css('display','none');
		}
	})

});
