<?php

function exec_flickr_bookmarklet_photo() {
  global $connect_id_auteur, $connect_statut;
  include_spip('inc/presentation');
  include_spip('inc/flickr_api');

  ///// debut de la page

  pipeline('exec_init',array('args'=>array('exec'=>'flickr_bookmarklet_photo'),'data'=>''));


  $id = intval(_request('id'));
  $secret = _request('secret');

  $photo_details = flickr_photos_getInfo($id,$secret);
  
  debut_page(_T('fpipr:ajouter_une_photo'),
			 "documents",
			 'plugin');

  debut_gauche();

  
  debut_boite_info();
  echo flickr_bookmarklet_info();
  fin_boite_info();

  echo '<div>&nbsp;</div>';
  echo icone(_T('icone_retour'), $photo_details->urls['photopage'], "article-24.gif", "rien.gif", '',false);

  debut_droite();
  echo '<div style="margin-top: 14px;" class="cadre-r">
<div style="position: relative;">
<div style="position: absolute; top: -16px; left: 10px;">
<img src="'.find_in_path('fpipr.gif').'"/>
</div>
</div>
<div style="overflow: hidden;" class="cadre-padding">';
  gros_titre(_T('fpipr:ajouter_une_photo'));


  echo '<div style="margin:.5em;">';
  echo '<img style="float:right;margin:.5em;" src="'.$photo_details->source('m').'"/>';
  echo '<h3>'._T('fpipr:ajouter_une_photo_info',array('title'=>$photo_details->title,'owner'=>$photo_details->owner_username)).'</h3>';
  if(!$photo_details->ispublic) {
	echo '<div  style="margin:.5em;">';
	if($photo_details->isfriend && $photo_details->isfamily) {
	  echo _T('fpipr:warning_family_friend');
	} else if($photo_details->isfamily) {
	  echo _T('fpipr:warning_family');
	} else if($photo_details->isfriend) {
	  echo _T('fpipr:waring_friend');
	} 
	echo '</div>';
  }
  echo '<div style="margin:.5em;">';
  if(($w = _T('fpipr:warning_copyright_'.$photo_details->license)) != 'warning copyright '.$photo_details->license) {
	echo $w;
  } else {
	$licenses = flickr_photos_licenses_getInfo();
	if($l = $licenses[$photo_details->license]) {			
	  echo _T('fpipr:warning_copyright_general',array('name'=>$l->name,'url'=>$l->url));
	} else {
	  echo _T('fpipr:warning_copyright_0');
	}
  }
  echo '</div>';
  echo '</div>';
  echo '<br clear="both"/>';

  if($connect_statut == '0minirezo')
	$requete = array('WHERE' => "", 'ORDER BY' => "date DESC");
  else {
	$rub = '';
	foreach(array_keys($connect_id_rubrique) as $id_rub) $rub .= 'OR id_rubrique='.$id_rub;
	$rub = substr($rub,3);
	$requete = array('WHERE' => "id_auteur='$connect_id_auteur' AND (statut='prop' OR statut='prepa' OR statut='poubelle')".(($rub)?" AND $rub":''), 'ORDER BY' => "date DESC");
  }
  echo '<form method="post" action="'.generer_action_auteur("flickr_ajouter_documents","article").'">';
  echo '<input type="hidden" name="type" value="article"/>';
  echo '<input type="hidden" name="photos[]" value="'."$id@#@$secret".'"/>';
echo  afficher_articles(_T('fpipr:choisir_un_article'),$requete,'flickr_afficher_articles_boucle');
  echo '<button type="submit">'._T('spip:bouton_valider').'</button>';
  echo '</form>';
  echo '</div>';
  fin_page();
}



?>
