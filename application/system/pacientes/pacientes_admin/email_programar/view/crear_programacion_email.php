
<div class="form-group col-md-12 col-xs-12">
    <label for="">LISTA DE COMPORTAMIENTOS</label>
    <ul class="list-inline" style="border-bottom: 0.6px solid #333333; padding: 3px">
        <li> <a class="btnhover btn btn-sm " style="color: #333333" onclick="Programar()" > <b> <i class="fa fa-clock-o"></i> Programar  </b>  </a> </li>
    </ul>
</div>


<div class="form-group col-md-10 col-xs-12 " style="position: relative" >

    <div class="row">
        <div class="form-group col-md-12">
            <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-envelope"></i></span>
                <input type="email" id="email_destinario" class="form-control" placeholder="Destinario" onkeyup="FormValidacionEmailProgram()">
            </div>
        </div>
    </div>

    <div class="row">
        <div class="form-group col-md-12">
            <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-info"></i></span>
                <input type="text" id="asunto_email" class="form-control" placeholder="Asunto" onkeyup="FormValidacionEmailProgram()">
            </div>
        </div>
    </div>

    <div class="row">
        <div class="form-group col-md-12">
            <label for="">Message</label>
            <textarea name="mensage_mail" id="mensage_mail" cols="30" rows="10" class="form-control" style="resize: vertical" onkeyup="FormValidacionEmailProgram()"></textarea>
        </div>
    </div>

    <div class="row">
        <div class="form-group col-md-12">
            <ul class="mailbox-attachments clearfix" id="ulmailBox">

            </ul>
        </div>
    </div>

    <div class="row">
        <div class="form-group col-md-12">
            <table style=" table-layout: auto;" >
                <tr>
                    <td style="padding-right: 5px">
                        <button class="btn  " id="EnviarCorreoGuardar" style="background-color: #1a73e8; color: #ffffff"><b>  Guardar</b></button>
                    </td>
                    <td style="padding-right: 5px">
                        <button class="btn  " id="EnviarCorreoNow" style="background-color: #1a73e8; color: #ffffff"><b>  Enviar Mensaje</b></button>
                    </td>
                    <td style="padding-right: 5px">
                        <button class="btn  " id="EnviarCorreoProgramar" style="background-color: #00a65a; color: #ffffff"><b> <i class="fa fa-clock-o"></i> &nbsp; Programar Mensaje</b></button>
                    </td>
                    <td style="padding-right: 5px">
                        <label for="fileAdjuntar" class="btn btn-default"> <b><i class="fa fa-paperclip"></i> &nbsp;  Adjuntar Archivo</b></label>
                        <input type="file" class="hidden" id="fileAdjuntar">
                    </td>
                </tr>
            </table>
        </div>
    </div>

</div>

<!--//modal add fecha programable -->
<div id="programa_Date_correo" class="modal fade " role="dialog" data-backdrop="static">
    <div class="modal-dialog modal-sm" >

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header modal-diseng">
                <button type="button" class="close" data-dismiss="modal">×</button>
                <h4 class="modal-title" id="iddet-comment" data-iddet="163"><span>Programar Correo</span></h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-xs-12 col-md-12">
                        <div class="date2">
                            <span><b>Establecer una fecha para el envio de correo</b></span>
                            <div class="input-group date" data-provide="datepicker">
                                <input type="text" class="form-control fechaIni"  id="inputFecha" readonly="">
                                <div class="input-group-addon">
                                    <span class="fa fa-calendar"></span>
                                </div>
                            </div>
                            <small class="msg-error" style="color: red"></small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" id="" onclick="EnviarMensajeProgramado('C')"> Guardar </button>
            </div>
        </div>

    </div>
</div>



