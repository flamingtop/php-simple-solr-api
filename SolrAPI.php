<?php

namespace SimpleSolrAPI;

require_once(__DIR__."/QueryString.php");

/**
 * Solr HTTP API wrapper
 *  
 * @examples
 *
 * <code>
 *   $qs = new QueryString();
 *
 *   $qs->q('Vincent Van Gogh');
 *   $qs->hl('true');
 *   $qs->hl_fl('*');
 *
 *   $qs->q = "Vincent Van Gogn";
 *   $qs->hl('true');
 *   $qs->hl_fl('*');
 *   
 *   Output
 *   q=Vincent%20Van%20Gogh&hl=true&hl.fl=*
 * </code>
 */
class SolrAPI extends QueryString
{
  /**
   * @var string Solr Service Root URL
   */
  public $serviceBase = NULL;

  public function __construct($serviceBase='') {
    $this->serviceBase = $serviceBase;
  }

  public function toParamKey($string) {
    $key = $string;
    (strpos($string, '_') !== FALSE) && $key = str_replace("_", ".", $string);
    return $key;
  }
}