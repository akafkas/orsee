<?php
// part of orsee. see orsee.org
ob_start();
$menu__area="impressum";
$title="impressum";
include ("header.php");
if ($proceed) {
    if ($settings['show_public_legal_notice']!='y') redirect("public/");
}
if ($proceed) {
    echo '<section class="or-public-card or-content-page">';
    echo '<div class="or-content-page-body or-public-prose">';
    echo content__get_content("impressum");
    echo '<div class="or-content-backtop"><a href="#">'.lang('back').'</a></div>';
    echo '</div>';
    echo '</section>';

}
include ("footer.php");
?>
