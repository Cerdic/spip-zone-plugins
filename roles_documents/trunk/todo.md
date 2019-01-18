# Rôles de documents : choses à faire

## Évolutions et bugs

Utiliser partout les fonctions de l'API des liens aux lieux de l'API SQL

Mettre à jour la fonction logo_modifier() dans l'API `action/editer_logo.php`

## Hacks vilains

Pour la gestion du logo du site, on associe un document à un faux objet éditorial « site_spip » avec un id négatif. Ça fait le job, mais ça peut occasionner des notices lorsque d'autres plugins se branchent sur les pipeline post edition de liens : ils peuvent être amenés à chercher une table `site_spips` qui n'existe pas. Par exemple les forums avec `forum_trig_supprimer_objets_lies()`.