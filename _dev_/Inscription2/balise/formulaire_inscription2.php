<?php
if (!defined("_ECRIRE_INC_VERSION")) return;
include_spip('base/abstract_sql');

function balise_FORMULAIRE_INSCRIPTION2 ($p) {
	return calculer_balise_dynamique($p, 'FORMULAIRE_INSCRIPTION2', array());}

// args[0] peut valoir "redac" ou "forum" 
function balise_FORMULAIRE_INSCRIPTION2_stat($args, $filtres) {
	//initialiser mode d'inscription
	$mode = $args[0];
	if(!$mode)
		$mode = $GLOBALS['meta']['accepter_inscriptions'] == 'oui' ? 'redac' : 'forum'; 
	if(!test_mode_inscription2($mode))
		return '';
	else return array($mode);}

function balise_FORMULAIRE_INSCRIPTION2_dyn($mode) {
	if (!test_mode_inscription2($mode)) 
		return _T('pass_rien_a_faire_ici');
	//recuperer les infos inserées par le visiteur
	$var_user = array();
	foreach(lire_config('inscription2') as $cle => $val) {
		if($val!='' and $cle !='creation' and $cle !='username' and $cle != 'naissance' and !ereg("^.+_fiche$", $cle) and !ereg("^.+_fiche_mod$", $cle) and !ereg("^.+_table$", $cle)){
			$var_user[$cle] = _request($cle);
			if($cle == 'adresse')
				$var_user[$cle] .= ' '._request('adresse2');
		}if($val!='' and $cle == 'username')
			$var_user['login'] = _request($cle);
		if($val!='' and $cle == 'naissance')
			$var_user[$cle] = _request('annee').'-'._request('mois').'-'._request('jour');
		if($val!='' and $cle == 'creation')
			$var_user[$cle] = date('Y-m-d');
	}
	$mail = $var_user[email];	
	$commentaire = true;
	if ($mail) {
		include_spip('inc/filtres'); // pour email_valide
		$commentaire = message_inscription2($var_user, $mode);
		if (is_array($commentaire)) 
			$commentaire = envoyer_inscription2($commentaire);
	}
	$message = $commentaire ? '' : _T('inscription2:lisez_mail');
	return array("formulaires/inscription2", $GLOBALS['delais'],
			array('message' => $message,
				'mode' => $mode,
				'commentaire' => $commentaire,
				'nom_inscription' => _request('nom_inscription'),
				'mail_inscription' => _request('mail_inscription'),
				'self' => str_replace('&amp;','&',(self()))));}

function test_mode_inscription2($mode) {
	return (($mode == 'redac' AND $GLOBALS['meta']['accepter_inscriptions'] == 'oui')
		OR ($mode == 'forum' AND ($GLOBALS['meta']['accepter_visiteurs'] == 'oui'
			OR $GLOBALS['meta']['forums_publics'] == 'abo')));}

function test_inscription2($mode, $var_user) {
	include_spip('inc/filtres');
	$nom = trim(corriger_caracteres($var_user['nom']));
	if (!$nom || strlen($nom) > 64)
	    return _T('ecrire:info_login_trop_court');
	$var_user['nom'] = $nom;	
	if (!email_valide($var_user['email'])) 
		return _T('info_email_invalide');
	return $var_user;}

function message_inscription2($var_user, $mode) {
	$declaration = test_inscription2($mode, $var_user);
	
	if (is_string($declaration))
		return  $declaration;
	else //c'est un array
		$var_user = $declaration;

	$row = spip_query("SELECT nom, statut, id_auteur, login, email, alea_actuel FROM spip_auteurs WHERE email=" . _q($var_user['email']));
	$row = spip_fetch_array($row);

	if (!$row) 							// il n'existe pas, creer les identifiants  
		return inscription2_nouveau($var_user);
	
	if ($row['statut'] == '5poubelle')	// irrecuperable
		return _T('form_forum_access_refuse');
	
	if ($row['statut'] == 'aconfirmer'){	// deja inscrit
		envoyer_inscription2($row);/**RENVOYER MAIL D'INSCRIPTION **/
		return _T('inscription2:mail_renvoye');
	}
	return _T('form_forum_email_deja_enregistre');}

function inscription2_nouveau($declaration){
	if (!isset($declaration['login']))
		$declaration['login'] = inscription2_test_login($declaration['nom'], $declaration['email']);

	$declaration['statut'] = 'aconfirmer';
	//insertion des données ds la table spip_auteurs
	foreach($declaration as $cle => $val){
		if ($cle == 'email' or $cle == 'nom' or $cle == 'bio' or $cle == 'statut' or $cle == 'login')
			$auteurs[$cle] = $val;
		else
			$elargis[$cle]= $val;
	}
	//insertion des données dans la table spip_auteurs
	$declaration['alea_actuel'] = rand(1,9999);
	$auteurs['alea_actuel']=$declaration['alea_actuel'];
	$n = spip_abstract_insert('spip_auteurs', ('(' .join(',',array_keys($auteurs)).')'), ("(" .join(", ",array_map('_q', $auteurs)) .")"));
	$declaration['id_auteur'] = $n;
	$elargis['id_auteur'] = $n;
	//insertion des données dans la table spip_auteurs_elargis
	$n = spip_abstract_insert('spip_auteurs_elargis', ('(' .join(',',array_keys($elargis)).')'), ("(" .join(", ",array_map('_q', $elargis)) .")"));
	
	return $declaration;}

function envoyer_inscription2($var_user) {
	include_spip('inc/mail');
	$nom_site_spip = nettoyer_titre_email($GLOBALS['meta']["nom_site"]);
	$adresse_site = $GLOBALS['meta']["adresse_site"];
	$message = _T('inscription2:message_auto')
			. _T('inscription2:email_bonjour', array('nom'=>$var_user['nom']))."\n\n"
			. _T('inscription2:texte_email_inscription', array(
			'link_activation' => $adresse_site.'/?page=inscription2_confirmation&id='
			   .$var_user['id_auteur'].'&cle='.$var_user['alea_actuel'].'&mode=conf', 
			'link_suppresion' => $adresse_site.'/?page=inscription2_confirmation&id='
			   .$var_user['id_auteur'].'&cle='.$var_user['alea_actuel'].'&mode=sup',
			'login' => $var_user['login'], 'nom_site' => $nom_site_spip ));

	if (envoyer_mail($var_user['email'],
			 "[$nom_site_spip] "._T('inscription2:activation_compte'),
			 $message))
		return false;
	else
		return _T('inscription2:probleme_email');}

function inscription2_test_login($nom, $mail) {
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
		$n = spip_num_rows(spip_query("SELECT id_auteur FROM spip_auteurs WHERE login='$login' LIMIT 1"));
		if (!$n) return $login;
		$login = $login_base.$i;
	}
}
?>
