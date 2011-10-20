<?php
/**
 * Module hwl
 * 
 * @category   hwl
 * @package    hwl_httpclient
 */

/**
 * Class hwl_httpclient
 *
 * Example: GET
 *  $client = new hwl_httpclient('http://www.example.com/get.php?var=value');
 *  if ($client->doGet() == 200) {
 *      $response = $client->getBody();
 *  }
 *
 * Example: POST/PUT
 *  $client = new hwl_httpclient('http://www.example.com/post.php);
 *  $data = '<?xml version="1.0" encoding="utf-8"?>
 *      <feed xmlns="http://www.w3.org/2005/Atom">
 *          <entry>...</entry>
 *      </feed>';
 *  if ($client->doPost($data) == 200) {
 *      $response = $client->getBody();
 *  }
 * 
 * @category   hwl
 * @package    hwl_httpclient
 */
class hwl_httpclient
{
  protected $_uri      = '';
  protected $_headers  = array('Accept' => '');
  protected $_body     = NULL;

  protected $_conn     = NULL;

  protected $_timeout  = 60;

  const AUTH_TEMPLATE = 'Authorization: auth="?"';
  const AUTH_DIVIDE = ':';

  /**
   * Content attributes
   */
  const CONTENT_TYPE   = 'Content-Type';
  const CONTENT_LENGTH = 'Content-Length';

  public function __construct($uri = NULL)
  {
    $this->_uri = $uri;
  }

  public function setUri($uri)
  {
    $this->_uri = $uri;
  }

  public function setHeader($k, $v)
  {
    $this->_headers[$k] = $v;
  }

  public function setTimeout($v)
  {
    $this->_timeout = $v;
  }

  protected function _conn($uri = NULL)
  {
    if ($this->_conn !== NULL && $uri === NULL) {
      return;
    }

    if ($uri !== NULL) {
      $this->_uri = $uri;
    }

    $this->_conn = curl_init();

    curl_setopt($this->_conn, CURLOPT_URL, $this->_uri);
    //curl_setopt($this->_conn, CURLOPT_HEADER, true);
    curl_setopt($this->_conn, CURL_HTTP_VERSION_1_1, true);
    curl_setopt($this->_conn, CURLOPT_CONNECTTIMEOUT, $this->_timeout);
    curl_setopt($this->_conn, CURLOPT_ENCODING , "gzip");
    curl_setopt($this->_conn, CURLOPT_USERAGENT, 'hwl_httpclient 1.0');
    curl_setopt($this->_conn, CURLOPT_RETURNTRANSFER, true);
  }

  final public function close()
  {
    if ($this->_conn !== NULL) {
      curl_close($this->_conn);
    }
  }

  final public function doGet($body = null)
  {
    $this->_conn();

    curl_setopt($this->_conn, CURLOPT_HTTPGET, true);

    return $this->_request($body);
  }

  final public function doPost($body)
  {
    $this->_conn();

    $this->setHeader(self::CONTENT_LENGTH, strlen($body));
    curl_setopt($this->_conn, CURLOPT_POST, true);
    curl_setopt($this->_conn, CURLOPT_POSTFIELDS, $body);

    return $this->_request($body);
  }

  final public function doPut($body)
  {
    $this->_conn();

    $this->setHeader(self::CONTENT_LENGTH, strlen($body));
    curl_setopt($this->_conn, CURLOPT_CUSTOMREQUEST, 'PUT');
    curl_setopt($this->_conn, CURLOPT_POSTFIELDS, $body);

    return $this->_request($body);
  }

  final public function doDelete($body)
  {
    $this->_conn();

    curl_setopt($this->_conn, CURLOPT_CUSTOMREQUEST, 'DELETE');

    return $this->_request($body);
  }

  protected function _request($body)
  {
    curl_setopt($this->_conn, CURLOPT_HTTPHEADER, $this->_headers);

    $this->_body = curl_exec($this->_conn);

    return curl_getinfo($this->_conn, CURLINFO_HTTP_CODE);
  }

  final public function getBody()
  {
    return $this->_body;
  }

  final public function getInfo()
  {
    return curl_getinfo($this->_conn);
  }
}
