(function () {

    $.fn.saisieListeObjets = function( options ) {

        var self = this,
            defaut_sortable = {
                // valeurs par défaut pour sortable
                containement: 'parent',
                cursor: 'move',
                placeholder: 'ui-state-highlight'
            },
            options_sortable;

        options = $.extend(true,
                           {
                               sortable: {
                                   update: function () { return; }
                               }
                           },
                           options
                          );

        options.sortable.update = calculerFonctionUpdate(options.sortable.update);
        options.sortable = $.extend(defaut_sortable, options.sortable);

        this.nom = options.nom;

        // numéroter les li's
        this.find('> li').each(function (index, li) {
            $(li).data('index_objet', index);
        });

        function calculerFonctionUpdate (callback) {
            return function (event, ui) {
                self.parent().find('input[name*="permutations"]')
                    .attr('value', (function () {
                        var permutations = [];

                        self.find('> li').each(function (index, li) {
                            permutations.push($(li).data('index_objet'));
                         });
                        return permutations.join(',');
                    })());
                if (typeof(callback === 'function')) {
                    callback.call(self, event, ui);
                }
            };
        }

        this.sortable(options.sortable);

        return this;
    };

})();
