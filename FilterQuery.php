<?php

namespace SimpleSolrAPI;

/**
 * A DSL for building filter queries with complex AND|OR conjunctions
 *
 * @method popen() Open parenthesis
 * @method pclose() Close parenthesis
 * @method and() AND conjunction
 * @method or() OR conjunction
 * @method add() Add filter
 */
class FilterQuery
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
      throw new \InvalidArgumentException("Unrecognized function");
    }
  }

  public function getFilterQuery() {
    return $this->filterQuery;
  }

  public function __toString() {
    return $this->getFilterQuery();
  }
}