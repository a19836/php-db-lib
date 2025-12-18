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
include_once get_lib("db.DBDumperHandler");

$table_name = "a_product_x";
$compress_type = null;

//print dumped file and then stop script
$tmp_file_path = @$_GET["tmp_file_path"];

if ($tmp_file_path) {
	//print dumped file
	$mime_type = !empty($_GET["compression"]) ? "application/octet-stream" : "text/plain";
	
	header('Content-Type: ' . $mime_type);
	header('Content-Length: ' . filesize($tmp_file_path));
	header('Content-Disposition: attachment; filename="' . basename($tmp_file_path) . '"');
	
	readfile($tmp_file_path);
	
	unlink($tmp_file_path);
	die();
}

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
		$html = '';
		
		if (!DBDumper::isValid($DBDriver))
			$status = 'Error: The \'' . $DBDriver->getLabel() . '\' driver doesn\'t allow dumps! Please contact the sysadmin to add this feature...';
		else {
			$compress_type = isset($_POST["compress_type"]) ? $_POST["compress_type"] : null;
			$db_options = $DBDriver->getOptions();
			$dump_file_name = "dbsqldump_data." . (isset($db_options["host"]) ? $db_options["host"] : "") . (!empty($db_options["port"]) ? "-" . $db_options["port"] : "") . "-" . (isset($db_options["db_name"]) ? $db_options["db_name"] : "") . ".sql";
			
			//prepare compression
			$compression = DBDumperHandler::NONE;
			
			if ($compress_type) {
				$compression = FileCompressionFactory::getClassPrefixByType($compress_type);
				
				if ($compression) {
					$extension = FileCompressionFactory::getExtension($compression);
					$dump_file_name .= "." . $extension;
				}
			}
			
			//prepare file name
			$tmp_file_path = sys_get_temp_dir() . "/" . $dump_file_name;
			
			if (file_exists($tmp_file_path))
				unlink($tmp_file_path);
			
			//prepare dump settings
			$selected_tables = array($table_name);
			
			$dump_settings = array(
				'include-tables' => count($selected_tables) == count($tables) ? array() : $selected_tables, //leave an empty array to dump all tables, otherwise select specific tables to dump.
				'exclude-tables' => array(),
				'include-views' => array(),
				'compress' => $compression,
				'no-data' => false,  //true or false
				'reset-auto-increment' => null,
				'add-drop-database' => null,
				'add-drop-table' => null,
				'add-drop-trigger' => null,
				'add-drop-routine' => null,
				'add-drop-event' => null,
				'add-locks' => null,
				'complete-insert' => true,
				'databases' => false,
				'default-character-set' => null, //DBDumperHandler::UTF8,
				'disable-keys' => true,
				'extended-insert' => null,
				'events' => null,
				'hex-blob' => null,
				'insert-ignore' => true,
				'net_buffer_length' => DBDumperHandler::MAX_LINE_SIZE,
				'no-autocommit' => null,
				'no-create-info' => null,
				'lock-tables' => true,
				'routines' => true,
				'single-transaction' => null,
				'skip-triggers' => null,
				'skip-tz-utc' => false,
				'skip-comments' => false,
				'skip-dump-date' => null,
				'skip-definer' => null,
				'where' => null, //where sql statement with only sql conditions.
			);
			$pdo_settings = !empty($db_options["persistent"]) && empty($db_options["new_link"]) ? array(PDO::ATTR_PERSISTENT => true) : array();
			
			//dump to file
			$DBDumperHandler = new DBDumperHandler($DBDriver, $dump_settings, $pdo_settings);
			$DBDumperHandler->connect();
			$DBDumperHandler->run($tmp_file_path);
			$DBDumperHandler->disconnect();
			
			if (!file_exists($tmp_file_path))
				$status = 'Error: Dumper did not created correctly the dumped file! Please try again...';
			else {
				$status = "File dumped successfully!";
				
				//print dumped file
				$html .= "<script>window.open('?tmp_file_path=$tmp_file_path&compression=$compression')</script>";
			}
		}

		//show error
		$html .= "<h5>Dump to file</h5>";
		$html .= "<div class=\"code status one-line\"><textarea readonly>$status</textarea></div>";
		
		//drop created temp table
		if (!empty($drop_table)) {
			$sql = $DBDriver->getDropTableStatement($table_name);
			$status = $DBDriver->setSQL($sql);
			//echo "sql: $sql<br/>status: $status<br/>";die();
		}
	}
}

echo $style;

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
		The system will create a temp table, fill it with some dummy data and then dump it into a temp file, that your browser will download.<br/>
		At the end will drop/remove the temp table from your DB.<br/>
		<br/>
		The dumped file can have compression according with your selection below.
		</span>
	</div>
	
	<div style="text-align:center;">
		<input type="submit" name="dump" value="Dump to File">
		with 
		<select name="compress_type">
			<option value="">-- None --</option>
			<option value="bzip2"' . ($compress_type == "bzip2" ? " selected" : "") . '>BZip 2</option>
			<option value="gzip"' . ($compress_type == "gzip" ? " selected" : "") . '>GZip</option>
			<option value="gzipstream"' . ($compress_type == "gzipstream" ? " selected" : "") . '>GZip Stream</option>
			<option value="zip"' . ($compress_type == "zip" ? " selected" : "") . '>Zip</option>
		</select>
		compression.
	</div>
</form>';

if (!empty($html))
	echo $html;
?>
