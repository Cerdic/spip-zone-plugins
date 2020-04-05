<?php
function odt2spip_traiter_mathml($chemin_fichier) {
  // recuperer le contenu du fichier
    if (!$mathml = file_get_contents($chemin_fichier)) return(_T('odtspip:err_transformation_xslt_mathml'));
  
  // virer le DOCTYPE qui plante le parseur vu que la dtd n'est pas disponible
    $mathml = preg_replace('/<!DOCTYPE.*?>/i', '', $mathml);

    
  // appliquer la transformation XSLT sur le fichier content.xml
    // déterminer les fonctions xslt à utiliser (php 4 ou php 5)
    if (!class_exists('XSLTProcessor')) {
      // on est en php4 : utiliser l'extension et les fonction xslt de Sablotron
      // Crée le processeur XSLT
        $xh = xslt_create();
        
      // nom du fichier xslt a utiliser pour les maths, le chemin sera donne par xslt_set_base()
        $xslt_texte = 'mmltex.xsl'; 
      // si on est sur un serveur Windows utiliser xslt_set_base avec le préfixe file://
//        if (strpos($_SERVER['SERVER_SOFTWARE'], 'Win') !== false) 
            xslt_set_base($xh, 'file://' . getcwd () . '/'._DIR_PLUGIN_ODT2SPIP.'inc/xsltml/');
      
      // lancer le parseur
        $arguments = array('/_xml' => $mathml);
        $latex_sortie = xslt_process($xh, 'arg:/_xml', $xslt_texte, NULL, $arguments);
        if (!$latex_sortie) return(_T('odtspip:err_transformation_xslt_mathml'));

      // Détruit le processeur XSLT
        xslt_free($xh);
    }
    else {
      // chemin du fichier xslt a utiliser pour les maths
        $xslt_texte = _DIR_PLUGIN_ODT2SPIP.'inc/xsltml/mmltex.xsl'; 
      // on est php5: utiliser les fonctions de la classe XSLTProcessor
        $proc = new XSLTProcessor();
        
        $xml = new DOMDocument();
        $xml->loadXML($mathml);
        $xsl = new DOMDocument();
        $xsl->load($xslt_texte);
        $proc->importStylesheet($xsl); // attachement des règles xsl
        
      // lancer le parseur
        if (!$latex_sortie = $proc->transformToXml($xml)) return(_T('odtspip:err_transformation_xslt_mathml'));
    }
  
    return $latex_sortie;
}

?>