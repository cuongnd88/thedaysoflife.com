<?php

use Facebook\Exceptions\FacebookSDKException;
use Jennifer\Controller\Controller;
use thedaysoflife\Facebook\FacebookHelper;
use Jennifer\Sys\Config;
use Jennifer\Sys\Globals;
use thedaysoflife\Com\Com;
use thedaysoflife\Model\Admin;
use thedaysoflife\Sys\Configs;

class ControllerFacebook extends Controller
{
    protected $requiredPermission = ["admin"];
    /** @var Admin */
    private $admin;
    /** @var  FacebookHelper */
    private $helper;

    public function __construct(Admin $admin = null, FacebookHelper $facebookHelper = null)
    {
        parent::__construct();
        $this->admin = $admin ? $admin : new Admin();
        $this->helper = $facebookHelper ? $facebookHelper : new FacebookHelper();
    }

    /**
     * Post days to facebook
     * @return array
     * @throws FacebookSDKException
     */
    public function ajaxPostToFacebook()
    {
        $appAccessToken = Globals::session("FB_appAccessToken");
        $id = (int)$this->request->post['id'];
        $type = $this->request->post['type'];
        $result = false;
        if ($appAccessToken) {
            $day = $this->admin->getDayById($id);
            switch ($type) {
                case FacebookHelper::FB_TEXT:
                    $attachment = $this->fbText($day);
                    $response = $this->helper->fb->post('/' . Config::getConfig("FB_PAGEID") .
                        '/feed', $attachment, $appAccessToken);
                    $postID = $response->getGraphNode();
                    if ($postID) {
                        $this->admin->updateFB($id, $type);
                        $result = ["status" => "OK", "id" => $id];
                    }
                    break;

                case FacebookHelper::FB_FEED:
                    $attachment = $this->fbFeed($day);
                    $response = $this->helper->fb->post('/' . Config::getConfig("FB_PAGEID") .
                        '/feed', $attachment, $appAccessToken);
                    $postID = $response->getGraphNode();
                    if ($postID) {
                        $this->admin->updateFB($id, $type);
                        $result = ["status" => "OK", "id" => $id, "data" => "OK"];
                    }
                    break;

                case FacebookHelper::FB_LINK:
                    $attachment = $this->fbLink($day);
                    $response = $this->helper->fb->post('/' . Config::getConfig("FB_PAGEID") .
                        '/feed', $attachment, $appAccessToken);
                    $postID = $response->getGraphNode();
                    if ($postID) {
                        $this->admin->updateFB($id, $type);
                        $result = ["status" => "OK", "id" => $id, "data" => "OK"];
                    }
                    break;

                case FacebookHelper::FB_ALBUM:
                    $title = Com::getDayTitle($day) . " (by {$day["username"]})";
                    $photos = explode(',', $day['photos']);
                    $status = "NO";
                    if (sizeof($photos) > 0) {
                        $albumData = $this->fbAlbum($day);
                        $newAlbum = $this->helper->fb->post("/" . Config::getConfig("FB_PAGEID") .
                            "/albums", $albumData, $appAccessToken);
                        $album = $newAlbum->getDecodedBody();
                        if (isset($album["id"])) {
                            foreach ($photos as $i => $name) {
                                $photoName = html_entity_decode($title . " - " . ($i + 1), ENT_COMPAT, "UTF-8");
                                $photoURL = Com::getPhotoURL($name, Configs::PHOTO_FULL_NAME);
                                $photoData = ['message' => $photoName, 'url' => $photoURL];
                                $newPhoto = $this->helper->fb->post('/' . $album["id"] .
                                    '/photos', $photoData, $appAccessToken);
                                $photo = $newPhoto->getDecodedBody();
                                if (isset($photo["id"])) {
                                    $this->admin->updateFB($id, $type);
                                    $status = "OK";
                                }
                            }
                        }
                    }
                    $result = ["status" => $status, "id" => $id, "data" => $status];
                    break;
            }
        } else {
            $permissions = ['manage_pages', 'publish_actions'];
            $helper = $this->helper->fb->getRedirectLoginHelper();
            $loginUrl = $helper->getLoginUrl(Config::getConfig("SITE_URL") . '/back/days/', $permissions);
            $result = ["status" => "login",
                "id" => $id,
                "data" => '<a href="' . $loginUrl . '">FBLogin</a>'];
        }

        return $result;
    }

    /**
     * @param array $day
     * @return array
     */
    private function fbText($day)
    {
        $title = Com::getDayTitle($day);
        $content = $this->fbEscape($day["content"], "text");
        $content = $title . " (by {$day["username"]}) \n\n" . $content;

        $message = html_entity_decode($content, ENT_COMPAT, "UTF-8");
        $attachment = [
            'message' => $message,
        ];

        return $attachment;
    }

    /**
     * @param $content
     * @param $type
     * @return string
     */
    private function fbEscape($content, $type = "album")
    {
        $content = stripslashes($content);
        if ($type == "feed") {
            $content = str_replace("<li>", " - ", $content);
        } else {
            $content = str_replace("<li>", "\n", $content);
        }

        $content = str_replace("<br>", "\n", $content);
        $content = str_replace("&nbsp;", " ", $content);
        $content = strip_tags($content);

        return $content;
    }

    /**
     * @param array $day
     * @return array
     */
    private function fbFeed($day)
    {
        $actionName = 'View on Thedaysoflife.com';
        $link = Com::getDayLink($day);
        $title = Com::getDayTitle($day);
        $content = $this->fbEscape($day["content"], "feed");
        $content = Com::subString($content, 300, 3);
        $photos = explode(',', $day['photos']);
        $photoURL = Com::getPhotoURL($photos[0], Configs::PHOTO_TITLE_NAME);

        $message = html_entity_decode($title . " (by {$day["username"]})", ENT_COMPAT, "UTF-8");
        $name = html_entity_decode($title, ENT_COMPAT, "UTF-8");
        $desc = html_entity_decode($content, ENT_COMPAT, "UTF-8");
        $attachment = [
            'message' => $message,
            'name' => $name,
            'link' => $link,
            'description' => $desc,
            'picture' => $photoURL,
            'actions' => json_encode(['name' => $actionName, 'link' => $link]),
        ];

        return $attachment;
    }

    /**
     * @param array $day
     * @return array
     */
    private function fbLink($day)
    {
        $link = Com::getDayLink($day);
        $title = Com::getDayTitle($day);
        $content = $this->fbEscape($day["content"], "link");
        $content = $title . " (by {$day["username"]}) \n\n" . $content;

        $message = html_entity_decode($content, ENT_COMPAT, "UTF-8");
        $attachment = [
            'type' => 'photo',
            'message' => $message,
            'link' => $link,
        ];

        return $attachment;
    }

    /**
     * @param array $day
     * @return array
     */
    private function fbAlbum($day)
    {
        $title = Com::getDayTitle($day) . " (by {$day["username"] })";
        $content = $this->fbEscape($day["content"], "album");

        $name = html_entity_decode($title, ENT_COMPAT, "UTF-8");
        $message = html_entity_decode($content, ENT_COMPAT, "UTF-8");
        $album = [
            "name" => $name,
            "message" => $message,
        ];

        return $album;
    }
}