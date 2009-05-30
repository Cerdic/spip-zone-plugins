<?php
/* fichier help de csv2spip 
*          langue = fr
*/
?>

Ce fichier sera fabriqu&eacute; &agrave; partir du fichier d'extraction des comptes de IACA ou d'un fichier CSV &eacute;quivalent.
<h3>Format du fichier CSV utilis&eacute; :</h3> 
Un fichier CSV (Common Separator Value) correspond &agrave; un fichier tableur enregistr&eacute; au format texte. 
Chaque ligne de ce fichier correspond &agrave; une ligne du tableur, les donn&eacute;es des cellules de cette ligne &eacute;tant s&eacute;par&eacute;es
par un s&eacute;parateur (ici c'est le ";"). On peut donc fabriquer un tel fichier avec n'importe quel tableur (OOo Calc par ex)
en s&eacute;lectionnant le format .csv ou .txt comme format d'enregistrement. 
<br /><strong>Remarque</strong> : si vous utilisez OpenOffice pour g&eacute;n&eacute;rer votre fichier CSV, n'oubliez pas de cocher la case "Editer les param&egrave;tres de filtre" lorsque vous enregistrez votre fichier pour pr&eacute;ciser ces param&eacute;trages ! (Sous Excel, d&eacute;brouillez vous comme vous pouvez !).
Vu qu'il s'agit d'un format texte, il est &eacute;galement 
possible de le cr&eacute;er/modifier avec un simple &eacute;diteur de texte (bloc-note par ex).<br>
<br>
A partir de la version 2.2 il FAUT ajouter une <strong>ligne en t&ecirc;te du fichier</strong> qui permet de rep&eacute;rer les donn&eacute;es de chaque colonne. 
Les noms de champs qui doivent appara&icirc;tre dans cette premi&egrave;re ligne sont les suivants : 
<br>
<strong>"login";"prenom";"groupe";"ss_groupe";"pass";"email";"pseudo_spip"</strong>
<br>
L'utilisation de cette premi&egrave;re ligne permet de pouvoir mettre les colonnes dans n'importe quel ordre dans votre fichier CSV, la moulinette fonctionnera quand m&ecirc;me.

<p class="spip"><strong class="spip">D&eacute;tails&nbsp;:</strong></p>

<div style="border: 1px solid rgb(255, 255, 255); font-size: 85%;">
<ul class="spip"><li class="spip"> <strong class="spip">"login"</strong> = obligatoire (le login dans spip). Attention&nbsp;: le login est sensible &agrave; la casse (Majuscules/minuscules).</li><li class="spip"> <strong class="spip">"prenom"</strong>&nbsp;: facultatif</li><li class="spip"> <strong class="spip">"groupe"</strong> = le groupe principal de chaque utilisateur  ("PROFS" ou "ELEVES" pour IACA). Ce champ permet de s&eacute;parer les utilisateurs qui seront <strong class="spip">r&eacute;dacteurs</strong> (groupe REDACTEUR par d&eacute;faut) de ceux qui seront <strong class="spip">administrateurs de rubriques</strong> (groupe ADMINS par d&eacute;faut) ou <strong class="spip">visiteurs</strong> (groupe VISITEURS par d&eacute;faut). Si ce champs est vide, les utilisateurs seront r&eacute;dacteurs.</li><li class="spip"> <strong class="spip">"ss_groupe"</strong>&nbsp;: le sous-groupe<ul class="spip"><li class="spip"> pour les auteurs et les visiteurs&nbsp;: facultatif si l'on n'utilise pas la g&eacute;n&eacute;ration des groupes d'acc&egrave;s <strong class="spip">acces_groupes</strong>.</li><li class="spip"> pour les administrateurs c'est le nom de la rubrique qu'ils administreront. </li></ul></li><li class="spip"> <strong class="spip">"pass"</strong>&nbsp;: le mot de passe (si il est vide, le login sera utilis&eacute; comme mot de passe)</li><li class="spip"> <strong class="spip">"email"</strong>&nbsp;: facultatif, n&eacute;cessaire si on souhaite que les utilisateurs aient leur mail d&eacute;clar&eacute; dans SPIP</li><li class="spip"> <strong class="spip">"pseudo_spip"</strong>&nbsp;: facultatif, permet de sp&eacute;cifier un nom d'auteur SPIP diff&eacute;rent de celui compos&eacute; automatiquement par "prenom LOGIN"</li></ul>

<ul class="spip"><li class="spip"> <strong class="spip">s&eacute;parateur de champ&nbsp;:</strong> <strong class="spip">&nbsp;;</strong> (point-virgule)</li><li class="spip"> valeurs encadr&eacute;es par des <strong class="spip">"</strong> (guillemets doubles) (vous n'&ecirc;tes pas oblig&eacute; d'encadrer les valeurs par des " mais si vous voulez &eacute;viter les probl&egrave;mes, c'est plus s&ucirc;r...)</li><li class="spip"> s&eacute;parateur de ligne&nbsp;: \r\n (sauts de lignes utilis&eacute; par OOo Calc par d&eacute;faut) sous Windows, \n sous Linux (dans les 2 cas c'est le s&eacute;parateur standard du syst&egrave;me, &agrave; priori vous n'avez pas &agrave; vous en soucier) 
</li></ul></div>

<p class="spip"><strong class="spip">Remarques&nbsp;:</strong></p>

<ul class="spip"><li class="spip"> Le champ <strong class="spip">ss_groupe</strong> est <strong class="spip">obligatoire</strong> si on veut la cr&eacute;ation automatique des rubriques par sous-groupe et que les membres du sous-groupe en soient administrateurs. 
<br>Il est &eacute;galement requis si l'on veut que les utilisateurs cr&eacute;&eacute;s soient automatiquement int&eacute;gr&eacute;s dans un groupe <strong class="spip">acces_groupes</strong>. Cette option permet en effet de cr&eacute;er les groupes pour le plugin acces_groupes et d'int&eacute;grer les utilisateurs dedans en fonction du contenu du champ ss_groupe.</li><li class="spip"> On suppose que la gestion des doublons de noms est assur&eacute;e en amont&nbsp;: si vous cr&eacute;ez le fichier csv &agrave; la main, vous devrez vous assurer que chaque utilisateur &agrave; un nom unique&nbsp;!</li><li class="spip"> sp&eacute;cifique IACA&nbsp;: si les profs ne sont pas regroup&eacute;s par discipline dans IACA (en tant que sous-groupes) il faudra &eacute;diter le fichier avec un tableur (OOo Calc par ex) pour ajouter celles-ci dans la colonne sous-groupe. En revanche, si les groupes de disciplines sont g&eacute;n&eacute;r&eacute;s par IACA, il faudra &eacute;diter le fichier et faire un "Rechercher / Remplacer" pour supprimer les pr&eacute;fixes "D_" qui pr&eacute;c&egrave;dent chaque nom de groupe de profs afin d'&eacute;viter que les rubriques de disciplines dans le SPIP n'aient ce pr&eacute;fixe.</li></ul>

<p class="spip"><br style="clear: both;"></p>
</div>

<h3>Mod&egrave;le de fichier CSV :</h3>

