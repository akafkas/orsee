<?php
// part of orsee. see orsee.org
ob_start();

$menu__area="my_registrations";
$title="experiments";
include("header.php");
if ($proceed) {
    if (isset($_REQUEST['s']) && $_REQUEST['s']) $session_id=trim($_REQUEST['s']); else $session_id="";

    if (isset($_REQUEST['register']) && $_REQUEST['register']) {
        $continue=true;

        if (isset($_REQUEST['betternot']) && $_REQUEST['betternot']) {
            redirect("public/participant_show.php".$token_string);
        }
        if (isset($_REQUEST['reallyregister']) && $_REQUEST['reallyregister']) {
            if (!csrf__validate_request_message()) {
                redirect("public/participant_show.php".$token_string);
            }
        }

        if ($proceed) {
            if (!$session_id) {
                $continue=false;
                log__participant("interfere enrolment - no session_id",$participant_id);
                message(lang('error_session_id_register'));
                redirect("public/participant_show.php".$token_string);
            }
        }
        if ($proceed) {
            $session=orsee_db_load_array("sessions",$session_id,"session_id");
            if (!isset($session['session_id'])) {
                log__participant("interfere enrolment - invalid session_id",$participant_id);
                message(lang('error_session_id_register'));
                redirect("public/participant_show.php".$token_string);
            }
        }
        if ($proceed) {
            $participate_at=expregister__get_participate_at($participant_id,$session['experiment_id']);
            if (!isset($participate_at['session_id'])) {
                $continue=false;
                redirect("public/participant_show.php".$token_string);
            }
        }
        if ($proceed) {
            if ($settings['enable_enrolment_only_on_invite']=='y') {
                if (!$participate_at['invited']) {
                    $continue=false;
                    redirect("public/participant_show.php".$token_string);
                }
            }
        }

        if ($proceed) {
            if (isset($participate_at['session_id']) && $participate_at['session_id']>0) {
                $continue=false;
                message(lang('error_already_registered'));
                redirect("public/participant_show.php".$token_string);
            }
        }

        if ($proceed) {
            $registration_end=sessions__get_registration_end($session);
            $full=sessions__session_full($session_id,$session);
            $now=time();
            if ($registration_end < $now) {
                $continue=false;
                message(lang('error_registration_expired'));
                redirect("public/participant_show.php".$token_string);
            }
        }

        if ($proceed) {
            if ($full) {
                 $continue=false;
                 message(lang('error_session_complete'));
                 redirect("public/participant_show.php".$token_string);
            }
        }

        if ($proceed) {
            if (isset($_REQUEST['reallyregister']) && $_REQUEST['reallyregister']) {
                // if all checks are done, register ...
                if ($continue) {
                    $done=expregister__register($participant,$session);
                    $done=participant__update_last_enrolment_time($participant_id);
                    $done=log__participant("register",$participant['participant_id'],
                        "experiment_id:".$session['experiment_id']."\nsession_id:".$session_id);
                    message(lang('successfully_registered_to_experiment_xxx')." ".
                        experiment__get_public_name($session['experiment_id']).", ".
                        session__build_name($session).". ".
                        lang('this_will_be_confirmed_by_an_email'));
                    $redir="public/participant_show.php".$token_string;
                    if ($token_string) $redir.="&"; else $redir.="?";
                    $redir.="s=".$session_id;
                    redirect($redir);
                }
            } else {
                echo '<section class="or-public-card or-public-dialog-card"><TABLE class="or_page_subtitle" style="background: '.$color['page_subtitle_background'].'; color: '.$color['page_subtitle_textcolor'].'">
                        <TR><TD align="center">
                            '.lang('experiment_registration').'
                        </TD></TABLE>';

                echo '<BR><BR>
                    <form action="participant_show.php" method="POST">
                    <INPUT type=hidden name="s" value="'.$_REQUEST['s'].'">';

                if ($token_string) echo '<INPUT type=hidden name="p" value="'.$participant['participant_id_crypt'].'">';
                echo '<INPUT type=hidden name="register" value="true">
                    '.csrf__field().'
                    <TABLE class="or_formtable">
                    <TR>
                        <TD colspan=2 align=center>
                            <B>'.lang('do_you_really_want_to_register_for_experiment').'</B>
                        </TD>
                    </TR>
                    <TR>
                        <TD>
                            '.lang('experiment').':
                        </TD>
                        <TD>
                            '.experiment__get_public_name($session['experiment_id']).'
                        </TD>
                    </TR>
                    <TR>
                        <TD>
                            '.lang('date_and_time').':
                        </TD>
                        <TD>
                            '.session__build_name($session).'
                        </TD>
                    </TR>
                    <TR>
                        <TD>
                            '.lang('laboratory').':
                        </TD>
                        <TD>
                            '.laboratories__get_laboratory_name($session['laboratory_id']).'
                        </TD>
                    </TR>
                    <TR>
                        <TD colspan=2>&nbsp;</TD>
                    </TR>
                    <TR>
                        <TD align=center colspan=2>
                            <INPUT class="button" type=submit name="reallyregister" value="'.lang('yes_i_want').'">
                            &nbsp;&nbsp;&nbsp;&nbsp;
                            <INPUT class="button" type=submit name="betternot" value="'.lang('no_sorry').'">
                        </TD>
                    </TR>
                    </TABLE>
                    </FORM>
                    </section>

                    ';
            }
        }

    } elseif (isset($_REQUEST['cancel']) && $_REQUEST['cancel'] &&
            isset($settings['allow_subject_cancellation']) && $settings['allow_subject_cancellation']=='y') {
        $continue=true;

        if (isset($_REQUEST['betternot']) && $_REQUEST['betternot']) {
            redirect("public/participant_show.php".$token_string);
        }
        if (isset($_REQUEST['reallycancel']) && $_REQUEST['reallycancel']) {
            if (!csrf__validate_request_message()) {
                redirect("public/participant_show.php".$token_string);
            }
        }

        if ($proceed) {
            if (!$session_id) {
                $continue=false;
                log__participant("interfere enrolment cancellation- no session_id",$participant_id);
                message(lang('error_session_id_register'));
                redirect("public/participant_show.php".$token_string);
            }
        }
        if ($proceed) {
            $session=orsee_db_load_array("sessions",$session_id,"session_id");
            if (!isset($session['session_id'])) {
                log__participant("interfere enrolment cancellation - invalid session_id",$participant_id);
                message(lang('error_session_id_register'));
                redirect("public/participant_show.php".$token_string);
            }
        }
        if ($proceed) {
            $participate_at=expregister__get_participate_at($participant_id,$session['experiment_id']);
            if (!isset($participate_at['session_id']) || $participate_at['session_id']!=$session_id) {
                $continue=false;
                redirect("public/participant_show.php".$token_string);
            }
        }

        if ($proceed) {
            $cancellation_deadline=sessions__get_cancellation_deadline($session);
            $now=time();
            if ($cancellation_deadline < $now) {
                $continue=false;
                message(lang('error_enrolment_cancellation_deadline_expired'));
                redirect("public/participant_show.php".$token_string);
            }
        }

        if ($proceed) {
            if (isset($_REQUEST['reallycancel']) && $_REQUEST['reallycancel']) {
                // if all checks are done, register ...
                if ($continue) {
                    $done=expregister__cancel($participant,$session);
                    $done=participant__update_last_enrolment_time($participant_id);
                    $done=log__participant("cancel_session_enrolment",$participant['participant_id'],
                        "experiment_id:".$session['experiment_id']."\nsession_id:".$session_id);
                    message(lang('successfully_canceled_enrolment_xxx')." ".
                        experiment__get_public_name($session['experiment_id']).", ".
                        session__build_name($session).". "
                        .lang('this_will_be_confirmed_by_an_email')
                        );
                    redirect("public/participant_show.php".$token_string);
                }
            } else {
                echo '<section class="or-public-card or-public-dialog-card">';

                echo '<TABLE class="or_page_subtitle" style="background: '.$color['page_subtitle_background'].'; color: '.$color['page_subtitle_textcolor'].'">
                        <TR><TD align="center">
                            '.lang('session_enrolment_cancellation').'
                        </TD>';

                echo '<BR><BR>
                    <form action="participant_show.php" method="POST">
                    <INPUT type="hidden" name="s" value="'.$_REQUEST['s'].'">';

                if ($token_string) echo '<INPUT type="hidden" name="p" value="'.$participant['participant_id_crypt'].'">';
                echo '<INPUT type=hidden name="cancel" value="true">
                    '.csrf__field().'
                    <TABLE style="outline: 1px solid black;">
                    <TR>
                        <TD colspan=2 align=center>
                            <B>'.lang('do_you_really_want_to_cancel_session_enrolment').'</B>
                        </TD>
                    </TR>
                    <TR>
                        <TD>
                            '.lang('experiment').':
                        </TD>
                        <TD>
                            '.experiment__get_public_name($session['experiment_id']).'
                        </TD>
                    </TR>
                    <TR>
                        <TD>
                            '.lang('date_and_time').':
                        </TD>
                        <TD>
                            '.session__build_name($session).'
                        </TD>
                    </TR>
                    <TR>
                        <TD>
                            '.lang('laboratory').':
                        </TD>
                        <TD>
                            '.laboratories__get_laboratory_name($session['laboratory_id']).'
                        </TD>
                    </TR>
                    <TR>
                        <TD colspan=2>&nbsp;</TD>
                    </TR>
                    <TR>
                        <TD align=center colspan=2>
                            <INPUT class="button" type=submit name="reallycancel" value="'.lang('yes_i_want').'">
                            &nbsp;&nbsp;&nbsp;&nbsp;
                            <INPUT class="button" type=submit name="betternot" value="'.lang('no_sorry').'">
                        </TD>
                    </TR>
                    </TABLE>
                    </FORM>
                    </section>

                    ';
            }
        }
    } else {

        if (!isset($preloaded_laboratories)) $preloaded_laboratories=laboratories__get_laboratories();
        echo '<section class="or-public-action-bar">';
        echo button_link('participant_edit.php'.$token_string,
                            lang('edit_your_profile'),'pencil-square-o');
        echo '</section>';

        echo '<div class="or-public-dashboard"><table class="or-public-dashboard-table" border="0" width="100%">';
        echo '<TR><TD>
                <TABLE width="100%" class="or_panel" style="width: 100%;">
                    <TR><TD>
                        <TABLE width="100%" border=0 class="or_panel_title"><TR>
                            <TD style="background: '.$color['panel_title_background'].'; color: '.$color['panel_title_textcolor'].'">
                                '.lang('experiments_you_are_invited_for').'
                            </TD>
                        </TR></TABLE>
                    </TD></TR>
                    <TR><TD>
                        '.lang('please_check_availability_before_register').'
                    </TD></TR>
                    <TR><TD>';
        $labs=expregister__list_invited_for($participant);
        echo '      </TD></TR>
                </TABLE>
            </TD></TR>';
        echo '<TR><TD>&nbsp;</TD></TR>';
        echo '<TR><TD>
                <TABLE width="100%" class="or_panel" style="width: 100%;">
                    <TR><TD>
                        <TABLE width="100%" border=0 class="or_panel_title"><TR>
                            <TD style="background: '.$color['panel_title_background'].'; color: '.$color['panel_title_textcolor'].'">
                                '.lang('experiments_already_registered_for').'
                            </TD>
                        </TR></TABLE>
                    </TD></TR>
                    <TR><TD>';
        $labs2=expregister__list_registered_for($participant,$session_id);
        echo '      </TD></TR>
                </TABLE>
            </TD></TR>';
        echo '<TR><TD>&nbsp;</TD></TR>';
        $laboratories=array_unique(array_merge($labs,$labs2));
        if (count($laboratories)>0) {
            echo '<TR><TD>
                    <table border="0" class="or_panel" style="width: 100%;">
                        <TR><TD colspan=2><B>';
            if (count($laboratories)==1) echo lang('laboratory_address');
            else echo lang('laboratory_addresses');
            echo '      </B></TD></TR>';
            foreach ($laboratories as $laboratory_id) {
                if (isset($preloaded_laboratories[$laboratory_id])) {
                    echo '<TR><TD valign=top>';
                    echo $preloaded_laboratories[$laboratory_id]['lab_name'];
                    echo '</TD><TD>';
                    $address=$preloaded_laboratories[$laboratory_id]['lab_address'];
                    echo str_replace("\n","<BR>",$address);
                    echo '</TD></TR>';
                }
            }
            echo '  </table>
                </TD></TR>';
            echo '<TR><TD>&nbsp;</TD></TR>';
        }
        echo '<TR><TD>
                <TABLE width="100%" class="or_panel" style="width: 100%;">
                    <TR><TD>
                        <TABLE width="100%" border=0 class="or_panel_title"><TR>
                            <TD style="background: '.$color['panel_title_background'].'; color: '.$color['panel_title_textcolor'].'">
                                '.lang('experiments_you_participated').'
                            </TD>
                        </TR></TABLE>
                    </TD></TR>
                    <TR><TD>
                        '.lang('registered_for').' '.$participant['number_reg'].',
                        '.lang('not_shown_up').' '.$participant['number_noshowup'].'
                </TD></TR>
                    <TR><TD>';
        expregister__list_history($participant);
        echo '      </TD></TR>
                </TABLE>
            </TD></TR>';
        echo '</table></div>';
    }

}
include("footer.php");
?>
