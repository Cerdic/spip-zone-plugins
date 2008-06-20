
             Assistant de Configuration du Site

http:// acs.geomaticien.org

________________________________________________________________________________
Licence: GPL
Dernière mise à jour de ce document le: 10-05-2008
Par: Daniel FAIVRE
________________________________________________________________________________

ACS permet de créer un site à base de composants paramétrables.

L'interface web d'administration permet de personnaliser le style de chaque composant
du site. L'administrateur du site peut modifier ainsi couleurs, styles et images de fonds
sans éditer de fichiers pour adapter squelettes et feuilles de style à sa charte graphique.

C'est un plugin SPIP qui facilite la maintenance et la personnalisation de
sites spip par l'usage de composants de pages web réutilisables par les développeurs
de squelettes spip/ACS (personnalisables par le web) et de composants ACS.

ACS répond en même temps à deux besoins différents:

- Pour les créateurs de sites, c'est un outil qui permet de personnaliser le site facilement par interface web, et d'y intégrer rapidement des composants prédéfinis ou personnalisés. Le modèle dist intégré à ACS comprend une navigation par onglets, des éléments de page dépendants de mots-clés, comme un édito ou un encart, des composants multimédias, un mini-agenda, , une playlist mp3 et l'équivalent pour les films (flv), ...
Certains éléments sont contextuels: un onglet "top 10" ne s'affiche que si les statistiques intégrées de spip sont activées, les brèves ne s'affichent pas si le système de brèves de spip est désactivé, certaines page de squelettes "choisissent" d'afficher ou non tel ou tel conteneur de composant, ...


- Pour les développeurs, ACS permet de modulariser le développement de sites spip, de personnaliser très rapidement le graphisme, et d'offrir aux webmestres une interface d'administration pour en faire autant.

Pour les développeurs, ACS offre d'autres possibilités de personnalisation:

- On peut "surcharger" un modèle ACS par un autre dossier de squelettes (défini dans l'onglet "Administration"). ACS cherchera alors les fichiers d'abord dans le dossier de squelette (y compris pour les composants), puis dans le dossier de modèle ACS actif. C'est la manière la plus simple, la plus rapide, et la plus sûre de personnaliser un modèle ACS entier ou certains composants. Si on a placé le dossier de "surcharge" (override) en dehors des dossiers et sous-dossiers du plugin ACS (par exemple à la racine du site, tout simplement), on pourra mettre à jour ACS en écrasant l'ancienne installation sans recopier avant ses modèles personnalisés ailleurs. Il est ainsi très facile d'intégrer des composants paramétrables par interface web aux squelettes existants.

- ACS peut contenir plusieurs dossiers de modèles.
Le modèle de squelettes intégré "catalogue de composants" est dans le dossier "acs/models/cat".
Vous pouvez créer de nouveaux modèles ACS  à partir de modèles existants,
en les recopiant dans un dossier acs/models/monstyle et en y copiant le dossier
acs/models/dist/composants sous le nom acs/models/<mon_model>/composants

- On peut également utiliser plusieurs plugins ACS.
Par convention, on les différencient en les nommant acs-quelquechose
dans le xml du nouveau plugin. C'est surtout utile pour pouvoir tester une
nouvelle version d'ACS et la comparer à l'ancienne avant de l'installer définitivement à sa place simplement en choisissant
le plugin ACS à activer dans l'interface d'admin de spip.



Composants pour ACS

ACS utilise des squelettes constitués de composants élémentaires.
Ceux-ci sont installés dans le dossier composants du squelette.

Le fichier composants/config.php :
Liste les composants du modèle, ordonnés par groupes.

Dossiers et fichiers d'un composant:
(seul le fichier composants/<composant>/ecrire/composant.xml est obligatoire)

composants/<composant> : racine

composants/<composant>/<composant>.html : squelette du composant
composants/<composant>/<composant>.css.html : feuille de style du composant
composants/<composant>/lang/ : fichiers de langue du composant (espace public)
composants/<composant>/<composant>.php : définition optionnelle d'une classe <NomDeMonComposant> implémentant des méthodes prédéfinies, (comme insert_head, par exemple), pour le composant.
composants/<composant>/<composant>_balises.php : définition optionnelle de balises spip propres au compoant

composants/<composant>/ecrire/ : éléments de l'espace ecrire
composants/<composant>/ecrire/composant.xml : fichier de définition du composant
composants/<composant>/ecrire/lang/ : dossier des traductions du composant pour l'espace privé
composants/<composant>/ecrire/<composant>.php : définition optionnelle d'une classe <NomDeMonComposant> implémentant des méthodes prédéfinies, (comme afterUpdate, par exemple), pour le composant.

composants/<composant>/img_pack/ : images et icônes du composant

Les images dépendantes du composant affichées sur le site public
sont installées dans le dossier <$GLOBALS['ACS_CHEMIN']>/img/<composant>
Par défaut, $GLOBALS['ACS_CHEMIN'] = IMG/_acs (défini dans acs_options.php)

Le fichier composant.xml:
param:
- optionnel: true (absent= false, par défaut),
             ou nom de variable meta (activation si la variable vaut "oui"),
             ou plugin(s) <nom_de_plugin> (activation du composant si le plugin <nom_de_plugin> est actif)
- preview:  true (absent=false, par défaut). Hauteur de preview si numérique.

variable:
- nom: nom de la variable
- type: type de variable: img, couleur, textarea, widget ...
- valeur: valeur par défaut. Peut être le nom d'une variable ACS: dans ce cas, la valeur est par défaut la valeur de la variable ACS.
- chemin: "chemin de la variable". C'est le chemin par défaut des images pour une variable de type image. Chemin relatif à partir de $GLOBALS['ACS_CHEMIN']
- label: "non" pour ne pas afficher de label pour cette variable.


Balises de squelette:

#ACS_CHEMIN : $GLOBALS['ACS_CHEMIN']
#ACS_DERNIERE_MODIF : date de dernière mise à jour avec ACS
#STATS_ACTIVES : indique si les stats intégrées de spip sont activées

+ les balises définies par les composants

Intégration de composants aux squelettes:
<INCLURE{fond=composants/<composant>/<sous-composant>}{self=#SELF}{autres paramètre(s)}>
(passer self permet à Spip de différencier les caches; c'est nécessaire quand un
même composant est utilisé sur plusieurs pages, et cette petite subtilité permet
aux composants ACS "ajaxifiés" de bénéficier en mode Ajax
d'un cache Spip différent de celui de leur conteneur)
________________________________________________________________________________

Installation:
1) Copier le dossier acs dans le dossier plugins de la racine du site Spip.
2) Se connecter à l'espace ecrire en tant qu'auteur n°1 (qui doit être administrateur)
3) Choisir l'option "Configurer le site" du menu "Configuration" de Spip.

