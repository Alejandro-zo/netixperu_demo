netix_buscarproductos();

function netix_buscarproductos(){
	var resultados = {
        ajax: {
            url     : url+'almacen/productos/buscar_productos',
            type    : 'POST',
            dataType: 'json',
            data    : {q: '{{{q}}}'}
        },
        locale: {emptyTitle: "SELECCIONE . . ."},
        preprocessData: function (data) {
            var i, l = data.length, array = [];
            if (l) {
                for (i = 0; i < l; i++) {
                    array.push($.extend(true, data[i], {
                        text : data[i].descripcion,
                        value: data[i].codproducto,
                        data : {subtext: data[i].marca}
                    }));
                }
            }
            return array;
        }
    };
    $(".selectpicker").selectpicker().filter(".ajax").ajaxSelectPicker(resultados); $("select").trigger("change");
}