<?php
// part of orsee. see orsee.org
ob_start();
$menu__area="public_register";
$title="reset_password";
include ("header.php");

if ($proceed) {
    if (isset($_REQUEST['t']) && $_REQUEST['t']) {
        $_SESSION['pw_reset_token']=$_REQUEST['t'];
        redirect("public/participant_reset_pw.php");
    }
}

if ($proceed) {
    if (isset($_SESSION['pw_reset_token']) && $_SESSION['pw_reset_token'] &&
        isset($_REQUEST['reset_email']) && isset($_REQUEST['password']) && isset($_REQUEST['password2']) &&
        ( $_REQUEST['reset_email'] || $_REQUEST['password'] || $_REQUEST['password2']) ) {
        if (!csrf__validate_request_message()) {
            redirect("public/participant_reset_pw.php");
        }

        $continue=true;

        $_SESSION['reset_email_address']=trim($_REQUEST['reset_email']);
        // captcha
        if ($continue) {
            if ($_REQUEST['captcha']!=$_SESSION['captcha_string']) {
                $continue=false;
                message(lang('error_wrong_captcha'));
                redirect("public/participant_reset_pw.php");
            }
        }
        if ($continue) {
            // check password, token, and email address
            $status_clause=participant_status__get_pquery_snippet("access_to_profile");
            $pars=array(':token'=>$_SESSION['pw_reset_token']);
            $query="SELECT * FROM ".table('participants')."
                    WHERE pwreset_token= :token
                    AND ".$status_clause;
            $participant=orsee_query($query,$pars);
            if (!isset($participant['participant_id'])) {
                //if token not ok, redirect to main page without comment
                $continue=false;
                message('token not found');
                redirect ("public/");
            } elseif ($participant['pwreset_request_time']+60*60<time()) {
                //if token validity elapsed, show message and redirect
                message(lang('password_reset_token_not_valid_anymore'));
                $continue=false;
                redirect ("public/");
            }
        }
        if ($continue) {
            if (strtolower($participant['email'])!=strtolower(trim($_REQUEST['reset_email']))) {
            //if email address not ok: save email address to session, show message, redirect
                message(lang('password_reset_provided_email_address_not_correct'));
                $continue=false;
                redirect("public/participant_reset_pw.php");
            }
        }
        if ($continue) {
            $pw_ok=participant__check_password($_REQUEST['password'],$_REQUEST['password2']);
            if (!$pw_ok) {
                //if passwords not ok: save email address to session, show message, redirect
                $continue=false;
                redirect("public/participant_reset_pw.php");
            }
        }
        if ($continue) {
        //if all ok, save new password (reset reset_request, token), reset token, password, email address, set OK, redirect
            $participant['password_crypted']=unix_crypt($_REQUEST['password']);
            $pars=array(':password'=>$participant['password_crypted'],
                        ':participant_id'=>$participant['participant_id']);
            $query="UPDATE ".table('participants')."
                    SET password_crypted = :password,
                    pwreset_token= NULL
                    WHERE participant_id = :participant_id";
            $participant=or_query($query,$pars);
            unset($_SESSION['pw_reset_token']);
            unset($_SESSION['captcha_string']);
            unset($_SESSION['reset_email_address']);
            $_SESSION['password_has_been_changed']=true;
            redirect("public/participant_reset_pw.php");
        }
    }
}

