netix_buscarsocios(); netix_ubigeos();

function netix_ubigeos(){
    var resultados = {
        ajax: {
            url     : url+'ventas/guias/ubigeos',
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
                        text : data[i].ubigeo,
                        value: data[i].codubigeo,
                    }));
                }
            }
            return array;
        }
    };
    $(".ubigeos").selectpicker().filter(".ajax").ajaxSelectPicker(resultados); $("select").trigger("change");
}

function netix_buscarsocios(){
    if (netix_controller=="ventas/ventas") {
        tipo = 1;
    }else{
        if (netix_controller=="compras/compras") {
            tipo = 2;
        }else{
            tipo = 0;
        }
    }

	var resultados = {
        ajax: {
            url     : url+'ventas/clientes/buscar',
            type    : 'POST',
            dataType: 'json',
            data    : {q: '{{{q}}}',tipo: tipo}
        },
        locale: {emptyTitle: "SELECCIONE . . ."},
        preprocessData: function (data) {
            var i, l = data.length, array = [];
            if (l) {
                for (i = 0; i < l; i++) {
                    array.push($.extend(true, data[i], {
                        text : data[i].razonsocial,
                        value: data[i].codpersona,
                        data : {subtext: data[i].documento}
                    }));
                }
            }
            return array;
        }
    };
    $(".selectpicker").selectpicker().filter(".ajax").ajaxSelectPicker(resultados); $("select").trigger("change");
}

function netix_numeros(e){
    tecla = e.keyCode || e.which;
    base =/[0-9-.]/;
    teclado = String.fromCharCode(tecla).toLowerCase();
    return base.test(teclado);
}

function netix_decimales(e,id,cantidad){
    key = e.keyCode || e.which;
    tecla = String.fromCharCode(key).toLowerCase();

    numero = String($("#"+id).val()); decimal = numero.split(".");
    if(decimal.length==2 && tecla=="."){
        return false;
    }

    if(decimal[1]!=undefined){
        if(decimal[1].length==cantidad){
            return false;
        }
    }
}