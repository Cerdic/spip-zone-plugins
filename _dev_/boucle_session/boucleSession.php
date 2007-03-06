<?php

/** BOUCLE SESSION
 * Christian Lefebvre, Atos Worldline © 2006
 * Distribué sous licence GPL v2
 */

$GLOBALS['tables_principales']['spip_session']= array(
	'field' => array(
		'id_auteur' => 'bigint(21) NOT NULL',
		'nom' => 'text NOT NULL',
		'login' => 'VARCHAR(255) BINARY NOT NULL',
		'email' => 'tinytext NOT NULL',
		'lang' => 'VARCHAR(5)',
		'statut' => 'VARCHAR(255) NOT NULL',
	),
	'key' => array('PRIMARY KEY' => 'id_auteur')
);
$GLOBALS['table_des_tables']['session'] = 'session';

/* cette boucle fait toujours un tour de boucle unique */
function boucle_SESSION($id_boucle, &$boucles) {
	$boucle = &$boucles[$id_boucle];

	$code='
		//error_log("session : ".var_export($GLOBALS[\'auteur_session\'], 1));
';

	foreach($boucle->where as $w) {
		$code.="\n		if(!($w)) return '';";
	}

	$code.=<<<CODE
		\$SP++;
		if(is_array(\$GLOBALS['auteur_session'])) {
			\$Pile[\$SP]['id_auteur']= \$GLOBALS['auteur_session']['id_auteur'];
			\$Pile[\$SP]['nom']= \$GLOBALS['auteur_session']['nom'];
			\$Pile[\$SP]['login']= \$GLOBALS['auteur_session']['login'];
			\$Pile[\$SP]['email']= \$GLOBALS['auteur_session']['email'];
			\$Pile[\$SP]['lang']= \$GLOBALS['auteur_session']['lang'];
			\$Pile[\$SP]['statut']= \$GLOBALS['auteur_session']['statut'];
			\$prefs = spip_abstract_fetsel("prefs", "spip_auteurs",
					"id_auteur = " . \$GLOBALS['auteur_session']['id_auteur']);
			\$Pile[\$SP]['prefs']= unserialize(\$prefs['prefs']);
			//error_log("PREFS : ".var_export(\$Pile[\$SP]['prefs'],1));
		} else {
			\$Pile[\$SP]['id_auteur']='';
			\$Pile[\$SP]['nom']='';
			\$Pile[\$SP]['login']='';
			\$Pile[\$SP]['email']='';
			\$Pile[\$SP]['lang']='';
			\$Pile[\$SP]['statut']='anonymous';
			\$Pile[\$SP]['prefs']= array();
		}
		return $boucle->return;
CODE;

	return $code;
}

function balise_PREFS($p) {
	if ($p->param && !$p->param[0][0]) {
		$p->code = '($Pile[$SP][\'prefs\']['
			.calculer_liste($p->param[0][1], $p->descr,
							$p->boucles, $p->id_boucle)
			.'])';
		$p->interdire_scripts = false;
		return $p;
	} else {
		$p->code = '($Pile[$SP][\'prefs\'])';
		$p->interdire_scripts = false;
		return $p;
	}
}

function critere_anonymous($idb, &$boucles, $crit) {
	$boucle = &$boucles[$idb];
	if($boucle->type_requete!='session') {
		error_log("Heu ...");
		return;
	}
	if($crit->not) {
		$boucle->where[]= "\$GLOBALS['auteur_session']!==''";
	} else {
		$boucle->where[]= "\$GLOBALS['auteur_session']===''";
	}
}

function critere_admin($idb, &$boucles, $crit) {
	$boucle = &$boucles[$idb];
	if($boucle->type_requete!='session') {
		error_log("Heu ...");
		return;
	}
	if($crit->not) {
		$boucle->where[]= "\$GLOBALS['auteur_session']['statut']!='0minirezo'";
	} else {
		$boucle->where[]= "\$GLOBALS['auteur_session']['statut']=='0minirezo'";
	}
}

function critere_visiteur($idb, &$boucles, $crit) {
	$boucle = &$boucles[$idb];
	if($boucle->type_requete!='session') {
		error_log("Heu ...");
		return;
	}
	if($crit->not) {
		$boucle->where[]= "\$GLOBALS['auteur_session']['statut']!='6forum'";
	} else {
		$boucle->where[]= "\$GLOBALS['auteur_session']['statut']=='6forum'";
	}
}

?>