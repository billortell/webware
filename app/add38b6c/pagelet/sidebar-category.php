<?php
if (!isset($this->reqs->uname)) {
    return;
}
$uid = uname2uid($this->reqs->uname);

$where = array('taxon' => 1, 'gid' => $uid);
$taxon_cats = hdata_taxonomy::fetchTerms($where);

?>
<div class="sidebarlet">
  <h4>Categories</h4>
  <ul>
    <?php
    foreach ($taxon_cats as $key => $val) { 
    $link = $this->siteurl("/index?term={$val['id']}", $this->reqs->ins);
    ?>
    <li style="padding-left: <?php echo 20 * $val['_level']?>px">
      <a href="<?=$link?>"><b><?=$val['name']?></b></a>
    </li>
    <?php } ?>
  </ul>
</div>

