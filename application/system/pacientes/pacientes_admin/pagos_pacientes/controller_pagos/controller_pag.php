<?php

if(isset($_GET['ajaxSend']) || isset($_POST['ajaxSend']))
{
    session_start();

    include_once '../../../../../config/lib.global.php';
    require_once DOL_DOCUMENT .'/application/config/main.php';

    global  $db , $conf, $user, $messErr;

    $accion = GETPOST('accion');

    switch ($accion)
    {
        //recaudaciones
        case 'listpagos_indepent':


            $emitido        = GETPOST('emitido');
            $idpaciente     = GETPOST('idpaciente');
            $abonado        = GETPOST('abonado');
            $realizado      = GETPOST('realizado');
            $id_tratamiento = GETPOST('id_tratamiento');

            if($emitido){
                $Filtros['fechaIni'] = str_replace('/','-', explode('-', $emitido)[0]);
                $Filtros['fechaFin'] = str_replace('/','-', explode('-', $emitido)[1]);
            }

            $Filtros['emitido']         = $emitido;
            $Filtros['abonado']         = $abonado;
            $Filtros['realizado']       = $realizado;
            $Filtros['id_tratamiento']  = $id_tratamiento;


            $resultado = list_pagos_independientes($idpaciente, $Filtros);

            $Output = [
                "data"            => $resultado['data'],
                "recordsTotal"    => $resultado['total'],
                "recordsFiltered" => $resultado['total']

            ];

            echo json_encode($Output);

            break;


        case 'listaprestaciones_apagar':

            $idpaciente = GETPOST('idpaciente');
            $idplantram = GETPOST('idplantram');

            $respuestas_pagos = listPrestacionesApagar($idpaciente, $idplantram);

            $Output = [
                'data' => $respuestas_pagos
            ];

            echo json_encode($Output);
            break;


        case 'realizar_pago_independiente':


            if(!PermitsModule('Recaudaciones', 'agregar')){
                $permits = false;
            }else{
                $permits = true;
            }

            $datos           = GETPOST('datos');
            $tipo_pago       = GETPOST('tipo_pago');
            $n_fact_bolet    = GETPOST('n_fact_bolet');
            $amount_total    = GETPOST('amount_total');
            $observa         = GETPOST('observ');
            $idpaciente      = GETPOST('idpaciente');
            $idplancab       = GETPOST('idplancab');

            $datosp['datos']        = $datos;
            $datosp['t_pagos']      = $tipo_pago;
            $datosp['nfact_boleto'] = $n_fact_bolet;
            $datosp['amoun_t']      = $amount_total;
            $datosp['observ']       = $observa;

            //se valida la caja asociado a un usuario
            //tiene qeu estar en estado Abierto no
            // C cerrada
            // E eliminada

            if($permits==true){
                $consultar_caja_usuario = ConsultarCajaUsers($user->id);
                if($consultar_caja_usuario['error']==""){
                    $respuesta = realizar_PagoPacienteIndependiente( $datosp, $idpaciente, $idplancab );
                    if($respuesta == 1){
                    }else{ }
                }else{
                    $respuesta = "Este usuario no tiene asociada una Caja Clinica<br> <b>No puede realizar esta Operación</b> <br> ".$consultar_caja_usuario['error'];
                }
            }else{
                $respuesta = "Ud. No tiene permisos para realizar esta Operación";
            }

            $Output = [
                'error' => $respuesta
            ];

            echo json_encode($Output);
            break;


        case 'list_pagos_particular':

            $idpaciente     = GETPOST('idpaciente');
            $formap         = GETPOST('formapago');
            $npago          = GETPOST('npago');
            $plan_tratam    = GETPOST('plan_tratam');
            $n_x_documento  = GETPOST('n_x_documento');
            $emitido        = GETPOST('emitido');

            $data = [];

            $Total          = 0;
            $start          = $_POST["start"];
            $length         = $_POST["length"];

            $group_pagos = []; //agrupo los pagos por plan de tratamiento

            $sql_a = "SELECT
                        CAST(p.fecha AS DATE) AS fecha,
                        p.fk_plantram,
                        CONCAT('Plan de Tratamiento N.', c.numero) AS nombplan
                    FROM
                        tab_pagos_independ_pacientes_cab p
                            INNER JOIN
                        (SELECT SUM(ROUND(pd.amount, 2)) AS monto, pd.fk_plantram_cab, pd.fk_plantram_det, pd.fk_pago_cab AS idpago FROM tab_pagos_independ_pacientes_det AS pd WHERE pd.fk_paciente = $idpaciente and pd.estado = 'A' GROUP BY pd.fk_pago_cab) AS pd ON pd.idpago = p.rowid
                            INNER JOIN 
                        tab_plan_tratamiento_cab c on c.rowid = p.fk_plantram and  c.fk_paciente = $idpaciente 
                      where 
                        p.fk_paciente = $idpaciente and p.fk_plantram <> 0 ";

            if(!empty($plan_tratam)){
                $sql_a .= "  and p.fk_plantram = $plan_tratam ";
            }
            if(!empty($formap)){
                $sql_a .= " and p.fk_tipopago = $formap ";
            }
            if(!empty($npago)){
                $sql_a .= " and p.rowid like '%$npago%' ";
            }
            if(!empty($n_x_documento)){
                $sql_a .= " and p.n_fact_boleta like '%$n_x_documento%' ";
            }
            if($idpaciente>0){
                $sql_a .= " and p.fk_paciente = $idpaciente";
            }
            if(!empty($emitido)){
                $dateff0 = explode('-', $emitido)[0];
                $dateff1 = explode('-', $emitido)[1];

                $dateff0 = date('Y-m-d', strtotime($dateff0));
                $dateff1 = date('Y-m-d', strtotime($dateff1));
                $sql_a  .= " and CAST(p.fecha AS DATE) between '$dateff0' and '$dateff1' ";
            }
            $sql_a .= " group by p.fk_plantram ";
            $sqlTotal = $sql_a; //total agrupado x plan de tratamiento
            if($start || $length){
                $sql_a .= " order by c.rowid desc LIMIT $start,$length;";
            }

            //print_r($sql_a); die();
            $Total = $db->query($sqlTotal)->rowCount();
            $resultP = $db->query($sql_a)->fetchAll(PDO::FETCH_ASSOC);

            if(count($resultP)>0){

                $dataTratamientos  = array();
                $data       = array();

                foreach ($resultP as $k => $value) {
                    $cabezera = [];
                    $i = 0;
                    $idplanCab = $value['fk_plantram'];

                    /*PLAN DE TRATAMIENTO*/
                    $row = array(); // plan de tratamineto Group
                    $row[] = "<b>".$value['nombplan']."</b>";
                    $row['boldPlanCab']     = 1;
                    $row['idPlantratamCab'] = $idplanCab;//id plan de tratamiento
                    $index = 0;
                    while ($index <= 6){
                            $row[] = "";
                        $index++;
                    }

                    $cabezera[] = $row; //cabezera

                    /*DETALLE X PLAN DE TRATAMIENTO PAGADO POR EL PACIENTE*/
                    $query = " SELECT 
                        p.rowid as idpagoCabezera, 
                        cast(p.fecha as date) fecha,
                        p.fk_paciente , 
                        (select concat('Plan de Tratamiento N.', c.numero) from tab_plan_tratamiento_cab c where c.rowid = p.fk_plantram) as nombplan,
                        p.rowid  n_pago, 
                        p.n_fact_boleta, 
                        pd.monto, 
                        p.observacion, 
                        p.fk_plantram as fk_plantramCab , 
                        (select bt.nom from tab_bank_operacion bt where bt.rowid = p.fk_tipopago) as mediopago
                    FROM 
                      tab_pagos_independ_pacientes_cab p 
                        inner join 
                      (SELECT SUM(round(pd.amount, 2)) as monto , pd.fk_plantram_cab, pd.fk_plantram_det, pd.fk_pago_cab as idpago FROM tab_pagos_independ_pacientes_det AS pd where pd.estado = 'A' group by pd.fk_pago_cab) AS pd ON pd.idpago = p.rowid
                    where p.rowid > 0 ";

                    $query     .= " and p.fk_plantram = ".$idplanCab;
                    if(!empty($formap)){
                        $query .= " and p.fk_tipopago = $formap ";
                    }
                    if(!empty($plan_tratam)){
                        $query .= " and p.fk_plantram = $plan_tratam ";
                    }
                    if(!empty($npago)){
                        $query .= " and p.rowid like '%$npago%' ";
                    }
                    if(!empty($n_x_documento)){
                        $query .= " and p.n_fact_boleta like '%$n_x_documento%' ";
                    }
                    if($idpaciente>0){
                        $query .= " and p.fk_paciente = $idpaciente";
                    }
                    if(!empty($emitido)){
                        $dateff0 = explode('-', $emitido)[0];
                        $dateff1 = explode('-', $emitido)[1];

                        $dateff0 = date('Y-m-d', strtotime($dateff0));
                        $dateff1 = date('Y-m-d', strtotime($dateff1));
                        $query .= " and CAST(p.fecha AS DATE) between '$dateff0' and '$dateff1' ";
                    }


                    $detalle = [];
                    $resul = $db->query($query);
                    if($resul && $resul->rowCount()>0){
                        while ($ob = $resul->fetchObject()){

                            $trantamiento = "<span class='text-sm text-blue' style='display: block'>".$ob->nombplan."</span>";
                            $key = 'P_'.str_pad($ob->idpagoCabezera, 6, "0", STR_PAD_LEFT);
                            $name_plantratamiento = $ob->nombplan;

                            $row = array();

                            $row[] = str_replace('-','/',$ob->fecha);
                            $row[] = $key."".$trantamiento;
                            $row[] = $ob->mediopago;
                            $row[] = "<small class='text-blue' style='display: block' title='".$ob->observacion."'>".$ob->observacion."</small>";
                            $row[] = $ob->n_fact_boleta;
                            $row[] = number_format($ob->monto, 2,'.',',');
                            $row[] = "";

                            $row[] = $ob->idpagoCabezera; #id del pago cabezara
                            $row['n_boleta'] = $ob->n_fact_boleta;
                            $row['url_imprimir'] = "<a href='".DOL_HTTP."/application/system/pacientes/pacientes_admin/pagos_recibidos/export/export_pagoparticular.php?npag=$ob->n_pago&idpac=$idpaciente' target='_blank'>Imprimir PDF</a>";
                            $row['name_tratamiento'] = $name_plantratamiento;
                            $row['idPlantratamCab']  = $ob->fk_plantramCab;
                            $row['id_pagocab']  = $ob->idpagoCabezera;
                            $row['valor']  = $ob->monto;

                            $detalle[] = $row;
                        }

                        $i++;
                    }

                    if(count($detalle)>0){
                        foreach ($cabezera as $valor){
                            $data[] = $valor;
                            foreach ($detalle as $itemdet){
                                $data[] = $itemdet;
                            }
                        }
                    }
                }
            }

            $Output = [
                "data" => $data,
                "recordsTotal"    => $Total,
                "recordsFiltered" => $Total

            ];

            echo json_encode($Output);
            break;

        case 'detalle_pagos_particular':

            $idpaciente   = GETPOST('idpaciente');
            $idpagos      = GETPOST('idpago');

            $data = array();

            $querydetallePagos = "SELECT 
                            (select c.descripcion from tab_conf_prestaciones c where c.rowid = d.fk_prestacion) as prestacion,
                            (select ifnull(dt.fk_diente, '') from tab_plan_tratamiento_det dt where dt.rowid = d.fk_plantram_det) as diente , 
                            d.amount 
                            FROM tab_pagos_independ_pacientes_det d 
                            where d.fk_paciente = $idpaciente and d.fk_pago_cab = $idpagos;";
//            print_r($querydetallePagos); die();
            $rs =  $db->query($querydetallePagos);
            if($rs && $rs->rowCount()>0){
                while($dp = $rs->fetchObject())
                {
                    $row = array();

                    $row[] = $dp->prestacion." &nbsp;&nbsp;&nbsp; ". (($dp->diente==0) ? "" : " <img src='".DOL_HTTP."/logos_icon/logo_default/diente.png' width='17px' height='17px' > ".$dp->diente );
                    $row[] = number_format($dp->amount,2,'.','');
                    $data[] = $row;
                }
            }

            $Output = [
                'data' => $data
            ];

            echo json_encode($Output);
            break;


            //forma de pagos update nuevo eliminar
        case 'fetchUpdateFormaPagos':

            $error = "";

            $sub        = GETPOST("subaccion");
            $nameForm   = GETPOST("name_formap");
            $textForm   = GETPOST("text_formap");
            $idtypepago = GETPOST("idformapago");

            if($sub=="nuevo"){
                $sqlinsert = "INSERT INTO `tab_tipos_pagos` (`fecha_create`,`descripcion`, `observacion`, `system`) VALUES (now(),'$nameForm', '$textForm', 0);";
                $resul = $db->query($sqlinsert);
                if(!$resul){
                    $error = "Ocurrio un error con la Operación <small><b>nuevo Pago</b></small>>";
                }
            }
            if($sub=="update"){
                if(!empty($idtypepago) && $idtypepago!=0){
                    $sqlUpdate = "UPDATE tab_tipos_pagos SET `descripcion`='$nameForm', `observacion`='$textForm' WHERE `rowid`= $idtypepago;";
                    $db->query($sqlUpdate);
                }
            }
            if($sub=="delete"){

            }

            $Output= [
                'error' => $error ,
                'sub'   => $sub,
            ];
            echo json_encode($Output);
            break;

        case 'fetchTiposPagos':

            $object = [];
            $type = "SELECT rowid ,  nom FROM tab_bank_operacion where  rowid not in(1,2,3,4,7)";
            $result = $db->query($type);

            if($result && $result->rowCount() > 0) {
                while ( $bank =  $result->fetchObject() ) {
                    $row = array('id' => $bank->rowid , 'text' => $bank->nom );
                    $object[] = $row;
                }
            }

            $Output= [
                'object' => $object ,
            ];
            echo json_encode($Output);
            break;

        case 'deletePagoPlantram':

            $error = '';
            $idpagos        = GETPOST("idpagocab");
            $idpaciente     = GETPOST("idpaciente");
            $idPlantratmCab = GETPOST("idPlantratam");
            $numeroPlantramiento = GETPOST("numTratamiento");
            $valor = GETPOST("valor");
            $idCajaAcount = ConsultarCajaUsers($user->id, true)->id_caja_account;

            //tiene caja asociada
            if( ConsultarCajaUsers($user->id, false) == 1){

                $saldo = $db->query("select sum(value) as value from tab_bank_transacciones  where id_account = $idCajaAcount")->fetchObject()->value;
                if((double)$valor <= (double)$saldo){
                    if($idPlantratmCab){

                        $Estado_tratam = $db->query("select estados_tratamiento from tab_plan_tratamiento_cab where rowid = $idPlantratmCab")->fetchObject()->estados_tratamiento;
                        $objectPago = $db->query("select * from tab_pagos_independ_pacientes_cab where rowid = $idpagos")->fetchObject();

                        //Estado F  == Plan de Tratamiento Finalizado
                        if($Estado_tratam!='F'){
                            $error = UpdatePagosParticular($idpagos, $idpaciente, $idPlantratmCab);
                            if($error==""){
                                //realiza un egreso de caja
                                trasacionEgresoDeletePago($idpagos, $objectPago, $numeroPlantramiento, $valor);
                            }else{
                                $error = $messErr." <br>"."error de transacción ( egreso )";
                            }
                        }
                        if($Estado_tratam=='F'){
                            $error = 'No puede eliminar el Pago <br> <b>El plan de tratamiento se encuentra en  estado Finalizado</b>';
                        }
                    }
                }else{
                    $error = 'Saldo insuficiente de Caja';
                }

            }else{
                $error = 'Ud. No puede realizar esta Operación no tiene caja asociada';
            }

            $Output= [
                'error' => $error ,
            ];
            echo json_encode($Output);
            break;

        case 'consulCajaUsuario':

            $error = "";

            $respuesta = ConsultarCajaUsers($user->id);

//            print_r($respuesta);
            if($respuesta['error']==""){
                $error = "";
            }else{
                $error = "Ud. No tiene asociado una caja";
            }

            $Output= [
                'error' => $error ,
            ];
            echo json_encode($Output);
            break;

        case 'deleteTipoPago':

            $error = "";

            $idpa = GETPOST("idpago");
            $result = $db->query("select count(*) as count from tab_pagos_independ_pacientes_cab where fk_tipopago = $idpa")->fetchObject()->count;
            if($result>0){
                $error = "No puede eliminar el pago registro asociado";
            }else{

                $system = $db->query("select count(*) as count from tab_tipos_pagos where rowid = $idpa and system = 1")->fetchObject()->count;
                if($system==0){
                    $db->query("DELETE FROM `tab_tipos_pagos` WHERE `rowid`= $idpa;");
                }else{
                    $error = "Ud. no puede eliminar este registro";
                }
            }

            $Output= [
                'error' => $error ,
            ];
            echo json_encode($Output);
            break;

    }

}


