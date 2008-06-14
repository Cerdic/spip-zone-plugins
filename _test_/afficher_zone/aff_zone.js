// le jquery nécessaire pour le fonctionnement de l'interface espace privé de aff_zone

// la fonction pour cocher les plugins en fonction du mot clé sélectionné
    function coche_plugins(id_mot) {
        $("input:checkbox:checked").attr("checked", "");
        $("input.mot_" + id_mot).attr("checked", "checked");
        $(".cat_encours").attr("class", "");
        $("#mot_" + id_mot).parent().attr("class", "cat_encours");
    }
    
// la fonction pour sélectionner le mot clé attaché à un plugin 
    function coche_mot(id_mot) {
        $("#mot_" + id_mot).attr("checked", "checked");
        coche_plugins(id_mot);
    }
    
// fonction pour virer les class/id qui marquent le plugin comme attribué (à la désélection d'un plugin)
    function deselect_plugin(id_plugin) {
        id_plugin = id_plugin.substr(7);
        $("#li_" + id_plugin).attr("class", "");
        $("#li_" + id_plugin + " .acces_cat").attr("id", "");
        $("#plugin_" + id_plugin).attr("class", "plugin");
    }

$(document).ready(function() {
  // à la sélection d'un radio de mot clé, cocher / décocher les plugins correspondants
    $("input.mot").focus(function() {
        coche_plugins($(this).val());
    });
    
  // à la déselection d'un plugin virer les class/id spécifiques d'un plugin attribué
    $("input.plugin").blur(function() {
        if ($(this).attr("checked") == undefined) {
            deselect_plugin($(this).attr("id"));
        }
    });
    
  // au click sur l'img mot clé d'un plugin, sélectionner le radio du mot correspondant
    $(".acces_cat").click(function() {
        var id_mot = $(this).attr("id");
        id_mot = id_mot.substr(4);
        coche_mot(id_mot);
    });
    
  // à la validation d'un ensemble de plugins, requete ajax pour maj de la table spip_mots_syndic_articles
    $("#bouton_valider").click(function() {
        var id_mot = $(".mot:checked").val();
        if (id_mot == undefined) {
            alert("Vous devez sélectionner une catégorie");
            return;
        }
        var liste_plugins = '';
        var array_plugins = new Array();
        var statut = '';
        var Tsearch = window.location.search.substr(1).split('&');
        for (var i in Tsearch) {
            if (Tsearch[i].substr(0,6) == 'statut') {
                statut = Tsearch[i].substr(7);
                break;
            } 
        } 
        $("input.plugin:checked").each(function(){ 
            liste_plugins += (liste_plugins == '' ? '' : ',') + $(this).val();
            array_plugins.push($(this).val());
        });
        $.get("?exec=aff_zone&id_mot=" + id_mot + "&id_plug=" + liste_plugins + '&statut=' + statut, 
              function(data) {
                  var coul_rep = (data == "OK" ? "#0c0" : "#f00");
                  $("#retour_validation").text(data).css({color: coul_rep, display: "inline", fontWeight: "bold"});
                  if (data == "OK") {
                      for (i in array_plugins) {
                          $("#li_" + array_plugins[i]).attr("class", "mot_oui");
                          $("#plugin_" + array_plugins[i]).attr("class", "mot_" + id_mot + " plugin");
                          $("#li_" + array_plugins[i] + " .acces_cat").attr("id", "aff_" + array_plugins[i]);
                      }
                      $("#retour_validation").hide(5000);
                  }
              }
        );
    });
    
  // afficher / masquer les plugins ayant déja une catégorie
    $("#aff_masq_mot_oui").click(function() {
        $(".mot_oui").hide("slow");
        $(".mot_non").show("slow");
    });
    $("#aff_masq_mot_non").click(function() {
        $(".mot_oui").show("slow");
        $(".mot_non").hide("slow");
    });

});