#Salvatore : Robot de gestion de traductions

##Les outils fournis :

* ```tireur.php``` :
	va chercher les fichiers de langue (décrits dans traduction.txt) et les dépose dans sa copie locale
* ```lecteur.php``` :
	prend les fichiers de référence dans sa copie locale (des SVN), et met à jour la base de données (Voir le plugin tradlang)
* ```ecriveur.php``` :
	exporte les traductions (à partir du plugin tradlang) sous forme de fichiers traduits, dans une copie locale (idem export SVN)
* ```pousseur.php``` :
	commit SVN les différents fichiers de langue
* ```inc_tradlang.php``` : 
	librairie commune aux outils  précédents

##Installation et fonctionnement de tradlang

Ces scripts nécessitent pour fonctionner : 
1. SPIP v3.0.x
2. Trad-lang v2.0.x
3. copier le répertoire à la racine du site ayant le plugin tradlang
4. créer manuellement le répertoire ```tmp/``` dans le répertoire des scripts, ce répertoire sert à stocker les copies locales des fichiers
5. créer manuellement le répertoire ```traductions/``` dans le répertoire des scripts et y placer un fichier traductions.txt comportant les descriptions des modules à importer [comme cet exemple](http://zone.spip.org/trac/spip-zone/browser/traductions.txt)
6. Pour récupérer ce fichier par svn, et uniquement celui-ci, se placer dans le répertoire des scripts et lancer : ```svn co svn://zone.spip.org/spip-zone/ traductions --depth empty```
	1. Cette commande ne crée qu'un répertoire SVN vide, puis lancer cette commande ```cd traductions && svn up traductions.txt```
	2. Cette dernière commande ne récupère que le fichier traductions.txt
7. récupérer les fichiers de langue indiqués dans le fichier traductions.txt, dans le répertoire des scripts lancer : ```php tireur.php```
8. import dans la base des modules de langue et de leur contenu, dans le répertoire des scripts, lancer : ```php lecteur.php```
9. exporter le contenu de la base de donnée, dans le répertoire des scripts, lancer : ```php ecriveur.php```
10. envoyer les modifications sur le SVN, pour ceci, vous devez pouvoir écrire sur le serveur SVN.
	1. Créer (s'il n'existe pas déjà) et modifier le fichier ```config/salvatore_passwd.inc```
	2. Dans ce fichier, ajouter les deux variables suivantes:
		* ```$SVNUSER = 'user@svn.tld';``` correspondant au nom d'utilisateur du serveur SVN qui enverra les fichiers.
		* ```$SVNPASSWD = 'mot de passe';``` correspondant au mot de passe de l'utilisateur du serveur SVN qui enverra les fichiers.
	3. Lancer ensuite la commande : ```php pousseur.php```


##Options possibles

Plusieurs options peuvent être définies dans le fichier config/mes_options.php du site par exemple.

```define('_EMAIL_ERREURS','nom@domaine.tld');```

```define('_EMAIL_SALVATORE','salvatore@domaine.tld');```

```define('_ID_AUTEUR_SALVATORE','23');```

```define('_SPAM_ENCRYPT_NAME',true);```
