<?php
/**
 * Plugin Inscription3 pour SPIP
 * © 2007-2010 - cmtmt, BoOz, kent1
 * Licence GPL v3
 *
 * Fonctions d'insertion dans les pipelines
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Insertion dans le pipeline ajouter_boutons (SPIP)
 * 
 * Modifie le bouton afficher les visiteurs aux webmestres
 *
 * @param object $boutons_admin
 * @return
 */
function inscription3_ajouter_boutons($boutons_admin){
	if ($GLOBALS['connect_statut'] == "0minirezo" && $GLOBALS["connect_toutes_rubriques"]) {
		if(isset($boutons_admin['auteurs'])){
			unset($boutons_admin['auteurs']->sousmenu['auteurs']);
		}
		if(isset($boutons_admin['bando_reactions'])){
			unset($boutons_admin['bando_reactions']->sousmenu['visiteurs']);
		}
	}
	return $boutons_admin;
}

/**
 *
 * Insertion dans le pipeline i3_exceptions_des_champs_auteurs_elargis
 * qui empêche la création de certains champs dans la table
 * après les avoir configuré
 *
 * @return array Un tableau des champs correspondant au "name" de son input de configuration dans le CFG
 * @param array $array Prend un tableau en argument qui doit être complété en fonction des besoins
 */

function inscription3_i3_exceptions_des_champs_auteurs_elargis($array){
	// liste des champs pour lesquels on ne doit pas créer de champs dans la table spip_auteurs_elargis

	// Principalement les champs déjà présents dans spip_auteurs
	$array[] = 'id_auteur';
	$array[] = 'bio';
	$array[] = 'nom';
	$array[] = 'pass';
	$array[] = 'login';
	$array[] = 'email';
	$array[] = 'statut';
	$array[] = 'pgp';
	$array[] = 'url_site';
	$array[] = 'nom_site';

	// Des choses spécifiques à inscription3
	$array[] = 'logo';
	$array[] = 'username';
	$array[] = 'statut_nouveau';
	$array[] = 'statut_int';
	$array[] = 'statut_interne';
	$array[] = 'accesrestreint';
	$array[] = 'password';
	$array[] = 'affordance_form';
	$array[] = 'reglement';
	$array[] = 'reglement_article';
	$array[] = 'password_complexite';
	$array[] = 'validation_numero_international';
	$array[] = 'pays_defaut';
	$array[] = 'valider_comptes';

	return $array;
}

/**
 *
 * Insertion dans le pipeline i3_verifications_specifiques du plugin inscription3
 * Utilisation de l'API verifier du plugin éponyme
 *
 * Pour chaque champs on fourni un array associatif contenant :
 * - type => la fonction de l'api de vérification à utiliser
 * - options => un array des options à passer à cette fonction
 *
 * @return array Tableau contenant plusieurs tableaux en fonction du type de champs
 * @param object $array Doit recevoir un tableau du même type
 */

function inscription3_i3_verifications_specifiques($array){

	// Les emails : fonction verifier/email
	$array['email'] = array('type' => 'email');
	$array['mail_inscription'] = array('type' => 'email');

	// Les noms (signature)
	$array['nom'] = array('type' => 'signature');
	$array['nom_inscription'] = array('type' => 'signature');
	
	// Les logins : fonction verifier/login
	$array['login'] = array('type' => 'login');

	// Les statuts : fonction verifier/statut
	$array['statut'] = array('type' => 'statut');

	// Les codes postaux : fonction verifier/codepostal
	$array['code_postal'] = array('type' => 'codepostal');
	$array['code_postal_pro'] = array('type' => 'codepostal');

	// Les numéros de téléphone : fonction verifier/telephone
	$array['telephone'] = array('type' => 'telephone');
	$array['fax'] = array('type' => 'telephone');
	$array['mobile'] = array('type' => 'telephone');
	$array['telephone_pro'] = array('type' => 'telephone');
	$array['fax_pro'] = array('type' => 'telephone');
	$array['mobile_pro'] = array('type' => 'telephone');

	return $array;
}

/**
 * Insertion dans le pipeline affiche_droite (SPIP)
 * 
 * Dans certaines pages définies, afficher le lien d'accès à la page des comptes utilisateurs
 *
 * @return array Le même tableau qu'il reçoit en argument
 * @param array $flux Un tableau donnant des informations sur le contenu passé au pipeline
 */

