
             Assistant de Configuration du Site

Version : 0.6

Documentation : http:// acs.geomaticien.org
Licence: cf LICENCES.txt
________________________________________________________________________________
Dernière mise à jour de ce document le: 21-06-2011
Par: Daniel FAIVRE
________________________________________________________________________________

ACS permet de créer des sites SPIP à base de composants paramétrables.

Le webmestre autorisé peut modifier ainsi couleurs, styles, images de fonds, fontes, ...  
et plus généralement n'importe quel paramètre sans éditer de fichiers pour adapter 
rapidement squelettes et feuilles de style à sa charte graphique.

ACS intègre un "modèle ACS" (jeu de squelettes SPIP) nommé "Cat",
entièrement personnalisable par interface web, multilingue, et extensible.

Pour développeur de squelettes SPIP :
Chaque composant ACS du modèle actif peut être intégré dans un jeu de squelettes
"d'override" personnalisé, qui vient en surcouche(s) d'ACS, et qui peut posséder 
ses propres composants personnalisés.

L'interface d'administration permet de sécuriser les pages sensibles 
de l'espace privé de SPIP et de n'importe quel plugin installé.

________________________________________________________________________________

Installation:
1) Utiliser la procédure d'installation automatique de plugins de spip 2, ou copier le dossier acs dans le dossier plugins de la racine du site SPIP.
2) Se connecter à l'espace ecrire en tant qu'auteur n°1 (qui doit être administrateur).
3) Choisir l'option "Configurer le site" du menu "Configuration" de SPIP.

________________________________________________________________________________

Version mini des plugins compatibles optionnels (apportent des fonctionnalités ou du confort en plus) :

crayons : svn > 21756
cfg : 1.12.3
palette : 1.2