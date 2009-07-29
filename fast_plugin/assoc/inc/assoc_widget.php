<?php

/* class qui va recuperer du html pour construire des widgets */
class assoc_loader{
	public static function get_html($fichier){
		$fichier = dirname (__FILE__)."/../../../plugins/assoc/widget/$fichier.html";
		if(file_exists($fichier)){
			$open = fopen($fichier, "r");
			$retour = fread($open, filesize($fichier) );
			fclose($open);
			return $retour;
		}
	}
}

/* Widget pour les boutons du haut */ 

interface widget_bt_haut{
	public static function get_widget();
}

class bt_haut_classique implements widget_bt_haut  {
	public static function get_widget(){
		return "<span id='fermer_panel' onclick='class_assoc.close()' />Fermer &nbsp;</span>";
	}
}



/* Widget pour la zone de recherche */ 


interface widget_recherche{
	public static function get_widget();
}


class recherche_basic implements widget_recherche  {
	public static function get_widget(){
		return recuperer_fond("fonds/assoc_rub_select");
	}
}

class recherche_date implements widget_recherche  {
	public static function get_widget(){
		return assoc_loader::get_html("recherche_date");
	}
}


/* Widget pour la zone d'onglets  */ 
interface widget_onglet{
	public static function get_widget();
}

class onglet_omm implements widget_onglet  {
	public static function get_widget(){
		return "<p id='les-onglets'>
					<span onclick='assoc_object.onglet_actif(\"video\",this)'>Vidéo</span>
					<span onclick='assoc_object.onglet_actif(\"resultat\",this)' class='selected' >Resultat  </span>
				<p>";
	}
}


/* Widget pour la zone de resultat */ 




/* Widget pour la zone de info  */ 




/* Widget pour la zone de association */ 
interface widget_association{
	public static function get_widget();
}


class association_basic implements widget_association  {
	public static function get_widget(){
		return "<p id='preselect_element'></p>";
	}

}

class association_mag2008 implements widget_association  {
	public static function get_widget(){
		return "<p id='preselect_element'></p>
				<p id='active_datepicker'>
				Article jusqu'au : <input type='text' value=''  id='preselect_date' /> 
				puis le bloc fonctionnera un mode dynamique à partir du ...<br>
				 Thème : <input type='radio' name='automatique' value='theme'><br>
				 Sous thème : <input type='radio' name='automatique' value='soustheme' checked><br>
				</p>
		
				";
	}
}

class association_video implements widget_association  {
	public static function get_widget(){
		return assoc_loader::get_html("titre_descriptif");
	}
}


/* Widget pour la zone de bouton bas */ 
interface widget_bt_bas{
	public static function get_widget();
}


class selection_basic_inserer implements widget_bt_bas  {
	public static function get_widget(){
		return "<input type='button' value='Inserer' onclick='assoc_object.associer()' />";
	}
}

class selection_basic_associer implements widget_bt_bas  {
	public static function get_widget(){
		return "<input type='button' value='Associer' onclick='assoc_object.association()' />";
	}
}

class selection_video_home_inserer implements widget_bt_bas  {
	public static function get_widget(){
		return "<br><br>Insérez un bloc vidéo :<br><br>
		 <input type='button' value='Associer' onclick='assoc_object.video_bloc()' />";
	}
}

class selection_actuphonore_home_inserer implements widget_bt_bas  {
	public static function get_widget(){
		return "<br><br>Insérez un bloc Actuphonore:<br><br>
		 <input type='button' value='Associer' onclick='assoc_object.actuphonore_bloc()' />";
	}
}




?>