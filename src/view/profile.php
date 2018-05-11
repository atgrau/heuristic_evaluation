<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Perfil del Usuario</h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
    <div class="row">
        <form>
          <div class="form-group">
            <label for="email">Correo Electrónico:</label>
            <input type="email" class="form-control" id="email" readonly placeholder="Introduzca un Email" value="<?php echo $GLOBALS["UserSession"]->GetEmail(); ?>">
            <small id="emailHelp" class="form-text text-muted">La dirección de correo electrónico no se puede cambiar.</small>
          </div>
          <div class="form-group">
            <label for="firstname">Nombre:</label>
            <input type="text" class="form-control" id="firstname" placeholder="Nombre" value="<?php echo $GLOBALS["UserSession"]->GetFirstName(); ?>">
          </div>
          <div class="form-group">
            <label for="lastname">Apellidos:</label>
            <input type="text" class="form-control" id="lastname" placeholder="Apellidos" value="<?php echo $GLOBALS["UserSession"]->GetLastName(); ?>">
          </div>
          <label>Sexo:</label>
          <div class="form-check">
            <input class="form-check-input" type="radio" name="gender" id="cbMale" value="male" <?php if ($GLOBALS["UserSession"]->GetGender() == 0) { echo "checked"; }?>>
            <label class="form-check-label" for="cbMale">
              Masculino
            </label>
          </div>
          <div class="form-check">
            <input class="form-check-input" type="radio" name="gender" id="cbFemale" value="female" <?php if ($GLOBALS["UserSession"]->GetGender() == 1) { echo "checked"; }?>>
            <label class="form-check-label" for="cbFemale">
              Femenino
            </label>
          </div>
          <br />
          <button type="submit" class="btn btn-primary">Guardar</button>
        </form>
    </div>
</div>
<!-- /#page-wrapper -->
