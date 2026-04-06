<?php
// part of orsee. see orsee.org
ob_start();
$menu__area="contact";
$title="contact";
include ("header.php");
if ($proceed) {
    if ($settings['show_public_contact']!='y') redirect("public/");
}
if ($proceed) {
    echo '<section class="or-public-card or-content-page">';
    echo '<div class="or-content-page-body or-public-prose">';
    echo content__get_content("contact");
    echo '<div class="or-content-backtop"><a href="#">'.lang('back').'</a></div>';
    echo '</div>';
    echo '</section>';

}
include ("footer.php");
?>
