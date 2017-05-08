<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

define('_DIR_BTEQ_IMG', _DIR_PLUGIN_TYPOEQUATION.'/img_pack/icones_barre/');


// https://code.spip.net/@afficher_barre
//function TypoEquation_bt_toolbox($flux)
/*
function TypoEnluminee_bt_toolbox($params) {
   $params['flux'] .= afficher_boutonsavances($params['champ'], $params['help'], $params['num']);
   return $params;
}

    <pipeline>
        <nom>header_prive</nom>
       <inclure>barre_equation.php</inclure>
    </pipeline>
function TypoEquation_header_prive($texte) {
   $texte.= "<script type=\"text/javascript\"  src=\""._DIR_PLUGIN_TYPOEQUATION.'/javascript/'."eqn.js\"></script>\n";
   return $texte;
}
*/


/*attacca il bottone che apre la barra*/
function TypoEquation_bt_gadgets($params_vierge)
{
   
      $champ = $params_vierge['champ'];
      $num_barre = $params_vierge['num'];
      $champhelp = $params_vierge['help'];
      $forum = $params['forum'];

    //
    $retG = bouton_barre_racc1("swap_couche('".$GLOBALS['numero_block']['tableau_equations']."','');", _DIR_BTEQ_IMG.'equation.png', _T('bareqn:barre_equations'), $champhelp);
       $params_vierge['flux'] .=$retG ;
       
   return $params_vierge;
     
}
/*attacca la barra*/
function TypoEquation_bt_toolbox($params_vierge)
{

         $champ = $params_vierge['champ'];
         $num_barre = $params_vierge['num'];
         $champhelp = $params_vierge['help'];
         $forum = $params['forum'];

     // 
     $toolbox = http_script('',  'eqn.js');
     $toolbox .= afficher_equations($champ, $spip_lang, $champhelp, $num_barre);
     
       $params_vierge['flux'] .=$toolbox ;
       
   return $params_vierge;

}

