<?php
add_action( 'wp_ajax_get_data_from_other_site', 'get_data_from_other_site' );
function get_data_from_other_site(){

    $url = $_REQUEST['link'];
    $domain_name = $_REQUEST['domainName'];

    if (preg_match('/https:\/\/ndla.no\/subjects\/subject:[0-9]/',
        $url) OR preg_match('/https:\/\/munin.buzz\/[12][0-9][0-9][0-9]\/[0-1][0-9]/',
            $url) ) {

        $args = array();

        $response = wp_remote_get($url, $args);

        $body = $response['body'];
        $check_answer = $response['response']['code'];

        if ($domain_name === "ndla.no" && $check_answer=== 200) {
            if ($startPos = strpos($body, '<body>')) {
                $endPos = strpos($body, '</body>');
                $body = (substr($body, $startPos, ($endPos - $startPos)));
                $article_introduction = get_article_introduction_content_ndla($body);
                $body = get_sections_ndla($body);
                $body = remove_tags_content($body, '<details', '</details>');
                $body = remove_tags_content($body, '<div class="c-bodybox">', '</a></p></div>');
                $body = find_text_ndla($body);
                $body = strip_tags($body, '<p>');
                $body = $article_introduction . $body;
            } else {
                $body = "Nothing found!";
            }
            $result = $body;
        }


        elseif ($domain_name === "munin.buzz" && $check_answer=== 200 ) {
            $body = $response['body'];
            if ($startPos = strpos($body, '<main')) {
                $endPos = strpos($body, '</main>');
                $body = substr($body, $startPos, ($endPos - $startPos) + 7);
                $body = remove_tags_content($body, '<figcaption>', '</figcaption>');
            } else {
                $body = "Nothing found!";
            }


            if ($startPos = strpos($body, 'main-lead')) {
                $endPos = strpos($body, '</div>', $startPos);
                $nextPos = strpos($body, 'container', $endPos);
                $main_lead = substr($body, $startPos + 12, ($endPos - $startPos) - 15);
                $main_lead = trim($main_lead);
                $main_lead = remove_tags_content_v2($main_lead, "<a href=", "</span></a>");
                $main_lead = strip_tags($main_lead, '<p>');
                $main_lead = preg_replace('/>\s+</', "><", $main_lead);
                $body = substr($body, $nextPos + 11);
            } else {
                $main_lead = "";
            }

            if ($endPos = strpos($body, 'main-footer')) {
                $body = stristr($body, '<div class="main-footer">', true);
            }

            $body = strip_tags($body, '<p><h2><h1><h3><li>');
            $body = preg_replace('/\s+/', ' ', $body);
            $body = preg_replace('/>\s+</', "><", $body);
            $body = preg_replace('/ class=(".*?")/', '', $body);
            $body = str_replace('<p></p>', '', $body);
            $body = str_replace('<h2>', '<p>', $body);
            $body = str_replace('</h2>', '</p>', $body);
            $body = str_replace('<li>', '<p>', $body);
            $body = str_replace('</li>', '</p>', $body);

            $result = $main_lead.$body;
            $result = $main_lead = preg_replace('/>\s+</', "><", $result);
        } else {
            $result = "Empty";
        }

        $report = (object)[
            'text' => $result,
        ];

        echo json_encode($report);
    }

    else {
        $result = "Sorry, we were not able to process your request. You could try again.";
        $report  = (object) [
            'text' => $result,
        ];

        echo json_encode( $report );
    }

    die();
}

// -----------  'ndla.no' being used functions  ------------------
function get_sections_ndla($text) { // working being used
    $result = '';
    while ($startPos = strpos($text, '<section><')) {
        $fragment_text = substr($text, $startPos);
        $endScriptPos = strpos($fragment_text, '</section>');
        $section  = substr($fragment_text, 0, ($endScriptPos + 10));
        $result .= $section;
        $text = substr($text, $endScriptPos + 10);
    }
    return $result;
}


function find_text_ndla($text) { // working and being used
    $result = '';
    while ($startPos = strpos($text, '<p>')) {
        $endPos   = strpos($text, '</p>', $startPos);
        $result .= substr($text, $startPos, ($endPos - $startPos) + 4 );
        $text = substr($text, $endPos + 5);
    }
    return $result;
}

