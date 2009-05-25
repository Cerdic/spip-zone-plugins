<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

// charger cfg
include_spip('cfg_options');

/**
 * 
 * Chargement des valeurs par defaut des champs du formulaire
 * 
 * @return array L'ensemble des champs et de leur valeurs
 * @param int $id_auteur[optional] Si cette valeur est utilisée, on entre dans le cadre de
 * la modification d'un auteur, et plus dans la création
 */
function formulaires_inscription2_charger_dist($id_auteur = NULL,$redirect = null){
   
	//initialise les variables d'environnement pas défaut
	$valeurs = array();

	//recupere la liste des champs possible
	$chercher_champs = charger_fonction('inscription2_champs_formulaire','inc');
	$champs = $chercher_champs($id_auteur);

	//si on a bien un auteur alors on preremplit le formulaire avec ses informations
	//les nom des champs sont les memes que ceux de la base de données
	if (is_numeric($id_auteur)) {
		
		$auteur = sql_fetsel(
			$champs,
			'spip_auteurs LEFT JOIN spip_auteurs_elargis USING(id_auteur)',
			'spip_auteurs_elargis.id_auteur ='.$id_auteur
		);
		$auteur['id_auteur'] = $id_auteur;
		if(in_array('naissance',$champs)){
			if(preg_match("/([0-9]{4})-([0-9]{2})-([0-9]{2})/",$auteur['naissance'],$date_naissance)){
				include_spip('inc/date');
				$auteur['annee'] = $date_naissance[1];
				$auteur['mois'] = $date_naissance[2];
				$auteur['jour'] = $date_naissance[3];
			}
		}
		$champs = $auteur;
	} else {
	    //si on est en mode creation et que l'utilisateur a saisi ses valeurs on les prends en compte
	    foreach($champs as $clef =>$valeurs) {
            if (_request($valeurs)) {
                $champs[$valeurs] = _request($valeurs);
            }
            if($valeurs == 'naissance'){
	            if(preg_match("/([0-9]{4})-([0-9]{2})-([0-9]{2})/",_request($valeurs),$date_naissance)){
					include_spip('inc/date');
					$champs['annee'] = $date_naissance[1];
					$champs['mois'] = $date_naissance[2];
					$champs['jour'] = $date_naissance[3];
				}
            }
	    }		
	}
	
	//Offrir aux autres plugins la possibilite de charger les donnees
	$champs = pipeline('i2_charger_formulaire',
		array(
			'args' => '',
			'data' => $champs
		)
	);
	
	return $champs;
}

