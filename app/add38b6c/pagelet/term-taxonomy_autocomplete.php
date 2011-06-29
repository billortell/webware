<?php
//print_r($this->reqs);

if (!isset($hdata_instance)) {
    return;
}
hdata_entry::setInstance($hdata_instance);

Hooto_Web_View::headStylesheet('/_w/css/cm.css');

$metadata = Hooto_Config_Array::get("hdata/entry{$hdata_instance}");

$taxon = $metadata['taxonomy']['tag']['id'];

$uid = uname2uid($this->reqs->uname);

$db = Hooto_Data_Sql::getTable('term_data');

$query = $db->select()
    ->where('taxon = ?', $taxon)
    ->where('gid = ?', $uid)
    ->order('rating', 'desc')
    ->limit(300);

$feed = $db->query($query);

$max = current($feed);
$max = $max['rating'];
$min = end($feed);
$min = $min['rating'];
$len = $max - $min + 1;

shuffle($feed);

echo "<div class=\"contentbox entry-tag-list\">";        
foreach ($feed as $key => $val) {
    $wcount = floor(($val['rating'] - $min) / ($len * 0.1));
    echo "<span><a class=\"term{$wcount}\">{$val['name']}</a></span>";        
}
echo "</div>";

