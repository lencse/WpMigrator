<?php


namespace hu\lokilevente\WpMigrator;


class Migrator {

   /**
    * @var Instance
    */
   private $source;

   /**
    * @var Instance
    */
   private $target;

   function __construct($source, $target) {
      $this->source = $source;
      $this->target = $target;
   }

   public function migrate() {
      $source = $this->source;
      $target = $this->target;

      $tablePrefix = 'wptest_';


      libxml_use_internal_errors(true);

      $cookieFileName = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'cookie_dir' . DIRECTORY_SEPARATOR . 'wpm_cookie_' . uniqid();

      $c = curl_init($source->getPhpMyAdminUrl());
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
      curl_setopt($c, CURLOPT_POSTFIELDS, ['pma_username' => $source->getUserName(), 'pma_password' =>$source->getPassword()]);
      curl_setopt($c, CURLOPT_FOLLOWLOCATION, true);

      curl_setopt($c, CURLOPT_URL, $source->getPhpMyAdminUrl() . 'db_export.php?db=' . $source->getDatabase());
      $resp = curl_exec($c);

      $doc = new \DOMDocument();
      $doc->loadHTML($resp);

      $tableList = [];
      foreach ($doc->getElementById('table_select')->getElementsByTagName('option') as $option) {
         if (preg_match('/^' . $tablePrefix . '/i', $option->getAttribute('value'))) {
            $tableList[] = $option->getAttribute('value');
         }
      }

      curl_setopt($c, CURLOPT_URL, $source->getPhpMyAdminUrl() . "export.php");
      curl_setopt($c, CURLOPT_POSTFIELDS, http_build_query(array(
         'db' => $source->getDatabase(),
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

      $sql = str_replace($source->getWpUrl(), $target->getWpUrl(), curl_exec($c));

      curl_close($c);

      $sqlFileName = dirname(__FILE__) . '\..\..\..\..' .
         DIRECTORY_SEPARATOR . 'sql' . DIRECTORY_SEPARATOR . 'wpm_sql_' . uniqid() . '.sql';
      file_put_contents($sqlFileName, $sql);

      $cookieFileName = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'cookie_dir' . DIRECTORY_SEPARATOR . 'wpm_cookie_' . uniqid();

      $c = curl_init($target->getPhpMyAdminUrl());
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

      curl_setopt($c, CURLOPT_URL, $target->getPhpMyAdminUrl() . "server_import.php");
      curl_setopt($c, CURLOPT_POST, true);
      curl_setopt($c, CURLOPT_POSTFIELDS, ['pma_username' => $target->getUserName(), 'pma_password' => $target->getPassword()]);
      curl_setopt($c, CURLOPT_FOLLOWLOCATION, true);

      curl_exec($c);

      curl_setopt($c, CURLOPT_URL, $target->getPhpMyAdminUrl() . "import.php");
      curl_setopt($c, CURLOPT_POSTFIELDS, array(
         'db' => $target->getDatabase(),
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

      $resp = curl_exec($c);
   }

} 