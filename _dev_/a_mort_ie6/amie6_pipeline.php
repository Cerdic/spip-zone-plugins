<?php

function amie6_affichage_final($texte) {
    $message = '<!--[if lte IE 6]>
<style type="text/css">
#ie6msg{border:3px solid #090; margin:8px 0; background:#cfc; color:#000;}
#ie6msg h4{margin:8px; padding:0;}
#ie6msg p{margin:8px; padding:0;}
#ie6msg p a.getie7{font-weight:bold; color:#006;}
#ie6msg p a.ie6expl{font-weight:normal; color:#006;}
</style>
<div id="ie6msg">
<h4>'._T('amie6:navigateur_obsolete').'</h4>
<p>'._T('amie6:mise_a_jour_navigateur').'</p>
<p>'._T('amie6:autre_navigateurs').'</p>
</div>
<![endif]-->';

    return preg_replace(
        '/(<body(.*?)>)/i',
        '$1'.$message,
        $texte
    );    
}

?>
