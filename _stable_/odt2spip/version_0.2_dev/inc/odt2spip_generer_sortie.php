<?php
function inc_odt2spip_generer_sortie($id_auteur,$rep_dezip){
	// variables en dur pour xml en entree et xslt utilisee
	// $xml_entre = $rep_dezip.'content.xml';  // chemin du fichier xml a lire  !!! ce chemin absolu ne fonctionne pas pour PHP4 !!!
	$xml_entre = _DIR_TMP.'odt2spip/'.$id_auteur.'/content.xml';  // chemin du fichier xml a lire
	$xslt_texte = _DIR_PLUGIN_ODT2SPIP.'inc/odt2spip.xsl'; //'inc/odt2spip_texte.xsl';  // chemin de la xslt a utiliser pour le texte
    
	// fichier de sortie
	$fichier_sortie = $rep_dezip.'snippet_odt2spip.xml';

	// determiner si le plugin enluminure_typo ou intertitres_enrichis est present & actif
	include_spip('inc/plugin');
	$Tplugins = liste_plugin_actifs();
	$intertitres_riches = ((array_key_exists('TYPOENLUMINEE', $Tplugins) OR array_key_exists('INTERTITRESTDM', $Tplugins)) ? 'oui' : 'non'); 
    
	// appliquer la transformation XSLT sur le fichier content.xml
	// daterminer si on est en php 4 ou php 5 pour choisir les fonctions xslt a utiliser
	// on est php5: utiliser les fonctions de la classe XSLTProcessor
	// verifier que l'extension xslt est active
	if (!class_exists('XSLTProcessor')) die(_T('odtspip:err_extension_xslt'));
        $proc = new XSLTProcessor();

		// passage d'un parametre a la xslt
		$proc->setParameter(null, 'IntertitresRiches', $intertitres_riches);   
        
        $xml = new DOMDocument();
        $xml->load($xml_entre);
        $xsl = new DOMDocument();
        $xsl->load($xslt_texte);
        $proc->importStylesheet($xsl); // attachement des regles xsl
        
      // lancer le parseur
        if (!$xml_sortie = $proc->transformToXml($xml)) die(_T('odtspip:err_transformation_xslt'));

	// traitements complementaires du flux de sortie
    // remplacer les &gt; et &lt;
    $a_remplacer = array('&#60;','&#62;','&lt;','&gt;', '"', "<date/>");
    $remplace = array('<','>','<','>', "'", '<date>'.(date("Y-m-d H:i:s")).'</date>');
    $xml_sortie = str_replace($a_remplacer, $remplace, $xml_sortie);
    
    // virer les sauts de ligne multiples
    $xml_sortie = preg_replace('/([\r\n]{2})[ \r\n]*/m', "$1", $xml_sortie);
        
	// traiter les images: dans tous les cas il faut les integrer dans la table documents 
	// en 2.0 c'est mode image + les fonctions de snippets font la liaison => on bloque la liaison en filant un id_article vide
	$rep_pictures = $rep_dezip."Pictures/";
    
	// parametres de conversion de taille des images : cm -> px (en 96 dpi puisque c'est ce que semble utiliser Writer)
    $conversion_image = 96/2.54;
    
	preg_match_all('/<img([;a-zA-Z0-9\.]*)/', $xml_sortie, $match, PREG_PATTERN_ORDER);

	if (@count($match) > 0) {
		if(!isset($odt2spip_retailler_img))
            $odt2spip_retailler_img = charger_fonction('odt2spip_retailler_img','inc');
		if(!isset($ajouter_documents))
			$ajouter_documents = charger_fonction('ajouter_documents','inc');
        $T_images = array();
        foreach($match[1] as $ch) {
            $Tdims = explode(';;;', $ch);
            $img = $Tdims[0];
            if (file_exists($rep_pictures.$img)) {
              // retailler l'image en fct des parametres ;;;largeur;;;hauteur;;;
                $largeur = round($Tdims[1]*$conversion_image);
                $hauteur = round($Tdims[2]*$conversion_image);
                $odt2spip_retailler_img($rep_pictures.$img, $largeur, $hauteur);
                $type = 'image';
                if ($id_document = $ajouter_documents($rep_pictures.$img, $img, "article", "", $type, 0,$toto="")) { 
                    $xml_sortie = str_replace($ch, $id_document, $xml_sortie);
                    $T_images[] = $id_document;
                }
            }
        }
    }
    
	//finalement enregistrer le contenu dans /tmp/odt2spip/id_auteur/snippet_odt2spip.xml
    if (!ecrire_fichier($fichier_sortie,$xml_sortie)) die(_T('odtspip:err_enregistrement_fichier_sortie').$fichier_sortie);
    
    return array($fichier_sortie,$xml_sortie);
}
?>