function list_pagos_independientes($idpaciente = 0, $Filtros = array())
{

    global  $db , $conf;


    if(!PermitsModule('Recaudaciones', 'consultar')){
        $consultar = " and 1<>1 ";
    }else{
        $consultar = " and 1=1 ";
    }

    $data = array();

    $Total = 0;
    $start          = GETPOST("start");
    $length         = GETPOST("length");

    $sql_a = "SELECT 
                    cast(ct.fecha_create as date) fecha_create,       
                    ct.rowid  as idplantratamiento, 
                    concat('Plan de Tratamiento N.' , ' ', ct.numero) as name_tratamm, 
                    ct.edit_name , 
                    ct.fk_cita as cita  , 
                    (SELECT ifnull(round(SUM(dt.total), 2), 0) AS totalprestaciones FROM tab_plan_tratamiento_det dt WHERE dt.fk_plantratam_cab = ct.rowid) AS total , -- total prestaciones servicios
                    (SELECT ifnull(round(SUM(dt.total), 2), 0) AS totalprestaciones FROM tab_plan_tratamiento_det dt WHERE dt.fk_plantratam_cab = ct.rowid and dt.estadodet = 'R') AS total_r , -- total realizados
                    (SELECT round(sum(pd.amount),2) saldo FROM tab_pagos_independ_pacientes_det pd where pd.fk_plantram_cab = ct.rowid and pd.fk_paciente = ct.fk_paciente) as total_sp  -- total abonados y pagadas       
                FROM
                tab_plan_tratamiento_cab ct where  ct.estados_tratamiento in('A', 'S')  and ct.fk_paciente =".$idpaciente;

    if(!empty($Filtros['emitido'])){
        $sql_a .= " and cast(ct.fecha_create as date) between '".$Filtros['fechaIni']."' and '".$Filtros['fechaFin']."' ";
    }
    if(!empty($Filtros['id_tratamiento'])){
        $sql_a .= " and ct.rowid = ".$Filtros['id_tratamiento'];
    }
    if( $Filtros['abonado'] == "true" ){
        $sql_a .= " and ct.estados_tratamiento = 'S' "; //cuando el plan de tratamiento tiene asociado saldo
    }
    if( $Filtros['realizado'] == "true"){
        $sql_a .= " and (select count(*) from tab_plan_tratamiento_det d where d.fk_plantratam_cab=ct.rowid and d.estadodet='R')  "; //plan de tratamiento con prestaciones realizadas
    }

    $sql_a .= $consultar;

    $sql_a .= " order by ct.rowid desc";
    $Total =  $db->query($sql_a)->rowCount();
    if($start || $length){
        $sql_a .=" LIMIT $start,$length;";
    }

    $rspagos = $db->query($sql_a);
    if( $rspagos && $rspagos->rowCount() > 0 ){
        while( $objpagos = $rspagos->fetchObject() ){

            $row = array();
            $pay_dom = ""; 
            if(1 == 1){
                $pay_dom = "<div class='form-group col-md-12 col-xs-12'> 
                                <a href='". DOL_HTTP ."/application/system/pacientes/pacientes_admin/?view=pagospaci&key=". KEY_GLOB ."&id=". tokenSecurityId($idpaciente) ."&v=paym_pay&idplantram=". $objpagos->idplantratamiento ." ' 
                                    style='color: green; background-color: #e9edf2; font-weight: bold' class='btn btnhover btn-xs '   > 
                                    cobrar
                                </a>
                            </div>";
            }

            $row[] = $pay_dom;
            $row[] = date('Y/m/d', strtotime($objpagos->fecha_create));
            $row[] = $objpagos->name_tratamm;

            $row[] = "<span class='' style='padding: 1px 2px; border-radius: 5px; font-weight: bolder; '> $objpagos->total </span>  ";  //total prestaciones servicios
            $row[] = "<span class='' style='padding: 1px 2px; border-radius: 5px; font-weight: bolder; '> $objpagos->total_r </span>  "; //total realizados

            #pago o saldo ++
            $row[] = "<span class='' style='padding: 1px 2px; border-radius: 5px; font-weight: bolder; '> ". (($objpagos->total_sp==null) ? "0.00" : $objpagos->total_sp) ." </span>  "; //total abonados y pagadas
            $row[] = "";
            $data[] = $row;

        }
    }

    $resul_b = [
       'total' => $Total,
       'data'  => $data
    ];

    return $resul_b;
}


