<?php


namespace DUT\Controllers;


use DUT\Models\Post;
use Silex\Application;
use Symfony\Component\BrowserKit\Response;

class HomeController
{

    public function displayHomePage(Application $app) {
        $posts = [new Post(1, "Mon Super Post",
                            "Novitates autem si spem adferunt, ut tamquam in herbis non fallacibus
                            zructus appareat, non sunt illae quidem repudiandae, vetustas tamen suo loco conservanda; 
                            maxima est enim vis vetustatis et consuetudinis. Quin in ipso equo, cuius modo feci mentionem, 
                            si nulla res impediat, nemo est, quin eo, quo consuevit, libentius utatur quam intractato et novo. 
                            Nec vero in hoc quod est animal, sed in iis etiam quae sunt inanima, consuetudo valet, cum locis ipsis delectemur, 
                            montuosis etiam et silvestribus, in quibus diutius commorati sumus.",
                            "21/02/1989",
                            "http://www.ouest-cornouaille.com/upload/balade_voilier_douarnenez_2012-xl.jpg"),

            new Post(2, "Mon Super Post",
                            "Novitates autem si spem adferunt, ut tamquam in herbis non fallacibus
                            zructus appareat, non sunt illae quidem repudiandae, vetustas tamen suo loco conservanda; 
                            maxima est enim vis vetustatis et consuetudinis. Quin in ipso equo, cuius modo feci mentionem, 
                            si nulla res impediat, nemo est, quin eo, quo consuevit, libentius utatur quam intractato et novo. 
                            Nec vero in hoc quod est animal, sed in iis etiam quae sunt inanima, consuetudo valet, cum locis ipsis delectemur, 
                            montuosis etiam et silvestribus, in quibus diutius commorati sumus.",
                            "21/02/1989","http://www.carantec-nautisme.com/wp-content/uploads/planche-%C3%A0-voile_2.jpg"),

            new Post(3, "Mon Super Post",
                            "Novitates autem si spem adferunt, ut tamquam in herbis non fallacibus
                            zructus appareat, non sunt illae quidem repudiandae, vetustas tamen suo loco conservanda; 
                            maxima est enim vis vetustatis et consuetudinis. Quin in ipso equo, cuius modo feci mentionem, 
                            si nulla res impediat, nemo est, quin eo, quo consuevit, libentius utatur quam intractato et novo. 
                            Nec vero in hoc quod est animal, sed in iis etiam quae sunt inanima, consuetudo valet, cum locis ipsis delectemur, 
                            montuosis etiam et silvestribus, in quibus diutius commorati sumus.",
                            "21/02/1989","https://image.jimcdn.com/app/cms/image/transf/dimension=1920x400:format=jpg/path/sac50fedda40aff1a/image/ic77338ead89b80bb/version/1456430854/image.jpg"
            )
        ];

        $mainPost = new Post(4, "Le Monologue D'Otis",
                        "Mais, vous savez, moi je ne crois pas
                                qu'il y ait de bonne ou de mauvaise situation.
                                Moi, si je devais résumer ma vie aujourd'hui avec vous,
                                je dirais que c'est d'abord des rencontres,
                                Des gens qui m'ont tendu la main,
                                peut-être à un moment où je ne pouvais pas, où j'étais seul chez moi.
                                Et c'est assez curieux de se dire que les hasards,
                                les rencontres forgent une destinée...
                                Parce que quand on a le goût de la chose,
                                quand on a le goût de la chose bien faite,
                                Le beau geste, parfois on ne trouve pas l'interlocuteur en face,
                                je dirais, le miroir qui vous aide à avancer.
                                Alors ce n'est pas mon cas, comme je le disais là,
                                puisque moi au contraire, j'ai pu ;
                                Et je dis merci à la vie, je lui dis merci,
                                je chante la vie, je danse la vie... Je ne suis qu'amour!
                                Et finalement, quand beaucoup de gens aujourd'hui me disent :
                                \"Mais comment fais-tu pour avoir cette humanité ?\",
                                Eh bien je leur réponds très simplement,
                                je leur dis que c'est ce goût de l'amour,
                                Ce goût donc qui m'a poussé aujourd'hui
                                à entreprendre une construction mécanique,
                                Mais demain, qui sait, peut-être simplement
                                à me mettre au service de la communauté,
                                à faire le don, le don de soi...",
                        "30/01/2002","http://cdn1-europe1.new2.ladmedia.fr/var/europe1/storage/images/le-lab/edouard-baer-semporte-violemment-contre-le-remaniement-du-gouvernement-2671424/25792454-1-fre-FR/Edouard-Baer-s-emporte-violemment-contre-le-remaniement-du-gouvernement.jpg"
        );

        $html = $app['twig']->render('home.twig', ['posts' => $posts, 'mainPost' => $mainPost]);
        return new Response($html);
    }

}