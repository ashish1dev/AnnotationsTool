<?php
if (!$public){
   $public = $_GET["public"]; 
}
if (!$username){
   $username = $_COOKIE["username"];
}

$TOOLHOME = "/Applications/XAMPP/htdocs/labelme/";
