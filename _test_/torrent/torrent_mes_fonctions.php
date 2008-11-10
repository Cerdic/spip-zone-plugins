<?php

    //sudo pear install File_Bittorrent2

    /*
     * Genere, sauvegarde et affecte un torrent
     * 
     * @param $id_document 
     *      Identifiant du document source
     * @return
     *      Résultat de l'écriture du torrent
     *
     */
    function torrent_make($id_document) {
        
        /* Données à écrire pour le torrent */
        $document = sql_fetsel(
            array('fichier', 'descriptif'), 
            'spip_documents', 
            'id_document='.sql_quote($id_document)
        );        

        /* Calcul du torrent */
        $data['source_uri'] =getcwd().'/'.get_spip_doc($document['fichier']);    
        $data['descriptif'] = $document['descriptif'];
        
        $metainfo = torrent_set_metainfo($data);
        
        /* Sauvegarde du torrent */
        return torrent_write($id_document,$metainfo);
    }
    
    /*
     * Calcul le contenu du torrent
     *
     * Genere le binaire d'un torrent à partir de valeur par défaut ou bien selon un CFG si celui ci est configuré
     * @param data
     *      data['source_uri'] chemin absolu du document source
     *      data['descriptif'] contenu du commentaire
     * @return string
     *      Chaine contenant l'intégralité des données binaire du torrent
     */
    function torrent_set_metainfo($data) {

        require_once 'File/Bittorrent2/MakeTorrent.php';
     
        /* Paramétre par défaut */
        $PieceLength = 256;
        $Announce = htmlspecialchars($GLOBALS['meta']['adresse_site'])."?page=tracker";
     
        /* Si CFG est par là autant en profiter et utiliser les valeurs définies par le webmestre */
        $lire_config = charger_fonction('lire_config','inc',true);
        
        if ($lire_config) {
            $PieceLength = $lire_config('torrent/PieceLength') ? $lire_config('torrent/PieceLength') : $PieceLength;
            $Announce = $lire_config('torrent/Announce') ? $lire_config('torrent/Announce') : $Announce;
        }
        
        /* Calcul du contenu du torrent */
        
        $MakeTorrent = new File_Bittorrent2_MakeTorrent($data['source_uri']);

        // Set the announce URL
        $MakeTorrent->setAnnounce($Announce);
        // Set the comment
        $MakeTorrent->setComment($data['descriptif']);
        // Set the piece length (in KB)
        $MakeTorrent->setPieceLength($PieceLength);
        // Build the torrent
        $metainfo = $MakeTorrent->buildTorrent();

        return $metainfo;
    }


    /*
     * Ecrit le fichier et l'affecte au document possédant le document source 
     *
     * Ecrit le torrent dans IMG/torrent avec pour nom de fichier "id_document.alea.torrent"
     *
     * @param $id_document 
     *      Document à l'origine du torrent
     * @param $metainfo
     *      Contenu du torrent à écrire
     * @return boulean
     *      Si création alors true sinon false
     */
    function torrent_write($id_document,$metainfo) {
    
        $objet = sql_fetsel(
            array('id_objet', 'objet'), 
            'spip_documents_liens, spip_articles', 
            'id_document='.sql_quote($id_document)
        );
        /* Calcul un aléa pour éviter des écriture concurrentes */   
        $alea = date('is');
        
        $torrent = getcwd()."/tmp/upload/".$id_document.".".$alea.".torrent";
            
        /* Ecriture du torrent dans upload/ */
        $pointeur = fopen($torrent,"w+");
        fwrite($pointeur,$metainfo);
        fclose($pointeur);

        /* Affecter le torrent au meme objet que le document source */
        $ajouter_documents = charger_fonction('ajouter_documents','inc');

        $actifs = array();
        $id_document = $ajouter_documents($torrent,$id_document.".torrent",$objet['objet'],$objet['id_objet'],'document',0, &$actifs);       
        return $torrent."--".$id_document;     
    }
