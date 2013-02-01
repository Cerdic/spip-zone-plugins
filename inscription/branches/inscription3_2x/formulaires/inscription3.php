<?php
/**
 * Formulaire d'inscription et de modification de profil amélioré au site
 *
 * Pour l'utiliser en tant que formulaire d'inscription :
 * - #FORMULAIRE_INSCRIPTION2
 *
 * Pour l'utiliser afin de modifier le profil d'un utilisateur :
 * - #FORMULAIRE_INSCRIPTION2{#ID_AUTEUR} dans une boucle auteur
 * - #FORMULAIRE_INSCRIPTION2{#ENV{id_auteur}} si l'id_auteur est dans
 * l'environnement (modèle / page spécifique)
 * - #FORMULAIRE_INSCRIPTION2{2} pour éditer le profil d'un utilisateur en
 * particulier (ici l'id_auteur numéro 2)
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/editer');

/**
 *
 * Chargement des valeurs par defaut des champs du formulaire
 *
 * @return array L'ensemble des champs et de leur valeurs
 * @param int $id_auteur[optional] Si cette valeur est utilisée, on entre dans le cadre de
 * la modification d'un auteur, et plus dans la création
 */
function formulaires_inscription3_charger_dist($id_auteur='new',$redirect = null){
	$valeurs = formulaires_editer_objet_charger('auteur',$id_auteur,0,0,$retour,$config_fonc,$row,$hidden);

	/*
	 * Recupere la liste des champs possible
	 * Cette liste peut être différente si on est dans une inscription
	 * ou un profil utilisateur
	 */
	$chercher_champs = charger_fonction('inscription3_champs_formulaire','inc');
	$champs = $chercher_champs($id_auteur);

	/*
	 * Si on a un auteur (profil)
	 * - On vérifie qu'il a le droit de modifier ce profil
	 * - On traite le champs naissance pour qu'il s'affiche correctement
	 */
	if (is_numeric($id_auteur)) {
		include_spip('inc/autoriser');
		if (!autoriser('modifier','auteur',$id_auteur)) {
			$valeurs['editable'] = false;
			$valeurs['message_erreur'] = _T('inscription3:profil_droits_insuffisants');
			return $valeurs;
		}
		/**
		 * On sélectionne tout pour éviter les champs qui ne sont pas dans la
		 * base de donnée.
		 * par exemple : logo_auteur et reglement
		 */
		if(in_array('naissance',$champs)){
			if(preg_match("/([0-9]{4})-([0-9]{2})-([0-9]{2})/",$auteur['naissance'],$date_naissance)){
				include_spip('inc/date');
				$valeurs['annee'] = $date_naissance[1];
				$valeurs['mois'] = $date_naissance[2];
				$valeurs['jour'] = $date_naissance[3];
			}
		}
	}
	/*
	 *
	 */
	else {
	    //si on est en mode creation et que l'utilisateur a saisi ses valeurs on les prends en compte
	    foreach($champs as $clef =>$valeur) {
            if (_request($valeur)) {
                $valeurs[$valeur] = trim(_request($valeur));
            }
            if($valeur == 'naissance'){
				include_spip('inc/date');
				$valeurs['annee'] = _request('annee');
				$valeurs['mois'] = _request('mois');
				$valeurs['jour'] = _request('jour');
            }
	    }
	}

	/*
	 * Offrir aux autres plugins la possibilite de charger les donnees
	 */
	$valeurs = pipeline('i3_charger_formulaire',
		array(
			'args' => '',
			'data' => $valeurs
		)
	);

	return $valeurs;
}

