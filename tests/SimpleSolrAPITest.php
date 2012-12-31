<?php

require_once(__DIR__."/../SolrAPI.php");
use \SimpleSolrAPI\SolrAPI;



class SolrAPITest extends PHPUnit_Framework_TestCase
{
  public function testSolrAPI() {
    $api = new SolrAPI;
    $times = 500;
    for($i=0; $i<$times; $i++) {
      $param = $this->generate_solr_style_param_name();
      $func = str_replace('.', '_', $param);
      try {
        $api->$func('');
        $params = $api->getParams();
        $this->assertTrue(isset($params[$param]));
      } catch (PHPUnit_Framework_Error $e) {
        $this->fail("Call to $func() failed");
      }
    }
  }

  private function generate_solr_style_param_name() {
    $letters = implode('', array_merge(range('a', 'z'), range('A','Z')));

    $pieces = array();
    $pieces_count = rand(1,3);
    for($i=1; $i<=$pieces_count; $i++) {
      $n_letters_per_piece = rand(1,10);
      $p = '';
      for($j=0; $j<$n_letters_per_piece; $j++) {
        $p .= $letters[rand(0, strlen($letters)-1)];
      }
      $pieces[] = $p;
    }
    return implode('.', $pieces);
  }
}