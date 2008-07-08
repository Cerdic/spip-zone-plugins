<?php
	/**Plugin Inscription 2 avec CFG **/
	if (!defined("_ECRIRE_INC_VERSION")) return;
	include_spip('cfg_options');
	include_spip('base/serial');
	include_spip('base/abstract_sql');
	
	//verifier qu'on a bien cfg
	if(!function_exists('lire_config')) die("Installez cfg voyons !");
	
	#define('_SIGNALER_ECHOS', false); // horrible
	
	// a chaque validation de cfg, verifier l'etat de la table spip_auteurs_elargis
	// BoOz : le bug du foreach quand on ajoute un champ est ptet lie a ce code ?	
	if(_request('exec')=='cfg' and _request('cfg')=='inscription2'){
		include_spip('inscription2_mes_fonctions');
		inscription2_verifier_tables();
	}
	
	// declaration des tables
	$GLOBALS['table_des_tables']['auteurs_elargis'] = 'auteurs_elargis';
	global $tables_principales;
	$table_nom = "spip_auteurs_elargis";
	$var_user = array();
	$spip_auteurs_elargis['id'] = "int NOT NULL";
	foreach(lire_config('inscription2') as $cle => $val) {
		$cle = ereg_replace("_(obligatoire|fiche|table).*", "", $cle);
		if($val!='' and $clef != 'login' and $cle != 'nom' and $cle != 'statut_nouveau' and $cle != 'email' and $cle != 'username' and $cle != 'statut_int'  and $cle != 'accesrestreint' and !ereg("^(categories|zone|newsletter).*$", $cle) ){
			if($cle == 'naissance' )
				$spip_auteurs_elargis[$cle] = "DATE DEFAULT '0000-00-00' NOT NULL";
			elseif($cle == 'validite' )
				$spip_auteurs_elargis[$cle] = "datetime DEFAULT '0000-00-00 00:00:00 NOT NULL";
			elseif($cle == 'pays')
				$spip_auteurs_elargis[$cle] = "int NOT NULL";
			elseif($cle == 'pays_pro')
				$spip_auteurs_elargis[$cle] = "int NOT NULL";
			else	
				$spip_auteurs_elargis[$cle] = "text NOT NULL";
			$var_user[$cle] = ' ';
		}
	}
	
	$spip_auteurs_elargis['id_auteur'] = "bigint(21) NOT NULL";
	$spip_auteurs_elargis_key = array("PRIMARY KEY"	=> "id", 'KEY id_auteur' => 'id_auteur');
	
	$spip_geo_pays['id_pays'] = "bigint(21) NOT NULL";
	$spip_geo_pays['pays'] = "text NOT NULL ";
	$spip_geo_pays_key = array("PRIMARY KEY"	=> "id_pays");
	
	$tables_principales['spip_auteurs_elargis']  =	array('field' => &$spip_auteurs_elargis, 'key' => &$spip_auteurs_elargis_key);
	$tables_principales['spip_geo_pays']  =	array('field' => &$spip_geo_pays, 'key' => &$spip_geo_pays_key);
	
	// surcharger auteur session, desactiv� car ca pete en 193
	/*
	if(is_array($var_user) and isset($GLOBALS['auteur_session']['id_auteur'])){
		$id = $GLOBALS['auteur_session']['id_auteur'];
		$query = spip_query("select ".join(', ', array_keys($var_user))." from spip_auteurs_elargis where id_auteur = $id");
		$query = spip_fetch_array($query);
		exit;
		$GLOBALS['auteur_session'] = array_merge($query,$GLOBALS['auteur_session'] );
	}
	*/	
	
	
	/* Gerer table Societes */
	$spip_societes['id_societe'] = "BIGINT(21) NOT NULL";
	$spip_societes['nom'] = "VARCHAR(255) NOT NULL ";
	$spip_societes['secteur'] = "VARCHAR(255) NOT NULL ";
	$spip_societes['adresse'] = "TEXT NOT NULL ";
	$spip_societes['code_postal'] = "VARCHAR(255) NOT NULL ";
	$spip_societes['ville'] = "VARCHAR(255) NOT NULL ";
	$spip_societes['id_pays'] = "BIGINT(21) NOT NULL";
	$spip_societes['telephone'] = "VARCHAR(255) NOT NULL ";
	$spip_societes['fax'] = "VARCHAR(255) NOT NULL ";	
	
	$spip_societes_key = array('PRIMARY KEY' => 'id_societe', 'KEY id_pay' => 'id_pays');
	
	$tables_principales['spip_societes'] = array('field' => &$spip_societes, 'key' => &$spip_societes_key);
    $table_des_tables['societes'] = 'societes';
	
