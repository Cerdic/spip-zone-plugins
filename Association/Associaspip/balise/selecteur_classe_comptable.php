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

/* Cette balise affiche un selecteur de classe de reference comptable utilisant le plan comptable francais 
si la meta (reglable dans la page de config) est activee */
if (!defined("_ECRIRE_INC_VERSION")) return;

function balise_SELECTEUR_CLASSE_COMPTABLE_dist ($p) {
	/* on recupere dans l'environement la classe qui doit donc etre assignees par la fonction charger du formulaire contenant la balise */
	  return calculer_balise_dynamique($p, 'SELECTEUR_CLASSE_COMPTABLE', array('classe'));
}

function balise_SELECTEUR_CLASSE_COMPTABLE_dyn($classe) {
	$res = '<li class="editer_classe">'
			.'<label for="classe"><strong>'._T('asso:classe').'</strong></label>'
			.'<select name="classe" id="classe" class="formo">';
			
	if ($GLOBALS['association_metas']['plan_comptable_prerenseigne']) {
	include_spip('inc/association_plan_comptable');
		/* inclure les intitules de classes */
		for ($i=1; $i<11; $i++) {
			$index_classe = $i%10; /* pour avoir la classe 0 a la fin */
			$res .= '<option value="'.$index_classe.'"';
			if ($classe!='' && $classe==$index_classe) $res .= ' selected="selected"';
			$res .='>'.$index_classe.' - '.association_plan_comptable_complet($index_classe).'</option>';
		}

	} else {
		/* pas d'intitule de classes */
		for ($i=1; $i<11; $i++) {
			$index_classe = $i%10; /* pour avoir la classe 0 a la fin */
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
