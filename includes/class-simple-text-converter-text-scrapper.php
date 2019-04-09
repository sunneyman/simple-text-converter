<?php
/**
 * Created by PhpStorm.
 * User: alex_sh
 * Date: 2019-04-09
 * Class:  Simple_Text_Converter_Text_Scrapper (STCTS)
 * Abbr: STCTS
 * Version: 2.0.0
 */

class STCTS {

    /**
     * @param $url
     * @param $domain_name
     * @return bool|string
     */
    public static function get_text_from_site($url, $domain_name)
    {
        $holder = new STCTS();
        $validation = $holder->validate_url($url, $domain_name);

        if ($validation === 1) {
            $response = $holder->get_from_ndla($url);
        }

        elseif ($validation === 2) {
            $response = $holder->get_from_munin($url);
        }

        else {
            $response = $holder->error_message();
        }

        return json_encode($response);
    }

    /**
     * @param $url
     * @param $domain_name
     * @return Integer
     */
    protected function validate_url($url, $domain_name)
    {
        if (preg_match('/https:\/\/ndla.no\/subjects\/subject:[0-9]/',
                $url) && $domain_name === "ndla.no" ) {
            return 1;
        }

        elseif (preg_match('/https:\/\/munin.buzz\/[12][0-9][0-9][0-9]\/[0-1][0-9]/',
                $url) && $domain_name === "munin.buzz") {
            return 2;
        }

        else {
            return 0;
        }
    }

    /**
     * @param
     * @return String
     */
    protected function error_message()
    {
        $response = (object)[
            'text' => "Sorry, we were not able to process your request. Either the link you requested was not attached to any page, or external server could not process it. Click anywhere to continue.",
            'status' => false
        ];
        return $response;
    }


    /**
     * @param $request_status
     * @param $request_body
     * @return object
     */
    protected function prepare_response($request_status, $request_body)
    {
        $response = (object)[
            'text' => $request_body,
            'status' => $request_status,
        ];
        return $response;
    }

    /**
     * @param $url
     * @return object
     */
    protected function get_from_ndla($url)
    {
        $response = $this->fetch_url($url);
        $status = $response['status'];
        if ($status) {
            $body = $response['body'];

            if ($startPos = strpos($body, '<body>')) {
                $endPos = strpos($body, '</body>');
                $body = substr($body, $startPos, ($endPos - $startPos));
                $article_introduction = $this->get_article_introduction_content_ndla($body);
                $body = $this->get_sections_ndla($body);
                $body = $this->remove_tags_content($body, '<details', '</details>');
                $body = $this->remove_tags_content($body, '<div class="c-bodybox">', '</a></p></div>');
                $body = $this->find_text_ndla($body);
                $body = strip_tags($body, '<p>');
                $body = $article_introduction . $body;
            } else {
                $body = "";
            }

            $response = $this->prepare_response($status, $body);
        } else {
            $response = $this->error_message();
        }
        return $response;
    }

    /**
     * @param $url
     * @return object
     */
    protected function get_from_munin($url)
    {
        $response = $this->fetch_url($url);
        $status = $response['status'];

        if ($status) {

            $body = $response['body'];
            if ($startPos = strpos($body, '<main')) {
                $endPos = strpos($body, '</main>');
                $body = substr($body, $startPos, ($endPos - $startPos) + 7);
                $body = $this->remove_tags_content($body, '<figcaption>', '</figcaption>');
            }

            if ($startPos = strpos($body, 'main-lead')) {
                $endPos = strpos($body, '</div>', $startPos);
                $nextPos = strpos($body, 'container', $endPos);
                $main_lead = substr($body, $startPos + 12, ($endPos - $startPos) - 15);
                $main_lead = trim($main_lead);
                $main_lead = $this->remove_tags_content_v2($main_lead, "<a href=", "</span></a>");
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

            $response = $this->prepare_response($status, $result);
        } else {
            $response = $this->error_message();
        }
        return $response;
    }

    /**
     * @param $text
     * @return string
     */
    protected function find_text_ndla($text) { // working and being used
        $result = '';
        while ($startPos = strpos($text, '<p>')) {
            $endPos   = strpos($text, '</p>', $startPos);
            $result .= substr($text, $startPos, ($endPos - $startPos) + 4 );
            $text = substr($text, $endPos + 5);
        }
        return $result;
    }

    /**
     * @param $text
     * @param $start_tag
     * @param $end_tag
     * @return string
     * @description Removes fragment between unique fragments
     * @version 1.0
     */
    protected function remove_tags_content($text, $start_tag, $end_tag) {
        $result = '';
        if($startPos = strpos($text, $start_tag)) {
            $result .= stristr($text, $start_tag, true);
            $result .= stristr($text, $end_tag);
        } else {
            $result .= $text;
        }
        return $result;
    }

    /**
     * @param $text
     * @param $start_tag
     * @param $end_tag
     * @return String
     * @description Removes fragment between two unique fragments
     * @version 2.0
     */

    protected function remove_tags_content_v2($text, $start_tag, $end_tag) {
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

    /**
     * @param $text
     * @return String
     */
    protected function get_article_introduction_content_ndla($text) {
        $result = '';
        if($startPos = strpos($text, 'class="article_introduction">')) {
            $result .= substr($text, $startPos + 29);
            $result = stristr($result, '</p>', true);
            $result = '<p>' . $result . '</p>';
        }
        return $result;
    }

    /**
     * @param $text
     * @return string
     */
    protected function get_sections_ndla($text) {
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

    /**
     * @param $url
     * @return array
     */
    protected function fetch_url($url)
    {
        $args = array();
        $resp = wp_remote_get($url, $args);
        $body = $resp['body'];
        $check_answer = $resp['response']['code'];
        if($check_answer === 200 ) $request_status = true;
        else $request_status = false;

        $response = array(
            'body' => $body,
            'status' => $request_status
        );

        return $response;
    }
}


add_action( 'wp_ajax_get_data_from_other_site', 'get_data_from_other_site' );
function get_data_from_other_site()
{
    $requested_url = $_REQUEST['link'];
    $domain_name = $_REQUEST['domainName'];
    $response = STCTS::get_text_from_site($requested_url, $domain_name);

    echo $response;

    die();
}
