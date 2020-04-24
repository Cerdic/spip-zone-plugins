
# Plugin FFE Data

le plugin FFE Data permet de récupérer les données du web service de la FFE (Fédération Française des Échecs)

On y trouve des données qui concernent :
- les joueurs
- les clubs
- les matches en équipe
- les ligues
- les comités

## Installation

L’installation se fait comme pour tout plugin SPIP
Lors de l’installation automatique du plugin SVP installe trois autres plugins.
- Mailcrypt : parce que le plugin permet de restituer des adresses mails, il ne faut pas prendre le risque de les afficher sans protection.
- Saisies : pour afficher le formulaire interne qui permet de retrouver les informations sur les équipes du club à partir de l’identifiant du club
- Chart.js : qui permet d’afficher le graphique d’évolution élo des joueurs.

## Configuration 

Sur la page de gestion des plugins il y a un bouton qui permet d’accéder à la "page de configuration". Cette page permet de retrouver l’ensemble des informations techniques sur l’ensemble des équipes.
L’adresse de cette page est http://monsite.tld/ecrire/?exec=configurer_ffedata
Utilisations

Attention à la syntaxe qui doit respecter ce qui est attendu. Notamment il y a une farce avec Ref qui s’écrit la plupart du temps avec une majuscule... mais pas toujours ! Regardez bien les exemples.

## Les modèles

Un modèle pour avoir la fiche d’identité d’un joueur (ici une joueuse), son évolution élo :
<ffe_fiche_joueur|Ref=139033>

Afficher un graphique avec l’évolution élo d’un joueur :
<ffe_graph_joueur|Ref=10280>
*depuis que le classement FIDE est généralisé cette information n'est plus mise à jour sur le webservice*

Afficher tous les joueurs d’un club.
<ffe_joueurs_detail|Ref=2569>

Afficher toutes les données possibles des clubs d’un département :
<ffe_clubs_departement|Ref=31>
*Attention ces données sont souvent incomplètes entre le 31 août et le début de la nouvelle saison après le premier match en équipes en octobre* 

Afficher les contacts (Président et Correspondant) de chaque département
<ffe_clubs_contacts|Ref=32>
*idem avertissement modèle précédent, données incomplètes entre deux saisons*

Les clubs d'un département classés par nombre de licences
<ffe_clubs_classement|Ref=13>

Afficher les équipes du club pour le public
<ffe_equipes_club|saison=2013|Ref=1282>  

Afficher les dates des rencontres pour la saison
<ffe_equipes_dates|saison=2016|ref=1244|equipe=3525>

Afficher Les PV d’une équipe
<ffe_equipes_pv|saison=2013|ref=2|equipe=789>
*attention les résultats des équipes jeunes ne sont pas rendus correctement en raison du calcul différent entre les différentes catgories*

Afficher seulement le PV d’un match en équipe
<ffe_equipes_pv_ronde|saison=2014|ref=85|equipe=2164|ronde=4>
*idem avertissement modèle précédent pour équipes jeunes* 

Le tableau de résultat d’un groupe
<ffe_equipes_groupe|saison=2012|Ref=20>

Pour afficher les responsables d’une Ligue
<ffe_ligue|Ref=occ>

## Aide à la saisie des modèles

Il est possible d’installer le plugin insérer-modèles.
Une aide à la saisie des principaux modèles est alors proposée.
