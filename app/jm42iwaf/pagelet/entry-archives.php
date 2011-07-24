<?php
//print_r($this->reqs);

if (!isset($hdata_instance)) {
    return;
}
Hooto_Web_View::headStylesheet('/_w/css/cm.css');
$this->headtitle = "Archives | {$this->headtitle}";

$uid = uname2uid($this->reqs->uname);

hdata_entry::setInstance($hdata_instance);

$cols = array('YEAR(created) AS year', 'MONTH(created) AS month', 'COUNT(*) as count');

$query  = hdata_entry::select($cols)
    ->where('uid = ?', $uid)
    ->group('YEAR(created)', 'desc')
    ->group('MONTH(created)', 'asc')
    ->limit(100000);

if (!user_session::isLogin($uid)) {
    $query->where('status = ?', jm42iwaf_entry::STATUS_PUBLISH);
} else {
    $query->where('status > ?', 0);
}

$ret = hdata_entry::query($query);

$feed = array();
foreach ((array)$ret as $val) {
    $val['month'] = sprintf("%02d", $val['month']); 
    $feed[$val['year']][$val['month']] = $val;
}


$url = $this->siteurl("/index?date=", $this->reqs->ins);
foreach ($feed as $year => $archives): ?>
<div class="entry-archives">
  <div class="hinfo">
    <h3><a href="<?=$url.$year?>"><?=$year?></a></h3>
  </div>
  <p>
    <?php foreach ($archives as $val): ?>
    <a href="<?=$url.$year.'-'.$val['month']?>"><b><?=date("F", strtotime($year.'-'.$val['month']))?></b></a>(<?=$val['count']?>)&nbsp;&nbsp;
    <?php endforeach; ?>
  </p>
  <div class="binfo"></div>
</div>
<?php endforeach; ?>
