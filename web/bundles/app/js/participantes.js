
//https://github.com/ninsuo/symfony-collection/issues/47

 $('.mis_participantes').collection({
            //prototype_name: '{{ form.participantes.vars.prototype.vars.name }}',
            //name_prefix: '{{ form.participantes.vars.full_name }}',
            allow_add: true,
            'add': '<a href="#" class="btn-sm btn-default collection-add" title="añadir Participante"><span class="glyphicon glyphicon-plus-sign"></span></a>',
            allow_remove: true,
            //'remove': '<a href="#" class="btn-sm btn-default collection-remove" title="quitar Participante"><span class="glyphicon glyphicon-trash"></span></a>',
            allow_down: false,
            allow_up: false,
            //custom_add_location: true,
            add_at_the_end: true,
            allow_duplicate: false,
            min: 3,
            hide_useless_buttons: true,
            drag_drop: false,
            position_field_selector: '.my-position',
            before_remove: function(collection, element) 
            { 
                var hijosPuros=collection["0"].childElementCount;
                var indiceUltima=hijosPuros-3;
                var indiceElemento=element["0"].id.split("_")["2"];
               if (indiceElemento==indiceUltima)
                  return true
               else
                { 
                  alert("no puedes borrar mas que la ultima"); 
                  return false
                  }
            }
        });

