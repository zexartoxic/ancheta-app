
<?php

$conexion = mysql_connect("localhost","root","");
$bd = mysql_select_db("demos");

$producto = $_POST['producto'];
$consulta = "SELECT precio,categoria,codigo FROM tbl_productos WHERE nombre = '$producto'";
$result = $conexion->query($consulta);
 
$respuesta = new stdClass();
if($result->num_rows > 0){
    $fila = $result->fetch_array();
    $respuesta->precio = $fila['precio'];
    $respuesta->categoria = $fila['categoria'];
    $respuesta->codigo = $fila['codigo'];
}
echo json_encode($respuesta);

?>