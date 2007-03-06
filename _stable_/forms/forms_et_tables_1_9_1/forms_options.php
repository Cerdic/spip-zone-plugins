<?php
/*
 * forms
 * Gestion de formulaires editables dynamiques
 *
 * Auteurs :
 * Antoine Pitrou
 * Cedric Morin
 * Renato Formato
 * (c) 2005,2006 - Distribue sous licence GNU/GPL
 *
 */
if (!defined('_DIR_PLUGIN_FORMS')){
	$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
	define('_DIR_PLUGIN_FORMS',(_DIR_PLUGINS.end($p))."/");
}
if (defined('_DIR_PLUGIN_CRAYONS'))
	include_spip('forms_crayons');

include_spip('base/forms');
$GLOBALS['forms_actif_exec'][] = 'donnees_edit';
$GLOBALS['forms_saisie_km_exec'][] = 'donnees_edit';

function autoriser_form_dist($faire, $type='', $id=0, $qui = NULL, $opt = NULL) {
	if ($type=='form' OR $type=='donnee'){
		if ($faire=='administrer'){
			return ($qui['statut'] == '0minirezo');
		}
		else
			return ($qui['statut'] == '0minirezo');
	}
	return false;
}
function autoriser_donnee_dist($faire,$type,$id_donnee,$qui,$opt){
	static $types = array();
	if (!isset($opt['id_form']) OR !isset($opt['statut'])){
		if ($id_donnee>0){
			$res = spip_query("SELECT id_form,statut FROM spip_forms_donnees WHERE id_donnee="._q($id_donnee));
			if (!$row = spip_fetch_array($res)) return false;
			$opt['id_form'] = $row['id_form'];
			$opt['statut'] = $row['statut'];
		}
		else
			$opt['statut'] = '';
	}
	$id_form = $opt['id_form'];
	if (!isset($opt['type_form'])){
		if (!isset($types[$id_form])){
			$res = spip_query("SELECT type_form FROM spip_forms WHERE id_form="._q($id_form));
			if (!$row = spip_fetch_array($res)) return false;
			$types[$id_form] = $row['type_form'];
		}
		$opt['type_form'] = $types[$id_form];
	}
	$type_form = in_array($opt['type_form'],array('','sondage'))?'form':$opt['type_form'];
	// Chercher une fonction d'autorisation explicite
	if (
	// 1. Sous la forme "autoriser_type_form_donnee_faire"
		(
		$type_form
		AND $f = 'autoriser_'.$type_form.'_donnee_'.$faire
		AND (function_exists($f) OR function_exists($f.='_dist'))
		)

	// 2. Sous la forme "autoriser_type_form_donnee"
	OR (
		$type_form
		AND $f = 'autoriser_'.$type_form.'_donnee'
		AND (function_exists($f) OR function_exists($f.='_dist'))
		)
	// 3. Sous la forme "autoriser_table_donnee_faire"
	OR (
		$f = 'autoriser_table_donnee_'.$faire
		AND (function_exists($f) OR function_exists($f.='_dist'))
	)
	// 4. Sous la forme "autoriser_table_donnee_faire"
	OR (
		$f = 'autoriser_table_donnee'
		AND (function_exists($f) OR function_exists($f.='_dist'))
	)

	// 5. Sinon autorisation generique
	OR (
		$f = 'autoriser_form'
		AND (function_exists($f) OR function_exists($f.='_dist'))
	)

	)
		$a = $f($faire,$type,intval($id_donnee),$qui,$opt);
	if (_DEBUG_AUTORISER) spip_log("autoriser_form_donnee_dist delegue a $f($faire,$type,$id): ".($a?'OK':'niet'));
	return $a;
}

