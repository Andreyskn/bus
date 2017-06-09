<?php
try
{
	$pdo = new PDO('mysql:host=localhost;dbname=bus', 'root', '');
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$pdo->exec('SET NAMES "utf8"');
}
catch (PDOException $e)
{
	$output = 'Unable to connect to the database server.';
	include 'output.html.php';
	exit();
}

set_time_limit(1800);
$string = file_get_contents("weekdaysETA.txt");
$json = json_decode($string, true);

for ($i=0; $i < count($json); $i++) { 
	$StopDescription = $json[$i][0]['StopDescription'];
	$busStopName = $json[$i][0]['busStopName'];
	$routeNumber = $json[$i][0]['routeNumber'];
	foreach ($json[$i][0]['weekdaysETA'] as $time) {
		$sql = "INSERT INTO initial (route, stop_name, stop_descr, eta)
 		VALUES (:route, :stop_name, :stop_descr, :eta)";
 		$stmt = $pdo->prepare($sql);
 		$stmt->bindValue(':route', $routeNumber);
 		$stmt->bindValue(':stop_name', $busStopName);
 		$stmt->bindValue(':stop_descr', $StopDescription);
 		$stmt->bindValue(':eta', $time);
 		$stmt->execute();
	}
}
echo "done!";