function formulaires_inscription3_verifier_dist($id_auteur='new',$redirect = null){
	$config_i2 = lire_config('inscription3');

	/*
	 * Vérification des champs obligatoires
	 * En fonction de ceux présents dans le formulaire
	 */
	$champs_obligatoires = charger_fonction('inscription3_champs_obligatoires','inc');
	$obligatoires = $champs_obligatoires($id_auteur);
	$erreurs = formulaires_editer_objet_verifier('auteur',$id_auteur,$obligatoires);

	/**
	 * Naisance est un champs spécifique coupé en trois on le vérifie séparément
	 * s'il est obligatoire
	 */
	if($erreurs['naissance']){
		$annee = trim(_request('annee'));
		$mois = trim(_request('mois'));
		$jour = trim(_request('jour'));
		if($annee && $mois && $jour){
			unset($erreurs['naissance']);
		}
	}

	/**
	 * Si le password est vide et que l'on est dans le cas de la modification d'un auteur
	 * On garde le pass original
	 */
	if(is_numeric($id_auteur) && ($config_i2['pass_fiche_mod'] == 'on') && (strlen(_request('pass')) == 0)){
		unset($erreurs['pass']);
		$pass == 'ok';
	}
	if(count($erreurs)){
		$erreurs_obligatoires = true;
	}

    $valeurs = array();

    $verifier = charger_fonction('verifier','inc',true);

    if($verifier){
		$champs_a_verifier = pipeline('i3_verifications_specifiques',array());

		//gere la correspondance champs -> _request(champs)
		foreach($champs_a_verifier as $clef => $type) {
			/*
			 * Si le champs n'est pas déjà en erreur suite aux champs obligatoires
			 * On s'assure qu'il est bien présent dans le formulaire également
			 */
			if(!isset($erreurs[$clef]) && _request($clef)){
				$valeurs[$clef] = trim(_request($clef));
				$type['options']['id_auteur'] = $id_auteur;
				$type['options'] = array_merge($type['options'],$_GET);
				$erreurs[$clef] = $verifier($valeurs[$clef],$type['type'],$type['options']);
			}
		}
    }else{
    	spip_log('INSCRIPTION3 : vous devriez installer le plugin verifier peut être?');
    }

	/**
	 * messages d'erreur au cas par cas (PASSWORD)
	 *
	 * Il se peut que l'on active pas le password à l'inscription
	 * mais uniquement à la modification ...
	 * On le test ici en créant la variable $pass_actif
	 * pass et password1 sont les variables a la creation du compte
	 * password et password1 sont les variables a la modification du compte
	 */
	$pass_actif = false;
	if(is_numeric($id_auteur) && ($config_i2['pass_fiche_mod'] == 'on')){
		$pass_actif = true;
	}else if(!$id_auteur && ($config_i2['inscription3/pass'] == 'on')){
		$pass_actif = true;
	}
	if(($pass != 'ok') && $pass_actif) {
		if (strlen(_request('password')) != 0){
			$p = _request('password');
		}
		else{
			$p = _request('pass');
		}
		if($p){
			if(strlen($p)){
				$pass_min = !defined('_PASS_MIN') ? 6 : _PASS_MIN;
				if (strlen($p) < $pass_min) {
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
					$erreurs['pass'] = _T('inscription3:password_obligatoire');
				}
			}
		}
	}

	/*
	 * Offrir aux autres plugins la possibilite de verifier les donnees
	 */
	$erreurs = pipeline('i3_verifier_formulaire',
		array(
			'args' => array(
			    'champs' => $valeurs
			),
		'data' => $erreurs
		)
	);

	/*
	 * Message d'erreur generalise
	 */
	if (count($erreurs)) {
		if(isset($erreurs_obligatoires)){
			$erreurs['message_erreur'] .= _T('inscription3:formulaire_remplir_obligatoires');
		}else{
			$erreurs['message_erreur'] .= _T('inscription3:formulaire_remplir_validation');
		}
	}
    return $erreurs;
}

