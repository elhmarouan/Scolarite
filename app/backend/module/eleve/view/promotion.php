<?php if ($manageClass) : ?>
   <h1>Consulter les notes de la promotion <?php echo $promo; ?></h1>
   <ul>
      <li><a href="<?php echo $promo; ?>/notesm">Notes de la promotion par matière</a></li>
   </ul>   
   <h1>Consulter des moyennes de la promotion <?php echo $promo; ?></h1>
   <ul>
      <li><a href="<?php echo $promo; ?>/moyennesm">Moyennes de la promotion par matière</a></li>
   </ul>
<?php else : ?>
        <li><a href="/eleve/"></a></li>
   </ul>
<?php endif; ?>

   

      