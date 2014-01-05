<?php


namespace hu\lokilevente\WpMigrator;


class WpMigratorTest extends \PHPUnit_Framework_TestCase {

   public function testMigration() {
      $postTitle = 'Hello world! - ' . uniqid('', true);

      $db = new \PDO('mysql:host=localhost;dbname=kahuna', 'lencse', 'lencse');
      $db->exec("UPDATE wptest_posts SET post_title = '$postTitle' WHERE id = 1");

      $wpm = new WpMigrator();
      $wpm->migrate();

      $db = new \PDO('mysql:host=localhost;dbname=test', 'lencse', 'lencse');
      $newTitle = $db->query("SELECT post_title FROM wptest_posts  WHERE id = 1")->fetchColumn();

      $this->assertEquals($postTitle, $newTitle);
   }

} 