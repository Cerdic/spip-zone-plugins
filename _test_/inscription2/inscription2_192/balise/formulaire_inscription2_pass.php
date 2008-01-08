<?php
if (!defined("_ECRIRE_INC_VERSION")) return;
include_spip('base/abstract_sql');

function balise_FORMULAIRE_INSCRIPTION2_PASS ($p) {
	return calculer_balise_dynamique($p, 'FORMULAIRE_INSCRIPTION2_PASS', array());}

// args[0] peut valoir "redac" ou "forum" 
function balise_FORMULAIRE_INSCRIPTION2_PASS_stat($args, $filtres) {
	//initialiser mode d'inscription
	$mode = $args[0];
	if(!$mode)
		$mode = $GLOBALS['meta']['accepter_inscriptions'] == 'oui' ? 'redac' : 'forum'; 
	return array($mode);}

function balise_FORMULAIRE_INSCRIPTION2_PASS_dyn($mode) {
	//recuperer les infos inserées par le visiteur
	$var_user = array();
	foreach(lire_config('inscription2') as $cle => $val) {
		if($val!='' and !ereg("^.+_(obligatoire|fiche|table).*$", $cle)){
			if($cle== 'zones')
				continue;
				
			if($cle== 'statut_int' OR $cle== 'statut_nouveau')
				continue;
				
			elseif($val!='' and $cle == 'username')
				$var_user['login'] = _request($cle);
			
			elseif($val!='' and $cle == 'naissance')
				$var_user[$cle] = _request('annee').'-'._request('mois').'-'._request('jour');
			
			elseif($val!='' and $cle == 'creation')
				$var_user[$cle] = date('Y-m-d');
			
			elseif(ereg("^categorie.*$", $cle)){
				$aux = _request('categories');
				if($aux != '0')
					$var_user['categorie'] = $aux;
			}
			elseif(ereg("^newsletter.*$", $cle)){
				$var_user['newsletters'] = _request('newsletters');
				$var_user['`spip_listes_format`'] =_request('format');
			}
			elseif(ereg("^statut_int.*$", $cle))
				$var_user['statut_interne'] = lire_config('inscription2/statut_interne');
	
			elseif($cle=='accesrestreint') 
				$var_user['zones'] = lire_config('inscription2/zones');
			
			elseif(ereg("^statut_abonnement.*$", $cle)){
				$var_user['statut_abonnement'] = lire_config('inscription2/statut_abonnement');
				$var_user['abonnement'] =  _request('abonnement');
			}
			else
				$var_user[$cle] = _request($cle);
		}
	}
	$var_user['pass'] = _request('pass');
	$var_user['statut'] = _request('statut');
	$commentaire = true;
	$aux = true;
	if($var_user['domaines']){
		include(find_in_path("inc/domaines.php"));
		$var_user['sites'] = $domaine[$var_user['domaines']]['sites'] ;
		$var_user['zone'] = $domaine[$var_user['domaines']]['zones'] ;
		foreach($var_user['sites'] as $val)
			$aux = ($aux or ereg("^.*".$val."$", $var_user[email]));
		if(!$aux)
			$aux = empty($var_user['sites']);
		if(!$aux)
			$message = _T('inscription2:mail_non_domaine');
	}
	if($var_user['email'] and $aux){
		$commentaire = message_inscription2_pass($var_user, $mode);
		if (is_array($commentaire)){ 
				if(defined('_DIR_PLUGIN_ABONNEMENT')){
				//envoyer un mail a l'admin et a l'abonne
				$id_abonne = $commentaire["id_abonne"] ;
				$abonne_res = spip_query("SELECT a.nom_famille, a.prenom, a.adresse, a.code_postal, a.ville, a.pays, a.telephone, a.commentaire, a.validite, b.email, b.id_auteur, b.alea_actuel, b.login , b.pass FROM `spip_auteurs_elargis` a, `spip_auteurs` b WHERE a.id='$id_abonne' AND a.id_auteur = b.id_auteur") ;
				
				while($row = spip_fetch_array($abonne_res)){$abonne = $row ;}
				// remettre vide pour generer le lien
				$abonne['pass'] = "" ;
				$id_auteur = $commentaire["id_auteur"] ;

				spip_query("update spip_auteurs set pass = '' where id_auteur = $id_auteur");
				abonnement_envoyer_mails_confirmation("ok",$abonne,$commentaire["libelle"],"abonnement","");
				$commentaire = false ;
				}
				else{
				$commentaire = envoyer_inscription2_pass($commentaire);
				}
			}
		
		$message = $commentaire ? '' : _T('inscription2:lisez_mail');
	}
	$var_user['message'] = $message;
	$var_user['commentaires'] = $commentaire;
	$var_user['mode'] = $mode;
	$var_user['self'] = str_replace('&amp;','&',(self()));
	return array("formulaires/inscription2_pass", $GLOBALS['delais'],
			$var_user);}


