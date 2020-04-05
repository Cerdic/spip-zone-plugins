# Location d'objets

Gère la location d'objets

## Dépendances
- [saisies](https://plugins.spip.net/saisies.html)
- [verifier](https://plugins.spip.net/verifier.html)
- [prix_objets_periodes](https://github.com/abelass/prix_objets_periodes)
- [objets_disponibilites](https://github.com/abelass/objets_disponibilites)
- [facteur](https://plugins.spip.net/facteur.html)
- [NoSPAM](https://contrib.spip.net/NoSPAM)


## Configuration

### Location objets

- Définition du statut par défaut
- Définir les service extras
- Type de période de location (jour ou nuit, defiuut jour), définir comment est affiche la période louée.
- Gestion des notifications


### SPIP
sous Configuration / Interactivité. (ecrire/?exec=configurer_interactions)

Activez :
- Accepter les inscriptions
- Accepter l’inscription de visiteurs du site public

## Utilisation
Allez sur la page d'édition de l'objet que vous aimeriez louer et ajoutez lui des prix avec les deux plugins
[prix_objets](https://plugins.spip.net/prix_objets.html) et [prix_objets_periodes](https://github.com/abelass/prix_objets_periodes)

### Dans vos squelettes placez la balise
	#FORMULAIRE_EDITER_OBJETS_LOCATION{#ENV{id_objets_location},#ENV{location_objet},#ENV{id_location_objet}, #ENV{options}}

Prenez l'example dans La fonction `squelettes/modeles/formulaire_location.html`

- la première variable `id_objets_location` est l'identifiant de la location
- la deuxième `location_objet`désigne l'objet à louer. Utilisez le nom complet de
	la table, donc par example pour l'objet espace `location_objet=spip_espaces`
- la troisième variable prend l'identifiant de l'objet à réserver
- la quatrième variable est un tableau avec toutes les variables à passer dans l'environnement.
 Par example `#ARRAY{entite_duree=nuit,location_extras_objets=objets_service}}` mettrait le type de période de location en nuits et définirait [objets_services_extras](https://github.com/abelass/objets_services_extras) come service extra.
- les autres variables sont les typiques d'un formulaire cvt de spip

### Noisette
utiliser la noisette `squelettes/content/location.html`en lui passant le variables
nécessaires.

### Modèle
vous pouvez également utiliser le modèle `formulaire_location` en y passant le variables
souhaitées.


## Les services extras?
Il s'agit de tout service s'ajoutant à la location de base. N'importe quel objet spip
peut servir comme service extra, il suffit de le définir dans la configuration et de
lui ajouter un prix, si nécessaire. Chaque service extra pourrait être ajouté à la location.

Le choix dans la config peut être surchargé via
la variable `options` du formulaire en utilisant `location_extras_objets`, par example
	#FORMULAIRE_EDITER_OBJETS_LOCATION{
		#ENV{id_objets_location},
		#ENV{location_objet},
		#ENV{id_location_objet},
		#ARRAY{location_extras_objets=objets_service}}

# Extensions
- [prix_objets](https://plugins.spip.net/prix_objets.html) : Gestion de prix pour l'objet á louer.
- [prix_objets_periodes](https://github.com/abelass/prix_objets_periodes) : Gestion de prix par période.
- [locations_objets_restrictions](https://github.com/abelass/locations_objets_restrictions) : Gestion de limitations quant á la location.
- [objets_restrictions_periodes](https://github.com/abelass/objets_restrictions_periodes) : Des limitations temporelles ppour la location.
- [location_objets_bank](https://github.com/abelass/location_objets_bank) : Gérer les paiements.
- [objets_disponibilites](https://github.com/abelass/objets_disponibilites) : Gérer les dates de disponibilités et d'indisponibilités pour le objet á louer
- [objets_services_extras](https://github.com/abelass/objets_services_extras) Des service extras

# Examples
le plugin [location_immeubles](https://github.com/abelass/location_immeubles), propose
une location pour des immeubles/espaces
