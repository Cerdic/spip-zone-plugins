<?php
$spip_pipeline['affichage_final'] = str_replace("|f_surligne","|f_surlignejs",$spip_pipeline['affichage_final']);

function f_surlignejs($flux) {
  $surlignejs_engines = array(
      array(",".str_replace(array("/","."),array("\/","\."),$GLOBALS['meta']['adresse_site']).",i", ",recherche=([^&]+),i"), //SPIP
      array(",^http://(www\.)?google\.,i", ",q=([^&]+),i"),                            // Google
      array(",^http://(www\.)?search\.yahoo\.,i", ",p=([^&]+),i"),                     // Yahoo
      array(",^http://(www\.)?search\.msn\.,i", ",q=([^&]+),i"),                       // MSN
      array(",^http://(www\.)?search\.live\.,i", ",query=([^&]+),i"),                  // MSN Live
      array(",^http://(www\.)?search\.aol\.,i", ",userQuery=([^&]+),i"),               // AOL
      array(",^http://(www\.)?ask\.com,i", ",q=([^&]+),i"),                            // Ask.com
      array(",^http://(www\.)?altavista\.,i", ",q=([^&]+),i"),                         // AltaVista
      array(",^http://(www\.)?feedster\.,i", ",q=([^&]+),i"),                          // Feedster
      array(",^http://(www\.)?search\.lycos\.,i", ",q=([^&]+),i"),                     // Lycos
      array(",^http://(www\.)?alltheweb\.,i", ",q=([^&]+),i"),                         // AllTheWeb
      array(",^http://(www\.)?technorati\.com,i", ",([^\?\/]+)(?:\?.*)$,i"),           // Technorati  
  );

  if (isset($_SERVER['HTTP_REFERER'])) {
    $ref = $_SERVER['HTTP_REFERER'];
    foreach($surlignejs_engines as $engine) 
      if(preg_match($engine[0],$ref)) 
        if(preg_match($engine[1],$ref,$match)) {
          //good referrer found
          $script = "<script src='".find_in_path("javascript/SEhighlight.js")."'></script>
          <script type='text/javascript'>
            jQuery(function(){
              jQuery(document).SEhighlight({
                style_name:'spip_surligne',
                exact:'whole',
                style_name_suffix:false,
                engines:[/^".str_replace(array("/","."),array("\/","\."),$GLOBALS['meta']['adresse_site'])."/i,/recherche=([^&]+)/i],
                startHighlightComment:'debut_surligneconditionnel',
                stopHighlightComment:'finde_surligneconditionnel'
              })
            });
          </script>";
          $flux = preg_replace(",</head>,",$script."\n</head>",$flux);
          break;
        }
  }
  
  return $flux;
}

?>
