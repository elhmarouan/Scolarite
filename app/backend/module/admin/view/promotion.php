<?php if ($addPromo) : ?>
   <h1>Ajouter une promotion</h1>
   <form method="post" action="">
      <label>Intitulé</label> <input type="text" name="libelle" /><br />
      <input type="submit" value="Ajouter !" />
   </form>
   <p><a href="/admin/promos"> Retour à la liste des promotions</a></p>
<?php elseif ($editPromo) : ?>
   <h1>Modifier une promotion</h1>
   <form method="post" action="">
      <label>Intitulé</label> <input type="text" name="libelle" value="<?php echo $promo; ?>" /><br />
      <input type="submit" value="Modifier !" />
   </form>
   <p><a href="/admin/<?php echo $promo; ?>">Retour à la gestion de la promotion</a></p>
<?php elseif ($managePromo) : ?>
   <h1>Gestion de la promotion <?php echo $promo; ?></h1>
   <p><a href="/admin/<?php echo $promo; ?>/supprimer" class="button redButton" onClick="return confirm('Êtes-vous sûr de vouloir supprimer cette promotion ?');">Supprimer cette promotion</a> <a href="/admin/<?php echo $promo; ?>/modifier" class="button orangeButton">Modifier la promotion</a></p>
   <ul>
      <li><a href="/admin/<?php echo $promo; ?>/étudiants">Liste des étudiants</a></li>
      <li><a href="/admin/<?php echo $promo; ?>/modules">Gestion des modules</a></li>
   </ul>
   <p><a href="/admin/promos">Retour à la gestion des promotions</a></p>
<?php else : ?>
   <h1>Gestion des promotions</h1>
   <p><a href="/admin/promo/ajouter" class="button greenButton">Ajouter une promotion</a></p>
   <ul>
      <?php
      if (!empty($promosList)) :
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