function message_inscription2_pass($var_user, $mode) {
	$row = spip_query("SELECT nom, statut, id_auteur, login, email, alea_actuel FROM `spip_auteurs` WHERE email=" . _q($var_user['email']));
	$row = spip_fetch_array($row);

	if (!$row) 							// il n'existe pas, creer les identifiants  
		return inscription2_nouveau_pass($var_user);
	
	if ($row['statut'] == '5poubelle')	// irrecuperable
		return _T('form_forum_access_refuse');
	
	if ($row['statut'] == 'aconfirmer'){	// deja inscrit
		envoyer_inscription2($row);/**RENVOYER MAIL D'INSCRIPTION **/
		return _T('inscription2:mail_renvoye');
	}
	return _T('form_forum_email_deja_enregistre');}

function inscription2_nouveau_pass($declaration){
	$declaration = inscription2_test_login($declaration);

	//insertion des données ds la table spip_auteurs
	foreach($declaration as $cle => $val){
		if($cle == 'newsletters' or $cle == 'zones' or $cle =='sites' or $cle == 'zone' or $cle =='abonnement')
			continue;
		if ($cle == 'email' or $cle == 'nom' or $cle == 'bio' or $cle == 'statut' or $cle == 'login' or $cle =='pass')
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
			spip_query("INSERT INTO `spip_zones_auteurs` (`id_auteur`, `id_zone`) VALUES ('$n', '$value')");
	}
	if(isset($declaration['domaines']) and $declaration['zone'] and lire_config('plugin/ACCESRESTREINT')){
		foreach($declaration['zone'] as $value)
			spip_query("INSERT INTO `spip_zones_auteurs` (`id_auteur`, `id_zone`) VALUES ('$n', '$value')");
	}
	
	$n = spip_abstract_insert('`spip_auteurs_elargis`', ('(' .join(',',array_keys($elargis)).')'), ("(" .join(", ",array_map('_q', $elargis)) .")"));
	
	if(isset($declaration['abonnement'])){
		$value = $declaration['abonnement'] ;	
		
		spip_query("INSERT INTO `spip_auteurs_elargis_abonnements` (`id_auteur_elargi`, `id_abonnement`) VALUES ('$n', '$value')");
	
		$abonnement_res = spip_query("SELECT a.duree, a.periode, a.montant, a.libelle FROM `spip_abonnements` a  WHERE a.id_abonnement = $value") ;
	
		while($abonnement = spip_fetch_array($abonnement_res)){
		$libelle = $abonnement['libelle'];
		$duree = $abonnement['duree'] ;
		$periode = $abonnement['periode'] ;
		$montant = $abonnement['montant'] ;
		}
	
	
	if($periode == "jours"){
	$validite =  "DATE_ADD(CURRENT_DATE, INTERVAL ".$duree." DAY)" ;
	}elseif($periode == "mois"){
	$validite = "DATE_ADD(CURRENT_DATE, INTERVAL ".$duree." MONTH)" ;
	}
	
	// fixer la date de validite et le statut de paiement, (et des zones acces restreint selon l'abonnement a l'occasion)
	spip_query("UPDATE `spip_auteurs_elargis` SET statut_abonnement='abonne', statut_paiement='ok'  WHERE id='$n'") ;
	spip_query("UPDATE `spip_auteurs_elargis_abonnements` SET validite = $validite, montant = '$montant' WHERE id_auteur_elargi='$n'") ;
	
	$declaration['libelle'] = $libelle ;
	$declaration['id_abonne'] = $n ;
	
	}
	
	return $declaration;
}

function envoyer_inscription2_pass($var_user) {
	include_spip('inc/filtres'); // pour email_valide(), sinon pas d'envoi...
	include_spip('inc/mail');
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
