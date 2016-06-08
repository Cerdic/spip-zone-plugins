GeoIP
============

Utiliser la bibiothèque GeoIP de Maxmind version 1

Ce plugin vous permet de géolocaliser le pays d'une adresse IP. Nous utilisons la version 1 de geoip de Maxmind. 
Contrairement à la version du plugin geoip qui permet d'utiliser les deux versions de la librairie. 

Github : https://github.com/maxmind/
Version 1 : https://github.com/maxmind/geoip-api-php/archive/master.zip
Database version 1 : http://geolite.maxmind.com/download/geoip/database/GeoLiteCountry/GeoIP.dat.gz

## Changelog

### Version 1.0.1

- Permet d'utiliser les fonctions de la librairie
- Documentation sur ?exec=test_geoip

### Version 1.0.0

- Test si libapache2-geoip est installé sur le serveur
- Récupérer le code pays d'après une IP
