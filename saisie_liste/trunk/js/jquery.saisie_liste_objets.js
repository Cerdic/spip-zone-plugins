(function () {

    $.fn.saisieListeObjets = function( options ) {

        var self = this;

        this.nom = options.nom;

        // numéroter les li's
        this.find('> li').each(function (index, li) {
            $(li).data('index_objet', index);
        });

        this.sortable({
            containement: 'parent',
            cursor: 'move',
            placeholder: 'ui-state-highlight',
            update: function (event, ui) {
                $(this).find('input[name*="permutations"]')
                    .attr('value', (function () {
                        var permutations = [];

                        self.find('> li').each(function (index, li) {
                            permutations.push($(li).data('index_objet'));
                         });
                        return permutations.join(',');
                    })());
            }
        });

        return this;
    };

})();
