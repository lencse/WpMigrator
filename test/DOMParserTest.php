<?php


namespace Lencse\WpMigrator;


class Test extends \PHPUnit_Framework_TestCase {

   private static $html = <<<'EOS'
<!DOCTYPE HTML>
<html>
   <title>Test HTML</title>
<head>
</head>
<body>
   <form method="post" action="index.php" name="login_form" class="disableAjax login hide js-show">
      <input type="text" name="pma_username" id="input_username" value="password" size="24" class="textfield"/>
      <input type="hidden" name="token" value="abc123" />
      <select name="table_select[]" id="table_select" size="10" multiple="multiple">
         <option value="tb_table1" selected="selected">tb_table1</option>
         <option value="table2" selected="selected">table2</option>
         <option value="tb_table3" selected="selected">tb_table3</option>
      </select>
   </form>
</body>
</html>
EOS;

   /**
    * @var DOMParser
    */
   private $dom;

   public function setUp() {
      $this->dom = new DOMParser(self::$html);
   }

   public function testInputValue() {
      $this->assertEquals('abc123', $this->dom->getInputValue('token'));
   }

   public function testPrefixedTableList() {
      $tableList = $this->dom->getPrefixedTableList('tb_');
      $this->assertEquals(2, count($tableList));
      $this->assertContains('tb_table1', $tableList);
      $this->assertContains('tb_table3', $tableList);
   }

}
 