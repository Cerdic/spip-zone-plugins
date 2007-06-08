<?php

function spip_demo_header_prive($flux) {
  $file_demo = '';
  $name_demo = '';
  switch(_request('exec')) {
    case '':
      $file_demo = 'demo.xml';
      $name_demo = 'test';
      break;
  }
  if($file_demo && $name_demo)  
  $flux .= "<link type='text/css' rel='stylesheet' href='"._DIR_PLUGIN_SPIP_DEMO."javascript/jqModal.css' />
  <script src='"._DIR_PLUGIN_SPIP_DEMO."javascript/jqModal.js'></script>
  <script src='"._DIR_PLUGIN_SPIP_DEMO."javascript/jDemo.js'></script>
  <script type='text/javascript'>
    jQuery(function(){jQuery.startDemo('"._DIR_PLUGIN_SPIP_DEMO."$file_demo','$name_demo');});
  </script>
  "; 
  return $flux;
}

?>
