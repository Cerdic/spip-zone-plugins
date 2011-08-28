jQuery.fn.extend({
	incrementalInput: function(){
		var selector = this;
		//Ajoute un  +/- au champ
		selector.wrap('<span class="ii_field"></span>').after('<a href="#" class="plus">+</a><a href="#" class="moins">-</a>');
		
		//ajoute ou retire une unité (1) lors du clic
		$('.plus').click(function(e){
			var fieldValue = parseFloat(selector.val());
			selector.attr('value',fieldValue+1);
			e.preventDefault();
		})
		$('.moins').click(function(e){
			var fieldValue = parseFloat(selector.val());
			if(fieldValue>0){
				selector.attr('value',fieldValue-1);
			}
			e.preventDefault();
		})
	}
});