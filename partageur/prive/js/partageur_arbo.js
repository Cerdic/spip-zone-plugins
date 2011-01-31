/* 
  affiche l'arbo ds site  en jquery/ajax 

*/


//----------------------------------
// Function
//---------------------------------- 
function arbocallback(data){ 

    // html    
    // ... recherche
    htm = "<form id='partageur_search'><div><input type='text' name='partageur_cherche' id='partageur_cherche' value='"+recherche+"' /><input type='button' value='ok' id='partageur_search_ok' /></div></form>";
    
    // .... ariane ? 
    htm += "<ul class='partage_ariane'>";
    htm += "<li><a href='#' rel='-1'>&lt;</a></li>";
    htm += "<li class='url'><a href='#' rel='0'>"+url_site_partage+"</a></li>";   
    if (data.ariane) {    
       for (i=0;i<data.ariane.length;i++)            
          htm += "<li class='rub'><a href='#' rel='"+data.ariane[i].id+"'>"+data.ariane[i].titre+"</a></li>";       
    } 
    htm += "</ul><br class='nettoyeur' />";

    // .... rubrique ?
    htm += "<ul class='partage_contenu'>";
    if (data.rubrique) {    
       for (i=0;i<data.rubrique.length;i++)            
          htm += "<li class='rub'><a href='#' rel='"+data.rubrique[i].id+"'>"+data.rubrique[i].titre+"</a></li>";       
    } 
    // .... article ?
    if (data.article) {    
       for (i=0;i<data.article.length;i++)            
          htm += "<li class='art'><a href='#' rel='art"+data.article[i].id+"'>"+data.article[i].titre+"</a></li>";       
    } 
    
    // .... erreur ?
    if (data.erreur) { 
        htm += "<li class='erreur'>Erreur: la clé de ce site est incorrecte</li>";   //FIXME lang ?
    } 
    
    htm += "</ul>";
     
    $("#partageur_source").empty().append(htm); 
    
    // comportement
    $("#partageur_source li a").click(function(){                
               if ($(this).attr("rel") !="-1") {
                    //charge_arbo($(this).attr("rel"),url_site_partage);
                    var ref = $(this).attr("rel");
                    if (ref.substr(0,3) == "art") {
                        ref = ref.substr(3);
                        var id_rub = $(".cadre-info .numero p").html();                        
                        location.href  ="?exec=partageur_import&id_partageur="+id_url_site_partage+"&id_article="+ref+"&id_rubrique="+id_rub+"&cle="+cle_url_site_partage;                        
                    } 
                    else charge_arbo(ref,url_site_partage,cle_url_site_partage);
                    
               } 
                    else charge_sites();               
    }); 
    
    $("#partageur_search").submit(function(){
        return false;
    });
        
    $("#partageur_search_ok").click(function(){
          recherche = $('input[name=partageur_cherche]').val();
          charge_recherche(recherche,url_site_partage,cle_url_site_partage);
    });  
    
    
        
} 




// charge un bout d'arbo
// requete en json-p (requetes interdomaines)
function charge_arbo(id_rubrique, url_site,cle_url_site_partage) {
    recherche = "";
    $.getJSON(url_site+'/spip.php?page=partageur_arbo_json&id_rubrique='+id_rubrique+'&cle='+cle_url_site_partage+'&arbocallback=?'); 
}

function charge_recherche(recherche, url_site,cle_url_site_partage) {
    $.getJSON(url_site+'/spip.php?page=partageur_arbo_json&recherche='+recherche+'&cle='+cle_url_site_partage+'&arbocallback=?'); 
}


function charge_sites() {
    $('#partageur_source').load('../spip.php?page=partageur_sites', function() {
    
        $("#partageur_source li a").click(function(){ 
             url_site_partage = $(this).attr("href");
             id_url_site_partage = $(this).attr("rel").substr(3); 
             cle_url_site_partage = $(this).attr("class").substr(3);             
             charge_arbo(0,url_site_partage,cle_url_site_partage);
             return false;
         }); 

    
    });
  
}  

      

//----------------------------------
// Global
//----------------------------------

var url_site_partage = "";
var id_url_site_partage = 0;
var cle_url_site_partage = "";
var recherche = "";

//----------------------------------
// Main
//----------------------------------
$(document).ready(function(){   
    charge_sites();
    
});