##########################################
// pour les FORMULE MATEMATICHE
// function afficher_caracteres
function afficher_equations($champ, $spip_lang, $champhelp, $num_barre) {
   /*
   $latex_command =(
      '\cdot'
      ,'\pm',
      ,'\rightarrow'
      ,'\sqrt{}'
      ,'\rightarrow'
      ,'\leftarrow'
      ,'\right)'
      ,'\left('
      ,'\right]'
      ,'\left]'
      ,'\right\{'
      ,'\left\}'
      ,'\frac{}{}'
      ,'\Rightarrow'
      ,'\Leftrightarrow'
      ,'\rightarrow'
      ,'\rightarrow'
      )

   for each
   */
   $reta .= bouton_barre_racc1("barre_raccourci_etendu('$','$','$$',$champ, $num_barre)", _DIR_BTEQ_IMG."dollar.png", _T('bareqn:barre_dollar'), $champhelp);

   $reta .= bouton_barre_racc1("barre_raccourci_etendu('<math>','</math>','<math>\\n\\n</math>',$champ, $num_barre)", _DIR_BTEQ_IMG."math.png", _T('bareqn:barre_math'), $champhelp);
   
   //$reta .= bouton_barre_racc1("barre_raccourci_etendu('<math>','</math>',$champ, $num_barre)", _DIR_BTEQ_IMG."math.png", _T('bareqn:barre_math'), $champhelp);

   // System
   $sys_begin = '$\\n';  
   $sys_begin .= '\\\\left';
   $sys_begin .= '\\\\{';
   $sys_begin .= '\\n';
   $sys_begin .= '\\\\begin{array}{}';
   $sys_begin .= '\\n';
   $sys_end = ' ax+by=c\\\\\\\\';
   $sys_end .= '\\n';
   $sys_end .= 'a_{1}x+b_{1}y=c_{1}\\n';
   $sys_end .= '\\\\end{array}';
   $sys_end .= '\\n$';
   $reta .= bouton_barre_racc1("barre_raccourci_etendu('$sys_begin','$sys_end','$sys_begin$sys_end',$champ, $num_barre)", _DIR_BTEQ_IMG."system.png", _T('bareqn:barre_system'), $champhelp);

   
   $reta .= bouton_barre_racc1("barre_raccourci_etendu('$\\n\\\\begin{array}\\\\left','\\\\right\\\\end{array}\\n$','$\\n\\\\begin{array}\\\\left\\n\\n\\\\right\\\\end{array}\\n$',$champ, $num_barre)", _DIR_BTEQ_IMG."array.png", _T('bareqn:barre_array'), $champhelp);
   
   // brackets
   $reta .= bouton_barre_racc1("barre_raccourci_etendu('\\\\left(','\\\\right)','\\\\left(  \\\\right)',$champ, $num_barre)", _DIR_BTEQ_IMG."brackets.png", _T('bareqn:barre_brackets'), $champhelp);

   $reta .= bouton_barre_racc1("barre_raccourci_etendu('\\\\left[','\\\\right]','\\\\left[  \\\\right]',$champ, $num_barre)", _DIR_BTEQ_IMG."squarebrackets.png", _T('bareqn:barre_squarebrackets'), $champhelp);

   $reta .= bouton_barre_racc1("barre_raccourci_etendu('\\\\left\\\\{','\\\\right\\\\}','\\\\left\\\\{  \\\\right\\\\}',$champ, $num_barre)", _DIR_BTEQ_IMG."setbrackets.png", _T('bareqn:barre_setbrackets'), $champhelp);
   
   // symbols
   $reta .= bouton_barre_racc1("barre_inserer('\\\\cdot ',$champ)", _DIR_BTEQ_IMG.'cdot.png', _T('bareqn:barre_cdot'), $champhelp);

   $reta .= bouton_barre_racc1("barre_inserer('\\\\pm ',$champ)", _DIR_BTEQ_IMG.'plusminus.png', _T('bareqn:barre_plusminus'), $champhelp);   

   $reta .= bouton_barre_racc1("barre_raccourci_etendu('\\\\frac{','}{} ','\\\\frac{}{} ',$champ)", _DIR_BTEQ_IMG.'frac.png', _T('bareqn:barre_frac'), $champhelp);   

   $reta .= bouton_barre_racc1("barre_raccourci_etendu('\\\\sqrt{','}','\\\\sqrt{} ',$champ)", _DIR_BTEQ_IMG.'sqrt.png', _T('bareqn:barre_sqrt'), $champhelp);   

    $reta .= "<br />";
   $reta .= bouton_barre_racc1("barre_raccourci_etendu('_{','}','_{}',$champ)", _DIR_BTEQ_IMG."pedex.png", _T('bareqn:barre_pedex'), $champhelp);
   
   $reta .= bouton_barre_racc1("barre_raccourci_etendu('\\\\vec{','}','\\\\vec{}',$champ)", _DIR_BTEQ_IMG."vector.png", _T('bareqn:barre_vector'), $champhelp);
   
   $reta .= bouton_barre_racc1("barre_inserer('\\\\sum',$champ)", _DIR_BTEQ_IMG."sum.png", _T('bareqn:barre_sum'), $champhelp);
   
   $reta .= bouton_barre_racc1("barre_inserer('\\\\prod',$champ)", _DIR_BTEQ_IMG."prod.png", _T('bareqn:barre_prod'), $champhelp);
  
   $reta .= bouton_barre_racc1("barre_inserer('\\\\Rightarrow ',$champ)", _DIR_BTEQ_IMG.'rarrow.png', _T('bareqn:barre_Rightarrow'), $champhelp);   
   
   $reta .= bouton_barre_racc1("barre_inserer('\\\\Leftrightarrow ',$champ)", _DIR_BTEQ_IMG.'lrarrow.png', _T('bareqn:barre_Leftrightarrow'), $champhelp);   
   
   $reta .= bouton_barre_racc1("barre_inserer('\\\\rightarrow ',$champ)", _DIR_BTEQ_IMG.'rightarrow.png', _T('bareqn:barre_rightarrow'), $champhelp);   
   
   $reta .= bouton_barre_racc1("barre_inserer('\\\\leftarrow ',$champ)", _DIR_BTEQ_IMG.'leftarrow.png', _T('bareqn:barre_leftarrow'), $champhelp);   
   

$tableau_formulaire = '
<table class="spip_barre" style="width: 100%; padding: 1px!important; border-top: 0px;" summary="">
  <tr class="spip_barre">
    <td>'._T('bareqn:barre_equations').
    "<br /><a class='aide'
    href='"._DIR_PLUGIN_TYPOEQUATION."doc/latex_symbols.html'".'
    onclick="javascript:window.open(this.href,\'spip_aide\', \'scrollbars=yes, resizable=yes, width=740, height=580\'); return false;"><img src=\'../prive/images/aide.gif\' alt="'._T('info_image_aide').'"  title="'._T('bareqn:latex_reference').'" class=\'aide\' /></a></td><td>'.$reta.'
    </td>
  </tr> 
</table>
';

  return produceWharf('tableau_equations','',$tableau_formulaire);  
}
// https://code.spip.net/@bouton_barre_racc1
function bouton_barre_racc1($action, $img, $help, $champhelp) {

   $a = attribut_html($help);
   if (function_exists('test_espace_prive'))
   {
      return "<a\nhref=\"javascript:"
      .$action
      ."\" tabindex='1000'\ntitle=\""
      . $a
      ."\"" 
      . (test_espace_prive() ? '' :  ("\nonmouseover=\"helpline('"
        .addslashes(str_replace('&#39;',"'",$a))
        ."',$champhelp)\"\nonmouseout=\"helpline('"
                .attribut_html(_T('barre_aide')))
        ."', $champhelp)\"")
         ."><img\nsrc='"
        // cas horrible de action/poster_forum_prive. cf commentaire dedans
        . ((test_espace_prive() AND !_DIR_RACINE AND _AJAX AND _request('exec')=='poster_forum_prive') ? '../' : '')
         .$img
         //    ."' style=\"height: 16px; width: 16px; background-position: center 
         ."' style=\"height: 16px; background-position: center 
         center;\" alt=\"$a\" /></a>";
   }
   else
   {
      return "<a\nhref=\"javascript:"
      .$action
      ."\" tabindex='1000'\ntitle=\""
      . $a
      ."\"" 
        .(!_DIR_RESTREINT ? '' :  ("\nonmouseover=\"helpline('"
        .addslashes(str_replace('&#39;',"'",$a))
        ."',$champhelp)\"\nonmouseout=\"helpline('"
                .attribut_html(_T('barre_aide')))
        ."', $champhelp)\"")
      ."><img src='"
      .$img
      ."' style=\"height: 16px; background-position: center center;\" alt=\"$a\" /></a>";
   
   }
}


?>