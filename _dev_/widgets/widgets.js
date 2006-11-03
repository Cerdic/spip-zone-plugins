/*
 *  widgets.js (c) Fil & Toggg 2006 -- licence GPL
 */

// le prototype configuration de Widgets
function cfgWidgets(options)
{
  this.url_widgets_html = 'spip.php?action=widgets_html';
  this.img = {
    'searching':'searching.gif',
    'edit':'crayon.png',
    'changed':'changed.png'
  };
  this.txt = {
    'searching':'En attente du serveur ...',
    'edit':'Editer',
    'img-changed':'Deja modifie'
  };
  for (opt in options) {
    this[opt] = options[opt];
  }
}
cfgWidgets.prototype.mkimg = function(what) {
  return '<img class="widget-' + what +
    '" src="' + this.imgPath + '/' + this.img[what] +
    '" title="' + this.txt[what] + '" />';
}
cfgWidgets.prototype.iconclick = function() {
  return "<span class='widget-icones'><span>" +
      this.mkimg('edit') +
      this.mkimg('img-changed') +
    "</span></span>";
}

// ouvre un widget
$.fn.openwidget = function(evt) {
  if (evt.stopPropagation) {
    evt.stopPropagation();
  }
  return this
  .each(function(){
    // verifier que je suis un widget
    if (!$(this).is('.widget'))
      return;

    // voir si je dispose deja du widget comme voisin
    if ($(this).is('.widget-has')) {
      $(this)
      .hide()
      .next()
        .show();
    }
    // sinon charger le formulaire
    else {
      $(this)
      .append(configWidgets.mkimg('searching')); // icone d'attente
      var me=this;
      $.getJSON(configWidgets.url_widgets_html,
        {
          'w': $(this).width(),
          'h': $(this).height(),
          'wh': window.innerHeight,
          'em': $(this).css('fontSize'),
          'class': me.className
        },
        function (c) {
          $(me)
          .find("img.widget-searching")
            .remove();
          if (c.$erreur) {
            alert(c.$erreur);
            return false;
          }
          $(me)
          .hide()
          .addClass('widget-has')
          .after('<div>'+c.$html+'</div>')
          .next()
            .activatewidget();
        }
      );
    }
  });
}

// annule le widget ouvert (fonction destructive)
$.fn.cancelwidget = function() {
  return this.prev()
    .filter('.widget-has')
    .show()
    .removeClass('widget-has')
    .removeClass('widget-changed')
  .next()
    .remove();
}

// masque le widget ouvert
$.fn.hidewidget = function() {
  return this
  .filter('.widget-has')
  .show()
  .next()
    .hide()
    .removeClass('widget-hover');
}

// active un widget qui vient d'etre charge
$.fn.activatewidget = function() {
  return this
  .click(function(e){
    e.stopPropagation();
  })
  .each(function(){
    var me = this;
    var w,h;
    $(me)
    .find('form')
      .ajaxForm(function(d){
        $(me)
          .find("img.widget-searching")
            .remove();
        eval('d=' + d.responseText + ';');
        if (d.$erreur > '') {
          if (d.$annuler) {
              alert(d.$erreur);
              $(me)
                .cancelwidget();
          } else {
              alert(d.$erreur+'\n'+configWidgets.txt.error);
              $(me)
                .find(".widget-boutons")
                  .show(); // boutons de validation
          }
          return false;
        }
        $(me)
        .prev()
          .html(
            d[$('form', me).find('.widget-id').val()]
          )
          .iconewidget();
        $(me)
          .cancelwidget();
      }).onesubmit(function(){
        $(this)
        .append(configWidgets.mkimg('searching')) // icone d'attente
        .find(".widget-boutons")
          .hide(); // boutons de validation
      }).keyup(function(){
        $(this)
        .find(".widget-boutons")
          .show();
        $(me)
        .prev()
          .addClass('widget-changed');
      })
      .find(".widget-active")
        .css({
            'fontSize': $(me).prev().css('fontSize'),
            'fontFamily': $(me).prev().css('fontFamily'),
            'fontWeight': $(me).prev().css('fontWeight'),
            'lineHeight': $(me).prev().css('lineHeight'),
            'color': $(me).prev().css('color'),
            'backgroundColor': $(me).prev().css('backgroundColor')
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
      .find(".widget-submit")
        .click(function(e){
          e.stopPropagation();
          $(this)
          .ancestors("form")
          .submit();
        })
      .end()
      .find(".widget-cancel")
        .click(function(e){
          e.stopPropagation();
          $(me)
          .cancelwidget();
        })
      .end()
      .find(".widget-hide")
        .click(function(e){
          e.stopPropagation();
          $(me)
          .prev()
          .hidewidget();
        })
      .end()
    .end();
  });
}

// insere les icones dans l'element
$.fn.iconewidget = function(){
  return this
    .prepend(configWidgets.iconclick())
    .find('.widget-edit') // le crayon a clicker lui-meme
      .click(function(e){
        $(this).ancestors('.widget').openwidget(e);
      });
}

// initialise les widgets (cree le clone actif)
$.fn.initwidget = function(){
  this
  .addClass('widget-autorise')
  .click(function(e){
    e.stopPropagation();
  })
  .dblclick(function(e){
    $(this).openwidget(e);
  })
  .iconewidget();

  // :hover pour MSIE
  if (jQuery.browser.msie) {
    this.hover(
      function(){
        $(this).addClass('widget-hover');
      },function(){
        $(this).removeClass('widget-hover');
      }
    );
  }

  return this;
}

// demarrage
$(document).ready(function() {
  if (!configWidgets.droits) return;
  if (!jQuery.getJSON) return; // jquery >= 1.0.2

  // sortie, demander pour sauvegarde si oubli
  $(window).unload(function(e) {
    var chg = $(".widget-changed");
    if (chg.length && confirm("Sauvegarder les modifications ?")) {
      chg.next().find('form').submit();
    }
  });

  $(".widget")
  .filter(configWidgets.droits)
  .initwidget();

  // fermer tous les widgets ouverts
  $("html")
  .click(function() {
    $(".widget.widget-has:hidden")
    .hidewidget();
  });
});

function dump(elt)
{var txt=''; for (var prop in elt) {txt += prop+'='+elt[prop]+'/';} alert(txt);}

