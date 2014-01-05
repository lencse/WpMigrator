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
      $src = new SourceConnection($this);
      $src->exportSql();

      $trgt = new TargetConnection($this);
      $trgt->loadSql($src->getSqlFileName());

      unlink($src->getSqlFileName());
   }

   /**
    * @return \hu\lokilevente\WpMigrator\Instance
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
    * @return \hu\lokilevente\WpMigrator\Instance
    */
   public function getSource() {
      return $this->source;
   }

}