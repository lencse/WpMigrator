<?php


namespace hu\lokilevente\WpMigrator;


class Connection {

   /**
    * @var Instance
    */
   private $instance;

   /**
    * @var string
    */
   private $token;

   private $curl;

   /**
    * @var string
    */
   private $sqlFileName;

   /**
    * @var string
    */
   private $tablePrefix;

   /**
    * @param Instance $instance
    * @param $tablePrefix string
    */
   public function __construct(Instance $instance, $tablePrefix) {
      $this->instance = $instance;
      $this->tablePrefix = $tablePrefix;
      $this->curl = curl_init();
      curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($this->curl, CURLOPT_HEADER, false);
      curl_setopt($this->curl, CURLOPT_COOKIEFILE, dirname(__FILE__) . DIRECTORY_SEPARATOR . 'cookie_dir' .
         DIRECTORY_SEPARATOR . 'wpm_cookie_' . uniqid());
      curl_setopt($this->curl, CURLOPT_URL, $this->instance->getPhpMyAdminUrl());

      $doc = new \DOMDocument();
      $doc->loadHTML(curl_exec($this->curl));

      foreach ($doc->getElementsByTagName('input') as $input) {
         if ($input->getAttribute('name') == 'token') {
            $this->token = $input->getAttribute('value');
         }
      }
      curl_setopt($this->curl, CURLOPT_POST, true);
      curl_setopt($this->curl, CURLOPT_POSTFIELDS, array(
         'pma_username' => $this->instance->getUserName(),
         'pma_password' => $this->instance->getPassword(),
      ));
      curl_setopt($this->curl, CURLOPT_FOLLOWLOCATION, true);
   }

   public function exportSql() {
      curl_setopt($this->curl, CURLOPT_URL, $this->instance->getPhpMyAdminUrl() . 'db_export.php?db=' .
         $this->instance->getDatabase());

      $doc = new \DOMDocument();
      $doc->loadHTML(curl_exec($this->curl));

      $tableList = [];
      foreach ($doc->getElementById('table_select')->getElementsByTagName('option') as $option) {
         if (preg_match('/^' . $this->tablePrefix . '/i', $option->getAttribute('value'))) {
            $tableList[] = $option->getAttribute('value');
         }
      }

      curl_setopt($this->curl, CURLOPT_URL, $this->instance->getPhpMyAdminUrl() . "export.php");
      curl_setopt($this->curl, CURLOPT_POSTFIELDS, http_build_query(array(
         'db' => $this->instance->getDatabase(),
         'token' => $this->token,
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

      $sql = str_replace($this->instance->getWpUrl(), $this->instance->getWpUrl(), curl_exec($this->curl));

      curl_close($this->curl);

      $this->sqlFileName = dirname(__FILE__) . '\..\..\..\..' .
         DIRECTORY_SEPARATOR . 'sql' . DIRECTORY_SEPARATOR . 'wpm_sql_' . uniqid() . '.sql';
      file_put_contents($this->sqlFileName, $sql);
   }

   /**
    * @param $sqlFileName string
    */
   public function loadSql($sqlFileName) {
      curl_setopt($this->curl, CURLOPT_URL, $this->instance->getPhpMyAdminUrl() . "server_import.php");
      curl_exec($this->curl);

      curl_setopt($this->curl, CURLOPT_URL, $this->instance->getPhpMyAdminUrl() . "import.php");
      curl_setopt($this->curl, CURLOPT_POSTFIELDS, array(
         'db' => $this->instance->getDatabase(),
         'token' => $this->token,
         'import_type' => 'database',
         'charset_of_file' => 'utf-8',
         'allow_interrupt' => 'yes',
         'skip_queries' => '0',
         'format' => 'sql',
         'sql_compatibility' => 'NONE',
         'sql_no_auto_value_on_zero' => 'something',
         'import_file' => '@' . $sqlFileName,
      ));
      curl_exec($this->curl);
      curl_close($this->curl);
   }

   /**
    * @return string
    */
   public function getSqlFileName() {
      return $this->sqlFileName;
   }

} 