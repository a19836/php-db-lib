<?php
/*
 * Copyright (c) 2025 Bloxtor (http://bloxtor.com) and Joao Pinto (http://jplpinto.com)
 * 
 * Multi-licensed: BSD 3-Clause | Apache 2.0 | GNU LGPL v3 | HLNC License (http://bloxtor.com/LICENSE_HLNC.md)
 * Choose one license that best fits your needs.
 *
 * Original PHP DB Lib Repo: https://github.com/a19836/php-db-lib/
 * Original Bloxtor Repo: https://github.com/a19836/bloxtor
 *
 * YOU ARE NOT AUTHORIZED TO MODIFY OR REMOVE ANY PART OF THIS NOTICE!
 */

include_once __DIR__ . "/config.php";

//TEST DRIVER CONNECTION
//Connect to DB - This is optional, because any action to the DB, will trigger the connect or connectWithoutDB methods.
//$DBDriver->connect();
//$DBDriver->connectWithoutDB();
//$DBDriver->isConnected();
//$DBDriver->ping();
//$DBDriver->createDB("test2");
//$DBDriver->selectDB("test2");

try {
	if ($password)
		$DBDriver->connectWithoutDB();
	else 
		throw new Exception("Please edit config.php file and define your DB credentials first!");
}
catch (Throwable $e) {
	$error = $e->getMessage() . (!empty($e->problem) ? "<br/>" . $e->problem : "");
}

if ($DBDriver->isConnected()) {
	//EXECUTE SQL IN DRIVER
	$sql = $DBDriver->getDBsStatement();
	$result = $DBDriver->getSQL($sql);
	//echo "<pre>";print_r($result);die();
}

//CLOSE DRIVER CONNECTION
//$DBDriver->close();

echo $style;

//SHOW RESULTS
echo "<h1>DB Connection</h1>
<p>Connect to a DB and execute a sql statement</p>";
echo '<h4>Choose a DB driver: 
	<select onChange="document.location=\'?type=\' + this.value;">';

$types = DB::getAllDriverLabelsByType();
foreach ($types as $id => $label)
	echo "<option value='$id'" . ($id == $type ? " selected" : "") . ">$label</option>";
echo "</select></h4>";

echo '<div class="note">
		<span>
		This shows how to connect to a DB and list the available DBs.
		</span>
</div>';

if (!empty($error))
	echo '<div class="error">' . $error . '</div>';
else {
	echo "<h5>Available DBs:</h5>";

	echo '<ul>';

	if (!empty($result))
		foreach ($result as $row) {
			$k = key($row);
			echo '<li>' . $row[$k] . '</li>';
		}

	echo '</ul>';
}
?>
