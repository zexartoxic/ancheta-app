<?php
class ClsComentario {
	private $id;
	private $nombre;
	private $correo;
	private $comentario;
	
	public function __construct(){
		$this->nombre="";
		$this->correo="";
		$this->comentario="";
		}
	
	public function setNombre($nom){
		$this->nombre=$nom;
		}

	public function getNombre(){
		return $this->nombre;
		}
		
	public function setCorreo($correo){
		$this->correo= $correo;
		}

	public function getCorreo(){
		return $this->correo;
		}
		
	public function setComentario($com){
		$this->comentario= $com;
		}

	public function getComentario(){
		return $this->comentario;
		}
		
	public function GuardarComentario(){
		$conexion= mysql_connect("localhost", "root", "");
		mysql_select_db("comentarios", $conexion);
		mysql_query("INSERT INTO `comentarios`.`tblcomentario` (`id`, `nombre`, `email`, `comentario`) VALUES (NULL, '$this->nombre', '$this->correo', '$this->comentario');");
		}					
	}
?>