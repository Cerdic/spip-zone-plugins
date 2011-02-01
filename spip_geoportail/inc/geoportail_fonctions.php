<?php
/**
* Plugin SPIP Geoportail
*
* @author:
* Jean-Marc Viglino (ign.fr)
*
* Copyright (c) 2010
* Logiciel distribue sous licence GNU/GPL.
*
* Ajouter un bouton dans la barre de menu
* Vous pouvez redefinir geoportail_menu pour placer le menu ailleurs
*
* Definition des pipes pour l'affichage du formulaire dans les pages SPIP
*  
**/
include_spip('inc/documents');
include_spip('public/assembler');
include_spip('inc/compat_192');
include_spip('public/geoportail_profils');

/** Recalcule sur chaque page (hors cache) 
 - pseudo balise _SPIP_GEOPORATAIL_HASH_ qui doit etre calcule sur chaque page
*/
function geoportail_affichage_final($page)
{	// on regarde rapidement si la page inclus un geoportail
	if (strpos($page, '<!_SPIP_GEOPORTAIL_HASH_!>')===FALSE) return $page;
	
	// Inclure le hash code de maniere dynamique
	charger_fonction('securiser_action','inc');
	$action = calculer_action_auteur('geoportail');
	return str_replace('<!_SPIP_GEOPORTAIL_HASH_!>', $action, $page);
} 

/**
 Fonction pour placer le menu geoportail
*/
function geoportail_menu_dist(&$boutons_admin, $menu, $icone, $libelle)
{	// Surcharge de la fonction
	if (function_exists(geoportail_menu)) geoportail_menu($boutons_admin,$menu, $icone, $libelle);
	else
	{	switch ($menu)
		{	case 'geoservice_tous':
				if ($GLOBALS['meta']['geoportail_service'])
				{	if ($boutons_admin['bando_edition']) $ssmenu = 'bando_edition';
					else $ssmenu = 'naviguer';
					$boutons_admin[$ssmenu]->sousmenu[$menu] =  new Bouton ($icone,$libelle);
				}
			break;
			default:
				if ($boutons_admin['bando_configuration']) $ssmenu ='bando_configuration';
				else $ssmenu = 'configuration';
				$boutons_admin[$ssmenu]->sousmenu[$menu] =  new Bouton ($icone,$libelle);
			break;
		}
	}
}

// Ajout des boutons dans l'interface privee
function geoportail_ajouterBoutons($boutons_admin) 
{	if (autoriser('configurer','geoportail',0)) 
		geoportail_menu_dist ($boutons_admin, 'geoportail_config', _DIR_PLUGIN_GEOPORTAIL."img/geo.png", _T('geoportail:geoportail'));
	if ($GLOBALS['connect_statut'] == "0minirezo") 
		geoportail_menu_dist ($boutons_admin, 'geoservice_tous', "site-24.gif", _T('geoportail:geoservices'));
	return $boutons_admin;
}

// Affichage d'un popup de services (va chercher dans la dscription de la table SPIP)
function geoportail_services($val, $att)
{	$stat = spip_fetch_array(spip_query("DESCRIBE spip_geoservices $att"));
	$reg = "^enum\('(.*)'\)$";
	$stat = ereg_replace($reg,'\1',$stat['Type']);
	$stat = split("[']?,[']?",$stat);
	$rep = "";
	foreach ($stat as $s) $rep .= "<option value='$s' ".($val==$s?"SELECTED":"").">$s</option>";
	return $rep;
}

// Fonction de gestion des geoservices
function geoportail_table_geoservices ($id_rubrique=0)
{ 	global $connect_statut, $connect_toutes_rubriques, $couleur_foncee, $couleur_claire;

	$contexte = array(
		'id_rubrique'=>$id_rubrique,
		'couleur_foncee'=>$couleur_foncee,
		'couleur_claire'=>$couleur_claire
	);
	if (autoriser ("modifier", "geoservice", 0, NULL, array('id_rubrique'=>$id_rubrique))) $contexte['modifier'] = " ";

	$fond = recuperer_fond ('fonds/geoservices_table',$contexte);
	if ($fond)
	echo "\n"
//		. bandeau_titre_boite2("<b>&nbsp;"._T("geoportail:geoservices")."</b>", "site-24.gif", $couleur_claire, 'black', false)
		. debut_cadre("trait-couleur", "site-24.gif", "", "<b>&nbsp;"._T("geoportail:geoservices")."</b>", $couleur_claire, 'black', false)
		. $fond
		. fin_cadre(true)
		. "\n";
	elseif ($id_rubrique==0) echo "<p> </p>";

	if (autoriser ("creer", "geoservice", 0, NULL, array('id_rubrique'=>$id_rubrique))) 
	{	echo icone_inline(_T('geoportail:icone_ajouter_service'), 
					generer_url_ecrire('geoservice_edit',"id_rubrique=$id_rubrique"), 
					"site-24.gif",
					"creer.gif",
					"right", 
					false);
	}
}

