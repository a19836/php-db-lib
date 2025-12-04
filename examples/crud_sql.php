<?php
include_once __DIR__ . "/config.php";

echo $style;

echo "<h1>DB CRUD SQL</h1>
<p>Generates sql through objects/arrays and vice-versa</p>";

echo '<h4>Choose a DB driver: 
	<select onChange="document.location=\'?type=\' + this.value;">';

$types = DB::getAllDriverLabelsByType();
foreach ($types as $id => $label)
	echo "<option value='$id'" . ($id == $type ? " selected" : "") . ">$label</option>";
echo "</select></h4>";

echo '<div class="note">
		<span>
		This shows how to convert objects into sql statements and vice-versa.
		</span>
</div>';

echo "<h5>Convert simple array into sql:</h5>";
$obj = array(
	"type" => "select",
	"table" => "product",
	"attributes" => array(
		array("column" => "*"), 
	),
);
$sql = $DBDriver->convertObjectToSQL($obj);
echo "<div class=\"code\"><textarea readonly>" . print_r($obj, true) . "</textarea></div>
<div class=\"code sql short\"><textarea readonly>$sql</textarea></div>";

echo "<h5>Convert complex array into sql:</h5>";
$obj = array(
	"type" => "select",
	"table" => "product",
	"attributes" => array(
		array("column" => "product_id",), 
		array("table" => "pt", "column" => "name", "name" => "type"), 
		array("column" => "name"), 
		array("column" => "count(product.client_id)", "name" => "total"), 
	),
	"keys" => array(
		array("pcolumn" => "type_id", "ftable" => "product_type pt", "fcolumn" => "product_type_id")
	),
	"conditions" => array(
		array("column" => "name", "operator" => "in", "value" => array("carrot", "tomato", "picle")),
		array("table" => "pt", "column" => "name", "operator" => "!=", "value" => "potato")
	),
	"groups_by" => array(
		array("column" => "product_id")
	),
	"sorts" => array(
		array("column" => "name", "order" => "desc")
	),
	"start" => 10,
	"limit" => 50
);
$sql = $DBDriver->convertObjectToSQL($obj);
echo "<div class=\"code\"><textarea readonly>" . print_r($obj, true) . "</textarea></div>
<div class=\"code sql\"><textarea readonly>$sql</textarea></div>";

echo "<h5>Convert sql into array:</h5>";
$object = $DBDriver->convertSQLToObject($sql);
echo "<div class=\"code sql\"><textarea readonly>$sql</textarea></div>
<div class=\"code\"><textarea readonly>" . print_r($object, true) . "</textarea></div>";

echo "<h5>Build insert sql:</h5>";
$attributes = array(
	"client_id" => 10,
	"type_id" => 2,
	"name" => "onion",
	"description" => "new onion",
);
$sql = $DBDriver->buildTableInsertSQL("product", $attributes);
echo "<div class=\"code short\"><textarea readonly>\$attributes = array(
	\"client_id\" => 10,
	\"type_id\" => 2,
	\"name\" => \"onion\",
	\"description\" => \"new onion\",
);
\$sql = \$DBDriver->buildTableInsertSQL(\"product\", \$attributes);</textarea></div>
<div class=\"code sql one-line\"><textarea readonly>$sql</textarea></div>";

echo "<h5>Build update sql:</h5>";
$attributes = array(
	"name" => "onion",
	"description" => "new onion",
);
$conditions = array(
	"product_id" => 13,
);
$sql = $DBDriver->buildTableUpdateSQL("product", $attributes, $conditions);
echo "<div class=\"code short\"><textarea readonly>\$attributes = array(
	\"name\" => \"onion\",
	\"description\" => \"new onion\",
);
\$conditions = array(
	\"product_id\" => 13,
);
\$sql = \$DBDriver->buildTableUpdateSQL(\"product\", \$attributes, \$conditions);</textarea></div>
<div class=\"code sql one-line\"><textarea readonly>$sql</textarea></div>";

echo "<h5>Build delete sql:</h5>";
$sql = $DBDriver->buildTableDeleteSQL("product", array(
	"product_id" => array(
		"operator" => ">=",
		"value" => 13
	),
));
echo "<div class=\"code short\"><textarea readonly>\$sql = \$DBDriver->buildTableDeleteSQL(\"product\", array(
	\"product_id\" => array(
		\"operator\" => \">=\",
		\"value\" => 13
	),
));</textarea></div>
<div class=\"code sql one-line\"><textarea readonly>$sql</textarea></div>";

