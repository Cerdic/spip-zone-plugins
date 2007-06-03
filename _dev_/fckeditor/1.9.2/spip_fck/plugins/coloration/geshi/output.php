<?php
  include_once('geshi.php');
  
  $geshi =& new GeSHi($_POST['source'], $_POST['lang']);
  //Pour permettre la variation de la taille de la tabulation
  //Attention les tabulation seront alors remplacèes par des espaces.
  $geshi->set_header_type(GESHI_HEADER_DIV);
  
  //Permet la décoration des lignes : changement de couleur toutes les 5 lignes
  if ($_POST['lines']!='') 
    $geshi->enable_line_numbers(GESHI_FANCY_LINE_NUMBERS, 5);
    else
    $geshi->enable_line_numbers(GESHI_NO_LINE_NUMBERS,5);
  $geshi->set_line_style('background: #fcfcfc;', 'background: #f0f0f0;');

  // Choix du set de charactère
  $geshi->set_encoding('utf8');
  
  //Largeur de la tabulation
  $geshi->set_tab_width($_POST['largtab']);
  
  //Début de la numérotation
  $geshi->start_line_numbers_at($_POST['premnum']); 
  
  echo $geshi->parse_code();
?>