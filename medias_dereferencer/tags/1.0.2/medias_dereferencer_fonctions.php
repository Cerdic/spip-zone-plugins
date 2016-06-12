<?php

/**
 * Fonctions utiles au plugin Déréférencer les médias.
 *
 * @plugin     Déréférencer les médias
 *
 * @copyright  2015
 * @author     Teddy Payet
 * @licence    GNU/GPL
 */
if (!defined('_ECRIRE_INC_VERSION')) {
    return;
}

/**
 * Lister les champs de type text (TINYTEXT, TEXT, MEDIUMTEXT, et LONGTEXT) des différentes principales de SPIP.
 *
 * @return array
 *         Tableau avec pour clé le nom de la table.
 */
function medias_lister_champs_texte()
{
    include_spip('base/objets');
    $lister_tables_objets_sql = lister_tables_objets_sql();
    $lister_tables_principales = array_keys(lister_tables_principales());
    $champs_texte = array();
    foreach ($lister_tables_objets_sql as $table => $valeur) {
        // On ne prend que les objets qui font partis des tables principales de SPIP.
        // Donc, on ne prend pas les tables telles que spip_visites, spip_referers, etc.
        // C'est une sécurité.
        $id_primary_double = preg_match('/,/', $valeur['key']['PRIMARY KEY']); // l'id_primary doit faire référence à un seul champ
        if (in_array($table, $lister_tables_principales) and $id_primary_double == 0) {
            $champs_texte[$table]['id_primary'] = $valeur['key']['PRIMARY KEY'];
            $champs_texte[$table]['objet'] = objet_type($champs_texte[$table]['id_primary']);
            $champs_texte[$table]['statut'] = (isset($valeur['field']['statut']) ? true : false);
            $champs_texte[$table]['publie'] = ($champs_texte[$table]['statut'] ? $valeur['statut'][0]['publie'] : false);

            foreach ($valeur['field'] as $champs => $descriptif) {
                if (preg_match('/text/', $descriptif)) {
                    $champs_texte[$table]['texte'][] = $champs;
                }
            }
            $champs_texte[$table]['texte'] = implode(',', $champs_texte[$table]['texte']);
            if (!isset($champs_texte[$table]['texte'])) {
                unset($champs_texte[$table]);
            }
        }
    }
    $champs_texte = array_filter($champs_texte);

    return $champs_texte;
}

/**
 * On regarde les raccourcis typo <docXXX> <embXXX> <imgXXX> utilisés
 * dans les rubriques de la région.
 *
 * @return array
 */
function medias_lister_medias_used_in_text()
{
    include_spip('base/abstract_sql');

    $tables_texte = medias_lister_champs_texte();
    $documents = array();
    foreach ($tables_texte as $table => $champs) {
        $statut_requete = '';
        if (isset($champs['statut']) and $champs['statut'] and $champs['objet'] != 'auteur') {
            $statut_requete = ', statut';
        }
        $resultats = sql_allfetsel($champs['id_primary'].' as id_primary, CONCAT('.$champs['texte'].') as texte_tmp'.$statut_requete, $table);
        foreach ($resultats as $resultat => $value) {
            // On recherche les raccourcis typographiques
            if (preg_match_all('/(doc|img|emb)([0-9]+)/', $value['texte_tmp'], $docs)) {
                // On a au moins un résultat, alors on commence le traitement.
                if (isset($value['statut']) and $value['statut'] == $champs['publie']) {
                    // l'objet a un statut et est publié ou actif,
                    // alors le document doit-être publié aussi.
                    $statut = 'publie';
                } elseif (isset($value['statut']) and $value['statut'] != $champs['publie']) {
                    // l'objet a un statut et n'est publié ou actif,
                    // alors le document doit-être en préparation.
                    $statut = 'prepa';
                } elseif (!isset($value['statut'])) {
                    // L'objet n'a pas de statut
                    // et donc son affichage n'est pas conditionné par le statut,
                    // alors le document sera publié.
                    $statut = 'publie';
                }
                // On stocke maintenant toutes ces infos pour chaque document trouvé.
                foreach ($docs[2] as $id_doc) {
                    // structure du tableau :
                    // 0 : id_document
                    // 1 : id_objet
                    // 2 : objet
                    // 3 : vu (oui ou non)
                    // 4 : statut du document
                    $documents[] = array(
                        'id_document' => $id_doc,
                        'id_objet' => $value['id_primary'],
                        'objet' => $champs['objet'],
                        'vu' => 'oui',
                        'statut' => $statut,
                    );
                }
            }
        }
    }
    // asort($documents);
    // $documents = array_values(array_unique($documents));

    return $documents;
}

