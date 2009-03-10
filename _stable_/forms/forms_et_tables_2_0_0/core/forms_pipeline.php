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

	function forms_insert_head($flux){
		$config = unserialize(isset($GLOBALS['meta']['forms_et_tables'])?$GLOBALS['meta']['forms_et_tables']:"");
		if (!isset($config['inserer_head']) OR $config['inserer_head']) {
			/*
				$flux .= 	"<link rel='stylesheet' href='".find_in_path('img_pack/spip_forms.css')."' type='text/css' media='all' />\n";
				$flux .= 	"<link rel='stylesheet' href='".find_in_path('img_pack/donnee_voir.css')."' type='text/css' media='all' />\n";
				$flux .= 	"<link rel='stylesheet' href='".find_in_path('img_pack/donnees_tous.css')."' type='text/css' media='all' />\n";
				$flux .= 	"<link rel='stylesheet' href='"._DIR_PLUGIN_FORMS."img_pack/date_picker.css' type='text/css' media='all' />\n";
				$flux .= 	"<link rel='stylesheet' href='"._DIR_PLUGIN_FORMS."img_pack/jtip.css' type='text/css' media='all' />\n";
			*/
			$flux .= "<link rel='stylesheet' href='".generer_url_public('forms_styles.css')."' type='text/css' media='all' />\n";
		}
		return $flux;
	}
	
	function forms_affiche_milieu($flux) {
		$exec =  $flux['args']['exec'];
		$config = unserialize(isset($GLOBALS['meta']['forms_et_tables'])?$GLOBALS['meta']['forms_et_tables']:"");
		switch ($exec){
			case 'articles' :
				$liste_type = (isset($GLOBALS['forms_type_associer']['article'])?$GLOBALS['forms_type_associer']['article']:array());
				if (isset($config['associer_donnees_articles']) AND $config['associer_donnees_articles'])
					$liste_type = array_merge($liste_type,array('table'));
				if (count($liste_type)){
					include_spip('base/forms_base_api_v2');
					foreach($liste_type as $type)
						if (count(forms_lister_tables($type))){
							$id_article = $flux['args']['id_article'];
							$forms_lier_donnees = charger_fonction('forms_lier_donnees','inc');
							$flux['data'] .= "<div id='forms_lier_donnees'>";
							$flux['data'] .= $forms_lier_donnees('article',$id_article, $exec, false, $type);
							$flux['data'] .= "</div>";
						}
				}
				break;
			case 'naviguer':
				$liste_type = (isset($GLOBALS['forms_type_associer']['rubrique'])?$GLOBALS['forms_type_associer']['rubrique']:array());
				if (isset($config['associer_donnees_rubriques']) AND $config['associer_donnees_rubriques'])
					$liste_type = array_merge($liste_type,array('table'));
				$id_rubrique = $flux['args']['id_rubrique'];
				if (count($liste_type) && $id_rubrique){
					include_spip('base/forms_base_api_v2');
					foreach($liste_type as $type)
						if (count(forms_lister_tables($type))){
							$forms_lier_donnees = charger_fonction('forms_lier_donnees','inc');
							$flux['data'] .= "<div id='forms_lier_donnees'>";
							$flux['data'] .= $forms_lier_donnees('rubrique',$id_rubrique, $exec, false, $type);
							$flux['data'] .= "</div>";
						}
				}
				break;
			case 'auteur_infos':
				$liste_type = (isset($GLOBALS['forms_type_associer']['auteur'])?$GLOBALS['forms_type_associer']['auteur']:array());
				if (isset($config['associer_donnees_auteurs']) AND $config['associer_donnees_auteurs'])
					$liste_type = array_merge($liste_type,array('table'));
				if (count($liste_type)){
					include_spip('base/forms_base_api_v2');
					foreach($liste_type as $type)
						if (count(forms_lister_tables($type))){
							$id_auteur = $flux['args']['id_auteur'];
							$forms_lier_donnees = charger_fonction('forms_lier_donnees','inc');
							$flux['data'] .= "<div id='forms_lier_donnees'>";
							$flux['data'] .= $forms_lier_donnees('auteur',$id_auteur, $exec, false, $type);
							$flux['data'] .= "</div>";
						}
				}
				break;
		}
		return $flux;
	}
	
	function forms_affiche_droite($flux){
		if (_request('exec')=='articles_edit'){
			/*
			include_spip('interface/inc/forms_interface');
			$flux['data'] .= forms_afficher_insertion_formulaire($flux['args']['id_article']);
			*/
		}
		return $flux;
	}
	function forms_header_prive($flux){
		/*if ($f=find_in_path('spip_forms_prive.css'))
			$flux .= "<link rel='stylesheet' href='$f' type='text/css' media='all' />\n";
		else
			$flux .= "<link rel='stylesheet' href='"._DIR_PLUGIN_FORMS."img_pack/spip_forms.css' type='text/css' media='all' />\n";
			*/
		$flux .= "<link rel='stylesheet' href='"._DIR_PLUGIN_FORMS."img_pack/donnee_voir.css' type='text/css' media='all' />\n";
		//$flux .= "<link rel='stylesheet' href='"._DIR_PLUGIN_FORMS."img_pack/donnees_tous.css' type='text/css' media='all' />\n";
		//$flux .= "<link rel='stylesheet' href='"._DIR_PLUGIN_FORMS."img_pack/date_picker.css' type='text/css' media='all' />\n";
		//$flux .= "<link rel='stylesheet' href='"._DIR_PLUGIN_FORMS."img_pack/jtip.css' type='text/css' media='all' />\n";
		//$flux .= "<script type='text/javascript'><!--\n var ajaxcharset='utf-8';\n//--></script>";
		
		/*if (in_array(_request('exec'),array('articles','donnees_edit'))){
			$flux .= "<script src='".find_in_path('javascript/iautocompleter.js')."' type='text/javascript'></script>\n"; 
			$flux .= "<script src='".find_in_path('javascript/interface.js')."' type='text/javascript'></script>\n"; 
			if (!_request('var_noajax'))
				$flux .= "<script src='"._DIR_PLUGIN_FORMS."javascript/forms_lier_donnees.js' type='text/javascript'></script>\n";
		}*/
		/*
		if (_request('exec')=='forms_edit'){
			$flux .= "<script src='"._DIR_PLUGIN_FORMS."javascript/interface.js' type='text/javascript'></script>";
			if (!_request('var_noajax'))
				$flux .= "<script src='"._DIR_PLUGIN_FORMS."javascript/forms_edit.js' type='text/javascript'></script>";
			$flux .= 	"<link rel='stylesheet' href='"._DIR_PLUGIN_FORMS."spip_forms_edit.css' type='text/css' media='all' />\n";
		
			if($GLOBALS['meta']['multi_rubriques']=="oui" || $GLOBALS['meta']['multi_articles']=="oui")
				$active_langs = "'".str_replace(",","','",$GLOBALS['meta']['langues_multilingue'])."'";
			else
				$active_langs = "";
			$flux .= "<script src='".find_in_path('forms_lang.js')."' type='text/javascript'></script>\n". 
			"<script type='text/javascript'>\n".
			"var forms_def_lang='".$GLOBALS["spip_lang"]."';var forms_avail_langs=[$active_langs];\n".
			"$(forms_init_lang);\n".
			"</script>\n";
		}*/
		/*
		if (_request('exec')=='donnees_edit'){
			$flux .= "<link rel='stylesheet' href='"._DIR_PLUGIN_FORMS."img_pack/donnees_edit.css' type='text/css' media='all' />\n";
			$flux .= "<script src='"._DIR_PLUGIN_FORMS."javascript/interface.js' type='text/javascript'></script>";
			if (!_request('var_noajax'))
				$flux .= "<script src='"._DIR_PLUGIN_FORMS."javascript/donnees_edit.js' type='text/javascript'></script>";
		}*/
		return $flux;
	}
	
	define('_RACCOURCI_MODELE_FORM', 
		 '(<(form)' # <modele
		.'\s*([0-9]*)\s*' # id
		.'([|](?:<[^<>]*>|[^>])*)?' # |arguments (y compris des tags <...>)
		.'>)' # fin du modele >
		.'\s*(<\/a>)?' # eventuel </a>
	       );
	
	function forms_trouve_liens($texte){
		$forms = array();
		if (preg_match_all(','._RACCOURCI_MODELE_FORM.',is', $texte, $regs, PREG_SET_ORDER)){
			foreach ($regs as $r) {
				$id_form = $r[3];
				$forms[$id_form] = $id_form;
			}
		}
		return $forms;
	}

	function forms_post_edition($flux){
		if ($flux['args']['table']!='spip_articles') return $flux;
		$id_article = intval($flux['args']['id_objet']);
		$res = sql_select("*","spip_articles","id_article=".intval($id_article));
		sql_delete("spip_forms_articles","id_article=".intval($id_article));
		if (($row = sql_fetch($res))
		 && (count($forms = forms_trouve_liens(implode(' ',$row))))){
		 	$ins = array();
		 	foreach($forms as $id)
		 		$ins[] = array("id_article"=>$id_article,"id_form"=>$id);
			sql_insertq_multi("spip_forms_articles",$ins);
		 	
		}
		return $flux;
	}
?>
