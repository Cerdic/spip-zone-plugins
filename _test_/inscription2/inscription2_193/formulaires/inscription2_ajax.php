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

// charger cfg
include_spip('cfg_options');
// charger les fonctions de formilaires
include_spip('inc/inscription2_form_fonctions');

// chargement des valeurs par defaut des champs du formulaire
function formulaires_inscription2_ajax_charger_dist($id_auteur = NULL){

	global $tables_principales;
   
	//initialise les variables d'environnement pas défaut
	$valeurs = array();

	//récupere la liste des champs possible
	$champs = inscription2_champs_formulaire();

	//si on a bien un auteur alors on préremplit le formulaire avec ses informations
	//les nom des champs sont les memes que ceux de la base de données
	if (is_numeric($id_auteur)) {
		$auteur = sql_fetsel(
			$champs,
			'spip_auteurs LEFT JOIN spip_auteurs_elargis USING(id_auteur)',
			'id_auteur ='.$id_auteur            
		);

		$champs = $auteur;
	}
	return $champs;
}

function formulaires_inscription2_ajax_verifier_dist($id_auteur = NULL){
    
	//charge la fonction de controle du login et mail
	//$test_inscription = charger_fonction('test_inscription');
	
	//initialise le tableau des erreurs
	$erreurs = array();
				
	//messages d'erreur au cas par cas
	//vérifier les champs obligatoire
	foreach (lire_config('inscription2/') as $clef => $valeur) {
		$champs = ereg_replace("_(obligatoire|fiche|table|mod)", "", $clef);
		
		if ($champs && $valeur == 'on'){
			if(preg_match('/^code_postal/', $champs)){
				$cp = _request($champs);
				$erreur = inscription2_valide_cp($cp);
				if($erreur){
					$erreurs[$champs] = $erreur;
				}
			}
			else if((preg_match('/^telephone/', $champs))||(preg_match('/^fax/', $champs))||(preg_match('/^mobile/', $champs))){
				$numero = _request($champs);
				$erreur = inscription2_valide_numero($numero);
				if($erreur){
					$erreurs[$champs] = $erreur;
				}
			}
			
			//pipeline pour la verifications des donnees de plugins tiers
			else {
				$erreur = pipeline('i2_validation_formulaire',
					array(
						'args' => array(
							'champs' => $champs,
							'valeur' => _request($champs)
						),
					'data' => null
					)
				);
				if($erreur){
					$erreurs[$champs] = $erreur;
				}
				
			}
			
			//si clef obligatoire, obligatoire activé et _request() vide alors erreur
			if (!$erreurs[$champs] && (lire_config('inscription2/'.$champs.'_obligatoire') == 'on') && !_request($champs)) {
				$erreurs[$champs] = _T('inscription2:champ_obligatoire');
			}
		}
	}

	//verifier que l'auteur a bien des droits d'edition
	if (is_numeric($id_auteur)) {
		include_spip('inc/autoriser');
		if (!autoriser('modifier','auteur',$id_auteur)) {
			$erreurs['message_erreur'] .= _T('inscription2:profil_droits_insuffisants');
		}
	}
    
	//Verifier certains champs specifiquement
	
	//Verifier le login
	// c'est a dire regarder dans la base si un autre utilisateur que celui en cours possede le login saisi
	if (_request('login')) {
		if (sql_getfetsel('id_auteur','spip_auteurs','id_auteur !='.intval($id_auteur).' AND login LIKE \''._request('login').'\'')) {
			$erreurs['login'] = _T('inscription2:formulaire_login_deja_utilise');
		}
	}
	spip_log($erreurs,'inscription2');
	//message d'erreur generalise
	if (count($erreurs)) {
		$erreurs['message_erreur'] .= _T('inscription2:formulaire_remplir_obligatoires');
	}

    return $erreurs; // si c'est vide, traiter sera appele, sinon le formulaire sera resoumis
}

