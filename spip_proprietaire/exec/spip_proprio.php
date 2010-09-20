<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function exec_spip_proprio() {
	global $connect_statut, $spip_lang_right, $spip_lang_left;
	if ($connect_statut != "0minirezo" ) { include_spip('inc/minipres'); echo minipres(); exit; }
	include_spip('inc/presentation');
	$commencer_page = charger_fonction('commencer_page', 'inc');
	$titre_page = _T('spip_proprio:proprietaire_titre_page');

	$page = _request('page');
	$link_test = $GLOBALS['meta']['adresse_site'].'/?page=test_proprietaire';
	$lien_page_test = icone_horizontale(_T('spip_proprio:testing_page_public'), $link_test, "article-24.gif", "rien.gif", false)
		."[<a href='".$link_test."' target='_blank' title='"._T('spip_proprio:new_window')."'>"._T('spip_proprio:new_window')."</a>]";
	$warning = debut_boite_info(true)
		. "\n<div class='verdana2' style='text-align: justify'>"
		. http_img_pack("warning.gif", (_T('avis_attention')),
			"width='48' height='48' style='float: $spip_lang_right; padding-$spip_lang_left: 10px;'")
		. _T('spip_proprio:pconfig_avertissement')
		. "</div>"
		. fin_boite_info(true);
	$contenu = debut_boite_info(true)
		. "\n<div class='verdana2' style='text-align: justify'>"
		. cadre_depliable('rien.gif', _T('spip_proprio:pourquoi_ce_plugin'), false, propre(_T('spip_proprio:presentation')), "bloc_presentation", '')
		. cadre_depliable('rien.gif', _T('spip_proprio:utiliser_ce_plugin'), false, propre(_T('spip_proprio:presentation_plugin')), "bloc_presentation_plugin", '')
		. cadre_depliable('rien.gif', _T('spip_proprio:outils_de_communication'), false, propre(_T('spip_proprio:presentation_outils_de_communication')).$lien_page_test, "bloc_presentation_outils", '')
		. "</div>"
		. fin_boite_info(true)
		. boutons_proprietaire(false);
	$info_texte = _T('spip_proprio:proprietaire_texte');
	$info_supp = _T("spip_proprio:proprietaire_texte_supp");
	$icone = find_in_path('images/idisk-dir-24.png');

	// on force le chargement de proprietaire_fr si present
	spip_proprio_proprietaire_texte();
	if( $save = _request('save') AND $save == 'oui' ){
		$raccourci = _request('raccourci') ? _request('raccourci') : false;
		if($raccourci) if($ok = traiter_textes_proprietaire($raccourci)) { print $ok; exit; }
	}

	if($page) switch($page){
		case 'textes' :
			$titre_page = _T('spip_proprio:ptexte_titre_page');
			$icone = find_in_path('images/gnome-text-abiword-24.png');
			$boutons = boutons_proprietaire('texte', true);
			$lien_page_test = '';
			$info_texte = _T("spip_proprio:ptexte_texte");
			$info_supp = _T("spip_proprio:ptexte_info_tags")
				. "<br /><br />" . _T("spip_proprio:ptexte_info_supp");
			$raccourci = _request('editer');
			$form_depliement = $raccourci ? 'deplie' : 'replie';
			$contenu = $warning . debut_boite_info(true)
				. "<div class='titrem replie' onmouseover=\"jQuery(this).depliant('#ptexte-info');\"><a href='#' onclick=\"return jQuery(this).depliant_clicancre('#ptexte-info');\" class='titremancre'></a>"
				. _T('spip_proprio:ptexte_info_titre')."</div><div id='ptexte-info' class='bloc_depliable blocreplie'>"._T('spip_proprio:ptexte_info_texte')."</div><br class='nettoyeur' />"
				. "<div class='titrem $form_depliement' onmouseover=\"jQuery(this).depliant('#ptexte-form');\"><a href='#' onclick=\"return jQuery(this).depliant_clicancre('#ptexte-form');\" class='titremancre'></a>"
				. _T('spip_proprio:ptexte_form_titre')."</div><div id='ptexte-form' class='bloc_depliable bloc$form_depliement'>"
				. recuperer_fond("/prive/proprietaire_textes", array('raccourci' => $raccourci))
				. "</div><br class='nettoyeur' />"
				. charger_textes_proprietaire(false)
				. fin_boite_info(true);
			break;
		case 'proprietaire' :
			$titre_page = _T('spip_proprio:infos_proprietaire');
			$icone = find_in_path('images/gnome-http-url-24.png');
			$boutons = boutons_proprietaire('proprietaire', true);
			$info_texte = _T("spip_proprio:pconfig_texte", array('type'=>'propri&eacute;taire'))
				. _T("spip_proprio:pconfig_texte_lien_doc") . $lien_page_test; 
			$info_supp = _T("spip_proprio:pconfig_texte_notes") . _T("spip_proprio:pconfig_texte_ajouts");

			$infos_necessaires = recuperer_fond("/prive/proprietaire_formulaires", array('who'=>$page, 'form'=>'infos_necessaires'));
			$adresse = recuperer_fond("/prive/proprietaire_formulaires", array('who'=>$page, 'form'=>'adresse'));
			$infos_legales = recuperer_fond("/prive/proprietaire_formulaires", array('who'=>$page, 'form'=>'infos_legales'));
			$cnil = recuperer_fond("/prive/proprietaire_formulaires", array('form'=>'cnil'));
			$copyright = recuperer_fond("/prive/proprietaire_formulaires", array('form'=>'copyright'));

			$contenu = $warning . debut_cadre_trait_couleur(find_in_path("images/idisk-dir-24.png"), true, "", _T('spip_proprio:infos_legend'))
				. pipeline('affiche_milieu', array('args'=>array('exec'=>'spip_proprio'), 'data'=>$infos_necessaires))
				. fin_cadre_trait_couleur(true)
				. debut_cadre_trait_couleur(find_in_path("images/temp-home-24.png"), true, "", _T('spip_proprio:adresse_legend'))
				. pipeline('affiche_milieu', array('args'=>array('exec'=>'spip_proprio'), 'data'=>$adresse))
				. fin_cadre_trait_couleur(true)
				. debut_cadre_trait_couleur(find_in_path("images/gnome-text-x-troff-man-24.png"), true, "", _T('spip_proprio:legal_legend'))
				. pipeline('affiche_milieu', array('args'=>array('exec'=>'spip_proprio'), 'data'=>$infos_legales))
				. fin_cadre_trait_couleur(true)
				. debut_cadre_trait_couleur(find_in_path("images/gnome-text-x-troff-man-24.png"), true, "", _T('spip_proprio:cnil_legend'))
				. pipeline('affiche_milieu', array('args'=>array('exec'=>'spip_proprio'), 'data'=>$cnil))
				. fin_cadre_trait_couleur(true)
				. debut_cadre_trait_couleur(find_in_path("images/gnome-application-pgp-24.png"), true, "", _T('spip_proprio:copyright_legend'))
				. pipeline('affiche_milieu', array('args'=>array('exec'=>'spip_proprio'), 'data'=>$copyright))
				. fin_cadre_trait_couleur(true);
			break;
		case 'hebergeur' :
			$titre_page = _T('spip_proprio:infos_hebergeur');
			$icone = find_in_path('images/gnome-http-url-24.png');
			$boutons = boutons_proprietaire('hebergeur', true);
			$info_texte = _T("spip_proprio:pconfig_texte", array('type'=>'h&eacute;bergeur'))
				. _T("spip_proprio:pconfig_texte_lien_doc") . $lien_page_test; 
			$info_supp = _T("spip_proprio:pconfig_texte_notes") . _T("spip_proprio:pconfig_texte_ajouts");

			$idem = recuperer_fond("/prive/proprietaire_formulaires", array('who'=>$page, 'form'=>'idem'));
			$infos_necessaires = recuperer_fond("/prive/proprietaire_formulaires", array('who'=>$page, 'form'=>'infos_necessaires'));
			$adresse = recuperer_fond("/prive/proprietaire_formulaires", array('who'=>$page, 'form'=>'adresse'));
			$infos_legales = recuperer_fond("/prive/proprietaire_formulaires", array('who'=>$page, 'form'=>'infos_legales'));
			$serveur = recuperer_fond("/prive/proprietaire_formulaires", array('form'=>'serveur'));

			$contenu = $warning . debut_cadre_trait_couleur(find_in_path("images/stock_about.png"), true, "", _T('spip_proprio:infos_idem'))
				. pipeline('affiche_milieu', array('args'=>array('exec'=>'spip_proprio'), 'data'=>$idem))
				. fin_cadre_trait_couleur(true)
				. debut_cadre_trait_couleur(find_in_path("images/idisk-dir-24.png"), true, "", _T('spip_proprio:infos_legend'))
				. pipeline('affiche_milieu', array('args'=>array('exec'=>'spip_proprio'), 'data'=>$infos_necessaires))
				. fin_cadre_trait_couleur(true)
				. debut_cadre_trait_couleur(find_in_path("images/temp-home-24.png"), true, "", _T('spip_proprio:adresse_legend'))
				. pipeline('affiche_milieu', array('args'=>array('exec'=>'spip_proprio'), 'data'=>$adresse))
				. fin_cadre_trait_couleur(true)
				. debut_cadre_trait_couleur(find_in_path("images/gnome-text-x-troff-man-24.png"), true, "", _T('spip_proprio:legal_legend'))
				. pipeline('affiche_milieu', array('args'=>array('exec'=>'spip_proprio'), 'data'=>$infos_legales))
				. fin_cadre_trait_couleur(true)
				. debut_cadre_trait_couleur(find_in_path("images/gnome-text-x-troff-man-24.png"), true, "", _T('spip_proprio:serveur_legend'))
				. pipeline('affiche_milieu', array('args'=>array('exec'=>'spip_proprio'), 'data'=>$serveur))
				. fin_cadre_trait_couleur(true);
			break;
		case 'createur' :
			$titre_page = _T('spip_proprio:infos_createur');
			$icone = find_in_path('images/gnome-http-url-24.png');
			$boutons = boutons_proprietaire('createur', true);
			$info_texte = _T("spip_proprio:pconfig_texte", array('type'=>'cr&eacute;ateur'))
				. _T("spip_proprio:pconfig_texte_lien_doc") . $lien_page_test; 
			$info_supp = _T("spip_proprio:pconfig_texte_notes") . _T("spip_proprio:pconfig_texte_ajouts");

			$idem = recuperer_fond("/prive/proprietaire_formulaires", array('who'=>$page, 'form'=>'idem'));
			$infos_necessaires = recuperer_fond("/prive/proprietaire_formulaires", array('who'=>$page, 'form'=>'infos_necessaires'));
			$adresse = recuperer_fond("/prive/proprietaire_formulaires", array('who'=>$page, 'form'=>'adresse'));
			$infos_legales = recuperer_fond("/prive/proprietaire_formulaires", array('who'=>$page, 'form'=>'infos_legales'));
			$createur = recuperer_fond("/prive/proprietaire_formulaires", array('form'=>'createur'));

			$contenu = $warning . debut_cadre_trait_couleur(find_in_path("images/stock_about.png"), true, "", _T('spip_proprio:infos_idem'))
				. pipeline('affiche_milieu', array('args'=>array('exec'=>'spip_proprio'), 'data'=>$idem))
				. fin_cadre_trait_couleur(true)
				. debut_cadre_trait_couleur(find_in_path("images/idisk-dir-24.png"), true, "", _T('spip_proprio:infos_legend'))
				. pipeline('affiche_milieu', array('args'=>array('exec'=>'spip_proprio'), 'data'=>$infos_necessaires))
				. fin_cadre_trait_couleur(true)
				. debut_cadre_trait_couleur(find_in_path("images/temp-home-24.png"), true, "", _T('spip_proprio:adresse_legend'))
				. pipeline('affiche_milieu', array('args'=>array('exec'=>'spip_proprio'), 'data'=>$adresse))
				. fin_cadre_trait_couleur(true)
				. debut_cadre_trait_couleur(find_in_path("images/gnome-text-x-troff-man-24.png"), true, "", _T('spip_proprio:legal_legend'))
				. pipeline('affiche_milieu', array('args'=>array('exec'=>'spip_proprio'), 'data'=>$infos_legales))
				. fin_cadre_trait_couleur(true)
				. debut_cadre_trait_couleur(find_in_path("images/gnome-text-x-troff-man-24.png"), true, "", _T('spip_proprio:admin_legend'))
				. pipeline('affiche_milieu', array('args'=>array('exec'=>'spip_proprio'), 'data'=>$createur))
				. fin_cadre_trait_couleur(true);
			break;
/*
		case 'skels' :
			$titre_page = _T('spip_proprio:pskels_titre_page');
			$icone = find_in_path('images/gnome-image-rgb-24.png');
			$boutons = boutons_proprietaire('skels', true);
			$link_test = $GLOBALS['meta']['adresse_site'].'/?page=proprietaire';
			$lien_page_test = icone_horizontale(_T('spip_proprio:pconfig_testing_page_public'), $link_test, "article-24.gif", "rien.gif", false)
				."[<a href='".$link_test."' target='_blank' title='"._T('spip_proprio:new_window')."'>"._T('spip_proprio:new_window')."</a>]";
			$info_texte = _T("spip_proprio:pcskels_texte") . $lien_page_test;
			$info_supp = _T("spip_proprio:pconfig_texte_notes");

			$copyright = recuperer_fond("/prive/proprietaire_formulaires", array('form'=>'copyright'));
			$infos_legales_createur = recuperer_fond("/prive/proprietaire_formulaires", array('form'=>'infos_legales_createur'));
			$infos_legales_hebergeur = recuperer_fond("/prive/proprietaire_formulaires", array('form'=>'infos_legales_hebergeur'));

			$contenu = debut_cadre_trait_couleur(find_in_path("images/gnome-application-pgp-24.png"), true, "", _T('spip_proprio:pconfig_copyright_legend'))
				. pipeline('affiche_milieu', array('args'=>array('exec'=>'spip_proprio'), 'data'=>$copyright))
				. fin_cadre_trait_couleur(true)
				. debut_boite_info(true) . "\n<div class='verdana2' style='text-align: justify'>"
				. http_img_pack(find_in_path("images/idisk-dir-36.png"), (_T('avis_attention')),
					"width='36' height='36' style='float: $spip_lang_right; padding-$spip_lang_left: 10px;'")
				. _T('spip_proprio:pskels_info_mentions_legales') . "</div>" . fin_boite_info(true)
				. debut_cadre_trait_couleur(find_in_path("images/gnome-text-x-troff-man-24.png"), true, "", _T('spip_proprio:pskels_legal_legend_createur'))
				. pipeline('affiche_milieu', array('args'=>array('exec'=>'spip_proprio'), 'data'=>$infos_legales_createur))
				. fin_cadre_trait_couleur(true)
				. debut_cadre_trait_couleur(find_in_path("images/gnome-text-x-troff-man-24.png"), true, "", _T('spip_proprio:pskels_legal_legend_hebergeur'))
				. pipeline('affiche_milieu', array('args'=>array('exec'=>'spip_proprio'), 'data'=>$infos_legales_hebergeur))
				. fin_cadre_trait_couleur(true);
			break;
*/
	}

	echo($commencer_page(_T('spip_proprio:spip_proprio')." - ".$titre_page, 'configuration', "configuration")),
		"<br /><br /><br />\n", gros_titre($titre_page,'', false), barre_onglets("configuration", "spip_proprio"),
		debut_gauche('', true),
		debut_cadre_relief($icone, true, "", $titre_page), $info_texte, fin_cadre_relief(true), 
		($info_supp ? debut_cadre_enfonce('', true, '', '')."<b>"._T('spip_proprio:notes')."</b><br />".$info_supp.fin_cadre_enfonce(true) : ''), 
		$boutons, "<br class='nettoyeur' />",
		creer_colonne_droite('', true), debut_droite('', true),
		$contenu, fin_gauche(), fin_page();
}

