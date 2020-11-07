
<div class="form-group col-xs-12 col-md-12">
    <div class="col-xs-12" >
        <br>
            <h3><span><b>Ficheros</b></span></h3>

        <div class="col-xs-12 col-md-12 center-block" >

            <form class="form-horizontal col-sm-12 col-md-10 col-lg-7 col-xs-12 col-centered" id="formFicheros" >
                <div class="form-group">
                    <img id="iconviewblock" class="center-block" width="80px" height="80px" src="<?= DOL_HTTP .'/logos_icon/logo_default/file.png'?>" alt="">
                </div>
                <div class="form-group">

                    <br>
                    <div class="col-sm-4 col-centered">
                        <div style="width: 154.13px; " class="col-centered">
                            <input type="file" name="files[]" id="file-5" class="inputfile inputfile-4" style="display: none" multiple />
                            <label for="file-5" style="cursor: pointer;" class="col-centered">
                                <i class="fa fa-2x fa-upload "></i>&nbsp;&nbsp;
                                <span>Seleccione Archivo</span>
                            </label>
                        </div>
                        <div style="width: 154.13px; " class="col-centered">
                            <a class="btn btn-sm btn-block" id="limpiarFile"><b>Limpiar</b></a>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="" class="control-label col-sm-2 col-xs-12 col-md-2">Titulo</label>
                    <div class="col-sm-10 col-xs-12 col-md-10" >
                        <input type="text" class="form-control input-sm" placeholder="Titulo" id="ficheroTitulo" name="tituloFichero" onkeyup="FormValidationFicheroUpload()">
                    </div>
                </div>
                <div class="form-group">
                    <label for="" class="control-label col-sm-2 col-xs-12 col-md-2">Doct@r</label>
                    <div class="col-sm-10 col-xs-12 col-md-10">
                        <select name="doctor" id="doctor" class="form-control " style="width: 100%" onchange="FormValidationFicheroUpload()">
                            <option value=""></option>
                            <?php
                                $sql = "SELECT concat( nombre_doc, ' ', apellido_doc ) as doc , rowid FROM tab_odontologos";
                                $rs = $db->query($sql);
                                if($rs->rowCount() > 0)
                                { while($r = $rs->fetchObject())
                                    { print "<option value='$r->rowid'>$r->doc</option>";} }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="" class="control-label col-sm-2 col-xs-12 col-md-2">Comentario</label>
                    <div class="col-sm-10 col-xs-12 col-md-10">
                        <textarea  class="form-control" name="observacion" id="ficheroobservacion" placeholder="Ingrese un commentario"></textarea>
                    </div>
                </div>
                <div class="form-group">
                    <button  style="font-weight: bolder; color: green;" class="btn  btnhover btn-block">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>


<div class="form-group col-md-12 col-xs-12">
    <div class="col-xs-12 col-md-12">
        <div class="table-responsive">
            <table class="table" width="100%" id="table_ficheros_paciente">
                <thead>
                <tr>
                    <th width="50.33%">Nombre</th>
                    <th width="40.33%">Descripción</th>
                    <th width="20.33%">Fecha</th>
                    <th width="5%"></th>
                </tr>
                </thead>
            </table>
        </div>
    </div>

</div>