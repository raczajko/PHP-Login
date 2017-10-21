<?php
//Variables de Conexion a la Base de Datos
$host = "localhost"; // Equipo
$username = "user"; // Usuario MySql/PostgreSQL
$password = "password"; // Password MySql/PostgreSQL
$db_name = "login"; // Nombre de la BD

//NO REALIZAR CAMBIOS DESPUES DE ESTA LINEA A MENOS QUE HAYAS CAMBIADO LOS NOMBRES DE LAS TABLAS MEMBERS Y LOGINATTEMPTS

$tbl_prefix = ""; //***PLANNED FEATURE, LEAVE VALUE BLANK FOR NOW*** Prefix for all database tables
$tbl_members = $tbl_prefix."members";
$tbl_attempts = $tbl_prefix."loginAttempts";