function get_article_introduction_content_ndla($text) {
    $result = '';
    if($startPos = strpos($text, 'class="article_introduction">')) {
        $result .= substr($text, $startPos + 29);
        $result = stristr($result, '</p>', true);
        $result = '<p>' . $result . '</p>';
    }
    return $result;
}


// -----------------  universal functions -------------------

function remove_tags_content($text, $start_tag, $end_tag) { // removes fragment between unique fragments
    $result = '';
    if($startPos = strpos($text, $start_tag)) {
        $result .= stristr($text, $start_tag, true);
        $result .= stristr($text, $end_tag);
    } else {
        $result .= $text;
    }
    return $result;
}

function remove_tags_content_v2($text, $start_tag, $end_tag) { // removes fragment between unique fragments
    $result = '';
    if($startPos = strpos($text, $start_tag)) {
        $endPos = strpos($text, $end_tag, $startPos) + strlen($end_tag);
        $result .= stristr($text, $start_tag, true);
        $result .= substr($text, $endPos);
    } else {
        $result .= $text;
    }
    return $result;
}

// ------------  not being used functions --------------------


function text_scrapper($text) { // beta-version
    $result = '<p>';
    $iter = 0;

    while ($text) {
        $iter++;
        $startPos = strpos($text, '<');
        $endPos   = strpos($text, '>');

        if (substr($text, $startPos+1, 6) == 'script') {
            $fragment_text = substr($text, $startPos);
            $endScriptPos = strpos($fragment_text, '</script');
            $fragment_text = substr($fragment_text, $endScriptPos);
            $endScriptPos = strpos($fragment_text, '>');
            $text = substr($text, $endPos+1);
        }

        elseif (substr($text, $startPos+1, 8) == 'noscript') {
            $fragment_text = substr($text, $startPos);
            $endScriptPos = strpos($fragment_text, '</noscript');
            $fragment_text = substr($fragment_text, $endScriptPos);
            $endScriptPos = strpos($fragment_text, '>');
            $text = substr($text, $endPos+1);
        }

        elseif (substr($text, $startPos+1, 5) == 'span') {
            $fragment_text = substr($text, $startPos);
            $endScriptPos = strpos($fragment_text, '</span');
            $fragment_text = substr($fragment_text, $endScriptPos);
            $endScriptPos = strpos($fragment_text, '>');
            $text = substr($text, $endPos+1);
        }

        elseif (substr($text, $startPos+1, 5) == 'style') {
            $fragment_text = substr($text, $startPos);
            $endScriptPos = strpos($fragment_text, '</style');
            $fragment_text = substr($fragment_text, $endScriptPos);
            $endScriptPos = strpos($fragment_text, '>');
            $text = substr($text, $endPos+1);
        }

        elseif (substr($text, $startPos+1, 5) == 'title') {
            $fragment_text = substr($text, $startPos);
            $endScriptPos = strpos($fragment_text, '</title>');
            $fragment_text = substr($fragment_text, $endScriptPos);
            $endScriptPos = strpos($fragment_text, '>');
            $text = substr($text, $endPos+1);
        }

        elseif (substr($text, $startPos+3, 4) == 'href') {
            $fragment_text = substr($text, $startPos);
            $endScriptPos = strpos($fragment_text, '</a>');
            $fragment_text = substr($fragment_text, $endScriptPos);
            $endScriptPos = strpos($fragment_text, '>');
            $text = substr($text, $endPos+1);
        }

        elseif (substr($text, $endPos+1, 1) == '<') {
            $text = substr($text, $endPos+1);
        }

        else {
            $fragment_text = substr($text, $endPos + 1);
            $endFragmentPos = strpos($fragment_text, '<');
            $result .= substr($fragment_text, 0, $endFragmentPos)."</p><p>";
            $text = substr($text, $endPos+1);
        }
    }
    return $result."</p>";
}



function find_all_p_tags_ndla($text) {
    $result = '';
    while ($startPos = strpos($text, '<p>')) {
        $endPos   = strpos($text, '</p>');
        $result .= (substr($text, $startPos, ($endPos - $startPos)));
        $text = substr($text, $endPos + 5);
    }
    return $result;
}
