<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP

$GLOBALS[$GLOBALS['idx_lang']] = array(

'nom' => 'Article',
'description' => 'Article',
'info' => 'Apparence d\'un article',
'help' => '',

'StylePage' => 'Style de page',
'StylePageHelp' => 'La page article peut dépendre d\'un mot-clé: dans ce cas, elle contiendra l\'instance de composant Cadre de numéro <i>nn</i> défini par une balise &lt;nic-page=<i>nn</i>&gt; dans le champ description du mot clé choisi pour cet article dans le groupe des mots-clefs des styles de pages article, ou à défaut le cadre n° 2100 si aucun mot-clé n\'est choisi dans ce groupe ou si le mot-clé choisi ne contient pas cette balise.
<br />
<br />
Pour que ce groupe de mots-clefs techniques n\'apparraisse pas sur le site public, il suffit que son titre commence par un "_".',

'Bord' => 'Bord sup&eacute;rieur',
'LogoTailleMax' => 'Taille maxi du logo',
'LogoAlign' => _T('acs:align'),

'Dates' => 'Dates',
'Aut' => 'Auteurs',
'ChapoGras' => 'Mettre le chapeau de l\'article en gras',
'Sommaire' => 'Sommaire',
'Stats' => 'Statistiques de visites',
'LogoTailleMaxHelp' => 'Taille maxi du logo. Si la valeur n\'est pas numérique, le logo n\'est pas affiché',
'Lock' => 'Contrôle d\'accès',
'LockHelp' => 'Limite l\'accès aux articles avec le mot-clé _ide, _aut, _adm ou _acs, respectivement, aux visiteurs enregistrés, aux rédacteurs, aux administrateurs, ou aux administrateurs ACS.',
'Added' => 'Ajouté à la dernière révision',
'Deleted' => 'Supprimé à la dernière révision'

);
?>