
url_widgets_html = 'spip.php?action=widgets_html&class=';
SEARCHING = '<img src="dist/images/searching.gif" style="float:right;" />';

$.cancelwidgets = function() {
  $(".widget").each(function(){
    var html = $(this).attr('orig_html');
    if (html != null) {
      // enregistrer le widget avec le contenu modifie, si on veut y revenir
//      $(this).attr('widget', $(this).html());
      // puis reafficher le contenu initial
      $(this).html(html);
    }
    $(this).removeAttr('orig_html');
  });
}

$.initallwidgets = function(e) {
  $('.widget').each(function(){
    $.initwidget(this);}
  );
  e.stopPropagation();
}

$.initwidget = function(me) {
    // voir si je suis en mode "widget"
    if ($(me).attr('orig_html') != null)
      return;

    // voir si je dispose deja du widget (ne marche pas)
    if ($(me).attr('widget') != null) {
      $(me)
      .attr('orig_html', $(me).html())
      .html($(me).attr('widget'));
      // ici reactiver .ajaxForm() etc...
      return;
    }

    // charger le formulaire
    var contenu = $(me).html();
    $(me).append(SEARCHING); // icone d'attente
    $.get(url_widgets_html+encodeURIComponent(me.className),
       function (c) {
         var w,h;
         w = $(me).width();
         h = $(me).height();
         $(me)
         .attr('orig_html', contenu)
         .html(c)
         .find('form')
           .ajaxForm(function(c){
             $(me)
             .html(c.responseText)
             .removeAttr('orig_html');
           }).submit(function(){
             $(me).find("form").append(SEARCHING); // icone d'attente
           })
           .find(".widget-active")
             .css('backgroundColor', 'yellow')
             .css('font', 'inherit') // pour safari
             .css({
                 'fontSize': $(me).css('fontSize'),
                 'fontFamily': $(me).css('fontFamily')
             })
             // resize widget to fit current space, and a bit more if too small
             .each(function() {
               if (w<100) w=100;
               if (w>700) w=700;
               if (this.nodeName.toUpperCase()=='TEXTAREA') {
                 if (h<36) h=36;
                 h+='px';
               } else {
                 if (h<12) h=$(me).css('fontSize');
                 else h+='px';
               }
               $(this).css({"width":w+'px',"height":h});
             })
             .each(function(n){if (n==0) this.focus();})
             .keypress(function(e){
               if (e.keyCode == 27) {
                 $(me)
//                 .attr('widget',$(me).html()) // sauver le widget
                 .html($(me).attr('orig_html'))
                 .removeAttr('orig_html');
               }
             })
           .end()
           .find(".cancel_widget")
             .click(function(){
               $(me)
//               .attr('widget',$(me).html()) // sauver le widget
               .html($(me).attr('orig_html')) // retablir le contenu d'origine
               .removeAttr('orig_html');
               return false;
             })
           .end()
         .end()
         ;
       }
     );
  }

$.clickwidget = function(e){
  e.stopPropagation(); // avoid sending a global click to the body onclick
  $.initwidget(this);
}

$(function() {
  $('head')
  .prepend('<style>.widget:hover { background-image: url("dist/images/edit.gif"); background-repeat:no-repeat; background-position:right top; background-color: #e3eeee;}</style>');
  $(".widget")
  .attr('title', 'Cliquez pour modifier')  // pas terrible ;-)
  .click($.clickwidget);
// .animate(????);
  $("html")
  .click($.cancelwidgets);
});

