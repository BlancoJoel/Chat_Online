<?php 

session_start(); 

// Destruimos todas las variables de sesión 
session_unset(); 

// Destruimos la sesión para resetear el pedido. 
session_destroy(); 

// Redirigir al usuario a la página de inicio de sesión 
header('Location: ./login.php'); 

exit(); 