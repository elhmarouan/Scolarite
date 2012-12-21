<?php if ($addMatiere) : ?>
   <h1>Ajouter une matière</h1>
   <form method="post" action="/admin/<?php echo $promo; ?>/<?php echo $module; ?>/matières/ajouter">
      Nom de la matière : <input type="text" name="libelle" placeholder="ex: Physique" /><br />
      Coefficient : <input type="number" name="coef" /><br />
      <input type="submit" value="Ajouter !" />
   </form>
<?php elseif ($editMatiere) : ?>
   <h1>Modifier une matière</h1>
   <form method="post" action="/admin/<?php echo $promo; ?>/<?php echo $module; ?>/<?php echo $matiere; ?>/modifier">
      Nom de la matière : <input type="text" name="libelle" value="<?php echo $matiere; ?>" /><br />
      Coefficient : <input type="number" name="coef" value="<?php echo $coef; ?>" /><br />
      <input type="submit" value="Modifier !" />
   </form>
<?php elseif ($addModule) : ?>
   <h1>Ajouter un module</h1>
   <form method="post" action="/admin/<?php echo $promo; ?>/modules/ajouter">
      Nom du module : <input type="text" name="libelle" placeholder="ex: Informatique" /><br />
      <input type="submit" value="Ajouter !" />
   </form>
<?php elseif ($editModule) : ?>
   <h1>Modifier un module</h1>
   <form method="post" action="/admin/<?php echo $promo; ?>/<?php echo $module; ?>/modifier">
      Nom du module : <input type="text" name="libelle" value="<?php echo $module; ?>" /><br />
      <input type="submit" value="Modifier !" />
   </form>
<?php elseif ($manageMatiere) : ?>
   <h1>Gestion de la matière <?php echo $matiere; ?></h1>
<?php elseif ($manageModule) : ?>
   <h1>Gestion du module <?php echo $module; ?></h1>
   <p><a href="/admin/<?php echo $promo; ?>/<?php echo $module; ?>/matières/ajouter">Ajouter un nouvelle matière</a> - <a href="/admin/<?php echo $promo; ?>/<?php echo $module; ?>/modifier">Modifier ce module</a> - <a href="/admin/<?php echo $promo; ?>/<?php echo $module; ?>/supprimer">Supprimer ce module</a></p>
   <ul>
      <?php
      if (!empty($listeDesMatieres)) :
         foreach ($listeDesMatieres as $matiere) :
            ?>
            <li><a href="/admin/<?php echo $promo; ?>/<?php echo $module; ?>/<?php echo $matiere; ?>"><?php echo $matiere; ?></a></li>
            <?php
         endforeach;
      else :
         ?>
         <li>Aucune matière disponible</li>
      <?php endif; ?>
   </ul>
   <p><a href="/admin/<?php echo $promo; ?>/modules">Retour à la gestion des modules</a></p>
<?php else : ?>
   <h1>Gestion des modules <?php echo $prefixPromo . $promo; ?></h1>
   <p><a href="/admin/<?php echo $promo; ?>/modules/ajouter">Ajouter un nouveau module</a></p>
   <ul>
      <?php
      if (!empty($listeDesModules)) :
         foreach ($listeDesModules as $module) :
            ?>
            <li><a href="/admin/<?php echo $promo; ?>/<?php echo $module; ?>/matières"><?php echo $module; ?></a></li>
            <?php
         endforeach;
      else :
         ?>
         <li>Aucun module disponible</li>
      <?php endif; ?>
   </ul>
   <p><a href="/admin/<?php echo $promo; ?>">Retour à la gestion de la promotion</a></p>
<?php endif; ?>