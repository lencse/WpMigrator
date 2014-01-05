<?php


namespace hu\lokilevente\WpMigrator;


abstract class Connection {

   /**
    * @var Migrator
    */
   protected $migrator;

   /**
    * @var string
    */
   private $token;

   /**
    * @var Curl
    */
   private $curl;

   /**
    * @var string
    */
   private $sqlFileName;

   /**
    * @param Migrator $migrator
    */
   public function __construct(Migrator $migrator) {
      $this->migrator = $migrator;
      $this->curl = new Curl();

      $dom = new DOMParser($this->curl->get($this->getInstance()->getPhpMyAdminUrl()));
      $this->token = $dom->getInputValue('token');
   }

   public function exportSql() {
      $dom = new DOMParser($this->curl->post(
            $this->getInstance()->getPhpMyAdminUrl() . 'db_export.php?db=' . $this->getInstance()->getDatabase(),
            array(
               'pma_username' => $this->getInstance()->getUserName(),
               'pma_password' => $this->getInstance()->getPassword(),
            )
         ));
      $tableList = $dom->getPrefixedTableList($this->migrator->getTablePrefix());

      $resp = $this->curl->post(
            $this->getInstance()->getPhpMyAdminUrl() . 'export.php',
            http_build_query(array(
               'db' => $this->getInstance()->getDatabase(),
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
            ))
         );

      $sql = str_replace($this->migrator->getSource()->getWpUrl(), $this->migrator->getTarget()->getWpUrl(), $resp);

      $this->sqlFileName = dirname(__FILE__) . '\..\..\..\..' .
         DIRECTORY_SEPARATOR . 'sql' . DIRECTORY_SEPARATOR . 'wpm_sql_' . uniqid() . '.sql';
      file_put_contents($this->sqlFileName, $sql);
   }

   /**
    * @param $sqlFileName string
    */
   public function loadSql($sqlFileName) {
      $this->curl->post(
         $this->getInstance()->getPhpMyAdminUrl() . "server_import.php",
         array(
            'pma_username' => $this->getInstance()->getUserName(),
            'pma_password' => $this->getInstance()->getPassword(),
         )
      );

      $this->curl->post(
         $this->getInstance()->getPhpMyAdminUrl() . "import.php",
         array(
            'db' => $this->getInstance()->getDatabase(),
            'token' => $this->token,
            'import_type' => 'database',
            'charset_of_file' => 'utf-8',
            'allow_interrupt' => 'yes',
            'skip_queries' => '0',
            'format' => 'sql',
            'sql_compatibility' => 'NONE',
            'sql_no_auto_value_on_zero' => 'something',
            'import_file' => '@' . $sqlFileName,
         )
      );

   }

   /**
    * @return Instance
    */
   abstract protected function getInstance();

   /**
    * @return string
    */
   public function getSqlFileName() {
      return $this->sqlFileName;
   }

} 