<?php
/* Make a copy of this template - name it database.php */ 
$server = '--your local server hostname here--';
$username = '--your local server username here--';
$password = '--your local server credentials here--';
$database = 'myxtaomy_LA_OS'; /* If you rename the database, edit the name here */

try{
	$conn = new PDO("mysql:host=$server;dbname=$database;", $username, $password);
} catch(PDOException $e){
	die( "Connection failed: " . $e->getMessage());
}