function listPrestacionesApagar($idpaciente, $idplantram)
{
    global  $db, $conf;

    $data = array();

//    -- ESTADO
//    -- PA RECAUDADO COMPLETO (SE REALIZO EL PAGO COMPLETO )
//    -- PE RECAUDADO PENDIENTE (NO HAY PAGOS)
//    -- PS ABONADO (SOLO HAY ABONADO)

    $sql = "SELECT 
        dt.rowid iddetplantram,
        ct.rowid idcabplantram,
        dt.fk_prestacion,
        ct.fk_paciente AS paciente,
        dt.fk_diente AS diente,
        dt.estado_pay,
        cp.descripcion prestacion,
        dt.estadodet,
        IF(dt.estadodet = 'R','Realizada','Pendiente') AS estadoprestacion,
        ROUND(dt.total, 2) AS totalprestacion,
        -- (SELECT  IFNULL(ROUND(SUM(pd.amount), 2), 0) FROM tab_pagos_independ_pacientes_det pd WHERE pd.fk_plantram_cab = ct.rowid AND pd.fk_plantram_det = dt.rowid) AS abonado,
        IFNULL(ROUND(pd.abonado, 2),0)  AS abonado , 
        IFNULL((SELECT   lb.name FROM tab_conf_laboratorios_clinicos lb WHERE lb.rowid = cp.fk_laboratorio),'') AS nom_laboratorio
    FROM
    -- plan de tratamiento cabezera
        (SELECT * FROM tab_plan_tratamiento_cab AS ct where ct.rowid = $idplantram) AS ct
            INNER JOIN 
    -- plan de tratamiento detalle
        (SELECT * FROM tab_plan_tratamiento_det AS dt where dt.fk_plantratam_cab = $idplantram) AS dt ON dt.fk_plantratam_cab = ct.rowid
            LEFT JOIN 
    -- prestaciones
        (SELECT * FROM tab_conf_prestaciones AS cp) AS cp ON cp.rowid = dt.fk_prestacion 
            LEFT JOIN 
    -- pagos
        (SELECT SUM(round(pd.amount, 2)) as abonado , pd.fk_plantram_cab, pd.fk_plantram_det FROM tab_pagos_independ_pacientes_det AS pd where pd.fk_plantram_cab = $idplantram  and pd.estado = 'A' group by fk_plantram_cab, fk_plantram_det) AS pd ON pd.fk_plantram_cab = ct.rowid and pd.fk_plantram_det = dt.rowid
    WHERE
    ct.fk_paciente = $idpaciente 
      AND ct.rowid = $idplantram
        AND dt.estado_pay IN ('PE' , 'PS', 'PA')
          AND (ROUND(dt.total, 2) > 0)
ORDER BY dt.rowid DESC";
//    print_r($sql); die();
    $resul = $db->query($sql);
//    $icoPieza = "data:image/*; base64,".base64_encode(file_get_contents(DOL_DOCUMENT.'/logos_icon/logo_default/diente.png'));
    if($resul && $resul->rowCount() > 0){
        $i = 0;
        while($objPrest =   $resul->fetchObject() ){

            $row = array();

            $estadoDetPresta = "";
            $StatusPagado    = ""; #COMPRUEBO LA RECAUDAION COMPLETA

            #PA RECAUDACION COMPLETA
            if($objPrest->estado_pay == 'PA'){
                $StatusPagado = 'disabled_link3 hidden';
            }
            #R => REALIZADO
            if($objPrest->estadodet == 'R'){
                $estadoDetPresta = '<label class="label" style=";background-color: #D5F5E3; color: green; font-weight: bolder;font-size: 0.8em; " title="REALIZADO">REALIZADO</label>';
            }

            #P => EN PROCESO
            if($objPrest->estadodet == 'P'){
                $estadoDetPresta = '<label class="label" style="background-color: #7BA5E1; color: #114DA4; font-weight: bolder;font-size: 0.8em; " title="EN PROCESO">EN PROCESO</label>';
            }

            #A => PENDIENTE
            if($objPrest->estadodet == 'A'){
                $estadoDetPresta = '<label class="label" style="background-color: #F6E944; color: #B88B1C; font-weight: bolder;font-size: 0.8em; " title="PENDIENTE">PENDIENTE</label>';
            }

            $apagar = '<span class="" style="font-weight: bolder; ">    
                            <a style="color: #333333 !important;" class="total_apagar">'. $objPrest->totalprestacion .' </a> 
                       </span>
                            ';

            $row[] = '<span class="custom-checkbox-myStyle '.$StatusPagado.' ">
                            <input type="checkbox" onchange="IngresarValorApagar($(this), \'checkebox\');" class="check_prestacion" id="checkeAllCitas-'.$i.'" data-status="'.$objPrest->estado_pay.'">
                            <label for="checkeAllCitas-'.$i.'"></label>
                      </span> ';



            $row[] = "<p class='prestaciones_det' data-idprest='$objPrest->fk_prestacion' data-iddetplantram='$objPrest->iddetplantram' data-idcabplantram='$objPrest->idcabplantram' data-status='$objPrest->estado_pay' > $objPrest->prestacion 
                            &nbsp;&nbsp;&nbsp; ".(($objPrest->diente==0)?"":"<small style='display: block; ' class='text-blue' >Pieza: ".($objPrest->diente)."</small>")." 
                            <small style='display: block; color: #2c4ea4' title='$objPrest->nom_laboratorio'> ".((!empty($objPrest->nom_laboratorio))?"<i class='fa fa-flask'></i> $objPrest->nom_laboratorio":"")."</small>".
                        ((!empty($StatusPagado))?"<small style='display: block; color: green'> Recaudación Completa </small>":"").
                      "</p>";

            $val_pendiente = number_format(( $objPrest->totalprestacion - $objPrest->abonado ), 2, '.', '');

            $row[] = $apagar; //total de la prestacion del tratamiento
            $row[] = '<a style="color: #333333 !important;" class="Abonado"> '. $objPrest->abonado .' </a>'; //ABONADO
            $row[] = '<a style="color: #333333 !important;" class="Pendiente"> '. $val_pendiente .' </a>'; //PENDIENTE
            $row[] = "<div style='display: block; padding: 5px 5px; '>".$estadoDetPresta."</div> "; #Estado prestacion
            $row[] = "<input type='text' value='".(!empty($StatusPagado)?"":"0.00")."' ".((!empty($StatusPagado))?"disabled":"")." class='form-control input-sm Abonar ".$StatusPagado." ' onkeyup='moneyPagosInput($(this))'  onfocus='moneyPagosInput($(this))'  style='background-color: #f0f0f0; border-radius: 5px; font-weight: bolder; font-size: 1.3rem; color: black;'>
                      <small style='color: red; display: block' class='error_pag'></small> ";

            $data[] = $row;

            $i++;
        }
    }

    return $data;
}


