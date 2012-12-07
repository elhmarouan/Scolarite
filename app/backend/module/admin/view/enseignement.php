<?php if ($addModule) : ?>
   <h1>Ajouter un module</h1>
   <form method="post" action="/admin/<?php echo $promo; ?>/modules/ajouter">
      Nom du module : <input type="text" name="NameModule" placeholder="ex: Informatique" /><br />
      <input type="submit" value="Ajouter !" />
   </form>
<?php elseif ($manageMatiere) : ?>
   <h1>Gestion de la matière <?php echo $matiere; ?></h1>
<?php elseif ($manageModule) : ?>
   <h1>Gestion du module <?php echo $module; ?></h1>
<?php else : ?>
   <h1>Gestion des modules de <?php echo $promo; ?></h1>
   <p><a href="/admin/<?php echo $promo; ?>/modules/ajouter">Ajouter un nouveau module</a></p>
   <ul>
      <?php foreach ($listeDesModules as $module) : ?>
         <li><a href="/admin/<?php echo $promo; ?>/<?php echo $module['name']; ?>"><?php echo $module['name']; ?></a></li>
      <?php endforeach; ?>
   </ul>
<?php endif; ?>