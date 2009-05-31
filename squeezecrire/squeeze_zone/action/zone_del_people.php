<?php
/*

 *
 * Auteurs :
 * kent1
 * © 2007 - Distribue sous licence GNU/GPL
 *
 */

function action_zone_del_people(){
	global $auteur_session;
	
	// Qui est on?
	$id_auteur = $auteur_session['id_auteur'];
	
		include_spip("inc/actions");
		
		// La personne a virer
		$newauteur = _request('id_auteur');
		
		// La zone concernee
		$id = _request('id_rubrique');
		
		// URL de retour
		$redirect = urldecode(_request('redirect_del_auteur'));
		$delete = _request('supprimer_people');
		
		// supprimer le mot ?
		if ($delete
		AND $s = sql_select("*","spip_zones_auteurs","id_zone=".$id." AND id_auteur=".$delete)
		AND $t = sql_fetch($s)) {
			$zone_rub = sql_select("id_rubrique","spip_zones_rubriques","id_zone=".$id);
			$rub = sql_fetch($zone_rub);
			$liste_articles = sql_select("id_article","spip_articles","id_rubrique=".$rub['id_rubrique']);
			spip_log("rubrique = ".$rub['id_rubrique'], 'squeeze_zone');// On logue tout cela
			while($articles = sql_fetch($liste_articles)){
				$tous_auteurs = sql_select("id_auteur","spip_auteurs_articles","id_article=".$articles['id_article']);
				spip_log("article = ".$articles['id_article'], 'squeeze_zone');// On logue tout cela
				$nb_auteur = sql_count($tous_auteurs);
				while($auteur = sql_fetch($tous_auteurs)){
					spip_log("auteur = ".$auteur['id_auteur'], 'squeeze_zone');// On logue tout cela
					if (($nb_auteur < 2 ) && ($delete == $auteur['id_auteur'])){
						spip_log("Nombre d'auteurs : ".$nb_auteur." : id_auteur = ".$auteur['id_auteur'], 'squeeze_zone');// On logue tout cela
						$erreur = _T('vous_plus_auteur');
						$supprimer_auteur = 1;
						spip_log("erreur : il n'y a qu'un seul auteur pour l'article".$articles['id_article'], 'squeeze_zone');// On logue tout cela
					}
					else if($auteur['id_auteur'] == $delete){
						sql_delete("spip_auteurs_articles","id_article =".$articles['id_article']." AND id_auteur = ".$delete);
						$supprimer_auteur = 1;
						spip_log("suppression auteur (id_auteur = $delete) de l'article".$articles['id_article'], 'squeeze_zone');// On logue tout cela
					}
				}
			}
			if ($supprimer_auteur != 1){
				sql_delete("spip_zones_auteurs","id_auteur=".$delete." AND id_zone=".$id); // on efface l'auteur associé a la rubrique
				spip_log("suppression personne (id_auteur = $delete) de la zone $id", 'squeeze_zone');// On logue tout cela
			}
			else{
				$redirect = parametre_url($redirect, 'erreur', $erreur);	
			}
			$invalider = true;
		}
	if ($invalider) {
		include_spip('inc/invalideur');
		suivre_invalideur("0",true);
		spip_log('invalider', 'squeeze_zone');
	}
	redirige_par_entete(str_replace("&amp;","&",$redirect));
}
?>