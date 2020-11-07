
class DocumentoFormulario{

    constructor(Elment, id_Elment){

        this.Elment     = Elment;
        this.id_Elment  = id_Elment;

    }

    Columna(Colmn, id, width){

        var Elemento = this.Elment;
        var table = "";

        var FormatoIndex = 0;
        FormatoIndex += (parseFloat(($(".column").length)) + 1);

        var idContent = [];

        table += "<table   width='100%' style='border-collapse: collapse; width: 100%; margin-top: 5px' >";
            table += "<tr>";

                for (var i = 1; i <= id; i++){

                    FormatoIndex++;
                    var idTable = "id='tableColumn"+id+"-"+FormatoIndex+"'";

                    table += "<td class='tdbox "+Colmn+" Columndblclick column' width='"+width+"%' id='tdContent"+FormatoIndex+"' style='border: 1px solid #bfbfbf; cursor: pointer; vertical-align: top'>";
                        table += "<table "+idTable+"  style='border-collapse: collapse; width: 100%; margin: 1px' width='100%' ></table>";
                    table += "</td>";

                    idContent.push("#tdContent"+FormatoIndex);
                }

            table += "</tr>";
        table += "</table>";

        Elemento.append($(table));

        $.each(idContent, function (i,item) {
            $(item).dblclick(function() {
                addElmentosConf($(this));
            });
        });

    }

    btntextalign(Element){

        if(Element.parent().children().hasClass("active")){
            Element.parent().children().removeClass("active");
            Element.addClass("active");
        }
    }

    addElementosTexto(idTableParent){

        var Table = $("#"+idTableParent);

        var validPosition = "";
        if($(".btn-checked-align-left").hasClass("active"))
            validPosition = "left";
        if($(".btn-checked-align-center").hasClass("active"))
            validPosition = "center";
        if($(".btn-checked-align-right").hasClass("active"))
            validPosition = "right";

        if(validPosition!="")
        {
            var Texto     = "";
            var value = $("#value_texto").val();

            if($("#text_titulo").is(":checked")){
                Texto = "<h3 style='margin: 0px; font-weight: bolder'><span>"+value+"</span></h3>";
            }if($("#text_parrafo").is(":checked")){
                Texto = value;
            }if($("#text_label").is(":checked")){
                Texto = "<span style='display: block;margin: 0px; padding: 0px;font-weight: bold'> "+value+" </span>";
            }
            Table.parent().removeClass("tdbox").css("padding", "2px");
            var td = "";
                td  += "<tr>";
                    td += "<td width='100%' style='text-align: "+validPosition+"; padding: 0px'>"+Texto+"</td>";
                td += "</tr>";
            Table.append(td);
        }

    }

    addElementosCamposTexto(idTableParent, name){

        var Table           = $("#"+idTableParent);
        var count           = "countName";
        var FormatoIndex    = 0;
        FormatoIndex       += (parseFloat(($(".countName").length)) + 1);
        var input           = "";

        if($("#cajaTextoTexo").is(":checked")){
            FormatoIndex++;
            input = "<input class='"+count+" InputCajasTexto form-control' type='text' name='"+name+"_"+ FormatoIndex +"' >";
        }if($("#cajaTextoNumero").is(":checked")){
            FormatoIndex++;
            input = "<input class='"+count+" InputCajasTexto form-control' type='number' name='"+name+"_"+ FormatoIndex +"'  >";
        }if($("#cajaTextoFecha").is(":checked")){
            FormatoIndex++;
            input = "<input class='"+count+" InputCajasTexto form-control' type='date' name='"+name+"_"+ FormatoIndex +"' >";
        }
        Table.parent().removeClass("tdbox").css("padding", "2px");
        var td = "";
            td += "<tr>";
                td += "<td width='100%' style='padding: 0px'>"+input+"</td>";
            td += "</tr>";
        Table.append(td);

    }

    addElementosCamposCheckebox(idTableParent, name, label){

        var Table           = $("#"+idTableParent);
        var count           = "countName";
        var FormatoIndex    = 0;
        FormatoIndex       += (parseFloat(($(".countName").length)) + 1);
        var input           = "";

        if($("#addchecked").is(":checked")){
            FormatoIndex++;
            input += "<label style='padding: 1.5px; margin: 0px'> <input type='checkbox' class='"+count+"' name='"+name+"_"+FormatoIndex+"' > "+label+" </label>";
        }

        Table.parent().removeClass("tdbox").css("padding", "2px");
        var td = "";
            td += "<tr>";
              td += "<td width='100%' style='padding: 0px'>"+input+"</td>";
            td += "</tr>";
        Table.append(td);

    }

    addElementosListDesplegable(idTableParent, name, label){

        var RgxVacio        =  new RegExp((/^\s*$/));
        var Table           = $("#"+idTableParent);
        var count           = "countName";
        var FormatoIndex    = 0;
        FormatoIndex       += (parseFloat(($(".countName").length)) + 1);
        var input           = "";

        FormatoIndex++;
        input += "<select class='"+count+"  form-control' name='"+name+"_"+FormatoIndex+"'>";
            $(".optionListaDesp").each(function(i, item) {
                var value   =  $(this).val();
                if(!RgxVacio.test(value)){
                    input += "<option value='"+(value.replace((/[^a-z0-9\s]/),'').replace((/ /g),'_'))+"'>"+value+"</option>";
                }
            });
        input += "</select>";

        Table.parent().removeClass("tdbox").css("padding", "2px");
        var td = "";
            td += "<tr>";
                td += "<td width='100%' style='padding: 0px'>"+input+"</td>";
            td += "</tr>";
        Table.append(td);

    }




}