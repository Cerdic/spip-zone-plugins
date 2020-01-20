# Salvatore : Robot de gestion de traductions

## Les outils fournis :

Salvatore s'utilise via des fonctions spip-cli.

* ```spip salvatore:tirer``` :
	va chercher et met à jour les repositories et link les sous-repertoires contenant les modules de langue décrits dans le fichier traductions
	Les depots sont checkout/update dans salvatore/depots/, 
	et leurs repertoires contenant les modules de langue sont ciblés par des liens symboliques placés dans modules/
	On peut avoir plusieurs fois le même module extrait de plusieurs repositories differents ou de plusieurs repertoires du meme repository 
* ```spip salvatore:lire``` :
	prend les fichiers de référence dans chaque dossier modules/xxx, et met à jour la base de données
* ```spip salvatore:ecrire``` :
	exporte les traductions depuis la base donnees dans chaque dossier modules/xxx, et prépare un fichier .json avec les infos de commit
* ```spip salvatore:pousser``` :
	commit et pousse sur chaque repo (git|svn) les différents fichiers de langue de chaque module modifie
* ```inc/savlatore.php``` : 
	librairie commune aux outils  précédents

## Installation et fonctionnement de tradlang

Ces scripts nécessitent pour fonctionner : 
1. SPIP v3.2.x
2. Trad-lang v3.0.x
3. créer le repertoire `salvatore/` a la racine du site avec les sous-dossiers `depots/` `modules/` et `tmp/`
4. créer également le sous-répertoire ```salvatore/traductions/``` dans le répertoire des scripts 
et y placer un ou des fichiers traductions.txt comportant les descriptions des modules à importer [comme cet exemple](http://zone.spip.org/trac/spip-zone/browser/traductions.txt)
<br/>Pour récupérer ce fichier par svn, et uniquement celui-ci, se placer dans le répertoire `salvatore/` et lancer : ```svn co svn://zone.spip.org/spip-zone/ traductions --depth empty```
	1. Cette commande ne crée qu'un répertoire SVN vide, puis lancer cette commande ```cd traductions && svn up traductions.txt```
	2. Cette dernière commande ne récupère que le fichier traductions.txt
5. récupérer les fichiers de langue indiqués dans le fichier traductions.txt
	1. Créer (s'il n'existe pas déjà) et modifier le fichier ```config/salvatore_passwd.inc```
	2. Dans ce fichier, ajouter les variables suivantes:
	    * ``` 
          $SVNUSER = 'user@svn.tld'; // correspondant au nom d'utilisateur du serveur SVN qui enverra les fichiers.
          $SVNPASSWD = 'mot de passe'; // correspondant au mot de passe de l'utilisateur du serveur SVN qui enverra les fichiers.
          $domaines_exceptions = array('git.spip.net');
          $domaines_exceptions_credentials = array(
            'git.spip.net' => array('user' => 'user git','pass'=>'mot de passe'),
          );
	      ```
    3. Lancer ensuite la commande : ```spip salvatore:tirer```
6. import dans la base des modules de langue et de leur contenu, dans le répertoire des scripts, lancer : ```spip salvatore:lire```
7. exporter le contenu de la base de donnée, dans le répertoire des scripts, lancer : ```spip salvatore:ecrire```
8. envoyer les modifications sur le SVN ou GIT, 
    1. pour ceci, vous devez pouvoir écrire sur les repositories grace aux logins/pass renseignés dans le fichier ```config/salvatore_passwd.inc```
	2. Lancer ensuite la commande : ```spip salvatore:pousser```


## Options possibles

Plusieurs options peuvent être définies dans le fichier config/mes_options.php du site par exemple.

```define('_EMAIL_ERREURS','nom@domaine.tld');```

```define('_EMAIL_SALVATORE','salvatore@domaine.tld');```

```define('_ID_AUTEUR_SALVATORE','23');```

```define('_SPAM_ENCRYPT_NAME',true);```

Pour plus de détails voir la fonction `salvatore_init()` de `inc/salvatore`
