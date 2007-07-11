<?php

function typo_partout_insertion_in_body_prive ($texte)
{
	include_spip('inc/barre');
	include_spip('inc/presentation');
	include_spip('inc/documents');
	$ajout_texte = "<script type=\"text/javascript\">
		$(document).ready(function(){
			
			$('form[textarea]').attr('name', 'formulaire');

			$('body.rubriques').find('#barre_typo_texte').insertBefore(\"textarea[@name=texte]\");
			$('body.rubriques').find('#barre_typo_descriptif').insertBefore(\"textarea[@name=descriptif]\");
			$('body.mots').find('#barre_typo_texte').insertBefore(\"textarea[@name=texte]\");
			$('body.mots').find('#barre_typo_descriptif').insertBefore(\"textarea[@name=descriptif]\");
			$('body.configuration').find('#barre_typo_descriptif_site').insertBefore(\"textarea[@name=descriptif_site]\");
			
			$('body.rubriques').find('.cadre-formulaire').find('#barre_typo_texte').css(\"display\",\"block\");
			$('body.rubriques').find('.cadre-formulaire').find('#barre_typo_descriptif').css(\"display\",\"block\");
			$('body.mots').find('.cadre-formulaire').find('#barre_typo_texte').css(\"display\",\"block\");
			$('body.mots').find('.cadre-formulaire').find('#barre_typo_descriptif').css(\"display\",\"block\");
			$('body.configuration').find('.cadre-couleur').find('#barre_typo_descriptif_site').css(\"display\",\"block\");
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