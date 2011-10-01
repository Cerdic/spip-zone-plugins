<?php
/**
 * Plugin Simple Calendrier pour Spip 2.1.2
 * Licence GPL (c) 2010-2011 Julien Lanfrey
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

//include_spip('inc/actions');
//include_spip('inc/editer');
include_spip('inc/simplecal_utils');

// NOTES : 
// paramètres des fonctions = fournis lors de l'appel du formulaire par #FORMULAIRE_xxx{arg1, arg2, arg3}
// dans editer_evenement.html, d'ou vient le #ENV{action} ?
// => reponse : du tableau de valeurs de la fonction charger. S'il n'y est pas (recommandé), il s'agit de l'url de la page en cours. cf. http://www.spip.net/fr_article4151.html - section "champs particuliers"
// id_evenement sert pour la fonction Traiter (et pour le choix de la rubrique parente.)



// Choix par defaut des options de presentation
function evenements_edit_config($row) {
    global $spip_ecran, $spip_lang, $spip_display;

    $config = $GLOBALS['meta'];
    $config['lignes'] = ($spip_ecran == "large")? 8 : 5;
    $config['afficher_barre'] = $spip_display != 4;
    $config['langue'] = $spip_lang;

    $config['restreint'] = ($row['statut'] == 'publie');
    return $config;
}

// C comme Charger 
function formulaires_editer_evenement_charger_dist($new='', $id_evenement='', $row=array(), $redirect='', $config=array()){
    
    $valeurs = array(
        'id_evenement'=>'',
        'id_secteur'=>'',
        'id_parent'=>'',
        'titre'=>'',
        'date_debut'=>'',
        'date_fin'=>'',
        'lieu'=>'',
        'descriptif'=>'',
        'texte'=>'',
        'ref'=>'',
        'lien_titre'=>'',
        'lien_url'=>'',
        'config_rubrique'=>'',
        'config_reference'=>'',
        'config_descriptif'=>'',
        'config_texte'=>'',
        'config_lieu'=>'',
        'config_lien'=>'');
    
    
    $valeurs['id_evenement'] = $id_evenement;
    $valeurs['id_secteur'] = $row['id_secteur'];
    $valeurs['id_parent'] = $row['id_rubrique'];
    
    
    // --- BIZARRE : à quoi ça sert ?? 
    if ($row['titre']) {
        $valeurs['titre'] = $row['titre'];
    } else {
        $valeurs['titre'] = simplecal_get_titre_from_obj($row['type'], $row['id_objet']);
    }
    // --- FIN de la bizarrerie...
    
    if (!empty($row['date_debut'])){
        $valeurs['date_debut'] = date_sql2affichage($row['date_debut']);
    }
    if (!empty($row['date_fin'])){
        $valeurs['date_fin'] = date_sql2affichage($row['date_fin']);
    }    
    if (!empty($row['lieu'])){
        $valeurs['lieu'] = $row['lieu'];
    }
    if (!empty($row['descriptif'])){
        $valeurs['descriptif'] = $row['descriptif'];
    }
    if (!empty($row['texte'])){
        $valeurs['texte'] = $row['texte'];
    }    
    if (!empty($row['lien_titre'])){
        $valeurs['lien_titre'] = $row['lien_titre'];
    }
    if (!empty($row['lien_url'])){
        $valeurs['lien_url'] = $row['lien_url'];
    }

    $valeurs['ref'] = simplecal_get_ref_from_obj($row['type'], $row['id_objet']);
    $valeurs['config_rubrique'] = $config['simplecal_rubrique'];
    $valeurs['config_reference'] = $config['simplecal_reference'];
    $valeurs['config_descriptif'] = $config['simplecal_descriptif'];
    $valeurs['config_texte'] = $config['simplecal_texte'];
    $valeurs['config_lieu'] = $config['simplecal_lieu'];
    $valeurs['config_lien'] = $config['simplecal_lien'];
    
    return $valeurs;
}


// V comme Verifier
function formulaires_editer_evenement_verifier_dist($new='', $id_evenement='', $row=array(), $redirect='', $config=array()){

    $erreurs = array();
    
    // titre obligatoire
    $titre = trim(_request('titre'));
    if (!$titre) {
        $erreurs['titre'] = _T('simplecal:validation_titre');
    }
    
    
    // Date de debut obligatoire
    $date_debut = trim(_request('date_debut'));
    if (!$date_debut) {
        $erreurs['date_debut'] = _T('simplecal:validation_date_debut');
    } 
    // Date de debut saisie correctement ?
    else {
        if (date_saisie2sql($date_debut) == '0000-00-00 00:00:00'){
            $erreurs['date_debut'] = _T('simplecal:validation_date_format');
        }
    }
    
    // Date de fin saisie correctement ?
    $date_fin = trim(_request('date_fin'));
    if ($date_fin && date_saisie2sql($date_fin) == '0000-00-00 00:00:00'){
        $erreurs['date_fin'] = _T('simplecal:validation_date_format');
    }
    
    if ($config['simplecal_reference'] == 'oui'){
        // Ref saisie correctement ?
        $ref = trim(_request('ref'));
        if ($ref){
            if (!simplecal_is_ref_ok($ref)){
                $erreurs['ref'] = _T('simplecal:validation_refobj_format');
            }
            else {
                // L'objet en question existe t-il ?
                $tab = simplecal_get_tuple_from_ref($ref);
                $type = $tab['type'];
                $id_objet = $tab['id_objet'];
                
                $existe = sql_fetsel("id_$type" ,"spip_".$type."s", "id_$type=".$id_objet);
                if (!$existe){
                    $erreurs['ref'] = _T('simplecal:validation_type_nexiste_pas', array('type'=>$type, 'id_objet'=>$id_objet));
                } 
            }
        }
    }
    
    if ($config['simplecal_rubrique'] != 'non'){
        $id_parent = intval(_request('id_parent'));
        if ($id_parent == 0){
            $erreurs['id_parent'] = _T('simplecal:validation_rubrique');
        }
    }

    
    if (count($erreurs) > 0){
        $erreurs['message_erreur'] = _T('simplecal:validation_corriger_svp');
    }

    
    
    return $erreurs;
}



// T comme Traiter
function formulaires_editer_evenement_traiter_dist($new='', $id_evenement='', $row=array(), $redirect='', $config=array()){
    
    //die("statut=".$row['statut']);
    include_spip('base/abstract_sql');
    
    $date_enregistrement = date('Y-m-d H:i:s');
    // ---
    $titre = trim(_request('titre'));
    $date_debut = date_saisie2sql(_request('date_debut'));
    $date_fin   = date_saisie2sql(_request('date_fin'));
    $lieu = trim(_request('lieu'));
    $descriptif = trim(_request('descriptif'));
    $ref = trim(_request('ref'));
    $texte = trim(_request('texte'));
    $lien_titre = trim(_request('lien_titre'));
    $lien_url = trim(_request('lien_url'));
    
    // parent
    $id_parent = trim(_request('id_parent'));
    if (empty($id_parent)){
        $id_parent = 0;
        $id_secteur = 0;
    }
    
    // calcul du secteur
    if ($id_parent != 0) {
        $row_tmp = sql_fetsel("id_secteur", "spip_rubriques", "id_rubrique=$id_parent");
        $id_secteur = $row_tmp['id_secteur'];
    }
    

    $data = array();
    

    // Mode 1ere création
    if ($new == 'oui'){
        $data['statut'] = 'prepa';
        $data['date'] = $date_enregistrement;
    } 
    // Mode modification
    else {
        $data['statut'] = $row['statut'];
    }

        
    // Autres champs
    $data['titre'] = $titre;
    $data['date_debut'] = $date_debut;
    $data['date_fin'] = $date_fin;
    
    if ($config['simplecal_lieu'] != 'non'){
        $data['lieu'] = $lieu;
    } else {
        // si gestion du lieu desactivée
        // => on ne touche pas aux données qu'ils pourraient y avoir... (réactivation ultérieure ?)
    }    
    if ($config['simplecal_descriptif'] != 'non'){
        $data['descriptif'] = $descriptif;
    } else {
        // si gestion du descriptif desactivée
        // => on ne touche pas aux données qu'ils pourraient y avoir... (réactivation ultérieure ?)
    }
    if ($config['simplecal_texte'] != 'non'){
        $data['texte'] = $texte;
    } else {
        // si gestion du texte desactivée
        // => on ne touche pas aux données qu'ils pourraient y avoir... (réactivation ultérieure ?)
    }
    if ($config['simplecal_lien'] != 'non'){
        $data['lien_titre'] = $lien_titre;
        $data['lien_url'] = $lien_url;
    } else {
        // si gestion du lien desactivée
        // => on ne touche pas aux données qu'ils pourraient y avoir... (réactivation ultérieure ?)
    }
    
    if ($config['simplecal_rubrique'] != 'non'){
        $data['id_rubrique'] = $id_parent;
        $data['id_secteur'] = $id_secteur;
    } else {
        // si gestion des rubriques desactivée
        // => on ne touche pas aux données qu'ils pourraient y avoir... (réactivation ultérieure ?)
    }
    
    if ($config['simplecal_reference'] == 'oui'){
        if (empty($ref)){
            $data['type'] = '';
            $data['id_objet'] = 0;
        } else {
            $tab = simplecal_get_tuple_from_ref($ref);
            $data['type'] = $tab['type'];
            $data['id_objet'] = $tab['id_objet'];
        }
    } else {
        // si gestion des references desactivée
        // => on ne touche pas aux données qu'ils pourraient y avoir... (réactivation ultérieure ?)
    }
    
    
    // Enregistrement / Mise à jour
    if ($new == 'oui'){
        $id_new = sql_insertq('spip_evenements', $data);
        $redirection = generer_url_ecrire("evenement_voir", "id_evenement=".$id_new);
    } else {
        sql_updateq('spip_evenements', $data, "id_evenement=".$id_evenement);
        $redirection = $redirect;
    }
    
        
    // Auteur
    if ($new == 'oui'){
        $id_auteur = $GLOBALS['visiteur_session']['id_auteur'];
        if ($id_auteur) {
            sql_insertq('spip_auteurs_evenements', array('id_auteur'=>$id_auteur, 'id_evenement'=>$id_new));
        }
    }
    
    
    $retour = array();
    $retour['redirect'] = $redirection;    
    
    return $retour;
}

?>
