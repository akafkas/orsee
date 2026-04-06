<?php
// part of orsee. see orsee.org
ob_start();

$menu__area="login";
$title="profile_login";
$page_header_width_mode="auth";
include("header.php");
if ($proceed) {
    if (isset($_REQUEST['logout']) && $_REQUEST['logout']) message(lang('logout'));
    if (isset($_REQUEST['pw']) && $_REQUEST['pw']) {
        message(lang('logout'));
        message (lang('password_changed_log_in_again'));
    }

    if (isset($_REQUEST['requested_url']) && $_REQUEST['requested_url'])
        $_SESSION['requested_url']=$_REQUEST['requested_url'];

    if (isset($_REQUEST['login']) && isset($_REQUEST['email']) && isset($_REQUEST['password'])) {
        if (!csrf__validate_request_message()) {
            redirect("public/participant_login.php");
        }
        $logged_in=participant__check_login($_REQUEST['email'],$_REQUEST['password']);
        if ($logged_in) {
            if (isset($_SESSION['requested_url']) && $_SESSION['requested_url']) {
                $url=$_SESSION['requested_url'];
                unset($_SESSION['requested_url']);
                redirect($url);
            } else redirect("public/participant_show.php");
        } else {
            redirect("public/participant_login.php");
        }
        $proceed=false;
    }
}

if ($proceed) {
    echo '<section class="or-public-auth-layout">';
    echo '<div class="or-public-auth-card">';
    echo '<div class="or-public-section-title">'.lang('profile_login').'</div>';
    echo '<p class="or-public-section-copy">Sign in to manage profile details, session invitations, and enrolments from any device.</p>';
    echo '<form name="login" action="participant_login.php" method="post" class="or-public-form">';
    echo csrf__field();
    echo '<label class="or-public-field">';
    echo '<span class="or-public-label">'.lang('email').'</span>';
    echo '<input type="text" size="30" maxlength="100" name="email">';
    echo '</label>';
    echo '<label class="or-public-field">';
    echo '<span class="or-public-label">'.lang('password').'</span>';
    echo '<input type="password" size="20" maxlength="30" name="password">';
    echo '</label>';
    echo '<div class="or-public-form-actions">';
    echo '<input class="button" type="submit" name="login" value="'.lang('login').'">';
    echo '</div>';
    echo '</form>';
    echo '<a class="or-public-text-link" href="participant_reset_pw.php">'.lang('forgot_your_password?').'</a>';
    echo '</div>';
    echo '</section>';
}
include("footer.php");
?>
