<?php

function socialtags_choix(){
	include_spip('socialtags_fonctions');
	global $couleur_fonce;
	$cfg = is_array($cfg = lire_config('socialtags/tags')) ? $cfg : array();

	$retour = '';
	foreach (socialtags_liste() as $service) {
		$t = $service['titre'];
		$u = $service['url'];
		$a = $service['lesauteurs'];
		$d = $service['descriptif'];

		$image = find_in_path('images/'.$a.'.png');
		$checked = in_array($a, $cfg) ? ' checked="checked"' : '';

		$retour .= "<div class='choix'>
				<input type='checkbox' id='choix_{$a}' name='tags[]' value='{$a}'{$checked} />
				<label for='choix_{$a}'>
					<img src=\"{$image}\" title=\"".texte_script($t)."\" alt=\"\" />
					" . ($checked ? "<strong>$t</strong>" : $t)
					. ($d ? "&nbsp;<span style='color:$couleur_fonce;font-size:90%'>$d</span>" : "") . "
				</label>
			</div>";
	}
	return $retour;
}
?>