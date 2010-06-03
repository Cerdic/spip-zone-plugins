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
	return $GLOBALS['association_metas'];
}

// version amelioree de la RegExp de cfg_formulaire.
define('_EXTRAIRE_SAISIES', 
	'#<(?:(select|textarea)|input type=["\'](text|password|checkbox|radio|hidden|file)["\']) name=["\'](\w+)(\[\w*\])?["\'](?: class=["\']([^\'"]*)["\'])?( multiple=)?[^>]*?>#ims');

// Cette fonction est ecrite de maniere generique pour servir ailleurs.
// Elle se base sur la valeur du parametre de requete "formulaire_action"
// censee etre une chaine "configurer_P" ou P est le prefix du plugin.
// Elle cherche le source du formulaire dans dans le repertoire formulaires/
// ce qui lui permet de calculer la liste des noms des saisies.
// Elle enregistre dans la table des meta de prefixe P
// les valeurs de ces saisies
// Elle renvoie ensuite sur le script de nom P dans exec.

function formulaires_configurer_association_traiter_dist(){

	$form = _request('formulaire_action');
	$f = find_in_path($form.'.' . _EXTENSION_SQUELETTES, 'formulaires/');
	$formulaire = $f ? file_get_contents($f) : '';
	if (!$formulaire) spip_log("formulaire '$form' introuvable");
	$prefix = substr($form, strlen('configurer_'));
	$table = $prefix . '_metas';
	if (preg_match_all(_EXTRAIRE_SAISIES, $formulaire, $r, PREG_SET_ORDER)) {
		foreach($r as $regs) {
			$k = $regs[3];
			$v = _request($k);
			ecrire_meta($k, $v, 'oui', $table);
		}
	}
	return array('redirect' => generer_url_ecrire($prefix));
}
?>