function inscription3_affiche_droite($flux){
	if(((preg_match('/^inscription3/',$flux['args']['exec']))
		 || (preg_match('/^auteur/',$flux['args']['exec']))
		 || (preg_match('/^i3_/',$flux['args']['exec']))
		 || (($flux['args']['exec'] == 'cfg') && ((_request('cfg') == 'inscription3') || preg_match('/^i3_/',_request('cfg'))))
		)
		 && ($flux['args']['exec'] != 'inscription3_adherents')){
    	$flux['data'] .= recuperer_fond('prive/inscription3_affiche_droite');
	}
	return $flux;
}

/**
 * Insertion dans le pipeline i3_definition_champs
 * 
 * Définition spécifique des champs qui ne sont pas de type text
 * Par défaut inscription3 définit les champs comme étant de type texte, cela peut être 
 * différent pour d'autres ...
 */
function inscription3_i3_definition_champs($flux){
	/**
	 * Récupération de la configuration d'inscription3
	 * pour éviter d'avoir à utiliser la fonction lire_config beaucoup de fois
	 */
	$config_i3 = lire_config('inscription3');

	$flux['pays'] = array(
		'type' => 'pays', // type de saisie
		'saisie_externe' => true,
		'sql' => "int NOT NULL", // declaration sql
		'saisie_parametres' => array('option_intro'=>_T('inscription3:aucun'),'class' => 'pays'),
		'defaut' => $config_i3['pays_defaut'] ? $config_i3['pays_defaut'] : '',
		'obligatoire' => ($config_i3['pays_obligatoire'] == 'on') ? true : false
	);
	$flux['pays_pro'] = array(
		'type' => 'pays', // type de saisie
		'saisie_externe' => true,
		'sql' => "int NOT NULL", // declaration sql
		'label' => _T('inscription3:label_pays').' ['._T('inscription3:label_travail').']',
		'saisie_parametres' => array('option_intro'=>_T('inscription3:aucun'),'class' => 'pays'),
		'obligatoire' => ($config_i3['pays_pro_obligatoire'] == 'on') ? true : false
	);
	$flux['validite'] = array(
		'type' => 'date_jour_mois_annee', // type de saisie
		'sql' => "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL", // declaration sql
		'obligatoire' => ($config_i3['validite_obligatoire'] == 'on') ? true : false,
		'verifier' => 'date',
		'verifier_options' => array('format' => 'amj')
	);
	$flux['creation'] = array(
		'type' => 'date_jour_mois_annee', // type de saisie
		'saisie_externe' => true,
		'sql' => "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL", // declaration sql
		'verifier' => 'date',
		'verifier_options' => array('format' => 'amj')
	);
	$flux['naissance'] = array(
		'type' => 'date_jour_mois_annee', // type de saisie
		'saisie_externe' => true,
		'sql' => "DATE DEFAULT '0000-00-00' NOT NULL", // declaration sql
		'obligatoire' => ($config_i3['naissance_obligatoire'] == 'on') ? true : false,
		'saisie_parametres' => array('class'=>'nomulti'),
		'verifier' => 'date',
		'verifier_options' => array('format' => 'amj')
	);
	$flux['sexe'] = array(
		'type' => 'radio', // type de saisie
		'saisie_externe' => true,
		'saisie_parametres' => array('label'=> _T('inscription3:label_civilite'),'datas'=> array('F' => _T('inscription3:choix_feminin'),'M' => _T('inscription3:choix_masculin'))),
		'sql' => "varchar(2) NOT NULL default ''", // declaration sql
		'obligatoire' => ($config_i3['sexe_obligatoire'] == 'on') ? true : false
	);
	
	$flux['ville_pro']['label'] = _T('inscription3:label_ville').' ['._T('inscription3:label_travail').']';
	
	$flux['commentaire']['type'] = 'textarea';
	$flux['commentaire']['saisie_parametres'] = array_merge((is_array($flux['addresse']['saisie_parametres']) ? $flux['addresse']['saisie_parametres'] : array()),array('rows'=>5,'class'=>'adresse'));
	
	$flux['adresse']['type'] = 'textarea';
	$flux['adresse']['saisie_parametres'] = array_merge((is_array($flux['addresse']['saisie_parametres']) ? $flux['addresse']['saisie_parametres'] : array()),array('rows'=>5,'class'=>'adresse'));

	$flux['adresse_pro']['type'] = 'textarea';
	$flux['adresse_pro']['saisie_parametres'] = array_merge((is_array($flux['addresse_pro']['saisie_parametres']) ? $flux['addresse_pro']['saisie_parametres'] : array()),array('rows'=>5,'class'=>'adresse'));
	$flux['adresse_pro']['label'] = _T('inscription3:label_adresse').' ['._T('inscription3:label_travail').']';

	$flux['telephone']['verifier'] = 'telephone';
	$flux['telephone']['saisie_parametres'] = array('class'=>'nomulti');
	$flux['telephone_pro']['verifier'] = 'telephone';
	$flux['telephone_pro']['label'] = _T('inscription3:label_telephone').' ['._T('inscription3:label_travail').']';
	$flux['telephone_pro']['saisie_parametres'] = array('class'=>'nomulti');
	
	$flux['fax']['verifier'] = 'telephone';
	$flux['fax']['saisie_parametres'] = array('class'=>'nomulti');
	$flux['fax_pro']['verifier'] = 'telephone';
	$flux['fax_pro']['label'] = _T('inscription3:label_fax').' ['._T('inscription3:label_travail').']';
	$flux['fax_pro']['saisie_parametres'] = array('class'=>'nomulti');
	
	$flux['mobile']['verifier'] = 'telephone';
	$flux['mobile']['saisie_parametres'] = array('class'=>'nomulti');
	$flux['mobile_pro']['verifier'] = 'telephone';
	$flux['mobile_pro']['label'] = _T('inscription3:label_mobile').' ['._T('inscription3:label_travail').']';
	$flux['mobile_pro']['saisie_parametres'] = array('class'=>'nomulti');

	$flux['code_postal']['verifier'] = 'codepostal';
	$flux['code_postal']['saisie_parametres'] = array('class'=>'nomulti');
	$flux['code_postal_pro']['verifier'] = 'codepostal';
	$flux['code_postal_pro']['label'] = _T('inscription3:label_code_postal').' ['._T('inscription3:label_travail').']';
	$flux['code_postal_pro']['saisie_parametres'] = array('class'=>'nomulti');

	return $flux;
}

