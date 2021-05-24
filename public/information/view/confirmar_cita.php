<?php



    $fecha = GET_DATE_SPANISH( date('Y-m-d') );

    $objPacienteInfo = [];

    if(isset($_GET['token']))
    {
        $objPacienteInfo = json_decode(decomposeSecurityTokenId($_GET['token']));
    }else{
        print_r("<h5>ACCESO DENEGADO</h5>");
        die();
    }

    $id_citadet     = $objPacienteInfo[0];  #id de la cita detalle
    $name_db_entity = $objPacienteInfo[1];  #nombre de la entidad a conectar
    $entitydb       = $objPacienteInfo[2];

    $db_encrytp = tokenSecurityId($name_db_entity); #NAME DATA BASE ENCRIPTADO

//    echo '<pre>'; print_r($objPacienteInfo); die();
?>

<style>


    .swal2-content{
        font-size: small !important;
    }

    .insetbox-body{
        -webkit-box-shadow: inset 0 3px 6px rgba(0, 0, 0, 0.16), 0 3px 6px rgba(0, 0, 0, 0.23);
        -moz-box-shadow: inset 0 3px 6px rgba(0, 0, 0, 0.16), 0 3px 6px rgba(0, 0, 0, 0.23);
        -ms-box-shadow: inset 0 3px 6px rgba(0, 0, 0, 0.16), 0 3px 6px rgba(0, 0, 0, 0.23);
        -o-box-shadow: inset 0 3px 6px rgba(0, 0, 0, 0.16), 0 3px 6px rgba(0, 0, 0, 0.23);
        box-shadow: inset 0 3px 6px rgba(0, 0, 0, 0.16), 0 3px 6px rgba(0, 0, 0, 0.23);
        padding-top: 15px;
    }

    #swal2-content{
        font-size: 1.6rem !important;
    }

</style>

<?php if(isset($_GET['token'])) { ?>

    <div style="width: 100%; padding: 20px">
        <table align="center" style="border: 1px solid #d2d6de; width: 500px; padding: 30px; border-collapse: initial ">
            <tr>
                <td align="center" colspan="2">
                    <p>
                        <img
                                src="<?= DOL_HTTP .'/logos_icon/logo_default/campana.png' ;?>"
                                alt=""
                                width="90px"
                                height="90px"
                        />
                    </p>
                </td>
            </tr>
            <tr>
                <td align="center" colspan="2"><h3 style="border: 1px solid #0078d7; padding: 2px;border-radius: 5px; color:#0078d7; font-weight: bolder">La dental Prueba</h3></td>
            </tr>


            <tr style="padding-bottom: 15px;">
                <td colspan="2" style="border-bottom: 1px solid #d2d6de;" ></td>
            </tr>

            <tr >
                <td align="center" colspan="2" style="padding-bottom: 15px;">
                    <small style="color: #6a737d;">Le recordamos que tiene una cita agendada para la fecha asignada<br> abril martes 06 , 2021 - hora 18:00:00</small>
                </td>
            </tr>

            <tr>
                <td colspan="2" style="border-bottom: 1px solid #d2d6de; padding-bottom: 15px;"></td>
            </tr>
            <tr>
                <td align="center" colspan="2" style="padding-bottom: 15px;">
                    <small>
                        recuerde que es importante que acuda a su cita con el tiempo establecido de anticipación, si por
                        cualquier motivo no va a asistir por favor comuníquese <b>0987722863</b>
                    </small>
                </td>
            </tr>

            <tr>
                <td colspan="2" style="border-bottom: 1px solid #d2d6de; padding-bottom: 15px;"></td>
            </tr>
            <tr>
                <td align="right">
                    <br>
                    <small style="border: 1px solid #0078d7; padding: 2px;border-radius: 5px; color:#0078d7; font-weight: bolder">Telefonó: 0987722863</small></td>
            </tr>
            <tr>
                <td align="right">
                    <br>
                    <small style="border: 1px solid #0078d7; padding: 2px;border-radius: 5px; color:#0078d7; font-weight: bolder">Dirección: Guayaquil</small></td>
            </tr>

            <tr>
                <td colspan="2" align="center">
                    <br>
                    <button type="button" class="btn btn-success btn-block" onclick="ConfirmarCitasAsistir(this)" data-actioncita="asistir" >Asistir</button>
                    <br>
                    <button type="button" class="btn btn-danger btn-block" onclick="ConfirmarCitasAsistir(this)" data-actioncita="no_asistir" >No Asistir</button>
                </td>
            </tr>

        </table>
    </div>

<?php } ?>




<script>

    $dbname   = "<?= $db_encrytp ?>";
    $dbentity = "<?= $entitydb ?>";

    function ConfirmarCitasAsistir(Element)
    {
        var Input       = $(Element);
        var action_cita = "";

        if(Input.prop('dataset').actioncita!=""&&Input.prop('dataset').actioncita=="asistir")
            action_cita = "ASISTIR";

        if(Input.prop('dataset').actioncita!=""&&Input.prop('dataset').actioncita=="no_asistir")
            action_cita = "NO_ASISTIR";


        if(action_cita==""){
            Swal.fire(
                'Error!',
                'Ocurrio un error con la cita confirmación de la cita, Consulte con soporte',
                'error'
            );
            return false;
        }

        $.ajax({

            url: "<?= DOL_HTTP ?>" + '/public/information/controller/informacion_controller.php',
            type:"POST",
            data:{
                'ajaxSend': 'ajaxSend',
                'accion'  : 'asistir_confim',
                'dbname'  : $dbname,
                'dbentity'  : $dbentity,
                'idcita'    : "<?= $id_citadet ?>",
                'token_id'  : "<?= $_GET['token'] ?>",
                'action_cita' : action_cita ,

            },
            dataType:'json',
            success: function(resp){

                if(resp.error.toString() == "") {
                    Swal.fire(
                        'Exito!',
                        'Información Actualizada',
                        'success'
                    );

                }else{

                    Swal.fire(
                        'Oops!',
                         resp.error,
                        'error'
                    );
                }
            }

        });
    }

</script>
