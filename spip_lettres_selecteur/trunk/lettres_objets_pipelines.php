<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

    function lettres_objets_editer_contenu_objet($flux) {
        if ($flux['args']['type'] == 'lettre') {

            $saisie = recuperer_fond("inclure/choisir_articles", $flux['args']['contexte']);
            $flux['data'] = preg_replace('/(<li class="editer_texte">)/',$saisie."$1",$flux['data']);
        }
        
        return $flux;
    }

    function lettres_objets_formulaire_charger($flux) {
        // Ne traiter que le formulaire editer_lettre
        if ($flux['args']['form'] != "editer_lettre")
            return $flux;

		include_spip('base/abstract_sql');

        $id_lettre = intval($flux['args']['args'][0]);
        
        if (!is_null($id_lettre)) {
            $res = sql_select(
                'id_objet,objet',
                'spip_lettres_liens',
                'id_lettre = '.$id_lettre
            );
         
            while ($r = sql_fetch($res)) {
                $ids_article[]= $r['objet']."|".$r['id_objet'];
            }
            
            $flux['data']['ids_article'] = $ids_article;
        }
        
        return $flux;
    }


    function lettres_objets_formulaire_verifier($flux) {
        // Ne traiter que le formulaire editer_lettre
        if ($flux['args']['form'] != "editer_lettre")
            return $flux;
        
        //Obtenir la liste des articles choisis
        $ids_article = _request('ids_article');
        
        //Ne rien vérifier si la liste est vide
        if (is_null($ids_article))
            return $flux;
        
        //Etre sur d'avoir un tableau d'id article
        if (!is_array($ids_article))
            $ids_article = array($ids_article);

        //Controler le bon format de la selection "article|xx"        

        foreach($ids_article as $valeur) {
            if (!$valeur) {
                $flux['data']['ids_article'] .= _T('lettre_objets:erreur_choix_article_valeur');
                continue;
            }

            list($objet,$id_objet) = split("\|",$valeur);            

            if ($objet != "article")
                $flux['data']['ids_article'] .= _T('lettres_objets:erreur_choix_objets');
                
            if (intval($id_objet) < 1)
                $flux['data']['ids_article'] .= _T('lettres_objets:erreur_choix_objets_id');
        }
        
        return $flux;
    }


    function lettres_objets_formulaire_traiter($flux) {
    
        // Ne traiter que le formulaire editer_lettre
        if ($flux['args']['form'] != "editer_lettre")
            return $flux;
    
        // Ne pas traiter si problème en amont
        if ($flux['data']['message_ok'] != "ok")
            return $flux;

		include_spip('base/abstract_sql');

        //Connaitre l'id lettre
        //La fonction traiter ne remonte pas l'information autrement que dans les informations de redirection
        preg_match("/id_lettre=(\d*)/",$flux['data']['redirect'],$id_lettre);
        $id_lettre = $id_lettre[1];

        //Obtenir la liste des articles choisis
        $ids_article = _request('ids_article');
        
        //Ne rien vérifier si la liste est vide
        if (is_null($ids_article))
            return $flux;
        
        //Etre sur d'avoir un tableau d'id article
        if (!is_array($ids_article))
            $ids_article = array($ids_article);
    
        //Retirer les associations préexistantes
        sql_delete(
            'spip_lettres_liens',
            "id_lettre = ". $id_lettre
        );

        //Mettre à jour la table de jointure
        foreach($ids_article as $valeur) {
            list($objet,$id_objet) = split("\|",$valeur);            

            sql_insertq(
                'spip_lettres_liens',
                array(
                    'id_lettre' => $id_lettre,
                    'id_objet' => intval($id_objet),
                    'objet' => $objet
                )
            );

        }
        
        return $flux;
    }

?>
