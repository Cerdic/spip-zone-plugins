<?php
// balise #FORMULAIRE_INSCRIPTION2_MAJ : permet à l'utilisateur de mettre à jour ses infos stockées par inscription2
// !!! AUCUN ELEMENT COMPLEMENTAIRE N'EST GERE !!!
// (plugins echope, spip_liste, association... ou inscription2 date naissance, publication, domaines...)

if (!defined("_ECRIRE_INC_VERSION")) return;
include_spip('base/abstract_sql');

function balise_FORMULAIRE_INSCRIPTION2_MAJ ($p) {
	return calculer_balise_dynamique($p, 'FORMULAIRE_INSCRIPTION2_MAJ', array());}

// args[0] peut valoir "redac" ou "forum" 
function balise_FORMULAIRE_INSCRIPTION2_MAJ_stat($args, $filtres) {
	//initialiser mode d'inscription et adresse de retour
	$mode = $args[0];
	$retour = $args[1];
	
	if(!$mode || ($mode != 'redac' && $mode != 'forum')){
		$mode = $GLOBALS['meta']['accepter_inscriptions'] == 'oui' ? 'redac' : 'forum'; 
		$retour = $args[0];
	}
	
	if(!$retour)
		$retour = $GLOBALS['meta']["adresse_site"];
	
	return array($mode, $retour);
}

function balise_FORMULAIRE_INSCRIPTION2_MAJ_dyn($mode, $retour) {
  // vérifier que le mode d'inscription est OK 
	  if (!test_mode_inscription2_maj($mode)) return _T('pass_rien_a_faire_ici');
  
  // afficher le formulaire de maj si pas de POST
    if (!_request('email')) return array("formulaires/inscription2_maj", $GLOBALS['delais']);

  // lancement de la procédure de maj
	//recuperer les infos inserées par le visiteur
  	$var_user = array();
  	foreach(lire_config('inscription2') as $cle => $val) {
  		if($val != '' AND !ereg("^.+_(obligatoire|fiche|table).*$", $cle) AND _request($cle) != ''){
            $var_user[$cle] = _request($cle);
        }
    }
    
  // maj de la table spip_auteurs si nécessaire
    $post_email = _request('email');
    if (_request('nom')) $post_nom = _request('nom');
    if (_request('login')) $post_login = _request('login');
    
    if ($post_nom != $GLOBALS['auteur_session']['nom']
      OR $post_login != $GLOBALS['auteur_session']['login']
      OR $post_email != $GLOBALS['auteur_session']['email']) {
        spip_query("UPDATE spip_auteurs SET nom = "._q($post_nom).", login = "._q($post_login).", email = "._q($post_email)." 
                    WHERE id_auteur = ".$GLOBALS['auteur_session']['id_auteur']."
                    LIMIT 1");
        if (mysql_error() != '') return _T('inscription2:maj_profil_erreur').mysql_error();
    }
    
  // maj de la table spip_auteurs_elargis
    $Ta_exclure = array('newsletters','spip_listes_format','sites','zone','abonnement','option','article','zones','email','nom','bio','statut','login');
    $elargis = array();
    foreach($var_user as $cle => $val){
        if (in_array($cle, $Ta_exclure)) continue;
        $elargis[$cle] = "$cle = "._q($val) ;
    }
    spip_query("UPDATE spip_auteurs_elargis SET ".join(', ', $elargis)."
                WHERE id_auteur = ".$GLOBALS['auteur_session']['id_auteur']."
                LIMIT 1");
    if (mysql_error() != '') return _T('inscription2:maj_profil_erreur').mysql_error();
    
  // tout s'est bien passé
    return _T('inscription2:maj_profil_ok');
}

// y'a pas trop l'air de pouvoir récup la fct test_mode_inscription2 sans problèmes si on est pas en var_mode=recalcul
// alors bètement on la clone en changeant de nom... (pas tout compris sur ce coup la)
function test_mode_inscription2_maj($mode) {
	return (($mode == 'redac' AND $GLOBALS['meta']['accepter_inscriptions'] == 'oui')
		OR ($mode == 'forum' AND ($GLOBALS['meta']['accepter_visiteurs'] == 'oui'
		OR $GLOBALS['meta']['forums_publics'] == 'abo')));}

?>
