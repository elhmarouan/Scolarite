<?php if ($manageClass) : ?>
   <h1>Gestion de la promotion <?php echo $promo; ?></h1>
   <ul>
      <li><a href="/admin/<?php echo $promo; ?>/etudiants">Gestion des étudiants</a></li>
      <li><a href="/admin/<?php echo $promo; ?>/modules">Gestion des modules</a></li>
   </ul>
<?php else : ?>
   <h1>Gestion des promotions</h1>
   <ul>
      <?php if (!empty($promosList)) :
         foreach ($promosList as $promo) :
            ?>
            <li><a href="/admin/<?php echo $promo; ?>"><?php echo $promo; ?></a></li>
            <?php
         endforeach;
      else :
         ?>
         <li>Aucune promotion disponible</li>
   <?php endif; ?>
   </ul>
   <p><a href="/admin/">Retour à l'accueil du panel d'administration</a></p>
<?php endif; ?>