/**
 * Insertion dans le pipeline post_edition (SPIP)
 * - Invalide le cache lors de la modification d'un auteur
 * L'invalideur est passé par défaut sur les articles et forums
 * cf : ecrire/inc/modifier.php
 *
 * @param array $flux Le contexte du pipeline
 */
function inscription3_post_edition($flux){
	if(($flux['args']['action'] == 'modifier') && ($flux['args']['type'] == 'auteur')){
		$id = $flux['args']['id_objet'];
		$invalideur = "id='id_auteur/$id'";
		if($invalideur){
			include_spip('inc/invalideur');
			suivre_invalideur($invalideur);
		}
	}
	return $flux;
}

/**
 * Insertion dans le pipeline formulaire_charger (SPIP)
 * 
 * Charge des valeurs spécifiques dans le formulaire d'inscription
 * 
 * @param array $flux Le contexte d'environnement du pipeline
 * @return array $flux Le contexte d'environnement modifié
 */
function inscription3_formulaire_charger($flux){
	if ($flux['args']['form']=='inscription'){
		$valeurs['_commentaire'] = '';
		$chercher_champs = charger_fonction('inscription3_champs_formulaire','inc');
		$champs = $chercher_champs();

		foreach($champs as $clef =>$valeur) {
            if (_request($valeur)) {
                $valeurs[$valeur] = trim(_request($valeur));
            }
            if($valeur == 'naissance'){
            	include_spip('inc/date');
            	if(_request('naissance') && preg_match("/([0-9]{4})-([0-9]{2})-([0-9]{2})/",_request('naissance'),$date_naissance)){
					$valeurs['annee'] = $date_naissance[1];
					$valeurs['mois'] = $date_naissance[2];
					$valeurs['jour'] = $date_naissance[3];
            	}else{
					$valeurs['annee'] = _request('annee');
					$valeurs['mois'] = _request('mois');
					$valeurs['jour'] = _request('jour');
            	}
            }
	    }
	    $valeurs = pipeline('i3_charger_formulaire',
			array(
				'args' => $flux['args'],
				'data' => $valeurs
			)
		);
		$flux['data'] = array_merge($flux['data'],$valeurs);
	}
	return $flux;
}

