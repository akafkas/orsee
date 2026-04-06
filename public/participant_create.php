<?php
// part of orsee. see orsee.org
ob_start();
$menu__area="public_register";
$title="registration_form";
include ("header.php");

if ($proceed) {
    // check for sub-subject pool
    if (!(isset($_SESSION['subpool_id']))) {
        // get available subpools
        $subpools=subpools__get_subpools();
        $all_pool_ids=array();
        foreach ($subpools as $pool) {
            if ($pool['subpool_id']>1 && $pool['show_at_registration_page']=='y') {
                $all_pool_ids[]=$pool['subpool_id'];
            }
        }

        if (isset($_REQUEST['s']) && $_REQUEST['s']) {
            if (in_array(trim($_REQUEST['s']),$all_pool_ids)) {
                // set subpool
                $_SESSION['subpool_id']=trim($_REQUEST['s']);
                redirect("public/".thisdoc());
            } else {
                redirect("public/".thisdoc());
            }
        } else {
            if (count($all_pool_ids)<=1 && $settings['subpool_default_registration_id']) {
                $_SESSION['subpool_id']=$settings['subpool_default_registration_id'];
                redirect("public/".thisdoc());
            } elseif (count($all_pool_ids)==1 && !$settings['subpool_default_registration_id']) {
                $_SESSION['subpool_id']=$all_pool_ids[0];
                redirect ("public/".thisdoc());
            } elseif (count($all_pool_ids)==0 && !$settings['subpool_default_registration_id']) {
                $_SESSION['subpool_id']=1;
                redirect ("public/".thisdoc());
            } else {
                $self_descriptions=array();
                $query="SELECT * from ".table('lang')."
                        WHERE content_type='subjectpool'";
                $result=or_query($query);
                while ($line=pdo_fetch_assoc($result)) $self_descriptions[$line['content_name']]=$line[lang('lang')];

                echo '<section class="or-public-card or-register-shell">
                            <div class="or-public-section-title">'.lang('please_choose_subgroup').'</div>
                            <div class="or-public-choice-list">';
                foreach ($all_pool_ids as $subpool_id) {
                    echo '<A class="or-register-subpool-link" HREF="'.thisdoc().'?s='.$subpool_id.'">'.$self_descriptions[$subpool_id].'</A>';
                }
                echo '</div></section>';
            }
        }
        $proceed=false;
    }
}

if ($proceed) {
    // check for rules
    if (!isset($_SESSION['rules'])) {
        if ($settings['registration__require_rules_acceptance']=='y' ||
            $settings['registration__require_privacy_policy_acceptance']=='y') {
            if (isset($_REQUEST['accept_rules']) && $_REQUEST['accept_rules']) {
                if (!csrf__validate_request_message()) {
                    redirect ("public/".thisdoc());
                }
                $_SESSION['rules']=true;
                redirect ("public/".thisdoc());
            } elseif (isset($_REQUEST['notaccept_rules']) && $_REQUEST['notaccept_rules']) {
                if (!csrf__validate_request_message()) {
                    redirect ("public/".thisdoc());
                }
                unset ($_SESSION['subpool_id']);
                redirect ("public/");
            } else {
                echo '<section class="or-public-card or-register-shell">
                      <FORM action='.thisdoc().' method="POST" class="or-register-rules-form">
                      '.csrf__field();
                if ($settings['registration__require_rules_acceptance']=='y') {
                    echo '<details class="or-register-details" open>
                                <summary class="or-register-summary">'.lang('rules').'</summary>
                                <div class="or-register-details-content">'.content__get_content("rules").'</div>
                            </details>
                        ';
                }
                if ($settings['registration__require_privacy_policy_acceptance']=='y') {
                    echo '<details class="or-register-details" open>
                                <summary class="or-register-summary">'.lang('privacy_policy').'</summary>
                                <div class="or-register-details-content">'.content__get_content("privacy_policy").'</div>
                            </details>
                        ';
                }
                echo '<div class="or-register-consent">
                            '.lang('do_you_agree_rules_privacy').'
                        </div>
                        <div class="or-register-actions">
                            <INPUT class="button" type="submit" name="accept_rules" value="'.lang('yes').'">
                            <INPUT class="button button-secondary" type="submit" name="notaccept_rules" value="'.lang('no').'">
                        </div>
                    </FORM>
                    </section>';
            }
        } else {
            $_SESSION['rules']=true;
            redirect ("public/".thisdoc());
        }
        $proceed=false;
    }
}

