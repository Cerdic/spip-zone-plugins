<?php
function melusine_cssprive($flux){
$flux.='
<STYLE type="text/css">
.actif_gauche input, .actif_droite input, .reserve input,.init input,
.formulaire_editer_boutons input.submitb,input.voir,input.cache{
border:0px;
color:transparent;
background-repeat:no-repeat;
width:15px;
height:19px;
cursor:pointer;
font-size:0px;
_line-height:1000px;
}	
input.bas{background-image:url('.url_absolue(find_in_path('images/bas.jpg')).')}
input.haut{background-image:url('.url_absolue(find_in_path('images/haut.jpg')).')}
input.sup{background-image:url('.url_absolue(find_in_path('images/croix.gif')).');width:15px;height:14px}
input.ajout{background-image:url('.url_absolue(find_in_path('images/droite.jpg')).');width:20px;height:15px}
input.ajoutg  {background-image:url('.url_absolue(find_in_path('images/gauche.jpg')).');width:20px;height:15px}

.actif_gauche,.actif_droite,.reserve{
float:left;
width:33%;
border:1px solid;
height:550px;
}

.actif_gauche h2,.actif_droite h2,.reserve h2,
.actif_gauche h3,.actif_droite h3,.reserve h3{
text-align:center;
}

.actif_gauche ul, .actif_droite ul, .reserve ul{
display:block;margin:10px 0 5px 5px;;padding:0}
.reserve ul{text-align:center;}

.actif_gauche li,.actif_droite li,.reserve li,.actif_gauche ul{
list-style-type:none;
}

.actif_gauche li,.actif_droite li,.actif_gauche ul{
_margin-bottom:-5px;
}

.reserve {border:0px solid;}

.init ul li,.init{font-size:14px;float:left;width:100%;list-style-type:none;}


.formulaire_editer_boutons input.ok_bouton,.ok_bouton,.init input.ok_bouton{
background-image:url('.url_absolue(find_in_path('images/ok.gif')).');
width:22px;
height:20px;
border:1px solid #000;
margin:10px;}
/*position champs extras couleur*/

li.extra input{width:60px;}

.gestion_bouton{display:inline;}
.vue_bouton{display:inline;}
input.cache{background-image:url('.url_absolue(find_in_path('images/oeil_ferme.gif')).');display:inline;background-repeat:no-repeat;width:25px;height:25px}
input.voir{background-image:url('.url_absolue(find_in_path('images/oeil_ouvert.gif')).');display:inline;background-repeat:no-repeat;width:25px;height:25px}


</STYLE>';
return $flux;


}
?>