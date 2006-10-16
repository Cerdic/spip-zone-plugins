<?php
/* fichier help de csv2spip 
*          langue = fr
*/
?>

Ce fichier sera fabriqu� � partir du fichier d'extraction des comptes de IACA ou d'un fichier CSV �quivalent.
<h3>Format du fichier CSV utilis� :</h3> 
Un fichier CSV (Common Separator Value) correspond � un fichier tableur enregistr� au format texte. 
Chaque ligne de ce fichier correspond � une ligne du tableur, les donn�es des cellules de cette ligne �tant s�par�es
par un s�parateur (ici c'est le ";"). On peut donc fabriquer un tel fichier avec n'importe quel tableur (OOo Calc par ex)
en s�lectionnant le format .csv ou .txt comme format d'enregistrement. Vu qu'il s'agit d'un format texte, il est �galement 
possible de le cr�er/modifier avec un simple �diteur de texte (bloc-note par ex).<br>
<br>
A partir de la version 2.2 il FAUT ajouter une <strong>ligne en t�te du fichier</strong> qui permet de rep�rer les donn�es de chaque colonne. 
Les noms de champs qui doivent appara�tre dans cette premi�re ligne sont les suivants : 
<br>
<strong>"login";"prenom";"groupe";"ss_groupe";"pass";"email";"pseudo_spip"</strong>
<br>
L'utilisation de cette premi�re ligne permet de pouvoir mettre les colonnes dans n'importe quel ordre dans votre fichier CSV, la moulinette fonctionnera quand m�me.

<ul>
<li><strong>D�tails des 7 colonnes</strong> :
		<ul>
				<li>"<strong>login</strong>" = obligatoire (le login dans spip). Attention : le login est sensible � la casse (Majuscules/minuscules).</li>
				<li>"<strong>prenom</strong>" : facultatif
				<li>"<strong>groupe</strong>" =  le groupe principal de chaque utilisateur  ("PROFS" ou "ELEVES" pour IACA). 
						 Ce champ permet de s�parer les utilisateurs qui seront r�dateurs (groupe ELEVES par d�faut mais REDACTEUR sera plus adapt� si vous n'avez pas de contraintes li�es � l'import d'un fichier venant d'une autre application) 
						 de ceux qui seront administrateurs de rubriques (groupe PROFS par d�faut). 
						 Si ce champs est vide, les utilisateurs seront r�dacteurs.</li>
				<li>"<strong>ss_groupe</strong>" :
						<ul>
								<li> pour les �l�ves = la classe (facultatif)</li>
								<li>pour les administrateurs c'est le nom de la rubrique qu'ils administreront. 
										Sous IACA : la discipline pour les profs. 
										Ce champ est obligatoire si on veut la cr�ation automatique des rubriques par sous-groupe et que les membres du sous-groupe en soient administrateurs</li>
						</ul>
				</li>								
				<li>"<strong>pass</strong>" : obligatoire. Si ce champ est vide, <strong>le mot de passe sera le login</strong> de l'utilisateur</li>
				<li>"<strong>pseudo_spip</strong>" : facultatif, permet de sp�cifier un nom d'auteur SPIP diff�rent de celui compos� automatiquement par "pr�nom NOM"</li>
				<li>"<strong>email</strong>" : facultatif, n�cessaire si on souhaite que les utilisateurs aient leur mail d�clar� dans SPIP</li>
		</ul>
</li>
<li>s�parateur de champ: ; (point-virgule)</li>
<li>valeurs encadr�es par des " (guillemets doubles) 
		(vous n'�tes pas oblig� d'encadrer les valeurs par des " mais si vous voulez �viter les probl�mes, c'est plus s�r...)</li>
<li>s�parateur de ligne: \r\n (sauts de lignes utilis� par OOo Calc par d�faut) sous Windows, \n sous Linux 
		(dans les 2 cas c'est le s�parateur standard du syst�me, � priori vous n'avez pas � vous en soucier)
</ul>
<h3>Remarques :</h3>
On suppose que la gestion des doublons de noms est assur�e par IACA : si vous cr�ez le fichier csv � la main, vous devrez vous assurer que chaque utilisateur � un nom unique !<br>
Si les profs ne sont pas regroup�s par discipline dans IACA (en tant que sous-groupes) il faudra �diter le fichier avec un tableur (OOo Calc par ex)
pour ajouter celles-ci dans la colonne sous-groupe. En revanche, si les groupes de disciplines sont g�n�r�s par IACA, il faudra �diter 
le fichier et faire un "Rechercher / Remplacer" pour supprimer les pr�fixes "D_" qui pr�c�dent chaque nom de groupe de profs afin d'�viter que les
rubriques de disciplines dans le SPIP n'aient ce pr�fixe.

<h3>Mod&egrave;le de fichier CSV :</h3>

