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

$stmt = $pdo->prepare('SELECT stop_name, stop_descr
FROM initial 
GROUP BY stop_descr');
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

sort($result);

foreach ($result as $row){
	print_r($row);
	echo "<br>";
	$sql = "INSERT INTO stops (stop_name, stop_descr)
	VALUES (:stop_name, :stop_descr)";
	$stmt = $pdo->prepare($sql);
	$stmt->bindValue(':stop_name', $row['stop_name']);
	$stmt->bindValue(':stop_descr', $row['stop_descr']);
	$stmt->execute();
}
echo "done";