/**
 * Insertion dans le pipeline formulaire_verifier (SPIP)
 * 
 * Vérifie des valeurs spécifiques dans le formulaire d'inscription
 * 
 * @param array $flux Le contexte d'environnement du pipeline
 * @return array $flux Le contexte d'environnement modifié
 */
function inscription3_formulaire_verifier($flux){
	if ($flux['args']['form']=='oubli'){
		$erreurs = $flux['args']['erreurs'];
		if(!$erreurs OR (count($erreurs) == 0)){
			include_spip('base/abstract_sql');
			$email = _request('oubli');
			$statut = sql_getfetsel('statut','spip_auteurs','email='.sql_quote($email));
			if($statut == '8aconfirmer'){
				$flux['data']['oubli'] = _T('inscription3:erreur_compte_attente_mail');
				$flux['data']['message_erreur'] = _T('inscription3:erreur_compte_attente');
			}
		}
	}
	if ($flux['args']['form']=='inscription'){
		/**
		 * On inclue inscription3_mes_fonctions pour prendre en compte la surcharge de 
		 * formulaires_inscription_traiter en ajax
		 */
		include_spip('inscription3_mes_fonctions');
		$config_i3 = lire_config('inscription3');
		$erreurs['message_erreur'] = $flux['args']['erreurs']['message_erreur'];
		/**
		 * Vérification des champs obligatoires
		 * En fonction de ceux présents dans le formulaire
		 */
		$champs_obligatoires = charger_fonction('inscription3_champs_obligatoires','inc');
		$obligatoires = $champs_obligatoires(null,'inscription');
		unset($obligatoires['email']);
		unset($obligatoires['nom']);
		include_spip('inc/editer');
		$erreurs = formulaires_editer_objet_verifier('auteur',null,$obligatoires);
		
		if($erreurs['reglement']){
			$erreurs['reglement'] = _T('inscription3:erreur_reglement_obligatoire');
		}
		
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
					$type['options'] = array_merge(is_array($type['options']) ? $type['options'] : array(),$_GET);
					$erreurs[$clef] = $verifier($valeurs[$clef],$type['type'],$type['options']);
					if($erreurs[$clef] == null){
						unset($erreurs[$clef]);
					}
				}
			}
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
		if($config_i3['inscription3/pass'] == 'on'){
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
					// (1) Si on est dans la modif d'id_auteur on garde l'ancien pass si rien n'est rentre
					// donc on accepte la valeur vide
					// dans le cas de la création d'un auteur ... le password sera necessaire
					$erreurs['pass'] = _T('inscription3:password_obligatoire');
				}
			}
		}
		
		$args = array_merge($flux['args'],array('champs' => $valeurs));
		
		/**
		 * Offrir aux autres plugins la possibilite de verifier les donnees
		 */
		$erreurs = pipeline('i3_verifier_formulaire',
			array(
				'args' => $args,
				'data' => $erreurs
			)
		);
		
		/**
		 * Message d'erreur generalise
		 */
		if (count($erreurs)) {
			if(isset($erreurs_obligatoires)){
				$erreurs['message_erreur'] .= _T('inscription3:formulaire_remplir_obligatoires');
			}else{
				$erreurs['message_erreur'] .= _T('inscription3:formulaire_remplir_validation');
			}
		}
		$flux['data'] = array_merge($flux['data'],$erreurs);
	}
	return $flux;
}

/**
 * Insertion dans le pipeline formulaire_traiter (SPIP)
 * 
 * Traitement des valeurs spécifiques dans le formulaire d'inscription
 * 
 * @param array $flux Le contexte d'environnement du pipeline
 * @return array $flux Le contexte d'environnement modifié
 */
