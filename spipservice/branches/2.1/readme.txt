IMPORTANT : l'utilisation de ce plugin d�sactive l'authentification LDAP (seulement pour la version SPIP 2.1.*).

NOTE : Si vous avez des soucis de connexion depuis votre service Tiers, 
il se peut que votre h�bergeur g�re diff�remment les variables globales PHP,
notamment PHP_AUTH_USER et PHP_AUTH_PW.
C'est le cas d'OVH!
Pour corriger ce probl�me, vous devez ajouter dans votre fichier .htaccess,
juste apr�s l'instruction "RewriteBase /", l'instruction suivante : 

RewriteRule .* - [E=REMOTE_USER:%{HTTP:Authorization},L]