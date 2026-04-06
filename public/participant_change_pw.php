<?php
// part of orsee. see orsee.org
ob_start();

$menu__area="my_data";
$title="change_my_password";
$page_header_width_mode="wide";
include("header.php");

if ($proceed) {
    if (isset($_REQUEST['submit']) && $_REQUEST['submit']) {
        if (!csrf__validate_request_message()) {
            redirect("public/participant_change_pw.php");
        }

        if (isset($_REQUEST['passold'])) $passold=$_REQUEST['passold']; else $passold="";
        if (isset($_REQUEST['password'])) $password=$_REQUEST['password']; else $password="";
        if (isset($_REQUEST['password2'])) $password2=$_REQUEST['password2']; else $password2="";

        // password tests
        $continue=true;

        if ($continue) {
            if (!$passold) {
                message (lang('error_please_fill_in_all_fields'));
                $continue=false;
            }
        }
        if ($continue) {
            if (!crypt_verify($passold,$participant['password_crypted'])) {
                message(lang('error_old_password_wrong'));
                message(lang('for_security_reasons_we_logged_you_out'));
                $continue=false;
                participant__logout();
                redirect("public/participant_login.php");
            }
        }
        if ($continue) {
            $continue=participant__check_password($password,$password2);
        }

        if ($continue==false) {
            message (lang('error_password_not_changed'));
            redirect ("public/participant_change_pw.php");
        } else {
            participant__set_password($password,$participant['participant_id']);
            message (lang('password_changed_log_in_again'));
            log__participant("participant_password_change",$participant['participant_id']);
            log__participant("logout",$participant['participant_id']);
            participant__logout();
            redirect("public/participant_login.php?pw=true");
        }
        $proceed=false;
    }
}

if ($proceed) {
    echo '<section class="or-public-split-layout">';
    echo '<div class="or-public-card">';
    echo '<div class="or-public-section-title">'.lang('change_my_password').'</div>';
    echo '<form action="participant_change_pw.php" method="POST" class="or-public-form">';
    echo csrf__field();
    echo '<label class="or-public-field"><span class="or-public-label">'.lang('old_password').'</span>
            <input type="password" name="passold" size="20" max-length="30"></label>';
    echo '<div class="or-public-legacy-form-fragment"><table class="or_formtable or-public-form-fragment-table">';
    echo participant__password_form_fields(true,false);
    echo '</table></div>';
    echo '<div class="or-public-form-actions">
            <input class="button" type="submit" name="submit" value="'.lang('change').'">
            </div>';
    echo '</form>';
    echo '</div>';
    echo '<aside class="or-public-card or-public-side-actions">';
    echo '<div class="or-public-section-title">'.lang('my_data').'</div>';
    echo button_link('participant_edit.php', lang('edit_your_profile'),'pencil-square-o');
    echo button_link('participant_show.php', lang('my_registrations'),'calendar-o');
    echo '</aside>';
    echo '</section>';
}
include("footer.php");
?>
