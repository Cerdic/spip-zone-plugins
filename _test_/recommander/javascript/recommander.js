$("div#formulaire_recommander").hide();
function recommander_js() {
	$("div#formulaire_recommander").css("height","");
	$("div#formulaire_recommander form")
	.prepend(
		"<input name='action' value='fragment_recommander' type='hidden' />"
	)
	.ajaxForm({"target":"#formulaire_recommander",
		"success":recommander_js,
		"beforeSubmit":function() {
			$("#formulaire_recommander").prepend(ajax_image_searching);
		},
		/* avant jquery 1.2 */
		"after":recommander_js,
		"before":function() {
			$("#formulaire_recommander").prepend(ajax_image_searching);
		}
	});
}
recommander_js();
$("#recommander>h2").click(function(){
	$("div#formulaire_recommander:visible").slideUp("slow");
	$("div#formulaire_recommander:hidden").slideDown("slow");
});