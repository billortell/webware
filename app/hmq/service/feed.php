<?php
/**
 * HMQ Web Service
 * 
 * @category   hmq
 * @package  hmq_service_feed
 */


/**
 * Class hmq_service_feed
 * 
 * @category   hmq
 * @package  hmq_service_feed
 */
class hmq_service_feed extends hmq_service_feed_abstract
{
  public function send($status = '200', $fields = array())
  {
    if (!isset($this->_status[$status])) {
      $status = '500';
    }
    
    $dom = new DOMDocument('1.0', 'utf-8');
    $dom->formatOutput = true;
    
    $root = $dom->createElement('feed');
    $dom->appendChild($root);
    
    $root->setAttribute('xmlns', 'http://www.w3.org/2005/Atom');
    foreach ($this->_namespaces as $key => $val) {
      $root->setAttribute('xmlns:'.$key, $val);
    }
    
    $gene = $dom->createElement('generator', self::GENERATOR);
    $gene->setAttribute('version', self::VERSION); 
    $root->appendChild($gene);
    
    if ($status > "299") {
      $msg = $dom->createElement('hmq:error', $this->_status[$status]);
      $msg->setAttribute('code', $status); 
      $root->appendChild($msg);
    }
    
    if (count($this->_entries) > 0) {
      foreach ($this->_entries as $node) {
        $node = $dom->importNode($node, true);
        $dom->documentElement->appendChild($node);
      }
    }
    
    header("HTTP/1.1 $status {$this->_status[$status]}");
    
    $xml = $dom->saveXML();
    
    if (isset($_GET['alt']) && $_GET['alt'] == 'json') {
      header('Content-Type: application/json; charset=utf-8');
      $xml = hmq_service_json::fromXml($xml);
      if (isset($_GET['callback'])) {
        $xml = "{$_GET['callback']}($xml)";
      }
    } else {
      header('Content-Type: text/xml; charset=utf-8');
      //header('Content-Type: application/atom+xml; charset=utf-8');
    }
    
    print $xml;
    die();
  }
}
