<?php
function tagscloud_css($nbSteps, $minColor, $maxColor, $nuageClass)
{
  $start['r'] = hexdec(substr($minColor, 1, 2));
  $start['g'] = hexdec(substr($minColor, 3, 2));
  $start['b'] = hexdec(substr($minColor, 5, 2));

  $end['r'] = hexdec(substr($maxColor, 1, 2));
  $end['g'] = hexdec(substr($maxColor, 3, 2));
  $end['b'] = hexdec(substr($maxColor, 5, 2));

  $step['r'] = ($end['r'] - $start['r']) / ($nbSteps - 1);
  $step['g'] = ($end['g'] - $start['g']) / ($nbSteps - 1);
  $step['b'] = ($end['b'] - $start['b']) / ($nbSteps - 1);
  
  $css = '';
  for ($i = 0; $i < $nbSteps; $i++) {
    $rgb['r'] = floor($start['r'] + ($step['r'] * $i));
    $rgb['g'] = floor($start['g'] + ($step['g'] * $i));
    $rgb['b'] = floor($start['b'] + ($step['b'] * $i));
  
    $css .= '#nuage'.$nuageClass.' li a.tag'.$i.', #nuage'.$nuageClass.' li a.tag'.$i.':visited {
  color: '.sprintf('#%02x%02x%02x', $rgb['r'], $rgb['g'], $rgb['b']).' !important;
}
  ';
  }
  return $css;
}

function spip_asort($array) {
    if (is_array($array)) {
        asort($array);
    }
    return $array;
}

function spip_arsort($array) {
    if (is_array($array)) {
        arsort($array);
    }
    return $array;
}
?>