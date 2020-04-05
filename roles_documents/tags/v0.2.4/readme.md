# Plugin rôles de documents

Le plugin est utilisable, mais encore au stade de "proof of concept".

## À quoi ça sert ?

Ce plugin permet d'ajouter des rôles aux documents. Le but est de proposer une alternative à la gestion des logos traditionnels de SPIP afin de gérer ceux-ci sous forme de documents, et surtout d'étendre celle-ci en permettant de définir d'autres rôles que les logos.
Par défaut, 2 rôles sont proposés : `logo` et `logo de survol`. Selon les besoins du squelette utilisé, d'autres rôles peuvent être ajoutés afin d'identifier des documents : ceux qui servent de visuel principal à un article, pour la bannière, pour la vignette dans les listes, etc.

## Ajouter d'autres rôles aux documents

Pour ajouter vos rôles aux documents, il faut utiliser le pipeline `declarer_tables_objet_sql`.
Par exemple, mettons qu'on ait besoin de 3 rôles supplémentaires pour des documents liés à des livres : `couverture`, `4ème de couverture` et `extrait`.
Dans un plugin de squelettes, le pipeline pourrait ressembler à ça :

````
function skel_declarer_tables_objets_sql($tables){

	// 3 nouveaux roles à utiliser pour un objet 'livre'
	$nouveaux_roles_titres = array(
		'couverture'  => 'skel:role_couverture',
		'4couverture'  => 'skel:role_4couverture',
		'extrait' => 'skel:extrait'
	);
	$nouveaux_roles_objets = array(
		'livres' => array(
			'choix' =>  array_keys($nouveaux_roles_titres),
			'defaut' => ''
		)
	);

	// anciens rôles (par défaut 'logo' et 'logo_survol' pour tous les objets)
	$anciens_roles_titres = is_array($tables['spip_documents']['roles_titres']) ? $tables['spip_documents']['roles_titres'] : array();
	$anciens_roles_objets = is_array($tables['spip_documents']['roles_objets']) ? $tables['spip_documents']['roles_objets'] : array();

	// on mélange le tout
	$roles_titres = array_merge($nouveaux_roles_titres,$anciens_roles_titres);
	$roles_objets = array_merge($nouveaux_roles_objets,$anciens_roles_objets);
	array_set_merge($tables, 'spip_documents', array(
		"roles_titres" => $roles_titres,
		"roles_objets" => $roles_objets
	));

	return $tables;
}
````
