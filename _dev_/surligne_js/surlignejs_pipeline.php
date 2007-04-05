<?php

function Surlignejs_insert_head($flux) {
  
  $flux .= "<script type='text/javascript' src='".find_in_path("javascript/SEhighlight.js")."'></script>\n";
  $flux .= "<script type='text/javascript'>
  jQuery(function(){jQuery(document).SEhighlight({style_name:'spip_surligne',exact:false,style_name_suffix:false})});
  </script>
  ";

  return $flux;
}

?>
