<?php
/***************************************************************************\
 *  SPIPAL, Utilitaire de paiement en ligne pour SPIP                      *
 *                                                                         *
 *  Copyright (c) 2007 Thierry Schmit                                      *
 *  Copyright (c) 2011 Emmanuel Saint-James                                *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined('_ECRIRE_INC_VERSION')) return;

function spipal_ajouter_boutons($flux){
	$flux['naviguer']->sousmenu['spipal']= 
	  new Bouton(_DIR_PLUGIN_SPIPAL_ICONES."/av_edit.png",_T('spipal:menu_editer'));
    
	return $flux;
}

// il faudrait rajouter la gestion des droits

function spipal_affiche_milieu($flux) {
	if ($flux['args']['exec'] !== 'articles') return $flux;
	$id_article = $flux['args']['id_article'];
	$id = sql_fetsel("id_rubrique", "spip_articles", "id_article=$id_article");
	if (empty($GLOBALS['spipal_metas']['rubriques'])
	OR preg_match("/\b$id\b/", $GLOBALS['spipal_metas']['rubriques'])) {
		$f = charger_fonction('spipal_article', 'inc');
		$flux['data'] .= $f($id_article);
	}
	return $flux;
}
?>
