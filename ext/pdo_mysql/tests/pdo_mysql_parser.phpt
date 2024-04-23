--TEST--
MySQL PDO Parser custom syntax
--EXTENSIONS--
pdo_mysql
--SKIPIF--
<?php
require_once __DIR__ . '/inc/mysql_pdo_test.inc';
MySQLPDOTest::skip();
?>
--FILE--
<?php
require_once __DIR__ . '/inc/mysql_pdo_test.inc';
$db = MySQLPDOTest::factory();

$table = 'pdo_mysql_parser';

$db->exec("DROP TABLE IF EXISTS {$table}");
$db->exec("CREATE TABLE {$table} (`a``?` int NOT NULL)");
$db->exec("INSERT INTO {$table} VALUES (1)");

// No parameters
$queries = [
    "SELECT * FROM {$table}",
    "SELECT * FROM {$table} -- ?",
    "SELECT * FROM {$table} # ?",
    "SELECT * FROM {$table} /* ? */",
];

foreach ($queries as $k => $query) {
    $stmt = $db->prepare($query);
    $stmt->execute();
    var_dump($stmt->fetch(PDO::FETCH_NUM) === [0 => 1]);
}

// One parameter
$queries = [
    "SELECT * FROM {$table} WHERE 1 = ?",
    "SELECT * FROM {$table} WHERE \"?\" IN (?, '?')",
    "SELECT * FROM {$table} WHERE `a``?` = ?",
];

foreach ($queries as $k => $query) {
    $stmt = $db->prepare($query);
    $stmt->execute([1]);
    var_dump($stmt->fetch(PDO::FETCH_NUM) === [0 => 1]);
}

$db->exec("DROP TABLE pdo_mysql_parser");

?>
--EXPECT--
bool(true)
bool(true)
bool(true)
bool(true)
bool(true)
bool(true)
bool(true)
