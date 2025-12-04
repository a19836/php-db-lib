<?php
/*
 * Copyright (c) 2025 Bloxtor (http://bloxtor.com) and Joao Pinto (http://jplpinto.com)
 * 
 * Multi-licensed: BSD 3-Clause | Apache 2.0 | GNU LGPL v3 | HLNC License (http://bloxtor.com/LICENSE_HLNC.md)
 * Choose one license that best fits your needs.
 *
 * Original PHP DB Lib Repo: https://github.com/a19836/phpdblib/
 * Original Bloxtor Repo: https://github.com/a19836/bloxtor
 *
 * YOU ARE NOT AUTHORIZED TO MODIFY OR REMOVE ANY PART OF THIS NOTICE!
 */

include_once dirname(__DIR__) . "/lib/app.php";
include_once get_lib("db.DB");

//GET DRIVER TYPE
$type = !empty($_GET["type"]) ? $_GET["type"] : "mysql"; //values: mysql or pg or mssql
$password = "";

//CREATE DRIVER OBJ
$DBDriver = DB::createDriverByType($type); //to get all available types call: DB::getAvailableDriverClassNames(); and then for each item call: DB::getDriverTypeByClassName($DBDriver_class);

//PREPARE DRIVER CREDENTIALS
//$dsn = "mysql:Server=localhost;dbname=test;";
//$options = $DBDriver->parseDSN($dsn);
//$options["username"] = "root";
//$options["password"] = "";
//or

switch ($type) {
	case "mysql":
		$options = array(
			"extension" => "mysqli", //to get all the extensions by type call: DB::getAllExtensionsByType(); 
			"host" => "localhost",
			"port" => "", //if empty, set port automatically by default
			"db_name" => "test",
			"username" => "root",
			"password" => $password,
			"persistent" => true, //create persistent connection
			"new_link" => false, //create new_link on connection
			"reconnect" => false, //reconnect if DB is disconnect automatically
			"encoding" => "utf8", //to get all the encodings by type call: DB::getAllDBConnectionEncodingsByType();
			"schema" => "", //Schema of DB - if apply
			"odbc_data_source" => "", //ODBC Data Source - only used if the extension is ODBC
			"odbc_driver" => "", //ODBC Driver - only used if the extension is ODBC and odbc_data_source is not defined
			"extra_dsn" => "", //Extra settings for the dsn - only used if the extension is ODBC or PDO
			"extra_settings" => "", //PDO settings - only used if the extension is PDO. If driver is MSSqlDB is also used if extension is sqlsrv
		); //to get the dsn based in options call: $DBDriver->getDSN($options);
		break;
	
	case "pg":
		$options = array(
			"extension" => "pg", //to get all the extensions by type call: DB::getAllExtensionsByType(); 
			"host" => "localhost",
			"port" => "", //if empty, set port automatically by default
			"db_name" => "test",
			"username" => "jplpinto",
			"password" => $password,
			"persistent" => true, //create persistent connection
			"new_link" => false, //create new_link on connection
			"reconnect" => false, //reconnect if DB is disconnect automatically
			"encoding" => "utf8", //to get all the encodings by type call: DB::getAllDBConnectionEncodingsByType();
			"schema" => "", //Schema of DB - if apply
			"odbc_data_source" => "", //ODBC Data Source - only used if the extension is ODBC
			"odbc_driver" => "", //ODBC Driver - only used if the extension is ODBC and odbc_data_source is not defined
			"extra_dsn" => "", //Extra settings for the dsn - only used if the extension is ODBC or PDO
			"extra_settings" => "", //PDO settings - only used if the extension is PDO. If driver is MSSqlDB is also used if extension is sqlsrv
		); //to get the dsn based in options call: $DBDriver->getDSN($options);
		break;
	
	case "mssql":
		$options = array(
			"extension" => "pdo", //to get all the extensions by type call: DB::getAllExtensionsByType(); 
			"host" => "localhost",
			"port" => "", //if empty, set port automatically by default
			"db_name" => "master",
			"username" => "jplpinto",
			"password" => $password,
			"persistent" => true, //create persistent connection
			"new_link" => false, //create new_link on connection
			"reconnect" => false, //reconnect if DB is disconnect automatically
			"encoding" => "utf8", //to get all the encodings by type call: DB::getAllDBConnectionEncodingsByType();
			"schema" => "", //Schema of DB - if apply
			"odbc_data_source" => "", //ODBC Data Source - only used if the extension is ODBC
			"odbc_driver" => "ODBC Driver 17 for SQL Server", //ODBC Driver - only used if the extension is ODBC and odbc_data_source is not defined
			"extra_dsn" => "TrustServerCertificate=yes;", //Extra settings for the dsn - only used if the extension is ODBC or PDO
			"extra_settings" => "", //PDO settings - only used if the extension is PDO. If driver is MSSqlDB is also used if extension is sqlsrv
		); //to get the dsn based in options call: $DBDriver->getDSN($options);
		break;
}

//SET DRIVER CREDENTIALS
$DBDriver->setOptions($options);

//SET SOME STYLING
$style = '<style>
select {background:#eee; border:1px solid #ccc; border-radius:3px; padding:3px 2px;}
h1 {margin-bottom:0; text-align:center;}
h4 {text-align:center;}
h5 {font-size:1em; margin:40px 0 0; font-weight:bold;}
p {margin:0 0 20px; text-align:center;}

.note {text-align:center;}
.note span {text-align:center; margin:0 20px 20px; padding:10px; color:#aaa; border:1px solid #ccc; background:#eee; display:inline-block; border-radius:3px;}

.error {margin:20px 0; text-align:center; color:red;}

.code {display:block; margin:10px 0; padding:0; background:#eee; border:1px solid #ccc; border-radius:3px; position:relative;}
.code:before {content:"php"; position:absolute; top:5px; left:5px; display:block; font-size:80%; opacity:.5;}
.code.sql:before {content:"sql";}
.code.status:before {content:"status";}
.code.input:before {content:"input";}
.code.output:before {content:"output";}
.code textarea {width:100%; height:300px; padding:30px 10px 10px; display:inline-block; background:transparent; border:0; resize:vertical; font-family:monospace;}
.code.short textarea {height:120px;}
.code.one-line textarea {height:60px;}
.code.sql textarea {height:200px;}
.code.sql.short textarea {height:100px;}
.code.sql.one-line textarea {height:60px;}
</style>';
?>