function realizar_PagoPacienteIndependiente( $datos, $idpaciente, $idplancab )
{

    global  $db, $conf, $user;

    require_once DOL_DOCUMENT .'/application/system/operacion/class/Class.operacion.php';
    $operaciones = new operacion($db);


    $idpacgos         = 0;
    $datosdet         = $datos['datos'];

    $t_pagos          = $datos['t_pagos'];
    $observacion      = !empty($datos['observ']) ? $datos['observ'] : "";
    $amoun_t          = $datos['amoun_t'];
    $nfact_boleto     = !empty($datos['nfact_boleto']) ? $datos['nfact_boleto'] : 0;

    $iddetplantram = [];
    foreach ($datosdet as $key => $idValue){
        $iddetplantram[] = $idValue['iddetplantram'];
    }

    if((double)$amoun_t == 0){
        return "El monto no puede ser 0";
    }

    if(count($iddetplantram) == 0)
        return "Ocurrio un error con la Operación Recaudar: Verifique la información antes de Recaudar";


    $labelServicio = []; //nom _ servicio
    $countValid = 0; #Valido que ninguna prestacion no se encuentre en estado PA => Pagado
    $result = $db->query("SELECT d.estado_pay , cp.descripcion FROM tab_plan_tratamiento_det d, tab_conf_prestaciones cp  where cp.rowid = d.fk_prestacion and  d.rowid in(".(implode(',', $iddetplantram)).") ")->fetchAll();
    foreach ($result as $key => $valueVaid){
        if($valueVaid['estado_pay'] == 'PA'){ //PRESTACION RECAUDADA
            $labelServicio[] = $valueVaid['descripcion'];
            $countValid++;
        }
    }

    if($countValid>0){
        return "Estas Prestaciones ya se encuentran recaudadas <br>"."<b>".(implode("<br>", $labelServicio))."</b>";
    }

    //se valida todos los servicios asociados
    //se valida los pagos que el abonado no sea mayor al monto total de cada servicio
    $result_valid_pag = validar_pagos_pacientes($datosdet, $idplancab);
    if($result_valid_pag != -1){ //si es diferente a -1 el resultado contiene un mensaje de error
        return $result_valid_pag;
    }

//    die();

    $sql1  = " INSERT INTO `tab_pagos_independ_pacientes_cab` ( `fecha`, `fk_tipopago`, `observacion`, `monto`, n_fact_boleta, fk_plantram, fk_paciente, id_login)";
    $sql1 .= " VALUES( ";
    $sql1 .= " now() ,";
    $sql1 .= " $t_pagos ,";
    $sql1 .= " '$observacion' ,";
    $sql1 .= " $amoun_t ,";
    $sql1 .= " '$nfact_boleto',  ";
    $sql1 .= " $idplancab , ";
    $sql1 .= " $idpaciente , ";
    $sql1 .= " $user->id  "; //usuario author
    $sql1 .= ")";

    $rsPagos  = $db->query($sql1);
    $idpacgos = $db->lastInsertId('tab_pagos_independ_pacientes_cab');

    $fetch_caja    = ConsultarCajaUsers($user->id);
    $n_tratamiento = $db->query("select concat('',pc.numero) as n_tratamiento from tab_plan_tratamiento_cab pc where pc.rowid = $idplancab")->fetchObject()->n_tratamiento;
    $nom_paciente  = $db->query("select (select concat(d.nombre, ' ', d.apellido) from tab_admin_pacientes d where d.rowid = pc.fk_paciente)  as nom_p from tab_plan_tratamiento_cab pc where pc.rowid = $idplancab")->fetchObject()->nom_p;

    //monto de ingreso de caja
    $montoIngresoCaja = 0;

    if($rsPagos){

        $datos2 = array();
        $datos2['detalle'] = array();

        for ( $i = 0; $i <= count($datosdet) -1; $i++ ){

            $sql_b  = " INSERT INTO `tab_pagos_independ_pacientes_det` (`feche_create`, `fk_paciente`, `fk_usuario`, `fk_plantram_cab`, `fk_plantram_det`, `fk_prestacion`, `fk_tipopago`, `amount`, fk_pago_cab)";
            $sql_b .= " VALUES(";
            $sql_b .= " now(),";
            $sql_b .= " $idpaciente,";
            $sql_b .= " $user->id,";
            $sql_b .= " ". $datosdet[$i]['idcabplantram'] .",";
            $sql_b .= " ". $datosdet[$i]['iddetplantram'] .",";
            $sql_b .= " ". $datosdet[$i]['fk_prestacion'] .",";
            $sql_b .= " $t_pagos ,";
            $sql_b .= " ". $datosdet[$i]['valorAbonar'] ." ,";
            $sql_b .= " $idpacgos ";
            $sql_b .= ")";
            $result_b = $db->query($sql_b);
            //UPDATE PAGOS tab_plan_tratamiento_det
            // PE => pago pendiente
            // PA => Pagado
            // PS => saldo
            if($result_b){

                $idrecaudado_det              = $db->lastInsertId('tab_pagos_independ_pacientes_det');

                $datos['id_pago']             = $idpacgos;
                $datos['plan_tram_cab']       = $datosdet[$i]['idcabplantram'];
                $datos['plan_tram_det']       = $datosdet[$i]['iddetplantram'];
                $datos['id_paciente']         = $idpaciente;
                $datos['prestacion_servicio'] = $datosdet[$i]['fk_prestacion'];
                $datos['fk_tipo_pago']        = $t_pagos;
                $datos['amount']              = $datosdet[$i]['valorAbonar'];
                $datos['id_ope_caja_cab']     = $fetch_caja['caja']['id_caja_ope'];
                $datos['date_apertura']       = $fetch_caja['caja']['date_apertura'];
                $datos['date_cierre']         = null;
                $datos['estado']              = "A";
                $datos['n_tratamiento']       = $n_tratamiento;
                $datos['id_cobro_recaudado']  = $idrecaudado_det;

                //ingreso a  caja clinicas
                $result_id = $operaciones->new_trasaccion_caja_tratamiento($datos, $nom_paciente , $n_tratamiento, $nfact_boleto);
                if($result_id > 0){
                    $montoIngresoCaja += (double)$datosdet[$i]['valorAbonar']; //se crear para registrar un ingreso en el diario clinico

                    //ingreso de transaccion clinicas
                    //se va acumulando en un arreglo todas las transacciones de cada prestacion con su respectivo saldo
                    $datos2['detalle'][] = [
                        'datec'             => "now()",
                        'id_cuenta'         => $fetch_caja['caja']['id_cuenta'],   //cuenta caja asociada al usuario logeado
                        'id_user_author'    => $user->id ,
                        'tipo_mov'          => 1 , //ingreso
                        'amount_ingreso'    => (double)$datosdet[$i]['valorAbonar'] , //monto de ingreso a la cuenta
                        'amount_egreso'     => 0 ,
                        'value'             => (double)$datosdet[$i]['valorAbonar'],
                        'id_documento'      => $result_id, //se guarda el id retornado desde al realizar el cobro de caja
                        'tipo_documento'    => 'cajas_clinicas', //tipo de documento y/o modulo que genero esta transaccion
                        'fk_type_payment'   => $t_pagos, //medio de pago
                        'table'             => 'tab_ope_cajas_clinicas_det', //informacion opcional para saber a que table pertenece el id_documento
                        'label'             => "Cobro de Paciente ".(getnombrePaciente($datos['id_paciente'])->nom)." | Forma de pago: ".getnombFormaPago($t_pagos)." | Caja: ".$fetch_caja['caja']['name_caja']." | Plan de tratamiento N.".$n_tratamiento. " | Prestación/Servicios: ".getnombrePrestacionServicio($datosdet[$i]['fk_prestacion'])->descripcion." | Doc. ".$nfact_boleto.' | CJA_'.str_pad($fetch_caja['caja']['id_caja_ope'], 5, "0", STR_PAD_LEFT),
                    ];
                }
            }
            //se realiza las transacciones ingreso o egreso de caja
            //realizarTrasaccionCobrosRecaudaciones($observacion, $idplancab, $idpacgos, $datosdet[$i]['valorAbonar'], $datosdet[$i]['fk_prestacion']);

        }

        //se usa para llevar un control de todo lo que ingresa en la clinica (valores monetaios)
        //registro de ingreso de caja en el diario clinico
        if($montoIngresoCaja > 0){
            $datos2['label']   = "Cobro de paciente en ".getnombFormaPago($t_pagos)." | de Caja: ".strtoupper($fetch_caja['caja']['name_caja']) ." | Plan de tratamiento N.".$n_tratamiento." | Doc. ".$nfact_boleto.' | CJA_'.str_pad($fetch_caja['caja']['id_caja_ope'], 5, "0", STR_PAD_LEFT);
            $datos2['date_c']  = "now()";

            $operaciones->diarioClinico($datos2); //se registra en el diario clinico
        }

        //Consulto los pagos que este y actualizo el estado si ya esta pagada o solo haya saldo
        $sql3 = "SELECT 
                    
                    c.fk_paciente,
                    d.rowid iddetplantram ,
                    c.rowid idcabplantram ,
                    round(d.total, 2) as totalprestacion ,  
                    sum(round(ifnull(pd.amount, 0), 2)) as pagado , 
                    if(round(d.total, 2) = sum(round(ifnull(pd.amount,0), 2)), 'pagado', if(sum(round(ifnull(pd.amount,0), 2))=0, 'pendiente' , 'saldo' )) as estado
                            
                FROM 
                tab_plan_tratamiento_det as d
                    inner join 
                tab_plan_tratamiento_cab c on d.fk_plantratam_cab = c.rowid
                    left join
                tab_pagos_independ_pacientes_det pd on pd.estado = 'A' and pd.fk_paciente = $idpaciente and pd.fk_plantram_cab = c.rowid and pd.fk_plantram_det = d.rowid and pd.estado = 'A'
                
                where c.fk_paciente = $idpaciente and c.rowid = $idplancab 
                group by d.rowid desc;";

//        print_r($sql3);
        $result_a  = $db->query($sql3);
        if($result_a){
            while ( $ob3 = $result_a->fetchObject() ){
                if( $ob3->estado == 'pagado'){ //prestacion pagado
                    $sql3 = "UPDATE `tab_plan_tratamiento_det` SET `estado_pay`='PA' WHERE `rowid`= ". $ob3->iddetplantram ." and fk_plantratam_cab =  ". $ob3->idcabplantram ." ;";
                    $db->query($sql3);
                }
                if( $ob3->estado == 'saldo'){//Saldo abonado
                    $sql3 = "UPDATE `tab_plan_tratamiento_det` SET `estado_pay`='PS' WHERE `rowid`= ". $ob3->iddetplantram ." and fk_plantratam_cab =  ". $ob3->idcabplantram ." ;";
                    $db->query($sql3);
                }
            }


            #Esta variable si detecta un saldo o una prestacion que no se apagado aun entonces esta variable lo detecta
            $Apagar_plantram = 0;
            $hay_saldo       = 0;

            $sqlPagada  = "SELECT d.fk_prestacion , d.estado_pay as estado_pagado , round(d.total, 2) as total FROM tab_plan_tratamiento_cab c , tab_plan_tratamiento_det d where c.rowid = d.fk_plantratam_cab 
                              AND c.rowid = $idplancab and c.fk_paciente = $idpaciente";
            $rsPag      = $db->query($sqlPagada);

            if( $rsPag && $rsPag->rowCount()>0)
            {
                while ( $pag = $rsPag->fetchObject() )
                {
                    //pendiente
                    if( $pag->estado_pagado == 'PE' ){ //Pago pendiente no hay saldo abonado
                        $Apagar_plantram++;
                        $hay_saldo++;
                    }

                    //saldo
                    if( $pag->estado_pagado == 'PS' ){ //Saldo Abonado
                        $Apagar_plantram++;
                        $hay_saldo++;
                    }

                    //pagadas
                    if( $pag->estado_pagado == 'PA' ){ //SI una o varias Prestaciones Estan pagadas
                        $Apagar_plantram++;
                        $hay_saldo++;
                    }

                }
            }

            # PLAN DE TRATAMIENTO CABEZERA
            # A = PENDIENTE
            # N = ANULADO
            # S = SALDO

            //plan de tratamiento pagado completo
            /*if( $Apagar_plantram == 0)
            {
                $sqlComplePagTram = "UPDATE `tab_plan_tratamiento_cab` SET situacion = 'PAGADO' , estados_tratamiento = 'P' WHERE `rowid`='$idplancab';";
                $db->query($sqlComplePagTram);
            }*/

            if($hay_saldo > 0){
                $sqlComplePagTram = "UPDATE `tab_plan_tratamiento_cab` SET situacion = 'SALDO' , estados_tratamiento = 'S' WHERE `rowid`='$idplancab';";
                $db->query($sqlComplePagTram);
            }
        }

        return 1;

    }else{
        return 'Ocurrió un error Con la Operación Guardar. Consulte con soporte';
    }
}


