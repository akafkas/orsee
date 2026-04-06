<?php
// part of orsee. see orsee.org
ob_start();
$menu__area="mainpage";
$title="";
$lang_icons_prepare=true;
include "header.php";

if ($proceed) {
    echo '<section class="or-public-hero">';
    echo '<div class="or-public-hero-copy">';
    echo '<p class="or-public-hero-kicker">Recruitment that feels current</p>';
    echo '<h2 class="or-public-hero-title">'.$settings['default_area'].'</h2>';
    echo '<div class="or-public-prose">'.content__get_content("mainpage_welcome").'</div>';
    echo '</div>';
    echo '<div class="or-public-hero-panel">';
    echo '<div class="or-public-feature-card">';
    echo '<h3 class="or-public-feature-title">'.lang('profile_login').'</h3>';
    echo '<p class="or-public-feature-copy">Access invitations, registrations, profile details, and study history from one responsive portal.</p>';
    echo '<div class="or-public-feature-actions">';
    echo '<a class="button" href="participant_login.php">'.lang('login').'</a>';
    echo '<a class="button" href="participant_create.php">'.lang('registration_form').'</a>';
    echo '</div>';
    echo '</div>';
    echo '</div>';
    echo '</section>';
    if (!isset($addp)) $addp="";
    if ($addp) $sign="&"; else $sign="?";
    $langarray=lang__get_public_langs();
    $lang_names=lang__get_language_names();
    if  (count($langarray) > 1) {
        echo '<section class="or-public-card or-public-language-card">';
        echo '<div class="or-public-section-title">Switch language</div>';
        echo '<div class="or-public-language-list">';
        foreach ($langarray as $thislang) {
            if ($thislang != lang('lang')) {
                echo '<A class="or-public-language-link" HREF="index.php'.$addp.$sign.'language='.$thislang.'">';
                echo '<span class="languageicon langicon-'.$thislang.'">';
                if ($lang_names[$thislang]) echo $lang_names[$thislang]; else echo $thislang;
                echo '</span>';
                echo '</A>&nbsp;&nbsp;&nbsp;';
            }
        }
        echo '</div>';
        echo '</section>';
    }
}
include("footer.php");
?>
