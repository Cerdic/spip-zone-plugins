<?php
/* fichier help de csv2spip 
*          langue = fr
*/
?>

Ce fichier sera fabriqué à partir du fichier d'extraction des comptes de IACA ou d'un fichier CSV équivalent.
<h3>Format du fichier CSV utilisé :</h3> 
Un fichier CSV (Common Separator Value) correspond à un fichier tableur enregistré au format texte. 
Chaque ligne de ce fichier correspond à une ligne du tableur, les données des cellules de cette ligne étant séparées
par un séparateur (ici c'est le ";"). On peut donc fabriquer un tel fichier avec n'importe quel tableur (OOo Calc par ex)
en sélectionnant le format .csv ou .txt comme format d'enregistrement. Vu qu'il s'agit d'un format texte, il est également 
possible de le créer/modifier avec un simple éditeur de texte (bloc-note par ex).<br>
<br>
A partir de la version 2.2 il FAUT ajouter une <strong>ligne en tête du fichier</strong> qui permet de repérer les données de chaque colonne. 
Les noms de champs qui doivent apparaître dans cette première ligne sont les suivants : 
<br>
<strong>"login";"prenom";"groupe";"ss_groupe";"pass";"email";"pseudo_spip"</strong>
<br>
L'utilisation de cette première ligne permet de pouvoir mettre les colonnes dans n'importe quel ordre dans votre fichier CSV, la moulinette fonctionnera quand même.

<ul>
<li><strong>Détails des 7 colonnes</strong> :
		<ul>
				<li>"<strong>login</strong>" = obligatoire (le login dans spip). Attention : le login est sensible à la casse (Majuscules/minuscules).</li>
				<li>"<strong>prenom</strong>" : facultatif
				<li>"<strong>groupe</strong>" =  le groupe principal de chaque utilisateur  ("PROFS" ou "ELEVES" pour IACA). 
						 Ce champ permet de séparer les utilisateurs qui seront rédateurs (groupe ELEVES par défaut mais REDACTEUR sera plus adapté si vous n'avez pas de contraintes liées à l'import d'un fichier venant d'une autre application) 
						 de ceux qui seront administrateurs de rubriques (groupe PROFS par défaut). 
						 Si ce champs est vide, les utilisateurs seront rédacteurs.</li>
				<li>"<strong>ss_groupe</strong>" :
						<ul>
								<li> pour les élèves = la classe (facultatif)</li>
								<li>pour les administrateurs c'est le nom de la rubrique qu'ils administreront. 
										Sous IACA : la discipline pour les profs. 
										Ce champ est obligatoire si on veut la création automatique des rubriques par sous-groupe et que les membres du sous-groupe en soient administrateurs</li>
						</ul>
				</li>								
				<li>"<strong>pass</strong>" : obligatoire. Si ce champ est vide, <strong>le mot de passe sera le login</strong> de l'utilisateur</li>
				<li>"<strong>pseudo_spip</strong>" : facultatif, permet de spécifier un nom d'auteur SPIP différent de celui composé automatiquement par "prénom NOM"</li>
				<li>"<strong>email</strong>" : facultatif, nécessaire si on souhaite que les utilisateurs aient leur mail déclaré dans SPIP</li>
		</ul>
</li>
<li>séparateur de champ: ; (point-virgule)</li>
<li>valeurs encadrées par des " (guillemets doubles) 
		(vous n'êtes pas obligé d'encadrer les valeurs par des " mais si vous voulez éviter les problèmes, c'est plus sûr...)</li>
<li>séparateur de ligne: \r\n (sauts de lignes utilisé par OOo Calc par défaut) sous Windows, \n sous Linux 
		(dans les 2 cas c'est le séparateur standard du système, à priori vous n'avez pas à vous en soucier)
</ul>
<h3>Remarques :</h3>
On suppose que la gestion des doublons de noms est assurée par IACA : si vous créez le fichier csv à la main, vous devrez vous assurer que chaque utilisateur à un nom unique !<br>
Si les profs ne sont pas regroupés par discipline dans IACA (en tant que sous-groupes) il faudra éditer le fichier avec un tableur (OOo Calc par ex)
pour ajouter celles-ci dans la colonne sous-groupe. En revanche, si les groupes de disciplines sont générés par IACA, il faudra éditer 
le fichier et faire un "Rechercher / Remplacer" pour supprimer les préfixes "D_" qui précèdent chaque nom de groupe de profs afin d'éviter que les
rubriques de disciplines dans le SPIP n'aient ce préfixe.

<h3>Mod&egrave;le de fichier CSV :</h3>

