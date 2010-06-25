<?php
#              ACS
#          (Plugin Spip)
#     http://acs.geomaticien.org
#
# Copyright Daniel FAIVRE, 2007-2010
# Copyleft: licence GPL - Cf. LICENCES.txt
#
/**
 * ACS model default values
 * 
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
	'acsAgendaBulleFondColor' => '=acsRubnavFond6',
	'acsAgendaBulleArticleModifieFondColor' => '=acsRubnavFond4',
	'acsAgendaBulleBreveFondColor' => '=acsRubnavFond2',
	'acsAgendaBulleVoirArticlesModifies' => 'oui',

	'acsArticlesUse' => 'oui',
	'acsArticlesTitreFondColor' => '#f4f4f4',
	'acsArticlesBordColor' => '#cec1eb',
	'acsArticlesBordWidth' => '1px',
	'acsArticlesBordStyle' => 'inset',
	'acsArticlesTabBordColor' => '#cfcfcf',
	'acsArticlesTabBordWidth' => '1px',
	'acsArticlesTabBordStyle' => 'inset',
	'acsArticlesTabFirst' => '#dfe5ef',
	'acsArticlesTabOdd' => '#e4dfef',
	'acsArticlesTabEven' => '#dfdfef',
	'acsArticlesPuce' => 'puce_8x8.gif',

	'acsAudioUse' => 'oui',
	'acsAudioTitreFond' => '=acsRubnavTitreFond',
	'acsAudioTitreImage' => '=acsRubnavTitreFondImage',
	'acsAudioFond' => '=acsRubnavFond',
	'acsAudioBordColor' => '=acsRubnavBordColor',
 	'acsAudioBordWidth' => '=acsRubnavBordWidth',
	'acsAudioBordStyle' => '=acsRubnavBordStyle', 
	'acsAudioSep' => '=acsRubnavSep', 
	'acsAudioMp3hover' => '=acsRubnavFondHover', 
	'acsAudioMp3on' => '=acsRubnavFond6', 

	'acsAuteursUse' => 'oui',
	'acsAuteursTitreFond' => '=acsRubnavTitreFond',
	'acsAuteursTitreFondImage' => '=acsRubnavTitreFondImage',
	'acsAuteursFond' => '=acsRubnavFond',
	'acsAuteursBordColor' => '=acsRubnavBordColor',
	'acsAuteursBordWidth' => '=acsRubnavBordWidth',
	'acsAuteursBordStyle' => '=acsRubnavBordStyle',

	'acsBanniereUse' => 'oui',
	'acsBanniereFond' => '#fcfcfc',
	'acsBanniereFondImage' => 'titrefond_00.png',
	'acsBanniereFondImageRepeatX' => 'non',
	'acsBanniereFondImageRepeatY' => 'oui',
	'acsBanniereLogo' => 'non',
	'acsBanniereFont' => 'Verdana, Arial',

	'acsBrevesUse' => 'oui',
	'acsBrevesTitreFond' => '=acsRubnavTitreFond',
	'acsBrevesTitreFondImage' => '=acsRubnavTitreFondImage',
	'acsBrevesFond' => '=acsRubnavFond',
	'acsBrevesBordColor' => '=acsRubnavBordColor',
	'acsBrevesBordWidth' => '=acsRubnavBordWidth',
	'acsBrevesBordStyle' => '=acsRubnavBordStyle',

	'acsBandeauUse' => 'oui',
	'acsBandeauFond' => '=acsRubnavFond',
	'acsBandeauBordColor' => '#c3d5c8',
	'acsBandeauBordBas' => '#c3d5c8',
	'acsBandeauBordWidth' => '1px',
	'acsBandeauBordStyle' => 'inset',
	'acsBandeauTextColor' => '#352d4d',
	'acsBandeauLegende' => 'En construction - Under construction',
	'acsBandeauContenu' => '<a href="http://acs.geomaticien.org">ACS</a>: pour configurer ce site, <a href="ecrire/?exec=acs&amp;onglet=composants&amp;composant=fond">cliquez ici</a>.',
	'acsBandeauText2' => '-&gt; <a href="ecrire/?exec=acs&amp;onglet=composants&amp;composant=bandeau">Modifier ce bandeau</a> &lt;-',
	'acsCustomUse' => 'oui',

	'acsEditoUse' => 'oui',

	'acsFondUse' => 'oui',
	'acsFondFavicon' => 'favicon.ico',
	'acsFondColor' => '#f4f4f4',
	'acsFondText' => '#002200',
	'acsFondLink' => '#00008d',
	'acsFondLinkHover' => '#0000f4',

	'acsForumsUse' => 'oui',
	'acsForumsTitreFond' => '=acsRubnavTitreFond',
	'acsForumsTitreFondImage' => '=acsRubnavTitreFondImage',
	'acsForumsFond' => '=acsRubnavFond',
	'acsForumsBordColor' => '=acsRubnavBordColor',
	'acsForumsBordWidth' => '=acsRubnavBordWidth',
	'acsForumsBordStyle' => '=acsRubnavBordStyle',

	'acsKeysUse' => 'oui',
	'acsKeysTitreFond' => '=acsRubnavTitreFond',
	'acsKeysTitreFondImage' => '=acsRubnavTitreFondImage',
	'acsKeysFond' => '=acsRubnavFond',
	'acsKeysBordColor' => '=acsRubnavBordColor',
	'acsKeysBordWidth' => '=acsRubnavBordWidth',
	'acsKeysBordStyle' => '=acsRubnavBordStyle',

	'acsMailUse' => 'oui',
	'acsMailTitreFond' => '=acsRubnavTitreFond',
	'acsMailTitreFondImage' => '=acsRubnavTitreFondImage',
	'acsMailFond' => '=acsRubnavFond',
	'acsMailBordColor' => '=acsRubnavBordColor',
	'acsMailBordWidth' => '=acsRubnavBordWidth',
	'acsMailBordStyle' => '=acsRubnavBordStyle',

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
	'acsRechercheTitreFond' => '=acsRubnavTitreFond',
	'acsRechercheTitreFondImage' => '=acsRubnavTitreFondImage',
	'acsRechercheFond' => '=acsRubnavFond',
	'acsRechercheInput' => '#ffffff',
	'acsRechercheBordColor' => '=acsRubnavBordColor',
	'acsRechercheBordWidth' => '=acsRubnavBordWidth',
	'acsRechercheBordStyle' => '=acsRubnavBordStyle',

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
	'acsRubnavBordWidth' => '1px',
	'acsRubnavBordStyle' => 'solid',
	'acsRubnavSep' => '#dfdfdf',
	'acsRubnavDeplierHaut' => 'deplierhaut.gif',
	'acsRubnavDeplierHautOver' => 'deplierhauton.gif',
	'acsRubnavDeplierHaut_rtl' => 'deplierhaut_rtl.gif',
	'acsRubnavDeplierHautOver_rtl' => 'deplierhauton_rtl.gif',
	'acsRubnavDeplierBas' => 'deplierbas.gif',
	'acsRubnavDeplierBasOver' => 'deplierbason.gif',

	'acsRubriquesUse' => 'oui',
	'acsRubriquesTitreFond' => '=acsRubnavTitreFond',
	'acsRubriquesTitreFondImage' => '=acsRubnavTitreFondImage',
	'acsRubriquesFond' => '=acsRubnavFond',
	'acsRubriquesBordColor' => '=acsRubnavBordColor',
	'acsRubriquesBordWidth' => '=acsRubnavBordWidth',
	'acsRubriquesBordStyle' => '=acsRubnavBordStyle',

	'acsSyndicUse' => 'oui',
	'acsSyndicTitreFond' => '=acsRubnavTitreFond',
	'acsSyndicTitreFondImage' => '=acsRubnavTitreFondImage',
	'acsSyndicFond' => '=acsRubnavFond',
	'acsSyndicBordColor' => '=acsRubnavBordColor',
	'acsSyndicBordWidth' => '=acsRubnavBordWidth',
	'acsSyndicBordStyle' => '=acsRubnavBordStyle',

	'acsTagsUse' => 'oui',
	'acsTagsTitreFond' => '=acsRubnavTitreFond',
	'acsTagsTitreFondImage' => '=acsRubnavTitreFondImage',
	'acsTagsFond' => '=acsRubnavFond',
	'acsTagsBordColor' => '=acsRubnavBordColor',
	'acsTagsBordWidth' => '=acsRubnavBordWidth',
	'acsTagsBordStyle' => '=acsRubnavBordStyle',
	'acsTagsTitreFond' => '=acsRubnavTitreFond',
	'acsTagsTitreFondImage' => '=acsRubnavTitreFondImage',
	'acsTagsSep' => '=acsRubnavSep',
	'acsTagsTitre' => '=acsRubnavTitre',

	
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

//auteur
	'acsModule44Use' => 'oui',
	'acsModule44Left' => '10px',
	'acsModule44Right' => '10px',
	'acsModule442' => 'mail',

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
	'acsModule452' => 'rubriques',

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