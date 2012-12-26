<?php
// fonction pour récuperer le block HTML produit par gallery
	function call_imageblock($params) {
	// RQ:  getBlock n'existe qu'a partir de la version 1.1.9 du plugin "image block"
	// la version 1.1.9 du plugin "image block" n'existe que pour une version de gallery2 >= 2.3
		$class_methods = get_class_methods('GalleryEmbed');
		if (array_search('getBlock', $class_methods)) { // test sur l'existence de la fn getBlock
			list($status,$html,$head1)=GalleryEmbed::getBlock('imageblock','ImageBlock',$params);			
			if ($status) { // affichage du message d'erreur si  getBlock a echoue
                return "<blink><span style='color: red;'>"._T('gallery:erreur_insertion')." ".$ret."</span></blink>";
				exit;
			  }
		} else {
				return "<blink><span style='color: red;'>"._T('gallery:erreur_getblock')."</span></blink>";
				exit;
		}
		return $html;
	}

  
// fonction pour lister les ss-reps de /squelettes du plugin et retourner un array
    function liste_ssreps() {
        $Treps = array();
        if ($pointeur = opendir(_DIR_PLUGIN_G2.'squelettes')) {  
            while (false !== ($fich = readdir($pointeur))) {
                if ($fich != "." AND $fich != ".." AND $fich != ".svn") $Treps[] = $fich;
            }
            closedir($pointeur);
        }
        asort($Treps);
        return $Treps;
    }

  
// fonction d'initialisation de gallery
    function  gallery_init() {
      // récupérer les paramétrages dans les metas de CFG et les mettre aux bons formats de chaînes
        $cfg = @unserialize($GLOBALS['meta']['g2']);
        $cfg['squelette_gallery'] = str_replace('.html', '', $cfg['squelette_gallery']);
      // s'assurer que les chemins ont les /
        if ($cfg['chemin_spip'] == '') $cfg['chemin_spip'] = '/';
        if ($cfg['chemin_spip'] != '/') $cfg['chemin_spip'] = '/'.trim(rtrim($cfg['chemin_spip'], '/'), '/').'/';
        $cfg['chemin_gallery'] = '/'.trim(rtrim($cfg['chemin_gallery'], '/'), '/').'/';

      // mauvaise bidouille pour gérer l'include du fichier embed.php de Gallery 
      // selon qu'on est dans le public ou le privé...
        $chem_inclure = trim($cfg['chemin_gallery'],'/').'/';
        $chem_inclure = _DIR_RACINE.$chem_inclure;
        if (file_exists($chem_inclure.'embed.php')) 
            include_once($chem_inclure.'embed.php');
        elseif (file_exists('../'.$chem_inclure.'embed.php')) 
            include_once('../'.$chem_inclure.'embed.php');
        else 
            die(_T('gallery:fichier_embed_pas_trouve'));
		 
        $lang = $GLOBALS['auteur_session']['lang'] ;
        if( $lang =='' ) $lang = 'fr';
        
      // bidouillage pour gérer les url propres si intégration de gallerie.html 
      // en tant que composition pour une rubrique
        if (isset($_SERVER["REDIRECT_url_propre"])) 
            $fic_embed = $_SERVER["REDIRECT_url_propre"];
        else $fic_embed = 'spip.php?page='.$cfg['squelette_gallery'];
        $fic_embed = trim($fic_embed , '/' );

      // initialisation de Gallery
        $ret = GalleryEmbed::init(array( 'activeLanguage' => $lang,
                                         'embedUri' => $cfg['chemin_spip'].$fic_embed,
                                         'g2Uri' => $cfg['chemin_gallery'],
                                         'fullInit' => true,
                                         'loginRedirect' => $cfg['chemin_spip'],  
                                         'activeUserId' => $GLOBALS['auteur_session']['id_auteur'] ));
        return $ret;
    }
    
    
// le nécessaire pour faire tourner gallery en mode "embed" dans son squelette SPIP
    function gallery2(){    
        $ret=gallery_init();
    	if ($ret) {
    		 /* Error! */
    		 /* Did we get an error because the user doesn't exist in g2 yet? */	 
    		$ret2 =GalleryEmbed::isExternalIdMapped($GLOBALS['auteur_session']['id_auteur'],'GalleryUser');   
    		 if ($ret2 && $ret2->getErrorCode() & ERROR_MISSING_OBJECT) {
    			 /* The user does not exist in G2 yet. Create in now on-the-fly */		 
    				$extUserId = $GLOBALS['auteur_session']['id_auteur'];
    				$args = array('username' => $GLOBALS['auteur_session']['login']);
    				$ret = GalleryEmbed::createUser($extUserId, $args) ;															
    			 if ($ret) {
    			 /* An error during user creation. Not good, print an error or do whatever is appropriate * in your emApp when an error occurs */
    				 print "An error occurred during the on-the-fly user creation <br>";
    				 print $ret->getAsHtml();
    				 exit;
    			 }
    		 } else {
    			 /* The error we got wasn't due to a missing user, it was a real error */
    			 if ($ret2) {
    				 print "An error occurred while checking if a user already exists<br>";
    				 print $ret2->getAsHtml();
    			 }
    			 print "An error occurred while trying to initialize G2<br>";
    			 print $ret->getAsHtml();
    			 exit;
    		 }
	 }
	 
      // balancer le résultat de GalleryEmbed::handleRequest(); dans une GLOBALS pour pouvoir interroger par des filtres plus tard
        $GLOBALS['g2data'] = GalleryEmbed::handleRequest();
    }

    