echo "<h5>Build select sql:</h5>";
$attributes = array("product_id", "name");
$conditions = array("product_id" => 13);
$sql = $DBDriver->buildTableFindSQL("product", $attributes, $conditions);
echo "<div class=\"code short\"><textarea readonly>
\$attributes = array(\"product_id\", \"name\");
\$conditions = array(\"product_id\" => 13);
\$sql = \$DBDriver->buildTableFindSQL(\"product\", \$attributes, \$conditions);</textarea></div>
<div class=\"code sql one-line\"><textarea readonly>$sql</textarea></div>";

echo "<h5>Build count sql:</h5>";
$sql = $DBDriver->buildTableCountSQL("product", array("product_id" => 13));
echo "<div class=\"code one-line\"><textarea readonly>\$sql = \$DBDriver->buildTableCountSQL(\"product\", array(\"product_id\" => 13));</textarea></div>
<div class=\"code sql one-line\"><textarea readonly>$sql</textarea></div>";

echo "<h5>Build max sql:</h5>";
$sql = $DBDriver->buildTableFindColumnMaxSQL("product", "type_id");
echo "<div class=\"code one-line\"><textarea readonly>\$sql = \$DBDriver->buildTableFindColumnMaxSQL(\"product\", \"type_id\");</textarea></div>
<div class=\"code sql one-line\"><textarea readonly>$sql</textarea></div>";

echo "<h5>Build simple select sql with foreign table:</h5>";
$rel_elm = array(
	"keys" => array(
		array("pcolumn" => "type_id", "ftable" => "product_type pt", "fcolumn" => "product_type_id")
	)
);
$sql = $DBDriver->buildTableFindRelationshipSQL("product", $rel_elm);
echo "<div class=\"code short\"><textarea readonly>\$rel_elm = array(
	\"keys\" => array(
		array(\"pcolumn\" => \"type_id\", \"ftable\" => \"product_type pt\", \"fcolumn\" => \"product_type_id\")
	)
);
\$sql = \$DBDriver->buildTableFindRelationshipSQL(\"product\", \$rel_elm);</textarea></div>
<div class=\"code sql one-line\"><textarea readonly>$sql</textarea></div>";

echo "<h5>Build select sql with foreign table:</h5>";
$rel_elm = array(
		"attributes" => array(
			array("table" => "pt", "column" => "product_type_id", "name" => "type_id"),
			array("table" => "pt", "column" => "name", "name" => "type"),
		),
		"keys" => array(
			array("pcolumn" => "type_id", "ftable" => "product_type pt", "fcolumn" => "product_type_id")
		),
		"conditions" => array(
			array("table" => "product", "column" => "type_id", "reftable" => "pt", "refcolumn" => "product_type_id"),
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
$sql = $DBDriver->buildTableFindRelationshipSQL("product", $rel_elm, $parent_conditions, $options);
echo "<div class=\"code\"><textarea readonly>\$rel_elm = array(
	\"attributes\" => array(
		array(\"table\" => \"pt\", \"column\" => \"product_type_id\", \"name\" => \"type_id\"),
		array(\"table\" => \"pt\", \"column\" => \"name\", \"name\" => \"type\"),
	),
	\"keys\" => array(
		array(\"pcolumn\" => \"type_id\", \"ftable\" => \"product_type pt\", \"fcolumn\" => \"product_type_id\")
	),
	\"conditions\" => array(
		array(\"table\" => \"product\", \"column\" => \"type_id\", \"reftable\" => \"pt\", \"refcolumn\" => \"product_type_id\"),
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
\$sql = \$DBDriver->buildTableFindRelationshipSQL(\"product\", \$rel_elm, \$parent_conditions, \$options);</textarea></div>
<div class=\"code sql short\"><textarea readonly>$sql</textarea></div>";

echo "<h5>Build count sql with foreign table:</h5>";
$rel_elm = array(
	"keys" => array(
		array("pcolumn" => "type_id", "ftable" => "product_type pt", "fcolumn" => "product_type_id")
	),
	"conditions" => array(
		"type_id" => array(
			"operator" => "<",
			"value" => 3
		)
	)
);
$sql = $DBDriver->buildTableCountRelationshipSQL("product", $rel_elm);
echo "<div class=\"code\"><textarea readonly>\$rel_elm = array(
	\"keys\" => array(
		array(\"pcolumn\" => \"type_id\", \"ftable\" => \"product_type pt\", \"fcolumn\" => \"product_type_id\")
	),
	\"conditions\" => array(
		\"type_id\" => array(
			\"operator\" => \"<\",
			\"value\" => 3
		)
	)
);
\$sql = \$DBDriver->buildTableCountRelationshipSQL(\"product\", \$rel_elm);</textarea></div>
<div class=\"code sql short\"><textarea readonly>$sql</textarea></div>";

echo "<br/>";
?>
