<?php

function typo_partout_insertion_in_body_prive ($texte)
{
	include_spip('inc/barre');
	include_spip('inc/presentation');
	include_spip('inc/documents');
	$activer_barre_rubriques = "";
	if (lire_config('typo_partout/rubriques_typo_partout') == "on")
	{
		$activer_barre_rubriques = "$('body.rubriques').find('#barre_typo_texte').insertBefore(\"textarea[@name=texte]\");
		$('body.rubriques').find('#barre_typo_descriptif').insertBefore(\"textarea[@name=descriptif]\");
		$('body.rubriques').find('.cadre-formulaire').find('#barre_typo_texte').css(\"display\",\"block\");
		$('body.rubriques').find('.cadre-formulaire').find('#barre_typo_descriptif').css(\"display\",\"block\");";	
	}
	$activer_barre_mots = "";
	if (lire_config('typo_partout/mots_typo_partout') == "on")
	{
		$activer_barre_mots="$('body.mots').find('#barre_typo_texte').insertBefore(\"textarea[@name=texte]\");
		$('body.mots').find('#barre_typo_descriptif').insertBefore(\"textarea[@name=descriptif]\");
		$('body.mots').find('.cadre-formulaire').find('#barre_typo_texte').css(\"display\",\"block\");
		$('body.mots').find('.cadre-formulaire').find('#barre_typo_descriptif').css(\"display\",\"block\");";
	}
	$activer_barre_configuration = "";
	if (lire_config('typo_partout/configuration_typo_partout') == "on")
	{
		$activer_barre_configuration="$('body.configuration').find('#barre_typo_descriptif_site').insertBefore(\"textarea[@name=descriptif_site]\");
		$('body.configuration').find('.cadre-couleur').find('#barre_typo_descriptif_site').css(\"display\",\"block\");";
	}
	$activer_barre_sites = "";
	if (lire_config('typo_partout/sites_typo_partout') == "on")
	{
		$activer_barre_sites="$('body.sites').find('#barre_typo_descriptif').insertBefore(\"textarea[@name=descriptif]\");
		$('body.sites').find('.cadre-formulaire').find('#barre_typo_descriptif').css(\"display\",\"block\");";
	}
	$ajout_texte = "<script type=\"text/javascript\">
		$(document).ready(function(){
			
			$('form[textarea]').attr('name', 'formulaire');
			".$activer_barre_rubriques.$activer_barre_mots.$activer_barre_configuration.$activer_barre_sites."
			
		});
	</script><script type=\"text/javascript\" src=\"../dist/javascript/spip_barre.js\"></script>
";

	$barre_temporaire = "
		<div id=\"barre_typo_texte\" style=\"display: none;\">".afficher_barre('document.formulaire.texte')."</div>
		<div id=\"barre_typo_descriptif\" style=\"display: none;\">".afficher_barre('document.formulaire.descriptif')."</div>		
		<div id=\"barre_typo_descriptif_site\" style=\"display: none;\">".afficher_barre('document.formulaire.descriptif_site')."</div>		
	";

	return $ajout_texte.$texte.$barre_temporaire;
	
}
function typo_partout_insertion_in_head_prive ($texte)
{
	return $texte."<script type=\"text/javascript\" src=\"../dist/javascript/spip_barre.js\"></script>";
}

?>