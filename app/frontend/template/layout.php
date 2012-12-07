<!DOCTYPE html>
<html lang="fr">
   <head>
      <title><?php echo $windowTitle; ?></title>
      <meta charset="utf-8" />
      <base href="<?php echo $root; ?>" />
      <link rel="stylesheet" type="text/css" href="css/frontend.css" />
      <link href="http://fonts.googleapis.com/css?family=Rambla:700|Open+Sans" rel="stylesheet" type="text/css">
   </head>
   <body>
      <div class="menu">
         <ul>
            <li> <a href="/admin/" id="admin">Admin</a></li>
            <li><a href="/prof/" id="prof">Professeur</a></li>
            <li><a href="/eleve/" id="eleve">Élève</a></li>
         </ul>
      </div>
      <?php echo $content; ?>
   </body>
</html>