/**
  Affichage du formulaire pour la saisie de coordonnees
*/
function geoportail_affiche_milieu($flux) 
{	$exec =  $flux['args']['exec'];

	// Le contexte
	$contexte = array(
					'class'			=> 'carto',
					'deplier'		=> _request('zone') ? ' ':'',
					'lon'			=> _request('lon'),
					'lat'			=> _request('lat'),
					'zoom'			=> _request('zoom'),
					'zone'			=> _request('zone')
				);
	if ($exec == 'articles' && $GLOBALS['meta']['geoportail_geoarticle']) 
	{	$contexte['id_objet'] = $flux['args']['id_article'];
		if ($GLOBALS['meta']['geoportail_geodocument']) $contexte['id_article'] = $flux['args']['id_article'];
		$contexte['objet'] = 'article';
	}
	else if ($exec == 'auteur_infos' && $GLOBALS['meta']['geoportail_geoauteur']) 
	{	$contexte['id_objet'] = $flux['args']['id_auteur'];
		$contexte['objet'] = 'auteur';
	}
	else if ($exec == 'naviguer' && $GLOBALS['meta']['geoportail_georubrique']) 
	{	$contexte['id_objet'] = $flux['args']['id_rubrique'];
		$contexte['objet'] = 'rubrique';
	}
	else if ($exec == 'mots_edit' && $GLOBALS['meta']['geoportail_geomot']) 
	{	$contexte['id_objet'] = $flux['args']['id_mot'];
		$contexte['objet'] = 'mot';
	}
	else if ($exec == 'breves_voir' && $GLOBALS['meta']['geoportail_geobreve']) 
	{	$contexte['id_objet'] = $flux['args']['id_breve'];
		$contexte['objet'] = 'breve';
	}
	else if ($exec == 'sites' && $GLOBALS['meta']['geoportail_geosyndic']) 
	{	$contexte['id_objet'] = $flux['args']['id_syndic'];
		$contexte['objet'] = 'syndic';
	}
	if ($contexte['id_objet'])
	{	$flux['data'] .= 
		debut_cadre_enfonce(_DIR_PLUGIN_GEOPORTAIL."img/punaise.png", true, "", 
			"<a id=carto href=\"javascript:geoportail_formulaire_show()\">"
			."<img class=carto_show src='".find_in_path('images/deplierhaut.gif')."' title='"._T('spip:info_deplier')."' />"
			."<img class=carto_show style='display:none;' src='".find_in_path('images/deplierbas.gif')."' title='"._T('spip:info_deplier')."' />"
			."</a>"
			._T("geoportail:geoposition")
			." <img class=carto_patience style='display:none; vertical-align:top;' src='".find_in_path('images/searching.gif')."' />"
			)
			. recuperer_fond ('formulaires/geoportail_formulaire',$contexte)
			. fin_cadre_enfonce(true);
	}
	return $flux;
}

/** seulement en SPIP v.2 
	idem pour les documents : afficher le lien 
	Recherche automatique d'un georef dans le ficher (si cas geoportail_geodocument_auto)
*/
function geoportail_afficher_contenu_objet($flux)
{	
	if ($flux['args']['type']=='case_document' && $GLOBALS['meta']['geoportail_geodocument'])
	{	// permettre la modification du document (documents_edit)
		$id_article = _request('id_article');
		if (!$id_article) $id_rubrique = _request('id_rubrique');
		$id_document = $flux['args']['id'];
		if (autoriser('modifier','document', $id_document))
		{	// Recherche du document
			$document = spip_fetch_array(spip_query("SELECT * FROM spip_documents WHERE id_document = ".$id_document));
			$titre = $document['titre'];
			$fichier = $document['fichier'];
			$descriptif = $document['descriptif'];
			$extension = $document['extension'];
			if (!$extension)
			{	$result = spip_fetch_array(spip_query("SELECT * FROM spip_types_documents WHERE id_type=".$document['id_type']));
				$extension = $result['extensions'];
			}
			// Georeferencement de l'objet
			include_spip('public/geoportail_boucles');
			$info =_T('geoportail:georef');
			$result = spip_fetch_array(spip_query("SELECT * FROM spip_geopositions WHERE id_objet=$id_document AND objet='document'"));
			if ($result)
			{	$lon=$result['lon'];
				$lat=$result['lat'];
				$info = "(".geoportail_longitude($lon,true).", ".geoportail_latitude($lat,true).")";
			}
			// Rechercher le georeferencement sur le fichier
			else if ($GLOBALS['meta']['geoportail_geodocument_auto'])
			{	include_spip('inc/geoupload');
				if (geoportail_get_coord(_DIR_IMG.$fichier,$extension,$lon,$lat))
					$info = "(".geoportail_longitude($lon,true).", ".geoportail_latitude($lat,true).")";
				$id_position = sql_insert("spip_geopositions",
						"(id_objet, objet, lon, lat, zoom, zone)",
						"($id_document, 'document', $lon, $lat, 10, 'FXX')"
					);
			}

			$flux['data'] .= '<a style="display:block; text-align:center;" href="'
			.generer_url_ecrire("geodocuments_edit","id_article=$id_article&id_rubrique=$id_rubrique&id_document=$id_document",true)
			.'">'.$info.'</a>';
		}
	}
	return $flux;
}

/** Ne pas exporter le RGC en SPIP v2 ! */
function geoportail_lister_tables_noexport($liste)
{
	$liste[] = 'spip_georgc';
	return $liste;
}

?>