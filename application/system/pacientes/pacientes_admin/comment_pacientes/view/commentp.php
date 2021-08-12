
<?php
    accessoModule('Comentarios Administrativos');
?>


<div class="form-group col-xs-12 col-md-12">
    <div class="form-group col-md-12 col-xs-12">
        <table class="table table-hover table-condensed" id="list_Comentarios_asociados">
            <thead>
            <tr>
                <th colspan="2" style="background-color: #f4f4f4"><h3 style="font-size: 2rem ;" class="no-margin">Comentarios</h3></th>
            </tr>
            <tr>
                <th width="2%"> &nbsp;</th>
                <th width="90%"> &nbsp;</th>
            </tr>
            </thead>
        </table>
    </div>
</div>

<div class="form-group col-md-12 col-xs-12">
    <div class="input-group">
        <textarea name="texto_comment" id="texto_comment" style="resize: vertical;" cols="30" rows="5" class="form-control" placeholder="Agrega un comentario"></textarea>
        <div class="input-group-addon btn comment" id="comment" style="border-radius: 0px; margin-left: 3px">
            <i class="fa fa-comment"></i>
        </div>
        <div class="input-group-addon btn comment"  id="refresh_comment" style="border-radius: 0px; margin-left: 3px">
            <span class="fa fa-refresh btnSpinner"></span>
        </div>
    </div>
</div>



