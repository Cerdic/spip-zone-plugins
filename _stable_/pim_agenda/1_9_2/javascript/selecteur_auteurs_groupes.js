function ajoute_auteur(node,id_auteur,name){
        var cible = $(node).parent().parent().parent().children('.liste_elts');
        var virgule = cible.children().length ?"<span class='virgule'>, </span>":"";
        var html = "<span class='elt' id='"+name+id_auteur+"'><input type='hidden' name='"+name+"[]' value='"+id_auteur+"' />"
                +virgule+$(node).html()
                +'<a href="#" onclick="return remove_auteur(this);">'
                +"<img class='remove' src='dist/images/croix-rouge.gif' width='7' height='7' />"
                +"</a>"
        +"</span>";
        cible.append(html);
        return false;
}
function remove_auteur(node){
        var cible=$(node).parent();
        var liste=cible.parent();
        cible.remove();
        liste = liste.children('span.elt');
        if (liste.length) liste.eq(0).children('span.virgule').remove();
        return false;
}
