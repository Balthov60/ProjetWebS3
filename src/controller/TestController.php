<?php
namespace DUT\controller;

use DUT\model\Card;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Silex\Application;

class TestController
{
    public function displayHomepage(Application $application)
    {
        $cards = [new Card("
        http://www.ouest-cornouaille.com/upload/balade_voilier_douarnenez_2012-xl.jpg"
        ,"Novitates autem si spem adferunt, ut tamquam in herbis non fallacibus
         fructus appareat, non sunt illae quidem repudiandae, vetustas tamen suo loco conservanda; 
         maxima est enim vis vetustatis et consuetudinis. Quin in ipso equo, cuius modo feci mentionem, 
         si nulla res impediat, nemo est, quin eo, quo consuevit, libentius utatur quam intractato et novo. 
         Nec vero in hoc quod est animal, sed in iis etiam quae sunt inanima, consuetudo valet, cum locis ipsis delectemur, 
         montuosis etiam et silvestribus, in quibus diutius commorati sumus."),

            new Card("http://www.carantec-nautisme.com/wp-content/uploads/planche-%C3%A0-voile_2.jpg"
        , "Quin in ipso equo, cuius modo feci mentionem, 
         si nulla res impediat, nemo est, quin eo, quo consuevit, libentius utatur quam intractato et novo. 
         Nec vero in hoc quod est animal, sed in iis etiam quae sunt inanima, consuetudo valet, cum locis ipsis delectemur, 
         montuosis etiam et silvestribus, in quibus diutius commorati sumus."),


            new Card("https://image.jimcdn.com/app/cms/image/transf/dimension=1920x400:format=jpg/path/sac50fedda40aff1a/image/ic77338ead89b80bb/version/1456430854/image.jpg"
            , "Nec vero in hoc quod est animal, sed in iis etiam quae sunt inanima, consuetudo valet, cum locis ipsis delectemur, 
         montuosis etiam et silvestribus, in quibus diutius commorati sumus.")
        ];

        $html = $application['twig']->render('home.twig', ['cards' => $cards]);
        return new Response($html);
    }
}