<?php

if (!isset($this->reqs->uname)) {
    return;
}
$puid = uname2uid($this->reqs->uname);

if (!isset($hdata_instance) || !isset($hdata_instance_parent)) {
    return;
}

hdata_entry::setInstance($hdata_instance);


$query  = hdata_entry::select()->where('puid = ?', $puid)
    ->where('pinstance = ?', $hdata_instance_parent)
    ->where('status > 0')
    ->order('created', 'desc')->limit(10);
$feed   = hdata_entry::query($query);

?>
<div class="sidebarlet nounderline">
  <h4>Recent Comments</h4>
  <?php
  foreach ($feed as $val) { 
    $val['content'] = Hooto_Util_Format::summaryPlainText($val['content'], 100);
  ?>
  <div class="comments-info">
    <img src="/_w/img/fffam/comment.png" align="absmiddle" />
    <b><?=$val['uname']?></b>@<?php echo date("Y-m-d", strtotime($val['created']));?>
  </div>
  <div class="comments-summary">
    <a href="<?=$this->siteurl("/view/{$val['pid']}.html#{$val['id']}")?>"><?=$val['content']?></a>
  </div>
  <?php } ?>
</div>

