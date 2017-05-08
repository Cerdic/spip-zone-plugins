<?php
//!\ ceci est un exemple a adapter selon le besoin !

// Sécurité
if (!defined('_ECRIRE_INC_VERSION'))
    return;


// Chargement de l'environnement
function formulaires_catalogue_charger($id_article=0)
{
    $contexte = array();
    if ($id_article)
        $contexte['id_article'] = intval($id_article);
    else { // pas en parametre, alors dans l'environnement ?
        $contexte['id_article'] = intval(_request('id_article')?_request('id_article'):_request('id'));
    }

	return $contexte;
}


// Verification des saisies (traitement si ok, sinon rechargement avec messages d'erreurs)
function formulaires_catalogue_verifier($id_article=0)
{
	$erreurs = array();


	return $erreurs;
}


// Traitement des saisies (ayant passe la validation)
function formulaires_catalogue_traiter($id_article=0)
{
	$retours = array();
	$ok = true;
    // recuperer les parametres du formulaire.
    $quantite = floatval(_request('quantite'));
    $id_article = intval(_request('id_article'));
    $id_variante = intval(_request('id_cat_variante'));
    // preparer les parametres

    if (test_plugin_actif('PANIERS')) {
        include_spip('inc/paniers');
        include_spip('inc/session');
        // recuperer l'ID du panier...
        if ($id_panier = paniers_id_panier_encours()) {
            session_set(’id_panier’, $id_panier) ; // le mettre dans la session
        } else {
            session_set(’id_panier’) ; // s'assurer que la session ne contient pas un vieux panier
            $id_panier = paniers_creer_panier(); // creer un nouveau panier
        }
        // y rajouter la variante d'article choisie (en fait on fait le lien avec la table, il ira chercher prix_ht+tva ou prix...) -- idealement, il aurait fallu verifier que ca n'y est pas deja et le mettre a jour si c'est le cas, mais par chance la clef empeche les doublons grossiers et comme on sera redirige vers le panier on pourra modifier la quantite...
        if ($id_variante) {
            $ok = intval(sql_insertq(
                'spip_paniers_liens',
                array(
                    'id_panier' => $id_panier,
                    'id_objet' => $id_variante,
                    'objet' => 'cat_variantes',
                    'quantite' => $quantite,
                )
            ));
        } else {
            $ok = intval(sql_insertq(
                'spip_paniers_liens',
                array(
                    'id_panier' => $id_panier,
                    'id_objet' => $id_article,
                    'objet' => 'articles',
                    'quantite' => $quantite,
                )
            ));
        }
        // se presenter sur la page du panier pou valider+payer sa commande ou poursuivre ses achats
        $page = 'panier'; // il s'agit du squelette "panier.html" qui contiendrait #FORMULAIRE{panier,id_panier=#ENV{id_panier}}
        $retours['redirect'] = parametre_url($page, 'id_panier', $id_panier, '&'); //@: http://groups.google.com/group/spip/browse_thread/thread/a78e26d38d2ddecb
        if ($ok) {
            $retours['editable'] = TRUE;
            $retours['message_ok'] = _L("Article ajout&eacute;e au panier.<br />"). _T('paniers:panier_quantite_ok');
        } else {
            $retours['editable'] = TRUE;
            $retours['message_erreur'] = _L("Un probl&egrave;me technique est survenu lors de l'ajout de votre article au panier. Merci de recommencer.");
        }
        return $retours;
        header("Location: $page&id_panier=$id_panier"); exit;
    } elseif (test_plugin_actif('SPIPAL')) {
        // ajouter les champs supplementaires requis (et configures par/avec le plugin)
        $ref_produit = "$id_article-$id_variante-0";
        $code_devise = _request('monnaie');
        if (!$code_devise)
            $code_devise = 'EUR';
        $data = sql_fetsel('titre', 'spip_articles', "id_article=$id_article" );
        $nom_com = $data['titre'];
        // inserer l'achat dans la table des produits achetes
        if ($id_variante) {
            $data = sql_fetsel('prix_ht, tva, titre', 'spip_cat_variantes', "id_cat_variante=$id_variante" );
            $nom_com .= ' - '.$data['titre'];
        } else {
            $data = @sql_fetsel('prix_ht, tva', 'spip_articles', "id_article=$id_article" );
            if (!count($data))
                $data = @sql_fetsel('prix AS prix_ht, tva', 'spip_articles', "id_article=$id_article" );
            if (!count($data))
                $data = @sql_fetsel('prix AS prix_ht, 0 AS tva', 'spip_articles', "id_article=$id_article" );
        }
        $ok = intval(sql_insertq(
            'spip_produits',
            array(
                //'id_article' => $id_article,
                'ref_produit' => $ref_produit,
                'nom_com' => $nom_com,
                'don' => 0,
                'prix_unitaire_ht' => $data['prix_ht'],
                'tva' => $data['tva'],
            )
        ));
        if ($ok) {
            // ajouter les champs supplementaires requis (et configures par/avec le plugin)
            set_request('lc', utiliser_langue_visiteur() );
//          set_request('lc', $GLOBALS['visiteur_session']['lang'] );
            set_request('cmd', '_xclick');
            set_request('item_number', $ref_produit);
            set_request('business', $GLOBALS['spipal_metas']['compte_paypal']);
            set_request('quantity', $quantite);
            set_request('amount', (1+($data['tva']>1?$data['tva']/100:$data['tva']))*$data['prix_ht']); // prix_unitaire_ttc
            set_request('currency_code', $code_devise); // monnaie
            set_request('item_name', $nom_com); // nom_com
            set_request('page_style', $GLOBALS['spipal_metas']['style_page']);
            set_request('custom', _request('custom') ); // custom
            set_request('return', $GLOBALS['spipal_metas']['url_retour']);
            set_request('notify_url', $GLOBALS['spipal_metas']['notify_url']);
            // on renvoie le formulaire ainsi cree...
            $retours['action'] = $GLOBALS['spipal_metas']['url_paypal']; //@ https://programmer.spip.net/Autres-options-de-chargement
            $retours['redirect'] = $GLOBALS['spipal_metas']['url_paypal']; //@ http://www.mail-archive.com/spip@rezo.net/msg28067.html
            $retours['editable'] = FALSE; // pour verification+confirmation avant redirection (nouvelle adresse d'action)
            return $retours;
//            echo redirige_action_post('activer_plugins','activer', 'admin_plugin','', $corps); // https://programmer.spip.net/Fonctions-predefinies-d-actions
        }
    } else {
        $envoyer_mail = charger_fonction('envoyer_mail','inc');
        $email_to = $GLOBALS['meta']['email_webmaster'];
        $email_from = $GLOBALS['visiteur_session']['email']; //!\ on est dans le cas ou les personnes qui commandent sont inscrites sur le site et sont connectees avant de soumettre le formulaire... sinon prevoir par exemple un champ email dans le formulaire et le recuperer avec _request('email'); et penser aussi a traiter le paiement et la livraison (bref plus complexe)
        $sujet = _L("Commande d'un article du catalogue");
        $data = sql_fetsel('titre', 'spip_articles', "id_article=$id_article" );
        $nom_article = $data['titre'];
        $data = sql_fetsel('titre', 'spip_cat_variantes', "id_cat_variante=$id_variante" );
        $nom_variante = $data['titre'];
        $message = "Article: $id_article - $nom_article\nVariante: $id_variante - $nom_variante\nQuantite: $quantite\nTarifs ".$GLOBALS['meta']['url_site'].': '.$GLOBALS['meta']['url_site']."/ecrire/?exec=articles&id_article=$id_article";
        $ok = $envoyer_mail($email_to,$sujet,$message,$email_from);
        if ($ok) {
            $retours['message_ok'] = _L("Votre commande est bien prise en compte.");
            $retours['editable'] = FALSE;
        } else {
            $retours['message_erreur'] = _L("Probl&egrave;me technique rencontr&eacute; lors de l'envoie ; meric de r&eacute;essayer ult&eacute;rieurement.");
            $retours['editable'] = TRUE;
        }
    }


	return $retours;
}

?>