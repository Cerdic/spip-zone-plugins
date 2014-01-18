# Infographies

Plugin SPIP ajoutant la prise en compte de certains objets nécessaires pour la création d'infographies dynamiques ou pas depuis SPIP.

## Description technique

### Tables sql

Quatre tables SQL sont créées en utilisant les pipelines de l'API de création d'objets de SPIP. Elles peuvent donc êtres modifiées facilement.

#### spip_infographies

Cette table définit les différentes infographies (du coup on peut en avoir plusieurs différentes).
  
Cette table est définie comme éditable dans l'API, on peut donc accéder aux pages :

* *ecrire/?exec=infographies* listant toutes les infographies
* *ecrire/?exec=infographie_edit&new=oui* permettant de créer une nouvelle infographie
* *ecrire/?exec=infographie&id_infographie=1* affichant le contenu d'une infographie dans l'espace privé.

** Ses informations sont définies sous la forme :**

* *id_infographie* : l'identifiant numérique de l'infographie
* *titre* : le titre de l'infographie
* *texte* : le texte de l'infographie
* *credits* : les crédits de l'infographie
* *date* : la date de l'infographie
* *statut* : le statut de l'infographie (statuts similaires aux articles)
* *maj* : la date de mise à jour
  
#### spip_infographies_datas

Table définissant les jeux de données liés à une infographie ou plusieurs infographies (On utilise la table spip_infographies_datas_liens pour faire les liens entre jeux de données et infographies).

Cette table est définie comme éditable dans l'API, on peut donc accéder aux pages :

* *ecrire/?exec=infographies_datas* listant tous les jeux de données
* *ecrire/?exec=infographies_data_edit&new=oui* permettant de créer un nouveau jeu de données
* *ecrire/?exec=infographies_data&id_infographies_data=1* affichant le contenu d'un jeu de données dans l'espace privé.
  
**Ses informations sont définies sous la forme :**

* *id_infographies_data* : l'identifiant numérique du jeu de donnée
* *titre* : le titre du jeu de donnée
* *texte* : le texte du jeu de donnée
* *css_class* : class css du jeu de donnée (pour être utilisé dans une visualisation)
* *axe_x* : le label de l'axe horizontal de l'infographie
* *axe_y* : le label de l'axe vertical de l'infographie
* *unite* : unité de mesure du jeu de donnée (pour être utilisé dans une visualisation)
* *credits* : les crédits du jeu de donnée
* *type* : le type du jeu de donnée
* *url* : URL distante du jeu de donnée si externe
* *date* : la date du jeu de donnée
* *maj* : la date de mise à jour
  

#### spip_infographies_donnees

Table définissant les données en base liées à un jeu de donnée.
  
Cette table est indiquée dans l'API comme non éditable, elle ne dispose donc pas de page d'édition spécifique.

Les données s'éditent depuis la page de l'infographie.
  
Chaque ligne se présente sous la forme :

* *id_infographies_donnee* : l'identifiant numérique de la donnée
* *id_infographies_data* : à quelle infographie est liée cette donnée
* *rang* : la place de la donnée dans le jeu
* *axe_x* : valeur de cette donnée sur l'axe X
* *axe_y* : valeur de cette donnée sur l'axe Y
* *commentaire* : un commentaire qui peut être utilisé ensuite dans l'infographie
* *date* : date de la donnée
* *maj* : date de mise à jour de la donnée


#### spip_infographies_datas_liens

Table de jointures pour les jeux de données.

Les jeux de données pouvant être liés à plusieurs infographies différentes par exemple.
  
Elle est composée des champs suivants :

* *id_infographies_data* : l'identifiant numérique du jeu de données lié
* *id_objet* : l'identifiant numérique de l'objet lié
* *objet* : le type d'objet lié


### Formulaires

#### Formulaire d'édition d'infographie

Le formulaire d'édition d'infographie permet de modifier son titre, son texte et les crédits associés.

On l'appelle comme ceci :

	[(#FORMULAIRE_EDITER_INFOGRAPHIE{#ID_INFOGRAPHIE,#ENV{redirect}})]

  
## Utilisation

### Étape 1 : création de l'infographie

En premier lieu, on crée une infographie en lui donnant les informations nécessaires.

### Étape 2 : créer un ou plusieurs jeu de données

On crée ensuite un jeu de données que l'on associe à l'infographie créée. L'association se fait sur la page de l'infographie puisqu'un même jeu de données pourrait être utilisé par plusieurs infographies différentes.

### Étape 3 : remplir le ou les jeux de données

On ajoute des données au jeu de données.

Les données sont ajoutées directement sur la page du jeu de données.

Cet ajout peut être fait via téléchargement d'un fichier CSV (pour l'instant seul ce format est utilisable).

Les données peuvent être supprimées toutes en même temps ou une à une.


**Formatage du fichier CSV**

Le fichier CSV doit avoir des données sur deux ou trois colonnes.

* La première colonne est considérée comme valeurs de l'axe horizontal;
* La seconde colonne est consdidérée comme valeurs de l'axe vertical;
* La troisième, quant à elle est facultative, elle correspond aux commentaires potentiels de chaque valeur;

Lors de l'insertion depuis un fichier CSV, on vérifie :

* si la première ligne est exclusivement composée de valeurs non numériques, cette ligne est considérée comme légende, on utilise ces valeurs pour remplir ou remplacer les valeurs *axe_x* et *axe_y* du jeu de donnée lié;



