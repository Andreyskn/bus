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

$stmt = $pdo->prepare('SELECT eta
FROM initial 
GROUP BY eta');
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

sort($result);

foreach ($result as $row){
	$sql = "INSERT INTO times (time)
	VALUES (:time)";
	$stmt = $pdo->prepare($sql);
	$stmt->bindValue(':time', $row['eta']);
	$stmt->execute();
}
echo "done";