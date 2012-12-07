<style>
   #menu{
      list-style-type: none; /* On enlève les vieilles puces toutes pourries */ 
      padding: 0; 
      width: 20%;  /* On met une largeur pour pas que le tableau prenne toute la place */
      border : solid #1D5CBC;  /* On applique des bordures à ul */
      border-bottom: none; /* mais on retire celle du bas, parce que quand on va appliquer à li des bordures en bas, faut pas que la bordure appliquée à ul, et celle aplliquée à li se superposent et donne un truc chelou */
   }

   #menu li{
      padding: 0; 
      border-bottom: solid #1D5CBC;  /* On applique des bordures sous chaque ligne */
   }

   #menu a{
      text-decoration: none;  /* On enlève le soulignement sous chaque lien */
      display: block; /* Pour qu'une fois le hover appliqué, toute la case est surlignée et pas que le texte" */
      color: #1D5CBC; /* couleur des liens */
   }

   #menu a:hover{
      background-color: pink; /* couleur du surlignage */
      color: #1D5CBC; /* couleur des liens quand il y a le surlignage. On garde la même couleur que quand y a pas de surlignage. */
   }
</style>

<h1><center>Gestion des modules <?php echo $modules; ?></center></h1>
<center><ul id="menu">
      <li><a href="http://www.scolarite.fr.nf/prof/cpi1/Mathematiques">Mathématiques</a></li>
      <li><a href="http://www.scolarite.fr.nf/prof/cpi1/Physique">Physique</a></li>
      <li><a href="http://www.scolarite.fr.nf/prof/cpi1/Informatique">Informatique</a></li>
      <li><a href="http://www.scolarite.fr.nf/prof/cpi1/Francais">Français</a></li>
      <li><a href="http://www.scolarite.fr.nf/prof/cpi1/Anglais">Anglais</a></li>
      <li><a href="http://www.scolarite.fr.nf/prof/cpi1/Electronique">Electronique</a></li>
      <li><a href="http://www.scolarite.fr.nf/prof/cpi1/TIPE">TIPE</a></li>
   </ul></center>