if ($proceed) {
    $form=true; $errors__dataform=array();
    if (isset($_REQUEST['add'])) {
        if (!csrf__validate_request_message()) {
            redirect("public/participant_create.php");
        }
        $continue=true;

        if (!isset($_REQUEST['captcha']) || !isset($_SESSION['captcha_string']) || $_REQUEST['captcha']!=$_SESSION['captcha_string']) {
            if (!isset($_REQUEST['subscriptions']) || !is_array($_REQUEST['subscriptions'])) $_REQUEST['subscriptions']=array();
            $_REQUEST['subscriptions']=id_array_to_db_string($_REQUEST['subscriptions']);
            $continue=false;
            message(lang('error_wrong_captcha'));
        }

        if ($continue) {
            // checks and errors
            foreach ($_REQUEST as $k=>$v) {
                if(!is_array($v)) $_REQUEST[$k]=trim($v);
            }
            $_REQUEST['subpool_id']=$_SESSION['subpool_id'];
            $errors__dataform=participantform__check_fields($_REQUEST,false);
            $error_count=count($errors__dataform);
            if ($error_count>0) $continue=false;

            $response=participantform__check_unique($_REQUEST,"create");
            if (isset($response['disable_form']) && $response['disable_form']) {
                $continue=false;
                $proceed=false;
                unset ($_SESSION['subpool_id']);
                unset ($_SESSION['rules']);
                unset ($_SESSION['pauthdata']['pw_provided']);
                unset ($_SESSION['pauthdata']['submitted_checked_pw']);
                if($settings['subject_authentication']=='token') {
                    redirect ("public/");
                } else {
                    redirect ("public/participant_login.php");
                }
            } elseif($response['problem']) {
                $continue=false;
            }

            if ($settings['subject_authentication']!='token') {
                if (isset($_SESSION['pauthdata']['pw_provided']) && $_SESSION['pauthdata']['pw_provided'] &&
                    isset($_SESSION['pauthdata']['submitted_checked_pw']) && $_SESSION['pauthdata']['submitted_checked_pw']) {
                        $_REQUEST['password']=$_SESSION['pauthdata']['submitted_checked_pw'];
                } else {
                    $pw_ok=participant__check_password($_REQUEST['password'],$_REQUEST['password2']);
                    if ($pw_ok) {
                        $_SESSION['pauthdata']['pw_provided']=true;
                        $_SESSION['pauthdata']['submitted_checked_pw']=$_REQUEST['password'];
                    } else {
                        $continue=false;
                    }
                }
            }
        }

        if ($continue) {
            $participant=$_REQUEST;
            unset ($_SESSION['pauthdata']['pw_provided']);
            unset ($_SESSION['pauthdata']['submitted_checked_pw']);
            unset ($_SESSION['captcha_string']);
            $new_id=participant__create_participant_id($participant);
            $participant['participant_id']=$new_id['participant_id'];
            $participant['participant_id_crypt']=$new_id['participant_id_crypt'];
            if ($settings['subject_authentication']!='token') {
                $participant['password_crypted']=unix_crypt($participant['password']);
            }
            $participant['confirmation_token']=create_random_token(get_entropy($participant));
            $participant['creation_time']=time();
            $participant['last_profile_update']=$participant['creation_time'];
            $participant['status_id']=0;
            $participant['subpool_id']=$_SESSION['subpool_id'];
            if (!isset($participant['language']) || !$participant['language']) $participant['language']=$settings['public_standard_language'];
            $done=orsee_db_save_array($participant,"participants",$participant['participant_id'],"participant_id");
            if ($done) {
                log__participant("subscribe",$participant['lname'].', '.$participant['fname']);
                $proceed=false;
                $done=experimentmail__confirmation_mail($participant);
                message(lang('successfully_registered'));
                redirect ("public/");
            } else {
                message(lang('database_error'));
            }
        }
    }
}

if ($proceed) {

    echo '<section class="or-public-card or-register-shell">';
    $_REQUEST['subpool_id']=$_SESSION['subpool_id'];

    $extra=''; $pwfields=''; $captcha='';
    if ($settings['subject_authentication']!='token') {
        if (isset($_SESSION['pauthdata']['pw_provided']) && $_SESSION['pauthdata']['pw_provided']) {
                $pwfields.=participant__password_form_fields(false,true);
        } else {
                $pwfields.=participant__password_form_fields(false,false);
        }
    }
    $captcha='<TR><TD>'.lang('captcha_text').'<br><IMG src="captcha.php"><BR>
            <INPUT type="text" name="captcha" size="8" maxlength="8" value="" autocomplete="off" autocapitalize="none" inputmode="text">
            </TD></TR>';
    if ($pwfields || $captcha) $extra='<TABLE class="or-register-extra"><TR><TD>&nbsp;</TD></TR>'.
        $pwfields.$captcha.'</TABLE>';
    else $extra='';
    participant__show_form($_REQUEST,lang('submit'),$errors__dataform,false,$extra);
    echo '</section>';

}

echo '<script src="../style/public-registration.js"></script>';
include("footer.php");
?>
