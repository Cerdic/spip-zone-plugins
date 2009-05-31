<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2006                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

/*
	Modifications : James pour la balise #SESSION (2006)
*/
# ou est l'espace prive ?
@define('_DIR_RESTREINT_ABS', 'ecrire/');
include_once _DIR_RESTREINT_ABS.'inc_version.php';

# rediriger les anciens URLs de la forme page.php3fond=xxx
if (isset($_GET['fond']))
	redirige_par_entete(generer_url_public($_GET['fond']));

$tmp_marqueur = $GLOBALS['marqueur'];

if(isset($GLOBALS['auteur_session']) AND is_array($GLOBALS['auteur_session']) AND isset($GLOBALS['auteur_session']['id_auteur'])) {
	if(@function_exists('session_start') AND isset($contexte_inclus['session'])){
		$contexte_inclus['session'] = $contexte_inclus['session'] ?
			strtoupper($contexte_inclus['session']) :
			'SPIPSESSID';
		$session = session_name($contexte_inclus['session']);
		session_start();
		$GLOBALS['auteur_session'] = array_merge(
			$_SESSION,
			$GLOBALS['auteur_session']
		);
		//faire un cache base sur le contenu de $_SESSION
		$GLOBALS['marqueur'] .= ':'.md5(serialize($_SESSION));
	}

	//faire un cache base sur l'id_auteur
	$GLOBALS['marqueur'] .= ':session'.$GLOBALS['auteur_session']['id_auteur'];
	//pour faire un cache base sur le statut des visiteurs, decommenter la ligne
	//ci desous, et ajouter un # devant la ligne au dessus.
	//vous pouvez aussi cloner ce fichier et le nommer statut.php
	//puis appeler <INCLURE(statut.php){fond=page_visiteurs}> dans vos squelettes
	#$GLOBALS['marqueur'] .= ':'.$GLOBALS['auteur_session']['statut'];

}
# au travail...
include _DIR_RESTREINT_ABS.'public.php';

$GLOBALS['marqueur'] = $tmp_marqueur;

?>
