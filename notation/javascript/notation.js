/* Init du js de notation */
function notation_init(){jQuery(function(){
jQuery('.formulaire_notation .access').hide();
jQuery(function(){ jQuery('input[type=radio].star').rating(); });
jQuery('.auto-submit-star').rating({
	required: true,
	callback: function(value, link){
	jQuery(this.form).submit();
	}});
});}
jQuery(function(){notation_init.apply(document); onAjaxLoad(notation_init);});