<?php
// part of orsee. see orsee.org
ob_start();
$menu__area="mainpage";
$title="";
include "header.php";

if ($proceed) {
    $show_login_cta=($settings['subject_authentication']!='token');
    $primary_href=$show_login_cta ? './participant_login.php' : './participant_create.php';
    $primary_label=$show_login_cta ? lang('login') : lang('registration_form');
    $secondary_href=$show_login_cta ? './participant_create.php' : './show_calendar.php';
    $secondary_label=$show_login_cta ? lang('registration_form') : lang('experiment_calendar');

    if (isset($_REQUEST['logout']) && $_REQUEST['logout']) {
        message(lang('logout'));
    }
    if (isset($_REQUEST['pw']) && $_REQUEST['pw']) {
        message(lang('password_changed_log_in_again'));
    }

    show_message();

    echo '<section class="or-public-hero">';
    echo '<div class="or-public-hero-copy or-public-home-copy">';
    echo '<h2 class="or-public-hero-title">'.$settings['default_area'].'</h2>';
    echo '<div class="or-public-prose or-public-home-prose">'.content__get_content("mainpage_welcome").'</div>';
    echo '</div>';
    echo '<div class="or-public-hero-panel or-public-home-panel">';
    echo '<div class="or-public-feature-card or-public-home-feature-card">';
    if ($show_login_cta) {
        echo '<h3 class="or-public-feature-title">'.lang('profile_login').'</h3>';
    } else {
        echo '<h3 class="or-public-feature-title">'.lang('registration_form').'</h3>';
    }
    echo '<div class="or-public-feature-actions or-public-home-actions">';
    echo '<a class="button" href="'.$primary_href.'">'.$primary_label.'</a>';
    echo '<a class="button button-secondary" href="'.$secondary_href.'">'.$secondary_label.'</a>';
    echo '</div>';
    echo '</div>';
    echo '</div>';
    echo '</section>';
}
include("footer.php");
?>
