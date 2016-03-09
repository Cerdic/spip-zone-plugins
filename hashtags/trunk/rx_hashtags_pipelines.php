<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

function rx_hashtags_formulaire_charger($flux){

    return $flux;
}

function rx_hashtags_post_edition($flux){

    include_spip('inc/meta');
    $cfg = lire_config('rx_cfg_hashtags');

    //$table = $flux['args']['table'];                          // 'spip_articles',
    $table_objet = $flux['args']['table_objet'];                // 'articles',
    //$spip_table_objet = $flux['args']['spip_table_objet'];    // 'spip_articles',
    $objet = $flux['args']['type'];                             // 'article'
    $id_objet = $flux['args']['id_objet'];                      // '23'

    // spip_log("$objet . $id_objet . $table",_LOG_ERREUR);

    if ( isset($table_objet) || array_key_exists($table_objet,$cfg) ) {

        # Patterns Hashtags!
        $pattern = "~#(!->|->|!)*(\\w|\\')+(?![^<]*[]>|[^<>]*<\\/)~u";

        foreach ($cfg[$table_objet]['champs'] as $champ){

            if ( preg_match_all($pattern, $flux['data'][$champ], $matches) ) {

                # Déclarer le tableau de mots à lier
                $id_mots = array();

                # Récuperer les tags contenus  dans le texte et supprimer les doublons éventuels
                $tags = array_unique($matches['0']);
                $tags = preg_replace('`#(!->|!|->)*`','',$tags);

                # Verifier si les mots clef n'existent pas déjà sur le groupe demandé
                if ($req = sql_allfetsel('titre, id_mot', 'spip_mots', sql_in('titre', $tags))) {
                    foreach ($req as $ligne => $champ) {
                        $tagsConnus[] = $champ['titre'];
                        $id_mots[] = $champ['id_mot'];
                    }
                }

                $tagsACreer = ($tagsConnus)
                    ? array_diff($tags, $tagsConnus) : $tags;

                # Création des mots clefs inexistants et recuperation de leur id respectif
                if (count($tagsACreer)) {

                    $grpmot = sql_fetsel('titre', 'spip_groupes_mots');

                    $ins = array();
                    foreach ($tagsACreer as $titre)
                        $ins[] = array(
                            'titre' => $titre,
                            'id_groupe' => $cfg[$table_objet]['groupes'],
                            'type' => $grpmot['titre'] );

                    # Inserer tous les mots puis récuperer les ids ...
                    if (count($ins))
                        if (sql_insertq_multi("spip_mots", $ins))
                            if ($req = sql_allfetsel('id_mot', 'spip_mots', sql_in('titre', $tagsACreer)))
                                foreach ($req as $ligne => $champ)
                                    $id_mots[] = $champ['id_mot'];
                }

                # Charger l'api d édition de liens
                include_spip('action/editer_liens');

                # Associer les mots clefs a l'objet!
                if (count($id_mots))
                    objet_associer(array("mot" => $id_mots), array($objet => $id_objet));
            }
        }
    }

    return $flux;
}

function rx_hashtags_declarer_tables_interfaces($interfaces) {

    # Ajouter les traitements qui vont bien en fonctions de la config des mot clefs
    if (isset($GLOBALS['meta']['rx_cfg_hashtags']) AND $cfg_hashtags = $GLOBALS['meta']['rx_cfg_hashtags'])
        foreach (unserialize($cfg_hashtags) as $k => $v)
            foreach ($v['champs'] as $champ)
                $interfaces['table_des_traitements'][strtoupper($champ)][$k] =
                    isset ( $interfaces['table_des_traitements'][strtoupper($champ)][$k] )
                        ? str_replace("%s",
                                      "traitements_hashtags(%s, $v[groupes])",
                                      $interfaces['table_des_traitements'][strtoupper($champ)][$k])
                        : str_replace("%s",
                                      "traitements_hashtags(%s, $v[groupes])",
                                      $interfaces['table_des_traitements'][strtoupper($champ)][0]);

    return $interfaces;
}

function rx_hashtags_post_typo($flux){
    //$flux = preg_replace('~#(&nbsp;| )\!+(?![^<|-]*>|[^<>]*<\/)~u','#!',$flux);
    return $flux;
}