bootstrap/
  contient les sources de https://github.com/twitter/bootstrap
  (dossier less/ renomme en css/ et chemins css/ ajoute aux @import)
bootstrap2spip/
  contient les adaptations de bootstrap<->spip (dans les deux sens)
  css/ contient des surcharges des fichiers bootstrap : inclusion du fichier original par @import + regles complementaires
  formulaires/ et modeles/ contient des squelettes dont la structure a ete adaptee a BootStrap
  js/ contient le script de prise en charge de html5 pour IEx
squelettes/
  exemple.html contient un exemple d'appel des CSS de bootstrap dans une page HTML
  exemple.less est un exemple de feuille perso en LESS qui inclue les variables bootstrap pour les prendre en compte