<?php

if (!isset($hdata_instance)) {
    return;
}
hdata_entry::setInstance($hdata_instance);


$query  = hdata_entry::select()->where('uid = ?', '0eb466')->order('created', 'desc')->limit(10);
$feed   = hdata_entry::query($query);

?>
<div class="sidebarlet">
  <h4>Recents</h4>
  <ul>
    <?php foreach ($feed as $val) { ?>
    <li>
      <a href="/<?=$this->reqs->urlins?>/entry?id=<?=$val['id']?>"><b><?=$val['title']?></b></a>
      <div style="color:#666">Posted on <?php echo date("Y-m-d", strtotime($val['created']));?></div>
    </li>
    <?php } ?>
  </ul>
</div>

