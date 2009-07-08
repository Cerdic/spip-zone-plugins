<?php
// fonction pour tester si un user SPIP est user de Gallery2
    function trouveid($userName) {
        global $gallery;

        $query = 'SELECT [GalleryUser::id]
                  FROM [GalleryUser]
                  WHERE [GalleryUser::userName] = ?
        ';

        /* Check to see if we have a collision */
        list($ret, $results) =
            $gallery->search($query, array($userName), array('limit' => array('count' => 1)));  

        $result = $results->nextResult();
        if ($result[0] > 0) 
            $userspip_exist_in_gallery = array( true, $result[0] );  
        else 
           $userspip_exist_in_gallery = array( false, 0 );  
       
        return($userspip_exist_in_gallery);
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
        if (file_exists($chem_inclure.'embed.php')) 
            include_once($chem_inclure.'embed.php');
        elseif (file_exists('../'.$chem_inclure.'embed.php')) 
            include_once('../'.$chem_inclure.'embed.php');
        else 
            die(_T('gallery:fichier_embed_pas_trouve'));
//        require_once('gallery/embed.php');
            
        $lang = $GLOBALS['auteur_session']['lang'] ;
        if( $lang =='' ) $lang = 'fr';
        
      // initialisation de Gallery
        $ret = GalleryEmbed::init(array( 'activeLanguage' => $lang,
                                         'embedUri' => $cfg['chemin_spip'].'spip.php?page='.$cfg['squelette_gallery'],
                                         'g2Uri' => $cfg['chemin_gallery'],
                                         
                                         'loginRedirect' => $cfg['chemin_spip'],  
                                         'activeUserId' => $GLOBALS['auteur_session']['id_auteur'] ));
         if ($ret) print 'GalleryEmbed::init failed, here is the error message: ' . $ret->getAsHtml();
    }
    

// le nécessaire pour faire tourner gallery en mode "embed" dans son squelette SPIP
    function gallery2(){    
        gallery_init();                                         
        $ret = GalleryEmbed::checkActiveUser( $GLOBALS['auteur_session']['id_auteur']);
    
        if($ret) {
             $extUserId = $GLOBALS['auteur_session']['id_auteur'];
//             $args = array('username' => $GLOBALS['auteur_session']['nom']);  
             $args = array('username' => $GLOBALS['auteur_session']['login']);
             $spipgallery = trouveid( $args['username']) ;
             if(  $spipgallery[0] ) {
                $ret = GalleryEmbed::addExternalIdMapEntry($extUserId, $spipgallery[1], 'GalleryUser') ;
             } 
             else {
                $ret = GalleryEmbed::createUser($extUserId, $args) ;
             }
/* A quoi pouvait bien servir cette redondance du code???
             $ret = GalleryEmbed::init(array( 'activeLanguage' => $lang,
                                              'embedUri' => 'spip.php?page=gallerie',
                                              'g2Uri' => '/gallery/',  
                                              'loginRedirect' =>'/',  
                                              'activeUserId' => $GLOBALS['auteur_session']['id_auteur'] ));
             $ret = GalleryEmbed::checkActiveUser( $GLOBALS['auteur_session']['id_auteur']);
*/
         }
         /* Now you *could* do something with $g2moddata['themeData'] */
        //$g2data = GalleryEmbed::handleRequest();
        
      // balancer le résultat de GalleryEmbed::handleRequest(); dans une GLOBALS pour pouvoir interroger par des filtres plus tard
        $GLOBALS['g2data'] = GalleryEmbed::handleRequest();
    }

    
// fonction d'interrogation des résultats de GalleryEmbed::handleRequest(); passés en $GLOBALS
    function g2data($rien, $partie) {
        if (isset($GLOBALS['g2data'][$partie])) return $GLOBALS['g2data'][$partie];
    }

    
