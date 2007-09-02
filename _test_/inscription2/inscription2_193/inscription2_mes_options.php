<?php
	// en attendant l'intervention corrective de real3t
	define('_SIGNALER_ECHOS', false); // horrible
	
	
	/**Plugin Inscription 2 avec CFG **/
	if (!defined("_ECRIRE_INC_VERSION")) return;
	include_spip('cfg_options');
	include_spip('base/serial');
	include_spip('base/abstract_sql');
	
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
		if($val!='' and $cle != 'nom' and $cle != 'statut_nouveau' and $cle != 'email' and $cle != 'username' and $cle != 'statut_int'  and $cle != 'accesrestreint' and !ereg("^(categories|zone|newsletter).*$", $cle) ){
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
	
	// surcharger auteur session, desactivé car ca pete en 193
	if(is_array($var_user) and isset($GLOBALS['auteur_session']['id_auteur'])){
		$id = $GLOBALS['auteur_session']['id_auteur'];
		$query = spip_query("select ".join(', ', array_keys($var_user))." from spip_auteurs_elargis where id_auteur = $id");
		$query = spip_fetch_array($query);
		/*var_dump($query);
		exit;*/
		//$GLOBALS['auteur_session'] = array_merge($query,$GLOBALS['auteur_session'] );
		
	}
	
# autoriser les visiteurs a modifier leurs infos
#define ('_DEBUG_AUTORISER', true);
if (!function_exists('autoriser_spip_auteurs_elargis')) {
function autoriser_auteurs_elargi($faire, $type, $id, $qui, $opt) {
	$query = spip_query("select id_auteur from spip_auteurs_elargis where id=$id");
	$query = spip_fetch_array($query);
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


//email envoye lors de l'inscription

function envoyer_inscription2($id_auteur) {
	if ($GLOBALS['spip_version_code']>=1.9259){include_spip('inc/envoyer_mail');}
	else{include_spip('inc/mail');}
	
	$nom_site_spip = nettoyer_titre_email($GLOBALS['meta']["nom_site"]);
	$adresse_site = $GLOBALS['meta']["adresse_site"];
	
	$prenom = (lire_config('inscription2/prenom')) ? "b.prenom," : "" ;
	
	$var_user=spip_fetch_array(spip_query("select a.nom, $prenom a.id_auteur, a.alea_actuel, a.login, a.email from spip_auteurs a join spip_auteurs_elargis b where a.id_auteur='$id_auteur' and a.id_auteur=b.id_auteur"));
	
	$message = _T('inscription2:message_auto')."\n\n"
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
		return "ok";
	else
		return _T('inscription2:probleme_email');
}

?>