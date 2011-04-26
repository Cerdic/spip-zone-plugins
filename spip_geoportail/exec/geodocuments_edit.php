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
* Formulaire pour l'edition de la position d'un document
*
**/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');
include_spip('inc/article_select');
include_spip('inc/documents');
include_spip('inc/geoupload');

function exec_geodocuments_edit_dist()
{
	$id_document = _request('id_document');
	$id_article = _request('id_article');
	$id_rubrique = _request('id_rubrique');

	$document = spip_fetch_array(spip_query("SELECT * FROM spip_documents WHERE id_document = " . intval($id_document)));
	$titre = $document['titre'];
	$fichier = $document['fichier'];
	$descriptif = $document['descriptif'];
	$extension = $document['extension'];
	$distant = $document['distant'];
	if (!$extension)
	{	$result = spip_fetch_array(spip_query("SELECT * FROM spip_types_documents WHERE id_type=".$document['id_type']));
		$extension = $result['extension'];
		$fichier = "../".$fichier;
	}
	
	$commencer_page = charger_fonction('commencer_page', 'inc');
	pipeline('exec_init',array('args'=>array('exec'=>'geodocuments_edit','id_document'=>$id_document),'data'=>''));

	echo $commencer_page(_T('spip:info_document', array('titre' => $titre)), "naviguer", "documents", $id_document);
	
	echo debut_gauche("",true);
	echo debut_boite_info(true)
		."\n<div style='font-weight: bold; text-align: center' class='verdana1 spip_xx-small'>"
		._T('geoportail:info_numero_document')
		."<br /><span class='spip_xx-large'>"
		.$id_document
		.'</span></div>'
		.fin_boite_info(true);

	// Ne pas afficher les liens
	$GLOBALS['doublons_documents_inclus'][]=$id_document;
	echo afficher_case_document ($id_document, $id_article?$id_article:$id_rubrique, 'geodocuments_edit', $id_article?'article':'rubrique', false);
/*	$doc[]=$document;
	echo documenter_boucle ($doc, $id_article?$id_article:$id_rubrique, '', $aut, $id_article?'article':'rubrique', false);
*/
	echo pipeline('affiche_gauche',array('args'=>array('exec'=>'geodocuments_edit','id_document'=>$id_document),'data'=>''));
	if ($id_article) 
	{	$rac = icone_horizontale(_T('ecrire:icone_retour_article'), generer_url_ecrire("articles","id_article=$id_article#access-l"), "article-24.gif", "rien.gif",false);
		$rac .= recuperer_fond ('fonds/geoportail_article_docs', array('id_article' => $id_article) );
	}
	if ($id_rubrique) $rac = icone_horizontale(_T('icone_retour'), generer_url_ecrire("naviguer","id_rubrique=$id_rubrique#access-f"), "rubrique-24.gif", "rien.gif",false);
	echo bloc_des_raccourcis($rac);
	echo creer_colonne_droite("",true);
	echo pipeline('affiche_droite',array('args'=>array('exec'=>'geodocuments_edit','id_document'=>$id_document),'data'=>''));
	echo debut_droite("",true);

	// Ooops
	echo debut_cadre_relief("doc-24.gif",true,'','','doc-voir','');
	echo gros_titre(_T('spip:info_document')." ".$id_document." : ".$titre,'',false)
		. "<div class='nettoyeur'></div>"
	;
	if ($descriptif) {
		echo "<div style='border: 1px dashed #aaaaaa; ' class='verdana1 spip_small'>"
		. propre($descriptif)
		. "&nbsp; "
		. "</div>";
	}

	if (autoriser('modifier', 'document', $id_document))
	{	if ($GLOBALS['meta']['geoportail_geodocument'])
		{	$contexte = array(
							'id_objet'		=> $id_document,
							'objet'			=> 'document',
							'class'			=> 'carto',
							'deplier'		=> ' ',
							'lon'			=> _request('lon'),
							'lat'			=> _request('lat'),
							'zoom'			=> _request('zoom'),
							'zone'			=> _request('zone')
						);
			// Rechercher le georef dans le fichier (s'il existe)...
			if ($distant != 'oui' && geoportail_get_coord(_DIR_IMG.$fichier,$extension,$lon,$lat))
				$contexte['pos_fichier'] = "$lon,$lat,12";
			
			// Recuperer la position de la rubrique ou de l'article pere
			if ($id_article)
			{   $a = spip_fetch_array(spip_query("SELECT * FROM spip_geopositions WHERE id_objet=$id_article AND objet='article'"));
			    if ($a) $contexte['pos_article'] = $a['lon'].",".$a['lat'].",".$a['zoom'];
			}
			else if ($id_rubrique)
			{   $a = spip_fetch_array(spip_query("SELECT * FROM spip_geopositions WHERE id_objet=$id_rubrique AND objet='rubrique'"));
			    if ($a) $contexte['pos_article'] = $a['lon'].",".$a['lat'].",".$a['zoom'];
			}
				
			// Afficher le formulaire
			echo "<br style='clear:both'>"
				.debut_cadre_enfonce(_DIR_PLUGIN_GEOPORTAIL."img/punaise.png", true, "",
				"<a id=carto href=\"javascript:geoportail_formulaire_show()\">"
				."<img class=carto_show src='".find_in_path('images/deplierhaut.gif')."' />"
				."<img class=carto_show style='display:none;' src='".find_in_path('images/deplierbas.gif')."' />"
				."</a>"
				._T("geoportail:geoposition")
				)
				. recuperer_fond ('formulaires/geoportail_formulaire',$contexte)
				. fin_cadre_enfonce(true);
		}
	}
	echo fin_cadre_relief(true);
	echo pipeline('affiche_milieu',array('args'=>array('exec'=>'geodocuments_edit','id_document'=>$id_document),'data'=>''));

	echo fin_gauche(), fin_page();
}
