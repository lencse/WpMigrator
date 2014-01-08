<?php


namespace Lencse\WpMigrator\Test;

use Lencse\WpMigrator\Instance as Instance;


class TestInstance {

   /**
    * @var \PDO
    */
   private $db;

   /**
    * @var Instance
    */
   private $wp;

   /**
    * @param \PDO $db
    * @param Instance $wp
    */
   function __construct(\PDO $db, Instance $wp) {
      $this->db = $db;
      $this->wp = $wp;
   }

   /**
    * @param $title string
    */
   public function updatePostTitle($title) {
      $this->db->exec("UPDATE wptest_posts SET post_title = '$title' WHERE id = 1");
   }

   /**
    * @return string
    */
   public function queryPostTitle() {
      return $this->db->query("SELECT post_title FROM wptest_posts  WHERE id = 1")->fetchColumn();
   }

   /**
    * @return \hu\lokilevente\WpMigrator\Instance
    */
   public function getWp() {
      return $this->wp;
   }

}
