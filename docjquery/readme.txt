EN PLEIN CHANTIER : POUR L'INSTANT CE CODE N'EST PAS FONCTIONNEL

IDEE : faciliter la traduction de la doc de jquery

Chez jquery, il y a un fichier xml contenant toute la doc en anglais, et
qu'il faut considérer comme fichier de référence
L'idée est donc de prendre chaque bloc de cette doc, et d'en faire un bloc
identique dans une autre langue (fr par exemple), puis de générer un xml
correspondant.
Chacun de ces bloc peut alors être visualisé dans une interface pour faciliter
la traduction initiale et les mises à jour.

Pour ça, il faut être capable de lister l'état de la traduction, et il faut
régulièrement reprendre le fichier de référence et en déduire la liste des
changements :
- mises à jour : l'idée est de comparer chaque bloc du nouveau fichier à ce
  qu'on a en stock
  - un bloc n'est pas en stock : il est nouveau, il faut l'insérer, à l'état
    "nouveau"
  - un bloc existe et il est identique : il est à jour, à l'état "à jour"
  - un bloc existe mais est différent : il est à mettre à jour, à l'état
    "a revoir"
  - à la fin, les blocs restants sont à passer à l'état "à supprimer" (mais
    à garder comme source puisqu'il peut s'agir d'un bloc dont la signature
    à changé)

- état des traductions
  - un bloc existe en EN mais pas en FR : il est a créer à l'état "a traduire"
  - le bloc existe des 2 cotés et la date EN est antérieure à la date FR :
    il est "à jour" ou "en cours", selon l'avis des traducteurs, qu'il faut
    donc pouvoir indiquer dans un coin
  - le bloc existe des 2 cotés et la date EN est postérieure : il est "à revoir"
  - un bloc existe en FR mais pas en EN : il est à passer à l'état "à supprimer"

On peut donc stocker tout ça dans une table ayant les colonnes
- id : pour avoir un id :-)
- nom, nombre de paramètres, paramètre : pour avoir la signature du bloc de doc
- date de dernière modif
- état : NEW=nouveau (pour les EN) ou à traduire (pour les autres) ou
  à revoir (modifié), TRV=travail= en cours de traduction, OK=à jour,
  SUP=à supprimer
- reference : id de l'enreg dont on est la trad, 0 pour les enreg EN.

- import initial :
  on prend chaque bloc du fichier EN et on crée autant d'enregistrements
  à l'état NEW
  pour chaque langue destination de la trad on crée des enregistrements
  vides, à l'état NEW.
- modifs des trads :
  quand quelqu'un travaille sur une trad, il édite le texte, ce qui le passe
  à l'état TRV. S'il considère que c'est au point, il passe à OK
- mise à jour :
  parcours du fichier EN, pour chaque bloc, on cherche son homologue et
  on met à jour son état ou on crée un nouveau en conséquence
  ensuite, on reporte ces modifs sur les trads

TODO ( '-' = à faire, '*' = fait)
* structure de données
  dans table.sql

* import et mise à jour
  php5 importer.php spip=/dir/de/spip
  => transformé en code dans le traitement de ?page=jq_admin

* un script (très simple, un insert/select) pour l'ajout d'une nouvelle
  langue
  php5 ajouterLangue.php spip=/dir/de/spip lang=LL
  => transformé en code dans le traitement de ?page=jq_admin

* squelettes permettant d'afficher l'état courant par langue (en/lang cote
  à cote + filtrage par etat)
  ?page=trads&lg=LL apres activation du plugin "docjquery"
  * "boucle xml" pour afficher comme sur le site d'origine
    + la version FR avec des blocs "editables"
  * action "save"
    recup de l'xml, de l'id et de la langue et maj en bdd de l'xml, le statut
    et la date
  * ajouter un truc pour spécifier un statut "en cours/terminé"

* squelette d'export xml d'une langue

- gestion de la version (tag dans l'xml à mettre en bdd pour reporter dans
  l'export)
- gestion de la trad des descriptions de type (pour l'instant dans un multi)
- traduire les mots dans la xsl (et les desc de types ?), via un dictionnaire
  spip
