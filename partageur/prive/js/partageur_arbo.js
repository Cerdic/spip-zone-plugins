/* 
  affiche l'arbo ds site  en jquery/ajax 

*/


//----------------------------------
// Function
//---------------------------------- 
function arbocallback(data){ 

    // html
    
    // .... ariane ? 
    htm = "<ul class='partage_ariane'>";
    htm += "<li><a href='#' rel='-1'>&lt;</a></li>";
    htm += "<li class='url'><a href='#' rel='0'>"+url_site_partage+"</a></li>";   
    if (data.ariane) {    
       for (i=0;i<data.ariane.length;i++)            
          htm += "<li class='rub'><a href='#' rel='"+data.ariane[i].id+"'>"+data.ariane[i].titre+"</a></li>";       
    } 
    
    htm += "</ul><br class='nettoyeur' /><ul class='partage_contenu'>";
    
    // .... rubrique ?
    if (data.rubrique) {    
       for (i=0;i<data.rubrique.length;i++)            
          htm += "<li class='rub'><a href='#' rel='"+data.rubrique[i].id+"'>"+data.rubrique[i].titre+"</a></li>";       
    } 
    // .... article ?
    if (data.article) {    
       for (i=0;i<data.article.length;i++)            
          htm += "<li class='art'><a href='#' rel='art"+data.article[i].id+"'>"+data.article[i].titre+"</a></li>";       
    } 
    
    
    htm += "</ul>";
     
    $("#partageur_source").empty().append(htm); 
    
    $("#partageur_source li a").click(function(){                
               if ($(this).attr("rel") !="-1") {
                    //charge_arbo($(this).attr("rel"),url_site_partage);
                    var ref = $(this).attr("rel");
                    if (ref.substr(0,3) == "art") {
                        ref = ref.substr(3);
                        var id_rub = $(".cadre-info .numero p").html();                        
                        location.href  ="?exec=partageur_import&id_partageur="+id_url_site_partage+"&id_article="+ref+"&id_rubrique="+id_rub;                        
                    } 
                    else charge_arbo(ref,url_site_partage);
                    
               } 
                    else charge_sites();               
    });  
        
} 




// charge un bout d'arbo
// requete en json-p (requetes interdomaines)
function charge_arbo(id_rubrique, url_site) {
    $.getJSON(url_site+'/spip.php?page=partageur_arbo_json&id_rubrique='+id_rubrique+'&arbocallback=?'); 
}


function charge_sites() {
    $('#partageur_source').load('../spip.php?page=partageur_sites', function() {
    
        $("#partageur_source li a").click(function(){ 
             url_site_partage = $(this).attr("href");
             id_url_site_partage = $(this).attr("rel").substr(3);
             charge_arbo(0,url_site_partage);
             return false;
         }); 

    
    });
  
}  

      

//----------------------------------
// Global
//----------------------------------

var url_site_partage = "";
var id_url_site_partage = 0;

//----------------------------------
// Main
//----------------------------------
$(document).ready(function(){
 
    charge_sites();
    
});

