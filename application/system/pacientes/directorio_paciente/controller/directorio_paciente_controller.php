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

            $start  = GETPOST('start');
            $length = GETPOST('length');

            $estado  = GETPOST('estado');
            $data    = array();
            $PermisoConsultar = (PermitsModule("Directorio de pacientes", "consultar")==false)?" and 1<>1":"";

            $sql = "SELECT * FROM tab_admin_pacientes WHERE rowid > 0 ";
            if(!empty($estado)) {
                if($estado == 'E'){ $sql .= " and estado = 'E' "; }
                if($estado == 'A'){ $sql .= " and estado = 'A' "; }
            }
            $sql .= $PermisoConsultar;

            $Total = $db->query($sql)->rowCount();

            if($start || $length){
                $sql.=" LIMIT $start,$length;";
            }

            $sql .= " order by rowid desc";
            $result = $db->query($sql);
            if($result->rowCount() > 0){
                while($object =  $result->fetchObject()){
                    $row = array();
                    /*
                     * {
                     *      key: PASSWORD ,
                     *      id: 18
                     * }
                     * */
                    //ID IMPORTANTE YA QUE ES UN TOKEN CREADO COMO UN ID DE LA CITAS GENERADO EN UN BINARIO HEXADECIMAL
                    $token = tokenSecurityId($object->rowid); #ME RETORNA UN TOKEN
                    $view  = "dop"; #view vista de datos personales admin pacientes

                    #ruc cedula
                    $Ruc_Cedula = (!empty($object->ruc_ced))?$object->ruc_ced:'no asignado';

                    $img = DOL_DOCUMENT."/logos_icon/icon_logos_".$conf->EMPRESA->ENTIDAD."/".$object->icon;


                    if(file_exists($img) && $object->icon != ""){
                        $img64 = 'data:image/*; base64, '.base64_encode(file_get_contents(DOL_DOCUMENT.'/logos_icon/icon_logos_'.$conf->EMPRESA->ENTIDAD.'/'.$object->icon));
                        $img = "<div style='padding: 0px; '> <img src='".$img64."' class='img-sm' > </div>";
                    }
                    else{
//                        $img64 = 'data:image/*; base64, '.base64_encode(file_get_contents(DOL_HTTP.'/logos_icon/logo_default/icon_avatar.svg'));
                        $img = "<div style='padding: 0px; '> <img src='".DOL_HTTP.'/logos_icon/logo_default/icon_avatar.svg'."' class='img-sm' > </div>";
                    }

                    $url_datosPersonales = DOL_HTTP ."/application/system/pacientes/pacientes_admin?view=$view&key=".KEY_GLOB."&id=$token";

                    $row[] = "";
                    $row[] = "<table> 
                                <tr>
                                    <td style='padding: 2px; background-color: black; background-color: rgba(0,0,0,0.0)'> $img</td>
                                    <td style='padding: 2px'>". $object->nombre." ".$object->apellido."</td>
                                </tr> 
                              </table>";
                    $row[] = $object->direccion;

                    #$row[] = "<a id='ruddni_link' class='link_pacientes_id' data-id='$object->rowid' href='".DOL_HTTP."/application/system/pacientes/admin_paciente?view=form_datos_personales&id=$object->rowid'>$object->rut_dni</a>";
                    /*$row[] = "<a id='ruddni_link' class='link_pacientes_id' data-id='$object->rowid'
                                    href='".DOL_HTTP."/application/system/pacientes/pacientes_admin?view=$view&key=".KEY_GLOB."&id=$token'> <b>$Ruc_Cedula</b> </a> ";*/

                    $row[] = $Ruc_Cedula;
                    $row[] = $object->email .'<br>'.(($object->telefono_movil == "") ? "<i class='fa fa-phone-square'></i> No asignado" : "<i class='fa fa-phone-square'></i> ". $object->telefono_movil);
                    $row[] = strtoupper($object->sexo);

                    if($estado=='A') {
                        $row[] = "<a class='btn btn-block btn-xs' style='background-color: #fadbd8; color: red; font-weight: bolder; ' onclick='ActivarEliminarPaciente($object->rowid,0)'><i class='fa fa-mouse-pointer'></i> Desactivar</a>";
                    }

                    if($estado=='E') {
                        $row[] = "<a class='btn btn-block btn-xs' style=\"background-color: #D5F5E3; color: green; font-weight: bolder\" onclick='ActivarEliminarPaciente($object->rowid,1)'> <i class='fa fa-mouse-pointer'></i> Activar</a>";
                    }

                    $row['url_datosper'] = $url_datosPersonales;
                    $row['id_paciente']  = $object->rowid;
                    $data[] = $row;
                }
            }

            $output = array(
                "data"            => $data,
                "recordsTotal"    => $Total,
                "recordsFiltered" => $Total
            );

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