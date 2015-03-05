<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function socialtags_choix(){
	include_spip('socialtags_fonctions');
	global $couleur_fonce;
	$cfg = is_array($cfg = lire_config('socialtags/tags')) ? $cfg : array();

	$retour = array();
	foreach (socialtags_liste() as $service) {
		$t = $service['titre'];
		$u = $service['url'];
		$a = $service['lesauteurs'];
		$d = isset($service['descriptif']) ? $service['descriptif'] : '';

		$category = (count($service['tags'])?textebrut(reset($service['tags'])):'99');
		$image = 'data:image/png;base64,'.base64_encode(file_get_contents(find_in_path('images/'.$a.'.png')));
		//$image = find_in_path('images/'.$a.'.png');
		$checked = in_array($a, $cfg) ? ' checked="checked"' : '';

		$retour[$category] .= "<div class='choix'>
				<input type='checkbox' id='choix_{$a}' name='tags[]' value='{$a}'{$checked} />
				<label for='choix_{$a}'>
					<img src=\"{$image}\" title=\"".texte_script($t)."\" alt=\"\" style=\"max-width:16px; height:auto;\" />
					" . ($checked ? "<strong>$t</strong>" : $t)
					. ($d ? "&nbsp;<span style='color:$couleur_fonce;font-size:90%'>$d</span>" : "") . "
				</label>
			</div>";
	}
	ksort($retour);
	return implode("<hr />",$retour);
}

