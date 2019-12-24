<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function image_split_header_prive_css($flux){
  $flux .= '<link rel="stylesheet" type="text/css" href="'.find_in_path("css/cropper.min.css").'" />'
  	.'<link rel="stylesheet" type="text/css" href="'.find_in_path("css/image_split_prive.css").'" />'
  	.'<link rel="stylesheet" href="'.find_in_path("css/twentytwenty.css").'" type="text/css" media="all" />';
  return $flux;
}

function image_split_insert_head($flux){
  $flux .= "<script src='".find_in_path("javascript/jquery.event.move.js")."' ></script>"
  	."<script src='".find_in_path("javascript/jquery.twentytwenty.js")."' ></script>"
  	."<link rel='stylesheet' href='".find_in_path("css/twentytwenty.css")."' type='text/css' media='all' />";
  return $flux;
}

function image_split_header_prive($flux){
  $flux .= "<script src='".find_in_path("javascript/cropper.min.js")."' ></script>"
  	."<script src='".find_in_path("javascript/jquery.twentytwenty.js")."' ></script>";
  return $flux;
}

?>