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

//charger cfg
include_spip('cfg_options');   

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
				$erreurs[$champs] = $erreur;
			}
			else if((preg_match('/^telephone/', $champs))||(preg_match('/^fax/', $champs))||(preg_match('/^mobile/', $champs))){
				$numero = _request($champs);
				$erreur = inscription2_valide_numero($numero);
				$erreurs[$champs] = $erreur;
			}
			
			//pipeline pour la verifications des donnees de plugins tiers
			else {
				$erreurs[$champs] = pipeline('i2_validation_formulaire',
					array(
						'args' => array(
							'champs' => $champs,
							'valeur' => _request($champs)
						),
					'data' => null
					)
				);
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
	
	//message d'erreur generalise
	if (count($erreurs)) {
		$erreurs['message_erreur'] .= _T('inscription2:formulaire_remplir_obligatoires');
	}

    return $erreurs; // si c'est vide, traiter sera appele, sinon le formulaire sera resoumis
}

function formulaires_inscription2_ajax_traiter_dist($id_auteur = NULL){

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
	$id_elargi = sql_getfetsel('id','spip_auteurs_elargis','id_auteur='.$id_auteur);
	
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
    return $message;
}

/*
 *  ! brief Determine les champs de formulaire à traiter
 */
function inscription2_champs_formulaire() {

	//charge les valeurs de chaque champs proposés dans le formulaire   
	foreach (lire_config('inscription2/') as $clef => $valeur) {
		/* Il faut retrouver les noms des champ, 
		* par défaut inscription2 propose pour chaque champ le cas champ_obligatoire
		*  On retrouve donc les chaines de type champ_obligatoire
		*  Ensuite on verifie que le champ est proposé dans le formulaire
		*  Remplissage de $valeurs[]
		*/
		//decoupe la clef sous le forme $resultat[0] = $resultat[1] ."_obligatoire"
		//?: permet de rechercher la chaine sans etre retournée dans les résultats
		preg_match('/^(.*)(?:_obligatoire)/i', $clef, $resultat);
	
		if ((!empty($resultat[1])) && (lire_config('inscription2/'.$resultat[1]) == 'on')) {
			$valeurs[] = $resultat[1];
			$valeur = _request($resultat[1]);
			$valeurs[$resultat[1]] = $valeur;
		}
	}
	return $valeurs;
}
/*
// http://doc.spip.org/@test_inscription_dist
function test_inscription_dist($mode, $mail, $nom, $id=0) {

    include_spip('inc/filtres');
    $nom = trim(corriger_caracteres($nom));
    if (!$nom || strlen($nom) > 64)
        return _T('ecrire:info_login_trop_court');
    if (!$r = email_valide($mail)) return _T('info_email_invalide');
    return array('email' => $r, 'nom' => $nom, 'bio' => $mode);
}
*/

function inscription2_valide_cp($cp){
	if(!$cp){
		return;
	}
	else{
		if(preg_match('/^[A-Z]{1,2}[-|\s][0-9]{3,6}$|^[0-9]{3,6}$|^[0-9|A-Z]{2,5}[-|\s][0-9|A-Z]{2,4}$|^[A-Z]{1,2} [0-9|A-Z]{2,5}[-|\s][0-9|A-Z]{2,4}/i',$cp)){
			return;
		}
		else{
			return _T('inscription2:cp_valide');
		}
	}
}

function inscription2_valide_numero($numero){
	if(!$numero){
		return;
	}
	else{
		if(preg_match('/^[0-9\+\. \-]+$/',$numero)){
			return;
		}
		else{
			return _T('inscription2:numero_tva_valide');
		}
	}
}
?>