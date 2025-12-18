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
include_once get_lib("db.DBFileImporter");

$table_name = "a_product_x";

echo $style;

echo "<h1>DB File Importer</h1>
<p>Import data to DB</p>";

echo '<h4>Choose a DB driver: 
	<select onChange="document.location=\'?type=\' + this.value;">';

$types = DB::getAllDriverLabelsByType();
foreach ($types as $id => $label)
	echo "<option value='$id'" . ($id == $type ? " selected" : "") . ">$label</option>";
echo "</select></h4>";

echo '<div class="note">
		<span>
		The system will create a temp table, which will be empty. Then will import the data from "products_to_import.xls" file, listing the inserted results.<br/>
		At the end will drop/remove the temp table from your DB.
		</span>
</div>';

//create temp table if not exists yet
try {
	if ($password) {
		$DBDriver->connect();
		$tables = $DBDriver->listTables();
		
		//Create product table
		$tn = DB::getStaticTableInNamesList($tables, $table_name);
		
		if (!$tn) {
			$drop_table = true;
			$sql = $DBDriver->getCreateTableStatement(array(
				"name" => $table_name,
				"attributes" => array(
					array("name" => "product_id", "type" => "bigint", "length" => 20, "primary_key" => true, "auto_increment" => true),
					array("name" => "client_id", "type" => "bigint", "length" => 20, "unsigned" => true, "null" => true),
					array("name" => "type_id", "type" => "bigint", "length" => 20, "unsigned" => true, "null" => true),
					array("name" => "name", "type" => "varchar", "length" => 200, "default" => "", "null" => false),
					array("name" => "description", "type" => "blob", "default" => "", "null" => false)
				)
			));
			$status = $DBDriver->setSQL($sql);
			//echo "sql: $sql<br/>status: $status<br/>";die();
		}
	}
	else 
		throw new Exception("Please edit config.php file and define your DB credentials first!");
}
catch (Throwable $e) {
	$error = $e->getMessage() . (!empty($e->problem) ? "<br/>" . $e->problem : "");
}

if (!empty($error))
	echo '<div class="error">' . $error . '</div>';
else {
	$DBFileImporter = new DBFileImporter($DBDriver);
	$DBFileImporter->setOptions(array( //all the following options are optional:
		"rows_delimiter" => "\n",
		"columns_delimiter" => "\t",
		"enclosed_by" => '"',
		"ignore_rows_number" => 1,
		"insert_ignore" => true, //If this is checked, the system will not insert the repteaded records. If update_existent is true, then insert_ignore must be false
		"update_existent" => false, //If this is checked, the system will update the repteaded records. If insert_ignore is true, then update_existent must be false
	));
	$force_rows_until_the_end = false; //If this is checked, the system will not stop on the first error and try to insert into the DB all rows until the end of the file.
	$file_path = __DIR__ . "/products_to_import.xls";
	$columns_attributes = array("product_id", "client_id", "type_id", "name");
	
	if ($DBFileImporter->importFile($file_path, $table_name, $columns_attributes, $force_rows_until_the_end)) 
		$status = "File dumped successfully to DB!";
	else {
		$errors = $DBFileImporter->getErrors();
		$status = "Error: File not imported!";
	}

	$file_contents = file_get_contents($file_path);
	echo "<h5>Import '" . basename($file_path) . "' file to '$table_name' table</h5>";
	echo "<div class=\"code input short\"><textarea readonly>$file_contents</textarea></div>
	<div class=\"code status one-line\"><textarea readonly>$status</textarea></div>";
	
	echo "<h5>Get all records from '$table_name' table</h5>";
	$sql = $DBDriver->buildTableFindSQL($table_name);
	$objects = $DBDriver->findObjects($table_name);
	echo "<div class=\"code sql one-line\"><textarea readonly>$sql</textarea></div>
	<div class=\"code\"><textarea readonly>" . print_r($objects, true) . "</textarea></div>";
	
	echo "<br/>";
	
	//drop created temp table
	if (!empty($drop_table)) {
		$sql = $DBDriver->getDropTableStatement($table_name);
		$status = $DBDriver->setSQL($sql);
		//echo "sql: $sql<br/>status: $status<br/>";die();
	}
}
?>
