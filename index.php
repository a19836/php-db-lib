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

include_once __DIR__ . "/examples/config.php";

echo $style;

echo "<h1>DB Lib</h1>
<p>Simple DB library.</p>";

echo '<h4>Choose a DB driver: 
	<select onChange="document.location=\'?type=\' + this.value;">';

$types = DB::getAllDriverLabelsByType();
foreach ($types as $id => $label)
	echo "<option value='$id'" . ($id == $type ? " selected" : "") . ">$label</option>";
echo "</select></h4>";

echo '<div class="note">
		<span>
		Learn how to use this DB library very easy.<br/>
		Currently supports Mysql, SqlServer and Postgres DBs.
		</span>
</div>';

echo '<div style="text-align:center;">
	<div style="display:inline-block; text-align:left;">
		<div>Some tutorials:</div>
		<ul>
			<li><a href="examples/db_connection.php?type=' . $type . '" target="db_connection">Learn how to connect to a BD</a></li>
			<li><a href="examples/crud_sql.php?type=' . $type . '" target="crud_sql">Learn how to create CRUD SQL statements</a></li>
			<li><a href="examples/crud_action.php?type=' . $type . '" target="crud_action">Learn how to execute CRUD actions</a></li>
			<li><a href="examples/file_importer.php?type=' . $type . '" target="file_importer">Learn how to import data from a file</a></li>
			<li><a href="examples/file_exporter.php?type=' . $type . '" target="file_exporter">Learn how to export data to a file</a></li>
			<li><a href="examples/file_dumper.php?type=' . $type . '" target="file_dumper">Learn how to dump your DB to a file</a></li>
		</ul>
	</div>
</div>';

echo '<div>
	<h5>Usage sample:</h5>
	<div class="code">
		<textarea readonly>
//Get DB Driver:
$DBDriver = DB::createDriverByType("mysql"); //mysql, pg, mssql

//Set DB credentials to connect
$DBDriver->setOptions(array(
	"host" => "",
	"db_name" => "",
	"username" => "",
	"password" => ""
));
//$DBDriver->connect(); //connect is optional since it will connect automatically when execute an action or sql statement to DB.

//create table
$sql = $DBDriver->getCreateTableStatement(array(
	"name" => "product",
	"attributes" => array(
		array("name" => "product_id", "type" => "bigint", "length" => 20, "primary_key" => true, "auto_increment" => true),
		array("name" => "name", "type" => "varchar", "length" => 200, "default" => "", "null" => false)
	)
));
$status = $DBDriver->setSQL($sql);

//insert record
$status = $DBDriver->insertObject("product", array("name" => "cocacola"));

//get records
$result = $DBDriver->findObjects("product");
//or: 
//$sql = $DBDriver->buildTableFindSQL("product");
//$result = $Driver->getSQL($sql); //you can also use: $Driver->getData($sql);
echo "<pre>" . print_r($result , true) . "</pre>";
		</textarea>
	</div>

	<h5>Some DB Driver Methods:</h5>
	<div class="code">
		<textarea readonly>
//Driver Generic Setters

