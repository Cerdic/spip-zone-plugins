
url_widgets_html = 'spip.php?action=widgets_html';
url_widgets_droits = 'spip.php?action=widgets_droits';
SEARCHING = '<img src="dist/images/searching.gif" style="float:right;" />';

$.cancelwidgets = function() {
  $(".widget").each(function(){
    if ($(this).attr('orig_html') != null) {
      $(this)
      .cancelwidget();
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

  $(me)
  .attr('orig_html', $(me).html());

  // voir si je dispose deja du widget
  if ($(me).attr('widget') != null) {
    // alors on restitue le widget enregistre
    $(me)
    .html($(me).attr('widget'))
    // avec sa valeur eventuellement modifiee
    .find('.widget-active')[0].value = $(me).attr('valuewidget');
    $(me)
    .activatewidget();
  }
  // sinon charger le formulaire
  else {
    $(me)
    .append(SEARCHING); // icone d'attente
    $.get(url_widgets_html
      + '&w=' + $(me).width()
      + '&h=' + $(me).height()
      + '&em=' + $(me).css('fontSize')
      + '&class=' + encodeURIComponent(me.className)
     ,
      function (c) {
        $(me)
        .html(c)
        .attr('widget',c)
        .activatewidget();
      }
    );
  }
}

$.clickwidget = function(e){
  e.stopPropagation(); // avoid sending a global click to the body onclick
  $.initwidget(this);
}

// recupere le contenu "actuel" d'un widget pour recuperer les donnees
// si on reouvre le widget apres l'avoir ferme
$.fn.cancelwidget = function() {
  this.each(function(){
    $(this)
    .attr('valuewidget', $('.widget-active',this)[0].value)
    .html($(this).attr('orig_html'))
    .removeAttr('orig_html');
  });
  return this;
}


$.fn.activatewidget = function() {
  this.each(function(){
    var me = this;
    var w,h;
    $(me)
    .find('form')
      .ajaxForm(function(d){
        $(me)
        .html(d.responseText)
        .removeAttr('orig_html');
      }).onesubmit(function(){
        $("form", me)
        .append(SEARCHING); // icone d'attente
      })
      .find(".widget-active")
        .css('font', 'inherit') // pour safari
        .css({
            'fontSize': $(me).css('fontSize'),
            'fontFamily': $(me).css('fontFamily')
        })
        .each(function(n){
          if (n==0)
            this.focus();
        })
        .keypress(function(e){
          if (e.keyCode == 27) {
            $(me)
            .cancelwidget();
          }
        })
      .end()
      .find(".cancel_widget")
        .click(function(){
          $(me)
          .cancelwidget();
          return false;
        })
      .end()
    .end();
  });
  return this;
}

$(function() {

  $('head')
  .prepend('<style>.widget-hover { background-image: url("dist/images/edit.gif"); background-repeat:no-repeat; background-position:right top; background-color: #e3eeee;}</style>');

  // Aller chercher les droits a partir de la liste des classes
  var vus = '';
  $(".widget")
  .each(function(){
    vus += '&'+this.className
  });

  // Quand on recupere la liste des droits, on active les widgets autorises
  if (vus)
  $.post(url_widgets_droits, {'vus': vus},
    function(c) {
      c = c.split('|');
      for (var i=0; i<c.length; i++) {
        $(".widget."+c[i])
        .hover( // obligatoire pour MSIE
          function(){$(this).addClass('widget-hover');},
          function(){$(this).removeClass('widget-hover');}
        )
        .attr('title', 'Cliquez pour modifier')  // pas terrible ;-)
        .click($.clickwidget);
//      .animate(????);
        $("html")
        .click($.cancelwidgets);
      }
    }
  );
});

