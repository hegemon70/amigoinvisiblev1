
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
            before_add:function(collection, element) 
            {
                 
                  if(document.getElementsByClassName("glyphicon-trash").length===0)
                  {
                     console.log("no hay iconos basura");
                  }
                  else
                  {
                      var trashs=document.getElementsByClassName("glyphicon-trash");
                      basura=trashs[0];
                      //console.log(basura);
                      basura.parentNode.removeChild(basura);       
                  }
             },
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
            },
            after_remove: function(collection, element)
            {               
                  //console.log(collection);
                  console.log(collection["0"].childNodes);
                  var len=collection["0"].childNodes.length;
                  console.log(len);
                 
                  indiceUltimo=len-1;
                  indicePenultimo=len-3
                  console.log(indicePenultimo);
                  
                   console.log(collection["0"].childNodes[indicePenultimo]);
                   var abuelo=(collection["0"].childNodes[indicePenultimo]);     
                  
                 // var padre=anadir.previousSibling;
                  //var padre=element["0"].childNodes[1].childNodes[5].childNodes[1];
                  console.log(abuelo.childNodes[1].childNodes[5].childNodes[1]);
                  var padre=abuelo.childNodes[1].childNodes[5].childNodes[1];
                  var span = document.createElement("span");
                  span.classList.add("glyphicon");
                  span.classList.add("glyphicon-trash");
                  //span.appendChild(padre);
                  console.log(padre);
             },
            after_add: function(collection, element)
            {
                // var iconos=document.getElementsByClassName("collection-actions");
                //  console.log(element["0"].childNodes[1].childNodes[5].children["0"].childNodes); 
                        
            }
        });

