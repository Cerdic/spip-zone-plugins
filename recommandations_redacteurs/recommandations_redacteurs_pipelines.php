<?php

function recommandations_redacteurs_affiche_gauche($flux) {
	$exec =  $flux['args']['exec'];
	$id_article= $_REQUEST['id_article'];
	$id_rubrique= $_REQUEST['id_rubrique'];
	if (function_exists('lire_config')) {
		if ($exec=='articles_edit'){
			if(lire_config("recommandations_redacteurs/message_articles_recommandations_redacteurs", "") != "") {
				$flux['data'] .= debut_cadre_relief(_DIR_PLUGIN_RECOMMANDATIONS_REDACTEURS."img_pack/logo_recommandations_redacteurs_mini.png",true)."<div style=\"font-size: .9em;\" class=\"recommandations-redacteurs\"><strong class='titre-bloc-recommandation'>"._T('recommandationsredacteurs:titre_recommandations_articles')."</strong><br />".propre(lire_config("recommandations_redacteurs/message_articles_recommandations_redacteurs", ""))."</div>".fin_cadre_relief(true)."
				<script type=\"text/javascript\">
					$('document').ready(function(){
						if ($('.recommandations-redacteurs h3')) {
							$('.recommandations-redacteurs').children().not('h3').not('.titre-bloc-recommandation').hide();
							$('.recommandations-redacteurs h3').css('text-align','left').css('margin','.5em').css('margin-bottom','0').wrap(\"<a class='titre-recommandations' href='#' style='font-size: .9em; padding-left: 1em;'></a>\");
							$('.recommandations-redacteurs a.titre-recommandations').bind('click', function() {
								$('.recommandations-redacteurs').children().not('a.titre-recommandations').not('.titre-bloc-recommandation').hide();
								$(this).next().not('a.titre-recommandations').not('.titre-bloc-recommandation').show().next().not('a.titre-recommandations').not('.titre-bloc-recommandation').show().next().not('a.titre-recommandations').not('.titre-bloc-recommandation').show().next().not('a.titre-recommandations').show().next().not('a.titre-recommandations').not('.titre-bloc-recommandation').show().next().not('a.titre-recommandations').show().next().not('a.titre-recommandations').not('.titre-bloc-recommandation').show().next().not('a.titre-recommandations').not('.titre-bloc-recommandation').show();
								return false;
							});
						}
					});
					function titre_suivant(){

					}
				</script>
				";
			}
		}
		else if ($exec=='rubriques_edit'){
			if(lire_config("recommandations_redacteurs/message_rubriques_recommandations_redacteurs", "") != "") {
				$flux['data'] .= debut_cadre_relief(_DIR_PLUGIN_RECOMMANDATIONS_REDACTEURS."img_pack/logo_recommandations_redacteurs_mini.png",true)."<div style=\"font-size: .9em;\" class=\"recommandations-redacteurs\"><strong class='titre-bloc-recommandation'>"._T('recommandationsredacteurs:titre_recommandations_rubriques')."</strong><br />".propre(lire_config("recommandations_redacteurs/message_rubriques_recommandations_redacteurs", ""))."</div>".fin_cadre_relief(true)."
				<script type=\"text/javascript\">
					$('document').ready(function(){
						if ($('.recommandations-redacteurs h3')) {
							$('.recommandations-redacteurs').children().not('h3').not('.titre-bloc-recommandation').hide();
							$('.recommandations-redacteurs h3').css('text-align','left').css('margin','.5em').css('margin-bottom','0').wrap(\"<a class='titre-recommandations' href='#' style='font-size: .9em; padding-left: 1em;'></a>\");
							$('.recommandations-redacteurs a.titre-recommandations').bind('click', function() {
								$('.recommandations-redacteurs').children().not('a.titre-recommandations').not('.titre-bloc-recommandation').hide();
								$(this).next().not('a.titre-recommandations').not('.titre-bloc-recommandation').show().next().not('a.titre-recommandations').not('.titre-bloc-recommandation').show().next().not('a.titre-recommandations').not('.titre-bloc-recommandation').show().next().not('a.titre-recommandations').show().next().not('a.titre-recommandations').not('.titre-bloc-recommandation').show().next().not('a.titre-recommandations').show().next().not('a.titre-recommandations').not('.titre-bloc-recommandation').show().next().not('a.titre-recommandations').not('.titre-bloc-recommandation').show();
								return false;
							});
						}
					});
					function titre_suivant(){

					}
				</script>
				";
			}		
	
		}
	}

	return $flux;
}

?>