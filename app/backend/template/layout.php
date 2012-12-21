<!DOCTYPE html>
<html lang="fr">
   <head>
      <title><?php echo $windowTitle; ?></title>
      <meta charset="utf-8" />
      <base href="<?php echo $root; ?>" />
      <link rel="stylesheet" type="text/css" href="css/backend.css" />
      <link href="http://fonts.googleapis.com/css?family=Rambla:700|Open+Sans" rel="stylesheet" type="text/css">
   </head>
   <body>
      <nav>
         <ul>
            <li><a href="/">Retour à l'accueil</a></li>
         </ul>
      </nav>
      <div id="content">
         <div id="popup">
            <?php debug(User::getPopups()); ?>
         </div>
         <?php echo $content; ?>
      </div>
   </body>
</html>