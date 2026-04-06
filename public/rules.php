<?php
// part of orsee. see orsee.org
ob_start();
$menu__area="rules";
$title="rules";
$page_header_width_mode="content";
include ("header.php");
if ($proceed) {
    if ($settings['show_public_rules_page']!='y') redirect("public/");
}
if ($proceed) {
    echo '<section class="or-public-card or-content-page">';
    echo '<div class="or-content-page-body or-public-prose">';
    echo content__get_content("rules");
    echo '</div>';
    echo '</section>';

}
include ("footer.php");
?>
