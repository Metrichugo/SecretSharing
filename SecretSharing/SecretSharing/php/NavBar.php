<?php
echo (
  '<nav class="navbar navbar-toggleable-md navbar-light bg-faded" id="Navigation">
    <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
      aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <a class="navbar-brand" href="#">SecretSharing</a>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav mr-auto">
        <li class="nav-item active">
          <a class="nav-link" href="#">Descubir <span class="sr-only">(current)</span></a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#">Servicios</a>
        </li>
      </ul>
      <form class="form-inline my-2 my-lg-0">
        <a class="btn btn-success my-2 my-sm-0 mr-sm-2" role="button" href="./login.php">Iniciar Sesión</a>
        <a class="btn btn-secondary my-2 my-sm-0" role="submit" href="./index.php#SignUp" style="color:black">Crear Cuenta</a>
      </form>
    </div>
  </nav>');
?>
