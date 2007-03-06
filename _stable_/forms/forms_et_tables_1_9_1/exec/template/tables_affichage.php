<?php
/*
 * forms
 * Gestion de formulaires editables dynamiques
 *
 * Auteurs :
 * Antoine Pitrou
 * Cedric Morin
 * Renato
 * © 2005,2006 - Distribue sous licence GNU/GPL
 *
 */

include_spip('inc/forms');

function forms_inserer_crayons($out){
	$out = pipeline('affichage_final', "</head>".$out);
	$out = str_replace("</head>","",$out);
	return $out;
}
function afficher_tables_tous($type_form, $titre_page, $titre_type, $titre_creer){
	global $spip_lang_right;
  include_spip("inc/presentation");
	include_spip('public/assembler');
	if (!include_spip('inc/autoriser'))
		include_spip('inc/autoriser_compat');

  _Forms_install();
	
	debut_page($titre_page, "documents", "forms");
	debut_gauche();
	debut_boite_info();
	echo _T("forms:boite_info");
	echo "<p>";
	fin_boite_info();
	
	creer_colonne_droite();
	if (include_spip('inc/snippets'))
		echo boite_snippets($titre_type,_DIR_PLUGIN_FORMS."img_pack/$type_form-24.gif",'forms','forms');
	
	debut_droite();

	if ( _request('exec')=='tables_tous'
		&& (_request('var_mode')=='dev' OR _OUTILS_DEVELOPPEURS)) {
		$res = spip_query("SELECT type_form FROM spip_forms GROUP BY type_form ORDER BY type_form");
		while ($row = spip_fetch_array($res)){
			$prefix = forms_prefixi18n($row['type_form']);
			$contexte = array('type_form'=>$row['type_form'],'titre_liste'=>_T("$prefix:toutes_tables")." [".$row['type_form']."]",'couleur_claire'=>$GLOBALS['couleur_claire'],'couleur_foncee'=>$GLOBALS['couleur_foncee']);
			echo recuperer_fond("exec/template/tables_tous",$contexte);
			if (autoriser('creer','form')) {
			  $icone = find_in_path("img_pack/".($row['type_form']?$row['type_form']:'form')."-24.png");
			  if (!$icone)
			  	$icone = "../"._DIR_PLUGIN_FORMS."img_pack/table-24.png";
				echo "<div style='float:right'>";
				$link=generer_url_ecrire('forms_edit', "new=oui&type_form=".$row['type_form']);
				$link=parametre_url($link,'retour',str_replace('&amp;', '&', self()));
				icone(_T("$prefix:icone_creer_table"), $link, $icone, "creer.gif");
				echo "</div>";
			}
		}
	}
	else {
		$prefix = forms_prefixi18n($type_form);
		$contexte = array('type_form'=>$type_form,'titre_liste'=>_T("$prefix:toutes_tables"),'couleur_claire'=>$GLOBALS['couleur_claire'],'couleur_foncee'=>$GLOBALS['couleur_foncee']);
		echo recuperer_fond("exec/template/tables_tous",$contexte);
		
		if (autoriser('creer','form')) {
		  $icone = find_in_path("img_pack/".($type_form?$type_form:'form')."-24.png");
		  if (!$icone)
		  	$icone = "../"._DIR_PLUGIN_FORMS."img_pack/table-24.png";
			echo "<div align='right'>";
			$link=generer_url_ecrire('forms_edit', "new=oui&type_form=$type_form");
			$link=parametre_url($link,'retour',str_replace('&amp;', '&', self()));
			icone(_T("$prefix:icone_creer_table"), $link, $icone, "creer.gif");
			echo "</div>";
		}
	}
	
	if ($GLOBALS['spip_version_code']>=1.9203)
		echo fin_gauche();
	echo fin_page();
}