function formulaires_inscription2_verifier_dist($id_auteur = NULL,$redirect = null){
    
	//charge la fonction de controle du login et mail
	//$test_inscription = charger_fonction('test_inscription');
	
	//initialise le tableau des erreurs
	$erreurs = array();
	
    //initilise le tableau de valeurs $champs => $valeur
    $valeurs = array();	
    
	//recupere la liste des champs possible
	$chercher_champs = charger_fonction('inscription2_champs_formulaire','inc');
	$champs = $chercher_champs($id_auteur);	

	$champs_a_verifier = pipeline('i2_verifications_specifiques',array());

	//gere la correspondance champs -> _request(champs)
	foreach($champs as $clef => $valeur) {
		
		// On récupère sa valeur 
		$valeurs[$valeur] = _request($valeur);
		
		// On vérifie s'il est obligatoire et s'il est bien rempli
		if ((lire_config('inscription2/'.$valeur.'_obligatoire') == 'on' ) && (empty($valeurs[$valeur]) OR (strlen(_request($valeur)) == 0))) {
			$erreurs[$valeur] = _T('inscription2:champ_obligatoire');
			$erreurs_obligatoires = true;
			if(is_numeric($id_auteur) && (lire_config('inscription2/pass_fiche_mod') == 'on') && (strlen(_request('pass')) == 0)){
				// Si le password est vide et que l'on est dans le cas de la modification d'un auteur
				// On garde le pass original
				unset($erreurs['pass']);
				$pass == 'ok';
			}
		}
		
		// Sinon on la vérifie une seconde fois si nécessaire avec les fonctions spécifiques de validations
		if(!$erreurs[$valeur]){
			if(array_key_exists($valeur,$champs_a_verifier)){
				$fonction_verif_{$valeur} = charger_fonction('inscription2_'.$champs_a_verifier[$valeur],'inc');
				if($val = $fonction_verif_{$valeur}($valeurs[$valeur],$id_auteur)){
					$erreurs[$valeur] = $val;
				}
			}
		}
	}

	//Verifier certains champs specifiquement

	//messages d'erreur au cas par cas (PASSWORD)
	//verification des champs
	// Sinon on le verifie
	if(($pass != 'ok') && (lire_config('inscription2/pass') == 'on')) {
		if (strlen(_request('password')) != 0){$p = _request('password');}else{$p = _request('pass');}
		if($p) {
			if(strlen($p)){
				if (strlen($p) < 6) {
					$erreurs['pass'] = _T('info_passe_trop_court');
					$erreurs['message_erreur'] .= _T('info_passe_trop_court')."<br />";
				} elseif ($p != _request('password1')) {
					$erreurs['pass'] = _T('info_passes_identiques');
					$erreurs['message_erreur'] .= _T('info_passes_identiques')."<br />";
				}
			}else{
				if(!is_numeric($id_auteur)){
					// (1) Si on est dans la modif d'id_auteur on garde l'ancien pass si rien n'est rentre
					// donc on accepte la valeur vide
					// dans le cas de la création d'un auteur ... le password sera necessaire
					$erreurs['pass'] = _T('inscription2:password_obligatoire');
				}
			}
		}
	}

	//Offrir aux autres plugins la possibilite de verifier les donnees
	$erreurs = pipeline('i2_verifier_formulaire',
		array(
			'args' => array(
			    'champs' => $valeurs
			),
		'data' => $erreurs
		)
	);

	//verifier que l'auteur a bien des droits d'edition
	if (is_numeric($id_auteur)) {
		include_spip('inc/autoriser');
		if (!autoriser('modifier','auteur',$id_auteur)) {
			$erreurs['message_erreur'] .= _T('inscription2:profil_droits_insuffisants');
		}
	}
	
	//message d'erreur generalise
	if (count($erreurs)) {
		if(isset($erreurs_obligatoires)){
			$erreurs['message_erreur'] .= _T('inscription2:formulaire_remplir_obligatoires');	
		}else{
			$erreurs['message_erreur'] .= _T('inscription2:formulaire_remplir_validation');
		}
		
	}
	
    return $erreurs; // si c'est vide, traiter sera appele, sinon le formulaire sera resoumis
}

