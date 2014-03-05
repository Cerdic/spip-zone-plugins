<?php
/*
 * Plugin Alertes
 * Distribué sous licence GPL
 *
 * Formulaire d'enregistrement des abonnements.
 */
if (!defined("_ECRIRE_INC_VERSION")) return;

function formulaires_alertes_email_charger_dist($id_auteur){
	$valeurs = array();
	$id_auteur = intval($id_auteur);
	//Récuperation de la configuration de base
	$a = @unserialize($GLOBALS['meta']['config_alertes']);
	if(is_array($a)){
		//Récuperation des abonnements au mots
		if($a['groupes']){
			$mots = array();
			$sql_mots = sql_select("id_objet", "spip_alertes", "objet = 'mot' AND id_auteur = ".$id_auteur);
			while ($mot = sql_fetch($sql_mots)) {
				$mots[] = $mot['id_objet'];
			}
			$valeurs['mots'] = $mots ? $mots : '';
		}
		//Récuperation des abonnements aux rubriques
		if($a['rubriques']){
			$rubriques = array();
			$sql_rubriques = sql_select("id_objet", "spip_alertes", "objet = 'rubrique' AND id_auteur = ".$id_auteur);
			while ($rub = sql_fetch($sql_rubriques)) {
				$rubriques[] = $rub['id_objet'];
			}
			$valeurs['rubriques'] = $rubriques ? $rubriques : '';
		}	
		//Récuperation des abonnements aux secteurs
		if($a['secteurs']){
			$secteurs = array();
			$sql_secteurs= sql_select("id_objet", "spip_alertes", "objet = 'secteur' AND id_auteur = ".$id_auteur);
			while ($sec = sql_fetch($sql_secteurs)) {
				$secteurs[] = $sec['id_objet'];
			}
			$valeurs['secteurs'] = $secteurs ? $secteurs : '';
		}	
		//Récuperation des abonnements aux auteurs
		if($a['auteurs']){
			$auteurs = array();
			$sql_auteurs= sql_select("id_objet", "spip_alertes", "objet = 'auteur' AND id_auteur = ".$id_auteur);
			while ($aut = sql_fetch($sql_auteurs)) {
				$auteurs[] = $aut['id_objet'];
			}
			$valeurs['auteurs'] = $auteurs ? $auteurs : '';
		}			
	}

	return $valeurs;
}

function formulaires_alertes_email_traiter_dist($id_auteur){
	$a = @unserialize($GLOBALS['meta']['config_alertes']);
	$now = date('Y-m-d h:i:s');
	if(is_array($a)){
			//Supprimer les anciennes configuration d'abonnements. Tous d'un coup, brutal mais efficace et comme ça on n'a que des insert à faire.
			sql_delete('spip_alertes', 'id_auteur = ' . intval($id_auteur));
			//Mots
			$mots = _request('mots');
			foreach($mots as $mot){
				//Insertion des nouveaux mots abonnés
				$ins_mot = sql_insertq('spip_alertes', array('id_objet'=> intval($mot), 'objet' => 'mot', 'id_auteur' => $id_auteur, 'maj' => $now ));
			}
			//Rubriques
			$rubriques = _request('rubriques');
			foreach($rubriques as $rubrique){
				//Insertion des nouvelles rubriques abonnés
					$ins_rub = sql_insertq('spip_alertes', array('id_objet'=> intval($rubrique), 'objet' => 'rubrique', 'id_auteur' => $id_auteur, 'maj' => $now ));
			}
			//Secteurs
			$secteurs = _request('secteurs');
			foreach($secteurs as $secteur){
				//Insertion des nouveaux secteurs abonnés
					$ins_sec = sql_insertq('spip_alertes', array('id_objet'=> intval($secteur), 'objet' => 'secteur', 'id_auteur' => $id_auteur, 'maj' => $now ));
			}
			//Auteurs
			$auteurs = _request('auteurs');
			foreach($auteurs as $auteur){
				//Insertion des nouveaux auteurs abonnés
					$ins_aut = sql_insertq('spip_alertes', array('id_objet'=> intval($auteur), 'objet' => 'auteur', 'id_auteur' => $id_auteur, 'maj' => $now ));
			}			
		}
	$message = _T('alertes:your_alertes_message_ok');
	$res = array('message_ok' => $message);

	return $res;
}
?>