function inscription3_formulaire_traiter($flux){
	include_spip('base/abstract_sql');
	if($flux['args']['form']=='mot_de_passe'){
		$row = sql_fetsel('id_auteur,email,login,source','spip_auteurs',array("statut<>'5poubelle'","pass<>''"),'','maj DESC','1');
		$affordance = lire_config('inscription3/affordance_form','login');
		switch($affordance){
			case 'email' : 
				$flux['data']['message_ok'] = _T('pass_nouveau_enregistre').
					"<p>" . _T('inscription3:pass_rappel_email', array('email' => $row['email'])); break;
			case 'login_et_email' :
				$flux['data']['message_ok'] = _T('pass_nouveau_enregistre').
					"<p>" . _T('inscription3:pass_rappel_login_email', array('email' => $row['email'],'login'=>$row['login']));break;
		}
	}
	if ($flux['args']['form']=='inscription'){
		include_spip('inscription3_mes_fonctions');
		$config_i3 = lire_config('inscription3',array());
		$data = array();
		/**
		 * Les valeurs "normales" du formulaire d'inscription
		 * qui nous permettront de retrouver l'id_auteur
		 */
		$nom = _request('nom_inscription');
		$mail = _request('mail_inscription');
		
		/**
		 * A ce moment là SPIP a déjà créé l'auteur et lui a déjà donné un login et pass
		 */
		$user = sql_fetsel('*','spip_auteurs','email='.sql_quote($mail));
		
		/**
		 * Si l'on demande le passe dans le formulaire
		 * On a un mode avec pass fourni
		 * Sinon un mode simple
		 */
		if(($config_i3['pass'] == 'on') && (strlen(_request('pass')))){
			$mode = 'inscription_pass';
		}
		else{
			$mode = 'inscription';
		}
	
		/**
		 * Generer la liste des champs a traiter
		 * champ => valeur formulaire
		 */
		$chercher_champs = charger_fonction('inscription3_champs_formulaire','inc');
		$champs = $chercher_champs();
	
		foreach($champs as $clef => $valeur) {
			$valeurs[$valeur] = trim(_request($valeur));
			if($valeur == 'naissance'){
				include_spip('inc/date');
				$annee = trim(_request('annee'));
				$mois = _request('mois');
				$jour = _request('jour');
				$valeurs[$valeur] = format_mysql_date($annee,$mois,$jour);
			}
		}
		// Definir le login s'il a besoin de l'etre
		// NOM et LOGIN sont des champs obligatoires donc a la creation il ne doivent pas etre vide
		// Apres on s'en fiche s'il n'est pas dans le formulaire
		if(!$valeurs['login'] && !$nom){
			if($valeurs['nom_famille']||$valeurs['prenom']){
				$valeurs['nom'] = trim($valeurs['prenom'].' '.$valeurs['nom_famille']);
			}
			else{
				$valeurs['nom'] = strtolower(translitteration(preg_replace('/@.*/', '', $mail)));
			}
		}else{
			$valeurs['nom'] = $nom;
		}
		$valeurs['email'] = $mail;
		if(!$valeurs['login']){
			if($user['login']){
				$valeurs['login'] = $user['login'];
			}
		}
		$trouver_table = charger_fonction('trouver_table','base');
		
		//genere le tableau des valeurs a mettre a jour pour spip_auteurs
		//toutes les clefs qu'inscription3 peut mettre a jour
		$clefs = $trouver_table('auteurs');
		$clefs = $clefs['field'];
	
		//extrait uniquement les donnees qui ont ete proposees a la modification
		$val = array_intersect_key($valeurs,$clefs);
		
		/**
		 * Si on demande le pass dans le formulaire
		 * Le compte est automatiquement activé
		 */
		if($mode == 'inscription_pass'){
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
			$val['statut'] = $config_i3['statut_nouveau'] ? $config_i3['statut_nouveau'] : '6forum';
		}
		
		/**
		 * On met le compte en "à confirmer" si on a configurer les chose comme cela
		 * Dans ce cas on met la bio à '' si elle n'est pas dans le form afin d'enlever le statut temporaire qui y est stocké par SPIP
		 * Sinon si on a la bio dans le formulaire et qu'on la reçoit, on met directement un statut à 
		 * l'auteur, sinon on laisse l'ancien (nouveau normalement)
		 */
		if($config_i3['valider_comptes'] == 'on'){
			$mode = 'aconfirmer';
			if(!$val['bio']){
				$val['bio'] = '';
			}
			$val['statut'] = '8aconfirmer';
		}
		/**
		 * Si on a le champ bio dans le formulaire on force le statut
		 */
		else if(_request('bio')){
			$val['statut'] = $config_i3['statut_nouveau'] ? $config_i3['statut_nouveau'] : '6forum';
		}
		
		$pass_length = strlen($val['pass']);
		if($pass_length == 0){
			unset($val['pass']);
		}
	
		if($config_i3['creation'] == 'on'){
			$val['creation'] = date("Y-m-d H:i:s",time());
		}
	
		if (function_exists('test_inscription'))
			$f = 'test_inscription';
		else $f = 'test_inscription_dist';

		$desc = $f($user['bio'], $mail, $valeurs['nom'], $user['id_auteur']);
		
		if (is_array($desc) AND $mail = $desc['email']){
			include_spip('base/abstract_sql');
			/**
			 * On recrée le pass pour être sûr d'avoir le bon
			 */
			$desc['pass'] = creer_pass_pour_auteur($user['id_auteur']);
			$desc['login'] = $val['login'];
			
			/**
			 * Mise à jour des infos
			 */
			sql_updateq(
				"spip_auteurs",
				$val,
				'id_auteur = '.$user['id_auteur']
			);
		
			$args = array_merge($flux['args'],array(
				'id_auteur' => $user['id_auteur'],
				'champs' => $valeurs
			));
			
			/**
			 * Prise en charge du logo
			 */
			if(isset($_FILES['logo']) && ($_FILES['logo']['error'] == 0)){
			    $chercher_logo = charger_fonction('chercher_logo', 'inc');
				
				// supprimer l'ancien logo
				$on = $chercher_logo($id_auteur, 'id_auteur', 'on');
				if ($on) @unlink($on[0]);
		
				// ajouter le nouveau
				include_spip('action/iconifier');
				action_spip_image_ajouter_dist(
					type_du_logo('id_auteur').'on'.$user['id_auteur'], false, false
				);
				// indiquer qu'on doit recalculer les images
				$GLOBALS['var_images'] = true;
			}
		    /**
		     * On appelle le pipeline traiter de inscription3
		     * On connait dorénavant l'id_auteur
		     * Ce pipeline doit retourner un array avec les valeurs possibles suivantes :
		     * - ne_pas_confirmer_par_mail boolean (permet de squeezer la notification)
		     * - message_ok string (permet de modifier le message de retour du formulaire)
		     * - editable boolean (permet de modifier le comportement d'affichage au retour) 
		     */
			$traiter_plugin = pipeline('i3_traiter_formulaire',
				array(
					'args' => $args,
					'data' => $flux['data']
				)
			);
			
			if(!$traiter_plugin['ne_pas_confirmer_par_mail']){
				if($mode == 'aconfirmer'){
					$traiter_plugin['message_ok'] = _T('inscription3:form_retour_aconfirmer');
					if ($notifications = charger_fonction('notifications', 'inc')) {
						$notifications('i3_inscriptionauteur', $user['id_auteur'],
							array('statut' => '8aconfirmer')
						);
					}
				}else if($mode == 'inscription_pass'){
					$traiter_plugin['message_ok'] = _T('inscription3:form_retour_inscription_pass');
					if ($notifications = charger_fonction('notifications', 'inc')) {
						$notifications('i3_inscriptionauteur', $user['id_auteur'],
							array('statut' => $val['statut'],'pass' => 'ok')
						);
					}
					if($config_i3['auto_login'] == 'on'){
						$auteur = sql_fetsel('*','spip_auteurs','id_auteur='.intval($user['id_auteur']));
						$session = charger_fonction('session','inc');
						$session($auteur);
					}
				}else{
					$envoyer_mail = charger_fonction('envoyer_mail','inc');
					if (function_exists('envoyer_inscription3')){
						$mode = $config_i3['statut_nouveau'];
						$f = 'envoyer_inscription3';
						list($sujet,$msg,$from,$head) = $f($desc, $nom, $mode, $id);
					}
					if($desc){
						if (!$envoyer_mail($mail, $sujet, $msg, $from, $head))
							$traiter_plugin['message_ok'] = _T('form_forum_probleme_mail');
						else{
							$traiter_plugin['message_ok'] = _T('form_forum_identifiant_mail');
						}
					}else{
						$traiter_plugin['message_ok'] = _T('form_forum_identifiant_mail');
					}
				}
			}
			
			$flux['data']['editable'] = $traiter_plugin['editable'];
			$flux['data']['message_ok'] = $traiter_plugin['message_ok'];
		}
	}
	return $flux;
}

