<?php
	/**
	* Plugin Association
	*
	* Copyright (c) 2007-2008
	* Bernard Blazin & François de Montlivault
	* http://www.plugandspip.com 
	* Ce programme est un logiciel libre distribue sous licence GNU/GPL.
	* Pour plus de details voir le fichier COPYING.txt.
	*  
	**/

if (!defined("_ECRIRE_INC_VERSION")) return;

function formulaires_configurer_association_charger_dist(){
	return $GLOBALS['asso_metas'];
}

function formulaires_configurer_association_traiter_dist(){
	foreach (array(
		       "activites",
		       "classe_banques",
		       "comptes",
		       "cp",
		       "declaration",
		       "dons",
		       "email",
		       "indexation",
		       "nom",
		       "pc_activites",
		       "pc_cotisations",
		       "pc_dons",
		       "pc_prets",
		       "pc_ventes",
		       "prefet",
		       "prets",
		       "rue",
		       "siret",
		       "telephone",
		       "ventes",
		       "ville") as $k) {
		$v = _request($k);
		ecrire_meta($k, $v, 'oui', 'asso_metas');
  }
	return array('redirect' => generer_url_ecrire('association'));
}
?>