// fonction de récupération du code HTML d'affichage d'une ou plusieurs photo de Gallery
// necessite le module imageblock de Gallery 2: http://codex.gallery2.org/Gallery2:Modules:imageblock
    function g2photo($item_id='', $nb_dernier='', $taille_perso='', $lien_perso='', $align='') {
//         echo 'item= '.$item_id.' nb= '.$nb_dernier.' taille= '.$taille_perso.' lien= '.$lien_perso.' align= '.$align.'<br>';
         
       // initialiser Gallery
         gallery_init();
         
       // récupérer les paramétrages dans les metas de CFG
         $cfg = @unserialize($GLOBALS['meta']['g2']);
         
       // gérer les éléments à afficher = param show
         $show = $sep = '';
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
             list($ret,$html,$head1) = GalleryEmbed::getImageBlock(array(
                  'blocks' => 'specificItem',
                  'show' => $show,
                  'link' => $lien,
                  'itemId' => intval($item_id),
                  'maxSize' => $taille));
            if ($ret) {
                return "<blink><span style='color: red;'>"._T('gallery:erreur_insertion')." ".$item_id." ".$ret."</span></blink>";
            }
        }
      // si pas référence d'item mais un nbe de dernières photos, on envoie les X dernières
        elseif ($nb_dernier != '' AND intval($nb_dernier)!= 0) {
            $ch_last = $sep = '';
            for ($i = 0; $i < intval($nb_dernier); $i++) {
                $ch_last .= $sep.'recentImage';
                $sep = '|';
            }
             list($ret,$html,$head1) = GalleryEmbed::getImageBlock(array(
                  'blocks' => $ch_last,
                  'show' => $show,
                  'link' => $lien,
                  'maxSize' => $taille));
            if ($ret) {
                return "<blink><span style='color: red;'>"._T('gallery:erreur_insertion')." ".$ret."</span></blink>";
            }
        }
      // si aucun paramètres on envoie une photo au hazard
        else {
             list($ret,$html,$head1) = GalleryEmbed::getImageBlock(array(
                  'blocks' => 'randomImage',
                  'show' => $show,
                  'link' => $lien,
                  'maxSize' => $taille));
            if ($ret) {
                return "<blink><span style='color: red;'>"._T('gallery:erreur_insertion')." ".$ret."</span></blink>";
            }
        }
echo '<br>brut= <br>'.$html.'<br>';    
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
        $remp = array($debut, $fin);
        $html = preg_replace($a_remplacer, $remp, $html);
        // ajouter <dt> autour des <a><img> et type="image/jpeg" (pour modalbox) dans le <a> si $lien_perso="spip"
        if ($lien == 'none') 
            $html = preg_replace('/<img.*?src.*?>/is', '<dt>$0</dt>', $html);
        elseif ($lien_perso == 'img') {
          // lien sur le squel g2_img_brute.html qui affiche uniquement <img src="src..." alt="...">
            preg_match('/<a.*?href ?= ?[\'" ].*?g2_itemId=(\d*)[\'" ]/is',$html, $match);
            $src_img = 'spip.php?page=g2_img_brute&item='.$match[1];
            $html = preg_replace('/(<a.*?href ?=[\'" ])(.*?)([\'" ].*?)(>.*?<\/a>)/is', '<dt>$1'.$src_img.'$3 type="image/jpeg" $4</dt>', $html);
        }
        else  
            $html = preg_replace('/<a.*?href.*?>.*?<\/a>/is', '<dt>$0</dt>', $html);
        
        
        
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
         
         list($ret,$html,$head1) = GalleryEmbed::getImageBlock(array(
              'blocks' => 'specificItem',
              'show' => 'none',
              'link' => 'none',
              'maxSize' => $taille,
              'itemId' => intval($item_id)
              ));
        if ($ret) {
            return "<blink><span style='color: red;'>"._T('gallery:erreur_insertion')." ".$item_id." ".$ret."</span></blink>";
        }
        
      // triturer le html de retour pour récup ce qui est voulu
        // recup les atributs de la balise <img>
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
            
        }
        
        return trim($html);
    }
    
?>