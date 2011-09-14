<?php

//
// functions
//

function coloriagedist_insert_head($flux){
  $flux .= "<link rel='stylesheet' type='text/css' href='?page=css_coloriage' />\n";   
  return $flux;
}

function coloriagedist_header_prive($flux){
  $flux .= "<link rel='stylesheet' type='text/css' href='".url_absolue(find_in_path('farbtastic/farbtastic.css'))."' />\n";     
  $flux .= "<script src='".url_absolue(find_in_path('farbtastic/farbtastic.js'))."' type=\"text/javascript\"></script>\n";
  $flux .= "<script src='".url_absolue(find_in_path('farbtastic/farbtastic_go.js'))."' type=\"text/javascript\"></script>\n";
	return $flux;

}


?>