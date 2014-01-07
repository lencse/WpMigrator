<?php


namespace Lencse\WpMigrator;


class DOMParser {

   /**
    * @var \DOMDocument
    */
   private $doc;

   /**
    * @param $html string
    */
   public function __construct($html) {
      libxml_use_internal_errors(true);
      $this->doc = new \DOMDocument();
      $this->doc->loadHTML($html);
   }

   /**
    * @param $inputName string
    * @return mixed
    */
   public function getInputValue($inputName) {
      foreach ($this->doc->getElementsByTagName('input') as $input) {
         if ($input->getAttribute('name') == $inputName) {
            return $input->getAttribute('value');
         }
      }
      return null;
   }

   /**
    * @param $tablePrefix string
    * @return array
    */
   public function getPrefixedTableList($tablePrefix) {
      $tableList = [];
      foreach ($this->doc->getElementById('table_select')->getElementsByTagName('option') as $option) {
         if (preg_match('/^' . $tablePrefix . '/i', $option->getAttribute('value'))) {
            $tableList[] = $option->getAttribute('value');
         }
      }
      return $tableList;
   }

}