<script>

    ArrayFile = [];
    $SizeMB = 0;

    var Programar= function(){

        if(!ModulePermission(27, 2)){
            notificacion('Ud. No tiene permiso para esta Operación','error');
            return false;
        }

        window.location = $DOCUMENTO_URL_HTTP + '/application/system/pacientes/pacientes_admin/index.php?view=programa_email&key='+$keyGlobal+'&id='+$Paciente_id+'&v=crear_programacion_email';

    };


    var  FormValidacionEmailProgram = () => {


        var Errores = [];
        var RgxVacio     =  new RegExp((/^\s*$/));
        var expresionRegularEmail = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;

        var email    = $("#email_destinario");
        var mss_mail = $("#mensage_mail");
        var asunto   = $("#asunto_email");


        if(RgxVacio.test(email.val())){
            Errores.push({
                'documento':email,
                'mesg':'campo vacio'
            });
        }else{
            // alert(10);
            if(!expresionRegularEmail.test(email.val())){
                Errores.push({
                    'documento':email,
                    'mesg':'Email incorrecto'
                });
            }
        }
        if(RgxVacio.test(asunto.val())){
            Errores.push({
                'documento':asunto,
                'mesg':'campo vacio'
            });
        }if(RgxVacio.test(mss_mail.val())){
            Errores.push({
                'documento':mss_mail,
                'mesg':'campo vacio'
            });
        }

        // console.log(Errores);
        var valid = true;

        $(".error_perfil").remove();

        if(Errores.length>0){
            for (var i=0; i<=Errores.length-1;i++ ){

                var menssage =  document.createElement("small");
                menssage.setAttribute("style","display: block; color:red;");
                menssage.setAttribute("class","error_perfil");
                menssage.appendChild(document.createTextNode(Errores[i]['mesg']));
                var documentoDol        = Errores[i]['documento'];

                console.log(documentoDol);
                var inp = documentoDol.attr("id");
                if(inp=="asunto_email" || inp=="email_destinario"){
                    $(menssage).insertAfter(documentoDol.parent('.input-group'))
                }else{
                    $(menssage).insertAfter(documentoDol);
                }

            }
            valid = false;
        }else{
            valid = true;
        }

        return valid;
    };
    
    var AdjuntarFile = function (event) {

        var li   = document.createElement('li');

        var info = "<div class='Element ContentElementosEmailAdjuntar'>" +
            "<span class='mailbox-attachment-icon'> <i class='fa fa-file-pdf-o'></i> </span>" +
            "<div class=\"mailbox-attachment-info trunc\" style='width: 197.78'>" +
            "<a href=\"#\" class=\"mailbox-attachment-name\"><i class=\"fa fa-paperclip\"></i> Sep2014-report.pdf</a>" +
                "<span class=\"mailbox-attachment-size\">" +
                "    <span class='textSize'>1,245 KB</span>" +
                "    <span class='delete_file'><a class=\"btn btn-default btn-xs pull-right\"  onclick='deleteElement($(this))' ><i class=\"fa fa-remove\"></i></a></span>" +
                "    <span class='download_file hidden'><a  class=\"btn btn-default btn-xs pull-right\"><i class=\"fa fa-cloud-download\"></i></a></span>" +
                "</span>" +
            "</div>" +
            "</div>";

        var inputFile = document.querySelector('input[type=file]').files;
        var File = event.target.files;

        var FileArray = File[0];
        var Extencion = File[0]['type'];
        var Size      = (FileArray.size/1024/1024);
        var add = 0;
        var ExtencionLabel = (FileArray['name'].split('.'))[1];

        // alert(ExtencionLabel);
        //si el tamaño es mayor a 2 MB
        if(Size>2){
            notificacion('Limite mayor a 3 MB', 'question');
            $("#fileAdjuntar").val(null);
            return false;
        }

        // alert($(".ContentElementosEmailAdjuntar").length);

        if( $(".ContentElementosEmailAdjuntar").length > 2){
            notificacion('Solo puede adjuntar 3 Ficheros', 'question');
            $("#fileAdjuntar").val(null);
            return false;
        }

        if(validarFileRepetido(FileArray['name'])>0){
            notificacion('Archivo Adjunto ya se encuentra agregado ? '+FileArray['name'], 'question');
            $("#fileAdjuntar").val(null);
            return false;
        }

        //no puede ser mayor a 2MB
        $SizeMB += parseFloat(Size);
        if($SizeMB>2){
            notificacion('Limite mayor a 3 MB', 'question');
            return false;
        }

        console.log(FileArray);
        //Pdf
        if(Extencion=='application/pdf' && ExtencionLabel == 'pdf'){

            var nameFile = "<i class='fa fa-paperclip'></i> "+ FileArray['name'];
            var size = (FileArray.size/1024/1024);
            var base64 = btoa(FileArray['name']);


            var E = $(info).clone();
              E.addClass(base64).attr('data-name',FileArray['name']);
              E.find(".mailbox-attachment-icon").find('i').attr('class', 'fa fa-file-pdf-o');
              E.find(".mailbox-attachment-name").html(nameFile);
              E.find(".mailbox-attachment-size .textSize").html(size.toFixed(2)+' MB');
              E.find(".delete_file").find('a').attr('data-name',base64);
              // E.find(".mailbox-attachment-size .download_file").html();

            var A = $(li).append(E);
            $('#ulmailBox').append(A);

            add++;

        }

        //Word
        if(ExtencionLabel == 'docx'){

            var nameFile = "<i class='fa fa-paperclip'></i> "+ FileArray['name'];
            var size = (FileArray.size/1024/1024);
            var base64 = btoa(FileArray['name']);


            var E = $(info).clone();
              E.addClass(base64).attr('data-name',FileArray['name']);
              E.find(".mailbox-attachment-icon").find('i').attr('class', 'fa fa-file-word-o');
              E.find(".mailbox-attachment-name").html(nameFile);
              E.find(".mailbox-attachment-size .textSize").html(size.toFixed(2)+' MB');
              E.find(".delete_file").find('a').attr('data-name',base64);
              // E.find(".mailbox-attachment-size .download_file").html();

            var A = $(li).append(E);
            $('#ulmailBox').append(A);

            add++;

        }

        //Excel
        if(ExtencionLabel == 'xlsx'){

            var nameFile = "<i class='fa fa-paperclip'></i> "+ FileArray['name'];
            var size = (FileArray.size/1024/1024);
            var base64 = btoa(FileArray['name']);


            var E = $(info).clone();
              E.addClass(base64).attr('data-name',FileArray['name']);
              E.find(".mailbox-attachment-icon").find('i').attr('class', 'fa  fa-file-excel-o');
              E.find(".mailbox-attachment-name").html(nameFile);
              E.find(".mailbox-attachment-size .textSize").html(size.toFixed(2)+' MB');
              E.find(".delete_file").find('a').attr('data-name',base64);
              // E.find(".mailbox-attachment-size .download_file").html();

            var A = $(li).append(E);
            $('#ulmailBox').append(A);

            add++;

        }

        //video
        if(Extencion=='video/mp4'){

            var nameFile = "<i class='fa fa-paperclip'></i> "+ FileArray['name'];
            var size = (FileArray.size/1024/1024);
            var base64 = btoa(FileArray['name']);


            var E = $(info).clone();
            E.addClass(base64).attr('data-name',FileArray['name']);
            E.find(".mailbox-attachment-icon").find('i').attr('class', 'fa fa-play');
            E.find(".mailbox-attachment-name").html(nameFile);
            E.find(".mailbox-attachment-size .textSize").html(size.toFixed(2)+' MiB');
            E.find(".delete_file").find('a').attr('data-name',base64);
            // E.find(".mailbox-attachment-size .download_file").html();

            var A = $(li).append(E);
            $('#ulmailBox').append(A);

            add++;

        }

        if( add > 0){
            ArrayFile.push(FileArray);
        }

        console.log(ArrayFile);
        // var Filereader = new FileReader();
        // Filereader.onload  = function(){
        //
        //     var dataUrl = Filereader.result;
        //     console.log(dataUrl);
        // };

        $("#fileAdjuntar").val(null);

        if(add==0){
            notificacion('El sistema no detecto el tipo de archivo a agregar')
        }
    };

    var deleteElement = function(dele){

        alert(dele.attr('data-name'));

        var DeleteElementArr = [];
        var ElementoName = dele.attr('data-name');

        if(ArrayFile.length>0){
            $.each(ArrayFile, function(i,item) {
                if(btoa(item.name) == ElementoName){
                    var SizeT = (item.size/1024/1024);
                    console.log(i);
                    DeleteElementArr.push(i);
                    dele.parents('.Element').parent().remove();
                    console.log(dele.parents('.Element'));
                    $SizeMB += (SizeT *-1);
                }
            });

            // alert($SizeMB);
            //Elimino los Elementos que ya no estoy usando
            for (var x = 0; x <= DeleteElementArr.length-1; x++){
                ArrayFile.splice(DeleteElementArr[x],1);
            }

            console.log(ArrayFile);

        }

        
        console
            .log(ArrayFile);
    };



    function EnviarMensajeProgramado(estadobtn) {

        boxloading($boxContentViewAdminPaciente,true);

        if(FormValidacionEmailProgram()==false){
            notificacion('Datos Incorrectos verifique antes de realizar la operación','error');
            boxloading($boxContentViewAdminPaciente,false);
            return false;
        }

        var Form = new FormData();

        var estado = estadobtn;
        var dateProgram = "";

        //PROGRAMADO PARA UNA FECHA ESPECIFICA
        if(estadobtn=='C'){
            dateProgram = $("#inputFecha").val();
            if(dateProgram==""){
                notificacion('Debe selecionar una fecha programable para realizar la Operación', 'question');
                return false;
            }
            if(dateProgram==""){
                notificacion('Debe selecionar una fecha programable para realizar la Operación', 'question');
                return false;
            }
        }

        Form.append('accion', 'ProgramarEnvioCorreos');
        Form.append('ajaxSend', 'ajaxSend');
        Form.append('idPaciente', $id_paciente);

        $.each(ArrayFile, function (i,file) {
            Form.append('files[]', file);
        });

        Form.append('asunto', $("#asunto_email").val() );
        Form.append('email_destinario', $("#email_destinario").val() );
        Form.append('mensage_mail', $("#mensage_mail").val() );
        Form.append('DateProgramCorreo', dateProgram||"" );
        Form.append('estado', estado);

        setTimeout(function () { Send(); },1000);

        var Send = function(){
            $.ajax({
                url: $DOCUMENTO_URL_HTTP + '/application/system/pacientes/pacientes_admin/email_programar/controller.php',
                type:'POST',
                data: Form,
                dataType:'json',
                async:false,
                contentType: false,
                processData:false,
                cache:false,
                complete:function(){
                    boxloading($boxContentViewAdminPaciente,false, 1000);
                },
                success:function (respuesta) {
                    boxloading($boxContentViewAdminPaciente,false, 1000);

                    if(respuesta['error']['errorMail'] != "" || respuesta['error']['errorFile']){
                        var msgErr = respuesta['error']['errorMail']+'<br>'+respuesta['error']['errorFile'];
                        notificacion(msgErr, 'error');
                    }
                    else{
                        notificacion('Información Actualizada', 'success');
                        setTimeout(()=>{
                            window.location = $DOCUMENTO_URL_HTTP+"/application/system/pacientes/pacientes_admin/index.php?view=programa_email&key="+$keyGlobal+"&id="+$Paciente_id+"&v=emails_program";
                        },1500);
                    }
                }
            });
        };

    }

    function validarFileRepetido(name = null){
        if(name==null)
            return 1;

        var valid = 0;
        $.each(ArrayFile, function(i,item){
            if(item.name==name){
                valid++;
            }
        });
        return valid;
    }

    $('#fileAdjuntar').on('change', function(event) {
        if($(this).val()!=""){
            AdjuntarFile(event);
        }
    });

    //ENVIAR NOW
    $('#EnviarCorreoNow').on('click', function () {
        EnviarMensajeProgramado('A');
    });

    //PROGRAMADO
    $('#EnviarCorreoProgramar').on('click', function () {
        $("#programa_Date_correo").modal("show");
    });

    //PENDIENTE
    $('#EnviarCorreoGuardar').on('click', function () {
        EnviarMensajeProgramado('P');
    });

    $(window).on('load', function () {

        notificacion('El sistema no se responsabiliza por correo electrónico mal ingresado', 'question');
        FormValidacionEmailProgram();

        var Dateadd = new Date();
        console.log(Dateadd.getDate());
        console.log(Dateadd.getDay());

        $("#inputFecha").daterangepicker({
            drops: 'top',
            minDate : new Date(Dateadd.getFullYear(), Dateadd.getMonth(), Dateadd.getDate()),
            locale: {
                format: 'YYYY/MM/DD' ,
                daysOfWeek: [
                    "Dom",
                    "Lun",
                    "Mar",
                    "Mie",
                    "Jue",
                    "Vie",
                    "Sáb"
                ],
                monthNames: [
                    "Enero",
                    "Febrero",
                    "Marzo",
                    "Abril",
                    "Mayo",
                    "Junio",
                    "Julio",
                    "Agosto",
                    "Septiembre",
                    "Octubre",
                    "Noviembre",
                    "Diciembre"
                ],
            },
            singleDatePicker: true,
            showDropdowns: true,
            autoclose: true,
            pickerPosition: "bottom-left"
        });

    });

</script>