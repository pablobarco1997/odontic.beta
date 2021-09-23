<?php



if(isset($_GET['ajaxSend']) || isset($_POST['ajaxSend']))
{



    session_start();

    require_once '../../../../config/lib.global.php';
    require_once DOL_DOCUMENT .'/application/config/main.php';
    require_once DOL_DOCUMENT .'/application/system/pacientes/class/class_paciente.php';
    require_once DOL_DOCUMENT .'/public/lib/PHPExcel2014/PHPExcel.php';

    $accion = GETPOST('accion');

    switch($accion)
    {
        case 'nuew_paciente':

            $paciente = new Pacientes($db);

            $datos = GETPOST('datos');

            $paciente->nombre        = $datos['nombre'];
            $paciente->apellido      = $datos['apellido'];
            $paciente->rud_dni       = $datos['rud_dni'];
            $paciente->email         = $datos['email'];
            $paciente->convenio      = $datos['convenio'];
            $paciente->n_interno     = $datos['n_interno'];
            $paciente->sexo          = $datos['sexo'];
            $paciente->fech_nacimit  = $datos['fech_nacimit'];
            $paciente->ciudad        = $datos['ciudad'];
            $paciente->comuna        = $datos['comuna'];
            $paciente->direcc        = $datos['direcc'];
            $paciente->t_fijo        = $datos['t_fijo'];
            $paciente->t_movil       = $datos['t_movil'];
            $paciente->act_profec    = $datos['act_profec'];
            $paciente->empleado      = $datos['empleado'];
            $paciente->obsrv         = $datos['obsrv'];
            $paciente->refer         = $datos['refer'];

            $res = $paciente->create_paciente();

            $output = [
                "error" => $res
            ];

            echo json_encode($output);
            break;


        case 'carga_masiva_pacientes':

            if(!PermitsModule('Pacientes', 'agregar')){
                $error['error'] = "Ud. No tiene permiso para realizar esta Operación";

                $output = [
                    "errores" => $error,
                    "req"     => "",
                ];
                echo json_encode($output);
                break;
            }

            $error = [];
            $puedoPasar = 0;
            $Fichero = $_FILES['file'];
            $invalic_excel = explode('.', $Fichero['name'])[1];

            $requerir = '';
            $error['error'] = '';

            if($invalic_excel != "xls" )
            { $puedoPasar++;  }
            if($invalic_excel != "xlsx")
            { $puedoPasar++; }

            if($puedoPasar == 1){

                $destino = "bak_fichero";
                if(!copy($Fichero['tmp_name'],$destino ))
                {
                    $error['error'] = 'Ocurrio un error al subir el fichero consulte con soporte tecnico  - (1)';
                }

                if(file_exists($destino))
                {

                    #SE OBTIENE LOS VALORES DEL FICHEROS

                    #Cargamos la hoja de calculo que se subio y copio
                    $ObjsExcel = new PHPExcel_Reader_Excel2007();
                    $ObjPHPExcel = $ObjsExcel->load($destino);

                    #Hoja de excel Activa
                    $colnm = $ObjPHPExcel->setActiveSheetIndex(0)->getHighestColumn();
                    $rows  = $ObjPHPExcel->setActiveSheetIndex(0)->getHighestRow();

                    $datapacientes = [];

                    $hay_datos_paciente = 0;
                    $ObjectPaciente = null;

                    $i = 0;
                    for ($ia = 8; $ia <= $rows ; $ia++)
                    {

                        $nombrepaciente = trim($ObjPHPExcel->getActiveSheet()->getCell('A'.$ia)->getCalculatedValue());
                        $apellipaciente = trim($ObjPHPExcel->getActiveSheet()->getCell('B'.$ia)->getCalculatedValue());
                        $cedula         = trim($ObjPHPExcel->getActiveSheet()->getCell('C'.$ia)->getCalculatedValue());

                        if($nombrepaciente != '' && $apellipaciente != '')
                        {

                            $nompaciente            = trim($ObjPHPExcel->getActiveSheet()->getCell('A'.$ia)->getCalculatedValue()); //NOMBRE
                            $Apellidpaciente        = trim($ObjPHPExcel->getActiveSheet()->getCell('B'.$ia)->getCalculatedValue()); //APELLIDO
                            $CI                     = trim($ObjPHPExcel->getActiveSheet()->getCell('C'.$ia)->getCalculatedValue()); //C.I
                            $emailpaciente          = trim($ObjPHPExcel->getActiveSheet()->getCell('D'.$ia)->getCalculatedValue()); //E-MAIL
                            $genero                 = trim($ObjPHPExcel->getActiveSheet()->getCell('E'.$ia)->getCalculatedValue()); //GENERO
                            $ciudadpaciente         = trim($ObjPHPExcel->getActiveSheet()->getCell('F'.$ia)->getCalculatedValue()); //CIUDAD
                            $direccpaciente         = trim($ObjPHPExcel->getActiveSheet()->getCell('G'.$ia)->getCalculatedValue()); //DIRECCION
                            $telef_cel              = trim($ObjPHPExcel->getActiveSheet()->getCell('H'.$ia)->getCalculatedValue()); //TELEFONO CELULAR
                            $observacion            = trim($ObjPHPExcel->getActiveSheet()->getCell('I'.$ia)->getCalculatedValue()); //OBSERVACION
                            $referencia             = trim($ObjPHPExcel->getActiveSheet()->getCell('J'.$ia)->getCalculatedValue()); //REFERENCIA

                            $ObjectPaciente[] = (object)array(
                                'nompaciente'        => $nompaciente,
                                'Apellidpaciente'    => $Apellidpaciente,
                                'ci'                 => $CI,
                                'emailpaciente'      => $emailpaciente,
                                'sexopaciente'       => $genero,
                                'direccpaciente'     => $direccpaciente,
                                'telefonopaciente'   => $telef_cel,
                                'observacion'        => $observacion,
                                'referencia'         => $referencia
                            );

                            $hay_datos_paciente++;

                            $i++;
                        }

                    }

                    unlink($destino);

                    $paciente = new Pacientes($db);
                    if($hay_datos_paciente > 0){
                        $errorCount = 0;
                        $req_ced    = 0;
                        $req_nomb   = 0;
                        $req_apell  = 0;
                        $req_sexo   = 0;
                        foreach ($ObjectPaciente as $key => $item){
                            if( $item->nompaciente == '' ){
                                $req_nomb++;
                            }
                            if( $item->Apellidpaciente == '' ){
                                $req_apell++;
                            }
                            if( $item->ci == '' ) {
                                $req_ced++;
                            }
                            #SOLO UNA PUEDE SER VERDADERO
                            if( $item->sexopaciente != 'M' && $item->sexopaciente != 'F'){
                                $req_sexo++;
                            }

                            //asigno los parametros para la insercion
                            $paciente->nombre        =    $item->nompaciente;
                            $paciente->apellido      =    $item->Apellidpaciente;
                            $paciente->rud_dni       =    $item->ci;
                            $paciente->email         =    $item->emailpaciente;
                            $paciente->sexo          =    ($item->sexopaciente == 'M') ? 'masculino' : 'femenino';
                            $paciente->direcc        =    $item->direccpaciente;
                            $paciente->t_movil       =    $item->telefonopaciente;
                            $paciente->obsrv         =    $item->observacion;
                            $paciente->refer         =    $item->referencia;

                            if( $req_nomb == 0 && $req_apell == 0 && $req_sexo == 0 && $req_ced == 0){
                                if($paciente->create_paciente() != 'exito'){
                                    $errorCount++;
                                }
                            }

                        }

                        $invalic_req = array();

                        if($req_nomb > 0){
                            $invalic_req[] = '1.- El campo nombre no puede ir vacio';
                        }
                        if( $req_apell > 0){
                            $invalic_req[] = '2.- El campo apellido no puede ir vacio';
                        }
                        if($req_ced > 0) {
                            $invalic_req[] = '3.- El campo cedula no puede ir vacio';
                        }
                        if($req_sexo > 0){
                            $invalic_req[] = '4.- No se reconoce el formato debe ingresar M => Masculino  Y  F => Femenino - debe ingresar M o F';
                        }
                        if( $req_nomb > 0 && $req_apell > 0 && $req_sexo > 0 && $req_ced > 0){
                            $invalic_req[] = '5.- Se detectaron campos vacios o mal ingresados porfavor verfique antes de cargar';
                        }
                        if($errorCount>0){
                            $invalic_req[] = '6.- Ocurrio un error con la Operacion no se lograron guardar todos los pacientes';
                        }

//                        print_r($invalic_req); die();
                        if(count($invalic_req) > 0){

                            $requerir = ''.implode('<br>', $invalic_req);
                        }

                    }else{

                        $error['error'] = '<br>Se detectaron campos vacíos - Antes de ingresar la información llene los campos ';

                    }

//                    echo '<pre>';  print_r( $invalic_req ); die();

                }else{

                    $error['error'] = '<br>Ocurrio un error al subir el fichero consulte con soporte tecnico   - (2) ';

                }
            }else{

                $error['error'] = "<br>Ocurrio un error - solo se admiten archivos excel";

            }

            #print_r($invalic_excel); die();

            $output = [
                "errores" => $error,
                "req"     => $requerir,
            ];

            echo json_encode($output);
            break;

        case 'validarCedulaRuc':

            $error = "";
            $cedruc = GETPOST('ruc_ced');

            $sql = "select count(*) rows from tab_admin_pacientes where ruc_ced = '$cedruc' ";
            $r   = $db->query($sql);
            if($r){
                $obj = $r->fetchObject();
                if($obj->rows>0){
                    $error = "Ocurrio un error numero repetido";
                }else{
                    $error = "";
                }
            }

            $output = [
                "error"  => $error,
            ];
            echo json_encode($output);
            break;
    }

}
?>