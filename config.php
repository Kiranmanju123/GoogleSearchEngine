<?php

ob_start(); //output buffering 

try 
{
	$con = new PDO("mysql:dbname=kirangoogle;host=localhost","root","");
	$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);

}
catch(PDOException $e) 
{
	echo "Connection failed" . $e->getMessage();

}

?>