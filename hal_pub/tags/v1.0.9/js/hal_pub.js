/*
  plugin hal_pub
*/

$(document).ready(function() {
	$(".hal-filtre select").on('change', function() {
        $(".hal-wrapper").addClass("hal-opacity"); // annoncer graphiquement qu'on soumet le formulaire
		$("form.hal-form-recherche").submit();
	});

	// nouvelle requete ? il faut re-init la pagination
    $("form.hal-form-recherche").on('submit', function() {
		  $("form.hal-form-recherche input[name=start]").val(0);
	});
});
