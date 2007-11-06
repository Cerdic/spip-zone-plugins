/*
 * labelhide for jQuery (c) Fil 2007, licensed GNU/GPL or MIT
 * cf. http://www.jquery.info/
 */
// hide an input and reveal it when clicking on its label
if (window.jQuery) (function($){
$.fn.labelhide = function(){
return this.each(function(){
	var label = $(this);
	if (!label.attr('for')) return;

	var input = $("input[name='"+label.attr('for')+"']");
	if (!input.length) return;

	var default_label = label.html();
	if (input.val().length) label.html(input.val()+'&nbsp;');

	input
	.hide()
	.blur(function(){
		input
		.hide();
		label
		.html(input.val().length ? input.val()+'&nbsp;' : default_label)
		.show();
	});

	label
	.click(function(){
		label.hide();
		input
		.show()
		.focus()
		.select();
	})
	.css({cursor:'text'})
	.add(input)
	.attr('title', default_label);
});};
})(jQuery);
