Nom : ancres_intertitres
Version : 0.2
Version spip minimale requise : 1.9beta1

Objectif : ajouter des ancres html calculees en fonction du texte de l'intertitre. Fournir une balise pour afficher la "table des matieres" d'un article (contenu de #TEXTE)

Fonctionnement : 

- les intertitres ( notes {{{intertitre}}} ) seront transformes en <h3 class="spip"><a name="intertitre"></a>intertitre</h3>
- le contenu de l'attribut name est calcule selon une methode approchant celle des urls propres de spip. Aussi un intertitre {{{ceci est un intertitre spécial}}} sera transforme en <h3 class="spip"><a name="ceci-est-un-intertitre-special"></a>ceci est un intertitre spécial</h3>
- chaque intertitre est memorise avec son ancre correspondante pour etre reproduite dans la balise #TABLE_MATIERE

Installation :

- copier le repertoire ancres_intertitres dans le repertoire plugins de votre site spip.
- activer le plugin via l'interface

Utilisation : les intertitres seront calcules automatiquement. Pour afficher la table des matieres, ajouter la balise #TABLE_MATIERE dans votre squelette.

Note : si vous voulez afficher la table des matieres avant le #TEXTE, procedez de la facon suivante.

[(#TEXTE|?{#NOTES,''}|is_array)]
#TABLE_MATIERE
#TEXTE
#NOTES

(l'appel de #TEXTE calcule la table et les notes. L'astuce permet aussi d'eviter l'affichage des notes en double)

#TABLE_MATIERE affichera les ancres de la maniere suivante par defaut :

- <a href="lienA">intertitreA</a><br />
- <a href="lienB">intertitreB</a><br />

pour faire une belle noisettes
on peut parametrer la balise comme suit :

[<div id="table_matiere">
	<h2><:table_matiere:></h2>
	<ul>
		(#TABLE_MATIERE{<li>,</li>})
	</ul>
</div>]

ou <li> sera affiche avant chaque lien et </li> apres

ChangeLog :

2005-11-17 : version initiale 0.1
2006-03-11 : version 0.2, adaptation pour SPIP1.9b1 et parametrage du rendu