function formulaires_inscription3_traiter_dist($id_auteur = 'new',$redirect = null){
	$config_i2 = lire_config('inscription3');
	$retour = array();
	$data = array();

	if((is_numeric($id_auteur) && ($config_i2['pass_fiche_mod'] != 'on'))
		OR (is_numeric($id_auteur) && ($config_i2['pass_fiche_mod'] == 'on')) && (strlen(_request('password')) == 0)){
		$mode = 'modification_auteur_simple';
	}
	else if((is_numeric($id_auteur) && ($config_i2['pass_fiche_mod'] == 'on')) and (strlen(_request('password')) != 0)){
		$mode = 'modification_auteur_pass';
	}
	else if(($config_i2['pass'] == 'on') && (strlen(_request('pass')))){
		$mode = 'inscription_pass';
	}
	else{
		$mode = 'inscription';
	}

	/*
	 * Generer la liste des champs a traiter
	 * champ => valeur formulaire
	 */
	if(!is_numeric($id_auteur)){
		$new = true;
	}

	$chercher_champs = charger_fonction('inscription3_champs_formulaire','inc');
	$champs = $chercher_champs($id_auteur);

	foreach($champs as $clef => $valeur) {
		$valeurs[$valeur] = trim(_request($valeur));
		if($valeur == 'naissance'){
			include_spip('inc/date');
			$annee = trim(_request('annee'));
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
			$definir_login = charger_fonction('inscription3_definir_login','inc');
			$valeurs['login'] = $definir_login($valeurs['nom'], $valeurs['email']);
		}
	}

	$trouver_table = charger_fonction('trouver_table','base');

	//$valeurs contient donc tous les champs remplit ou non
	//definir les champs pour spip_auteurs
	$table = "spip_auteurs";

	//genere le tableau des valeurs a mettre a jour pour spip_auteurs
	//toutes les clefs qu'inscription3 peut mettre a jour

	//$clefs = array_fill_keys(array('login','nom','email','bio','nom_site','url_site','pgp'),'');
	$clefs = $trouver_table('auteurs');
	$clefs = $clefs['field'];

	//extrait uniquement les donnees qui ont ete proposees a la modification
	$val = array_intersect_key($valeurs,$clefs);

	//Verification du password
	if(($mode == 'inscription_pass') || ($mode == 'modification_auteur_pass')){
		if (strlen(_request('password')) != 0)
			$new_pass = _request('password');
		elseif($mode == 'inscription_pass')
            $new_pass = _request('pass');

		if (strlen($new_pass)>0) {
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
			$val['statut'] = $config_i2['statut_nouveau'] ? $config_i2['statut_nouveau'] : '6forum';
		}
	}else{
		if(!is_numeric($id_auteur)){
			$val['statut'] = 'aconfirmer';
		}
	}
	$pass_length = strlen($val['pass']);
	if($pass_length == 0){
		unset($val['pass']);
	}

	// affecter $id_auteur avec la session si dispo
	// présent depuis moins de quelques minutes ou inscrit partiel
	include_spip("inc/inscription3_session");
	if($id_inscrit = i3_session_valide()){
		$id_auteur = $id_inscrit ;
		$modif_par_session = true ;
		$data["modif"] = true ;
	}

	if($config_i2['creation'] == 'on'){
		$val['creation'] = date("Y-m-d H:i:s",time());
	}

	//inserer les donnees dans spip_auteurs -- si $id_auteur : mise a jour - autrement : nouvelle entree
	if (!$new or $modif_par_session) {
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

	if(isset($_FILES['logo_auteur']) && ($_FILES['logo_auteur']['error'] == 0)){
		$chercher_logo = charger_fonction('chercher_logo', 'inc');

		// supprimer l'ancien logo
		$on = $chercher_logo($id_auteur, 'id_auteur', 'on');
		if ($on) @unlink($on[0]);

		// ajouter le nouveau
		include_spip('action/iconifier');
		action_spip_image_ajouter_dist(
			type_du_logo('id_auteur').'on'.$id_auteur, false, false
		); // beurk
		// indiquer qu'on doit recalculer les images
		$GLOBALS['var_images'] = true;
	}

	$traiter_plugin = pipeline('i3_traiter_formulaire',
		array(
			'args' => array(
				'id_auteur' => $id_auteur,
				'champs' => $valeurs
			),
		'data' => $data
		)
	);

    if (!$new){
        $message = _T('inscription3:profil_modifie_ok');
        if($mode == 'modification_auteur_simple'){
        	$message .= '<br />'._T('inscription3:mot_passe_reste_identique');
        }
        $editable = true;
    } else {
		if(!$traiter_plugin['ne_pas_confirmer_par_mail']){
			$envoyer_inscription = charger_fonction('envoyer_inscription3','inc');
			$envoyer_inscription($id_auteur,$mode);
			$message = _T('inscription3:formulaire_inscription_ok');
		}
		if($traiter_plugin['message_ok'])
			$message = $traiter_plugin['message_ok'];
			$editable = false;
    }

	// Invalider les caches
	include_spip('inc/invalideur');
    suivre_invalideur("id='id_auteur/$id_auteur'");

	$retour['editable'] = $editable;
	$retour['message_ok'] = $message;

	if($redirect){
		$retour['redirect'] = $redirect;
	}

    return $retour;
}
?>