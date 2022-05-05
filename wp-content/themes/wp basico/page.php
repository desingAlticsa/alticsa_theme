<?php get_header(); ?>
    <div class="row">
        <div class="col-md-12">
            <?php 
                while ( have_posts() ) : the_post();
            ?>
            <?php 
            endwhile;
            ?>
        </div>
    </div>

<?php the_content();?>


<?php get_footer(); ?>