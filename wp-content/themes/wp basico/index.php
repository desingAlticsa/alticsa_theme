
<?php get_header();?>

    <style>
        #pWelcome {
            background: linear-gradient(rgba(255, 255, 255, 0.94), rgba(255, 255, 255, 0.94)), url(wp-content/themes/wp basico/assets/images/tapiz_fondo.jpg);
            background-size: 30%;
        }

        #pServicios {
            background: linear-gradient(75deg, #002536, #005075);
        }

        #pServicios div.card {
            box-shadow: 0 0 8px white;
        }

        #pServicios div.card a {
            color: var(--color4);
        }

        #pServicios div.card:hover a {
            color: var(--color2);
        }

        #pServicios div.crop {
            height: 240px;
            max-height: 240px;
            overflow: hidden;
        }

        #pServicios div.crop>img {
            height: 240px;
            max-height: 240px;
            object-fit: cover;
        }


        #textWasap {
            display: none;
        }
    </style>

    <div class="container-fluid mt-4" id="pWelcome">
        <br><br><br><br>
        <div class="container">
            <div class="row">
        <!---Entrada----->
            <?php 
                if ( have_posts() ) : 
                    while ( have_posts() ) : the_post(); ?>

                        <div class="col-lg-9 mb-4">
                        <br><br>
                            <a href="<?php the_permalink(); ?>">
                            <h1 class="mb-4 text-center"><?php the_title();?></h1>
                            </a>
                            
                            <?php the_excerpt();?>
                            <br>
                         <a href="<?php the_permalink();?>" class="btn btn-primary">Ver más</a>
                        <?php 
                            if( has_post_thumbnail() ) {
                                the_post_thumbnail('post-thumbnails', array(
                                    'class' => 'img-fluid mb-3'
                                ));
                            }
                        ?>
                        </div>             
            <?php 
                    endwhile; 
                endif; 
            ?>
        <!---Entrada----->
        <!--Aside-->
        <?php get_sidebar(); ?>
         <!--Aside-->
        </div>
    </div>
    
    </div>
   

    <br>


    <?php get_footer(); ?>
            </body>
