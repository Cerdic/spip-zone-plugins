<?php


  function filtre_listeLogo($inArray)
  {return commandParser($inArray,"logo");
  }
  
  function filtre_listeCite($inArray)
  {
     return commandParser($inArray,"cite");

  }
  
  function filtre_listeTypes($inListeLogo,$inListeCite)
  {
  $kind=0;
  $tab = 0;
  calculerOrdre($inListeLogo,$inListeCite,$kind,$tab);
   return $kind;  
  }
  
  function filtre_listeID($inListeLogo,$inListeCite)
  {
  $kind=0;
  $tab = 0;
  calculerOrdre($inListeLogo,$inListeCite,$kind,$tab);
   return $tab;  
  }
  
  function calculerOrdre($inListeLogo,$inListeCite,&$kind,&$tab)
  {
    $logoTotal = count($inListeLogo);
   $citeTotal = count($inListeCite);
   $total = $logoTotal + $citeTotal ;
   
   $lastLogo = 0;
   $lastCite = 0;
   $tab = array_fill ( 0 , $total,0 );
   $kind = array_fill ( 0 , $total,0 );
  
   for ($k=0;$k<$total;$k++)
   {
    $ratioLogo = $lastLogo / $logoTotal;
    $ratioCite = $lastCite / $citeTotal;
    if($ratioLogo < $ratioCite)
    {
     $tab[$k] = $inListeLogo[$lastLogo];
     $kind[$k] = 'logo';
     $lastLogo++;
    }
    else
    {
     $tab[$k] = $inListeCite[$lastCite];
     $kind[$k] = 'cite';
     $lastCite++;
     
    }
   }
   
  }
  
  
  
  
  function commandParser($inArray,$inCommandName)
   {$outArray = array();
    foreach($inArray as $item)
    {
      $list = explode("+",$item);
      $command = array_shift($list);
      if(strcmp($command,$inCommandName)==0)
      {
       $outArray = array_merge($outArray,$list);
      }
    }
   
   return $outArray;
  }

?>