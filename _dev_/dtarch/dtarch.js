function initialiser_archives() {
	/* empecher le get standard pour les boutons ancien et recent */	$("#archives p a").attr("href", "#archives");
	/* attribuer les comportements */	$("#archives p a").click (		function() {			charger_archives(this, archives_parametres, sablier, 'decal', $(this).find("img").attr("alt"));		}	);
	/* TODO: click sur les liens d'archives = ajax de la noisette*/
	$("#archives li a").click (		function() {
			charger_archives(this, archives_parametres, sablier, 'date', $(this).parent().attr('class'));		}	);
	/* TODO: click sur les liens d'archives = ajax de la zone de resultat (c'est necessaire ?) */}
function charger_archives(_this, archives_parametres, sablier, parametre, valeur) {
	/* faire patienter */
	$("#archives h2").append(sablier);
	/*appeler la noisette en get avec le parametre de decalage */
	var decal = (parametre == 'decal') ? "&decal=" + valeur : ''; //si clic sur ancien/recent
	/* TODO: traiter le parametre date (et son nomm de parametre) */
	var archives_date = (parametre == 'date') ? '&date=' + valeur: ''; //si clic sur date
	$.ajax({
		type:	 "GET",
		url:	 "spip.php",
		data:	 archives_parametres + decal + archives_date,
		success: function(html) {
			/* rafrachir les elements de la noisette */
			$("#archives p.ancien").empty();
			$("#archives p.recent").empty();

			/* star wars : on cache */
			$("#archives ul").hide('slow');
			/* star wars : on transforme */
			$("#archives ul").html($("ul",html).html());
			/* star wars : on montre */
			$("#archives ul").show('slow');

			$("#archives p.ancien").html($("p.ancien",html).html());
			$("#archives p.recent").html($("p.recent",html).html());
			initialiser_archives();		}
	});
	/* c'est fini */
	$("#archives h2 img.sablier").remove();
}
$(document).ready(function() {
	initialiser_archives();});