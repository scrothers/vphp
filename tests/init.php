<?PHP

$testcfg = parse_ini_file(__DIR__ . "/../vcenter.ini");
require __DIR__ . "/../vendor/autoload.php";

print_r($testcfg);
