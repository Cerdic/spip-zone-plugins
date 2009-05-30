<?php


if (!defined("_ECRIRE_INC_VERSION")) return;

// Initialise les reglages sous forme de tableau
function alertemotscles_notification($x) {
	$table=$x['args']['table'];
	$objet=(substr($table,0,5)=='spip_')?substr($table,5,-1):$table;
	if (substr($objet,-1)=='s') $objet=substr($objet,0,-1);
	$id_objet=$x['args']['id_objet'];
	$statut=$x['data']['statut'];
	$notif = 'notification_'.$objet.'_'.$statut;

	if (!is_array($GLOBALS['alertemotscles']
	= @unserialize($GLOBALS['meta']['alertemotscles'])))
		$GLOBALS['alertemotscles'] = array();


	if ((isset($table)) && ($GLOBALS['alertemotscles'][$table.'_'.$statut]=='on')) {
		include_spip('base/abstract_sql');
		$m=array();
		$q=sql_select('id_mot', 'spip_mots_'.$objet.'s', 'id_'.$objet.'='.intval($id_objet));
		while ($r=sql_fetch($q))
			$m[]=$r['id_mot'];
		if (count($m)) {
			$a=array();
			$q=sql_select('a.email', 'spip_mots_auteurs as m, spip_auteurs as a', 'm.id_auteur=a.id_auteur AND m.id_mot IN ('.implode(',',$m).')');
			while ($r=sql_fetch($q))
				$a[]=$r['email'];
			if (count($a)) {
				alertemotscles_envoi($a, $notif, $table, $objet, $id_objet, $m, $x);
			}
		}

	}
	return $x;
}


// Envoi des notifications
function alertemotscles_envoi($emails, $notif, $table, $objet, $id_objet, $mots=array()) {
	include_spip('inc/filtres'); # pour email_valide()
	include_spip('inc/utils'); # pour _T() et les url propres
	charger_generer_url();

	$obj=sql_fetsel('*', $table, 'id_'.$objet.'='.$id_objet);
	$obj['lien_public']= url_absolue(generer_url_public($objet,'id_'.$objet.'='.$id_objet));
	$obj['lien_prive']= url_absolue(generer_url_prive($objet,'id_'.$objet.'='.$id_objet));
	$fonction_propre='generer_url_'.$objet;
	if (function_exists($fonction_propre))
		$obj['lien_public'] = url_absolue($fonction_propre($id_objet));
	else spip_log ('!!!pas de fonction propre :'.$fonction_propre);

	$mots_titres=array();
	$q=sql_select('titre', 'spip_mots', 'id_mot IN ('.implode(',',$mots).')');
	while ($r=sql_fetch($q))
		$mots_titres[]=$r['titre'];
	$obj['mots'] = implode(', ',$mots_titres);

	$subject=_T('alertemotscles:'.$notif.'_subject',$obj);
	$body=_T('alertemotscles:'.$notif.'_body',$obj);

	$envoyer_mail = charger_fonction('envoyer_mail','inc');
	// Attention $email peut etre une liste d'adresses, et on verifie qu'elle n'a pas de doublon
	$emails = array_unique($emails);
	foreach ($emails as $email){
		spip_log('alertemotscles_envoi:'.$email.'/'.$subject.'('.$obj['mots'].')'.' - '.$obj['lien_public']);
		if (email_valide(trim($email)))
			$envoyer_mail($email, $subject, $body);
	}
}

?>
