
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
    <script src="https://kit.fontawesome.com/e9ef1ef4f2.js" crossorigin="anonymous"></script>
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<header class="fixed-top" id="header" >
    
    <div class="bar-sup">
        <div class="ml-auto networks animate__animated animate__backInLeft">
            <a href="https://www.facebook.com/alticsa/" target="_blank">
                <i class="fa fa-facebook-square" aria-hidden="true"></i>
                <span>Facebook</span>
            </a>
            <a href="https://pe.linkedin.com/company/alticsa?trk=public_profile_topcard-current-company" target="_blank">
                <i class="fa fa-linkedin-square" aria-hidden="true"></i>
                <span>LinkedIn</span>
            </a>
            <a href="https://www.youtube.com/channel/UC32AmtRUXX5Va-jmtkWUp1g/featured" target="_blank">
                <i class="fa fa-youtube-play" aria-hidden="true"></i>
                <span>Youtube</span>
            </a>
        </div>
        
    </div>

    <nav class="navbar navbar-expand-md navbar-light"> 
        <a class="navbar-brand" href="<?php echo esc_url( home_url( '/' )); ?>">
            <?php 
            $logo_header = get_theme_mod( 'logo_header');
            if  ($logo_header) {
                echo '<img src="' .$logo_header . '" alt="'.get_bloginfo( 'name').'">';
            }
            ?>
        </a>
        <button class="navbar-toggler d-lg-none" type="button" data-toggle="collapse" data-target="#collapsibleNavId" aria-controls="collapsibleNavId" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- <div class="collapse navbar-collapse" id="collapsibleNavId">
             <ul class="navbar-nav ml-auto mt-2 mt-lg-0">
                <li>
                    <a class="nav-link" href="index.php">Inicio</a>
                </li>
                <li>
                    <a class="nav-link" href="nosotros.php">Nosotros</a>
                </li>
                <li>
                    <a class="nav-link" href="servicios.php">Servicios</a>
                </li>
                <li>
                    <a class="nav-link" href="contactanos.php">Contáctanos</a>
                </li>
            </ul> 
        </div>  -->
        <?php
        wp_nav_menu( array(
            'theme_location'    => 'menu-principal',
            'depth'             => 2,
            'container'         => 'div',
            'container_class'   => 'collapse navbar-collapse',
            'container_id'      => 'collapsibleNavId',
            'menu_class'        => 'navbar-nav ml-auto',
            'fallback_cb'       => 'WP_Bootstrap_Navwalker::fallback',
            'walker'            => new WP_Bootstrap_Navwalker(),
        ) );
        ?>
    </nav>
</header>
