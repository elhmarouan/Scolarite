<h1>Accueil professeurs</h1>
Veuillez sélectionner la promotion :
<ul>
   <?php
   if (!empty($promosList)) :
      foreach ($promosList as $promo) :
         ?>
         <li><a href="/prof/<?php echo $promo; ?>"><?php echo $promo; ?></a></li>
         <?php
      endforeach;
   else :
      ?>
      <li>Aucune promotion disponible</li>
   <?php endif; ?>
</ul>

<p><a href="/">Retour à l'accueil</a></p>