/**
 * Mettre à jour le statut des documents publiés liés à des objets non publiés.
 * Ces documents doivent donc avoir un statut 'prepa' et non 'publie'.
 *
 * @return bool
 */
function medias_maj_documents_lies()
{
    include_spip('base/abstract_sql');
    include_spip('base/objets');
    $message_log = array();
    $message_log[] = "\n-----";
    $message_log[] = date_format(date_create(), 'Y-m-d H:i:s');
    $message_log[] = 'Fonction : '.__FUNCTION__;
    if (session_get('id_auteur')) {
        // S'il y a un auteur authentifié, on indique que c'est lui qui a lancé l'action.
        $message_log[] = "L'action a été lancé par l'auteur #".session_get('id_auteur').', '.session_get('nom').' ('.session_get('statut').')';
    } else {
        // S'il n'y a pas d'auteur authentifié, c'est SPIP qui lance le script en tâche de fond.
        $message_log[] = "L'action a été lancé par SPIP en tâche de fond.";
    }

    // On ne s'occupe que des objets pour lesquels on a des liens avec des documents.
    $objets_lies = sql_fetsel('DISTINCT objet', 'spip_documents_liens');
    foreach ($objets_lies as $objet_lie) {
        // exemple de requête demandée :
        // SELECT * FROM spip_documents
        // WHERE id_document IN (SELECT DISTINCT id_document FROM spip_documents_liens WHERE objet='article' AND id_objet IN (SELECT id_article FROM spip_articles WHERE statut NOT IN ('publie')))
        // AND statut IN ('publie')
        // ***
        // Sélectionner tous les documents publiés liés à des objets non publiés
        // ***
        $documents = sql_allfetsel('id_document,statut', 'spip_documents', "statut IN ('publie') AND id_document IN (SELECT DISTINCT id_document FROM spip_documents_liens WHERE objet='".$objet_lie."' AND id_objet IN (SELECT ".id_table_objet($objet_lie).' FROM '.table_objet_sql($objet_lie)." WHERE statut NOT IN ('publie')))");
        if (is_array($documents) and count($documents) > 0) {
            foreach ($documents as $document) {
                if (sql_updateq('spip_documents', array('statut' => 'prepa'), 'id_document='.$document['id_document'])) {
                    $message_log[] = 'Le statut du document #'.$document['id_document'].' lié à l\'objet '.$objet_lie.' a bien été mis à jour avec le statut \''.$document['statut'].'\'';
                }
            }
        }
    }
    // Par défaut, le message de log a 4 entrées. Voir en début de la présente fonction.
    if (count($message_log) == 4) {
        $message_log[] = 'Il n\'y a pas eu d\'action à faire en base de données.';
    }
    // On met l'heure de fin de la procédure dans le message de log
    $message_log[] = date_format(date_create(), 'Y-m-d H:i:s');
    $message_log[] = "-----\n";
    // Et maintenant on stocke les messages dans un fichier de log.
    spip_log(implode("\n", $message_log), 'medias_dereferencer');

    return true;
}

/**
 * Cette fonction récupère les medias qui ont été utilisé par raccourcis typographiques dans les champs de type text. Et crée les liens entre le media et l'objet. Puis met à jour le statut du media.
 *
 * @uses medias_lister_medias_used_in_text()
 *
 * @return [type] [description]
 */
