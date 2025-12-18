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
include_once get_lib("db.DBFileExporter");

$table_name = "a_product_x";
$export_type = "txt";
$output_type = "save"; //If output_type is "return" then return the created output. If output_type is "save" then save the output to the doc file pass as the send argument in the exportFile function. If output_type is empty or "print", then the browser will download the doc automatically and stop script right away.

//connect to DB
try {
	if ($password) {
		$DBDriver->connect();
	}
	else 
		throw new Exception("Please edit config.php file and define your DB credentials first!");
}
catch (Throwable $e) {
	$error = $e->getMessage() . (!empty($e->problem) ? "<br/>" . $e->problem : "");
}

if (!empty($error))
	$html = '<div class="error">' . $error . '</div>';
else if (!empty($_POST)) {
	//create temp table if not exists yet
	try {
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
			
			//insert some dummy records to export
			$attributes = array(
				"client_id" => 10,
				"type_id" => 2,
				"name" => "onion",
				"description" => "simple onion",
			);
			$status = $DBDriver->insertObject($table_name, $attributes);
			//echo "sql: $sql<br/>status: $status<br/>";die();
			
			$attributes["name"] = $attributes["description"] = "carrot";
			$status = $DBDriver->insertObject($table_name, $attributes);
			//echo "sql: $sql<br/>status: $status<br/>";die();
			
			$attributes["name"] = $attributes["description"] = "potato";
			$status = $DBDriver->insertObject($table_name, $attributes);
			//echo "sql: $sql<br/>status: $status<br/>";die();
		}
	}
	catch (Throwable $e) {
		$error = $e->getMessage() . (!empty($e->problem) ? "<br/>" . $e->problem : "");
	}
	
	if (!empty($error))
		$html = '<div class="error">' . $error . '</div>';
	else {
		$export_type = !empty($_POST["export_type"]) ? $_POST["export_type"] : "txt"; 
		$DBFileExporter = new DBFileExporter($DBDriver);
		$DBFileExporter->setOptions(array( //all the following options are optional:
			"export_type" => $export_type, //default is txt 
		));
		$file_path = __DIR__ . "/products_from_export.xls";
		$sql = $DBDriver->buildTableFindSQL($table_name);
		$status = true;
		
		try {
			$status = $DBFileExporter->exportFile($sql, $file_path, $output_type);
		}
		catch (Exception $e) {
			$status = false;
		}
		
		if ($status) 
			$status = "File dumped successfully from DB!";
		else {
			$errors = $DBFileExporter->getErrors();
			$status = 'Error: File not exported!';
			
			if ($errors) {
				$func = function($v) { return nl2br($v, false); };
				$status .= "\n\nErrors:\n- " . implode('\n- ', array_map($func, $errors));
			}
		}

		$file_contents = file_exists($file_path) ? file_get_contents($file_path) : 'File Does NOT exists!';
		$html = "<h5>Export '" . basename($file_path) . "' file from '$table_name' table</h5>";
		$html .= "<div class=\"code sql one-line\"><textarea readonly>$sql</textarea></div>
		<div class=\"code output\"><textarea readonly>$status\n\nFile contents:\n$file_contents</textarea></div>";
		
		//drop created temp table
		if (!empty($drop_table)) {
			$sql = $DBDriver->getDropTableStatement($table_name);
			$status = $DBDriver->setSQL($sql);
			//echo "sql: $sql<br/>status: $status<br/>";die();
		}
	}
}

echo $style;
echo '<style>
.code.sql:before {content:"sql used to export";}
</style>';

echo "<h1>DB File Exporter</h1>
<p>Export data from DB</p>";

echo '<form method="POST">	
	<h4>Choose a DB driver: 
		<select onChange="this.form.setAttribute(\'action\', \'?type=\' + this.value);">';

$types = DB::getAllDriverLabelsByType();
foreach ($types as $id => $label)
	echo "<option value='$id'" . ($id == $type ? " selected" : "") . ">$label</option>";
echo '</select>
	</h4>
	
	<div class="note">
		<span>
		The system will create a temp table, fill it with some dummy data and then export that data into the "products_from_export.xls" file.<br/>
		At the end will drop/remove the temp table from your DB.<br/>
		<br/>
		The exported file can be a tab delimiter or csv file according with your selection below.<br/>
		If you wish to download the exported file automatically, change the $output_type var to "print".
		</span>
	</div>
	
	<div style="text-align:center;">
		<select name="export_type">
			<option value="txt"' . ($export_type == "txt" ? " selected" : "") . '>Tab Delimiter</option>
			<option value="csv"' . ($export_type == "csv" ? " selected" : "") . '>CSV</option>
			<option value="xls"' . ($export_type == "xls" ? " selected" : "") . '>XLS</option>
		</select>
		<input type="submit" name="export" value="Export to File">
	</div>
</form>';

if (!empty($html))
	echo $html;
?>
