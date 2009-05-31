<?php
######################################################################
# RECHERCHE MULTI-CRITERES                                           #
# Auteur: Dominique (Dom) Lepaisant - Octobre 2007                   #
# Adaptation de la contrib de Paul Sanchez - Netdeveloppeur          #
# http://www.netdeveloppeur.com                                      #
# Ce programme est un logiciel libre distribue sous licence GNU/GPL. #
# Pour plus de details voir le fichier COPYING.txt                   #
######################################################################

$GLOBALS[$GLOBALS['idx_lang']] = array(
//A
'affichage_groupes_mots' =>'Affichage des groupes de mots-cl&eacute; dans le formulaire','aide' => 'AIDE en ligne',
'aide' => 'AIDE en ligne',
'aide_select_multi' => 'Maintenez la touche clavier &laquo;&nbsp;<abbr title="Controle">Ctrl</abbr>&nbsp;&raquo; enfonc&eacute;e pour s&eacute;lectionner plusieurs crit&egrave;res.',
'articles_date' => 'Chercher les articles de',
'articles_sans' => 'Il n&rsquo;y a pas d&rsquo;article li&eacute; aux mots ',
'article_trouve' => 'article trouv&eacute; pour les mots : ',
'articles_trouves' => 'articles trouv&eacute;s pour les mots : ',

//B
'bordure' =>'Bordure des cadres : ',

//C
'checkboxs' =>'Cases &agrave; cocher',
'colonnes' => 'Colonnes de groupes de mots',
'conf' => 'Configuration',
'conf_public' => 'Affichage public',
'couleur_aide' =>'Cliquez sur la couleur de votre choix pour la s&eacute;lectionner.',
'couleur_bordure' => 'Couleur de la bordure de tableau',
'couleur_police' => 'Couleur de la police de caract&egrave;res',

//D
'dans' => 'dans',

//E
'erreur_groupes_mots' => 'Vous devez avoir cr&eacute;&eacute; au moins un groupe de mots-cl&eacute;s pour configurer la Recherche Multi-crit&egrave;res',
'erreur_rubrique' => 'Vous devez avoir cr&eacute;&eacute; au moins une rubrique pour configurer la Recherche Multi-crit&egrave;res',

//G
'groupe' =>'Groupe',
'groupes' =>'Groupes',
'groupes_mots' => 'Groupes de mots-cl&eacute;s',
'groupes_mots_attribues' => 'Groupes de mots-cl&eacute;s attribu&eacute;s &agrave; cette rubrique',
'groupes_mots_non_attribues' => 'Groupes de mots-cl&eacute;s non attribu&eacute;s',
'groupes_mots_tous_attribues' => '<div style="text-align:center;color:#c00;font-weight:bold;">Tous les groupes de mots-cl&eacute;s sont attribu&eacute;s &agrave; cette rubrique !</div> ',
'groupes_toutes_rubriques' => 'Groupes de mots-cl&eacute;s attribu&eacute;s &agrave; tout le site',

//I
'info_config' => 'S&eacute;lectionnez les groupes de mots-cl&eacute;s que vous voulez attribuer aux diff&eacute;rentes rubriques.<br /> ',
'info_recherche_multi' => 'La recherche multicrit&egrave;res par mots-cl&eacute;s permet de rechercher des articles 
			auxquels vous avez attribu&eacute; des mots-cl&eacute;s.<br />
			<strong><em>Attention : il est imp&eacute;ratif, pour voir fonctionner cette contrib, d&rsquo;avoir cr&eacute;er au pr&eacute;alable des mots-cl&eacute;s dans 
			l&rsquo;interface priv&eacute;e et d&rsquo;associer certains de ces mots-cl&eacute;s ou tous &agrave; des articles.</strong></em><br /><br />',
'insert_bord' =>'Choix de la couleur de bordure.',
'insert_coul' =>'Choix de la couleur de police de caract&egrave;res.',
'insert_taille' => 'Taille de la police de caract&egrave;res des textes (<em>Valeur en pixels</em>)',
'insert_taille_rub' => 'Taille des textes',
'insert_nb_colonnes' => 'Nombre de colonnes affich&eacute;es pour la recherche g&eacute;n&eacute;rale <em><small>(Affichage de la recherche page sommaire par exemple)</small></em>',
'insert_nb_colonnes_rub' => 'Nombre de colonnes affich&eacute;es pour la recherche par rubrique <em><small>(Affichage de la recherche page rubrique )</small></em>',

//J
'jours' => 'jours',

//L
'liste_deroulante' =>'Liste d&eacute;roulante',
'liste_mots' => 'Listes des mots-cl&eacute;s', 
'limiter_recherche_rub' => 'Recherche limit&eacute;e &agrave; la rubrique en cours',

//M
'mois' => 'mois',
'mot_exclu' => 'Mot exclu',
'mot_exclure' => 'Exclure ce mot',
'mots_tous_presents' =>'Rechercher uniquement les articles associ&eacute;s &agrave; tous les mots-cl&eacute; :',
'mots_tous_presents_aide' =>'L&rsquo;option choisie est l&rsquo;option par d&eacute;faut. Elle peut &ecirc;tre modifi&eacute;e dans le formulaire public pour chaque recherche',
//N
'nouvelle_recherche' => 'Nouvelle recherche',

//O
'options_recherche' => 'Options de recherche',

//P
'police' =>'Police de caract&egrave;res :', 

//R
'recherche_rubrique' => 'Recherche par rubrique',
'recherche_site' => 'Recherche sur tout le site',
'recherche_resultats' => 'R&eacute;sultats de votre recherche :',
'rubriques' => 'Rubriques',

//S
'select_groupes_mots' => 'S&eacute;lectionnez les groupes de mots que vous voulez associer &agrave; la rubrique.',
'select_rubrique' => 'S&eacute;lectionnez une rubrique',
'select_ttes_rubriques' => '<strong>Tout le site</strong>',
'selectionner_mot' => 'Merci de bien vouloir s&eacute;lectionner au moins 1 mot-cl&eacute; !',
'signature' => '<strong>Recherche Multi-crit&egrave;res v.1.0</strong><br />
		Ce plugin vous permet d&rsquo;effectuer une recherche d&rsquo;articles en s&eacute;lectionnant plusieurs
		mots-cl&eacute;s.<br /><br />
		Pluginis&eacute; par <strong>Dom </strong>, <a href=\'http://www.etab.ac-caen.fr/bureaudestests/TiSpip\' target=\'_blank\'>TiSpiP-sKeLeT</a> (11/2007 - ??/200?) <br />
		Contrib originale de Paul Sanchez - 
		<a href=\'http://www.netdeveloppeur.com/\' target=\'_blank\'>NetDeveloppeur</a>.<br /><br />
		Merci &agrave; <strong>Scoty</strong> - <a href=\'www.koakidi.com\'>www.koakidi.com</a> (<em>ze king of ze plugin</em>) pour ses conseils salvateurs ',

//T
'taille_police' => 'Taille de la police de caract&egrave;res',
'text_select_rubrique' => 'S&eacute;lectionnez une rubrique &agrave; laquelle vous voulez ajouter des groupes de mots',
'text_choix_colonnes' => 'Choisissez le nombre de colonnes',
'text_conf_colonnes' => 'L&rsquo;affichage des groupes de mots dans vos pages peut se faire sur une ou plusieurs colonnes. <br />',
'text_conf_public' => 'Sur cette page, vous pouvez configurer le style et la forme de l&rsquo;affichage des formulaires de recherche. <br />',
'titre_aide' => 'Pr&eacute;sentation et Installation',
'titre_config' => 'Configuration des groupes de mots-cl&eacute;s par rubrique',
'titre_config_public' => 'Configuration de l&rsquo;affichage public',
'titre_page_admin' => 'Recherche Multi-crit&egrave;res',
'titre_page_result_mc' => 'R&eacute;sultats de la recherche multi-crit&egrave;res',
'tous' => 'tous',

//V
'vers_doc'=>'Vers documentation',
'vers_config_mots' =>'Vers configuration des mots-cl&ecute;',

//Z
'z' => 'z'
);

?>
