<?php
// part of orsee. see orsee.org
ob_start();
$menu__area="calendar";
$title="experiment_calendar";
$page_header_width_mode="wide";
include ("header.php");
if ($proceed) {
    if ($settings['show_public_calendar']!='y') redirect("public/");
}
if ($proceed) {
    echo '<section class="or-public-card or-public-calendar-shell">';
    $done=calendar__display_calendar(0);
    echo '</section>';

}
include ("footer.php");
?>