$DBDriver->setOptions($options, $launch_exception = false);
$DBDriver->setData($sql, $options);
$DBDriver->setSQL($sql, $options);
$DBDriver->execute($sql, $options); 
$DBDriver->setConnectionEncoding(\'utf8\'); 
$DBDriver->selectDB($table_name);
$DBDriver->createDB($db_name, $options = false);

//Driver Generic Getters
$status = $DBDriver->connect();
$status = $DBDriver->connectWithoutDB();
$status = $DBDriver->disconnect(); 
$status = $DBDriver->close(); 
$status = $DBDriver->ping();
$status = $DBDriver->isConnected();
$status = $DBDriver->isDBSelected();
$status = $DBDriver->getSelectedDB($options);
$link = $DBDriver->getConnectionLink();
$extension = $DBDriver->getConnectionPHPExtensionType();
$options = $DBDriver->getOptions();
$option = $DBDriver->getOption($option_name);
$status = $DBDriver->areOptionsValid($options, $launch_exception);
$result = $DBDriver->getFunction($function_name, $parameters, $options);
$result = $DBDriver->getData($sql, $options);
$result = $DBDriver->getSQL($sql, $options);
$status = $DBDriver->isTheSameTableName($table_name_1, $table_name_2);
$status = $DBDriver->isTableInNamesList($tables_list, $table_to_search);
$table_name = $DBDriver->getTableInNamesList($tables_list, $table_to_search);

$dbs = $DBDriver->listDBs($options, $column_name); 
$tables = $DBDriver->listTables($db_name, $options); 
$attributes = $DBDriver->listTableFields($table, $options); 
$fks = $DBDriver->listForeignKeys($table, $options);
$table_charsets = $DBDriver->listTableCharsets();
$column_charsets = $DBDriver->listColumnCharsets();
$table_collations = $DBDriver->listTableCollations();
$column_collations = $DBDriver->listColumnCollations();
$engines = $DBDriver->listStorageEngines();
$views = $DBDriver->listViews($db_name, $options);
$triggers = $DBDriver->listTriggers($db_name, $options);
$procedures = $DBDriver->listProcedures($db_name, $options);
$functions = $DBDriver->listFunctions($db_name, $options);
$events = $DBDriver->listEvents($db_name, $options);
$id = $DBDriver->getInsertedId($options); 

//Driver DAO Methods
$status = $DBDriver->insertObject($table_name, $attributes, $options);
$status = $DBDriver->updateObject($table_name, $attributes, $conditions, $options);
$status = $DBDriver->deleteObject($table_name, $conditions, $options);
$objects = $DBDriver->findObjects($table_name, $attributes, $conditions, $options);
$total = $DBDriver->countObjects($table_name, $conditions, $options);
$objects = $DBDriver->findRelationshipObjects($table_name, $rel_elm, $parent_conditions, $options);
$total = $DBDriver->countRelationshipObjects($table_name, $rel_elm, $parent_conditions, $options);
$max = $DBDriver->findObjectsColumnMax($table_name, $attribute_name, $options);

//Driver Converters
$sql = $DBDriver->convertObjectToSQL($data, $options);
$array = $DBDriver->convertSQLToObject($sql, $options);
$sql = $DBDriver->buildTableInsertSQL($table_name, $attributes, $options);
$sql = $DBDriver->buildTableUpdateSQL($table_name, $attributes, $conditions, $options);
$sql = $DBDriver->buildTableDeleteSQL($table_name, $conditions, $options);
$sql = $DBDriver->buildTableFindSQL($table_name, $attributes, $conditions, $options);
$sql = $DBDriver->buildTableCountSQL($table_name, $conditions, $options);
$sql = $DBDriver->buildTableFindRelationshipSQL($table_name, $rel_elm, $parent_conditions, $options);
$sql = $DBDriver->buildTableCountRelationshipSQL($table_name, $rel_elm, $parent_conditions, $options);
$sql = $DBDriver->buildTableFindColumnMaxSQL($table_name, $attribute_name, $options);

//Driver Getters - Driver STATIC Methods - can be called through \'$DBDriver->\' or \'XXXDBStatic::\' (i.e: \'MySqlDBStatic::\')
$type = $DBDriver->getType();
$label = $DBDriver->getLabel();
$delimiters = $DBDriver->getEnclosingDelimiters();
$delimiters = $DBDriver->getAliasEnclosingDelimiters();
$encodings = $DBDriver->getDBConnectionEncodings();
$table_charsets = $DBDriver->getTableCharsets();
$column_charsets = $DBDriver->getColumnCharsets();
$table_collations = $DBDriver->getTableCollations();
$column_collations = $DBDriver->getColumnCollations();
$engines = $DBDriver->getStorageEngines();
$php_to_db_column_types = $DBDriver->getPHPToDBColumnTypes();
$db_to_php_column_types = $DBDriver->getDBToPHPColumnTypes();
$column_types = $DBDriver->getDBColumnTypes();
$column_simple_types = $DBDriver->getDBColumnSimpleTypes();
$default_values = $DBDriver->getDBColumnDefaultValuesByType();
$props_to_ignore_by_column_type = $DBDriver->getDBColumnTypesIgnoredProps();
$props_to_hide_by_column_type = $DBDriver->getDBColumnTypesHiddenProps();
$numeric_types = $DBDriver->getDBColumnNumericTypes();
$date_types = $DBDriver->getDBColumnDateTypes();
$text_types = $DBDriver->getDBColumnTextTypes();
$blob_types = $DBDriver->getDBColumnBlobTypes();
$boolean_types = $DBDriver->getDBColumnBooleanTypes();
$mandatory_length_by_column_type = $DBDriver->getDBColumnMandatoryLengthTypes();
$auto_increment_types = $DBDriver->getDBColumnAutoIncrementTypes();
$boolean_type_available_values = $DBDriver->getDBBooleanTypeAvailableValues();
$current_timestamp_available_values = $DBDriver->getDBCurrentTimestampAvailableValues();
$attribute_value_reserved_words = $DBDriver->getAttributeValueReservedWords();
$reserved_words = $DBDriver->getReservedWords();
$default_schema = $DBDriver->getDefaultSchema();
$ignore_connection_options = $DBDriver->getIgnoreConnectionOptions();
$ignore_connection_options_by_extension= $DBDriver->getIgnoreConnectionOptionsByExtension();
$available_php_extension_types = $DBDriver->getAvailablePHPExtensionTypes();
$status = $DBDriver->allowTableAttributeSorting();
$status = $DBDriver->allowModifyTableEncoding();
$status = $DBDriver->allowModifyTableStorageEngine();

//Driver Statements - Driver STATIC Methods - can be called through \'$DBDriver->\' or \'XXXDBStatement::\' (i.e: \'MySqlDBStatement::\')
$sql = $DBDriver->getCreateDBStatement($db_name, $options);
$sql = $DBDriver->getDropDatabaseStatement($db_name, $options);
$sql = $DBDriver->getSelectedDBStatement($options);
$sql = $DBDriver->getDBsStatement($options);
$sql = $DBDriver->getTablesStatement($db_name, $options);
$sql = $DBDriver->getTableFieldsStatement($table, $db_name, $options);
$sql = $DBDriver->getForeignKeysStatement($table, $db_name, $options);
$sql = $DBDriver->getCreateTableStatement($table_data, $options);
$sql = $DBDriver->getCreateTableAttributeStatement($attribute_data, $options, $parsed_data);
$sql = $DBDriver->getRenameTableStatement($old_table, $new_table, $options);
$sql = $DBDriver->getModifyTableEncodingStatement($table, $charset, $collation, $options);
$sql = $DBDriver->getModifyTableStorageEngineStatement($table, $engine, $options);
$sql = $DBDriver->getDropTableStatement($table, $options);
$sql = $DBDriver->getDropTableCascadeStatement($table, $options);
$sql = $DBDriver->getAddTableAttributeStatement($table, $attribute_data, $options);
$sql = $DBDriver->getModifyTableAttributeStatement($table, $attribute_data, $options);
$sql = $DBDriver->getRenameTableAttributeStatement($table, $old_attribute, $new_attribute, $options);
$sql = $DBDriver->getDropTableAttributeStatement($table, $attribute, $options);
$sql = $DBDriver->getAddTablePrimaryKeysStatement($table, $attributes, $options);
$sql = $DBDriver->getDropTablePrimaryKeysStatement($table, $options);
$sql = $DBDriver->getAddTableForeignKeyStatement($table, $fk, $options);
$sql = $DBDriver->getDropTableForeignKeysStatement($table, $options);
$sql = $DBDriver->getDropTableForeignConstraintStatement($table, $constraint_name, $options);
$sql = $DBDriver->getAddTableIndexStatement($table, $attributes, $options);
$sql = $DBDriver->getDropTableIndexStatement($table, $constraint_name, $options);
$sql = $DBDriver->getTableIndexesStatement($table, $options);
$sql = $DBDriver->getLoadTableDataFromFileStatement($file_path, $table, $options);
$sql = $DBDriver->getShowCreateTableStatement($table, $options);
$sql = $DBDriver->getShowCreateViewStatement($view, $options);
$sql = $DBDriver->getShowCreateTriggerStatement($trigger, $options);
$sql = $DBDriver->getShowCreateProcedureStatement($procedure, $options);
$sql = $DBDriver->getShowCreateFunctionStatement($function, $options);
$sql = $DBDriver->getShowCreateEventStatement($event, $options);
$sql = $DBDriver->getShowTablesStatement($db_name, $options);
$sql = $DBDriver->getShowViewsStatement($db_name, $options);
$sql = $DBDriver->getShowTriggersStatement($db_name, $options);
$sql = $DBDriver->getShowTableColumnsStatement($table, $db_name, $options);
$sql = $DBDriver->getShowForeignKeysStatement($table, $db_name, $options);
$sql = $DBDriver->getShowProceduresStatement($db_name, $options);
$sql = $DBDriver->getShowFunctionsStatement($db_name, $options);
$sql = $DBDriver->getShowEventsStatement($db_name, $options);
$sql = $DBDriver->getSetupTransactionStatement($options);
$sql = $DBDriver->getStartTransactionStatement($options);
$sql = $DBDriver->getCommitTransactionStatement($options);
$sql = $DBDriver->getStartDisableAutocommitStatement($options);
$sql = $DBDriver->getEndDisableAutocommitStatement($options);
$sql = $DBDriver->getStartLockTableWriteStatement($table, $options);
$sql = $DBDriver->getStartLockTableReadStatement($table, $options);
$sql = $DBDriver->getEndLockTableStatement($options);
$sql = $DBDriver->getStartDisableKeysStatement($table, $options);
$sql = $DBDriver->getEndDisableKeysStatement($table, $options);
$sql = $DBDriver->getDropTriggerStatement($trigger, $options);
$sql = $DBDriver->getDropProcedureStatement($procedure, $options);
$sql = $DBDriver->getDropFunctionStatement($function, $options);
$sql = $DBDriver->getDropEventStatement($event, $options);
$sql = $DBDriver->getDropViewStatement($view, $options);
$sql = $DBDriver->getShowTableCharsetsStatement($options);
$sql = $DBDriver->getShowColumnCharsetsStatement($options);
$sql = $DBDriver->getShowTableCollationsStatement($options);
$sql = $DBDriver->getShowColumnCollationsStatement($options);
$sql = $DBDriver->getShowDBStorageEnginesStatement($options);
		</textarea>
	</div>

	<h5>Some DB Static Methods:</h5>
	<div class="code">
		<textarea readonly>
$class_name = DB::getDriverClassNameByPath($driver_path);
$class_name = DB::getDriverTypeByClassName($driver_class);
$type = DB::getDriverTypeByPath($driver_path);
$class_names = DB::getAvailableDriverClassNames();
$class_name = DB::getDriverClassNameByType($type);
$path = DB::getDriverPathByType($type);
$DBDriver = DB::createDriverByType($type);
$options = DB::convertDSNToOptions($dsn);
$dsn = DB::getDSNByType($type, $options);
$labels = DB::getAllDriverLabelsByType();
$encodings_by_type = DB::getAllDBConnectionEncodingsByType();
$engines_by_type = DB::getAllStorageEnginesByType();
$extensions_by_type = DB::getAllExtensionsByType();
$conn_options_to_ignore_by_type = DB::getAllIgnoreConnectionOptionsByType();
$conn_options_to_ignore_by_extension_and_type = DB::getAllIgnoreConnectionOptionsByExtensionAndType();
$column_types_by_type = DB::getAllColumnTypesByType();
$column_types = DB::getAllColumnTypes();
$shared_column_types = DB::getAllSharedColumnTypes();
$column_simple_types_by_type = DB::getAllColumnSimpleTypesByType();
$column_simple_types = DB::getAllColumnSimpleTypes();
$shared_column_simple_types = DB::getAllSharedColumnSimpleTypes();
$column_numeric_types_by_type = DB::getAllColumnNumericTypesByType();
$column_numeric_types = DB::getAllColumnNumericTypes();
$shared_column_numeric_types = DB::getAllSharedColumnNumericTypes();
$column_date_types_by_type = DB::getAllColumnDateTypesByType();
$column_date_types = DB::getAllColumnDateTypes();
$shared_column_date_types = DB::getAllSharedColumnDateTypes();
$column_text_types_by_type = DB::getAllColumnTextTypesByType();
$column_text_types = DB::getAllColumnTextTypes();
$shared_column_text_types = DB::getAllSharedColumnTextTypes();
$column_blob_types_by_type = DB::getAllColumnBlobTypesByType();
$column_blob_types = DB::getAllColumnBlobTypes();
$shared_column_blob_types = DB::getAllSharedColumnBlobTypes();
$column_boolean_types_by_type = DB::getAllColumnBooleanTypesByType();
$column_boolean_types = DB::getAllColumnBooleanTypes();
$shared_column_boolean_types = DB::getAllSharedColumnBooleanTypes();
$column_mandatory_length_types_by_type = DB::getAllColumnMandatoryLengthTypesByType();
$column_mandatory_length_types = DB::getAllColumnMandatoryLengthTypes();
$shared_column_mandatory_length_types = DB::getAllSharedColumnMandatoryLengthTypes();
$column_auto_increment_types_by_type = DB::getAllColumnAutoIncrementTypesByType();
$column_auto_increment_types = DB::getAllColumnAutoIncrementTypes();
$shared_column_auto_increment_types = DB::getAllSharedColumnAutoIncrementTypes();
$column_boolean_type_available_values_by_type = DB::getAllBooleanTypeAvailableValuesByType();
$column_boolean_type_available_values = DB::getAllBooleanTypeAvailableValues();
$shared_column_boolean_type_available_values = DB::getAllSharedBooleanTypeAvailableValues();
$column_current_timestamp_available_values_by_type = DB::getAllCurrentTimestampAvailableValuesByType();
$column_current_timestamp_available_values = DB::getAllCurrentTimestampAvailableValues();
$shared_column_current_timestamp_available_values = DB::getAllSharedCurrentTimestampAvailableValues();
$column_column_types_props_to_ignore_by_type = DB::getAllColumnTypesIgnoredPropsByType();
$column_column_types_props_to_ignore = DB::getAllColumnTypesIgnoredProps();
$shared_column_column_types_props_to_ignore = DB::getAllSharedColumnTypesIgnoredProps();
$column_column_types_props_to_hide_by_type = DB::getAllColumnTypesHiddenPropsByType();
$column_column_types_props_to_hide = DB::getAllColumnTypesHiddenProps();
$shared_column_column_types_props_to_hide = DB::getAllSharedColumnTypesHiddenProps();
$column_attribute_value_reserved_words_by_type = DB::getAllAttributeValueReservedWordsByType();
$column_attribute_value_reserved_words = DB::getAllAttributeValueReservedWords();
$shared_column_attribute_value_reserved_words = DB::getAllSharedAttributeValueReservedWords();
$reserved_words_by_type = DB::getAllReservedWordsByType();
$reserved_words = DB::getAllReservedWords();
$shared_reserved_words = DB::getAllSharedReservedWords();

$list_with_sqls = DB::splitSQL($sql, $options = false);
$sql_without_comments = DB::removeSQLComments($sql, $options = false);
$sql_without_repeated_delimiters = DB::removeSQLRepeatedDelimiters($sql, $options = false);
$sql_with_new_delimiters = DB::replaceSQLEnclosingDelimiter($sql, $delimiters_to_search, $delimiters_to_replace);
$status = DB::isTheSameStaticTableName($table_name_1, $table_name_2, $options = false);
$status = DB::isStaticTableInNamesList($tables_name_list, $table_to_search, $options = false);
$table_name = DB::getStaticTableInNamesList($tables_name_list, $table_to_search, $options = false);

$sql = DB::convertObjectToDefaultSQL($data, $options = false);
$array = DB::convertDefaultSQLToObject($sql, $options = false);
$sql = DB::buildDefaultTableInsertSQL($table_name, $attributes, $options = false);
$sql = DB::buildDefaultTableUpdateSQL($table_name, $attributes, $conditions = false, $options = false);
$sql = DB::buildDefaultTableDeleteSQL($table_name, $conditions = false, $options = false);
$sql = DB::buildDefaultTableFindSQL($table_name, $attributes = false, $conditions = false, $options = false);
$sql = DB::buildDefaultTableCountSQL($table_name, $conditions = false, $options = false);
$sql = DB::buildDefaultTableFindRelationshipSQL($table_name, $rel_elm, $parent_conditions = false, $options = false);
$sql = DB::buildDefaultTableCountRelationshipSQL($table_name, $rel_elm, $parent_conditions = false, $options = false);
$sql = DB::buildDefaultTableFindColumnMaxSQL($table_name, $attribute_name, $options = false);
		</textarea>
	</div>

	<h5>Some DB Importer Methods:</h5>
	<div class="code short">
		<textarea readonly>
$DBFileImporter = new DBFileImporter($DBDriver);
//$DBFileImporter->setOptions(array(...)); //view more details in the examples/file_importer.php
$status = $DBFileImporter->importFile($file_path_to_import, $table_name, $columns_attributes, $force_rows_until_the_end);
$errors = $DBFileImporter->getErrors();
		</textarea>
	</div>

	<h5>Some DB Exporter Methods:</h5>
	<div class="code short">
		<textarea readonly>
$DBFileExporter = new DBFileExporter($DBDriver);
//$DBFileExporter->setOptions(array(...)); //view more details in the examples/file_exporter.php
$status = $DBFileExporter->exportFile($sql, $file_path_or_name_to_export, $output_type);
		</textarea>
	</div>

	<h5>Some DB Dumper Methods:</h5>
	<div class="code short">
		<textarea readonly>
$DBDumperHandler = new DBDumperHandler($DBDriver, $dump_settings, $pdo_settings); //view more details in the examples/file_dumper.php
$DBDumperHandler->connect();
$DBDumperHandler->run($file_path_to_dump);
$DBDumperHandler->disconnect();
		</textarea>
	</div>
	<br/>
</div>';
?>
