<?php

    spip_log('portezip chargé','portezip');
    global $zip;
    global $filename;

    function balise_ZIP_INIT_dist($p) {                                    
        $_filename = interprete_argument_balise(1,$p);
        $_filename = $_filename ? $_filename : "'test.zip'";
        
        $p->code = "zip_initialiser($_filename)";
        
        return $p;
    }
                

    function balise_ZIP_EXPORT_dist($p) {
        $p->code = "zip_exporter()";    
        return $p;
    }


    function zip_ajout($fichier) {
        global $zip;
    
        $zip->addFile($fichier);
        
        spip_log('ajout fichier :'.$fichier,'portezip');
        return '';
    }


    function zip_initialiser($_filename) {
        global $zip;        
        global $filename;
        $filename = $_filename;
            
        @unlink($filename);
        spip_log('zip a créer :'.$filename,'portezip');

        $zip = new ZipArchive();
        if ($zip->open($filename, ZIPARCHIVE::CREATE)!==TRUE) {
            exit("Impossible d'ouvrir <$filename>\n");
        }
        
        spip_log('zip créé :'.$filename,'portezip');
        
    }

    function zip_exporter() {
        global $zip;
        global $filename;

        if ($zip->numFiles > 0) {
            $return = $filename;
        } else {
            $return = '';
        }

        spip_log('fermeture zip :'.$filename,'portezip');    
        $zip->close();       

        return  $return;
    }

?>
