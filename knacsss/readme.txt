Plugin Knasss

Framework Css Knass pour SPIP en mode Scss (Sass)
voir http://knacss.com et http://www.alsacreations.com/tuto/lire/1577-decouverte-du-framework-css-KNACSS.html

Utilise le plugin scssphp qui compile une CSS et mets en cache le fichier sass/knacss.scss et ses fichiers en @import

Appel du fichier
[<link rel="stylesheet" href="(#CSS{sass/knacss.css}|direction_css)" type="text/css" />]

Appel de la démo 
?page=demo/knacsss

Warning

Pour rester cohérent avec les bons usages SPIP, le dossier sass/ est renommé en css/ 
sauf l'ajout dans un unique fichier css/knacss.css du chemin css/ sur toutes les lignes @import, 
son contenu comme ses fichiers n'a pas été modifié et n'a subit aucun mauvais traitement lors du passage en zone SPIP,
il est conforme à l'original disponible sur https://github.com/HugoGiraudel/KNACSS-Sass

Surcharges

Les surcharges .scss des squelettes (effectives aussi pour les @import) doivent reprendre le nom des dossiers du plugin
exemple:
squelettes/css/helpers/_reset.scss
