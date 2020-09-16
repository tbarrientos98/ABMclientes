<?php

if (file_exists("data.txt")) {
    $jsonClientes = file_get_contents("data.txt");
    $aClientes = json_decode($jsonClientes, true);
} else {
    $aClientes = array();
}

//pregunta si existe la variable $_GET, sino ""
$id = isset($_GET["id"]) ? $_GET["id"] : "";

//pregunta si existe la varible $_GET["do"] si existe lo borra
if (isset($_GET["id"]) && isset($_GET["do"]) && $_GET["do"] == "eliminar") {
    unset($aClientes[$id]);
    $jsonClientes = json_encode($aClientes);
    file_put_contents("data.txt", $jsonClientes);
    header("Location:index.php");
}

if ($_POST) { //postback

    $dni = $_POST["txtDni"];
    $nombre = $_POST["txtNombre"];
    $telefono = $_POST["txtTelefono"];
    $correo = $_POST["txtCorreo"];
    $nombreImagen = "";

    if ($_FILES["archivo"]["error"] === UPLOAD_ERR_OK) { //Guarda una imagen

        $nombreAleatorio = date("Ymdhmsi");
        $archivo_tmp = $_FILES["archivo"]["tmp_name"];
        $nombreArchivo = $_FILES["archivo"]["name"];
        $extension = pathinfo($nombreArchivo, PATHINFO_EXTENSION);
        $nombreImagen = $nombreAleatorio . "." . $extension;
        move_uploaded_file($archivo_tmp, "archivos/$nombreImagen");
        
    }
    
    if (isset($_GET["id"])) {
        //Si no viene ninguna imagen, conservar el nombre de la imagen anterior
       
        $imagenAnterior = $aClientes[$id]["imagen"];

        if ($_FILES["archivo"]["error"] === UPLOAD_ERR_OK) {

            if ($imagenAnterior != "") {
                unlink("archivos/$imagenAnterior");
            }
        }
        if ($_FILES["archivo"]["error"] !== UPLOAD_ERR_OK) {
            $nombreImagen = $imagenAnterior;
            //Si hay una imagen anterior eliminarla, siempre y cuando se suba una nueva imagen
        }
        
        //Actualiza
        $aClientes[$id] = array(
            "dni" => $dni,
            "nombre" => $nombre,
            "telefono" => $telefono,
            "correo" => $correo,
            "imagen" => $nombreImagen,
        );
    } else {
        //Es nuevo
        $aClientes[] = array(
            "dni" => $dni,
            "nombre" => $nombre,
            "telefono" => $telefono,
            "correo" => $correo,
            "imagen" => $nombreImagen,
        );
        
    }
    //convertir el array en json
    $jsonClientes = json_encode($aClientes);

    //guardar el json en un afile_put_contents("data.txt")
    file_put_contents("data.txt", $jsonClientes);
    $id = "";
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ABM Clientes</title>
    <link rel="stylesheet" href="css/estilos.css">
    <link rel="stylesheet" href="css/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="css/fontawesome/css/fontawesome.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
</head>

<body id="fondo" background="Fondos-10.png">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center py-4">
                <h1>Registro de clientes</h1>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-6 ">
                <form action="" method="POST" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-12 form-group">
                            <label for="txtDni">DNI:</label>
                            <input type="text" id="txtDni" name="txtDni" class="form-control" required value="<?php echo isset($aClientes[$id]) ? $aClientes[$id]["dni"] : ""; ?>">
                        </div>
                        <div class="col-12 form-group">
                            <label for="txtNombre">Nombre:</label>
                            <input type="text" id="txtNombre" name="txtNombre" class="form-control" required value="<?php echo isset($aClientes[$id]) ? $aClientes[$id]["nombre"] : ""; ?>">
                        </div>
                        <div class="col-12 form-group">
                            <label for="txtTelefono">Teléfono:</label>
                            <input type="text" id="txtTelefono" name="txtTelefono" class="form-control" required value="<?php echo isset($aClientes[$id]) ? $aClientes[$id]["telefono"] : ""; ?>">
                        </div>
                        <div class="col-12 form-group">
                            <label for="txtCorreo">Correo:</label>
                            <input type="text" id="txtCorreo" name="txtCorreo" class="form-control" required value="<?php echo isset($aClientes[$id]) ? $aClientes[$id]["correo"] : ""; ?>">
                        </div>
                        <div class="col-12 form-group">
                            <label for="txtCorreo">Archivo adjunto:</label>
                            <input type="file" id="archivo" name="archivo" class="form-control">
                        </div>
                        <div class="col-12">
                            <button type="submit" id="btnGuardar" name="btnGuardar" class="btn btn-primary">Guardar</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="col-sm-6 pt-4">
                <table class="table table-hover border">
                    <tr>
                        <th>Imagen</th>
                        <th>Dni</th>
                        <th>Nombre</th>
                        <th>Correo</th>
                        <th style="width: 150px;" class="px-4">Acciones</th>
                    </tr>
                    <?php foreach ($aClientes as $key => $cliente) :?>
                        <tr>
                            <td><img src="archivos/<?php echo $cliente["imagen"]; ?>" class="img-thumbnail"></td>
                            <td><?php echo $cliente["dni"]; ?></td>
                            <td><?php echo $cliente["nombre"]; ?></td>
                            <td><?php echo $cliente["correo"]; ?></td>
                            <td><a href="index.php?id=<?php echo $key; ?>"><i class="fas fa-edit"></i></a>
                                <a href="index.php?id=<?php echo $key; ?>&do=eliminar"><i class="fas fa-trash-alt"></i></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </table>
                <a href="index.php"><i class="fas fa-user-plus"></i></a>
            </div>
        </div>
    </div>
</body>

</html>