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

// $stmt = $pdo->prepare('SELECT initial.route, routs.id, initial.stop_name, initial.stop_descr, stops.id, initial.eta, times.id
// FROM initial 
// INNER JOIN routs ON initial.route = routs.route
// INNER JOIN stops ON initial.stop_descr = stops.stop_descr
// INNER JOIN times ON initial.eta = times.time
// LIMIT 1000');
// $stmt->execute();
// $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
set_time_limit(1800);
$stmt = $pdo->prepare('SELECT times.id AS time_id
FROM initial 
INNER JOIN times ON initial.eta = times.time
');
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

// foreach ($result as $row){
	// $sql = "UPDATE weekdays
	// SET stop_id = :stop_id";
	// $stmt = $pdo->prepare($sql);
	// $stmt->bindValue(':stop_id', $row['stop_id']);
	// $stmt->execute();
// }
for ($i=1; $i <= count($result); $i++) { 
	$sql = "UPDATE weekdays
	SET time_id = :time_id
	WHERE id = :iteration";
	$stmt = $pdo->prepare($sql);
	$stmt->bindValue(':time_id', $result[$i-1]['time_id']);
	$stmt->bindValue(':iteration', $i);
	$stmt->execute();
}

echo 'done';