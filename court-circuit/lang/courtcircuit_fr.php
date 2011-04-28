<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	'configurer_courtcircuit' => 'Configurer les règles de court-circuitage des rubriques',
	'courtcircuit' => 'Court-circuit',
	'explication_article_accueil' => 'Si un article d\'accueil a été défini pour la rubrique, rediriger vers cet article ?',
	'explication_configurer' => 'Les règles ci-dessous permettent de rediriger une rubrique directement vers un article donné. Autrement dit, lorsque l\'on clique sur le lien de cette rubrique, on est automatiquement redirigé vers cet article. Choisissez quelles règles vous souhaitez utiliser pour déterminer si la rubrique doit être redirigée ou non et, le cas échéant, vers quel article elle doit être rédirigée. Les différentes règles ci-dessous sont testées dans cet ordre. Si aucune règle ne définit un article de redirection, alors la rubrique sera affichée (pas de redirection).',
	'explication_plus_recent' => 'Rediriger la rubrique vers l\'article le plus récent de la rubrique ? (Sans effet si la rubrique ne contient pas d\'article.)',
	'explication_plus_recent_branche' => 'Rediriger la rubrique vers l\'article le plus récent de la branche (soit parmi les articles de la rubrique et de ses sous-rubriques) ?',
	'explication_sousrubrique_titre' => 'Rediriger la rubrique vers la première sous-rubrique (tri par rang et par titre) ?',
	'explication_rang_un'=> 'Si les articles de la rubrique sont numérotés, rediriger vers l\'article ayant le plus petit rang ?',
	'explication_un_article' => 'Si la rubrique ne contient qu\'un seul article publié, rediriger vers cet article ?',
	'label_article_accueil' => 'Article d\'accueil',
	'label_plus_recent' => 'Article le plus récent',
	'label_plus_recent_branche' => 'Article de la branche le plus récent',
	'label_rang_un' => 'Article numéroté',
	'label_un_article' => 'Seul article de la rubrique',
	'label_sousrubrique_titre' => 'Sous-rubrique (par titre)',
);

?>