function autoriser_form_donnee_modifier_dist($faire, $type, $id_donnee, $qui, $opt) {
	if (!intval($id_donnee)) return false;
	if (!isset($opt['id_form']) OR !$id_form = $opt['id_form']) return false;
	// un admin dans le back office a toujours le droit de modifier
	if (($qui['statut'] == '0minirezo')) return true;
	$result = spip_query("SELECT * FROM spip_forms WHERE id_form="._q($id_form));
	if (!$row = spip_fetch_array($result)) return false;
	include_spip('inc/forms');
	$dejareponse=Forms_verif_cookie_sondage_utilise($id_form);
	global $auteur_session;
	$id_auteur = $auteur_session ? intval($auteur_session['id_auteur']) : 0;
	$cookie = $_COOKIE[Forms_nom_cookie_form($id_form)];
	if (($row['modifiable'] == 'oui') && $dejareponse) {
		$q = "SELECT id_donnee FROM spip_forms_donnees WHERE id_form="._q($id_form);
		if ($cookie) $q.=" AND (cookie="._q($cookie)." OR id_auteur="._q($id_auteur).")";
		else $q.=" AND id_auteur="._q($id_auteur)." ";
		//si unique, ignorer id_donnee, si pas id_donnee, ne renverra rien
		if ($row['multiple']=='oui' || !_DIR_RESTREINT) $q.=" AND id_donnee="._q($id_donnee);
		$r=spip_query($q);
		if ($r=spip_fetch_array($r)) return true;
	}
	return false;
}
function autoriser_table_donnee_modifier_dist($faire, $type, $id_donnee, $qui, $opt) {
	return autoriser_form_donnee_modifier_dist($faire, $type, $id_donnee, $qui, $opt);
}
function autoriser_form_donnee_creer_dist($faire, $type, $id_donnee, $qui, $opt) {
	if (!isset($opt['id_form']) OR !$id_form = $opt['id_form']) return false;
	// un admin dans le back office a toujours le droit d'inserer
	if (($qui['statut'] == '0minirezo')) return true;
	$result = spip_query("SELECT * FROM spip_forms WHERE id_form="._q($id_form));
	if (!$row = spip_fetch_array($result)) return false;
	if ($row['multiple']=='oui') return true;
	$dejareponse=Forms_verif_cookie_sondage_utilise($id_form);
	if ($dejareponse) return false;
	return true;
}
function autoriser_table_donnee_creer_dist($faire, $type, $id_donnee, $qui, $opt) {
	return autoriser_form_donnee_creer_dist($faire, $type, $id_donnee, $qui, $opt);
}
function autoriser_form_donnee_instituer_dist($faire,$type,$id_donnee,$qui,$opt) {
	if (($qui['statut'] != '0minirezo')
	OR !isset($opt['nouveau_statut'])
	OR ($opt['nouveau_statut']=='prepa')) return false;
	return true;
}
function autoriser_table_donnee_instituer_dist($faire,$type,$id_donnee,$qui,$opt) {
	return autoriser_form_donnee_instituer_dist($faire,$type,$id_donnee,$qui,$opt);
}

// le reglage du cookie doit se faire avant l'envoi de tout HTML au client
function Forms_poser_cookie_sondage($id_form) {
	if ($id_form = intval($id_form)) {
		$nom_cookie = $GLOBALS['cookie_prefix'].'cookie_form_'.$id_form;
		// Ne generer un nouveau cookie que s'il n'existe pas deja
		if (!$cookie = $_COOKIE[$nom_cookie]) {
			include_spip("inc/acces");
			$cookie = creer_uniqid();
		}
		$GLOBALS['cookie_form'] = $cookie; // pour utilisation dans inc_forms...
		// Expiration dans 30 jours
		setcookie($nom_cookie, $cookie, time() + 30 * 24 * 3600);
	}
}

function Forms_generer_url_sondage($id_form) {
	return generer_url_public("sondage","id_form=$id_form",true);
}

if ((intval(_request('ajout_reponse'))) && (_request('ajout_cookie_form') == 'oui'))
	Forms_poser_cookie_sondage(_request('ajout_reponse'));

// test si un cookie sondage a ete pose
foreach($_COOKIE as $cookie=>$value){
	if (preg_match(",".$GLOBALS['cookie_prefix']."cookie_form_([0-9]+),",$cookie,$reg)){
		$idf = intval($reg[1]);
		$res = spip_query("SELECT id_article FROM spip_forms_articles WHERE id_form=".intval($idf));
		while($row=spip_fetch_array($res)){
			$ida = $row['id_article'];
			if (
						(isset($GLOBALS['article'])&&($GLOBALS['article']==$ida))
					||(isset($GLOBALS['id_article'])&&($GLOBALS['id_article']==$ida))
					||(isset($GLOBALS["article$ida"]))
					||(isset($GLOBALS['contexte_inclus']['id_article'])&&($GLOBALS['contexte_inclus']['id_article']==$ida)) ){
					// un article qui utilise le form va etre rendu
					// il faut utiliser le marquer cache pour ne pas polluer la page commune
					$GLOBALS['marqueur'].=":sondage $idf";
					break;
				}
		}
	}
}

?>