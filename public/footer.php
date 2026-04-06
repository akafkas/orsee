<?php
// part of orsee. see orsee.org

if ($settings['support_mail']) {
    echo '<div class="or-public-support small">';
    echo lang('for_questions_contact_xxx');
    echo ' ';
    helpers__scramblemail($settings['support_mail']);
    echo $settings['support_mail'];
    echo '</A>';
    echo '.</div>';
}

    debug_output();

    html__show_style_footer('public');
    html__footer();
?>
