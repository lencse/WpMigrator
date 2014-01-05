<?php

header('Content-Type: text/plain');

$sourcePhpMyAdminUrl = 'http://phpmyadmin.local/';
$sourceWpUrl = 'http://localhost/wordpress';
$sourceUserName = 'lencse';
$sourcePassword = 'lencse';
$sourceDb = 'kahuna';

$targetPhpMyAdminUrl = 'http://phpmyadmin.local/';
$targetWpUrl = 'http://localhost/wordpress2';
$targetUserName = 'lencse';
$targetPassword = 'lencse';
$targetDb = 'test';

$tablePrefix = 'wptest_';


libxml_use_internal_errors(true);

$cookieFileName = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'cookie_dir' . DIRECTORY_SEPARATOR . 'wpm_cookie_' . uniqid();

$c = curl_init($sourcePhpMyAdminUrl);
curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
curl_setopt($c, CURLOPT_HEADER, false);
curl_setopt($c, CURLOPT_COOKIEFILE, $cookieFileName);

$resp = curl_exec($c);


$doc = new \DOMDocument();
$doc->loadHTML($resp);

foreach ($doc->getElementsByTagName('input') as $input) {
   if ($input->getAttribute('name') == 'token') {
      $token = $input->getAttribute('value');
   }
}

curl_setopt($c, CURLOPT_POST, true);
curl_setopt($c, CURLOPT_POSTFIELDS, ['pma_username' => $sourceUserName, 'pma_password' => $sourcePassword]);
curl_setopt($c, CURLOPT_FOLLOWLOCATION, true);

//$resp = curl_exec($c);

//$doc = new \DOMDocument();
//$doc->loadHTML($resp);
//
//$cont = $doc->getElementById('pma_navigation_tree');
//foreach ($cont->getElementsByTagName('a') as $link) {
//   if ($link->nodeValue == $sourceDb) {
//      $selDbPath = $link->getAttribute('href');
//   }
//}
//
//curl_setopt($c, CURLOPT_HTTPGET, true);
//
//curl_setopt($c, CURLOPT_URL, $sourceUrl . '/' . $selDbPath);
//
//$resp = curl_exec($c);
//
//$doc = new \DOMDocument();
//$doc->loadHTML($resp);
//
//$cont = $doc->getElementById('topmenu');
//foreach ($cont->getElementsByTagName('a') as $link) {
//   if (preg_match('/^db_export\.php/i', $link->getAttribute('href'))) {
//      $exportPath = $link->getAttribute('href');
//   }
//}

curl_setopt($c, CURLOPT_URL, $sourcePhpMyAdminUrl . "db_export.php?db=$sourceDb");
$resp = curl_exec($c);

$doc = new \DOMDocument();
$doc->loadHTML($resp);

$tableList = [];
foreach ($doc->getElementById('table_select')->getElementsByTagName('option') as $option) {
   if (preg_match('/^' . $tablePrefix . '/i', $option->getAttribute('value'))) {
      $tableList[] = $option->getAttribute('value');
   }
}

curl_setopt($c, CURLOPT_URL, $sourcePhpMyAdminUrl . "export.php");
curl_setopt($c, CURLOPT_POSTFIELDS, http_build_query(array(
      'db' => $sourceDb,
      'token' => $token,
      'export_type' => 'database',
      'export_method' => 'quick',
      'quick_or_custom' => 'custom',
      'table_select' => $tableList,
      'output_format' => 'sendit',
      'filename_template' => '@DATABASE@',
      'charset_of_file' => 'utf-8',
      'compression' => 'none',
      'what' => 'sql',
      'sql_structure_or_data' => 'structure_and_data',
      'sql_drop_table' => 'something',
      'sql_create_table_statements' => 'something',
      'sql_if_not_exists' => 'something',
      'sql_auto_increment' => 'something',
      'sql_backquotes' => 'something',
      'sql_type' => 'INSERT',
      'sql_insert_syntax' => 'both',
      'sql_max_query_size' => '50000',
      'sql_hex_for_blob' => 'something',
      'sql_utc_time' => 'something',
   )));

$sql = str_replace($sourceWpUrl, $targetWpUrl, curl_exec($c));

curl_close($c);

$sqlFileName = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'sql' . DIRECTORY_SEPARATOR . 'wpm_sql_' . uniqid() . '.sql';
file_put_contents($sqlFileName, $sql);

//echo $sql;

$cookieFileName = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'cookie_dir' . DIRECTORY_SEPARATOR . 'wpm_cookie_' . uniqid();

$c = curl_init($targetPhpMyAdminUrl);
curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
curl_setopt($c, CURLOPT_HEADER, false);
curl_setopt($c, CURLOPT_COOKIEFILE, $cookieFileName);

$resp = curl_exec($c);

$doc = new \DOMDocument();
$doc->loadHTML($resp);

foreach ($doc->getElementsByTagName('input') as $input) {
   if ($input->getAttribute('name') == 'token') {
      $token = $input->getAttribute('value');
   }
}

curl_setopt($c, CURLOPT_URL, $targetPhpMyAdminUrl . "server_import.php");
curl_setopt($c, CURLOPT_POST, true);
curl_setopt($c, CURLOPT_POSTFIELDS, ['pma_username' => $targetUserName, 'pma_password' => $targetPassword]);
curl_setopt($c, CURLOPT_FOLLOWLOCATION, true);

curl_exec($c);

curl_setopt($c, CURLOPT_URL, $targetPhpMyAdminUrl . "import.php");
//curl_setopt($c, CURLOPT_URL, 'http://localhost/sandbox/post.php');
curl_setopt($c, CURLOPT_POSTFIELDS, array(
//      'noplugin' => $noplugin,
      'db' => $targetDb,
      'token' => $token,
      'import_type' => 'database',
      'charset_of_file' => 'utf-8',
      'allow_interrupt' => 'yes',
      'skip_queries' => '0',
      'format' => 'sql',
      'sql_compatibility' => 'NONE',
      'sql_no_auto_value_on_zero' => 'something',
      'import_file' => '@' . $sqlFileName,
   ));
//curl_setopt($c, CURLOPT_URL, $sourcePhpMyAdminUrl . "/import.php");

$resp = curl_exec($c);

//echo $resp;