function UpdatePagosParticular($idpagos, $idpaciente, $idPlantratmCab){

    global $db;

    $objectDeleteUpdate = array();
    $objectPrestaciones = array();

    if($idpagos==0 && $idPlantratmCab==0)
        return false;


    $sqldelet = "SELECT 
                        pd.fk_prestacion,
                        td.fk_diente,
                        td.total,
                        pd.amount,
                        pd.fk_plantram_cab,
                        pd.fk_plantram_det
                    FROM
                        tab_pagos_independ_pacientes_det pd,
                        tab_plan_tratamiento_det td
                    WHERE
                        td.rowid = pd.fk_plantram_det
                            AND pd.fk_pago_cab =".$idpagos." AND pd.fk_paciente = ".$idpaciente;
    $result = $db->query($sqldelet);
    if($result && $result->rowCount()>0){
        while ($object = $result->fetchObject()){

            $objectDeleteUpdate[] =    array(
                'fk_prestacion'     => $object->fk_prestacion ,
                'fk_diente'         => $object->fk_diente ,
                'fk_plantram_det'   => $object->fk_plantram_det ,
            );
        }
    }


    if(count($objectDeleteUpdate)== 0){
        return false;
    }


    #Delete pago asociado
    $resultdeletcab = $db->query("DELETE FROM `tab_pagos_independ_pacientes_cab` WHERE `rowid`= $idpagos and fk_plantram = $idPlantratmCab and fk_paciente = $idpaciente;");
    if($resultdeletcab){
        //elimina el detalle del pago
        $db->query("DELETE FROM `tab_pagos_independ_pacientes_det` WHERE `rowid`!= 0 and fk_pago_cab = $idpagos and fk_plantram_cab = $idPlantratmCab and fk_paciente = $idpaciente;");

    }else{
        return false;
    }

    /**
     * Estado del Pago en el Plan de Tratamiento
     * tab_plan_tratamiento_det    campo     estado_pay
     * PE => PENDIENTE
     * PS => PAGADO SALDO
     */

    /**
     * situaccion
     * se actualiza a situacion si en caso elimino todos los pagos asociado a de ese plan de tratamiento
     * se actualiza a DIAGNÓSTICO
    */

    #1 paso update a los detalle si es PS  a  PE
    if(count($objectDeleteUpdate)>0){

        foreach ($objectDeleteUpdate as $key => $value){

            #Se valida la prestacion
            $quePrestacion = "select ifnull(sum(amount),0) as saldo 
                      from tab_pagos_independ_pacientes_det pc , tab_plan_tratamiento_det pd   
                      where
                       pc.fk_plantram_det = pd.rowid
                       and pc.fk_plantram_cab = $idPlantratmCab and pc.fk_prestacion = ".$value['fk_prestacion']. " and pc.fk_paciente = ".$idpaciente ." and pd.fk_diente = ".$value['fk_diente'];
            $rsulsald = $db->query($quePrestacion);
//            echo '<pre>';  print_r($quePrestacion);
            while ($objectprest = $rsulsald->fetchObject()){
                if((double)$objectprest->saldo == 0 ){
                    #Actualizo la prestacion en estado pendiente  tab_plan_tratamiento_det
                    $db->query("UPDATE `tab_plan_tratamiento_det` SET `estado_pay`='PE' WHERE `rowid`=".$value['fk_plantram_det']." and fk_plantratam_cab =".$idPlantratmCab );
                }
            }
        }

        $valid = 0;
        #Compruebo los saldo asociado al plan de tratamiento
        #Para actualizar el plan de tratamiento a DIAGNÓSTICO
        $resultpt = $db->query("select amount as saldo_asoc from tab_pagos_independ_pacientes_det where fk_plantram_cab =".$idPlantratmCab);
        if($resultpt&&$resultpt->rowCount()>0){
            while ($obj = $resultpt->fetchObject()){
                $valid += (double)$obj->saldo_asoc;
            }
        }

        if((double)$valid == 0){
            $db->query("UPDATE `tab_plan_tratamiento_cab` SET `situacion` = 'DIAGNÓSTICO' , estados_tratamiento = 'A' WHERE `rowid`= $idPlantratmCab;");
        }
    }

    return "";
}

