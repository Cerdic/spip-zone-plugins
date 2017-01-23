GeoIP (version 2)
============

Utiliser la bibiothèque GeoIP version 2 de Maxmind

En installant la version 2, vous profitez des dernières fonctionnalités de geoIP.
La base de données (fichier .mmdb) est installé dans le répertoire lib/

Version 2 : https://github.com/maxmind/GeoIP2-php/archive/master.zip 
Database version 2 : http://geolite.maxmind.com/download/geoip/database/GeoLite2-City.mmdb.gz

## Changelog

### Version 2.0.3

- Mise à jour de la librairie Maxmind et un peu de documentation

### Version 2.0.2

- Petite coquille de doc et pétouille de code

### Version 2.0.1

- Suppression du code pour utiliser la version 1
- Suppression du formulaire de configuration inutile

### Version 2.0.0

- Passage en version 2 suite à l'ajout sur le svn de la version 1

### Version 1.0.1

- Faire fonctionner la librairie version 2 sans l'autoload (cet enchaînement d'include_spip, faire mieux)
- Installation automatique des bases de données
- Installation automatique des librairies MaxMind

### Version 1.0.0

- Test si libapache2-geoip est installé sur le serveur, sinon la lib doit être installé à la racine du site dans lib/
- Possibilité d'utiliser la version 1 et 2 de la librairie
- Récupérer le code pays d'après une IP

### TODO