// fonction d'interrogation des résultats de GalleryEmbed::handleRequest(); passés en $GLOBALS
    function g2data($rien, $partie) {
        if (isset($GLOBALS['g2data'][$partie])) return $GLOBALS['g2data'][$partie];
    }

    
// fonction de récupération du code HTML d'affichage d'une ou plusieurs photo de Gallery
// necessite le module imageblock de Gallery 2: http://codex.gallery2.org/Gallery2:Modules:imageblock
    function g2photo($item_id='', $nb_dernier='', $taille_perso='', $lien_perso='', $align='', $legende='', $sep_item='', $type='') {
//         echo 'item= '.$item_id.' nb= '.$nb_dernier.' taille= '.$taille_perso.' lien= '.$lien_perso.' align= '.$align.' legende= '.$legende.' type= '.$type.'<br>';
       // initialiser Gallery
         gallery_init();
         
       // récupérer les paramétrages dans les metas de CFG
         $cfg = @unserialize($GLOBALS['meta']['g2']);
         
       // gérer les éléments de légende à afficher = param show
         $show = $sep = '';
         if ($legende == 'non') $show = 'none';
         else {
             $Tshow = array('g2photo_elem_titre', 
                            'g2photo_elem_date', 
                            'g2photo_elem_nbvues', 
                            'g2photo_elem_proprio');
             foreach($Tshow as $p) {
                 if (isset($cfg[$p]) AND $cfg[$p]!= '') {
                     $show .= $sep.$cfg[$p];
                     $sep = '|';
                 }
             }
             if ($show == '') $show = 'none';
         }
       
       // gérer la taille d'affichage
         if ($taille_perso != '' AND intval($taille_perso)!= 0)
             $taille = intval($taille_perso);
         elseif (isset($cfg['g2photo_taille']) AND intval($cfg['g2photo_taille'])!= 0) 
             $taille = intval($cfg['g2photo_taille']);
         else $taille = 200;
         
       // gérer un éventuel lien personnalisé (par défaut lien sur l'image dans ?page=gallerie)
         $lien = trim(rtrim($lien_perso));
         if ($lien == 'non') $lien = 'none'; // pas de lien 
         if ($lien_perso == 'img') $lien = '';     // lien vers l'image comme dans <docXX> de SPIP (=> bidouillage + loin) 
         
         $html = '';
       // si il existe une référence d'item on l'utilise pour envoyer une image unique
         if ($item_id != '' AND intval($item_id)!= 0){ 
            $params= array(
                          'blocks' => 'specificItem',
                          'show' => $show,
                          'link' => $lien,
                          'itemId' => intval($item_id),
                          'maxSize' => $taille);			
            $html=call_imageblock($params);
        }

      // si pas référence d'item mais un nbe de dernières photos, on envoie les X dernières
        elseif ($nb_dernier != '' AND intval($nb_dernier)!= 0) {
            $ch_last = $sep = '';
            $ch_type =  ($type == 'album' ? 'recentAlbum' :  'recentImage');
            for ($i = 0; $i < intval($nb_dernier); $i++) {
                $ch_last .= $sep.$ch_type;
                $sep = '|';
            }
            $params=  array(
                          'blocks' => $ch_last,
                          'show' => $show,
                          'link' => $lien,
                          'maxSize' => $taille);			
            $html=call_imageblock($params);
        }

      // si aucun paramètre on envoie une photo au hazard
        else {
            $ch_type =  ($type == 'album' ? 'randomAlbum' :  'randomImage');
            $params= array(
                          'blocks' => $ch_type,
                          'show' => $show,
                          'link' => $lien,
                          'maxSize' => $taille);			
            $html=call_imageblock($params);
        }

      // retourner des blocs formatés comme les <docXX> de SPIP  
        // supprimer le <div class="block-imageblock-ImageBlock"> englobant les résultats
        $html = preg_replace('/^[\s ]*<div.*?class.*?block-imageblock-ImageBlock.*?>(.*)<\/div>[\s ]*$/is', '$1', $html);
        
        // transformer en <dl> (avec float éventuel) les <div class="one-image"> 
        $a_remplacer = array('/<div.*?class.*?one-image.*?>/is', '/<\/div>/is');
        $debut = '<dl class="spip_documents';
        if (in_array($align, array('left','right'))) 
            $debut .= ' spip_documents_'.$align.'" style="float: '.$align.';';
        $debut .= '">';
        $fin = '</dl>';
        // ajouter $sep_item si X derniers
        if ($nb_dernier != '' AND intval($nb_dernier)!= 0) $fin .= $sep_item;
        $remp = array($debut, $fin);
        $html = preg_replace($a_remplacer, $remp, $html);
        
        // ajouter <dt> autour des <a><img> et type="image/jpeg" dans le <a> si $lien_perso="img" (pour modalbox)
        if ($lien == 'none') 
            $html = preg_replace('/<img.*?src.*?>/is', '<dt>$0</dt>', $html);
        elseif ($lien_perso == 'img') {
          // si {lien=img} lien sur le squel g2_img_brute.html qui affiche uniquement <img src="src..." alt="...">
            preg_match('/<a.*?href ?= ?[\'" ].*?g2_itemId=(\d*)[\'" ]/is',$html, $match);
            $src_img = 'spip.php?page=g2_img_brute&item='.$match[1];
            $html = preg_replace('/(<a.*?href ?=[\'" ])(.*?)([\'" ].*?)(>.*?<\/a>)/is', '<dt>$1'.$src_img.'$3 type="image/jpeg" $4</dt>', $html);
        }
        else  
            $html = preg_replace('/<a.*?href.*?>.*?<\/a>/is', '<dt>$0</dt>', $html);
        
        // transformer les balise de G2 qui emballent titre et infos supplémentaires en <dt> et <dd> à la mode SPIP
        preg_match('/width ?= ?[\'" ](.*?)[\'" ]/is', $html, $match);
        $width = (isset($match[1]) ? $match[1] : '');
        $a_remp = array('/<h4.*?>(.*?)<\/h4>/is', '/<p.*?giInfo.*?>(.*?)<\/p>/is');
        $remp = array('<dt class=" spip_doc_titre" style="width: '.$width.'px;"><strong>$1</strong></dt>',
                      '<dd class=" spip_doc_descriptif" style="width: '.$width.'px;">$1</dd>');
        $html = preg_replace($a_remp, $remp, $html);
        
        return $html;
    }


