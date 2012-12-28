<?php

/**
 * A general abstraction of a HTTP query string
 * 
 * Each query parameter can be added with a function call 
 * or property assignemnt. In which the function call or the left
 * value of the assignment is passed to toParamKey() which 
 * turns the input into a sensible key of a query parameter.
 *
 * 
 * By default, toParamKey() does no conversion, actual conversion
 * logic should be implemented by overriding this method in sub-classes.
 *
 */
class QueryString
{
  /**
   * @var array $params
   */
  public $params = array();
  
  /**
   * Set arbitrary query parameter with a function call
   *
   * @param string $func Function name
   * @param array $args Function arguments
   */
  public function __call($func, $args) {
    $paramKey   = $this->toParamKey($func);
    $paramValue = $args[0];
    if (!is_string($paramValue)) {
      throw new InvalidArgumentException("Argument must be a string");
    }
    $this->setParam($paramKey, $paramValue);
  }
  
  /**
   * Set arbitrary query parameter with assignment
   *
   * @param string $name
   * @param string $value
   */  
  public function __set($name, $value) {
    $paramKey   = $this->toParamKey($name);
    if (!is_string($value)) {
      throw new InvalidArgumentException("Argument must be a string");
    }
    $this->setParam($paramKey, $value);
  }

  /**
   * Set parameter of specified key to value
   *
   * @param string $key
   * @param string $value
   */
  public function setParam($key, $value) {
    if (!isset($this->params[$key])) {
      $this->params[$key] = array();
    }
    $this->params[$key][] = $value;
  }

  /**
   * Get parameter of specifc key
   *
   * @param string $key
   * @return array
   */
  public function getParam($key) {
    return $this->param[$key];
  }

  /**
   * Get all parameters
   *
   * @return array
   */
  public function getParams() {
    return $this->params;
  }

  /**
   * Get the final query string assembled from all the parameters
   * 
   * @return string The query string
   */  
  public function getQueryString() {
    $params = array();
    foreach ($this->params as $key=>$items) {
        foreach($items as $item) {
          $params[] = $key.'='.urlencode($item);
        }
    }
    $queryString = implode('&', $params);
    return $queryString;
  }
  
  /**
   * Takes an input string and converts it to a proper parameter key
   *
   * Override this method to provide more sophisticated
   * converting logic
   *
   * @param string $string
   * @return string
   */
  public function toParamKey($string) {
    return $string;
  } 
}

/**
 * Very Thin Solr HTTP API wrapper
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
  public function toParamKey($string) {
    $key = $string;
    (strpos($string, '_') !== FALSE) && $key = str_replace("_", ".", $string);
    return $key;
  }
}

/**
 * A DSL for building filter queries with complex AND|OR conjunctions
 *
 * @method popen() Open parenthesis
 * @method pclose() Close parenthesis
 * @method and() AND conjunction
 * @method or() OR conjunction
 * @method add() Add filter
 */
class SolrAPIComplexFilterQuery
{
  private $filterQuery = '';

  public function __call($func, $args) {
    switch ($func) {
    case 'popen':
      $this->filterQuery .= '(';
      break;
    case 'pclose':
      $this->filterQuery .= ')';
      break;
    case 'and':
      $this->filterQuery .= ' AND ';
      count($args) && $this->add($args[0], $args[1]);
      break;
    case 'or':
      $this->filterQuery .= ' OR ';
      count($args) && $this->add($args[0], $args[1]);
      break;
    case 'add':
      $this->filterQuery .= $args[0] . ':' . $args[1];
      break;
    default:
      throw new InvalidArgumentException("Unrecognized function");
    }
  }

  public function getFilterQuery() {
    return $this->filterQuery;
  }

  public function __toString() {
    return $this->getFilterQuery();
  }
}