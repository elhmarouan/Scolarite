<?php if ($addStudent) : ?>
   <h1>Ajouter un étudiant</h1>
<?php else : ?>
   <h1>Gestion des étudiants <?php echo $prefixPromo . $promo; ?></h1>
   <p><a href="/admin/<?php echo $promo; ?>/étudiants/ajouter">Ajouter un étudiant</a></p>
<?php endif; ?>