# autoriser les visiteurs a modifier leurs infos
# define ('_DEBUG_AUTORISER', true);
if (!function_exists('autoriser_spip_auteurs_elargis')) {
function autoriser_auteurs_elargi($faire, $type, $id, $qui, $opt) {
	$query = sql_select("id_auteur","spip_auteurs_elargis","id=".$id);
	$query = sql_fetch($query);
	if($query['id_auteur']==$qui['id_auteur'])
		$qui['id_auteur'] = $id;
	return autoriser($faire,'auteur', $id, $qui, $opt);
}
}

if (!function_exists('autoriser_auteur_modifier')) {
function autoriser_auteur_modifier($faire, $type, $id, $qui, $opt) {

	// Ni admin ni redacteur => non
	if (in_array($qui['statut'], array('0minirezo', '1comite')))
		return autoriser_auteur_modifier_dist($faire, $type, $id, $qui, $opt);
	else
		return
			$qui['statut'] == '6forum'
			AND $id == $qui['id_auteur'];
	}
}


if (!function_exists('revision_auteurs_elargi')) {
function revision_auteurs_elargi_dist($id, $c=false) {
	return modifier_contenu('auteurs_elargi', $id,
		array(
			'champs' => array('nom_famille', 'prenom', 'adresse','ville','code_postal','pays','telephone','fax','mobile','adresse_pro','code_postal_pro','pays_pro','ville_pro','telephone_pro','fax_pro','mobile_pro'),
			'nonvide' => array('nom_email' => _T('info_sans_titre'))
		),
		$c);
}
}
	
//email envoye lors de l'inscription

function envoyer_inscription2($id_auteur,$mode="inscription") {
    include_spip('inc/envoyer_mail');
	
	$nom_site_spip = nettoyer_titre_email($GLOBALS['meta']["nom_site"]);
	$adresse_site = $GLOBALS['meta']["adresse_site"];
	
	$prenom = (lire_config('inscription2/prenom')) ? "b.prenom," : "" ;
	$nom = (lire_config('inscription2/nom_famille')) ? "b.nom_famille," : "" ;
	
    $var_user = sql_fetsel(
        "a.nom,$prenom $prenom a.id_auteur, a.alea_actuel, a.login, a.email",
        "spip_auteurs AS a LEFT JOIN spip_auteurs_elargis AS b USING(id_auteur)",
        "a.id_auteur =$id_auteur"
    );

    spip_log('envoie mail id: '.$id_auteur,'inscription2');
    spip_log($var_user,'inscription2');

	if($var_user['alea_actuel']==''){ 
 		$var_user['alea_actuel'] = rand(1,99999); 
 		sql_updateq(
 		    "spip_auteurs",
 		    array(
 		        "alea_actuel" => $var_user['alea_actuel']
 		    ),
 		    "id_auteur = $id_auteur"
 		);
      } 
 	
 	if($mode=="inscription"){ 
	
	$message = _T('inscription2:message_auto')."\n\n"
			. _T('inscription2:email_bonjour', array('nom'=> $var_user['prenom']." ".$var_user['nom']))."\n\n"
			. _T('inscription2:texte_email_inscription', array(
			'link_activation' => $adresse_site.'/spip.php?page=inscription2_confirmation&id='
			   .$var_user['id_auteur'].'&cle='.$var_user['alea_actuel'].'&mode=conf', 
			'link_suppresion' => $adresse_site.'/spip.php?page=inscription2_confirmation&id='
			   .$var_user['id_auteur'].'&cle='.$var_user['alea_actuel'].'&mode=sup',
			'login' => $var_user['login'], 'nom_site' => $nom_site_spip ));
			
		$sujet = "[$nom_site_spip] "._T('inscription2:activation_compte'); 
	}
	
	if($mode=="rappel_mdp"){ 
 	
 	$message = _T('inscription2:message_auto')."\n\n" 
 	. _T('inscription2:email_bonjour', array('nom'=>sinon($var_user['prenom'],$var_user['nom'])))."\n\n" 
 	. _T('inscription2:rappel_password')."\n\n" 
 	. _T('inscription2:choisir_nouveau_password')."\n\n" 
 	
 	. $adresse_site."/spip.php?page=inscription2_confirmation&id=" 
 	. $var_user['id_auteur']."&cle=".$var_user['alea_actuel']."&mode=conf"."\n\n" 
 	. _T('inscription2:rappel_login') . $var_user['login'] ; 
 	$sujet = "[$nom_site_spip] "._T('inscription2:rappel_password'); 
 	} 

    spip_log($message,'inscription2');

	if (inc_envoyer_mail_dist($var_user['email'],
			$sujet,
			 $message))
		return "ok";
	else
		return _T('inscription2:probleme_email');
}

?>
