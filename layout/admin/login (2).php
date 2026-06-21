<?php
session_start();
if(isset($_SESSION['sesion_user']) == false){
  header("Location: " . $URL . '/login/index.php');
} ?>