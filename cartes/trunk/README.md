#Cartes - un générateur de cartes dynamiques "simples""

Ce plugin pour [SPIP](https://www.spip.net) ajoute un objet "carte" (au même titre que les articles, les rubriques, les auteurs ...) et permet de configurer des cartes dans les moindres détails (du moins au maximum).


## Les plugins connexes

### Obligatoires

#### GIS

Plugin central et primordial, la visualisation de l'objet "carte" consiste à affichier le modèle ```carte_gis``` de ce plugin préconfiguré tout en y ajoutant quelques fonctionnalités.

#### GIS géométries

Ce plugin permet de dessiner des éléments sur une carte en plus du simple affichage de POIs.

Il est obligatoire car il permet de spécifier les bornes maximales d'usage de la carte lors de sa configuration.

#### Rôles GIS

Permet d'ajouter des rôles aux points et autres objets GIS (markers, polygones...).

Il nous permet ici de spécifier si l'objet est simplement informatif (s'affiche sur la carte sans ouvrir de popup) ou pas.

Ce plugin nécessite le plugin rôles qui lui fournit l'API nécessaire.

#### Saisies

Permet de gérer les champs du formulaire

### Facultatifs 

#### Licence

Permet de stipuler, pour le moment, une licence globale sur les cartes.

## Éléments pouvant différencier des cartes

### Le titre

Il est possible de spécifier pour chaque carte un titre.

### Le texte

Pour chaque carte le texte est modifiable.

### Fond de carte

Chaque carte peut utiliser un fond de carte spécifique, configurable à partir de la liste de base des fonds de carte disponible dans le plugin GIS.

Il est également possible de ne pas utiliser de Tiles (fond de carte à proprement dit) du tout.

### Fond de carte topojson

Pouvoir utiliser une couche topojson comme fond de carte (les fichiers doivent être dans un répertoire topojson_carte/ et avoir comme extension .topojson).

Ce fichier peut être choisi via un sélecteur dans la configuration de la carte.

### Zoom par défaut et limitation de niveau de zoom

Chaque carte a son zoom par défaut personnel.

On peut également fixer ses zooms minimal et maximal qui permettent de limiter l'usage du zoom.

### Bornes maximales (ou bounds)

Une carte peut se limiter à une zone précise, il n'est pas forcément utile de permettre à l'internaute de naviguer sur l'ensemble du globe alors que les informations sont uniquement ciblées sur une partie concrète.

Il est donc possible de dessiner un rectangle permettant de limiter l'usage d'une carte à une zone spécifique.

### Les contrôles affichés sur la carte

Un sélecteur via checkbox permet de sélectionner quels controles supplémentaires sont affichés sur la carte parmis les choix suivants :

* ```fullscreen``` : affiche un bouton permettant de passer la carte en plein écran;
* ```scale``` : affiche l'échelle de la carte;
* ```overview``` : affiche une mini carte d'aperçu;

### La manière dont est affiché le contenu des POIs

Le contenu des POIs peut être affiché de deux manières différentes :

* ```openpopup```, le click sur le POI affichera une popup (fonctionnement par défaut et généralement utilisé);
* ```control```, le click sur le POI s'affiche dans un cadre séparé sur la carte (un control Leaflet);

### POI(s) associé(s)

Chaque carte dispose de ses propres POIs ou autres objets GIS (polygones, lignes...).

Chaque objet peut avoir un des deux rôles suivant :

* ```informatif```, aucun popup ne sera ouvert lors du click, un label inamovible affiche son titre. 
* ```action```, les informations du POI seront affichées comme configuré dans *La manière dont est affiché le contenu des POIs*

### Styles graphiques

### Pied de carte



