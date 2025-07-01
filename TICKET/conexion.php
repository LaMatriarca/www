<?php
$mysqli = new mysqli ("localhost", "root", "", "artepan");
$mysqli->set_charset("utf8");
if($mysqli->connect_error){
    echo 'Error de conexion: ' . $mysqli->connect_error; 
    exit; 
}