function formulaires_inscription2_ajax_traiter_dist($id_auteur = NULL){
	spip_log('traiter','inscription2');
	global $tables_principales;
	
	/* Génerer la liste des champs à traiter
	* champ => valeur formulaire
	*/
	
	foreach(inscription2_champs_formulaire() as $clef => $valeur) {
		$valeurs[$valeur] = _request($valeur);  
	}
	
	//Définir le login
	include_spip('balise/formulaire_inscription');
	if (!_request('login')) {
		$valeurs['login'] = test_login($valeurs['nom'], $valeurs['email']);
	}
    
	//$valeurs contient donc tous les champs remplit ou non 
	
	//definir les champs pour spip_auteurs
	$table = "spip_auteurs";
    
	//genere le tableau des valeurs à mettre à jour pour spip_auteurs
	//toutes les clefs qu'inscription2 peut mettre à jour
	$clefs = array_fill_keys(array('login','nom','email','bio'),'');
	//extrait uniquement les données qui ont été proposées à la modification
	$val = array_intersect_key($valeurs,$clefs);
	
	//inserer les données dans spip_auteurs -- si $id_auteur mise à jour autrement nouvelle entrée
	if (is_numeric($id_auteur)) {
		$where = 'id_auteur = '.$id_auteur;
		sql_updateq(
			$table,
			$val,
			$where
		);
		$new = false;
	} else {
		$val['statut'] = 'aconfirmer';
		$id_auteur = sql_insertq(
			$table,
			$val
		);
		$new = true;
	}
	$table = 'spip_auteurs_elargis';

	//extrait les valeurs propres à spip_auteurs_elargis
	
	//genere le tableau des valeurs à mettre à jour pour spip_auteurs
	//toutes les clefs qu'inscription2 peut mettre à jour
	//s'appuie sur les tables definies par le plugin
	$clefs = $tables_principales['spip_auteurs_elargis']['field'];
	
	//extrait uniquement les données qui ont été proposées à la modification
	$val = array_intersect_key($valeurs,$clefs);
	
	//recherche la presence d'un complément sur l'auteur
	$id_elargi = sql_getfetsel('id_auteur','spip_auteurs_elargis','id_auteur='.$id_auteur);
	
	if ($id_elargi) {
		$where = 'id_auteur = '.$id_auteur;
		sql_updateq(
			$table,
			$val,
			$where      
		);
	} else {
		$val['id_auteur'] = $id_auteur;
		$id = sql_insertq(
			$table,
			$val
		);
	}
    
    if (!$new) {    
        $message = _T('inscription2:profile_modifie_ok');
    } else {
		$envoyer_inscription = charger_fonction('envoyer_inscription2','inc');
		$envoyer_inscription($id_auteur);
		$message = _T('inscription2:formulaire_inscription_ok');
    }
    return array('editable'=>"false",'message' => $message);
}

// http://doc.spip.org/@test_login
function test_login($nom, $mail) {
	include_spip('inc/charsets');
	$nom = strtolower(translitteration($nom));
	$login_base = preg_replace("/[^\w\d_]/", "_", $nom);

	// il faut eviter que le login soit vraiment trop court
	if (strlen($login_base) < 3) {
		$mail = strtolower(translitteration(preg_replace('/@.*/', '', $mail)));
		$login_base = preg_replace("/[^\w\d]/", "_", $nom);
	}
	if (strlen($login_base) < 3)
		$login_base = 'user';

	// eviter aussi qu'il soit trop long (essayer d'attraper le prenom)
	if (strlen($login_base) > 10) {
		$login_base = preg_replace("/^(.{4,}(_.{1,7})?)_.*/",
			'\1', $login_base);
		$login_base = substr($login_base, 0,13);
	}

	$login = $login_base;

	for ($i = 1; ; $i++) {
		if (!sql_countsel('spip_auteurs', "login='$login'"))
			return $login;
		$login = $login_base.$i;
	}
}
?>