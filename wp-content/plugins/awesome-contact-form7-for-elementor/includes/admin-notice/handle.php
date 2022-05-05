<?php 
include __DIR__ . '/ca-framework/loader.php';

$req_plugin_slug = 'ultraaddons-elementor-lite/init.php';
$this_plugin_slug = 'awesome-contact-form7-for-elementor/init.php';
$args = array(
    'Name' => "UltraAddons Elementor"
);

$my_notice = new CA_Framework\Notice('aSsa');
// $my_notice->start_date = '4/21/2022 18:48:24';
$my_notice->notice_type = '';
//$my_notice->set_message("Notice Message description text here.")
$my_notice->set_img_target('https://ultraaddons.com/?ref=8');
$my_notice->notice_style='notice';

$my_notice->set_img('http://ultraaddons.com/wp-content/uploads/2022/04/ua-banner.png')
->show();

