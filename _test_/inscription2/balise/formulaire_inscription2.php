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
		if($val!='' and !ereg("^.+_(fiche|table).*$", $cle)){
			if($cle== 'zones')
				continue;
			
			elseif($val!='' and $cle == 'username')
				$var_user['login'] = _request($cle);
			
			elseif($val!='' and $cle == 'naissance')
				$var_user[$cle] = _request('annee').'-'._request('mois').'-'._request('jour');
			
			elseif($val!='' and $cle == 'creation')
				$var_user[$cle] = date('Y-m-d');
			
			elseif($cle == 'adresse')
				$var_user[$cle] = _request('adresse').' '._request('adresse2');
			
			elseif(ereg("^categorie.*$", $cle)){
				$aux = _request('categories');
				if($aux != '0')
					$var_user['categorie'] = $aux;
			}
			elseif(ereg("^newsletter.*$", $cle)){
				$var_user['newsletters'] = _request('newsletters');
				$var_user['`spip_listes_format`'] =_request('format');
			}
			elseif(ereg("^statut_rel.*$", $cle))
				$var_user['statut_relances'] = lire_config('inscription2/statut_rel');
	
			elseif($cle=='accesrestreint') 
				$var_user['zones'] = lire_config('inscription2/zones');
				
			elseif( $cle == 'domaines')
				$var_user['dom'] = _request($cle);

			else
				$var_user[$cle] = _request($cle);
		}
	}
	$commentaire = true;
	$aux = true;
	if($var_user['dom']){
		include(find_in_path("inc/domaines.php"));
		$var_user['sites'] = $domaine[$var_user['dom']]['sites'] ;
		$var_user['zone'] = $domaine[$var_user['dom']]['zones'] ;
		$aux = !empty($var_user['sites']);
		foreach($var_user['sites'] as $val)
			$aux = ($aux or ereg("^.*".$val."$", $var_user[email]));
		if(!$aux)
			$message = _T('inscription2:mail_non_domaine');
	}
	if($var_user[email] and $aux){
		$commentaire = message_inscription2($var_user, $mode);
		if (is_array($commentaire)) 
			$commentaire = envoyer_inscription2($commentaire);
		$message = $commentaire ? '' : _T('inscription2:lisez_mail');
	}
	$var_user['message'] = $message;
	$var_user['commentaire'] = $commentaire;
	$var_user['mode'] = $mode;
	$var_user['self'] = str_replace('&amp;','&',(self()));
	$var_user['adresse2'] = _request('adresse2');
	return array("formulaires/inscription2", $GLOBALS['delais'],
			$var_user);}

function test_mode_inscription2($mode) {
	return (($mode == 'redac' AND $GLOBALS['meta']['accepter_inscriptions'] == 'oui')
		OR ($mode == 'forum' AND ($GLOBALS['meta']['accepter_visiteurs'] == 'oui'
			OR $GLOBALS['meta']['forums_publics'] == 'abo')));}

function message_inscription2($var_user, $mode) {
	$row = spip_query("SELECT nom, statut, id_auteur, login, email, alea_actuel FROM `spip_auteurs` WHERE email=" . _q($var_user['email']));
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
	$declaration = inscription2_test_login($declaration);

	$declaration['statut'] = 'aconfirmer';
	//insertion des données ds la table spip_auteurs
	foreach($declaration as $cle => $val){
		if($cle == 'newsletters' or $cle == 'zones' or $cle == 'dom' or $cle =='sites' or $cle == 'zone')
			continue;
		if ($cle == 'email' or $cle == 'nom' or $cle == 'bio' or $cle == 'statut' or $cle == 'login')
			$auteurs[$cle] = $val;
		else
			$elargis[$cle]= $val;
	}
	//insertion des données dans la table spip_auteurs
	$declaration['alea_actuel'] = rand(1,99999);
	$auteurs['alea_actuel']=$declaration['alea_actuel'];
	$n = spip_abstract_insert('spip_auteurs', ('(' .join(',',array_keys($auteurs)).')'), ("(" .join(", ",array_map('_q', $auteurs)) .")"));
	$declaration['id_auteur'] = $n;
	$elargis['id_auteur'] = $n;
	$date = date('Y-m-d');
	//insertion des données dans la table spip_auteurs_elargis
	if(isset($declaration['newsletters'])){
		foreach($declaration['newsletters'] as $value){
			if($value != '0')
				spip_query("INSERT INTO `spip_auteurs_listes` 
				(`id_auteur`, `id_liste`, `statut`, `date_inscription`) 
				VALUES ('$n', '$value', 'valide','$date')");
	}}
	if(isset($declaration['zones'])){
		foreach($declaration['zones'] as $value)
			spip_query("INSERT INTO `spip_zones_auteurs` (`id_auteur`, `id_zone`)VALUES ('$n', '$value')");
	}
	if(isset($declaration['dom']) and $declaration['zone'] and lire_config('plugin/ACCESRESTREINT')){
		foreach($declaration['zone'] as $value)
			spip_query("INSERT INTO `spip_zones_auteurs` (`id_auteur`, `id_zone`)VALUES ('$n', '$value')");
	}
	
	$n = spip_abstract_insert('`spip_auteurs_elargis`', ('(' .join(',',array_keys($elargis)).')'), ("(" .join(", ",array_map('_q', $elargis)) .")"));
	
	return $declaration;}

function envoyer_inscription2($var_user) {
	include_spip('inc/mail');
	$nom_site_spip = nettoyer_titre_email($GLOBALS['meta']["nom_site"]);
	$adresse_site = $GLOBALS['meta']["adresse_site"];
	$message = _T('inscription2:message_auto')
			. _T('inscription2:email_bonjour', array('nom'=>sinon($var_user['prenom'],$var_user['nom'])))."\n\n"
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

function inscription2_test_login($var_user) {
	if(!isset($var_user['login']))
		$var_user['login']=$var_user['nom'];
	$login = $var_user['login'];
	for ($i = 1; ; $i++) {
		$n = spip_num_rows(spip_query("SELECT id_auteur FROM spip_auteurs WHERE login='$login' LIMIT 1"));
		if (!$n){
			$var_user['login'] = $login;
			return $var_user;
		}
		$login = $var_user['login'].$i;
	}
}
?>
