<?php
	/**
	* Plugin Bannières
	*
	* Copyright (c) 2008 - 2009
	* François de Montlivault - Jeannot
	* Ce programme est un logiciel libre distribue sous licence GNU/GPL.
	* Pour plus de details voir le fichier COPYING.txt.
	*  
	**/

// securité
if (!defined("_ECRIRE_INC_VERSION")) return;
	
function action_visit_url() {
	$id_banniere=$_GET['banniere'];
	$url=$_GET['url'];
	
	// compteur de clics > +1clic à chaque fois
	$query = sql_update ("spip_bannieres", array('clics' => "clics+1"), "id_banniere=$id_banniere") ;

	// si le visiteur est connecté, on cherche qui il est sinon : visiteur
	$id_auteur = $GLOBALS['auteur_session']['id_auteur'];
	if($id_auteur =='') {
		$id_auteur = 'visiteur';
	}
	
	// récupère l'url de la page ou on était et l'adresse IP
	$page = $_SERVER['HTTP_REFERER'];
	$ip=$_SERVER['REMOTE_ADDR'];
	
	// garde une trace dans un fichier log.
	/*$date = date('Y-m-d H:i:s');
	$suivi = $id_banniere.' '.$date.' '.$id_auteur.' '.$page;
	spip_log($suivi,bannieres_suivi);*/
			
	// Insère les données dans une table. A supprimmer si on ne veut pas les garder
	sql_insertq('spip_bannieres_suivi',
					array(
					'id_auteur' => $id_auteur,
					'id_banniere' => $id_banniere,
					'page' => $page,
					'ip' => $ip,
					)
				);
			
header("location:".$url);

exit();
}
?>