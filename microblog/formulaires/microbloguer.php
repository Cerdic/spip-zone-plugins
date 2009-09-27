<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2009                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

// chargement des valeurs par defaut des champs du formulaire
function formulaires_microbloguer_charger_dist(){
	return 
		array(
			'status' => '',
		);
}

// chargement des valeurs par defaut des champs du formulaire
function formulaires_microbloguer_verifier_dist(){
	$erreurs = array();
	if (!$status = _request('status')){
		$erreurs['status'] = _T('info_obligatoire');
	}
	elseif (strlen($status)>140){
		$erreurs['status'] = _T('microblog:longueur_maxi_status');
	}

	return
		$erreurs;
}


function formulaires_microbloguer_traiter_dist(){
	$res = array();
	if ($status = _request('status')){
		include_spip('inc/microblog');
		microblog($status);
		set_request('status','');
		$res = array('message_ok'=>$status,'editable'=>true);
	}
	else
		$res = array('message_erreur'=>'???','editable'=>true);

	return
		$res;
}

?>
