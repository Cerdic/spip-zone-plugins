EN PLEIN CHANTIER : POUR L'INSTANT CE CODE N'EST PAS FONCTIONNEL

IDEE : faciliter la traduction de la doc de jquery

Chez jquery, il y a un fichier xml contenant toute la doc en anglais, et
qu'il faut consid�rer comme fichier de r�f�rence
L'id�e est donc de prendre chaque bloc de cette doc, et d'en faire un bloc
identique dans une autre langue (fr par exemple), puis de g�n�rer un xml
correspondant.
Chacun de ces bloc peut alors �tre visualis� dans une interface pour faciliter
la traduction initiale et les mises � jour.

Pour �a, il faut �tre capable de lister l'�tat de la traduction, et il faut
r�guli�rement reprendre le fichier de r�f�rence et en d�duire la liste des
changements :
- mises � jour : l'id�e est de comparer chaque bloc du nouveau fichier � ce
  qu'on a en stock
  - un bloc n'est pas en stock : il est nouveau, il faut l'ins�rer, � l'�tat
    "nouveau"
  - un bloc existe et il est identique : il est � jour, � l'�tat "� jour"
  - un bloc existe mais est diff�rent : il est � mettre � jour, � l'�tat
    "a revoir"
  - � la fin, les blocs restants sont � passer � l'�tat "� supprimer" (mais
    � garder comme source puisqu'il peut s'agir d'un bloc dont la signature
    � chang�)

- �tat des traductions
  - un bloc existe en EN mais pas en FR : il est a cr�er � l'�tat "a traduire"
  - le bloc existe des 2 cot�s et la date EN est ant�rieure � la date FR :
    il est "� jour" ou "en cours", selon l'avis des traducteurs, qu'il faut
    donc pouvoir indiquer dans un coin
  - le bloc existe des 2 cot�s et la date EN est post�rieure : il est "� revoir"
  - un bloc existe en FR mais pas en EN : il est � passer � l'�tat "� supprimer"

On peut donc stocker tout �a dans une table ayant les colonnes
- id : pour avoir un id :-)
- nom, nombre de param�tres, param�tre : pour avoir la signature du bloc de doc
- date de derni�re modif
- �tat : NEW=nouveau (pour les EN) ou � traduire (pour les autres) ou
  � revoir (modifi�), TRV=travail= en cours de traduction, OK=� jour,
  SUP=� supprimer
- reference : id de l'enreg dont on est la trad, 0 pour les enreg EN.

- import initial :
  on prend chaque bloc du fichier EN et on cr�e autant d'enregistrements
  � l'�tat NEW
  pour chaque langue destination de la trad on cr�e des enregistrements
  vides, � l'�tat NEW.
- modifs des trads :
  quand quelqu'un travaille sur une trad, il �dite le texte, ce qui le passe
  � l'�tat TRV. S'il consid�re que c'est au point, il passe � OK
- mise � jour :
  parcours du fichier EN, pour chaque bloc, on cherche son homologue et
  on met � jour son �tat ou on cr�e un nouveau en cons�quence
  ensuite, on reporte ces modifs sur les trads

TODO ( '-' = � faire, '*' = fait)
* structure de donn�es
  dans table.sql

* import et mise � jour
  php5 importer.php spip=/dir/de/spip
  => transform� en code dans le traitement de ?page=jq_admin

* un script (tr�s simple, un insert/select) pour l'ajout d'une nouvelle
  langue
  php5 ajouterLangue.php spip=/dir/de/spip lang=LL
  => transform� en code dans le traitement de ?page=jq_admin

* squelettes permettant d'afficher l'�tat courant par langue (en/lang cote
  � cote + filtrage par etat)
  ?page=trads&lg=LL apres activation du plugin "docjquery"
  * "boucle xml" pour afficher comme sur le site d'origine
    + la version FR avec des blocs "editables"
  * action "save"
    recup de l'xml, de l'id et de la langue et maj en bdd de l'xml, le statut
    et la date
  * ajouter un truc pour sp�cifier un statut "en cours/termin�"

* squelette d'export xml d'une langue

- gestion de la version (tag dans l'xml � mettre en bdd pour reporter dans
  l'export)
- gestion de la trad des descriptions de type (pour l'instant dans un multi)
- traduire les mots dans la xsl (et les desc de types ?), via un dictionnaire
  spip
