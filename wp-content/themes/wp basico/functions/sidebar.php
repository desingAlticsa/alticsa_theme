<?php 
// Agregar Siderbar
function alticsa_widgets() {
    register_sidebar(array(
      'id' =>'widgets-derecha',
      'name' =>  __('Widget Derecha'),
      'descripcion' =>  __('Arrastra lo que quieras'),
      'before_widget' => '<div class="card body">',
      'after_widget' =>'</div>',
      'before_title' => '<h4>',
      'after_title' => '</h4><hr>'
    ));
  }
  add_action('widgets_init', 'alticsa_widgets');
?>