<?php
/**
 * Plugin Inscription3 pour SPIP
 * © 2007-2012 - cmtmt, BoOz, kent1
 * Licence GPL v3
 * 
 * Fichier des fonctions spécifiques du plugin
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 *
 * Donne le nom d'un pays en fonction de son id
 *
 * @return false|string false dans le cas ou il ne reçoit pas de paramètres ou si le paramètre n'est pas bon
 * @param int $id_pays L'id_pays de la table spip_geo_pays
 */
if(!function_exists('id_pays_to_pays')){
	function id_pays_to_pays($id_pays){
		if((is_numeric($id_pays)) && ($id_pays != 0)){
			$pays = sql_getfetsel('nom', 'spip_geo_pays', 'id_pays ='.$id_pays);
			return typo($pays);
		}
		else return;
	}
}

/**
 *
 * Fonction utilisée par le critère i3_recherche
 *
 * @return array Le tableau des auteurs correspondants aux critères de recherche
 * @param string $quoi[optional] Le contenu textuel recherché
 * @param object $ou[optional] Le champs dans lequel on recherche
 * @param object $table[optional]
 */
function i3_recherche($quoi=NULL,$ou=NULL,$table=NULL){
	if(isset($quoi) && isset($ou)){
		$quoi = texte_script(trim($quoi));
		include_spip('base/serial'); // aucazou !
		global $tables_principales;

		if(isset($tables_principales[table_objet_sql($table)]['field'][$ou])){
			$auteurs = sql_get_select('id_auteur',table_objet_sql($table),"$ou LIKE '%$quoi%'");
		}
		else{
			global $tables_jointures;
			if(isset($tables_jointures[table_objet_sql($table)]) && ($jointures=$tables_jointures[table_objet_sql($table)])){
				foreach($jointures as $jointure=>$val){
					if(isset($tables_principales[table_objet_sql($val)]['field'][$ou])){
						$auteurs = sql_get_select('id_auteur',table_objet_sql($table)." AS $table LEFT JOIN ".table_objet_sql($val)." AS $val USING(id_auteur)","$val.$ou LIKE '%$quoi%'");
					}
				}
			}
		}
		return "($auteurs)";
	}
}

/**
 *
 * Critère utilisé pour rechercher dans les utilisateurs (page ?exec=inscription2_adherents)
 *
 */
function critere_i3_recherche_dist($idb, &$boucles){
	$boucle = &$boucles[$idb];
	$primary = $boucle->primary;
	$ou = '@$Pile[0]["case"]';
	$quoi = '@$Pile[0]["valeur"]';
	$table = $boucle->type_requete;
	$boucle->hash .= "
	\$auteurs = i3_recherche($quoi,$ou,$table);
	";
	$boucle->where[] = array("'IN'","'$boucle->id_table." . "$primary'",'$auteurs');
}

include_spip('inc/cextras_autoriser');
if(isset($GLOBALS['visiteur_session']['statut']) && ($GLOBALS['visiteur_session']['statut'] != '0minirezo') && function_exists('restreindre_extras')){
	if(isset($GLOBALS['inscription3'])){
		$inscription3 = is_array(@unserialize($GLOBALS['inscription3'])) ? unserialize($GLOBALS['inscription3']) : array();
		$champ_testes = array();
		foreach($inscription3 as $clef => $val) {
			$cle = preg_replace("/_(obligatoire|fiche|table).*/", "", $clef);
			if(!in_array($cle,$champ_testes) && ($val == 'on')){
				/**
				 * Si on n'autorise pas la modification dans la configuration
				 * ou si le champ en question est "creation"
				 */
				if($inscription3[$cle.'_fiche_mod'] != 'on'){
					restreindre_extras('auteurs', $cle,'*'); 
				}
				$champ_testes[] = $cle;
			}
		}
	}
}

if(function_exists('restreindre_extras'))
	restreindre_extras('auteurs', 'creation','*');

