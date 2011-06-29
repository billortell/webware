<?php
if (!isset($this->reqs->uname)) {
    return;
}
$uid = uname2uid($this->reqs->uname);

if (!isset($hdata_instance)) {
    return;
}
hdata_entry::setInstance($hdata_instance);


$query  = hdata_entry::select()->where('uid = ?', $uid)->order('created', 'desc')->limit(10);
$feed   = hdata_entry::query($query);

?>
<div class="sidebarlet">
  <h4>Recents</h4>
  <ul>
    <?php
    foreach ($feed as $val) {
    $link = $this->siteurl("/entry?id={$val['id']}", $this->reqs->ins);
    ?>
    <li>
      <a href="<?=$link?>"><b><?=$val['title']?></b></a>
      <div style="color:#666">Posted on <?php echo date("Y-m-d", strtotime($val['created']));?></div>
    </li>
    <?php } ?>
  </ul>
</div>

