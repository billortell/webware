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
    ->order('created', 'desc')->limit(10);
$feed   = hdata_entry::query($query);

?>
<div class="sidebarlet">
  <h4>Recent Comments</h4>
  <ul class="nounderline">
    <?php
    foreach ($feed as $val) { 
    $val['content'] = Hooto_Util_Format::summaryPlainText($val['content'], 100);
    ?>
    <li>      
      <a href="<?=$this->reqs->urlins?>/entry?id=<?=$val['pid']?>#<?=$val['id']?>"><b><?=$val['content']?></b></a>
      <div style="color:#666"><b><?=$val['uname']?></b>@<?php echo date("Y-m-d", strtotime($val['created']));?></div>
    </li>
    <?php } ?>
  </ul>
</div>