/**
 * Insertion dans le pipeline recuperer_fond (SPIP)
 * Insère des champs dans le formulaire d'inscription
 *
 * @param array $flux
 * @return array
 */
function inscription3_recuperer_fond($flux){
	$config = lire_config('inscription3');
	if ($flux['args']['fond']=='formulaires/inscription'){
		$insc = recuperer_fond('formulaires/inc-inscription-inscription3',$flux['data']['contexte']);
		$flux['data']['texte'] = preg_replace(",(<li [^>]*class=[\"']saisie_mail_inscription.*<\/li>),Uims","\\1".$insc,$flux['data']['texte'],1);
		if(($texte_inscription = $config['inscription_texte']) && ($texte_inscription != 'origine')){
			switch($texte_inscription){
				case 'aucun' :
					$flux['data']['texte'] = preg_replace(",<p class=\'explication\'>.*<\/p>,Uims",'',$flux['data']['texte'],1);
					break;
				case 'libre' :
					$texte = PtoBR(propre($config['inscription_texte_libre']));
					$flux['data']['texte'] = preg_replace(",(<p class=\'explication\'>)(.*)(<\/p>),Uims","\\1".$texte."\\3",$flux['data']['texte'],1);
					break;
			}		
		}
	}
	if ($flux['args']['fond']=='formulaires/login'){
		if(($type_affordance = $config['affordance_form']) && ($type_affordance != 'login')){
			switch($type_affordance){
				case 'email' :
					$label = _T('inscription3:votre_mail'); break;
				case 'login_et_email' : 
					$label = _T('inscription3:votre_login_mail'); break;
				case 'libre' :
					$label = $config['inscription3/affordance_form_libre'] ? $config['inscription3/affordance_form_libre'] : _T('login_login2');break;
			}
			if($label)
				$flux['data']['texte'] = preg_replace(",(<label.*for=\"var_login\">)(.*)(<\/label>),Uims","\\1".$label."\\3",$flux['data']['texte'],1);	
		}
	}
	/**
	 * On ajoute un vérificateur de complexité de mot de passe
	 */
	if (in_array($flux['args']['fond'],array('formulaires/mot_de_passe','formulaires/editer_auteur')) && ($config['inscription3/password_complexite'] == 'on')){
		$js = recuperer_fond('formulaires/inc-js_pass_verification',$flux['data']['contexte']);
		$flux['data']['texte'] = preg_replace(",(<\/form>)(.*),Uims","\\1".$js."\\2",$flux['data']['texte'],1);
	}
	return $flux;
}

