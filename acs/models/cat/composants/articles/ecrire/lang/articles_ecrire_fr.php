<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP

$GLOBALS[$GLOBALS['idx_lang']] = array(

'nom' => 'Articles',
'description' => 'Articles et listes d\'articles, pour rubriques, une, r&eacute;sum&eacute;, plan ...',
'info' => 'Apparence des articles et des listes d\'articles d\'une rubrique, de la Une, du r&eacute;sum&eacute, du plan, associés à un mot-clé,...',
'help' => 'L\'apparence et le comportement de ce composant dépendent de la page où il est utilisé.
<br />
<br />
<u>Article</u> :
<br />
Apparence d\'un article.
<br /><br />
<u>Articles</u> :<br />
Apparence des listes d\'articles (d\'une rubrique, associés à un mot-clé, ...).
<br /><br />
<u>Plan</u> :<br />
Les couleurs 2 &agrave; 6 concernent les rubriques de niveau 2 &agrave; 6 dans le plan du site:
 elles d&eacute;finissent l\'&eacute;claircissement ou l\'assombrissement du fond selon la profondeur.
',

'StylePage' => 'Style de page',
'StylePageHelp' => 'La page article peut dépendre d\'un mot-clé: dans ce cas, elle contiendra l\'instance de composant Cadre de numéro <i>nn</i> défini par une balise &lt;nic-page=<i>nn</i>&gt; dans le champ description du mot clé choisi pour cet article dans le groupe des mots-clefs des styles de pages article, ou à défaut le cadre n° 2100 si aucun mot-clé n\'est choisi dans ce groupe ou si le mot-clé choisi ne contient pas cette balise.
<br />
<br />
Pour que ce groupe de mots-clefs techniques n\'apparraisse pas sur le site public, il suffit que son titre commence par un "_".',

'Bord' => 'Bord sup&eacute;rieur',
'MargeBas' => 'Marge inf&eacute;rieure',
'NbLettres' => 'Nb de lettres avant coupure',
'LogoTailleMax' => 'Taille maxi du logo',

'Dates' => 'Dates',
'Aut' => 'Auteurs',
'ChapoGras' => 'Mettre le chapeau de l\'article en gras',
'Sommaire' => 'Sommaire',
'Stats' => 'Statistiques de visites',
'Lock' => 'Contrôle d\'accès',
'LockHelp' => 'Limite l\'accès aux articles avec le mot-clé _ide, _aut, _adm ou _acs, respectivement, aux visiteurs enregistrés, aux rédacteurs, aux administrateurs, ou aux administrateurs ACS.',
'Added' => 'Ajouté à la dernière révision',
'Deleted' => 'Supprimé à la dernière révision',

'Pagination' => 'Nombre d\'articles par page',
'ListesLogoTailleMax' => 'Taille maxi du logo',

'Puce' => 'Puce',
'RubFond2' => '2',
'RubFond3' => '3',
'RubFond4' => '4',
'RubFond5' => '5',
'RubFond6' => '6',
);
?>