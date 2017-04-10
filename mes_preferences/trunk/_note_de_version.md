------------------------------------------------------------------------------
#PROJET : mes_preferences

Auteur : Arnaud B. (Mist. GraphX)
e-mail : webhamster@mister-graphx.com

Date : Thu Jul 19 11:27:58 2012

------------------------------------------------------------------------------
SOURCES :
========

- basicone : <https://contrib.spip.net/basicone-icones-du-theme-prive>

- Thèmes pour l’interface privée : <https://contrib.spip.net/Themes-pour-l-interface-privee>

UTILISE :
- jappixmini : https://contrib.spip.net/Jappix-Mini

DOC :
=====

Mes prefs v1 :

- permet a l'utilisateur de l'espace privé de changer son thème et interface de l'espace privé

- ajoute plusieurs thème d'icones au choix sans avoir a passer par la global : car elle est globale ;-) et donc force tous le monde a utiliser le même thème.
Néanmoins dans `mes_preference_option.php` ont peut forcer la Global sur un jeux d'icone particulier ou un thème, par exemple si le webmaster veut imposer un jeux de base par défaut.

- ajoute un type de layout / taille d'écran : elastic : la colonne d'affichage centrale du contenu est en % qui s'adapte a la fenètre du navigateur
    
- pour personaliser les icones un gabarit est fournit au format .psd (importable par gimp si besoin) placé dans le répertoire images/sprite_spip_prive
les tranche sont prédécoupée et nomées.

- le logo du site est présent dans le pied de page , ainsi qu'un chat pour les utilisateurs de l'espace privé si le pluginjappixmini est installé


------------------------------------------------------------------------------
TRAVAUX :
=========

Tue Sep 04 11:04:25 2012

- [TEST] intégration du modèle [(#MODELE{minichat})] dans /prive/inclure/pied :
    |-> ajoute un chat jappix pour les rédacteur de l'espace privé si le plugin jappix mini est instalé

Wed Jul 25 10:10:01 2012

-   correction sur les puces des menus déroulant qui ont des enfants .has_child

Tue Jul 24 19:29:23 2012

- ajout du fichier /prive/inclure/pied
 |-> logo du site affiché dans le footer de l'administration
 
- ajout d'une feuille commune a tous les thèmes inc/commons.css

Thu Jul 19 11:29:57 2012

- renomage en mes_preferences du plug in theme switcher privé : en fait le nom était nul et c'est pas la qu'on voulait en venir donc mes préférences est plus parlant.
- le but : permet d'améliorer la gestion des préférences de l'utilisateur, pour le moment au niveau de l'interface et du thème de l'espace privé.
- ajoute un layout dit : elastic pour completer les deux fournis par défaut large et … petit : ça permet quand ont as des grand tableaux de données sur certains affichages d'objet de profiter de toute la largeur en fonction de l'écran.
- pour le reste c'est similaire au plugin précedent : deux thèmes basiques supplémentaire et la restitution du thème spip 2 ... surtout a titre d'exemple avec une surcharge simple et basique, + un sprite .psd prédécoupé fourni (/images/) pour faciliter l'adaptation des icones du bandeau rapidement
- pour + d'info note_de_version.md (markdown)


Thu Jul 05 07:07:02 2012 :

* Surcharge de prive/squelette/body : pour prendre en charge les extras sur le mode elastic
* Correction sur les css du layout elastic pour que #contenu s'adapte sans chasser avec les extras

* Correction sur le mode petit écran : etroit
* Corrigé sur css  spip_dist quand ont charge le thème , le #contenu mode étroit ne chasse plus

    TODO : revoir la mise en forme des fieldset sur les formulaires de config et les formulaires en générale

------------------------------------------------------------------------------

**BUG**

------------------------------------------------------------------------------
**TODO**

- TODO : surcharger le formulaire des préférences plutot que le dupliquer,
voir pour permettre a d'autres plugin d'y inssérer des prefs utilisateur.

------------------------------------------------------------------------------