function inscription3_openid_recuperer_identite($flux){
	if(isset($flux['args']['dob'])){
		$flux['data']['naissance'] = $flux['args']['dob'];
	}
	if(isset($flux['args']['country'])){
		$id_pays = sql_getfetsel('id_pays','spip_geo_pays','code_iso='.sql_quote($flux['args']['country']));
		$flux['data']['pays'] = $id_pays;
	}
	if(isset($flux['args']['postcode'])){
		$flux['data']['code_postal'] = $flux['args']['postcode'];
	}
	if(isset($flux['args']['gender'])){
		$flux['data']['sexe'] = $flux['args']['gender'];
	}
	if(isset($flux['args']['fullname'])){
		$noms = explode(' ',$flux['args']['fullname']);
		$flux['data']['prenom'] = $noms[0];
		array_shift($noms);
		$flux['data']['nom_famille'] = implode(' ',$noms);
	}
	return $flux;
}

function inscription3_openid_inscrire_redirect($flux){
	
	$auteur = $flux['args']['infos_auteur'];

	$url = $flux['args']['url'];
	
	$url = parametre_url($url,'code_postal',$auteur['code_postal']);
	$url = parametre_url($url,'pays',$auteur['pays']);
	$url = parametre_url($url,'naissance',$auteur['naissance']);
	$url = parametre_url($url,'sexe',$auteur['sexe']);
	$url = parametre_url($url,'login',$auteur['login']);
	$url = parametre_url($url,'nom_famille',$auteur['nom_famille']);
	$url = parametre_url($url,'prenom',$auteur['prenom']);
	$flux['data'] = $url;
	return $flux;
}

/**
 * Insertion dans le pipeline editer_contenu_objet (SPIP)
 * Enlève les champs dans le formulaire d'édition d'auteur pour le profil utilisateur
 * comme il est configuré dans inscription3
 *
 * @param array $flux
 * @return array
 */
