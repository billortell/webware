<?php
//print_r($this->reqs);

if (!isset($hdata_instance)) {
    return;
}
Hooto_Web_View::headStylesheet('/_w/css/cm.css');

$uid = uname2uid($this->reqs->uname);

hdata_entry::setInstance($hdata_instance);

$cols = array('YEAR(created) AS year', 'MONTH(created) AS month', 'COUNT(*) as count');

$query  = hdata_entry::select($cols)
    ->where('uid = ?', $uid)
    ->group('YEAR(created)', 'desc')
    ->group('MONTH(created)', 'asc')
    ->limit(100000);

if (!user_session::isLogin($uid)) {
    $query->where('status = ?', add38b6c_entry::STATUS_PUBLISH);
}
       
$ret = hdata_entry::query($query);

$feed = array();
foreach ((array)$ret as $val) {
    $val['month'] = sprintf("%02d", $val['month']); 
    $feed[$val['year']][$val['month']] = $val;
}

//krsort($feed);
        
foreach ($feed as $key => $val) {
    //ksort($feed[$key]); 
}

?>

<?php foreach ($feed as $year => $archives): ?>
<div class="entry-archives">
  <div class="hinfo">
    <h3><a href="/node/list/date/<?=$year?>"><?=$year?></a></h3>
  </div>
  <p>
    <?php foreach ($archives as $val): ?>
    <a href="/node/list/date/<?=$year.'-'.$val['month']?>"><b><?=date("F", strtotime($year.'-'.$val['month']))?></b></a>(<?=$val['count']?>)&nbsp;&nbsp;
    <?php endforeach; ?>
  </p>
  <div class="binfo"></div>
</div>
<?php endforeach; ?>
