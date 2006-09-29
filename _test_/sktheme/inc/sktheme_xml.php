<?php
  // ---------------------------------------------------------------------
  //
  // Sktheme : manage themes under SPIP (squelettes + habillages)
  //
  // Copyright (c) 2006 - Jerome RICHARD
  //
  // This program is free software; you can redistribute it and/or modify
  // it under the terms of the GNU General Public License as published by
  // the Free Software Foundation; either version 2 of the License, or
  // (at your option) any later version.
  //
  // You should have received a copy of the GNU General Public License
  // along with this program; 
  //
  // ---------------------------------------------------------------------
if (!defined("_ECRIRE_INC_VERSION")) return;

function sktheme_xml_get_infos($xml_dir,$xml_type){

  include_spip('inc/plugin'); 

  $ret = array();
  if ((@file_exists($xml_dir))&&(is_dir($xml_dir))){
    if (@file_exists($xml_dir."/$xml_type.xml")) {
      lire_fichier($xml_dir."/$xml_type.xml", $texte);
      $arbre = parse_plugin_xml($texte);
      if (!isset($arbre[$xml_type])&&is_array($arbre[$xml_type]))
	$arbre = array('erreur' => array(_T('erreur_plugin_fichier_def_incorrect')." : $xml_dir/$xml_type.xml"));
    }
    else {
      // pour arriver ici on l'a vraiment cherche...
      //debug echo "error: erreur_plugin_fichier_def_absent $xml_dir/$xml_type.xml<br>";
      $arbre = array('erreur' => array(_T('erreur_plugin_fichier_def_absent')." : $xml_dir/$xml_type.xml"));
    }

    sktheme_xml_verifie_conformite($xml_dir,$xml_type,$arbre);
		
    $ret['nom'] = applatit_arbre($arbre['nom']);
    $ret['version'] = trim(end($arbre['version']));
    if (isset($arbre['auteur']))
      $ret['auteur'] = applatit_arbre($arbre['auteur']);
    if (isset($arbre['description']))
      $ret['description'] = applatit_arbre($arbre['description']);
    if (isset($arbre['lien']))
      $ret['lien'] = join(' ',$arbre['lien']);
    if (isset($arbre['etat']))
      $ret['etat'] = trim(end($arbre['etat']));
    if (isset($arbre['options']))
      $ret['options'] = $arbre['options'];
    if (isset($arbre['fonctions']))
      $ret['fonctions'] = $arbre['fonctions'];
    $ret['prefix'] = $arbre['prefix'];
    if (isset($arbre['pipeline']))
      $ret['pipeline'] = $arbre['pipeline'];
    if (isset($arbre['erreur']))
      $ret['erreur'] = $arbre['erreur'];
    if (isset($arbre['squelette'])) {
      $ret['squelette'] = $arbre['squelette'];
    }

    // Compute extra values
    $etat = 'dev';
    if (isset($ret['etat']))
      $etat = $ret['etat'];
    switch ($etat) {
    case 'experimental':
      $puce = 'puce-rouge.gif';
      $titre_etat = _T('plugin_etat_experimental');
      break;
    case 'test':
      $puce = 'puce-orange.gif';
      $titre_etat = _T('plugin_etat_test');
      break;
    case 'stable':
      $puce = 'puce-verte.gif';
      $titre_etat = _T('plugin_etat_stable');
      break;
    default:
      $puce = 'puce-poubelle.gif';
      $titre_etat = _T('plugin_etat_developpement');
      break;
    }
    $ret['extra_puce']       = $puce;
    $ret['extra_img_puce']   = "<img src='"._DIR_IMG_PACK."$puce' width='9' height='9' style='border:0;' alt=\"$titre_etat\" title=\"$titre_etat\" />&nbsp;";
    $ret['extra_titre_etat'] = $titre_etat;
  }
  return $ret;
}

function sktheme_xml_verifie_conformite($xml_dir,$xml_type="plugin",&$arbre){
	$silence = false;
	if (isset($arbre[$xml_type])&&is_array($arbre[$xml_type]))
		$arbre = end($arbre[$xml_type]); // derniere def xml_type
	else{
		$arbre = array('erreur' => array(_T('erreur_plugin_tag_plugin_absent')." : $xml_dir/plugin.xml"));
		$silence = true;
	}
  // verification de la conformite du plugin avec quelques
  // precautions elementaires
  if (!isset($arbre['nom'])){
  	if (!$silence)
			$arbre['erreur'][] = _T('erreur_plugin_nom_manquant');
		$arbre['nom'] = array("");
	}
  if (!isset($arbre['version'])){
  	if (!$silence)
			$arbre['erreur'][] = _T('erreur_plugin_version_manquant');
		$arbre['version'] = array("");
	}
  if (!isset($arbre['prefix'])){
  	if (!$silence)
			$arbre['erreur'][] = _T('erreur_plugin_prefix_manquant');
		$arbre['prefix'] = array("");
	}
	else{
		$prefix = "";
		$prefix = trim(end($arbre['prefix']));
		if (isset($arbre['etat'])){
			$etat = trim(end($arbre['etat']));
			if (!preg_match(',^(dev|experimental|test|stable)$,',$etat))
				$arbre['erreur'][] = _T('erreur_plugin_etat_inconnu')." : $etat";
		}
		if (isset($arbre['options'])){
			foreach($arbre['options'] as $optfile){
				$optfile = trim($optfile);
				if (!@is_readable(_DIR_PLUGINS."$xml_dir/$optfile"))
  				if (!$silence)
						$arbre['erreur'][] = _T('erreur_plugin_fichier_absent')." : $optfile";
			}
		}
		if (isset($arbre['fonctions'])){
			foreach($arbre['fonctions'] as $optfile){
				$optfile = trim($optfile);
				if (!@is_readable(_DIR_PLUGINS."$xml_dir/$optfile"))
  				if (!$silence)
						$arbre['erreur'][] = _T('erreur_plugin_fichier_absent')." : $optfile";
			}
		}
		$fonctions = array();
		if (isset($arbre['fonctions']))
			$fonctions = $arbres['fonctions'];
	  $liste_methodes_reservees = array('__construct','__destruct','plugin','install','uninstall',strtolower($prefix));
		if (is_array($arbre['pipeline'])){
			foreach($arbre['pipeline'] as $pipe){
				$nom = trim(end($pipe['nom']));
				if (isset($pipe['action']))
					$action = trim(end($pipe['action']));
				else
					$action = $nom;
				// verif que la methode a un nom autorise
				if (in_array(strtolower($action),$liste_methodes_reservees)){
					if (!$silence)
						$arbre['erreur'][] = _T("erreur_plugin_nom_fonction_interdit")." : $action";
				}
				else{
					// verif que le fichier de def est bien present
					if (isset($pipe['inclure'])){
						$inclure = _DIR_PLUGINS."$xml_dir/".end($pipe['inclure']);
						if (!@is_readable($inclure))
		  				if (!$silence)
								$arbre['erreur'][] = _T('erreur_plugin_fichier_absent')." : $inclure";
					}
				}
			}
		}
	}
}

?>