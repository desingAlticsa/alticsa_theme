<div class="col-lg-3">
    <h4>Publi</h4>
    <?php if ( is_active_sidebar( 'widgets-derecha' ) ) : ?>
        <?php dynamic_sidebar( 'widgets-derecha' ); ?>
    <?php else: ?>
        <!---alternativo-->
    <?php endif; ?>
</div>