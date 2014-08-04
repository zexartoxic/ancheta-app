<?php

$conexion = mysql_connect("localhost","root","");
$bd = mysql_select_db("demos");

// aqui es para buscar
$criterio = $_GET['term'];
$consulta = "SELECT nombre FROM tbl_productos WHERE nombre LIKE '%$nombre%'";
 
$result = $conexion->query($consulta);
 
if($result->num_rows > 0){
    while($fila = $result->fetch_array()){
        $matriculas[] = $fila['nombre'];
    }
echo json_encode($matriculas);
}

?>