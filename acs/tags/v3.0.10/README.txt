
             Assistant de Configuration du Site

Version : 3.0.3

Documentation : http:// acs.geomaticien.org
Licence: cf LICENCES.txt
________________________________________________________________________________
Dernière mise à jour de ce document le: 25-01-2015
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
Tout composant ACS peut également être inséré et paramétré directement dans un élément éditable
de SPIP sous forme de modèle SPIP. Exemple :  <composant|c=audio|p=audio|parametre1=truc|parametre2=machin>

L'interface d'administration permet de sécuriser les pages sensibles 
de l'espace privé de SPIP et de n'importe quel plugin installé.

________________________________________________________________________________

Installation:
1) Utiliser la procédure d'installation automatique de plugins de spip 2 ou 3, ou copier le dossier acs dans le dossier plugins de la racine du site SPIP.
2) Se connecter à l'espace ecrire en tant qu'auteur n°1 (qui doit être administrateur).
3) Choisir l'option "Configurer le site" du menu "Configuration" de SPIP.
________________________________________________________________________________
Utilisation de composants ACS-Cat dans d'autres squelettes SPIP que ceux de Cat :
- indiquez dans l'onglet Administration d'ACS votre dossier de squelettes. 
Ceux-ci seront utilisés sur le site public à la place de ceux du modèle cat d'ACS, 
mais les composants ACS y deviennent ainsi insérables sous la forme d'inclusins SPIP
ou du modèle composant (issu de Cat). Exemples :
<INCLURE{fond=composants/audio/audio}{parametre1=truc}>
[(#MODELE{composant}{c=composants/audio/audio}{parametre1=truc})]
