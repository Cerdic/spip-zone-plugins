<?php
#              ACS
#          (Plugin Spip)
#     http://acs.geomaticien.org
#
# Copyright Daniel FAIVRE, 2007-2009
# Copyleft: licence GPL - Cf. LICENCES.txt
#
/**
 *  ACS model default values
 * On initialise les valeurs des variables du modèle 
 */
$def = array(
	'acsAgendaUse' => 'oui',
	'acsAgendaTitreFondColor' => '=acsRubnavTitreFond',
	'acsAgendaTitreFondImage' => '=acsRubnavTitreFondImage',
	'acsAgendaBordColor' => '=acsRubnavBordColor',
	'acsAgendaBordWidth' => '=acsRubnavBordWidth',
	'acsAgendaBordStyle' => '=acsRubnavBordStyle',
	'acsAgendaThisMonth' => '=acsRubnavFond', 
	'acsAgendaBulleFondColor' => '=acsRubnavFond2',

	'acsArticlesUse' => 'oui',
	'acsArticlesBordColor' => '#cec1eb',
	'acsArticlesBordWidth' => 'thin',
	'acsArticlesBordStyle' => 'inset',
	'acsArticlesTabBordColor' => '#cfcfcf',
	'acsArticlesTabBordWidth' => 'thin',
	'acsArticlesTabBordStyle' => 'inset',
	'acsArticlesTabFirst' => '#dfe5ef',
	'acsArticlesTabOdd' => '#e4dfef',
	'acsArticlesTabEven' => '#dfdfef',
	'acsArticlesPuce' => 'puce_8x8.gif',

	'acsAudioUse' => 'oui',
	'acsAudioTitreFond' => '=acsRubnavTitreFond',
	'acsAudioTitreImage' => '=acsRubnavTitreFondImage',
	'acsAudioBord' => '=acsRubnavBordColor',
 	'acsAudioBordWidth' => '=acsRubnavBordWidth',
	'acsAudioBordStyle' => '=acsRubnavBordStyle', 

	'acsAuteursUse' => 'oui',
	'acsAuteursBordColor' => '=acsRubnavBordColor',
	'acsAuteursBordWidth' => '=acsRubnavBordWidth',
	'acsAuteursBordStyle' => '=acsRubnavBordStyle',
	'acsAuteursTitreFond' => '=acsRubnavTitreFond',
	'acsAuteursTitreFondImage' => '=acsRubnavTitreFondImage',

	'acsBanniereUse' => 'oui',

	'acsBanniereLogo' => 'non',
	'acsBanniereFont' => 'Verdana, Arial',

	'acsBrevesUse' => 'oui',
	'acsBandeauUse' => 'oui',
	'acsBandeauContenu' => '<a href="http://acs.geomaticien.org">ACS</a>: pour configurer ce site, <a href="ecrire/?exec=acs&amp;onglet=composants&amp;composant=fond">cliquez ici</a>.',

	'acsCustomUse' => 'oui',

	'acsEditoUse' => 'oui',

	'acsFondUse' => 'oui',
	'acsFondFavicon' => 'favicon.ico',
	'acsFondColor' => '#f4f4f4',
	'acsFondText' => '#002200',
	'acsFondLink' => '#00008d',
	'acsFondLinkHover' => '#0000f4',
	'acsFondDeplierHaut' => 'deplierhaut.gif',
	'acsFondDeplierHaut_rtl' => 'deplierhaut_rtl.gif',
	'acsFondDeplierBas' => 'deplierbas.gif',

	'acsForumsUse' => 'oui',
	'acsForumsBordColor' => '=acsRubnavBordColor',
	'acsForumsBordWidth' => '=acsRubnavBordWidth',
	'acsForumsBordStyle' => '=acsRubnavBordStyle',

	'acsKeysUse' => 'oui',
	'acsKeysBordColor' => '=acsRubnavBordColor',
	'acsKeysBordWidth' => '=acsRubnavBordWidth',
	'acsKeysBordStyle' => '=acsRubnavBordStyle',
	'acsKeysTitreFond' => '=acsRubnavTitreFond',
	'acsKeysTitreFondImage' => '=acsRubnavTitreFondImage',

	'acsOngletsUse' => 'oui',
	'acsOnglets1' => 'sommaire',
	'acsOnglets2' => 'resume',
	'acsOnglets3' => 'plan',
	'acsOnglets4' => 'sites',
	'acsOngletschoix5' => 'non',
	'acsOnglets5' => 'forums',
	'acsOngletsFondColor' => '#ffffff',
	'acsOngletsBordColor' => '#dfdfdf',
	'acsOngletsCouleurInactif' => '#efefef',
	'acsOngletsCouleurSurvol' => '#ffffff',

	'acsOursUse' => 'oui',

	'acsRechercheUse' => 'oui',

	'acsRubnavUse' => 'oui',
	'acsRubnavTitre' => 'oui',
	'acsRubnavTitreFond' => '#eef5f7',
	'acsRubnavTitreFondImage' => 'titrefond_00.png',
	'acsRubnavFond' => '#f4f4ff',
	'acsRubnavFond2' => '#efefff',
	'acsRubnavFond3' => '#e4e4ff',
	'acsRubnavFond4' => '#dfdfef',
	'acsRubnavFond5' => '#cacaff',
	'acsRubnavFond6' => '#c4c4ef',
	'acsRubnavFondHover' => '#f8f8df',
	'acsRubnavBordColor' => '#939ac2',
	'acsRubnavBordWidth' => 'thin',
	'acsRubnavBordStyle' => 'solid',
	'acsRubnavSep' => '#dfdfdf',

	'acsSyndicUse' => 'oui',

	'acsTagsUse' => 'oui',
	'acsTagsTitre' => '=acsRubnavTitre',
	'acsTagsBordColor' => '=acsRubnavBordColor',
	'acsTagsBordWidth' => '=acsRubnavBordWidth',
	'acsTagsBordStyle' => '=acsRubnavBordStyle',
	'acsTagsTitreFond' => '=acsRubnavTitreFond',
	'acsTagsTitreFondImage' => '=acsRubnavTitreFondImage',
	'acsTagsSep' => '=acsRubnavSep',
	
	'acsVideoUse' => 'oui',

	'acsModuleUse' => 'oui',

	'acsModule1Use' => 'oui',
	'acsModule11' => 'banniere',
	'acsModule12' => 'bandeau',

	'acsModule3Use' => 'oui',
	'acsModule32' => 'ours',

//sommaire
	'acsModule20Use' => 'oui',
	'acsModule20Left' => '10px',
	'acsModule20Right' => '10px',
	'acsModule201' => 'recherche',
	'acsModule202' => 'rubnav',

	'acsModule40Use' => 'oui',
	'acsModule40Left' => '10px',
	'acsModule40Right' => '10px',
	'acsModule401' => 'agenda',
	'acsModule402' => 'tags',

//resume
	'acsModule21Use' => 'oui',
	'acsModule21Left' => '10px',
	'acsModule21Right' => '10px',
	'acsModule211' => 'recherche',
	'acsModule212' => 'rubnav',
	'acsModule213' => 'forums',

	'acsModule41Use' => 'oui',
	'acsModule41Left' => '10px',
	'acsModule41Right' => '10px',
	'acsModule411' => 'agenda',
	'acsModule412' => 'tags',

// plan
	'acsModule22Use' => 'oui',
	'acsModule22Left' => '10px',
	'acsModule22Right' => '10px',
	'acsModule221' => 'recherche',
	'acsModule222' => 'rubnav',

//sites
	'acsModule23Use' => 'oui',
	'acsModule23Left' => '10px',
	'acsModule23Right' => '10px',
	'acsModule231' => 'recherche',
	'acsModule232' => 'rubnav',

//forums
	'acsModule24Use' => 'oui',
	'acsModule24Left' => '10px',
	'acsModule24Right' => '10px',
	'acsModule241' => 'recherche',
	'acsModule242' => 'rubnav',

// article
	'acsModule25Use' => 'oui',
	'acsModule25Left' => '10px',
	'acsModule25Right' => '10px',
	'acsModule251' => 'recherche',
	'acsModule252' => 'rubnav',
	'acsModule253' => 'forums',
	'acsModule254' => 'keys',
	'acsModule255' => 'auteurs',

	'acsModule45Use' => 'oui',
	'acsModule45Left' => '10px',
	'acsModule45Right' => '10px',
	'acsModule451' => 'agenda',

// rubrique
	'acsModule26Use' => 'oui',
	'acsModule26Left' => '10px',
	'acsModule26Right' => '10px',
	'acsModule261' => 'recherche',
	'acsModule262' => 'rubnav',
	'acsModule263' => 'keys',

	'acsModule46Use' => 'oui',
	'acsModule46Left' => '10px',
	'acsModule46Right' => '10px',
	'acsModule461' => 'agenda',

// mot
	'acsModule27Use' => 'oui',
	'acsModule27Left' => '10px',
	'acsModule27Right' => '10px',
	'acsModule271' => 'recherche',
	'acsModule272' => 'rubnav',
	'acsModule273' => 'keys',

// forum
	'acsModule28Use' => 'oui',
	'acsModule28Left' => '10px',
	'acsModule28Right' => '10px',
	'acsModule281' => 'recherche',
	'acsModule282' => 'rubnav',
	'acsModule283' => 'keys',

	'acsDerniereModif' => time(),
	'acsModel' => 'cat',

);
?>