<?php
// part of orsee. see orsee.org
ob_start();

$menu__area="my_data";
$title="edit_participant_data";
$page_header_width_mode="wide";
include("header.php");
if ($proceed) {
    $form=true;
    $errors__dataform=array();
    if (isset($_REQUEST['add']) && $_REQUEST['add']) {
        if (!csrf__validate_request_message()) {
            redirect("public/participant_edit.php".$token_string);
        }
        $continue=true;
        $_REQUEST['participant_id']=$participant['participant_id'];
        if (isset($participant['pending_profile_update_request']) && $participant['pending_profile_update_request']=='y' &&
            isset($participant['profile_update_request_new_pool']) && $participant['profile_update_request_new_pool']) {
            $_REQUEST['subpool_id']=$participant['profile_update_request_new_pool'];
        }

        // checks and errors
        foreach ($_REQUEST as $k=>$v) {
            if(!is_array($v)) $_REQUEST[$k]=trim($v);
        }
        $errors__dataform=participantform__check_fields($_REQUEST,false);
        $error_count=count($errors__dataform);
        if ($error_count>0) $continue=false;

        $response=participantform__check_unique($_REQUEST,"edit",$_REQUEST['participant_id']);
        if($response['problem']) { $continue=false; }

        if ($continue) {
            if (isset($participant['pending_profile_update_request']) && $participant['pending_profile_update_request']=='y') {
                $_REQUEST['pending_profile_update_request']='n';
                $_REQUEST['profile_update_request_new_pool']=NULL;
                message(lang('profile_confirmed').'<BR>');
            }
            $participant=$_REQUEST;

            $participant['last_profile_update']=time();

            $done=orsee_db_save_array($participant,"participants",$participant['participant_id'],"participant_id");

            if ($done) {
                message(lang('changes_saved'));
                log__participant("edit",$participant['participant_id']);
                redirect("public/participant_edit.php".$token_string);
            } else {
                message(lang('database_error'));
                redirect ("public/participant_edit.php".$token_string);
            }
        }
    } else {
        $_REQUEST=$participant;
    }
}

if ($proceed) {
    if (isset($participant['pending_profile_update_request']) && $participant['pending_profile_update_request']=='y') {
        message(lang('profile_update_request_message').'<BR>');
        if (isset($participant['profile_update_request_new_pool']) && $participant['profile_update_request_new_pool']) {
            $_REQUEST['subpool_id']=$participant['profile_update_request_new_pool'];
        }
    }
}

if ($proceed) {
// form
    if ($form) {
        echo '<section class="or-public-split-layout">';
        echo '<div class="or-public-card or-public-form-card">';
        participant__show_form($_REQUEST,lang('save'),$errors__dataform,false);
        echo '</div>';
        echo '<aside class="or-public-card or-public-side-actions">';
        echo '<div class="or-public-section-title">'.lang('my_data').'</div>';
        echo button_link('participant_show.php'.$token_string,
                            lang('my_registrations'),'calendar-o');
        if ($settings['subject_authentication']!='token') {
            echo button_link('participant_change_pw.php',
                            lang('change_my_password'),'key');
        }
        echo button_link('participant_delete.php'.$token_string,
                            lang('unsubscribe'),'minus-circle');
        echo '</aside>';
        echo '</section>';
    }
}
include("footer.php");
?>
