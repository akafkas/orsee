<?php
// part of orsee. see orsee.org
ob_start();
$menu__area="privacy";
$title="privacy_policy";
$page_header_width_mode="content";
include ("header.php");
if ($proceed) {
    if ($settings['show_public_privacy_policy']!='y') redirect("public/");
}
if ($proceed) {
    echo '<section class="or-public-card or-content-page">';
    echo '<div class="or-content-page-body or-public-prose">';
    echo content__get_content("privacy_policy");
    echo '<div class="or-content-backtop"><a href="#">'.lang('back').'</a></div>';
    echo '</div>';
    echo '</section>';

}
include ("footer.php");
?>
