NOTE : Si vous avez des soucis de connexion depuis votre service Tiers, 
il se peut que votre hébergeur ne fixe pas certaines variables globales PHP,
notamment REMOTE_USER.
C'est le cas d'OVH!
Pour corriger ce problème, vous devez ajouter dans votre fichier .htaccess,
juste après l'instruction "RewriteBase /", l'instruction suivante : 

RewriteRule .* - [E=REMOTE_USER:%{HTTP:Authorization},L]

NOTE : vérifier aussi que votre configuration de Spip permet l'authentification HTTP
cf. : ecrire/inc_version.php
>> $ignore_auth_http = false;