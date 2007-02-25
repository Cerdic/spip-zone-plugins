<?php
/*
 * forms
 * Gestion de formulaires editables dynamiques
 *
 * Auteurs :
 * Antoine Pitrou
 * Cedric Morin
 * Renato
 * � 2005,2006 - Distribue sous licence GNU/GPL
 *
 */
if (!defined('_DIR_PLUGIN_FORMS')){
	$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
	define('_DIR_PLUGIN_FORMS',(_DIR_PLUGINS.end($p))."/");
}
include_spip('base/forms');
$GLOBALS['forms_actif_exec'][] = 'donnees_edit';

function autoriser_form_dist($faire, $type='', $id=0, $qui = NULL, $opt = NULL) {
	if ($type=='form')
		if ($faire=='administrer'){
			return ($qui['statut'] == '0minirezo');
		}
		else
			return ($qui['statut'] == '0minirezo');
	return false;
}
function autoriser_form_modifierdonnee_dist($faire, $type, $id_form, $qui, $opt) {
	// un admin dans le back office a toujours le droit de modifier
	if (!$opt['id_donnee']) return false;
	if (($qui['statut'] == '0minirezo')) return true;
	$result = spip_query("SELECT * FROM spip_forms WHERE id_form="._q($id_form));
	if (!$row = spip_fetch_array($result)) return false;
	$dejareponse=Forms_verif_cookie_sondage_utilise($id_form);
	global $auteur_session;
	$id_auteur = $auteur_session ? intval($auteur_session['id_auteur']) : 0;
	$cookie = $_COOKIE[Forms_nom_cookie_form($id_form)];
	if (($row['modifiable'] == 'oui') && $dejareponse) {
		$q = "SELECT id_donnee FROM spip_forms_donnees WHERE id_form="._q($id_form);
		if ($cookie) $q.=" AND (cookie="._q($cookie)." OR id_auteur="._q($id_auteur).")";
		else $q.=" AND id_auteur="._q($id_auteur)." ";
		//si unique, ignorer id_donnee, si pas id_donnee, ne renverra rien
		if ($row['multiple']=='oui' || !_DIR_RESTREINT) $q.=" AND id_donnee="._q($opt['id_donnee']);
		$r=spip_query($q);
		if ($r=spip_fetch_array($r)) return true;
	}
	return false;
}
function autoriser_form_insererdonnee_dist($faire, $type, $id_form, $qui, $opt) {
	// un admin dans le back office a toujours le droit d'inserer
	if (($qui['statut'] == '0minirezo')) return true;
	$result = spip_query("SELECT * FROM spip_forms WHERE id_form="._q($id_form));
	if (!$row = spip_fetch_array($result)) return false;
	if ($row['multiple']=='oui') return true;
	$dejareponse=Forms_verif_cookie_sondage_utilise($id_form);
	if ($dejareponse) return false;
	return true;
}
function autoriser_table_donnee_instituer($faire,$type,$id_donnee,$qui,$opt) {
	if (($qui['statut'] != '0minirezo')
	OR !isset($opt['nouveau_statut'])
	OR ($opt['nouveau_statut']=='prepa')) return false;
	return true;
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