if ($proceed) {
    if (isset($_SESSION['pw_reset_token']) && $_SESSION['pw_reset_token']) {
        // show form, captcha
        if (isset($_SESSION['reset_email_address']) && $_SESSION['reset_email_address'])
            $email=$_SESSION['reset_email_address'];
        else $email='';
        echo '<section class="or-public-auth-layout">';
        echo '<div class="or-public-auth-card">';
        echo '<div class="or-public-section-title">'.lang('reset_password').'</div>';
        echo '<p class="or-public-section-copy">'.lang('reset_pw_please_enter_email_and_new_password').'</p>';
        echo '<form action="participant_reset_pw.php" method="POST" class="or-public-form">';
        echo csrf__field();
        echo '<label class="or-public-field"><span class="or-public-label">'.lang('email').'</span>
                <input type="text" name="reset_email" size="30" max-length="100" value="'.$email.'"></label>';
        echo '<div class="or-public-legacy-form-fragment">';
        echo '<table class="or_formtable or-public-form-fragment-table">';
        echo participant__password_form_fields(true,false);
        echo '<TR><TD>'.lang('captcha_text').'<br><IMG src="captcha.php" alt="captcha"><BR>
                <INPUT type="text" name="captcha" size="8" maxlength="8" value="">
                </TD></TR>';
        echo '</table>';
        echo '</div>';
        echo '<div class="or-public-form-actions">
            <input class="button" type="submit" name="submit" value="'.lang('change').'">
            </div>';
        echo '</form>';
        echo '</div>';
        echo '</section>';
    $proceed=false;
    }
}

if ($proceed) {
    if (isset($_SESSION['password_has_been_changed']) && $_SESSION['password_has_been_changed']) {
        message(lang('password_changed'));
        unset($_SESSION['password_has_been_changed']);
        $proceed=false;
        echo '<section class="or-public-card or-public-status-card">';
        show_message();
        echo '</section>';
    }
}

if ($proceed) {
    if (isset($_REQUEST['email']) && $_REQUEST['email']) {
        if (!csrf__validate_request_message()) {
            redirect("public/participant_reset_pw.php");
        }
        $continue=true;
        // captcha
        if ($continue) {
            if ($_REQUEST['captcha']!=$_SESSION['captcha_string']) {
                $continue=false;
                message(lang('error_wrong_captcha'));
                redirect("public/participant_reset_pw.php");
            }
        }

        if ($continue) {
            $status_clause=participant_status__get_pquery_snippet("access_to_profile");
            $pars=array(':email'=>$_REQUEST['email']);
            $query="SELECT * FROM ".table('participants')."
                    WHERE email= :email
                    AND ".$status_clause;
            $participant=orsee_query($query,$pars);
            if (isset($participant['participant_id'])) {
                // create and save token
                $participant['pwreset_token']=create_random_token(get_entropy($participant));
                $pars=array(':token'=>$participant['pwreset_token'],
                        ':participant_id'=>$participant['participant_id'],
                        ':now'=>time());
                $query="UPDATE ".table('participants')."
                        SET pwreset_token = :token,
                        pwreset_request_time = :now
                        WHERE participant_id= :participant_id";
                $done=or_query($query,$pars);
                // send reset email
                $done=experimentmail__mail_pwreset_link($participant);
                message(lang('password_reset_link_sent_if_email_exists'));
                redirect('public/');
            } else {
                // to not reveal which email addresses exist, just do as if
                message(lang('password_reset_link_sent_if_email_exists'));
                redirect('public/');
            }
        }
    }
}

if ($proceed) {
    echo '<section class="or-public-auth-layout">';
    echo '<div class="or-public-auth-card">';
    echo '<div class="or-public-section-title">'.lang('reset_password').'</div>';
    echo '<p class="or-public-section-copy">'.lang('reset_pw_please_enter_your_email_address').'</p>';
    echo '<form action="participant_reset_pw.php" method="POST" class="or-public-form">';
    echo csrf__field();
    echo '<label class="or-public-field"><span class="or-public-label">'.lang('email').'</span>
            <input type="text" name="email" size="30" max-length="100"></label>';
    echo '<label class="or-public-field"><span class="or-public-label">'.lang('captcha_text').'</span>
            <span class="or-public-captcha"><IMG src="captcha.php" alt="captcha"><INPUT type="text" name="captcha" size="8" maxlength="8" value=""></span></label>';
    echo '<div class="or-public-form-actions">
            <input class="button" type="submit" name="submit" value="'.lang('submit').'">
            </div>';
    echo '</form>';
    echo '</div>';
    echo '</section>';

}

include("footer.php");
?>
