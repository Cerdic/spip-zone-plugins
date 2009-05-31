<?php
/*
 * forms
 * Gestion de formulaires editables dynamiques
 *
 * Auteurs :
 * Antoine Pitrou
 * Cedric Morin
 * Renato Formato
 * (c) 2005-2009 - Distribue sous licence GNU/GPL
 *
 */
if (!defined("_ECRIRE_INC_VERSION")) return;

// autorisation des boutons
function autoriser_forms_liste_bouton($faire, $type='', $id=0, $qui = NULL, $opt = NULL){
	$actif = unserialize($GLOBALS['meta']['forms_et_tables']);
	$actif = isset($actif['form_actif']) AND $actif['form_actif'];
	return ($actif AND $qui['statut'] == '0minirezo'); // tous les admin, restreint ou non
}
function autoriser_sondages_liste_bouton($faire, $type='', $id=0, $qui = NULL, $opt = NULL){
	$actif = unserialize($GLOBALS['meta']['forms_et_tables']);
	$actif = isset($actif['sondage_actif']) AND $actif['sondage_actif'];
	return ($actif AND $qui['statut'] == '0minirezo'); // tous les admin, restreint ou non
}
function autoriser_tables_liste_bouton($faire, $type='', $id=0, $qui = NULL, $opt = NULL){
	$actif = unserialize($GLOBALS['meta']['forms_et_tables']);
	$actif = isset($actif['table_actif']) AND $actif['table_actif'];
	return ($actif AND $qui['statut'] == '0minirezo'); // tous les admin, restreint ou non
}

function autoriser_form_dist($faire, $type='', $id=0, $qui = NULL, $opt = NULL) {
	if ($type=='form' OR $type=='donnee'){
		if ($faire=='administrer'){
			return ($qui['statut'] == '0minirezo'); // tous les admin, restreint ou non
		}
	}
	return
		$qui['statut'] == '0minirezo'
		AND !$qui['restreint'];
}
// en cas d'appel avec le nom de la table (crayons)
function autoriser_forms_donnee_dist($faire,$type,$id_donnee,$qui,$opt){
	return autoriser_donnee_dist($faire,"donnee",$id_donnee,$qui,$opt);
}
function autoriser_donnee_dist($faire,$type,$id_donnee,$qui,$opt){
	static $types = array();
	static $opts = array();
	if (!isset($opt['id_form']) OR !isset($opt['statut'])){
		if (!isset($opts[$id_donnee])){
			$opts[$id_donnee] = array('id_form'=>0,'statut'=>'');
			if ($row = sql_fetsel("id_form,statut","spip_forms_donnees","id_donnee=".intval($id_donnee)))
				$opts[$id_donnee] = $row;
			$opts[$id_donnee] = is_array($opt)?array_merge($opts[$id_donnee],$opt):$opts[$id_donnee];
		}
		$opt = $opts[$id_donnee];
	}
	$id_form = $opt['id_form'];
	if (!isset($opt['type_form'])){
		if (!isset($types[$id_form])){
			if (!$row = sql_fetsel("type_form","spip_forms","id_form=".intval($id_form)))
				return false;
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
	if (_DEBUG_AUTORISER) spip_log("autoriser_form_donnee_dist delegue a $f($faire,$type,$id_donnee): ".($a?'OK':'niet'));
	return $a;
}
function autoriser_form_donnee_voir_dist($faire, $type, $id_donnee, $qui, $opt) {
	// un admin dans le back office a toujours le droit de voir
	if (($qui['statut'] == '0minirezo')) return true;
	return false;
}
function autoriser_table_donnee_voir_dist($faire, $type, $id_donnee, $qui, $opt) {
	return autoriser_form_donnee_voir_dist($faire, $type, $id_donnee, $qui, $opt);
}

function autoriser_form_donnee_modifier_dist($faire, $type, $id_donnee, $qui, $opt) {
	if (!intval($id_donnee)) return false;
	if (!isset($opt['id_form']) OR !$id_form = $opt['id_form']) return false;
	// un admin dans le back office a toujours le droit de modifier
	if (($qui['statut'] == '0minirezo')) return true;
	if (!$row = sql_fetsel("*","spip_forms","id_form=".intval($id_form)))
		return false;
	include_spip('inc/forms');
	$dejareponse=forms_verif_cookie_sondage_utilise($id_form);
	global $auteur_session;
	$id_auteur = $auteur_session ? intval($auteur_session['id_auteur']) : 0;
	$cookie = $_COOKIE[forms_nom_cookie_form($id_form)];
	if (($row['modifiable'] == 'oui') && $dejareponse) {
		$where_cookie = "";
		if ($cookie) { 
			$where_cookie = "(cookie=".sql_quote($cookie). ($id_auteur?" OR id_auteur=".intval($id_auteur):" AND id_auteur=0").")";
		}
		else if ($id_auteur)
				$where_cookie = "id_auteur=".intval($id_auteur);
			else
				return false;
		//si unique, ignorer id_donnee, si pas id_donnee, ne renverra rien
		if ($row['multiple']=='oui' || !_DIR_RESTREINT)
		  $where_cookie .= " AND id_donnee=".intval($id_donnee);
		if (sql_fetsel("id_donnee","spip_forms_donnees","id_form=".intval($id_form)." AND $where_cookie"))
			return true;
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
	if (!$row = sql_fetsel("multiple","spip_forms","id_form=".intval($id_form)))
		return false;
	if ($row['multiple']=='oui') return true;
	$dejareponse=forms_verif_cookie_sondage_utilise($id_form);
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

?>