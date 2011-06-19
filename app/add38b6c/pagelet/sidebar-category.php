<?php

$where = array('taxon' => 1, 'gid' => '0eb466');
$taxon_cats = hdata_taxonomy::fetchTerms($where);

?>
<div class="sidebarlet">
  <h4>Categories</h4>
  <ul>
    <?php foreach ($taxon_cats as $key => $val) { ?>
    <li style="padding-left: <?php echo 20 * $val['_level']?>px">
      <a href="<?=$this->reqs->urlins?>/index?term=<?=$val['id']?>"><b><?=$val['name']?></b></a>
    </li>
    <?php } ?>
  </ul>
</div>

