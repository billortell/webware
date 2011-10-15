<?php

/**
 *  http://www.sklar.com/badgerfish/
 */

class hmq_service_json
{
    public static $ns = array();
    
    public static function fromXml($xmlStringContents) 
    {        
        $dom = new DOMDocument('1.0', 'utf-8');
        $dom->loadXML($xmlStringContents);
        
        $array = self::encode($dom);
        
        return json_encode($array);
    }
    
    public static function encode(DOMNode $node, $level = 0) 
    {
        static $xpath;

        if (is_null($xpath)) {
            $xpath = new DOMXPath($node);
        }
        
        if ($node->childNodes) {
        
            $r = array();
            $text = '';
            foreach ($node->childNodes as $child) {
                $idx = str_replace(':', '$', $child->nodeName);
                if (! is_null($cr = self::encode($child, $level+1))) {
                    if (($child->nodeType == XML_TEXT_NODE)||($child->nodeType == XML_CDATA_SECTION_NODE)) {
                        $text .= $cr;
                    } else {
                        $r[$idx][] = $cr;
                    }
                }
            }
            
            // Reduce 1-element numeric arrays
            foreach ($r as $idx => $v) {
                if (is_array($v) && (count($v) == 1) && isset($v[0])) {
                    $r[$idx] = $v[0];
                }
            }
            
            // Any accumulated text that isn't just whitespace?
            if (strlen(trim($text))) { $r['$t'] = $text; }

            // Attributes?
            if ($node->attributes && $node->attributes->length) {
                foreach ($node->attributes as $attr) {
                    $r[$attr->nodeName] = $attr->value;
                }
            }
            
            // Namespaces?
            foreach ($xpath->query('namespace::*[name() != "xml"]', $node) as $ns) {
                if ($ns->localName == 'xmlns') {
                    self::$ns['xmlns'] = $ns->namespaceURI;
                } else {
                    self::$ns['xmlns$'.$ns->localName] = $ns->namespaceURI;
                }
            }
        }
        // No children -- just return text;
        else {
            if (($node->nodeType == XML_TEXT_NODE)||($node->nodeType == XML_CDATA_SECTION_NODE)) {
                return $node->textContent;
            }
        }
        
        if ($level == 0) {
            $xpath = null;
            
            $r0 = array('version' => '1.0', 'encoding' => 'utf-8');

            $name = $node->firstChild->nodeName;

            arsort(self::$ns);
            $r0[$name] = array_merge(self::$ns, $r[$name]);
    
            return $r0;
        } else {
            return $r;
        }
    }
}
