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
en s&eacute;lectionnant le format .csv ou .txt comme format d'enregistrement. Vu qu'il s'agit d'un format texte, il est &eacute;galement 
possible de le cr&eacute;er/modifier avec un simple &eacute;diteur de texte (bloc-note par ex).<br>
<br>
A partir de la version 2.2 il FAUT ajouter une <strong>ligne en tête du fichier</strong> qui permet de rep&eacute;rer les donn&eacute;es de chaque colonne. 
Les noms de champs qui doivent apparaître dans cette premi&egrave;re ligne sont les suivants : 
<br>
<strong>"login";"prenom";"groupe";"ss_groupe";"pass";"email";"pseudo_spip"</strong>
<br>
L'utilisation de cette premi&egrave;re ligne permet de pouvoir mettre les colonnes dans n'importe quel ordre dans votre fichier CSV, la moulinette fonctionnera quand même.

<ul>
<li><strong>D&eacute;tails des 7 colonnes</strong> :
		<ul>
				<li>"<strong>login</strong>" = obligatoire (le login dans spip). Attention : le login est sensible &agrave; la casse (Majuscules/minuscules).</li>
				<li>"<strong>prenom</strong>" : facultatif
				<li>"<strong>groupe</strong>" =  le groupe principal de chaque utilisateur  ("PROFS" ou "ELEVES" pour IACA). 
						 Ce champ permet de s&eacute;parer les utilisateurs qui seront r&eacute;dateurs (groupe ELEVES par d&eacute;faut mais REDACTEUR sera plus adapt&eacute; si vous n'avez pas de contraintes li&eacute;es &agrave; l'import d'un fichier venant d'une autre application) 
						 de ceux qui seront administrateurs de rubriques (groupe PROFS par d&eacute;faut). 
						 Si ce champs est vide, les utilisateurs seront r&eacute;dacteurs.</li>
				<li>"<strong>ss_groupe</strong>" :
						<ul>
								<li> pour les &eacute;l&egrave;ves = la classe (facultatif)</li>
								<li>pour les administrateurs c'est le nom de la rubrique qu'ils administreront. 
										Sous IACA : la discipline pour les profs. 
										Ce champ est obligatoire si on veut la cr&eacute;ation automatique des rubriques par sous-groupe et que les membres du sous-groupe en soient administrateurs</li>
						</ul>
				</li>								
				<li>"<strong>pass</strong>" : obligatoire. Si ce champ est vide, <strong>le mot de passe sera le login</strong> de l'utilisateur</li>
				<li>"<strong>pseudo_spip</strong>" : facultatif, permet de sp&eacute;cifier un nom d'auteur SPIP diff&eacute;rent de celui compos&eacute; automatiquement par "pr&eacute;nom NOM"</li>
				<li>"<strong>email</strong>" : facultatif, n&eacute;cessaire si on souhaite que les utilisateurs aient leur mail d&eacute;clar&eacute; dans SPIP</li>
		</ul>
</li>
<li>s&eacute;parateur de champ: ; (point-virgule)</li>
<li>valeurs encadr&eacute;es par des " (guillemets doubles) 
		(vous n'êtes pas oblig&eacute; d'encadrer les valeurs par des " mais si vous voulez &eacute;viter les probl&egrave;mes, c'est plus sûr...)</li>
<li>s&eacute;parateur de ligne: \r\n (sauts de lignes utilis&eacute; par OOo Calc par d&eacute;faut) sous Windows, \n sous Linux 
		(dans les 2 cas c'est le s&eacute;parateur standard du syst&egrave;me, &agrave; priori vous n'avez pas &agrave; vous en soucier)
</ul>
<h3>Remarques :</h3>
On suppose que la gestion des doublons de noms est assur&eacute;e par IACA : si vous cr&eacute;ez le fichier csv &agrave; la main, vous devrez vous assurer que chaque utilisateur &agrave; un nom unique !<br>
Si les profs ne sont pas regroup&eacute;s par discipline dans IACA (en tant que sous-groupes) il faudra &eacute;diter le fichier avec un tableur (OOo Calc par ex)
pour ajouter celles-ci dans la colonne sous-groupe. En revanche, si les groupes de disciplines sont g&eacute;n&eacute;r&eacute;s par IACA, il faudra &eacute;diter 
le fichier et faire un "Rechercher / Remplacer" pour supprimer les pr&eacute;fixes "D_" qui pr&eacute;c&egrave;dent chaque nom de groupe de profs afin d'&eacute;viter que les
rubriques de disciplines dans le SPIP n'aient ce pr&eacute;fixe.

<h3>Mod&egrave;le de fichier CSV :</h3>

