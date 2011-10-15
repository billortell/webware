<?php
/**
 * REST API
 *
 */

//$rest = new hmq_service_feed();

//$tm = hdata_rds_service::getTable('hmq_message');
//$tq = hdata_rds_service::getTable('hmq_queue');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

  try {
    $xml = file_get_contents("php://input");
    $struct = @simplexml_load_string($xml);
  } catch (Exception $e) {
    $rest->send(400);
  }
  
  if (!isset($struct->entry)) {
    $rest->send(400);
  }
  
  //$conf = require SYS_ENTRY .'/etc/global.php';
  
  foreach ($struct->entry as $entry) {
    
    $dom = new DOMDocument('1.0', 'utf-8');
    $dom->formatOutput = true;
    $sxe = dom_import_simplexml($entry);
    $sxe = $dom->importNode($sxe, true);
    $sxe = $dom->appendChild($sxe);
    $str = $dom->saveXML();
    
  }
  $rest->send(200);
  
} else if ($_SERVER['REQUEST_METHOD'] == 'GET') {
  
  $dq = hds_kv::open("hmqdemo");
  $k = hwl_string::rand();
  $v = hwl_string::rand(64);
  //$dq->set($k, $v);
  $dq->lPush('hmq', $v);
  print_r($dq);
  echo __LINE__;
  //echo $this->reqs->qid;
  
} else if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {

  echo $this->reqs->qid;
  
} else {
  $rest->send(404);
}
