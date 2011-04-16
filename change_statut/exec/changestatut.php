<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2008                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');

function exec_changestatut(){
	// si on est ou etait admin
	if(!autoriser('changestatut')) {
		include_spip('inc/minipres');
		echo minipres();
		exit;
	}

	$statut_demande = _request("statut");
	$id_auteur = intval($GLOBALS['visiteur_session']['id_auteur']) ;
	$auteur = sql_fetsel("*", "spip_auteurs", "id_auteur=$id_auteur");

	spip_log("id_auteur : $id_auteur - statut_demande : $statut_demande");
	
	$erreur=false;
	switch($statut_demande){
		case 'redacteur' :
			sql_updateq("spip_auteurs", array("statut" => "1comite","webmestre" => "non","statut_orig" => "webmestre"), "id_auteur=$id_auteur");
			session_set('statut','1comite');
			session_set('webmestre','non');
		break;
		case 'admin' :
			sql_updateq("spip_auteurs", array("statut" => "0minirezo","webmestre" => "non","statut_orig" => "webmestre"), "id_auteur=$id_auteur");
			session_set('statut','0minirezo');
			session_set('webmestre','non');
		break;
		case 'webmestre' :
			sql_updateq("spip_auteurs", array("statut" => "0minirezo","webmestre" => "oui","statut_orig" => ""), "id_auteur=$id_auteur");
			session_set('statut','0minirezo');
			session_set('webmestre','oui');
		break;
		default :
			spip_log("Erreur statut_demande : $statut_demande");
			$erreur=true;
		break;
	}

	if(!$erreur) {
		$config = lire_config('changestatut',array());
		$config['statut'] = $statut_demande ;
		ecrire_meta('changestatut', serialize($config));
	}

	// $_SERVER["HTTP_REFERER"] ne fonctionne pas partout
	$referer = _DIR_RESTREINT_ABS ;
	if (isset($_SERVER['HTTP_REFERER'])) $referer = $_SERVER['HTTP_REFERER'];
	else if (isset($GLOBALS["HTTP_SERVER_VARS"]["HTTP_REFERER"])) $referer = $GLOBALS["HTTP_SERVER_VARS"]["HTTP_REFERER"];
	include_spip('inc/headers');
	redirige_par_entete($referer, true);
}

?>