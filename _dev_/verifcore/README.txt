But de ce plugin
         1) V�rifie que le transfert ftp des fichiers c'est bien effectu&eacute;
         2) Permettre de voir s'il y a eu des mis �  jour (du principalement &agrave; des corrections de bugs)

Ce plugin repose sur le fichier refcorespip191.txt(si vous avez un meilleur nom je prends). 
La 1ere ligne contient le r�pertoire svn qui servira de fichier de comparaison
la 2eme ligne contient a date � laquelle a �t� g�n�r� le fichier refcorespip191.txt
Ensuite toutes les lignes sont compos�s de trois information
nom du fichier:taille du fichier: date de la derniere revision svn du fichier (grace aux infos de svn info)

Ensuite on compare tous ces informations avec les fichiers de votre racine pour d�tecter les fichiers manquants et les fichiers modifi�s.

De plus grace au fichier fichier_repertoire_supprimer_depuis_svn6797.txt(pareil si vous avez un meilleur nom) qui liste tous les fichiers et r�pertoires supprim�s depuis SPIP 1.9 (1er juillet) cad svn 6797, on peut ainsi �l�miner les fichiers obsol�tes.

Modifications futures
-Lister tous les fichiers *.php3 devenu "inutile" depuis spip 1.9 
-faire un diff pour les fichiers modifier