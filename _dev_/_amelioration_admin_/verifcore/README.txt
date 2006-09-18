But de ce plugin
         1) Vérifie que le transfert ftp des fichiers c'est bien effectu&eacute;
         2) Permettre de voir s'il y a eu des mis à  jour (du principalement &agrave; des corrections de bugs)

Ce plugin repose sur le fichier refcorespip191.txt(si vous avez un meilleur nom je prends). 
La 1ere ligne contient le répertoire svn qui servira de fichier de comparaison
la 2eme ligne contient a date à laquelle a été généré le fichier refcorespip191.txt
Ensuite toutes les lignes sont composés de trois information
nom du fichier:taille du fichier: date de la derniere revision svn du fichier (grace aux infos de svn info)

Ensuite on compare tous ces informations avec les fichiers de votre racine pour détecter les fichiers manquants et les fichiers modifiés.

De plus grace au fichier fichier_repertoire_supprimer_depuis_svn6797.txt(pareil si vous avez un meilleur nom) qui liste tous les fichiers et répertoires supprimés depuis SPIP 1.9 (1er juillet) cad svn 6797, on peut ainsi éléminer les fichiers obsolètes.

Modifications futures
-Lister tous les fichiers *.php3 devenu "inutile" depuis spip 1.9 
-faire un diff pour les fichiers modifier