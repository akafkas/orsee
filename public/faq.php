<?php
// part of orsee. see orsee.org
ob_start();
$menu__area="faqs";
$title="faq_long";
$page_header_width_mode="content";
include("header.php");
if ($proceed) {
    if ($settings['show_public_faqs']!='y') redirect("public/");
}
if ($proceed) {
    $query="SELECT * FROM ".table('faqs').", ".table('lang')."
            WHERE ".table('lang').".content_name=".table('faqs').".faq_id
            AND ".table('lang').".content_type='faq_answer'";
    $result=or_query($query); $answers=array();
    while ($line=pdo_fetch_assoc($result)) $answers[$line['faq_id']]=$line;

    $query="SELECT * FROM ".table('faqs').", ".table('lang')."
            WHERE ".table('lang').".content_name=".table('faqs').".faq_id
            AND ".table('lang').".content_type='faq_question'
            ORDER BY ".table('faqs').".evaluation DESC, ".table('lang').".".lang('lang');
    $result=or_query($query);

    $faq_items='';
    while ($line=pdo_fetch_assoc($result)) {
        $faq_id=$line['faq_id'];
        $question=stripslashes($line[lang('lang')]);
        $answer=stripslashes($answers[$faq_id][lang('lang')]);
        $already_voted=(isset($_SESSION['vote'][$faq_id]) && $_SESSION['vote'][$faq_id]);

        $faq_items.='<details class="or-faq-item">
                <summary class="or-faq-summary">
                    <span class="or-faq-question">'.$question.'</span>
                    <span class="or-faq-meta">'.$line['evaluation'].' '.lang('persons').'</span>
                </summary>
                <div class="or-faq-answer">'.$answer.'</div>';
        if (!$already_voted) {
            $faq_items.='<div class="or-faq-actions">
                        <a href="#" class="button bicongreen or-faq-vote" data-faq-id="'.$faq_id.'">'.lang('this_faq_answered_my_question').'</a>
                    </div>';
        }
        $faq_items.='</details>';
    }

    echo '<section class="or-public-card or-content-page">';
    echo '<div class="or-content-page-body">';
    echo '<div class="or-faq-heading">'.lang('faq_long').'</div>';
    echo '<div class="or-faq-list">'.$faq_items.'</div>';
    echo '</div>';
    echo '</section>';

    echo '<script type="text/javascript">
        var votedLabel = '.json_encode(lang('yes')).';
        var csrfToken = '.json_encode(csrf__get_token()).';
        document.addEventListener("click", function (event) {
            var voteButton = event.target.closest(".or-faq-vote");
            if (!voteButton) return;
            event.preventDefault();
            var faqId = voteButton.getAttribute("data-faq-id");
            if (!faqId) return;
            fetch("faq_vote.php?eval=true&id=" + encodeURIComponent(faqId) + "&csrf_token=" + encodeURIComponent(csrfToken), { credentials: "same-origin" })
                .then(function () {
                    voteButton.classList.add("disabled");
                    voteButton.setAttribute("aria-disabled", "true");
                    voteButton.textContent = votedLabel;
                });
        });
    </script>';

}
include ("footer.php");
?>
