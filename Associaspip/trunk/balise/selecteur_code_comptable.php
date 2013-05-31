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
 * Cette balise affiche un selecteur de code de reference comptable utilisant le plan comptable choisi
 * Le selecteur n'est affiche que si la meta plan_comptable est
 * activee dans la configuration du pluging. Lorsque la valeur du selecteur
 * change, on va remplir(jQuery) les champs code et intitule qui sont presents
 * sur la page ou est inseree la balise
 */
function balise_SELECTEUR_CODE_COMPTABLE_dist ($p) {
	// on recupere dans l'environement le code qui doit donc etre assignees par la fonction charger du formulaire contenant la balise
	return calculer_balise_dynamique($p, 'SELECTEUR_CODE_COMPTABLE', array('code'));
}

function balise_SELECTEUR_CODE_COMPTABLE_dyn($code) {
	if ($GLOBALS['association_metas']['plan_comptable']) { // si la meta est activee on renvoit le selecteur
		include_spip('inc/association_comptabilite');
		$pcc = comptabilite_liste_plancomplet(); // on recupere tout le plan comptable dans un tableau pour afficher le code commencant comme celui existant si ce dernier n'est pas dans le plan comptable
		if ($code != '')
			$code = comptabilite_reference_intitule($code, -1); // avec un second parametre a TRUE, la fonction renvoie le code lui meme s'il est present dans le tableau ou le premier code hierarchiquement superieur present
		$res = '<select id="selecteur_code_comptable" class="select" onchange="var currentVal=String(document.getElementById(\'selecteur_code_comptable\').value).split(\'-\'); document.getElementById(\'code\').value=currentVal[0]; document.getElementById(\'intitule\').value=currentVal[1];">'; // code javascript en dur qui recopie l'intitule et le code dans les champs d'editions sur la page d'edition de la reference
		$firstOptgroup = TRUE;
		foreach ($pcc as $index_code => $intitule) { // on boucle sur tout le tableau
			if ($index_code<9) { // si le code est inferieur a 9, c'est une definition de classe, on en fait un optgroup
				if (!$firstOptgroup) $res .= '</optgroup>';
				$res .= '<optgroup id="codeOptGrp'.$index_code.'" label="'.$index_code.' - '.$intitule.'">';
				$firstOptgroup = FALSE;
			} else { // sinon c'est une definition de compte -> une option du select
				$res .= '<option value="'.$index_code.'-'.$intitule.'"';
				if ($code!='' && $code==$index_code) $res .=' selected="selected"';
				$res .= '>'.$index_code.' - '.$intitule."</option>\n";
			}
		}
		$res .= '</optgroup></select>';
		return $res;
	}

}

?>