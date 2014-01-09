<?php


namespace Lencse\WpMigrator;


class Migrator {

   /**
    * @var Instance
    */
   private $source;

   /**
    * @var Instance
    */
   private $target;

   /**
    * @var string
    */
   private $tablePrefix;

   public function __construct(Instance $source, Instance $target, $tablePrefix) {
      $this->source = $source;
      $this->target = $target;
      $this->tablePrefix = $tablePrefix;
   }

   public function migrate() {
      $src = new SourceConnection($this, new PHPCurl());
      $sql = $src->exportSql();
      $trgt = new TargetConnection($this, new PHPCurl());
      $trgt->loadSql($sql);
   }

   /**
    * @return \Lencse\WpMigrator\Instance
    */
   public function getTarget() {
      return $this->target;
   }

   /**
    * @return string
    */
   public function getTablePrefix() {
      return $this->tablePrefix;
   }

   /**
    * @return \Lencse\WpMigrator\Instance
    */
   public function getSource() {
      return $this->source;
   }

}