function inscription3_editer_contenu_objet($flux){
	$args = $flux['args'];
	$type = $args['type'];
	if ($type == 'auteur'){
		$config = lire_config('inscription3');
		$champs_spip = array('nom','email','bio','pgp','url_site','nom_site','login','pass');
		$champs_vires = array();
		$inserer_saisie = '';
		foreach($champs_spip as $champ){
			if(!test_espace_prive() && ($config[$champ.'_fiche_mod'] != 'on')){
				if($champ == 'login'){
					$flux['data'] = preg_replace(",(<li [^>]*class=[\"']editer_new_($champ).*<\/li>),Uims","",$flux['data'],1);
				}else if($champ == 'pass'){
					$flux['data'] = preg_replace(",(<li [^>]*class=[\"']editer_new_($champ).*<\/li>),Uims","",$flux['data'],1);
					$flux['data'] = preg_replace(",(<li [^>]*class=[\"']editer_new_($champ)2.*<\/li>),Uims","",$flux['data'],1);
				}else{
					$flux['data'] = preg_replace(",(<li [^>]*class=[\"']editer_($champ).*<\/li>),Uims","",$flux['data'],1);
				}
				$champs_vires[] = $champ;
				if(in_array($champ, array('nom','email')))
					$inserer_saisie .= "<input type='hidden' name='$champ' value='".$flux['args']['contexte'][$champ]."' />\n";				
			}
		}
		if(in_array('url_site',$champs_vires) && in_array('nom_site',$champs_vires)){
			$flux['data'] = preg_replace(",(<li [^>]*class=[\"']editer_liens.*<\/li>),Uims","",$flux['data'],1);
		}
		if(in_array('pass',$champs_vires) && in_array('login',$champs_vires)){
			$flux['data'] = preg_replace(",(<li [^>]*class=[\"']editer_identification.*<\/li>),Uims","",$flux['data'],1);
		}
		if(strlen($inserer_saisie)){
			$flux['data'] = preg_replace('%(<!-- controles md5 -->)%is',$inserer_saisie."\n".'$1', $flux['data']);
		}
	}
	return $flux;
}

/**
 * Insertion dans le pipeline "notifications_destinataires" (SPIP)
 * 
 * En fonction du type de notification, rempli un tableau d'adresses emails
 * 
 * @param array $flux Le contexte du pipeline
 * @return array
 */
function inscription3_notifications_destinataires($flux){
	$quoi = $flux['args']['quoi'];
	$options = $flux['args']['options'];

	/**
	 * Cas de la validation ou invalidation d'un compte d'un utilisateur
	 * Cas également de l'inscription d'un auteur
	 * Envoi à l'utilisateur ($options['type'] == 'user')
	 */
	if (($quoi=='instituerauteur' 
		AND $options['statut_ancien'] == '8aconfirmer'
		AND $options['type'] == 'user') OR
		($quoi=='i3_inscriptionauteur' 
		AND $options['type'] == 'user')){
	
		$id_auteur = $flux['args']['id']; 
		include_spip('base/abstract_sql'); 
		$mail = sql_getfetsel("email", "spip_auteurs", "id_auteur=".intval($id_auteur));
		$flux['data'][] = $mail;
	}
	/**
	 * Cas de la validation ou invalidation d'un compte d'un utilisateur
	 * Envoi aux administrateurs ($options['type'] == 'admin')
	 */
	else if(($quoi=='instituerauteur' 
		AND $options['statut_ancien'] == '8aconfirmer'
		AND $options['type'] == 'admin') OR
		($quoi=='i3_inscriptionauteur' 
		AND $options['type'] == 'admin')){
		$admins = sql_select('email','spip_auteurs','statut="0minirezo"');
		
		while ($qui = sql_fetch($admins)) {
			$flux['data'][] = $qui['email'];
		}
	}
	return $flux;
}


/**
 * Insertion dans le pipeline taches_generales_cron (SPIP)
 *
 * Vérifie la présence à intervalle régulier d'utilisateurs à valider ou invalider et notifie les admins
 *
 * @return L'array des taches complété
 * @param array $taches_generales Un array des tâches du cron de SPIP
 */
function inscription3_taches_generales_cron($taches_generales){
	$taches_generales['inscription3_taches_generales'] = 24*60*60;
	return $taches_generales;
}
?>