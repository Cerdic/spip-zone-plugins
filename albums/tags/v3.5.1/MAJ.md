Plugin Albums : mises à jour
============================

Notes sur certaines mises-à-jour, notamment sur les ruptures de compatibilité et comment vivre avec.

## V3.0

### Suppression du critère {contenu}
A la place, il suffit de faire des boucles avec une jointure : `<BOUCLE_x(ALBUMS documents)>`.
Du coup, on peut employer le critère {media} pour le même résultat, ainsi que d'autres critère des documents, et on peut utiliser des opérateurs.

 - `{media = x}` remplace `{contenu x}` : albums contenant un type de media.
 - `{extension == x|y}` : albums contenant les extensions x ou y.
 - `{id_document != x}` : albums ne contenant pas un document donné.
 - `{fichier LIKE %x%}` : albums contenant un fichier correspondant au terme x.

### Suppression de fonctions obsolètes
Les fonctions suivantes étaient utilisées dans certains squelettes de l'espace privé. Elles renvoyaient directement des tags html (bouh!) et étaient d'une utilité limitée.

 - `album_determiner_contenu()`
 - `filtre_album_contenu()`
 - `filtre_album_liaison()`

### Actions

 - ajout : `vider_album`
 - ajout : `supprimer_album`
 - suppression : `editer_liens_album`

### Formulaire édition

 - Le titre n'est plus obligatoire, afin de pouvoir créer des albums rapidement.
 - Plus de `&id_album=x` ajouté à l'URL après redirection.

### Formulaire d'ajout d'album à un objet
Nouveau formulaire `ajouter_album` qui permet d'ajouter un album à un objet en 1 étape : c'est un mélange de `editer_album` + `joindre_document`.
On peut soit créer et remplir un nouvel album, soit en choisir un existant.

### Formulaire pour générer un balise `<album>`
Nouveau formulaire inspiré du plugin «Insérer modèles» pour personnaliser une balise `<album>` à insérer dans un texte.
Les fichiers yaml des modèles sont compatibles.

### Formulaire de déplacements de documents
Révision du code JS. On ne l'affiche que si on est autorisé.

### Albums liés en mode édition
On affiche les albums également en mode édition d'un objet, de la même façon que les documents.
On peut ajouter à un album à un objet en cours de création, en utilisant le même hack que le plugin «Médias» (identifiant négatif temporaire).

### Modèles
Révision des modèles et de leurs paramètres. On part sur l'idée d'avoir des modèles minimalistes de base.
Par défaut, on n'affiche que le strict minimum : pas de titre, ni de descriptif, ni rien. Pour étoffer l'affichage, il faut passer des paramètres.
Révision du markup pour avoir des `<figure>` conforme, cf. [l'exemple de tiny typo](http://tinytypo.tetue.net/tinytypo.html#album)
Les modèles sont rendus compatibles avec la syntaxe de la version 1 : `<album|id_article=x>` et `<album|id=1,2,3>`

### Autorisations

 - Renommage de `associeralbum` en `album_associer`
 - Ajout de : `album_dissocier`, `ajouteralbum`, `deplacerdocumentsalbums`
 - Modification (pour la bonne cause) de : `album_modifier` et `album_supprimer`

### Boîte latérale «utilisations»
Déplacement du squelette de «navigation» vers «extra». On ne l'affiche pas en mode édition, + pétouilles.

### Configuration : téléversement des documents.
Message d'erreur si le téléversement n'est pas activé pour les albums, dans la configuration des documents et celle des albums.
Cf. pipeline `formulaire_traiter`.

### Compagnons
Ils sont de retours.

### Albumothèque
Refonte de l'interface : filtres latéraux.

### Chaînes de langue
Suppression de chaînes obsolètes, ajout de nouvelles.
