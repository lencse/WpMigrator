<?php


namespace Lencse\WpMigrator;


class SourceConnection extends Connection{

   /**
    * @return File
    */
   public function exportSql() {
      $dom = new DOMParser($this->curl->post(
         $this->getInstance()->getPhpMyAdminUrl() . 'db_export.php?db=' . $this->getInstance()->getDatabase(),
         $this->getLoginArray()
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
      return new File($sql);
   }

   protected function getInstance() {
      return $this->migrator->getSource();
   }

} 