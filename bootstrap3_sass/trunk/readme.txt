Portage du plugin bootstrap3 (version 3.0.8) de LESS à SASS

Le portage n'est pas iso fonctionnel : certaines adaptaions de bootstrap2spip liées à spipr n'ont pas été portées en 
SASS, notamment :
- compatibilité Boostrap2
- grid
- thumbnails
- layout gala

bootstrap/
  contient les sources de https://github.com/twbs/bootstrap-sass
  dossier assets/stylesheets/bootstrap renomme en css/ et chemins css/ ajoute aux @import
  copie de assets/stylesheets/_bootstrap.scss dans css/bootstrap.css 
bootstrap2spip/
  contient les adaptations de bootstrap<->spip (dans les deux sens)
  css/ contient des surcharges des fichiers bootstrap : inclusion du fichier original par @import + regles complementaires
  formulaires/ et modeles/ contient des squelettes dont la structure a ete adaptee a BootStrap
  js/ contient le script de prise en charge de html5 pour IEx
demo/
  bootstrap.html contient un exemple d'appel des CSS de bootstrap dans une page HTML
  exemple.scss est un exemple de feuille perso en SASS qui inclue les variables bootstrap pour les prendre en compte