<?php
	/**Plugin Inscription 2 avec CFG **/
	if (!defined("_ECRIRE_INC_VERSION")) return;
	include_spip('cfg_options');
	
	// a chaque validation de cfg, verifier l'etat de la table spip_auteurs_elargis	
	if(_request('exec')=='cfg' and _request('cfg')=='inscription2'){
		include_spip('inscription2_mes_fonctions');
		inscription2_verifier_tables();
	}
	global $tables_principales;
	$table_nom = "spip_auteurs_elargis";
	foreach(lire_config('inscription2') as $cle => $val) {
		$cle = ereg_replace("_(fiche|table).*", "", $cle);
		if($val!='' and $cle != 'nom' and $cle != 'email' and $cle != 'username' and $cle != 'statut_rel'  and $cle != 'accesrestreint' and !ereg("^(domaine|categories|zone|newsletter).*$", $cle) ){
			if($cle == 'naissance' )
				$spip_auteurs_elargis[$cle] = "DATE DEFAULT '0000-00-00' NOT NULL";
			else
				$spip_auteurs_elargis[$cle] = "text NOT NULL ";
		}
	}
	
	$spip_auteurs_elargis['id_auteur'] = "bigint(21) NOT NULL";
	$spip_auteurs_elargis_key = array("PRIMARY KEY"	=> "id", 'KEY id_auteur' => 'id_auteur');
	
	$spip_pays['id'] = "bigint(21) NOT NULL";
	$spip_pays['pays'] = "text NOT NULL ";
	$spip_pays_key = array("PRIMARY KEY"	=> "id");
	
	$tables_principales['`spip_auteurs_elargis`']  =	array('field' => &$spip_auteurs_elargis, 'key' => &$spip_auteurs_elargis_key);
	$tables_principales['`spip_pays`']  =	array('field' => &$spip_pays, 'key' => &$spip_pays_key);

	
# autoriser les visiteurs a modifier leurs infos
#define ('_DEBUG_AUTORISER', true);
if (!function_exists('autoriser_spip_auteurs_elargis')) {
function autoriser_spip_auteurs_elargis($faire, $type, $id, $qui, $opt) {
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

?>