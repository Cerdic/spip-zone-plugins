<?php

function oficina_key($email) {
  include_spip('inc/securiser_action');
  return calculer_cle_action('poster-'.$email);
}
