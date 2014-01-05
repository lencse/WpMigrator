<?php


namespace hu\lokilevente\WpMigrator;


class MigratorTest extends \PHPUnit_Framework_TestCase {

   public function testMigration() {
      $postTitle = 'Hello world! - ' . uniqid('', true);

      $db = new \PDO('mysql:host=localhost;dbname=kahuna', 'lencse', 'lencse');
      $db->exec("UPDATE wptest_posts SET post_title = '$postTitle' WHERE id = 1");

      $source = new Instance(
         'http://phpmyadmin.local/',
         'http://localhost/wordpress',
         'kahuna',
         'lencse',
         'lencse'
      );
      $target = new Instance(
         'http://phpmyadmin.local/',
         'http://localhost/wordpress2',
         'test',
         'lencse',
         'lencse'
      );
      $wpm = new Migrator($source, $target);
      $wpm->migrate();

      $db = new \PDO('mysql:host=localhost;dbname=test', 'lencse', 'lencse');
      $newTitle = $db->query("SELECT post_title FROM wptest_posts  WHERE id = 1")->fetchColumn();

      $this->assertEquals($postTitle, $newTitle);
   }

} 