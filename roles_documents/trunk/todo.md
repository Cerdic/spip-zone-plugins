# Rôles de documents : choses à faire

## Évolutions et bugs

### Identification des rôles de logo

Pour l'instant on se base sur le nom du rôle : si ça commence par logo, c'est un rôle de logo.
Il faut rendre ça plus souple en se basant sur la déclaration des rôles : c'est la-dedans qu'on dit : pour tel objet, tel rôle correspond à un rôle de logo.

### balise `#LOGO_SITE_SPIP`

S'occuper de `#LOGO_SITE_SPIP`. En principe le formulaire crée un document lié à l'objet 'site' avec l'identifiant 0.

### Bouton « détacher » dans les listes de documents :

* Si c´est depuis le portfolio, il faut dissocier tous les documents SAUF ceux avec un rôle éventuel de logo
* Si c'est depuis le formulaire de logo, il ne faut dissocier QUE le document avec ce rôle précis.

### Bouton « utiliser comme logo »

Ajouter un bouton d'action pour utiliser un document comme logo. Enfin un mini formulaire plutôt, car il faut choisir le rôle de logo souhaité s'il y en a plusieurs de disponibles.
Ça doit créer un nouveau lien, pas modifier le lien existant.

### Ajout d'un logo déjà associé à l'objet

Quand on ajoute un document en logo, mais que celui-ci est déjà associé à l'objet, ça modifie le lien présent au lieu d'en créer un nouveau.

## Limitations

### Unicité

L´interface s'assure qu'on ne peut attribuer un rôle de logo qu´une seule fois, mais techniquement c´est toujours possible de créer plusieurs liens avec le même rôle. Est-ce qu'il faut vérifier l'unicité à chaque création de lien ?
