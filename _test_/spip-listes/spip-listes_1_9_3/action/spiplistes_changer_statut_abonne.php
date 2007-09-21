<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_spiplistes_changer_statut_abonne_dist() {

	// les globales ne passent pas en action
	//global $connect_id_auteur;
	$connect_id_auteur = $GLOBALS['auteur_session']['id_auteur'];

	include_spip('inc/autoriser');
	include_spip(_DIR_PLUGIN_SPIPLISTES.'inc/spiplistes_api');

	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();
	$redirect = urldecode(_request('redirect'));

	$arg = explode('-',$arg);
	$id_auteur = intval($arg[0]);
	$action = $arg[1];

	if(($id_auteur > 0) && ($connect_id_auteur > 0)) {
		if ($action=='format') {
			//modification du format abonné ('html', 'texte' ou 'non')
			$statut = _request('statut');
			if(autoriser('statutabonement', 'auteur', $id_auteur)) {
				if(in_array($statut, explode(";", _SPIPLISTES_FORMATS_ALLOWED))) {
					if($res = spip_query("SELECT id_auteur FROM spip_auteurs_elargis WHERE id_auteur=$id_auteur LIMIT 1")) {
						if (spip_num_rows($res)) {
							spip_query("UPDATE spip_auteurs_elargis SET `spip_listes_format`='$statut' WHERE id_auteur=$id_auteur LIMIT 1");
						}
						else {
							spip_query("INSERT INTO spip_auteurs_elargis (id_auteur,`spip_listes_format`) VALUES ($id_auteur,'$statut')");
						}
						spiplistes_log("FORMAT ID_AUTEUR #$id_auteur changed to [$statut] by ID_AUTEUR #$connect_id_auteur");
					}	
				}
			}
		}
		if ($action=='listeabo') {
			//abonne un auteur, force en html si pas de format
			if ($id_auteur && ($id_liste = intval($arg[2])) 
				&& autoriser('abonnerauteur', 'liste', $id_liste, NULL, array('id_auteur'=>$id_auteur))
				) {
				$result=spip_query("DELETE FROM spip_auteurs_listes WHERE id_auteur=$id_auteur AND id_liste=$id_liste");
				$result=spip_query("INSERT INTO spip_auteurs_listes (id_auteur,id_liste) VALUES ($id_auteur,$id_liste)");
				//attribuer un format de reception si besoin (ancien auteur)
				$abo = spip_fetch_array(spip_query("SELECT `spip_listes_format` FROM `spip_auteurs_elargis` WHERE `id_auteur`='$id_auteur' LIMIT 1"));
				if(!$abo){
					// si auteur sans format, force en html
					$ok = spip_query("UPDATE `spip_auteurs_elargis` SET `spip_listes_format`='html' WHERE id_auteur="._q($id_auteur));
				}
			}
			spiplistes_log("SUBSCRIBE ID_AUTEUR #$id_auteur to ID_LISTE #$id_liste by ID_AUTEUR #$connect_id_auteur");	
		}
		if ($action=='listedesabo') {
			// désabonne un auteur
			if ($id_liste = intval($arg[2])) {
				if (autoriser('desabonnerauteur', 'liste', $id_liste, NULL, array('id_auteur'=>$id_auteur))) {
					if(spip_query("DELETE FROM spip_auteurs_listes WHERE id_auteur=$id_auteur AND id_liste=$id_liste")) {
						spiplistes_log("UNSUBSCRIBE ID_AUTEUR #$id_auteur from ID_LISTE #$id_liste by ID_AUTEUR #$connect_id_auteur");	
					}
				}
			}
		}
	}
	if ($redirect){
		redirige_par_entete(str_replace("&amp;","&",$redirect)."#abo$id_auteur");
	}
} // action_spiplistes_changer_statut_abonne_dist()

?>