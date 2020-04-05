# Plugin Intranet / Extranet

Intranet / Extranet est un plugin très simple permettant d’interdire l’accès au site à tout visiteur non identifié.

[Documentation](https://contrib.spip.net/Intranet-Extranet-4388)

## Todo

Ajouter une page listant l'ensemble des objets éditoriaux de sortis de l'intranet

## Changelog

### Versions 0.7.x

#### Version 0.7.0

* Ajouter un filtre d'autorisation par noms d'hôtes, pour autoriser des outils de monitoring par exemple

### Versions 0.6.x

#### Version 0.6.1

* Problème lors de l'accès en local, tout était débrayé (cf ce [forum](https://contrib.spip.net/Intranet-Extranet-4388#forum487356) et [celui-ci](https://contrib.spip.net/Intranet-Extranet-4388#forum487794))
* Accepter `::1`, `localhost` et `127.0.0.1` dans les ips comme hôte local (cf [ce forum](https://contrib.spip.net/Intranet-Extranet-4388#forum477511)).

#### Version 0.6.0

* Permettre de sortir des objets éditoriaux un à un de l'intranet, par exemple :
  * l'article "31"
  * la rubrique "12"

### Versions 0.5.x

#### Version 0.5.0

* Ajouter un header http 401 (not authorized) lorsque l'on tombe sur la page de login de l'intranet
* Vider le cache lors d'un changement de configuration
* Améliorer la détection des pages autoriser
* Ajout de ce fichier `README.md`

### Versions 0.4.x

#### Version 0.4.1

* Autoriser une IP ou une plage d'IP

#### Version 0.4.0

Version d'origine