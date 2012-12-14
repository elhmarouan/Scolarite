
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
   
   
   
      
   <center><h1>Accueil promotions</h1></center>
   <center>Choisissez une action:</center>
   <center><ul id="menu">
      <li><a href="">Liste des étudiants</a></li>
      <li><a href="/prof/<?php echo $promo; ?>/modules">Liste des modules</a></li>
   </ul></center>
   