function boutons_proprietaire($on=false, $raccourcis=false){
	$div = '';
	if($on)
		$div .= icone_horizontale(_T('spip_proprio:proprietaire_titre_page_short'), generer_url_ecrire('spip_proprio'), find_in_path('images/idisk-dir-24.png'), 'rien.gif', false);
/*
	if($on != 'config')
		$div .= icone_horizontale(_T('spip_proprio:pconfig_titre_page'), generer_url_ecrire('spip_proprio','page=config'), find_in_path('images/gnome-http-url-24.png'), 'rien.gif', false);
*/
	if($on != 'texte')
		$div .= icone_horizontale(_T('spip_proprio:ptexte_titre_page'), generer_url_ecrire('spip_proprio','page=textes'), find_in_path('images/gnome-text-abiword-24.png'), 'rien.gif', false);
	if($on != 'proprietaire')
		$div .= icone_horizontale(_T('spip_proprio:infos_proprietaire'), generer_url_ecrire('spip_proprio','page=proprietaire'), find_in_path('images/gnome-image-rgb-24.png'), 'rien.gif', false);
	if($on != 'hebergeur')
		$div .= icone_horizontale(_T('spip_proprio:infos_hebergeur'), generer_url_ecrire('spip_proprio','page=hebergeur'), find_in_path('images/gnome-image-rgb-24.png'), 'rien.gif', false);
	if($on != 'createur')
		$div .= icone_horizontale(_T('spip_proprio:infos_createur'), generer_url_ecrire('spip_proprio','page=createur'), find_in_path('images/gnome-image-rgb-24.png'), 'rien.gif', false);

	if($raccourcis) return bloc_des_raccourcis( $div );
	return( $div );
}
?>