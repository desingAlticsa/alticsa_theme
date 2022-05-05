<?php get_header();?>

    <div class="container-fluid" id="pWelcome">
        <br><br><br><br><br><br><br>
            <?php 
                if ( have_posts() ) : 
                    while ( have_posts() ) : the_post(); ?>
                     <div class="container">
                    <div class="row">
                        <div class="col-md mb-4">
                            <h1 class="mb-4 text-center"><?php the_title();?></h1>
                            <?php the_content();?>
                        </div>                
                    </div>
                </div>
            <?php 
                    endwhile; 
                endif; 
            ?>
       
        <br><br><br>
    </div>
    <script>
        $(document).ready(function() {
            $('#headerTop').height($('#header').height());

            AOS.init();
        });
    </script>
<?php get_footer(); ?>


  