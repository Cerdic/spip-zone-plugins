Macros
======

Ce plugin propose un système de macros pour les squelettes SPIP. C'est une
façon d'étendre le mécanisme de squelettes, et de contourner certaines de
ses limitations.

L'idée m'est venue à force de penser que souvent certains squelettes se
ressemblent, et qu'il est parfois difficile de ne pas se répéter, ce qui
en plus d'être un peu ennuyeux n'est pas top en terme de maintenabilité.
Par exemple, pour des boucles sur des objets éditoriaux, on fait parfois la
même boucle sur deux tables différentes, mais on est obligé d'écrire deux
boucle. En hyper-condensé, ça donnerait :

    <BOUCLE_livres(LIVRES){par date}{id_rubrique}>
      #TITRE - #DATE
    </BOUCLE_livres>

    <BOUCLE_disques(DISQUES){par date}{id_rubrique}>
      #TITRE - #DATE
    </BOUCLE_disques>

Alors qu'on pourrait définir la boucle une fois pour toutes, et ensuite
l'appliquer à un objet éditorial arbitraire.
Ce plugin propose de faire ceci en utilisant PHP pour générer des squelettes.
On pourrait alors ecrire le squelette ci-dessus de la façon suivante :

    <BOUCLE_objet(<?php echo $objet; ?>){par date}{id_rubrique}>
      #TITRE - #DATE
    </BOUCLE_objet>

et simplement passer des valeurs de `$objet` différentes à la boucle.

La balise #MACRO
----------------

Une macro est simplement un fichier php (avec l'extension .php) qui sert à
générer des squelettes, comme dans l'exemple ci-dessus.
Ces fichiers sont évalués par la balise `#MACRO`, qui retourne un nom de
fichier prêt à être utilisé dans `#INCLURE`. Ce fichier contient le
résultat de la macro. On peut alors s'en servir tel quel, ou alors comme
base à personnaliser.

En plus du nom de la macro, on peux lui passer un contexte, sous la forme
d'un tableau `nom_variable => valeur_variable`. L'exemple ci-dessus
devriendrait alors :

    #INCLURE{fond=#MACRO{liste_titre, #ARRAY{objets, #ARRAY{0,LIVRES, 1, DISQUES}}}}

et on aurait alors un fichier `liste_titre.php` qui contiendrait :

    <?php foreach ($objets as $i => $objet): ?>

      <BOUCLE_<?php echo $i; ?>(<?php echo $objet; ?>){par date}{id_rubrique}>
        #TITRE - #DATE
      </BOUCLE_<?php echo $i; ?>>

    <?php endforeach; ?>

Le cache peut-être désactivé par la constante `_NO_CACHE`, qui
désactive aussi le cache des squelettes, ou alors par la constante
`_NO_MACRO_CACHE`. Les macros seront alors ré-évaluées à chaque appel.