function realizarTrasaccionCobrosRecaudaciones($mensage = "", $idplanTratamiento, $idpacgos, $abonar, $idprestacion){

    global $db, $user, $conf;

    require_once DOL_DOCUMENT. '/application/system/cajas/class_transacciones/class_transsacion.php';

    #numero de plan de tratamiento
    $numtratm = $db->query("select concat('Plan de Tratamiento',' #',pc.numero) as trat_n from tab_plan_tratamiento_cab pc where pc.rowid = $idplanTratamiento")->fetchObject()->trat_n;
    #nombre del paciente
    $nombrePaciente = $db->query("select (select concat(d.nombre, ' ', d.apellido) from tab_admin_pacientes d where d.rowid = pc.fk_paciente)  as nom_p from tab_plan_tratamiento_cab pc where pc.rowid = $idplanTratamiento")->fetchObject()->nom_p;
    #nombre de la prestacion
    $nombprestacion = $db->query("select descripcion as name from tab_conf_prestaciones where rowid = $idprestacion")->fetchObject()->name;

    $idCajaAcount = ConsultarCajaUsers($user->id, true)->id_caja_account; // OBTENGO EL ID CUENTA DE LA CAJA

    if($idCajaAcount!=0){
        $transacciones = new transsacion($db);
        $comment = "Recaudación  del ".$numtratm. "\nPaciente: ".$nombrePaciente.""."\nprestación: ".$nombprestacion."\ncomment(".$mensage.")";

        // COBOR DE PLAN DE TRATAMIENTO HACIA CAJA
        // MOVIMIENTO DE PLAN DE TRATAMIENTO id =  2
        $transacciones->type_mov = 2;

        $transacciones->userAuthor = $user->id;

        $transacciones->type_operacion = 2; //cobros de la planes de tratamiento
        $log_tmp = "Cobro realizado del $numtratm \n prestación: $nombprestacion";
        $error = $transacciones->create_movimiento_bank($idCajaAcount,$idpacgos,$abonar,$comment,"tab_plan_tratamiento_cab", $log_tmp);
        return $error;
    }else{
        return 0;
    }


}

