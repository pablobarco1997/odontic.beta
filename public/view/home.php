

<?php

    if( isset($_GET['view']))
    {

        if( $_GET['view'] == 'inicio')
        {


?>

<style>

    .searchHome{
        width: 100% !important;
        border: 0;
    }

    .searchHome:focus{
        outline: 0;
        outline: none;
    }

</style>


<div class="row">

        <div class="col-md-12 col-xs-12">
           <div class="box box-solid" id="ContentboxHomeInicio">
               <div class="box-header with-border">
                   <i class="fa fa-dashcube"></i>
               </div>

               <div class="box-body">
                   <?php  require_once DOL_DOCUMENT.'/application/system/reportes/view/vistarep_principal.php'?>
               </div>
           </div>
        </div>

 </div>

<?php

        }else{

            echo "
            <div class='row'>
                <div class='col-md-8 col-xs-8 col-centered'>
                    <h3 style='font-weight: bolder'>Ocurrio un error no se encontro la vista de inicio</h3>
                </div>                
            </div>";

        }

    }else{


        echo "
            <div class='row'>
                <div class='col-md-8 col-xs-8 col-centered'>
                    <h3 style='font-weight: bolder'>Ocurrio un error no se encontro la vista de inicio</h3>
                </div>                
            </div>";

    }

?>