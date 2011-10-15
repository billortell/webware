<?php
/**
 * HMQ
 * 
 * @category  hmq
 * @package   hmq_service_feed
 */


/**
 * ensure this file is being included by a parent file
 */
defined('SYS_ENTRY') or die('Access Denied!');

/**
 * Class hmq_service_feed_entry
 * 
 * @category  hmq
 * @package   hmq_service_feed
 */
class hmq_service_feed_entry extends hmq_service_feed_abstract
{
  public function send($status = '200', $fields = array())
  {
    if (!isset($this->_status[$status])) {
      $status = '500';
    }
    
    $dom = new DOMDocument('1.0', 'utf-8');
    $dom->formatOutput = true;
    
    if (count($this->_entries) == 0) {
      $root = $dom->createElement('entry');
    } else {
      $root = $dom->importNode($this->_entries[0], true);
    }
    
    $root->setAttribute('xmlns', 'http://www.w3.org/2005/Atom');
    foreach ($this->_namespaces as $key => $val) {
      $root->setAttribute('xmlns:'.$key, $val);
    }
    
    $dom->appendChild($root);
    
    if ($status > "299") {
      $msg = $dom->createElement('hmq:error', $this->_status[$status]);
      $msg->setAttribute('code', $status); 
      $root->appendChild($msg); 
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
      header('Content-Type: application/atom+xml; charset=utf-8');
    }
    
    print $xml;
    die();
  }
}
