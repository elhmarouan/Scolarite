<?php if ($manageClass) : ?>
   <h1>Gestion de la promotion <?php echo $promo; ?></h1>
   <ul>
      <li><a href="/admin/<?php echo $promo; ?>/etudiants">Gestion des étudiants</a></li>
      <li><a href="/admin/<?php echo $promo; ?>/modules">Gestion des modules</a></li>
   </ul>
<?php else : ?>
   <h1>Gestion des promotions</h1>
   <ul>
      <li><a href="/admin/cpi1">cpi1</a></li>
      <li><a href="/admin/cpi2">cpi2</a></li>
   </ul>
<?php endif; ?>