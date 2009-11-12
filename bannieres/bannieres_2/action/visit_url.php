<?php
	/**
	* Plugin Banni�res
	*
	* Copyright (c) 2008 - 2009
	* Fran�ois de Montlivault - Jeannot
	* Ce programme est un logiciel libre distribue sous licence GNU/GPL.
	* Pour plus de details voir le fichier COPYING.txt.
	*  
	**/

// securit�
if (!defined("_ECRIRE_INC_VERSION")) return;
	
function action_visit_url() {
	$id_banniere=$_GET['banniere'];
	
	// compteur de clics > +1clic � chaque fois
	$query = sql_update ("spip_bannieres", array('clics' => "clics+1"), "id_banniere=$id_banniere") ;

	// rechercher l'url de destination
	$url = sql_getfetsel ('site', 'spip_bannieres', 'id_banniere='.$id_banniere);
	
	// si le visiteur est connect�, on cherche qui il est sinon : visiteur
	$id_auteur = $GLOBALS['auteur_session']['id_auteur'];
	if($id_auteur =='') {
		$id_auteur = 'visiteur';
	}
	
	// r�cup�re l'url de la page ou on �tait et l'adresse IP
	$page = $_SERVER['HTTP_REFERER'];
	$ip=$_SERVER['REMOTE_ADDR'];
	
	// garde une trace dans un fichier log. A supprimmer si on ne veut pas en garder
	$date = date('Y-m-d H:i:s');
	$suivi = $id_banniere.' '.$date.' '.$id_auteur.' '.$page;
	spip_log($suivi,bannieres_suivi);
			
	// autre solution : ins�re les donn�es dans une table. A supprimmer si on ne veut pas les garder
	sql_insertq('spip_bannieres_suivi',
					array(
					'id_auteur' => $id_auteur,
					'id_banniere' => $id_banniere,
					'page' => $page,
					'ip' => $ip,
					)
				);
			
header("location:".$url);

exit;
}
?>