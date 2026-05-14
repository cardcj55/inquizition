<?php
	$host = "sql207.infinityfree.com";
	$dbname = "if0_41902437_quiz_app";
	$username = "if0_41902437";
	$password = "uK08TMQyHvNs";

	try {
		$pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	} catch (PDOException $e) {
		die("Database connection failed: " . $e->getMessage());
}
?>