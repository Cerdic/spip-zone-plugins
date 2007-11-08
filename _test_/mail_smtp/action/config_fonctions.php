<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2007                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

// chryjs : fonction de retour introduite mais difficile de rester compatible en étant propre

if (!defined("_ECRIRE_INC_VERSION")) return;

// Mise a jour de l'option de configuration du proxy

function action_config_fonctions() {

	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

	if (_request('smtp_host')!==NULL){
		ecrire_meta('smtp_host',_request('smtp_host'));
		ecrire_meta('smtp_port',_request('smtp_port'));
		ecrire_meta('smtp_auth',_request('smtp_auth'));
		ecrire_meta('smtp_username',_request('smtp_username'));
		ecrire_meta('smtp_password',_request('smtp_password'));
		ecrire_metas();
	}

	// message a afficher dans l'exec de retour
	$r = rawurldecode(_request('redirect'));
	redirige_par_entete($r);
}
?>
