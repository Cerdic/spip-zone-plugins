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
'aide' => 'AIDE en ligne',
'aide_select_multi' =>'Maintenez la touche clavier &laquo;&nbsp;<abbr title="Controle">Ctrl</abbr>&nbsp;&raquo; enfonc&eacute;e pour s&eacute;lectionner plusieurs crit&egrave;res.',
'articles_date' =>'Chercher les articles de',
//B

//C
'colonnes' => 'Colonnes de goupes de mots',
'conf' => 'Configuration',
'conf_public' => 'Affichage publique',
'couleur_bordure' =>'Couleur de la bordure de tableau',
'couleur_police' =>'Couleur de la police de caract&egrave;res',

//D

//E
'erreur_groupes_mots' =>'Vous devez avoir cr&eacute;&eacute; au moins un groupe de mots-cl&eacute; pour configurer la Recherche Multi-crit&egrave;res',
'erreur_rubrique' =>'Vous devez avoir cr&eacute;&eacute; au moins une rubrique pour configurer la Recherche Multi-crit&egrave;res',

//G
'groupes_mots' =>'Goupes de mots-cl&eacute;',
'groupes_mots_attribues' => 'Groupes de mots-cl&eacute; attribu&eacute;s &agrave; cette rubrique',
'groupes_mots_non_attribues' => 'Groupes de mots-cl&eacute; non attribu&eacute;s',
'groupes_mots_tous_attribues' => '<div style="text-align:center;color:#c00;font-weight:bold;">Tous les groupes de mots-cl&eacute; sont attribu&eacute;s &agrave; cette rubrique !</div> ',
'groupes_toutes_rubriques' => 'Groupes de mots-cl&eacute; attribu&eacute;s &agrave; tout le site',

//I
'info_config' => 'S&eacute;lectionnez les groupes de mots-cl&eacute;s que vous voulez attribuer aux diff&eacute;rentes rubriques.<br /> ',
'info_recherche_multi' =>'La recherche multicrit&egrave;res par mots-cl&eacute;s permet de rechercher des articles 
			auxquels nous avons attribu&eacute; des mots-cl&eacute;s.<br />
			<strong><i>Attention : il est imp&eacute;ratif, pour voir fonctionner cette contrib, d\'avoir cr&eacute;er au pr&eacute;alable des mots-cl&eacute;s dans 
			l\'interface priv&eacute;e et d\'associer certains de ces mots-cl&eacute;s ou tous &agrave; des articles.</strong></i><br /><br />',
'insert_bord' =>'Couleur de bordure du tableau. <i>Vous pouvez l\'indiquer en littéral (c.à.d. blue, red, etc...) ou en hexadécimal </i>',
'insert_bord_rub' =>'Couleur de bordure du tableau. <i>Vous pouvez l\'indiquer en littéral (c.à.d. blue, red, etc...) ou en hexadécimal </i>',
'insert_coul' =>'Couleur des textes. <i>Vous pouvez l\'indiquer en littéral (c.à.d. blue, red, etc...) ou en hexadécimal </i>',
'insert_coul_rub' =>'Couleur des textes. <i>Vous pouvez l\'indiquer en littéral (c.à.d. blue, red, etc...) ou en hexadécimal </i>',
'insert_taille' =>'Taille de la police de caract&egrave;res des textes (<i>Valeur en pixelx</i>)',
'insert_taille_rub' =>'Taille des textes',
'insert_nb_colonnes' =>'Nombre de colonnes affich&eacute;es pour la recherche g&eacute;n&eacute;rale <i><small>( Affichage de la recherche page sommaire par exemple)</small></i>',
'insert_nb_colonnes_rub' =>'Nombre de colonnes affich&eacute;es pour la recherche par rubrique <i><small>( Affichage de la recherche page rubrique )</small></i>',

//J
'jours' =>'jours',

//L
'liste_mots' =>'Listes des mots-cl&eacute;s', 
'limiter_recherche_rub' =>'Recherche limit&eacute;e &agrave; la rubrique en cours',

//M
'mois' =>'mois',
'mot_exclu' =>'Mot exclu',
'mot_exclure' =>'Exclure ce mot',
'mots_tous_presents' =>'Tous les mots doivent &ecirc;tre pr&eacute;sents',

//O
'options_recherche' =>'Options de recherche',

//R
'recherche_rubrique' =>'Recherche par rubrique',
'recherche_site' =>'Recherche sur tout le site',
'rubriques' =>'Rubriques',

//S
'select_groupes_mots' => 'S&eacute;lectionnez les groupes de mots que vous voulez associer &agrave; la rubrique.',
'select_rubrique' => 'S&eacute;lectionnez une rubrique',
'select_ttes_rubriques' => '<strong>Tout le site</strong>',
'signature' => '<b>Recherche Multi-crit&egrave;res v.1.0</b><br />
		Ce plugin vous permet d\'effectuer une recherche d\'articles en s&eacute;lectionnant plusieurs
		mots cl&eacute;.<br /><br />
		Pluginnis&eacute; par <b>Dom </b>, <a href=\'http://www.etab.ac-caen.fr/bureaudestests/TiSpip\' title=\' \' target=\'_blank\'>TiSpiP-sKeLeT</a> (11/2007 - ??/200?) <br />
		Contrib originale de Paul SANCHEZ - 
		<a href=\'http://www.netdeveloppeur.com/\' title=\' \' target=\'_blank\'>NetDeveloppeur</a>.<br /><br />
		Merci &agrave; <strong>Scoty</strong> - <a href=\'www.koakidi.com\' title=\'www.koakidi.com\'>www.koakidi.com</a> (<i>ze king of ze plugin</i>) pour ses conseils salvateurs ',

//T
'taille_police' =>'Taille de la police de caract&egrave;res',
'text_select_rubrique' => 'S&eacute;lectionnez une rubrique &agrave; laquelle vous voulez ajouter des groupes de mots',
'text_choix_colonnes' => 'Choisissez le nombre de colonnes',
'text_conf_colonnes' => 'L\'affichage des groupes de mots dans vos pages peut se faire sur une ou plusieurs colonnes. <br />',
'text_conf_public' => 'Sur cette page, vous pouvez configurer le style et la forme de l\'affichage des formulaire de recherche. <br />',
'titre_aide' => 'Pr&eacute;sentation et Installation',
'titre_config' =>'Configuration des groupes de mots cl&eacute; par rubrique.',
'titre_config_public' =>'Configuration de l\'affichage publique.',
'titre_page_admin' =>'Recherche Multi-crit&egrave;res',
'tous' =>'tous',
//Z
'z' =>'z'
);

?>
