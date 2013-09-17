<?php

function genie_activite_editoriale_alerte_dist() {
    if (function_exists('lire_config')){
        $config_champ = lire_config('activite_editoriale/champ','maj_rubrique');
    }
    
    switch ($config_champ){
        case 'maj_rubrique':
            activite_tester_maj_rubrique();
            break;
        case 'date_modif_branche':
            activite_tester_date_modif_branche();
            break;
        case 'date_modif_rubrique':
            activite_tester_date_modif_rubrique();
            break;
        }    
	return 0;
}

function activite_tester_maj_rubrique(){
    if ($rubLists = sql_select("*", "spip_rubriques", "`extras_delai` != '' and TO_DAYS(NOW()) - TO_DAYS(maj) >= `extras_delai`")) {
		while($list = sql_fetch($rubLists)) {
		  activite_editoriale_envoyer_mail($list);
		}
    }
}

function activite_tester_date_modif_branche(){
    if ($rubLists = sql_select(array('id_rubrique','extras_delai','extras_identifiants','titre'), "spip_rubriques", "`extras_delai` != ''")){
        include_spip('inc/utils');
        while($list = sql_fetch($rubLists)){
            
            $date_modif = trim(recuperer_fond('inclure/maj_rubrique',array('id_rubrique'=>$list['id_rubrique'])));
            
                if (age_rubrique($date_modif)>$list['extras_delai']){
                    $list['maj'] = $date_modif;
                    activite_editoriale_envoyer_mail($list);
                    //echo "s;";
                }
        }
    
    }
}

function activite_tester_date_modif_rubrique(){
    if ($rubLists = sql_select(array('id_rubrique','extras_delai','extras_identifiants','titre'), "spip_rubriques", "`extras_delai` != ''")){
        include_spip('inc/utils');
        while($list = sql_fetch($rubLists)){
            
            $date_modif = trim(recuperer_fond('inclure/maj_rubrique',array('id_rubrique'=>$list['id_rubrique'])));

                if (age_rubrique($date_modif)>$list['extras_delai']){
                    $list['maj'] = $date_modif;
                    activite_editoriale_envoyer_mail($list);

                }
        }
    
    }
}

function activite_editoriale_envoyer_mail($list){
	
    $envoyer_mail = charger_fonction('envoyer_mail', 'inc');
    $subject = _T('activite_editoriale:rubrique_doit_maj');
    include_spip('activite_editoriale_fonctions');
    $url = $GLOBALS['meta']['adresse_site'].'/ecrire/?exec=rubrique&id_rubrique='.$list['id_rubrique'];
    $body = _T('activite_editoriale:rubrique_pas_maj',array('titre'=>$list['titre'],'jours'=>age_rubrique($list['maj'])))."\n\n";
    $body = $body._T('activite_editoriale:gestionnaire')."\n\n";
    $body = $body.$url;
   
    if ($auteurLists = sql_select("*", "spip_auteurs", "id_auteur in (".$list['extras_identifiants'].")")) {
        while($auteurs = sql_fetch($auteurLists)) {
        var_dump($auteurs);
            $to = $auteurs['email'];
            if ($envoyer_mail($to, $subject, $body)) {
                spip_log("Message envoyé à".$to, "activite_editoriale");
            } else {
                spip_log("Message n'a pu être envoyé à ".$to, "activite_editoriale");
            }
        }
    }
	$to = '';
    foreach (explode(',',activite_editoriale_emails($list['extras_identifiants'])) as $to){
    	
    	if ($to!=''){
    		
	    	if ($envoyer_mail($to, $subject, $body)) {
	                spip_log("Message envoyé à".$to, "activite_editoriale");
	            } else {
	                spip_log("Message n'a pu être envoyé à ".$to, "activite_editoriale");
	            }
	    	}
    }


}

function activite_editoriale_emails($champ){
	$champ = ','.str_replace(' ','',$champ).',';
	$champ = preg_replace('#,[0-9]*,#',',',$champ);
	return $champ;
}
?>