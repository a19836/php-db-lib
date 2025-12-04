<?php
include_once __DIR__ . "/config.php";

$table_name = "a_product_x";

echo $style;

echo "<h1>DB CRUD Actions</h1>
<p>Executes CRUD actions through objects/arrays</p>";

echo '<h4>Choose a DB driver: 
	<select onChange="document.location=\'?type=\' + this.value;">';

$types = DB::getAllDriverLabelsByType();
foreach ($types as $id => $label)
	echo "<option value='$id'" . ($id == $type ? " selected" : "") . ">$label</option>";
echo "</select></h4>";

echo '<div class="note">
		<span>
		The system will create 2 temp tables. Then will perform some CRUD actions on these tables and show the correspondent code and results.<br/>
		At the end will drop/remove the temp tables from your DB.	
		</span>
</div>';

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
		
		//create product_type table
		$tn = DB::getStaticTableInNamesList($tables, $table_name . "_type");
		
		if (!$tn) {
			$drop_table_type = true;
			$sql = $DBDriver->getCreateTableStatement(array(
				"name" => $table_name . "_type",
				"attributes" => array(
					array("name" => "product_type_id", "type" => "bigint", "length" => 20, "primary_key" => true, "auto_increment" => true),
					array("name" => "name", "type" => "varchar", "length" => 200, "default" => "", "null" => false)
				)
			));
			$status = $DBDriver->setSQL($sql);
			//echo "sql: $sql<br/>status: $status<br/>";die();
			
			if ($status) {
				$status = $DBDriver->insertObject($table_name . "_type", array("name" => "desert"));
				//echo "sql: $sql<br/>status: $status<br/>";die();
				$status = $DBDriver->insertObject($table_name . "_type", array("name" => "vegetal"));
				//echo "sql: $sql<br/>status: $status<br/>";die();
			}
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
	echo "<h5>Insert object into DB:</h5>";
	$attributes = array(
		"client_id" => 10,
		"type_id" => 2,
		"name" => "onion",
		"description" => "simple onion",
	);
	$status = $DBDriver->insertObject($table_name, $attributes);
	echo "<div class=\"code short\"><textarea readonly>\$attributes = array(
	\"client_id\" => 10,
	\"type_id\" => 2,
	\"name\" => \"onion\",
	\"description\" => \"simple onion\",
);
\$status = \$DBDriver->insertObject(\"" . $table_name . "\", \$attributes);</textarea></div>
	<div class=\"code one-line\"><textarea readonly>$status</textarea></div>";
	
	$id = $status ? $DBDriver->getInsertedId() : null;
	$id = $id ? $id : 13;
	
	echo "<h5>Update object in DB:</h5>";
	$attributes = array(
		"name" => "onion",
		"description" => "new onion",
	);
	$conditions = array(
		"product_id" => $id,
	);
	$status = $DBDriver->updateObject($table_name, $attributes, $conditions);
	echo "<div class=\"code short\"><textarea readonly>\$attributes = array(
	\"name\" => \"onion\",
	\"description\" => \"new onion\",
);
\$conditions = array(
	\"product_id\" => " . $id . ",
);
\$status = \$DBDriver->updateObject(\"" . $table_name . "\", \$attributes, \$conditions);</textarea></div>
	<div class=\"code one-line\"><textarea readonly>$status</textarea></div>";

	echo "<h5>Select objects from DB:</h5>";
	$attributes = array("product_id", "name");
	$conditions = array("product_id" => $id);
	$objects = $DBDriver->findObjects($table_name, $attributes, $conditions);
	echo "<div class=\"code short\"><textarea readonly>
\$attributes = array(\"product_id\", \"name\");
\$conditions = array(\"product_id\" => " . $id . ");
\$objects = \$DBDriver->findObjects(\"" . $table_name . "\", \$attributes, \$conditions);</textarea></div>
	<div class=\"code short\"><textarea readonly>" . print_r($objects, true) . "</textarea></div>";

	echo "<h5>Count objects in DB:</h5>";
	$total = $DBDriver->countObjects($table_name, array("product_id" => $id));
	echo "<div class=\"code one-line\"><textarea readonly>\$total = \$DBDriver->countObjects(\"" . $table_name . "\", array(\"product_id\" => " . $id . "));</textarea></div>
	<div class=\"code one-line\"><textarea readonly>$total</textarea></div>";

	echo "<h5>Get maximum value from DB:</h5>";
	$max = $DBDriver->findObjectsColumnMax($table_name, "product_id");
	echo "<div class=\"code one-line\"><textarea readonly>\$max = \$DBDriver->findObjectsColumnMax(\"" . $table_name . "\", \"product_id\");</textarea></div>
	<div class=\"code one-line\"><textarea readonly>$max</textarea></div>";

	echo "<h5>Get foreign objects:</h5>";
	$rel_elm = array(
		"keys" => array(
			array("pcolumn" => "type_id", "ftable" => $table_name . "_type pt", "fcolumn" => "product_type_id")
		)
	);
	$objects = $DBDriver->findRelationshipObjects($table_name, $rel_elm);
	echo "<div class=\"code short\"><textarea readonly>\$rel_elm = array(
	\"keys\" => array(
		array(\"pcolumn\" => \"type_id\", \"ftable\" => \"" . $table_name . "_type pt\", \"fcolumn\" => \"product_type_id\")
	)
);
\$objects = \$DBDriver->findRelationshipObjects(\"" . $table_name . "\", \$rel_elm);</textarea></div>
	<div class=\"code\"><textarea readonly>" . print_r($objects, true) . "</textarea></div>";

	echo "<h5>Get foreign objects together with main table:</h5>";
	$rel_elm = array(
		"attributes" => array(
			array("table" => "pt", "column" => "product_type_id", "name" => "type_id"),
			array("table" => "pt", "column" => "name", "name" => "type"),
		),
		"keys" => array(
			array("pcolumn" => "type_id", "ftable" => $table_name . "_type pt", "fcolumn" => "product_type_id")
		),
		"conditions" => array(
			array("table" => $table_name, "column" => "type_id", "reftable" => "pt", "refcolumn" => "product_type_id"),
			array("table" => "pt", "column" => "product_type_id", "operator" => "<", "value" => 3)
		),
		"groups_by" => array(
			array("table" => "", "column" => "type_id", "having" => "count(product_type_id) > 1")
		)
	);
	$parent_conditions = array(
		"pt.name" => "vegetal"
	);
	$options = array(
		"sorts" => array(
			array("table" => "", "column" => "type", "order" => "desc")
		),
		"sql_conditions" => "pt.name = 'vegetal'",
	);
	$objects = $DBDriver->findRelationshipObjects($table_name, $rel_elm, $parent_conditions, $options);
	echo "<div class=\"code\"><textarea readonly>\$rel_elm = array(
	\"attributes\" => array(
		array(\"table\" => \"pt\", \"column\" => \"product_type_id\", \"name\" => \"type_id\"),
		array(\"table\" => \"pt\", \"column\" => \"name\", \"name\" => \"type\"),
	),
	\"keys\" => array(
		array(\"pcolumn\" => \"type_id\", \"ftable\" => \$table_name . \"_type pt\", \"fcolumn\" => \"product_type_id\")
	),
	\"conditions\" => array(
		array(\"table\" => \$table_name, \"column\" => \"type_id\", \"reftable\" => \"pt\", \"refcolumn\" => \"product_type_id\"),
		array(\"table\" => \"pt\", \"column\" => \"product_type_id\", \"operator\" => \"<\", \"value\" => 3)
	),
	\"groups_by\" => array(
		array(\"table\" => \"\", \"column\" => \"type_id\", \"having\" => \"count(product_type_id) > 1\")
	)
);
\$parent_conditions = array(
	\"pt.name\" => \"vegetal\"
);
\$options = array(
	\"sorts\" => array(
		array(\"table\" => \"\", \"column\" => \"type\", \"order\" => \"desc\")
	),
	\"sql_conditions\" => \"pt.name = 'vegetal'\",
);
\$objects = \$DBDriver->findRelationshipObjects(\"" . $table_name . "\", \$rel_elm, \$parent_conditions, \$options);</textarea></div>
<div class=\"code short\"><textarea readonly>" . print_r($objects, true) . "</textarea></div>";

	echo "<h5>Count foreign objects:</h5>";
	$rel_elm = array(
		"keys" => array(
			array("pcolumn" => "type_id", "ftable" => $table_name . "_type pt", "fcolumn" => "product_type_id")
		),
		"conditions" => array(
			"type_id" => array(
				"operator" => "<",
				"value" => 3
			)
		)
	);
	$total = $DBDriver->countRelationshipObjects($table_name, $rel_elm);
	echo "<div class=\"code\"><textarea readonly>\$rel_elm = array(
	\"keys\" => array(
		array(\"pcolumn\" => \"type_id\", \"ftable\" => \"" . $table_name . "_type pt\", \"fcolumn\" => \"product_type_id\")
	),
	\"conditions\" => array(
		\"type_id\" => array(
			\"operator\" => \"<\",
			\"value\" => 3
		)
	)
);
\$total = \$DBDriver->countRelationshipObjects(\"" . $table_name . "\", \$rel_elm);</textarea></div>
	<div class=\"code one-line\"><textarea readonly>$total</textarea></div>";

	echo "<h5>Delete object in DB:</h5>";
	$status = $DBDriver->deleteObject($table_name, array(
		"product_id" => array(
			"operator" => ">=",
			"value" => $id,
		),
	));
	echo "<div class=\"code short\"><textarea readonly>\$status = \$DBDriver->deleteObject(\"" . $table_name . "\", array(
	\"product_id\" => array(
		\"operator\" => \">=\",
		\"value\" => " . $id . "
	),
));</textarea></div>
	<div class=\"code one-line\"><textarea readonly>$status</textarea></div>";
	
	echo "<br/>";
	
	if (!empty($drop_table)) {
		$sql = $DBDriver->getDropTableStatement($table_name);
		$status = $DBDriver->setSQL($sql);
		//echo "sql: $sql<br/>status: $status<br/>";die();
	}
	
	if (!empty($drop_table_type)) {	
		$sql = $DBDriver->getDropTableStatement($table_name . "_type");
		$status = $DBDriver->setSQL($sql);
		//echo "sql: $sql<br/>status: $status<br/>";die();
	}
}
?>
