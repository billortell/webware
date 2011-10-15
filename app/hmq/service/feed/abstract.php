<?php
/**
 * HMQ
 * 
 * @category  hmq
 * @package   hmq_service_feed
 */

/**
 * Class hmq_service_feed_abstract
 * 
 * @category  hmq
 * @package   hmq_service_feed
 */
abstract class hmq_service_feed_abstract
{
  const GENERATOR   = "HQM";
  const VERSION     = "1.0.0";
  
  protected $_status  = array(
    '200' => 'OK',
    '201' => 'CREATED',
    '202' => 'ACCEPTED',
    '400' => 'BAD REQUEST',
    '401' => 'UNAUTHORIZED',
    '403' => 'FORBIDDEN',
    '404' => 'NOT FOUND',
    '500' => 'INTERNAL SERVER ERROR',
  );
  
  protected $_namespaces = array(
    //'atom' => 'http://www.w3.org/2005/Atom',
  );
  
  protected $_entries = array();

  public function addNamespaces($key, $val)
  {
    if (!isset($this->_namespaces[$key])) {
      $this->_namespaces[$key] = $val;
    }
  }
  
  public function addEntry($doc)
  {
    if (is_string($doc)) {
    
      $dom = new DOMDocument('1.0', 'utf-8');
      $dom->formatOutput = true;
      $dom->loadXML($doc);
      $element = $dom->getElementsByTagName('entry')->item(0);
      
      $sx = simplexml_import_dom($element);
      $ns = $sx->getDocNamespaces(true);
      
      foreach ($ns as $nkey => $nval) {
        
        //$element->removeAttribute('xmlns:'.$nkey); // ISSUE
        
        if (strlen($nkey) == 0 || isset($this->_namespaces[$nkey])) {
          continue;
        }
        $this->addNamespaces($nkey, $nval);
      }
      
      $this->_entries[] = $element;
    }
  }
}
