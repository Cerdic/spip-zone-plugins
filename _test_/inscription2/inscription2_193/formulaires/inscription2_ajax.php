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
	} else {	
	    //si on est en mode création et que l'utilisateur a saisi ses valeurs on les prends en compte
	    foreach($champs as $clef =>$valeurs) {
            if (_request($valeurs)) {
                $champs[$valeurs] = _request($valeurs);
            }
	    }		
	}
	return $champs;
}

function formulaires_inscription2_ajax_verifier_dist($id_auteur = NULL){
    
	//charge la fonction de controle du login et mail
	//$test_inscription = charger_fonction('test_inscription');
	
	//initialise le tableau des erreurs
	$erreurs = array();
    //initilise le tableau de valeurs $champs => $valeur
    $valeurs = array();	
    
	//récupere la liste des champs possible
	$champs = inscription2_champs_formulaire();	

    //gere la correspondance champs -> _request(champs)
	foreach(inscription2_champs_formulaire() as $clef => $valeur) {
		$valeurs[$valeur] = _request($valeur);  
	}		
		
	//verifier les champs obligatoires
	foreach ($valeurs  as $champs => $valeur) {
		if ((lire_config('inscription2/'.$champs.'_obligatoire') == 'on') && empty($valeur)) {
			$erreurs[$champs] = _T('inscription2:champ_obligatoire');
		}
	}
	
	//messages d'erreur au cas par cas (PASSWORD)
	//vérification des champs
	//récupéré depuis le code de SPIP
	if(lire_config('inscription2/password') == 'on') {
		if($p = _request('password')) {
			if(strlen($p)){
				if (strlen($p) < 6) {
					$erreurs['password'] = _T('info_passe_trop_court');
					$erreurs['message_erreur'] .= _T('info_passe_trop_court');
				} elseif ($p != _request('password1')) {
					$erreurs['password'] = _T('info_passes_identiques');
					$erreurs['message_erreur'] .= _T('info_passes_identiques');
				}
			}else{
				if(!is_numeric($id_auteur)){
					// Si on est dans la modif d'id_auteur on garde l'ancien pass si rien n'est rentré
					// donc on accepte la valeur vide
					// dans le cas de la création d'un auteur ... le password sera nécessaire
					$erreurs['password'] = _T('inscription2:password_obligatoire');
				}
			}
		}
	}
	//messages d'erreur au cas par cas (CODE POSTAL)
    //liste des champs de type code postal
	$champs_code_postal = array('code_postal','code_postal_pro');
	
	// vérification des champs saisis
	foreach($champs_code_postal as $champs) {
	    if(lire_config('inscription2/'.$champs)== 'on') {
	        $erreur = inscription2_valide_cp($valeurs[$champs]);
	        if($erreur){
		        $erreurs[$champs] = $erreur;
	        }		
	    }
	}	

	//messages d'erreur au cas par cas (TELEPHONE)
	//liste des champs de type téléphone
	$champs_telephone = array('telephone','fax','mobile','telephone_pro','fax_pro','mobile_pro');
	
	// vérification des champs saisis
	foreach($champs_telephone as $champs) {
	    if(lire_config('inscription2/'.$champs)== 'on') {
	        $erreur = inscription2_valide_numero($valeurs[$champs]);
	        if($erreur){
		        $erreurs[$champs] .= $erreur;
	        }		
	    }
	}
	
	   
	//Offrir aux autres plugins de vérifier les données
	$erreurs_plugin = pipeline('i2_validation_formulaire',
		array(
			'args' => array(
			    'champs' => $valeurs
			),
		'data' => null
		)
	);
	
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
		spip_log("$erreurs","inscription2");
		$erreurs['message_erreur'] .= _T('inscription2:formulaire_remplir_obligatoires');
	}
	
    return $erreurs; // si c'est vide, traiter sera appele, sinon le formulaire sera resoumis
}

function formulaires_inscription2_ajax_traiter_dist($id_auteur = NULL){
	spip_log('traiter','inscription2');
	global $tables_principales;
	
	if((lire_config('inscription2/password') == 'on') && (strlen(_request('password')))){
		$mode = 'inscription_pass';
	}
	else{
		$mode = 'inscription';
	}
	
	/* Génerer la liste des champs à traiter
	* champ => valeur formulaire
	*/
	
	foreach(inscription2_champs_formulaire() as $clef => $valeur) {
		$valeurs[$valeur] = _request($valeur);
	}
	
	//Définir le login
	if(!$valeurs['nom']){
		if($valeurs['nom_famille']||$valeurs['prenom']){
			$valeurs['nom'] = $valeurs['prenom'].' '.$valeurs['nom_famille'];
		}
		else{
			$valeurs['nom'] = strtolower(translitteration(preg_replace('/@.*/', '', $valeurs['email'])));
		}
	}
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
	
	//Vérification du password
	if($mode == 'inscription_pass') {
		$new_pass = _request('password');
		if (strlen($new_pass)) {
			include_spip('inc/acces');
			$htpass = generer_htpass($new_pass);
			$alea_actuel = creer_uniqid();
			$alea_futur = creer_uniqid();
			$pass = md5($alea_actuel.$new_pass);
			$val['pass'] = $pass;
			$val['htpass'] = $htpass;
			$val['alea_actuel'] = $alea_actuel;
			$val['alea_futur'] = $alea_futur;
			$val['low_sec'] = '';
		}
		if(!is_numeric($id_auteur)){
			$val['statut'] = lire_config('inscription2/statut_nouveau');
		}
	}else{
		if(!is_numeric($id_auteur)){
			$val['statut'] = 'aconfirmer';
		}
	}
	
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
		// Si on utilise la date de creation de la fiche
		if(lire_config('inscription2/creation') == 'on'){
			$val['creation'] = date("Y-m-d H:i:s",time());
		}
		$val['id_auteur'] = $id_auteur;
		$id = sql_insertq(
			$table,
			$val
		);
	}
    
    if (!$new){
        $message = _T('inscription2:profil_modifie_ok');
    } else {
		$envoyer_inscription = charger_fonction('envoyer_inscription2','inc');
		$envoyer_inscription($id_auteur,$mode);
		$message = _T('inscription2:formulaire_inscription_ok');
    }
	
	$traiter_plugin = pipeline('i2_traiter_formulaire',
		array(
			'args' => array(
				'id_auteur' => $id_auteur,
				'champs' => $val
			),
		'data' => null
		)
	);
	
    return array('editable'=>false,'message' => $message);
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