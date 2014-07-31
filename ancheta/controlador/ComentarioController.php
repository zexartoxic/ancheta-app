<?php
require ("../modelo/ClsComentario.php");
if(trim($_POST['nombre'])==""){
	echo "No se ha enviado un nombre. <a href='../vista/index.php'>Intente de nuevo</a>";
	}else{
$NuevoComentario= new ClsComentario();
$NuevoComentario->setNombre(trim($_POST['nombre']));
$NuevoComentario->setCorreo(trim($_POST['email']));
$NuevoComentario->setComentario(trim($_POST['comentario']));
$NuevoComentario->GuardarComentario();
header("location:../vista/index.php");
}
?>