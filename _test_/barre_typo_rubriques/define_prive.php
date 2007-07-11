<?php

function typo_rubrique_insertion_in_body_prive ($texte)
{
	include_spip('inc/barre');
	include_spip('inc/presentation');
	include_spip('inc/documents');
	$ajout_texte = "<script type=\"text/javascript\">
		$(document).ready(function(){
			
			$('body.rubriques').find('.cadre-formulaire').find(\"form\").attr('name', 'formulaire');
			$('#barre_typo').insertBefore(\"textarea[@name=texte]\");
			
			$('body.rubriques').find('.cadre-formulaire').find('#barre_typo').css(\"display\",\"block\");
		});
	</script><script type=\"text/javascript\" src=\"../dist/javascript/spip_barre.js\"></script>
";

	$barre_temporaire = "<div id=\"barre_typo\" style=\"display: none;\">".afficher_barre('document.formulaire.texte')."</div>";

	return $ajout_texte.$texte.$barre_temporaire;
	
}
function typo_rubrique_insertion_in_head_prive ($texte)
{
	return $texte."<script type=\"text/javascript\" src=\"../dist/javascript/spip_barre.js\"></script>";
}

?>