function formulaires_inscription2_traiter_dist($id_auteur = NULL,$redirect = null){
	global $tables_principales;
	
	$retour = array();
	
	if((is_numeric($id_auteur) && (lire_config('inscription2/pass_fiche_mod') != 'on'))
		OR (is_numeric($id_auteur) && (lire_config('inscription2/pass_fiche_mod') == 'on')) && (strlen(_request('password')) == 0)){
		$mode = 'modification_auteur_simple';
	}
	else if((is_numeric($id_auteur) && (lire_config('inscription2/pass_fiche_mod') == 'on')) and (strlen(_request('password')) != 0)){
		$mode = 'modification_auteur_pass';
	}
	else if((lire_config('inscription2/pass') == 'on') && (strlen(_request('pass')))){
		$mode = 'inscription_pass';
	}
	else{
		$mode = 'inscription';
	}
	
	/* Generer la liste des champs a traiter
	* champ => valeur formulaire
	*/
	if(!is_numeric($id_auteur)){
		$new = true;
	}
	
	$chercher_champs = charger_fonction('inscription2_champs_formulaire','inc');
	$champs = $chercher_champs($id_auteur);
	
	foreach($champs as $clef => $valeur) {
		$valeurs[$valeur] = _request($valeur);
		if($valeur == 'naissance'){
			include_spip('inc/date');
			$annee = _request('annee');
			$mois = _request('mois');
			$jour = _request('jour');
			$valeurs[$valeur] = format_mysql_date($annee,$mois,$jour);
			spip_log("on récupère la valeur du champs naissance : ".$valeurs[$valeur]);
		}
	}
	// Definir le login s'il a besoin de l'etre
	// NOM et LOGIN sont des champs obligatoires donc a la creation il ne doivent pas etre vide
	// Apres on s'en fiche s'il n'est pas dans le formulaire
	if($new){
		if(!$valeurs['nom']){
			if($valeurs['nom_famille']||$valeurs['prenom']){
				$valeurs['nom'] = $valeurs['prenom'].' '.$valeurs['nom_famille'];
			}
			else{
				$valeurs['nom'] = strtolower(translitteration(preg_replace('/@.*/', '', $valeurs['email'])));
			}
		}
		if(!$valeurs['login']){
			$definir_login = charger_fonction('inscription2_definir_login','inc');
			$valeurs['login'] = $definir_login($valeurs['nom'], $valeurs['email']);
		}
	}
	
	//$valeurs contient donc tous les champs remplit ou non 
	include_spip('inc/inscription2_compat_php4');
	//definir les champs pour spip_auteurs
	$table = "spip_auteurs";
    
	//genere le tableau des valeurs a mettre a jour pour spip_auteurs
	//toutes les clefs qu'inscription2 peut mettre a jour

	$clefs = array_fill_keys(array('login','nom','email','bio','nom_site','url_site','pgp'),'');
	
	//extrait uniquement les donnees qui ont ete proposees a la modification
	$val = array_intersect_key($valeurs,$clefs);
	
	//Verification du password
	if(($mode == 'inscription_pass') || ($mode == 'modification_auteur_pass')){
		if (strlen(_request('password')) != 0)
			$new_pass = _request('password');
		else
			$new_pass = _request('pass');
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
	
	//inserer les donnees dans spip_auteurs -- si $id_auteur : mise a jour - autrement : nouvelle entree
	if (!$new) {
		$where = 'id_auteur = '.$id_auteur;
		sql_updateq(
			$table,
			$val,
			$where
		);
	} else {
		$id_auteur = sql_insertq(
			$table,
			$val
		);
	}
	
	$table = 'spip_auteurs_elargis';
	//extrait les valeurs propres a spip_auteurs_elargis
	
	//genere le tableau des valeurs a mettre a jour pour spip_auteurs
	//toutes les clefs qu'inscription2 peut mettre a jour
	//s'appuie sur les tables definies par le plugin
	
	$clefs = $tables_principales[$table]['field'];
	if(is_array($clefs)){
		//extrait uniquement les donnees qui ont ete proposees a la modification
		$val = array_intersect_key($valeurs,$clefs);
	}else{
		$where = 'id_auteur='.sql_quote($id_auteur);	
		$res = sql_select('*',$table,$where);
		$clefs = sql_fetch($res);
		$val = array_intersect_key($valeurs,$clefs);	
	}
	
	unset($val['login']);
	
	//recherche la presence d'un complement sur l'auteur
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
    
	$traiter_plugin = pipeline('i2_traiter_formulaire',
		array(
			'args' => array(
				'id_auteur' => $id_auteur,
				'champs' => $val
			),
		'data' => null
		)
	);
	
    if (!$new){
        $message = _T('inscription2:profil_modifie_ok');
        if($mode == 'modification_auteur_simple'){
        	$message .= '<br />'._T('inscription2:mot_passe_reste_identique');
        }
        $editable = true;
    } else {
		if(!$traiter_plugin['ne_pas_confirmer_par_mail']){
			$envoyer_inscription = charger_fonction('envoyer_inscription2','inc');
			$envoyer_inscription($id_auteur,$mode);
			$message = _T('inscription2:formulaire_inscription_ok');
		}
		if($traiter_plugin['message_ok'])
			$message = $traiter_plugin['message_ok'] ;
		$editable = false;
    }
	
	$retour['editable'] = $editable;
	$retour['message_ok'] = $message;
	
	if($redirect){
		$retour['redirect'] = $redirect;
	}
	
    return $retour;
}
?>