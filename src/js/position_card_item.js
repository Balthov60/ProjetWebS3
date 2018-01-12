$(document).ready(function()
{
   var cpt=0;
   $(".card-container").each(function()
   {
       $(this).css("margin-left", (Math.floor(Math.random() * Math.floor(10)) + 40*(cpt%2) + -3) + "%");
       cpt++;
       alert($(this).css("height"));
   })
});