GeoIP
============

Utiliser la bibiothèque GeoIP de Maxmind

Vous pouvez utiliser la version de la librairie en l'installant dans le répertoire lib à la racine du site. 
Si vous installez les deux versions vous pouvez switcher d'une version à une autre. 

En installant la version 1 de la librairie, vous devez renommer le fichier src/geoip.inc en src/geoip.php
Récupérer la base de données (fichier .dat) et installer la dans le répertoire lib/geoip-api-php/maxmind-db/ 

En installant la version 2, vous profitez des dernières fonctionnalités de geoIP, consulter le fichier README 
sur github pour installer composer et le vendor.
Récupérer la base de données (fichier .mmdb) et installer la dans le répertoire lib/GeoIP2-php/maxmind-db/ 

Github : https://github.com/maxmind/
Version 1 : https://github.com/maxmind/geoip-api-php/archive/master.zip
Database version 1 : http://geolite.maxmind.com/download/geoip/database/GeoLiteCountry/GeoIP.dat.gz

Version 2 : https://github.com/maxmind/GeoIP2-php/archive/master.zip 
Database version 2 : http://geolite.maxmind.com/download/geoip/database/GeoLite2-City.mmdb.gz

## Changelog

### Version 1.0.0

- Test si libapache2-geoip est installé sur le serveur, sinon la lib doit être installé à la racine du site
- Possibilité d'utiliser la version 1 et 2 de la librairie
- Récupérer le code pays d'après une IP
