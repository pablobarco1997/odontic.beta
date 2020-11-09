<?php


if(isset($_GET['ajaxSend']) || isset($_POST['ajaxSend']))
{

    session_start();

    require_once '../../../../config/lib.global.php';
    require_once DOL_DOCUMENT.'/application/config/main.php';


    $accion = GETPOST('accion');

    switch($accion)
    {
        case 'direct_pacient_list':

            $estado  = GETPOST('estado');
            $data    = array();

            $sql = "SELECT * FROM tab_admin_pacientes WHERE rowid > 0 ";
            if(!empty($estado))
            {
                if($estado == 'E'){ $sql .= " and estado = 'E' "; }
                if($estado == 'A'){ $sql .= " and estado = 'A' "; }
            }

            $sql .= " order by rowid desc";
            $rs = $db->query($sql);

            if($rs->rowCount() > 0)
            {
                while($fila =  $rs->fetchObject())
                {

                    $row = array();

                    /*
                     * {
                     *      key: PASSWORD ,
                     *      id: 18
                     * }
                     * */

                    #ID IMPORTANTE YA QUE ES UN TOKEN CREADO COMO UN ID DE LA CITAS GENERADO EN UN BINARIO HEXADECIMAL
                    $token = tokenSecurityId($fila->rowid); #ME RETORNA UN TOKEN
                    $view  = "dop"; #view vista de datos personales admin pacientes

                    #ruc cedula
                    $Ruc_Cedula = (!empty($fila->ruc_ced))?$fila->ruc_ced:'no asignado';

                    $img = DOL_DOCUMENT."/logos_icon/icon_logos_".$conf->EMPRESA->ENTIDAD."/".$fila->icon;
                    if(file_exists($img) && $fila->icon != "")
                        $img = "<div style='padding: 0px; '> <img src='".DOL_HTTP."/logos_icon/icon_logos_".$conf->EMPRESA->ENTIDAD."/".$fila->icon."' class='img-sm' > </div>";
                    else
                        $img = "<div style='padding: 0px; '> <img src='".DOL_HTTP."/logos_icon/logo_default/avatar_none.ico' class='img-sm' > </div>";

                    $url_datosPersonales = DOL_HTTP ."/application/system/pacientes/pacientes_admin?view=$view&key=".KEY_GLOB."&id=$token";

                    $row[] = "";
                    $row[] = "<table> 
                                <tr>
                                    <td style='padding: 2px; background-color: black; background-color: rgba(0,0,0,0.0)'> $img</td>
                                    <td style='padding: 2px'> &nbsp;&nbsp;&nbsp;". $fila->nombre." ".$fila->apellido."</td>
                                </tr> 
                              </table>";
                    $row[] = $fila->direccion;

                    #$row[] = "<a id='ruddni_link' class='link_pacientes_id' data-id='$fila->rowid' href='".DOL_HTTP."/application/system/pacientes/admin_paciente?view=form_datos_personales&id=$fila->rowid'>$fila->rut_dni</a>";
                    /*$row[] = "<a id='ruddni_link' class='link_pacientes_id' data-id='$fila->rowid'
                                    href='".DOL_HTTP."/application/system/pacientes/pacientes_admin?view=$view&key=".KEY_GLOB."&id=$token'> <b>$Ruc_Cedula</b> </a> ";*/
                    $row[] = $Ruc_Cedula;
                    $row[] = $fila->email .'<br>'.(($fila->telefono_movil == "") ? "<i class='fa fa-phone-square'></i>&nbsp; No asignado" : "<i class='fa fa-phone-square'></i>&nbsp; ". $fila->telefono_movil);
                    $row[] = strtoupper($fila->sexo);

                    if($estado=='A') {
                        $row[] = "<a class='btn btn-block btn-xs' style='background-color: #fadbd8; color: red; font-weight: bolder; ' onclick='ActivarEliminarPaciente($fila->rowid,0)'>Desactivar</a>";
                    }

                    if($estado=='E') {
                        $row[] = "<a class='btn btn-block btn-xs' style=\"background-color: #D5F5E3; color: green; font-weight: bolder\" onclick='ActivarEliminarPaciente($fila->rowid,1)'>Activar</a>";
                    }

                    $row['url_datosper'] = $url_datosPersonales;
                    $row['id_paciente']  = $fila->rowid;
                    $data[] = $row;
                }
            }

            $output = [
             'data' => $data,
            ];

            echo json_encode($output);

            break;

        case 'ObtenerPacienteslistaSearch':

            $data = [];

            $label = GETPOST('label');

            if( !empty($label) )
            {
                $searchType = " and concat(replace(ps.ruc_ced,' ',''),'',replace(ps.nombre,' ',''),'',replace(ps.apellido,' ','')) like '%".str_replace(' ', '', $label)."%'  limit 10";
                #busqueda de paciente se concat search type
                $sql = "SELECT * FROM tab_admin_pacientes ps WHERE ps.rowid > 0 ";
                $sql .= $searchType;

                $rs = $db->query($sql);
                if($rs &&  $rs->rowCount() > 0 &&  !empty($searchType) )
                {
                    while( $obPaciente =  $rs->fetchObject() )
                    {
                        #SI EN CASO EL PACIENTE ES ESTADO E se alerta paciente inactivo
                        $data[] = array(
                            'name'  => $obPaciente->ruc_ced .'  -  '. $obPaciente->nombre .' '.$obPaciente->apellido . (($obPaciente->estado=="E") ? "  ": ""),
                            'id'    => tokenSecurityId( $obPaciente->rowid )
                        );
                    }

                }else{

                }

            }else{

                $data[] = array(
                    'name'  =>  'NO SE ENCONTRO RESULTADOS ...',
                    'id'    =>  ''
                );
            }

            echo json_encode($data);

            break;

        case 'updateEstado':

            $id = GETPOST('id');
            $estado = GETPOST("estado");
            $error = false;

            if(!empty($id)) {
                $sql = "UPDATE `tab_admin_pacientes` SET `estado` = '$estado' WHERE (`rowid` = '$id');";
                $rs = $db->query($sql);
                if($rs){$error='OK';}
            }

            echo json_encode($error);

            break;
    }
}

?>