//se valida el pago que no sea mayor al total de la prestación
function validar_pagos_pacientes($detalle = array(), $idcab){

    global $db;

    $error_abonado      = 0;
    $id_tratam_det      = [];
    $fetch_tratam_det   = [];


    foreach ($detalle as $item){
        $id_tratam_det[$item['iddetplantram']]    = $item['iddetplantram'];
        $fetch_tratam_det[$item['iddetplantram']] = $item;
    }

    if(count($id_tratam_det)){ //no hay prestaciones

        $query = "
            select 
                detalle.rowid as id_detalle , 
                ifnull(pagos.key_tratamiento, '') as key_tratamiento , 
                detalle.fk_prestacion , 
                detalle.total as total_ttc , 
                ifnull((pagos.amount),0) as monto_pagado, 
                p.descripcion as descrip
            from
                tab_plan_tratamiento_det as detalle
                    left join 
                (select pagos.fk_plantram_det as key_tratamiento,  pagos.fk_plantram_det as id_tratam_det, pagos.fk_plantram_cab as id_tratam_cab, sum(round(pagos.amount, 2)) as amount, pagos.fk_prestacion from tab_pagos_independ_pacientes_det as pagos where pagos.estado = 'A' and pagos.fk_plantram_cab = $idcab group by pagos.fk_plantram_det, pagos.fk_plantram_cab) as pagos on pagos.id_tratam_det = detalle.rowid and pagos.id_tratam_cab = detalle.fk_plantratam_cab
                    left join
                tab_conf_prestaciones as p on p.rowid = pagos.fk_prestacion
            where 
                detalle.fk_plantratam_cab = $idcab
                and detalle.rowid in(".implode(',', $id_tratam_det).") ";

//        print_r($query); die();
        $result = $db->query($query);
        if($result){
            if($result->rowCount()>0){
                while ($object = $result->fetchObject()){

                    if(array_key_exists($object->key_tratamiento, $fetch_tratam_det)){ //si la key no existe significa que no tiene ningun pago asociado valido el id del detalle

                        $abonado = $fetch_tratam_det[$object->key_tratamiento]['valorAbonar'] += (double)$object->monto_pagado; // se suma el monto pagado junto con lo abonado

                        if($abonado > $object->total_ttc){ //si mi abonado es mayor a mi total prestacion/Servicio (error)
                            $error_abonado++;
                        }
                    }
                }

            }
        }

        if($error_abonado>0){
            return "Se ha detectado un inconveniente. El saldo abonado no puede ser mayor al total de la prestación/Servicio. Compruebe la información antes de realizar esta Operación";
        }else{
            return -1;
        }

    }

    return -1;

}





