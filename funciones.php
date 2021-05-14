<?php

/**
 * Función que comprueba la antiguedad del anuncio, dada la fecha. Devuelve true 
 * si el anuncio se ha publicado hace una semana o menos, false si hace mas tiempo
 * @author: Jorge M. Pardo Ramos.
 * @version: 1.0.
 * @param $fecha Fecha del anuncio
 * @return true|false
 */
function compararFechaAnuncio($fecha) {
    $fechaHaceUnaSemana = "20" . date("y-m-d");
    $fechaHaceUnaSemana = date('Y-m-d', strtotime($fechaHaceUnaSemana . ' -7 day'));
    if (intval(strtotime($fechaHaceUnaSemana)) > intval(strtotime($fecha))) {
        return false;
    }
    return true;
}

/**
 * Función que comprueba si la información para iniciar sesión existe en la base de datos.
 * @author: Jorge M. Pardo Ramos.
 * @version: 1.0.
 * @param String $login Login del usuario que debe ser igual al almacenado en la base de datos
 * @param String $password Contraseña del usuario que debe ser igual a la almacenada en la base de datos
 * @return true|false. 
 */
function comprobarInformacionInicioSesionEnBaseDeDatos($login, $password) {
    $conexionBD=new DB();

    $resultado=$conexionBD->comprobarInformacionInicioSesionEnBaseDeDatos($login, $password);
    if($resultado==-2)
        mostrarMensaje("Error", "No se ha podido conectar con la base de datos." , "danger");        
    elseif($resultado==-1)
        mostrarMensaje("Error", " Por favor, comprueba el nombre de usuario.", "danger");
    elseif($resultado==0){
        $bloquearCuenta = false;
        if (isset($_SESSION[$login . "erroresLogin"])) {
            $_SESSION[$login . "erroresLogin"] = $_SESSION[$login . "erroresLogin"] + 1;
            if ($_SESSION[$login . "erroresLogin"] >= 3) {
                $bloquearCuenta = true;
            }
        } else {
            $_SESSION[$login . "erroresLogin"] = 1;
        }
        
        if (!$bloquearCuenta) {
            mostrarMensaje("Error", "Los datos introducidos no son correctos entre sí. Llevas " . $_SESSION[$login . "erroresLogin"] . " fallo/s, al tercero tu cuenta será bloqueada.", "danger");
        } else {
            mostrarMensaje("Cuenta bloqueada", "Lo sentimos, pero tu cuenta ha sido bloqueada por fallar tres veces la contraseña.", "danger");
            $conexionBD->bloquearCuenta($login);
        }    
        
    }else{
        if (isset($_SESSION[$login . "erroresLogin"])) {
            $_SESSION[$login . "erroresLogin"] = 0;
        }
        return true;
    }
}