// fonction de récupération d'une photo de Gallery
// si $affiche == brute retourne le <img src="" alt="">
// sinon retourne le code équivalent à un <imgXXX|YY> de SPIP
// necessite le module imageblock de Gallery 2: http://codex.gallery2.org/Gallery2:Modules:imageblock
    function g2img($item_id, $affiche='', $taille_perso='', $align='') {
//         echo 'item= '.$item_id.' taille= '.$taille_perso.' align= '.$align.'<br>';
         
         if (!$item_id OR intval($item_id)== 0) return; 
         gallery_init();
         
       // récupérer les paramétrages dans les metas de CFG
         $cfg = @unserialize($GLOBALS['meta']['g2']);
         
       // gérer la taille d'affichage
         if ($taille_perso != '' AND intval($taille_perso)!= 0)
             $taille = intval($taille_perso);
         elseif (isset($cfg['gimg_taille']) AND intval($cfg['gimg_taille'])!= 0) 
             $taille = intval($cfg['gimg_taille']);
         else $taille = 640;
         
        $params=  array(
                  'blocks' => 'specificItem',
                  'show' => 'none',
                  'link' => 'none',
                  'maxSize' => $taille,
                  'itemId' => intval($item_id));			
        $html=call_imageblock($params);   
        
      	// triturer le html de retour pour retourner la forme voulue
        // recup les atributs et construire la balise <img>
        $Tattr = array('src', 'alt', 'height', 'width');
        foreach (array('src', 'alt', 'height', 'width') as $p) {
            preg_match('/'.$p.' ?= ?[\'" ](.*?)[\'" ]/is', $html, $match);
            $$p = (isset($match[1]) ? $match[1] : '');
        }
        $float = (in_array($align, array('left','right')) ? ' float: '.$align.';' : '');
        
        // balise img seule
        if ($affiche == 'brut' OR $affiche == 'brute') 
            $html = '<img src="'.$src.'" alt="'.$alt.'" title="'.$alt.'" style="'.$float.'height:'.$height.'px; width:'.$width.'px;" height="'.$height.'" width="'.$width.'"/>';
        // equivalent <imgXXX> SPIP
        else {
            $doc_align = (in_array($align, array('left','right')) ? ' spip_documents_'.$align.';' : '');
            $html = '<span class="spip_documents '.$doc_align.'" style="'.$float.'width:'.$width.'px;">';
            $html .= '<img src="'.$src.'" alt="'.$alt.'" title="'.$alt.'" style="height:'.$height.'px; width:'.$width.'px;" height="'.$height.'" width="'.$width.'"/>';
            $html .= '</span>';
        }
        
        return trim($html);
    }
    
?>