function trasacionEgresoDeletePago($idPago, $objectPago, $numeroPlantramiento, $valor){

    global $db, $user;

    require_once DOL_DOCUMENT. '/application/system/cajas/class_transacciones/class_transsacion.php';

    if($idPago && $numeroPlantramiento != ""){

        $idCajaAcount = ConsultarCajaUsers($user->id, true)->id_caja_account;

        $transacciones = new transsacion($db);

        // DELETE COBRO DEL PLAN DE TRATAMIENTO
        // MOVIMIENTO DE PLAN DE TRATAMIENTO EGRESO DE COBRO SALE DE CAJA
        $transacciones->type_mov = 3;
        $comment = "Eliminación de cobro Documento ".$objectPago->n_fact_boleta."\n"."Plan de Tratamiento: ".$numeroPlantramiento;
        $transacciones->userAuthor = $user->id;
        $transacciones->type_operacion = 3;
        $log_tmp = "Se elimina el registro del pago:"."AGR_".str_pad($idPago, 6, "0", STR_PAD_LEFT)."\n"."Plan de Tratamiento: ".$numeroPlantramiento;
        $error = $transacciones->create_movimiento_bank($idCajaAcount,$idPago,($valor*-1),$comment,"tab_pagos_independ_pacientes_cab", $log_tmp);
        return $error;

    }


}


?>