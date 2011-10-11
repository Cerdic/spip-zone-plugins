<?php
function action_clevermail_post_create_dist() {
	$securiser_action = charger_fonction('securiser_action', 'inc');
  $arg = $securiser_action();
  $lst_id = intval($arg);

  include_spip('inc/autoriser');
  if (autoriser('creer','cm_post',$lst_id)) {
  	include_spip('inc/clevermail_post_create');
    $pst_id = clevermail_post_create($lst_id);
    include_spip('inc/headers');
    if ($pst_id) {
      redirige_par_entete(generer_url_ecrire('clevermail_posts').'&lst_id='.$lst_id);
    } else {
      redirige_par_entete(generer_url_ecrire('clevermail_lists').'&err_lst='.$lst_id.'&err_msg=erreur_contenu_vide#lst'.$lst_id);
    }
  }
}
?>