function medias_maj_documents_non_lies()
{
    include_spip('base/abstract_sql');
    include_spip('base/objets');
    $documents_raccourcis = medias_lister_medias_used_in_text();
    $message_log = array();
    $message_log[] = "\n-----";
    // On met l'heure de début de la procédure dans le message de log
    $message_log[] = date_format(date_create(), 'Y-m-d H:i:s');
    $message_log[] = 'Fonction : '.__FUNCTION__;
    if (session_get('id_auteur')) {
        // S'il y a un auteur authentifié, on indique que c'est lui qui a lancé l'action.
        $message_log[] = "L'action a été lancé par l'auteur #".session_get('id_auteur').', '.session_get('nom').' ('.session_get('statut').')';
    } else {
        // S'il n'y a pas d'auteur authentifié, c'est SPIP qui lance le script en tâche de fond.
        $message_log[] = "L'action a été lancé par SPIP en tâche de fond.";
    }

    // On lance les opérations uniquement si on a des documents utilisés en raccourcis.
    if (is_array($documents_raccourcis) and count($documents_raccourcis) > 0) {
        foreach ($documents_raccourcis as $document) {
            if (sql_countsel('spip_documents_liens', array('id_document='.sql_quote($document['id_document']), 'id_objet='.sql_quote($document['id_objet']), 'objet='.sql_quote($document['objet']), 'vu NOT IN ('.sql_quote($document['vu']).')'))) {
                // Le lien de ce media avec l'objet existe mais n'a pas la bonne valeur dans 'vu'
                // Donc on met à jour la valeur de 'vu' pour ce lien.
                if (sql_updateq(
                    'spip_documents_liens',
                    array(
                        'vu' => $document['vu'],
                    ),
                    'id_document='.sql_quote($document['id_document'])
                    .' AND id_objet='.sql_quote($document['id_objet'])
                    .' AND objet='.sql_quote($document['objet'])
                    .' AND vu NOT IN ('.sql_quote($document['vu']).')'
                )) {
                    $message_log[] = 'Le lien entre le document #'.$document['id_document'].' et l\'objet '.$document['objet'].' #'.$document['id_objet'].' a bien été mis à jour avec la vu \''.$document['vu'].'\'';
                }
                // et on met à jour le statut dudit document si le statut est différent uniquement.
                if (sql_updateq('spip_documents', array('statut' => $document['statut']), 'id_document='.sql_quote($document['id_document']).' AND statut NOT IN ('.sql_quote($document['statut']).')')) {
                    $message_log[] = 'Le statut du document #'.$document['id_document'].' lié à l\'objet '.$document['objet'].' #'.$document['id_objet'].' a bien été mis à jour avec le statut \''.$document['statut'].'\'';
                }
            } elseif (!sql_countsel('spip_documents_liens', array('id_document='.sql_quote($document['id_document']), 'id_objet='.sql_quote($document['id_objet']), 'objet='.sql_quote($document['objet']), 'vu IN ('.sql_quote($document['vu']).')'))) {
                // Le lien de ce média avec l'objet n'existe pas
                // Alors on l'insère dans la table
                if (sql_insertq('spip_documents_liens', array(
                    'id_document' => $document['id_document'],
                    'id_objet' => $document['id_objet'],
                    'objet' => $document['objet'],
                    'vu' => $document['vu'],
                ))) {
                    $message_log[] = 'Le lien entre le document #'.$document['id_document'].' et l\'objet '.$document['objet'].' #'.$document['id_objet'].' a bien été inséré en base de données avec la vu \''.$document['vu'].'\'';
                }
                // et on met à jour le statut dudit document si le statut est différent uniquement.
                if (sql_updateq('spip_documents', array('statut' => $document['statut']), 'id_document='.sql_quote($document['id_document']).' AND statut NOT IN ('.sql_quote($document['statut']).')')) {
                    $message_log[] = 'Le statut du document #'.$document['id_document'].' lié à l\'objet '.$document['objet'].' #'.$document['id_objet'].' a bien été mis à jour avec le statut \''.$document['statut'].'\'';
                }
            }
        }
    }
    // Par défaut, le message de log a 4 entrées. Voir en début de la présente fonction.
    if (count($message_log) == 4) {
        $message_log[] = 'Il n\'y a pas eu d\'action à faire en base de données.';
    }
    // on met l'heure de fin de la procédure dans le message de log
    $message_log[] = date_format(date_create(), 'Y-m-d H:i:s');
    $message_log[] = "-----\n";
    // Et maintenant on stocke les messages dans un fichier de log.
    spip_log(implode("\n", $message_log), 'medias_dereferencer');

    return true;
}
