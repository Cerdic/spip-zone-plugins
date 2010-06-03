<?php
	/**
	* Plugin Association
	*
	* Copyright (c) 2007-2008
	* Bernard Blazin & Fran�ois de Montlivault
	* http://www.plugandspip.com 
	* Ce programme est un logiciel libre distribue sous licence GNU/GPL.
	* Pour plus de details voir le fichier COPYING.txt.
	*  
	**/

if (!defined("_ECRIRE_INC_VERSION")) return;

function formulaires_configurer_association_charger_dist(){
	return $GLOBALS['asso_metas'];
}

// version amelioree de la RegExp de cfg_formulaire.
define('_EXTRAIRE_SAISIES', 
	'#<(?:(select|textarea)|input type=["\'](text|password|checkbox|radio|hidden|file)["\']) name=["\'](\w+)(\[\w*\])?["\'](?: class=["\']([^\'"]*)["\'])?( multiple=)?[^>]*?>#ims');

function formulaires_configurer_association_traiter_dist(){

	$form = _DIR_PLUGINS . 'Association_2.0'.'/formulaires/configurer_association.html';
	$form = $form ? file_get_contents($form) : '';
	if (!$form) spip_log('configurer_association sans formulaire');
	if (preg_match_all(_EXTRAIRE_SAISIES, $form, $r, PREG_SET_ORDER)) {
		foreach($r as $regs) {
			$k = $regs[3];
			$v = _request($k);
			ecrire_meta($k, $v, 'oui', 'asso_metas');
		}
	}
	return array('redirect' => generer_url_ecrire('association'));
}
?>
