<?php

include_spip('inc/texte');
include_spip('inc/presentation');

function auteurs_complets_install(){
	auteurs_complets_verifier_base();
}

function auteurs_complets_uninstall(){
}

function auteurs_complets_verifier_base(){
	$version_base = 0.03;
	$current_version = 0.0;

	if (   (!isset($GLOBALS['meta']['auteurs_complets_base_version']) )
		&& (($current_version = $GLOBALS['meta']['auteurs_complets_base_version'])==$version_base))
	return;
	
	// ajout des champs additionnels a la table spip_auteurs
	// si pas deja existant

	if ($current_version==0.0){
		include_spip('base/create');
		include_spip('base/abstract_sql');
		creer_base();
		$desc = spip_abstract_showtable("spip_auteurs", '', true);
		if (!isset($desc['field']['telephone'])){
			spip_query("ALTER TABLE spip_auteurs ADD `telephone` TEXT NOT NULL AFTER `email`");}
		if (!isset($desc['field']['fax'])){
			spip_query("ALTER TABLE spip_auteurs ADD `fax` TEXT NOT NULL AFTER `telephone`");}
		if (!isset($desc['field']['skype'])){
			spip_query("ALTER TABLE spip_auteurs ADD `skype` TEXT NOT NULL AFTER `fax`");}
		if (!isset($desc['field']['adresse'])){
			spip_query("ALTER TABLE spip_auteurs ADD `adresse` TEXT NOT NULL AFTER `skype`");}
		if (!isset($desc['field']['codepostal'])){
			spip_query("ALTER TABLE spip_auteurs ADD `codepostal` TEXT NOT NULL AFTER `adresse`");}
		if (!isset($desc['field']['ville'])){
			spip_query("ALTER TABLE spip_auteurs ADD `ville` TEXT NOT NULL AFTER `codepostal`");}
		if (!isset($desc['field']['pays'])){
			spip_query("ALTER TABLE spip_auteurs ADD `pays` TEXT NOT NULL AFTER `ville`");}
		if (!isset($desc['field']['latitude'])){
			spip_query("ALTER TABLE spip_auteurs ADD `latitude` TEXT NOT NULL AFTER `pays`");}
		if (!isset($desc['field']['longitude'])){
			spip_query("ALTER TABLE spip_auteurs ADD `longitude` TEXT NOT NULL AFTER `latitude`");}
			ecrire_meta('auteurs_complets_base_version',$current_version=$version_base);
	}
	if ($current_version<0.03){
		$desc = spip_abstract_showtable("spip_auteurs", '', true);
		if (!isset($desc['field']['pays'])){
			spip_query("ALTER TABLE spip_auteurs ADD `pays` TEXT NOT NULL AFTER `ville`");}
		if (!isset($desc['field']['skype'])){
			spip_query("ALTER TABLE spip_auteurs ADD `skype` TEXT NOT NULL AFTER `fax`");}
		ecrire_meta('auteurs_complets_base_version',$current_version=0.03);
	}
	ecrire_metas();
}

function auteurs_complets_ajouts()
{
	global
	$connect_id_auteur,
	$id_auteur;

if ($id_auteur) {
$auteur = spip_fetch_array(spip_query("SELECT * FROM spip_auteurs WHERE id_auteur=$id_auteur"));
if (!$auteur) exit;
}

	$telephone=$auteur['telephone'];
	$fax=$auteur['fax'];
	$adresse=$auteur['adresse'];
	$codepostal=$auteur['codepostal'];
	$ville=$auteur['ville'];
	$pays=$auteur['pays'];
	$latitude=$auteur['latitude'];
	$longitude=$auteur["longitude"];
	$skype = $auteur["skype"];

	echo "<br />";
	gros_titre(_T('auteurscomplets:coordonnees_sup'));
	echo "<br />";
	echo "<table width='100%' cellpadding='0' border='0' cellspacing='0'>";
	
	echo "<tr>";

	echo "<td valign='top' width='100%'>";
	
	if (strlen($telephone) > 2){ echo "<div>"._T('auteurscomplets:affiche_telephone')." $telephone </div>";}
	if (strlen($fax) > 2){ echo "<div>"._T('auteurscomplets:affiche_fax')." $fax </div>";}
	if (strlen($skype) > 2){ echo "<div>"._T('auteurscomplets:affiche_skype')." $skype </div><hr />";}
	if ((strlen($latitude) > 2) || (strlen($longitude) >2))  echo "<div><b>"._T('auteurscomplets:affiche_coordonnees_geo')."</b></div>";
	if (strlen($latitude) > 2){ echo "<div>"._T('auteurscomplets:affiche_latitude')." $latitude </div>";}
	if (strlen($longitude) > 2){ echo "<div>"._T('auteurscomplets:affiche_longitude')." $longitude </div><hr />";}
	if ((strlen($adresse) > 2) || (strlen($codepostal) >2) || (strlen($ville) > 2) || (strlen($pays) > 2))  echo "<div><b>"._T('auteurscomplets:affiche_adresse')."</b></div>";
	if (strlen($adresse) > 2) echo "<div> $adresse </div>";
	if (strlen($codepostal) > 2) echo "<div> $codepostal ";
	if (strlen($ville) > 2) echo "$ville </div>";
	if (strlen($pays) > 2){ echo "<div> $pays </div>";}
	echo "</td></tr></table>";
}
?>