________________________________________________________________________________

FAQ

- Fonctionne en php5 et PAS en php4 (c'est un parti-pris d'ACS d'utiliser les "objets" en php5: les composants sont des "objets")

- Installation chez Free: mettre un fichier .htaccess contenant "php 1" à la racine du site pour activer php5.

- Et comment je sauvegarde ? Vos paramètres et images personalisés avec ACS sont automatiquement sauvegardés par une sauvegarde spip classique (sauvegarde du dossier IMG et de la base de données).

- Je voudrais utiliser ACS avec un autre squelette que "cat" ou "dist": c'est possible ? Le modèle Cat sert à la validation des composants et permet de fournir aux non-développeurs un site "clé en mains" au look et aux fonctionnalités paramétrables, et aux développeurs une base qu'on peut surcharger ("override") mais on peut aussi utiliser ACS avec d'autres modèles et même avec des squelettes pré-existants qui ne possèdent pas de composants ACS. Dans ce dernier cas, ACS permet de naviguer facilement dans les pages disponibles, et de visualiser leurs schémas fonctionnels ou leurs sources colorisées pour les modifier plus facilement. (ACS est - accessoirement - un "explorateur de squelettes, modèles et formulaires spip disponibles").

- Est-ce que Spip est modifié par ACS ? Non: ACS n'opère aucune surcharge du noyau Spip, et fonctionne uniquement en exploitant à ses fins les "points d'entrée" prévus par l'API Spip pour les plugins. Tant que celle-ci reste stable, ACS restera facilement compatible.

- Quelles versions de Spip sont compatibles ACS ? Une info-bulle sur le numéro de version ACS (onglet Pages) indique les versions de spip compatibles. A ce jour: 1.9.2c, 1.9.2d, et version 1.9.3svn (en mode dégradé pour cette dernière: tout marche, mais certaines fonctions rechargent encore la page au lieu d'utiliser Ajax, ce qui entraîne un confort moindre)

- Y'a t'il des plugins incompatibles ? En principe non, puisqu'ACS permet de surcharger les plugins. En pratique, un plugin qui écrase les globales de spip ou qui surcharge le noyau de spip de façon non conformes aux règles d'implémentation des plugins peut poser problème, et nécessiter l'écriture d'une surcharge. Par contre, il existe des plugins dont l'usage conjoint avec ACS n'a guère d'intérêt, parce qu'ils doublonnent des fonctions intégrées à ACS ou aux composants ACS du modèle standard ("Cat"). C'est le cas du plugin multimedia (ACS a son lecteur vidéo et sa playliste mp3 intégrés) ou de certains "plugins de squelettes" moins polyvalents ou moins riches de fonctionnalités.

- Quels sont les plugins dont la compatibilité a déjà été testée ?
  - crayons
  - notation (intégration conditionnelle au modèle cat)
  - openPublishing (overridé pour le modèle cat: utilise l'agenda intégré d'ACS et pas l'abominable système des brèves à la "Indy")
  - recommander
  - SpipCarto
  - w3c_go_home

________________________________________________________________________________
Evolutions prévues:

- Distribuer ACS avec d'autres modèles

- Compléter le modèle "Cat" avec des composants alternatifs équifonctionnels pour un "Rubnav" (navigation par rubriques) avec menus déroulants css ou js, un composant cartographique polymorphe et non limité à des technologies propriétaire, une gestion unifiée des "albums" photos et vidéos intégrée, une évolution progressive vers une conception "WYSIWYG" des pages du site (terminer l'intégration des boucles aux composants, puis les mettre dans une "boite à outils" adaptée, à développer)...

- Compléter l'API publique des composants pour faciliter l'intégration de multiples composants indépendants "Ajaxifiés en mode non intrusif" sur la partie publique.


