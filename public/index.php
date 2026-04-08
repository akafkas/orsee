<?php
// part of orsee. see orsee.org
ob_start();
$menu__area="mainpage";
$title="";
include "header.php";

if ($proceed) {
    $register_now_label=lang('register_now');
    if ($register_now_label=='register_now') $register_now_label='Register Now';
    $see_upcoming_label=lang('see_upcoming_experiments');
    if ($see_upcoming_label=='see_upcoming_experiments') $see_upcoming_label='See Upcoming Experiments';

    $primary_href='./participant_create.php';
    $primary_label=$register_now_label;
    $secondary_href='./show_calendar.php';
    $secondary_label=$see_upcoming_label;

    if (isset($_REQUEST['logout']) && $_REQUEST['logout']) {
        message(lang('logout'));
    }
    if (isset($_REQUEST['pw']) && $_REQUEST['pw']) {
        message(lang('password_changed_log_in_again'));
    }

    show_message();

    echo '<section class="or-public-hero or-public-hero--single">';
    echo '<div class="or-public-hero-copy or-public-home-copy">';
    echo '<div class="or-public-prose or-public-home-prose">'.content__get_content("mainpage_welcome").'</div>';
    echo '<div class="or-public-feature-actions or-public-home-actions">';
    echo '<a class="button" href="'.$primary_href.'">'.$primary_label.'</a>';
    echo '<a class="button button-secondary" href="'.$secondary_href.'">'.$secondary_label.'</a>';
    echo '</div>';
    echo '</div>';
    echo '</section>';
}
include("footer.php");
?>
