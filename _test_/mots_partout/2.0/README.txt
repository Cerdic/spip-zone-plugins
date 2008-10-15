Auteur: Pierre Andrews (aka Mortimer) mortimer.pa@free.fr
Licence: GPL

Description:

[fr] Page de l'interface privée pour pouvoir mettre des mots clefs sur
n'importe quelle "objet" spip. Pour plus d'info, voir la contrib sur
spip-contrib.net. L'interface etait destiné à l'origine à l'ajout de
mots clefs sur les documents et s'inspirait d'iPhoto. 

Utilisation 

L'interface est divisée en trois parties majeure:

la colonne du milieu

affiche une liste d'objets -- quand on parle d'objets ici, on se réfère aux articles, documents, etc... --. Cette liste, au départ contient tous les objets du type sélectionné (voir plus bas) paginés par page de 20.

le choix du type

par défaut, les objets affichés sont les articles. On peut changer cela en sélectionnant un nouveau type d'objet. Ici, seul les types configurés depuis la page de configuration sont affiché.

En changeant le type, la colonne du milieu affichera tous les objets du nouveau type, paginé par 20. On peut alors choisir une limitation à l'affichage et changer le nombre d'objets par page.

Chaque type a des limitations différentes, mais par exemple, pour les articles, on peut limiter la liste d'articles aux articles d'une certaine rubrique ou d'un certain auteur.

les actions

Cette partie est la plus importante de la page.

On voit la liste de mots-clefs (par groupe) qui sont associable au type d'objet sélectionné. Les mots-clefs sont colorés de trois façons:

    * en gris s'ils ne sont associés à aucun objet affiché dans la liste au milieu
    * en bleu s'ils sont associés à certain (mais pas tous) des objets dans la liste
    * en vert s'ils sont associés à tous les objets dans la liste du milieu 

On peut sélectionner différente action pour chaque mot-clef:

    * voir: affichera les objets qui ont ce mot-clef
    * cacher: exclura de la liste les objets qui ont ce mot-clef
    * ajouter: ajoutera ce mot-clef à tous les objets sélectionnés dans la liste
    * enlever: enlever ce mot-clef de tous les objets sélectionnés dans la liste 

Une fois que vous avez sélectionné l'action pour tous les mots-clefs qui vous interessent, il suffit de valider. Si vous avez sélectionné voir ou cacher, alors la liste à droite affichera les objets correspondant. Si vous avez sélectionné ajouter ou enlever, la liste affichera les objets sur lesquels vous venez de faire une modification. 

[en] Interface for the private part of a SPIP web site to put tags on
any "object". The interface was primarilly intended for using keywords
on documents and is inspired from iPhoto.

Listes des fichiers:

[fr]
README.txt vous y etes,
mots_partout.php nouvelle page pour l'interface,
motspartout_en.php fichier de localisation en anglais,
motspartout_fr.php fichier de localisation en francais,
motspartout_it.php fichier de localisation en italien,
_REGLES_DE_COMMIT.txt régles pour faire évoluer cette contrib,
TODO.txt ce qu'il reste à faire,
BUG.txt les bugs connus.

[en]
README.txt you are here <-,
mots_partout.php the main interface,
motspartout_en.php localisation in english,
motspartout_fr.php localisation in french,
motspartout_it.php localisation en italian,
_REGLES_DE_COMMIT.txt rules of contribution on this project,
TODO.txt what remains to do,
BUG.txt the known bugs.

Page de la contrib:
http://spip-contrib.net/ecrire/articles.php3?id_article=905