/**
 * Un critère règlement permettant de :
 * - Trouver les pages uniques avec le champ page reglement
 * - Retourner sinon l'article de règlement sélectionné dans la conf
 * 
 * @param unknown_type $idb
 * @param unknown_type $boucles
 * @param unknown_type $crit
 */
function critere_reglement_dist($idb, &$boucles, $crit){
	$boucle = &$boucles[$idb];
	if(defined('_DIR_PLUGIN_PAGES')){
		$sous = "sql_get_select('art.id_article','spip_articles as art','page=\'reglement\'')";
		$where = "array('IN', '".$boucle->id_table.".".$boucle->primary."', '('.$sous.')')";
	}
	if(!$sous){
		$reglement = lire_config('inscription3/reglement_article',0);
		$where = "array('=', '".$boucle->id_table.".id_article', '".$reglement."')";
	}
	$boucle->where[]= $where;
}

function envoyer_inscription3($desc, $nom, $mode, $id) {
	$nom_site_spip = nettoyer_titre_email($GLOBALS['meta']["nom_site"]);
	$adresse_site = $GLOBALS['meta']["adresse_site"];
	if ($mode == '6forum') {
		$adresse_login = generer_url_public('login'); 
		$msg = 'form_forum_voici1';
	} else {
		$adresse_login = $adresse_site .'/'. _DIR_RESTREINT_ABS;
		$msg = 'form_forum_voici2';
	}
	
	$msg = _T('form_forum_message_auto')."\n\n"
		. _T('form_forum_bonjour', array('nom'=>$nom))."\n\n"
		. _T($msg, array('nom_site_spip' => $nom_site_spip,
			'adresse_site' => $adresse_site . '/',
			'adresse_login' => $adresse_login)) . "\n\n- "
		. _T('form_forum_login')." " . $desc['login'] . "\n- "
		. _T('form_forum_pass'). " " . $desc['pass'] . "\n\n";

	return array("[$nom_site_spip] "._T('form_forum_identifiants'), $msg);
}

/**
 * Surcharge de la fonction de traitement du formulaire d'inscription sinon les surcharges de fonctions 
 * d'envoi de mails ne fonctionnent pas en ajax
 * 
 * @param unknown_type $mode
 * @param unknown_type $focus
 * @param unknown_type $id
 */
function formulaires_inscription_traiter($mode, $focus, $id=0) {

	$nom = _request('nom_inscription');
	$mail_complet = _request('mail_inscription');
	
	if (function_exists('test_inscription'))
		$f = 'test_inscription';
	else 	$f = 'test_inscription_dist';
	$desc = $f($mode, $mail_complet, $nom, $id);

	if (!is_array($desc)) {
		$desc = _T($desc);
	} else {
		include_spip('base/abstract_sql');
		$res = sql_select("statut, id_auteur, login, email", "spip_auteurs", "email=" . sql_quote($desc['email']));
		if (!$res) 
			$desc = _T('titre_probleme_technique');
		else {
			$row = sql_fetch($res);
			// s'il n'existe pas deja, creer les identifiants  
			$desc = $row ? $row : inscription_nouveau($desc);
		}
	}
	return array('message_ok'=>is_string($desc) ? $desc : _T('form_forum_identifiant_mail'));
}

/**
 *
 * Récupère la valeur d'un champs d'un auteur si on ne possède que le nom du champs
 * Dans le cas de la boucle FOR par exemple
 *
 * @return
 * @param object $champs
 * @param object $id_auteur
 */
function inscription3_recuperer_champs($champs,$id_auteur){
	if($champs == 'login'){
		$champs = 'spip_auteurs.login';
	}
	if($champs == 'pays'){
		$resultat = sql_getfetsel("b.nom","spip_auteurs a LEFT JOIN spip_geo_pays b on a.pays = b.id_pays","a.id_auteur=$id_auteur");
		return typo($resultat);
	}
	$resultat = sql_getfetsel($champs,"spip_auteurs","id_auteur=$id_auteur");
	return typo($resultat);
}
?>