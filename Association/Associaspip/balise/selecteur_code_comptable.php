<?php
/***************************************************************************
 *  Associaspip, extension de SPIP pour gestion d'associations             *
 *                                                                         *
 *  Copyright (c) 2007 Bernard Blazin & Francois de Montlivault (V1)       *
 *  Copyright (c) 2010-2011 Emmanuel Saint-James & Jeannot Lapin (V2)      *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

/* Cette balise affiche un selecteur de code de reference comptable utilisant le plan comptable francais */
/* Le selecteur n'est affiche que si la meta plan_comptable_prerenseigne est activee dans la configuration du pluging */
/* Lorsque la valeur du selecteur change, on va remplir(jQuery) les champs code et intitule qui sont presents sur la page */
/* ou est insere la balise */
if (!defined("_ECRIRE_INC_VERSION")) return;

function balise_SELECTEUR_CODE_COMPTABLE_dist ($p) {
	/* on recupere dans l'environement le code qui doit donc etre assignees par la fonction charger du formulaire contenant la balise */
	  return calculer_balise_dynamique($p, 'SELECTEUR_CODE_COMPTABLE', array('code'));
}

function balise_SELECTEUR_CODE_COMPTABLE_dyn($code) {
		
	/* si la meta est activee on renvoit le selecteur */
	if ($GLOBALS['association_metas']['plan_comptable_prerenseigne']) {
	include_spip('inc/association_plan_comptable');
		$pcc = association_plan_comptable_complet(); /* on recupere tout le plan comptable dans un tableau */
		/* pour afficher le code commencant comme celui existant si ce dernier n'est pas dans le plan comptable */
		if ($code != '') $code = association_plan_comptable_complet($code, true); /* avec un second parametre a true, la fonction renvoie le code lui meme si il est present dans le tableau ou le premier code hierarchiquement superieur present */
		
		/* code javascript en dur qui recopie l'intitule et le code dans les champs d'editions sur la page d'edition de la reference */
		$res = '<select id="selecteur_code_comptable" class="formo" onchange="var currentVal=String(document.getElementById(\'selecteur_code_comptable\').value).split(\'-\'); document.getElementById(\'code\').value=currentVal[0]; document.getElementById(\'intitule\').value=currentVal[1];">';
		$firstOptgroup = true;
		/* on boucle sur tout le tableau */
		foreach ($pcc as $index_code => $intitule) {
			if ($index_code<9) { /* si le code est inferieur a 9, c'est une definition de classe, on en fait un optgroup */
				if (!$firstOptgroup) $res .= '</optgroup>';
				$res .= '<optgroup id="codeOptGrp'.$index_code.'" label="'.$index_code.' - '.$intitule.'">';
				$firstOptgroup = false;
			} else { /* sinon c'est une definition de compte -> une option du select */
				$res .= '<option value="'.$index_code.'-'.$intitule.'"';
				if ($code!='' && $code==$index_code) $res .=' selected="selected"';
				$res .= '>'.$index_code.' - '.$intitule.'</option>';
			}
		}
		$res .= '</optgroup></select>';
		return $res;
	}

}
?>
