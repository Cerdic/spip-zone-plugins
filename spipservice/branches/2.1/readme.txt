IMPORTANT : l'utilisation de ce plugin désactive l'authentification LDAP (seulement pour la version SPIP 2.1.*).

NOTE : Si vous avez des soucis de connexion depuis votre service Tiers, 
il se peut que votre hébergeur gère différemment les variables globales PHP,
notamment PHP_AUTH_USER et PHP_AUTH_PW.
C'est le cas d'OVH!
Pour corriger ce problème, vous devez ajouter dans votre fichier .htaccess,
juste après l'instruction "RewriteBase /", l'instruction suivante : 

RewriteRule .* - [E=REMOTE_USER:%{HTTP:Authorization},L]