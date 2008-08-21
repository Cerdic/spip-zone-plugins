<?php
function genie_spipmotion_file($time)  {
	$nb_videos = sql_countsel('spip_spipmotion_attentes', "encode='non'");
	spip_log("Il y a $nb_videos vidéo(s) à encoder","spipmotion");
	if($nb_videos>0){
		$encoder = charger_fonction('encodage','inc');
		$video_attente = sql_fetsel("*","spip_spipmotion_attentes","encode='non'","","maj","1");
		$id_video_attente = $video_attente['id_document'];
		$document = sql_fetsel('*','spip_documents','id_document='.sql_quote($id_video_attente));
		$encoder($document,$video_attente);
	}
}
?>