function affichage_donnees_tous_corps($type_form,$id_form,$retour=false, $titre_page=false, $contexte = array()){
	global $spip_lang_right,$spip_lang_left;
	$out = "";
	if (!$id_form = intval($id_form)) return $out;
	if ($titre_page===false){
		$res = spip_query("SELECT titre FROM spip_forms WHERE id_form="._q($id_form));
		$row=spip_fetch_array($res);
		$titre_page = $row['titre'];
	}
	
	$prefix = forms_prefixi18n($type_form);
  $icone = find_in_path("img_pack/$type_form-24.png");
  if (!$icone)
  	$icone = "../"._DIR_PLUGIN_FORMS."img_pack/donnees-24.png";
	$out .=  "<table><tr><td>";
	if ($retour){
		$out .=  "<div style='float:$spip_lang_left;'>";
		$out .=  icone(_T('icone_retour'), urldecode($retour), $icone, "rien.gif","",false);
		$out .=  "</div>";
	}
	
	if (autoriser('administrer','form',$id_form)) {
		$retour = urlencode(self());
		
		$url_edit = generer_url_ecrire('donnees_edit',"id_form=$id_form&retour=$retour");
		$out .=  "<div style='float:$spip_lang_left;'>";
		$out .=  icone(_T("$prefix:icone_ajouter_donnees"), $url_edit, $icone, "creer.gif","",false);
		$out .=  "</div>";
		
		$out .=  "<div style='float:$spip_lang_left;'>";
		$out .=  icone(_T("$prefix:telecharger_reponses"),
			generer_url_ecrire("forms_telecharger","id_form=$id_form&retour=$retour"), "../"._DIR_PLUGIN_FORMS. "img_pack/donnees-exporter-24.png", "rien.gif","",false);
		$out .=  "</div>";
		if (defined('_DIR_PLUGIN_CSVIMPORT')){
			$out .=  "<div style='float:$spip_lang_left;'>";
			$out .=  icone(_T("$prefix:importer_donnees_csv"),
				generer_url_ecrire("csvimport_import","id_form=$id_form&retour=$retour"), "../"._DIR_PLUGIN_FORMS. "img_pack/donnees-importer-24.png", "rien.gif","",false);
			$out .=  "</div>";
		}
	}
	
	$out .=  '<div style="clear:left;text-align:center">';
	$out .=  gros_titre($titre_page,'',false);
	$out .=  '</div>';
	
	$contexte = array_merge($contexte,
		array('id_form'=>$id_form,
		'titre_liste'=>$titre_page,
		'aucune_reponse'=>_T("$prefix:aucune_reponse"),
		'couleur_claire'=>$GLOBALS['couleur_claire'],'couleur_foncee'=>$GLOBALS['couleur_foncee'],
		'statuts' => array('prepa','prop','propose','publie','refuse') )
	);
	$out .=  recuperer_fond("exec/template/donnees_tous",$contexte);
	$out = forms_inserer_crayons($out);
	
	$out .=  "</td></tr></table><br />\n";
	return $out;
}

function affichage_donnees_tous($type_form){
  include_spip("inc/presentation");
	include_spip('public/assembler');
	if (!include_spip('inc/autoriser'))
		include_spip('inc/autoriser_compat');

  _Forms_install();
	$row=spip_fetch_array(spip_query("SELECT titre FROM spip_forms WHERE id_form="._q(_request('id_form'))));
	$titre_page = $row['titre'];
	echo debut_page($titre_page, "documents", "forms");
	if (!$retour = _request('retour')){
		if (find_in_path("exec/{$type_form}s_tous"))
			$retour = generer_url_ecrire($type_form.'s_tous');
		else
			$retour = generer_url_ecrire('tables_tous');
	}
	echo affichage_donnees_tous_corps($type_form,_request('id_form'),$retour, $titre_page);
	if ($GLOBALS['spip_version_code']>=1.9203)
		echo fin_gauche();
	echo fin_page();
}

