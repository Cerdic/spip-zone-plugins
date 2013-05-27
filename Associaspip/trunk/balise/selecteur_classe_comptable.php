<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations
 *
 * @copyright Copyright (c) 2007 Bernard Blazin & Francois de Montlivault
 * @copyright Copyright (c) 2010 Emmanuel Saint-James
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
\***************************************************************************/

if (!defined('_ECRIRE_INC_VERSION'))
	return;

/**
 * Cette balise affiche un selecteur de classe de reference comptable utilisant
 * le plan comptable francais
si la meta (reglable dans la page de config) est activee
 */
function balise_SELECTEUR_CLASSE_COMPTABLE_dist ($p) {
	// on recupere dans l'environement la classe qui doit donc etre assignees par la fonction charger du formulaire contenant la balise
	return calculer_balise_dynamique($p, 'SELECTEUR_CLASSE_COMPTABLE', array('classe'));
}

function balise_SELECTEUR_CLASSE_COMPTABLE_dyn($classe) {
	$res = '<li class="editer_classe">'
		.'<label for="classe">'._T('asso:classe').'</label>';
	if ($GLOBALS['association_metas']['plan_comptable']) {
		include_spip('inc/association_comptabilite'); // javascript sur le onchange pour mettre le selecteur de code directement au debut de la classe selectionnée et appeler la fonction onchange du selecteur (repercuter la modif dans les champs libres code et intitule)
		$res .= '<select name="classe" id="classe" class="select" onchange="var currentVal = String(document.getElementById(\'classe\').value).split(\'-\'); var optGroupElt = document.getElementById(\'codeOptGrp\'+currentVal[0]); if (optGroupElt) {optGroupElt.childNodes[0].selected=\'selected\'; document.getElementById(\'selecteur_code_comptable\').onchange()}">';
		for ($i=1; $i<11; $i++) { // inclure les intitules de classes
			$index_classe = $i%10; // pour avoir la classe 0 a la fin
			$res .= '<option value="'.$index_classe.'"';
			if ($classe!='' && $classe==$index_classe) $res .= ' selected="selected"';
			$res .='>'.$index_classe.' - '. comptabilite_reference_intitule($index_classe) .'</option>';
		}
	} else { // pas d'intitule de classes
		$res .= '<select name="classe" id="classe" class="select">';
		for ($i=1; $i<11; $i++) {
			$index_classe = $i%10; // pour avoir la classe 0 a la fin ???
			$res .= '<option value="'.$index_classe.'"';
			if ($classe!='' && $classe==$index_classe) $res .= ' selected="selected"';
			$res .='>'.$index_classe.'</option>';
		}
	}
	$res .='</select>'
		.'</li>';

	return $res;
}

?>