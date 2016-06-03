GeoIP
============

Utiliser la bibiothèque GeoIP de Maxmind

Lors de l'installation du plugins vous installez deux versions de la librairie, vous devez installer les bases de données dans la configuration du plugins.

En installant la version 1 de la librairie, vous devez renommer le fichier src/geoip.inc en src/geoip.php
La base de données (fichier .dat) est installé dans le répertoire lib/geoip-api-php/maxmind-db/ 

En installant la version 2, vous profitez des dernières fonctionnalités de geoIP.
La base de données (fichier .mmdb) est installé dans le répertoire lib/GeoIP2-php/maxmind-db/ 

Github : https://github.com/maxmind/
Version 1 : https://github.com/maxmind/geoip-api-php/archive/master.zip
Database version 1 : http://geolite.maxmind.com/download/geoip/database/GeoLiteCountry/GeoIP.dat.gz

Version 2 : https://github.com/maxmind/GeoIP2-php/archive/master.zip 
Database version 2 : http://geolite.maxmind.com/download/geoip/database/GeoLite2-City.mmdb.gz

## Changelog

### Version 1.0.1

- Faire fonctionner la librairie version 2 sans l'autoload (cette enchaînement d'include_spip, faire mieux)
- Installation automatique des bases de données
- Installation automatique des librairies MaxMind

### Version 1.0.0

- Test si libapache2-geoip est installé sur le serveur, sinon la lib doit être installé à la racine du site dans lib/
- Possibilité d'utiliser la version 1 et 2 de la librairie
- Récupérer le code pays d'après une IP

### TODO

- Voir pourquoi les contantes dans le fichier des fonctions ne fonctionnent pas.