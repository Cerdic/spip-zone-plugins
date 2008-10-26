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
function formulaires_inscription2_pass_charger_dist($id_auteur = NULL){

	global $tables_principales;
   
	//initialise les variables d'environnement pas defaut
	$valeurs = array();

	//recupere la liste des champs possible
	$champs = inscription2_champs_formulaire();

	//si on a bien un auteur alors on preremplit le formulaire avec ses informations
	//les noms des champs sont les memes que ceux de la base de donnees
	if (is_numeric($id_auteur)) {
		$auteur = sql_fetsel(
			$champs,
			'spip_auteurs LEFT JOIN spip_auteurs_elargis USING(id_auteur)',
			'id_auteur ='.$id_auteur
		);
		spip_log($auteur);
		$champs = $auteur;
	}
	return $champs;
}

function formulaires_inscription2_pass_verifier_dist($id_auteur = NULL){
    
	//charge la fonction de controle du login et mail
	//$test_inscription = charger_fonction('test_inscription');
	
	//initialise le tableau des erreurs
	$erreurs = array();
				
	//messages d'erreur au cas par cas
	//verifier les champs obligatoire
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
			
			//si clef obligatoire, obligatoire active et _request() vide alors erreur
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
	var_dump($erreurs);
    return $erreurs; // si c'est vide, traiter sera appele, sinon le formulaire sera resoumis
}

function formulaires_inscription2_pass_traiter_dist($id_auteur = NULL){

	global $tables_principales;
	
	/* Generer la liste des champs a traiter
	* champ => valeur formulaire
	*/
	
	foreach(inscription2_champs_formulaire() as $clef => $valeur) {
		$valeurs[$valeur] = _request($valeur);  
	}
	
	//Definir le login
	include_spip('balise/formulaire_inscription');
	if (!_request('login')) {
		$valeurs['login'] = test_login($valeurs['nom'], $valeurs['email']);
	}
    
	//$valeurs contient donc tous les champs remplit ou non 
	
	//definir les champs pour spip_auteurs
	$table = "spip_auteurs";
    
	//genere le tableau des valeurs a mettre a jour pour spip_auteurs
	//toutes les clefs qu'inscription2 peut mettre a jour
	$clefs = array_fill_keys(array('login','nom','email','bio'),'');
	//extrait uniquement les donnees qui ont ete proposees a la modification
	$val = array_intersect_key($valeurs,$clefs);
	
	//inserer les donnees dans spip_auteurs -- si $id_auteur mise a jour autrement nouvelle entree
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

	//extrait les valeurs propres a spip_auteurs_elargis
	
	//genere le tableau des valeurs a mettre a jour pour spip_auteurs
	//toutes les clefs qu'inscription2 peut mettre a jour
	//s'appuie sur les tables definies par le plugin
	$clefs = $tables_principales['spip_auteurs_elargis']['field'];
	
	//extrait uniquement les donnees qui ont ete proposees a la modification
	$val = array_intersect_key($valeurs,$clefs);
	
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


function message_inscription2_pass($var_user, $mode) {
	$row = sql_select("nom, statut, id_auteur, login, email, alea_actuel","spip_auteurs", "email=" . _q($var_user['email']));
	$row = sql_fetch($row);

	if (!$row) 							// il n'existe pas, creer les identifiants  
		return inscription2_nouveau_pass($var_user);
	
	if ($row['statut'] == '5poubelle')	// irrecuperable
		return _T('form_forum_access_refuse');
	
	if ($row['statut'] == 'aconfirmer'){	// deja inscrit
		$envoyer_inscription = charger_fonction('envoyer_inscription2','inc');
		$envoyer_inscription($row);/**RENVOYER MAIL D'INSCRIPTION **/
		return _T('inscription2:mail_renvoye');
	}
	return _T('form_forum_email_deja_enregistre');}

function inscription2_nouveau_pass($declaration){
	$declaration = inscription2_test_login($declaration);

	//insertion des donnees ds la table spip_auteurs
	foreach($declaration as $cle => $val){
		if($cle == 'newsletters' or $cle == 'zones' or $cle =='sites' or $cle == 'zone' or $cle =='abonnement')
			continue;
		if ($cle == 'email' or $cle == 'nom' or $cle == 'bio' or $cle == 'statut' or $cle == 'login' or $cle =='pass')
			$auteurs[$cle] = $val;
		else
			$elargis[$cle]= $val;
	}
	//insertion des donnees dans la table spip_auteurs
	$n = sql_insert('spip_auteurs', ('(' .join(',',array_keys($auteurs)).')'), ("(" .join(", ",array_map('_q', $auteurs)) .")"));
	$declaration['id_auteur'] = $n;
	$elargis['id_auteur'] = $n;
	$date = date('Y-m-d');
	//insertion des donnees dans la table spip_auteurs_elargis
	if(isset($declaration['newsletters'])){
		foreach($declaration['newsletters'] as $value){
			if($value != '0')
				sql_insertq("spip_auteurs_listes",
				array("id_auteur" => $n, "id_liste" => $value, "statut" =>"valide" , "date_inscription" => $date));
	}}
	if(isset($declaration['zones'])){
		foreach($declaration['zones'] as $value)
			sql_insertq("spip_zones_auteurs", array("id_auteur" => $n, "id_zone" => $value));
	}
	if(isset($declaration['domaines']) and $declaration['zone'] and lire_config('plugin/ACCESRESTREINT')){
		foreach($declaration['zone'] as $value)
			sql_insertq("spip_zones_auteurs", array("id_auteur" => $n, "id_zone" => $value));
	}
	
	$n = sql_insert('`spip_auteurs_elargis`', ('(' .join(',',array_keys($elargis)).')'), ("(" .join(", ",array_map('_q', $elargis)) .")"));
	
	if(isset($declaration['abonnement'])){
		$value = $declaration['abonnement'] ;	
			sql_insertq("spip_auteurs_elargis_abonnements", array("id_auteur_elargi" => $n, "id_abonnement" => $value));
	}
	
	return $declaration;
}

function envoyer_inscription2_pass($var_user) {
	include_spip('inc/envoyer_mail');
	$nom_site_spip = nettoyer_titre_email($GLOBALS['meta']["nom_site"]);
			$adresse_site = $GLOBALS['meta']["adresse_site"];
			$message = _T('inscription2:message_auto')
			. _T('inscription2:email_bonjour', array('nom'=>$var_user['nom']))."\n\n"
			. _T('inscription2:texte_email_confirmation', array('login'=> $var_user['login'], 'nom_site' => $nom_site_spip));

	if (envoyer_mail($var_user['email'],"[$nom_site_spip] "._T('inscription2:compte_active'), $message))
		return false;
	else
		return _T('inscription2:probleme_email');
}

?>