function affichage_donnee_edit($type_form){
	global $spip_lang_right;
  include_spip("inc/presentation");
	include_spip('public/assembler');
	if (!include_spip('inc/autoriser'))
		include_spip('inc/autoriser_compat');

  _Forms_install();
	$prefix = forms_prefixi18n($type_form);
  $icone = find_in_path("img_pack/$type_form-24.png");
  if (!$icone)
  	$icone = "../"._DIR_PLUGIN_FORMS."img_pack/donnees-24.png";
  $titre_page = _T("$prefix:type_des_tables");
  
  $id_form = intval(_request('id_form'));
  $id_donnee = intval(_request('id_donnee'));
  $res = spip_query("SELECT id_form,statut FROM spip_forms_donnees WHERE id_donnee="._q($id_donnee));
  if ($row = spip_fetch_array($res))
  if (!$id_form && $id_donnee){
		$id_form = $row['id_form'];
  }
  $statut = $row['statut'];
  
	$contexte = array('id_form'=>$id_form,'id_donnee'=>$id_donnee,'type_form'=>$type_form,'titre_liste'=>$titre_page,'couleur_claire'=>$GLOBALS['couleur_claire'],'couleur_foncee'=>$GLOBALS['couleur_foncee']);
	$formulaire = recuperer_fond("modeles/form",$contexte);
	$row = spip_fetch_array(spip_query("SELECT COUNT(id_donnee) AS n FROM spip_forms_donnees WHERE id_form="._q($id_form)." AND statut!='poubelle'"));
	$nb_reponses = intval($row['n']);
	
	debut_page($titre_page, "documents", "forms");
	debut_gauche();
	debut_boite_info();
	if ($retour = _request('retour')) {
		echo icone_horizontale(_T('icone_retour'), urldecode($retour), $icone, "rien.gif",false);
	}
	if (autoriser('administrer','form',$id_form)) {
		$prefix = forms_prefixi18n($type_form);
		echo icone_horizontale(_T("$prefix:suivi_reponses")."<br />".(($nb_reponses==0)?_T("$prefix:aucune_reponse"):(($nb_reponses==1)?_T("$prefix:une_reponse"):_T("forms:nombre_reponses",array('nombre'=>$nb_reponses)))),
			generer_url_ecrire('donnees_tous',"id_form=$id_form".(strpos($retour,"exec=donnees_tous")===FALSE?"&retour=$retour":"")), "../"._DIR_PLUGIN_FORMS."img_pack/donnees-24.png", "rien.gif",false);
			
		$retour = urlencode(self());
		echo icone_horizontale(_T("$prefix:telecharger_reponses"),
			generer_url_ecrire("forms_telecharger","id_form=$id_form&retour=$retour"), "../"._DIR_PLUGIN_FORMS. "img_pack/donnees-exporter-24.png", "rien.gif",false);
		if (defined('_DIR_PLUGIN_CSVIMPORT')){
			echo icone_horizontale(_T("$prefix:importer_donnees_csv"),
				generer_url_ecrire("csvimport_import","id_form=$id_form&retour=$retour"), "../"._DIR_PLUGIN_FORMS. "img_pack/donnees-importer-24.png", "rien.gif",false);
		}
	}
	echo "<p>";
	fin_boite_info();
	
 	$res = spip_query("SELECT documents FROM spip_forms WHERE id_form="._q($id_form));
 	$row = spip_fetch_array($res);
 	if ($row['documents']=='oui'){
		if ($id_donnee){
			# affichage sur le cote des pieces jointes, en reperant les inserees
			# note : traiter_modeles($texte, true) repere les doublons
			# aussi efficacement que propre(), mais beaucoup plus rapidement
			echo afficher_documents_colonne($id_donnee, "donnee", _request('exec'));
		} else {
			# ICI GROS HACK
			# -------------
			# on est en new ; si on veut ajouter un document, on ne pourra
			# pas l'accrocher a l'article (puisqu'il n'a pas d'id_article)...
			# on indique donc un id_article farfelu (0-id_auteur) qu'on ramassera
			# le moment venu, c'est-ˆ-dire lors de la creation de l'article
			# dans editer_article.
			echo afficher_documents_colonne(0-$GLOBALS['auteur_session']['id_auteur'], "donnee", _request('exec'));
		}
 	}
	
	creer_colonne_droite();
	if ($id_donnee){
		$table_donnee_deplace = charger_fonction('table_donnee_deplace','inc');
		echo ajax_action_auteur('table_donnee_deplace',"$id_form-$id_donnee",'donnees_edit', "id_form=$id_form&id_donnee=$id_donnee", 
			$table_donnee_deplace($id_donnee,$id_form));		
	}
	
	/*if (include_spip('inc/snippets'))
		echo boite_snippets($titre_type,_DIR_PLUGIN_FORMS."img_pack/$type_form-24.gif",'forms','forms');*/
	
	debut_droite();
	if ($id_donnee){
		echo debut_cadre_relief();
		$instituer_forms_donnee = charger_fonction('instituer_forms_donnee','inc');
		echo $instituer_forms_donnee($id_form,$id_donnee,$statut);
		echo fin_cadre_relief();
	}

	echo "<div class='verdana2'>$formulaire</div>";
	
	if ($id_donnee) {
		if ($GLOBALS['spip_version_code']<1.92)		ob_start(); // des echo direct en 1.9.1
		$liste = afficher_articles(_T("forms:info_articles_lies_donnee"),
			array('FROM' => 'spip_articles AS articles, spip_forms_donnees_articles AS lien',
			'WHERE' => "lien.id_article=articles.id_article AND id_donnee="._q($id_donnee)." AND statut!='poubelle'",
			'ORDER BY' => "titre"));
		if ($GLOBALS['spip_version_code']<1.92) {
			$liste = ob_get_contents();
			ob_end_clean();
		}
		echo $liste;
	}

	// donnees liantes
	list($out,$les_donnees,$nombre_donnees) = Forms_afficher_liste_donnees_liees(
		"donnee_liee", 
		$id_donnee, 
		"donnee",
		"", 
		"forms_donnees_liantes", 
		"forms_donnees_liantes", 
		"id_donnee=$id_donnee", 
		self());
	echo "<div id='forms_donnees_liantes'>$out</div>";
	
	if ($GLOBALS['spip_version_code']>=1.9203)
		echo fin_gauche();
	echo fin_page();
}
?>