<?php

session_start();

include_once '../../../../../config/lib.global.php';
require_once DOL_DOCUMENT .'/application/config/main.php';

global  $db , $conf, $user, $messErr;

if(isset($_GET['ajaxSend']) || isset($_POST['ajaxSend']))
{

    $accion = GETPOST('accion');

    switch ($accion)
    {
        case 'listpagos_indepent':

            $idpaciente = GETPOST('idpaciente');

            $respuestas_pagos = list_pagos_independientes($idpaciente);

            $Output = [
                'data' => $respuestas_pagos
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

            if(ConsultarCajaUsers($user->id, false)==1){

                $respuesta = realizar_PagoPacienteIndependiente( $datosp, $idpaciente, $idplancab );
                if($respuesta == 1){

                }else{

                }
            }else{
                $respuesta = "Este usuario no tiene asociada una caja <br> <b>No puede realizar esta Operación</b>";
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

            $data = [];

            $Total          = 0;
            $start          = $_POST["start"];
            $length         = $_POST["length"];

            $group_pagos = []; //agrupo los pagos por plan de tratamiento


            $sqlP = "select cast(p.fecha as date) as fecha , p.fk_plantram , 
                        ifnull((select ifnull(c.edit_name, concat('Plan de Tratamiento #', c.numero)) from tab_plan_tratamiento_cab c where c.rowid = p.fk_plantram) , 'error plan de tratamiento no asigando consulte con soporte tecnico')as nombplan
                      from tab_pagos_independ_pacientes_cab p where p.fk_paciente = $idpaciente and p.fk_plantram <> 0 ";
            if(!empty($plan_tratam)){
                $sqlP .= " and p.fk_plantram = $plan_tratam ";
            }
            $sqlP .= " group by p.fk_plantram";
            $sqlTotal = $sqlP; //total agrupado x plan de tratamiento
            if($start || $length){
                $sqlP .= " LIMIT $start,$length;";
            }

//            print_r($sqlTotal); die();
            $Total = $db->query($sqlTotal)->rowCount();
            $resultP = $db->query($sqlP)->fetchAll(PDO::FETCH_ASSOC);

            if(count($resultP)>0)
            {

                $data = array();
                $i = 0;
                foreach ($resultP as $k => $value) {

                    $idplanCab = $value['fk_plantram'];

                    /*PLAN DE TRATAMIENTO*/
                    $rowlabel = array(); // plan de tratamineto Group
                    $rowlabel[] = "";
                    $rowlabel[] = "<b>".$value['nombplan']."</b>";
                    $rowlabel['boldPlanCab'] = 1;
                    $rowlabel['idPlantratamCab'] = $idplanCab;//id plan de tratamiento
                    $co = 0;
                    while ($co <= 7){
                        $rowlabel[] = "";
                        $co++;
                    }

                    $data[] = $rowlabel;

                    /*DETALLE X PLAN DE TRATAMIENTO PAGADO*/
                    $query = " SELECT 
                        p.rowid as idpagoCabezera, 
                        cast(p.fecha as date) fecha,
                        p.fk_paciente , 
                        ifnull((select ifnull(c.edit_name, concat('Plan de Tratamiento #', c.numero)) from tab_plan_tratamiento_cab c where c.rowid = p.fk_plantram) , 'error plan de tratamiento no asigando consulte con soporte tecnico')as nombplan,
                        p.rowid  n_pago, 
                        p.n_fact_boleta, 
                        p.monto, 
                        p.observacion, 
                        p.fk_plantram as fk_plantramCab , 
                        (select pt.descripcion from tab_tipos_pagos pt where pt.rowid = p.fk_tipopago) as mediopago
                    FROM tab_pagos_independ_pacientes_cab p where p.rowid > 0 ";

                    $query .= " and p.fk_plantram = " .$idplanCab;
                    if(!empty($formap))
                        $query .= " and p.fk_tipopago = $formap ";
                    if(!empty($plan_tratam))
                        $query .= " and p.fk_plantram = $plan_tratam ";
                    if(!empty($npago))
                        $query .= " and p.rowid like '%$npago%' ";
                    if(!empty($n_x_documento))
                        $query .= " and p.n_fact_boleta like '%$n_x_documento%' ";
                    if($idpaciente>0)
                        $query .= " and p.fk_paciente = $idpaciente";


                    $countRegistro = 0; //cuento los registro
                    $resul = $db->query($query);
                    if($resul && $resul->rowCount()>0){
                        while ($ob = $resul->fetchObject())
                        {
                            $countRegistro++;

                            $name_plantratamiento = $ob->nombplan;
                            $row = array();

                            $row[] = "";
                            $row[] = str_replace('-','/',$ob->fecha);
                            $row[] = 'P_'.str_pad($ob->idpagoCabezera, 6, "0", STR_PAD_LEFT);
                            $row[] = $ob->nombplan;
                            $row[] = $ob->mediopago;
                            $row[] = $ob->observacion;
                            $row[] = $ob->n_fact_boleta;
                            $row[] = number_format($ob->monto, 2,'.',',');
                            $row[] = "";

                            $row[] = $ob->idpagoCabezera; #id del pago cabezara
                            $row['n_boleta'] = $ob->n_fact_boleta;
                            $row['url_imprimir'] = "<a href='".DOL_HTTP."/application/system/pacientes/pacientes_admin/pagos_recibidos/export/export_pagoparticular.php?npag=$ob->n_pago&idpac=$idpaciente' target='_blank'> <i class='fa fa-print'></i> Imprimir  </a>";
                            $row['name_tratamiento'] = $name_plantratamiento;
                            $row['idPlantratamCab']  = $ob->fk_plantramCab;
                            $row['id_pagocab']  = $ob->idpagoCabezera;
                            $row['valor']  = $ob->monto;

                            $data[] = $row;
                        }
                    }

                    $i++;
                }
            }


//            print_r($Total); die();

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


        case 'list_info_formas_pagos':

            $data = array();

            $sql = "SELECT rowid ,  descripcion , observacion, system FROM tab_tipos_pagos";
            $result = $db->query($sql);
            if($result && $result->rowCount()>0){
                while ($object = $result->fetchObject()){

                    $row = array();
                    $row[] = $object->descripcion;
                    $row[] = $object->observacion; #descricopm del pago option
                    $row[] = "";
                    $row["rowid"]  = $object->rowid;
                    $row["system"] = $object->system;

                    $data[] = $row;
                }
            }
            $Output= [
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
            $querypagos = "SELECT rowid ,  descripcion , observacion FROM tab_tipos_pagos";
            $rspagos = $db->query($querypagos);

            if($rspagos && $rspagos->rowCount() > 0) {
                while ( $pag =  $rspagos->fetchObject() ) {
                    $row = array('id' => $pag->rowid , 'text' => $pag->descripcion );
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

            $respuesta = ConsultarCajaUsers($user->id, false);

//            print_r($respuesta);
            if($respuesta==1){
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


function list_pagos_independientes($idpaciente = 0)
{

    global  $db , $conf;

    $data = array();

    $sqlpagos = "SELECT 
                    cast(ct.fecha_create as date) fecha_create,       
                    ct.rowid  as idplantratamiento, 
                    -- NUMERO DE PLAN DE TRATAMIENTO
                    ifnull(ct.edit_name, concat('Plan de Tratamiento N.' , ' ', ct.numero)) as name_tratamm, 
                    -- CITAS ASOCIADAS
                    ct.fk_cita as cita  , 
                    -- TOTAL DE PRESTACIONES
                    (SELECT 
                            ifnull(round(SUM(dt.total), 2), 0) AS totalprestaciones
                        FROM
                            tab_plan_tratamiento_det dt
                        WHERE
                            dt.fk_plantratam_cab = ct.rowid
                    ) AS totalprestaciones    , 
                    -- TOTAL DE LAS PRESTACIONES REALIZADAS
                    (SELECT 
                            ifnull(round(SUM(dt.total), 2), 0) AS totalprestaciones
                        FROM
                            tab_plan_tratamiento_det dt
                        WHERE
                            dt.fk_plantratam_cab = ct.rowid and dt.estadodet = 'R'
                    ) AS totalprestaciones_realizadas ,
                    -- TOTAL PAGADO - y las que tenga saldo
                    (SELECT round(sum(pd.amount),2) saldo FROM tab_pagos_independ_pacientes_det pd where pd.fk_plantram_cab = ct.rowid and pd.fk_paciente = ct.fk_paciente) as totalpresta_pagadasSaldo         
                FROM
                tab_plan_tratamiento_cab ct where  ct.estados_tratamiento in('A', 'S')  and ct.fk_paciente = $idpaciente  ";

//    echo '<pre>';print_r($sqlpagos); die();

    $rspagos = $db->query($sqlpagos);

    if( $rspagos && $rspagos->rowCount() > 0 )
    {
        while( $objpagos = $rspagos->fetchObject() )
        {
            $row = array();

            $pay_dom = ""; 
            if(1 == 1)
            {
                $pay_dom = "<div class='form-group col-md-12 col-xs-12'> 
                                <a href='". DOL_HTTP ."/application/system/pacientes/pacientes_admin/?view=pagospaci&key=". KEY_GLOB ."&id=". tokenSecurityId($idpaciente) ."&v=paym_pay&idplantram=". $objpagos->idplantratamiento ." ' class='btn btnhover'> <img src='". DOL_HTTP ."/logos_icon/logo_default/ahorrar-dinero.png' class='img-sm img-rounded' alt=''> </a>
                            </div>";

            }

            $row[] = $pay_dom;
            $row[] = date('d/m/Y', strtotime($objpagos->fecha_create));
            $row[] = $objpagos->name_tratamm;
            $row[] = "<img  src='". DOL_HTTP. "/logos_icon/logo_default/cita-medica.ico' class='img-sm img-rounded'  > - " . (($objpagos->cita == 0) ? "No asignada" : str_pad($objpagos->cita,5,'0',STR_PAD_LEFT));
            $row[] = "<span class='' style='padding: 1px 2px; border-radius: 5px; font-weight: bolder; background-color: #66CA86'>$ $objpagos->totalprestaciones </span>  ";
            $row[] = "<span class='' style='padding: 1px 2px; border-radius: 5px; font-weight: bolder; background-color: #ffcc00'>$ $objpagos->totalprestaciones_realizadas </span>  ";

            #pago o saldo ++
            $row[] = "<span class='' style='padding: 1px 2px; border-radius: 5px; font-weight: bolder; background-color: #66CA86'>$ ". (($objpagos->totalpresta_pagadasSaldo==null) ? "0.00" : $objpagos->totalpresta_pagadasSaldo) ." </span>  ";

            $row[] = "";

            $data[] = $row;
        }
    }

    return $data;
}


function listPrestacionesApagar($idpaciente, $idplantram)
{
    global  $db, $conf;

    $data = array();

    $sql = "SELECT            
                dt.rowid iddetplantram,
                ct.rowid idcabplantram,   
                dt.fk_prestacion , 
                ct.fk_paciente as paciente,  
                dt.fk_diente as diente,           
                
                dt.estado_pay , 
                
                -- PRESTACION
                cp.descripcion prestacion ,  
                
                dt.estadodet , 
                
                IF(dt.estadodet = 'R',
                    'Realizada',
                    'Pendiente') AS estadoprestacion,
                
                -- TOTAL    
                ROUND(dt.total, 2) AS totalprestacion , 
                
                 -- ABONADO
                 (select ifnull(round(sum(pd.amount),2),0) from tab_pagos_independ_pacientes_det pd 
                      where 
                    pd.fk_plantram_cab = ct.rowid and 
                    pd.fk_plantram_det = dt.rowid
                 ) as abonado 
                 
                ,ifnull((select lb.name from tab_conf_laboratorios_clinicos lb where lb.rowid = cp.fk_laboratorio),'') as nom_laboratorio
                
            FROM
            
                tab_conf_prestaciones cp    ,
                tab_plan_tratamiento_cab ct ,
                tab_plan_tratamiento_det dt
                
            WHERE
            
                ct.rowid = dt.fk_plantratam_cab
                AND cp.rowid = dt.fk_prestacion
                
                    AND ct.fk_paciente = $idpaciente
                    
                    AND ct.rowid = $idplantram 
                    
                    -- Muestro 
                    -- PA RECAUDADO COMPLETO (SE REALIZO EL PAGO COMPLETO )
                    -- PE RECAUDADO PENDIENTE (NO HAY PAGOS)
                    -- PS ABONADO (SOLO HAY ABONADO)
                    AND dt.estado_pay IN('PE', 'PS', 'PA') 
                    order by dt.rowid desc";

//    echo '<pre>'; print_r($sql); die();
    $resul = $db->query($sql);

    if($resul && $resul->rowCount() > 0)
    {
        $i = 0;
        while($objPrest =   $resul->fetchObject() )
        {

            $row = array();

            $estadoDetPresta = "";
            $StatusPagado    = ""; #COMPRUEBO LA RECAUDAION COMPLETA

            #PA RECAUDACION COMPLETA
            if($objPrest->estado_pay == 'PA'){
                $StatusPagado = 'disabled_link3 hidden';
            }
            #R => REALIZADO
            if($objPrest->estadodet == 'R'){
                $estadoDetPresta = '<label class="label" style="background-color: #D5F5E3; color: green; font-weight: bolder;font-size: 0.8em">REALIZADO</label>';
            }

            #P => EN PROCESO
            if($objPrest->estadodet == 'P'){
                $estadoDetPresta = '<label class="label" style="background-color: #7BA5E1; color: #114DA4; font-weight: bolder;font-size: 0.8em">EN PROCESO</label>';
            }

            #A => PENDIENTE
            if($objPrest->estadodet == 'A'){
                $estadoDetPresta = '<label class="label" style="background-color: #F6E944; color: #B88B1C; font-weight: bolder;font-size: 0.8em">PENDIENTE</label>';
            }

            $apagar = '<span class="" style="padding: 1px; border-radius: 5px; font-weight: bolder; background-color: #66CA86">    
                            $ <a style="color: #333333 !important;" class="total_apagar">'. $objPrest->totalprestacion .' </a> 
                       </span>
                            ';

            $row[] = '<span class="custom-checkbox-myStyle '.$StatusPagado.' ">
                            <input type="checkbox" onchange="IngresarValorApagar($(this), \'checkebox\');" class="check_prestacion" id="checkeAllCitas-'.$i.'" data-status="'.$objPrest->estado_pay.'">
                            <label for="checkeAllCitas-'.$i.'"></label>
                      </span> ';


            $row[] = "<p class='prestaciones_det' data-idprest='$objPrest->fk_prestacion' data-iddetplantram='$objPrest->iddetplantram' data-idcabplantram='$objPrest->idcabplantram' data-status='$objPrest->estado_pay' > $objPrest->prestacion 
                            &nbsp;&nbsp;&nbsp; ".(($objPrest->diente==0)?"":"<img src='".DOL_HTTP."/logos_icon/logo_default/diente.png' width='17px' height='17px'> $objPrest->diente")." 
                            <small style='display: block; color: #2c4ea4' title='$objPrest->nom_laboratorio'> ".((!empty($objPrest->nom_laboratorio))?"<i class='fa fa-flask'></i> $objPrest->nom_laboratorio":"")."</small>".
                        ((!empty($StatusPagado))?"<small style='display: block; color: green'> <b>Recaudación Completa</b> <i class='fa fa-money'></i></small>":"").
                      "</p>";

            $val_pendiente = number_format(( $objPrest->totalprestacion - $objPrest->abonado ), 2, '.', '');

            $row[] = $apagar; //total de la prestacion del tratamiento
            $row[] = '<a style="color: #333333 !important;" class="Abonado"> '. $objPrest->abonado .' </a>'; //ABONADO
            $row[] = '<a style="color: #333333 !important;" class="Pendiente"> '. $val_pendiente .' </a>'; //PENDIENTE
            $row[] = $estadoDetPresta; #Estado prestacion
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

    $idpacgos = 0;
    $datosdet   = $datos['datos'];

    $t_pagos          = $datos['t_pagos'];
    $observacion      = !empty($datos['observ']) ? $datos['observ'] : "";
    $amoun_t          = $datos['amoun_t'];
    $nfact_boleto     = !empty($datos['nfact_boleto']) ? $datos['nfact_boleto'] : 0;

    $iddetplantram = [];
    foreach ($datosdet as $key => $idValue){
        $iddetplantram[] = $idValue['iddetplantram'];
    }


    if(count($iddetplantram)==0)
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
        return "Estas Prestaciones ya se encuentran ya se encuentran recaudadas: <br>"."<b>".(implode("<br>", $labelServicio))."</b>";
    }

//    print_r($countValid); die();

    $sql1  = " INSERT INTO `tab_pagos_independ_pacientes_cab` ( `fecha`, `fk_tipopago`, `observacion`, `monto`, n_fact_boleta, fk_plantram, fk_paciente, id_login)";
    $sql1 .= " VALUES( ";
    $sql1 .= " now() ,";
    $sql1 .= " $t_pagos ,";
    $sql1 .= " '$observacion' ,";
    $sql1 .= " $amoun_t ,";
    $sql1 .= " '$nfact_boleto',  ";
    $sql1 .= " $idplancab , ";
    $sql1 .= " $idpaciente , ";
    $sql1 .= " $user->id  ";
    $sql1 .= ")";
//    echo '<pre>';
//    print_r($sql1); die();
    $rsPagos = $db->query($sql1);

    $idpacgos = $db->lastInsertId('tab_pagos_independ_pacientes_cab');

    if($rsPagos){

        for ( $i = 0; $i <= count($datosdet) -1; $i++ )
        {
            $sql2  = " INSERT INTO `tab_pagos_independ_pacientes_det` (`feche_create`, `fk_paciente`, `fk_usuario`, `fk_plantram_cab`, `fk_plantram_det`, `fk_prestacion`, `fk_tipopago`, `amount`, fk_pago_cab)";
            $sql2 .= " VALUES(";
            $sql2 .= " now(),";
            $sql2 .= " $idpaciente,";
            $sql2 .= " $user->id,";
            $sql2 .= " ". $datosdet[$i]['idcabplantram'] .",";
            $sql2 .= " ". $datosdet[$i]['iddetplantram'] .",";
            $sql2 .= " ". $datosdet[$i]['fk_prestacion'] .",";
            $sql2 .= " $t_pagos ,";
            $sql2 .= " ". $datosdet[$i]['valorAbonar'] ." ,";
            $sql2 .= " $idpacgos ";
            $sql2 .= ")";

            $rs2 = $db->query($sql2);

            //UPDATE PAGOS tab_plan_tratamiento_det
            // PE => pago pendiente
            // PA => Pagado
            // PS => saldo

            #se realiza las transacciones ingreso o egreso de caja
            realizarTrasaccionCobrosRecaudaciones($observacion, $idplancab, $idpacgos, $datosdet[$i]['valorAbonar'], $datosdet[$i]['fk_prestacion']);
        }

        //Consulto los pagos que este y actualizo el estado si ya esta pagada o solo haya saldo
        $sql3 = "SELECT 
                 c.fk_paciente,
                 d.rowid iddetplantram ,
                 c.rowid idcabplantram ,
                 round(d.total, 2) as totalprestacion , 
                 
                 (select ifnull( round(sum(pd.amount), 2),0 ) from tab_pagos_independ_pacientes_det pd where pd.fk_paciente = $idpaciente 
                                and pd.fk_plantram_cab = c.rowid and pd.fk_plantram_det = d.rowid) as pagado ,
                 
                 if( round(d.total, 2) = (select ifnull( round(sum(pd.amount), 2),0 ) from tab_pagos_independ_pacientes_det pd where pd.fk_paciente = $idpaciente 
					and pd.fk_plantram_cab = c.rowid and pd.fk_plantram_det = d.rowid) 
					, 'pagado' , 
	
                 if((select ifnull( round(sum(pd.amount), 2),0 ) from tab_pagos_independ_pacientes_det pd where pd.fk_paciente = $idpaciente 
                            and pd.fk_plantram_cab = c.rowid and pd.fk_plantram_det = d.rowid) = 0 
                            , 'pendiente', 'saldo')		
                    ) as estado
                 
                FROM tab_plan_tratamiento_det d , tab_plan_tratamiento_cab c 
                where d.fk_plantratam_cab = c.rowid 
                and c.fk_paciente = $idpaciente
                and c.rowid = $idplancab";
        $rs3  = $db->query($sql3);
        if($rs3){

            while ( $ob3 = $rs3->fetchObject() ){

                if( $ob3->estado == 'pagado'){ //prestacion pagado

                    $sql3 = "UPDATE `tab_plan_tratamiento_det` SET `estado_pay`='PA' WHERE `rowid`= ". $ob3->iddetplantram ." and fk_plantratam_cab =  ". $ob3->idcabplantram ." ;";
                    $db->query($sql3);
                }
                if( $ob3->estado == 'saldo') //Saldo abonado
                {
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

            if( $rsPag && $rsPag->rowCount()>0){
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

            if($hay_saldo > 0)
            {
                $sqlComplePagTram = "UPDATE `tab_plan_tratamiento_cab` SET situacion = 'SALDO' , estados_tratamiento = 'S' WHERE `rowid`='$idplancab';";
                $db->query($sqlComplePagTram);
            }
        }

        return 1;

    }else{
        return 'Ocurrio un error no se guardar el pago';
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