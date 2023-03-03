<?php

class User
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = Database::connect();
    }

    public function getUserDataFromSession()
    {
        if (isset($_SESSION['user_id'])) {
            $userid = $_SESSION['user_id'];
            $stmt = $this->pdo->prepare("SELECT * FROM users WHERE user_id=:USER_ID");
            $stmt->bindParam(":USER_ID", $userid);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_OBJ);
        }
    }

    public function getUserDataFromURL($profileId)
    {

            $stmt = $this->pdo->prepare("SELECT * FROM users WHERE user_id=:USER_ID");
            $stmt->bindParam(":USER_ID", $profileId);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_OBJ);
        
    }

    public function search($search)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE `username` LIKE ? OR `fullName` LIKE ? OR  `email` LIKE ?");
        $stmt->bindValue(1, $search . '%', PDO::PARAM_STR);
        $stmt->bindValue(2, $search . '%', PDO::PARAM_STR);
        $stmt->bindValue(3, $search . '%', PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function getDetails($get_id, $what)
    {

        $stmt = $this->pdo->prepare("SELECT $what FROM users WHERE user_id=:USER_ID");
        $stmt->bindParam(":USER_ID", $get_id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_OBJ);
        return  $row->$what;
    }

    public function userIdByUsername($username){
        $stmt = $this->pdo->prepare("SELECT user_id FROM users WHERE username=:username");
        $stmt->bindParam(":username", $username);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_OBJ);
        return $row->user_id;
    }

    public function generate_filename($length)
    {

        $array = array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z');
        $text = "";

        for ($x = 0; $x < $length; $x++) {

            $random = rand(0, 61);
            $text .= $array[$random];
        }

        return $text;
    }


    public function uploadStory($file, $userid)
    {
        $fileInfo = getimagesize($file['tmp_name']);
        $fileName = $file['name'];
        $fileTmp = $file['tmp_name'];
        $fileSize = $file['size'];
        $errors = $file['error'];

        //get extension
        $ext = explode('.', $fileName);
        $ext = strtolower(end($ext));

        $allowed = array("image/png", "image/jpeg", "image/jpg", "image/webp");
        if (in_array($fileInfo['mime'], $allowed)) {
            $path_directory = $_SERVER['DOCUMENT_ROOT'] . "/Instagram/media/stories/" . $userid;

            if (!file_exists($path_directory) && !is_dir($path_directory)) {
                mkdir($path_directory, 0777, true);
            }
            $folder = "media/stories/" . $userid . "/" . $this->generate_filename(15) . '.jpg';
            $file = $_SERVER['DOCUMENT_ROOT'] . "/Instagram/" . $folder;
            if ($errors === 0) {
                move_uploaded_file($fileTmp, $file);
                return $folder;
            }
        }
    }

    public function uploadPost($file,$userid){
        $fileInfo = getimagesize($file['tmp_name']);
        $fileName = $file['name'];
        $fileTmp = $file['tmp_name'];
        $fileSize = $file['size'];
        $errors = $file['error'];

        //get extension
        $ext = explode('.', $fileName);
        $ext = strtolower(end($ext));

        $allowed = array("image/png", "image/jpeg", "image/jpg", "image/webp");
        if (in_array($fileInfo['mime'], $allowed)) {
            $path_directory = $_SERVER['DOCUMENT_ROOT'] . "/Instagram/media/posts/" . $userid;

            if (!file_exists($path_directory) && !is_dir($path_directory)) {
                mkdir($path_directory, 0777, true);
            }
            $folder = "media/posts/" . $userid . "/" . $this->generate_filename(15) . '.jpg';
            $file = $_SERVER['DOCUMENT_ROOT'] . "/Instagram/" . $folder;
            if ($errors === 0) {
                move_uploaded_file($fileTmp, $file);
                return $folder;
            }
        }
    }

    public function createStory($userid, $image)
    {
        $datetime = date('Y-m-d H:i:s');

        $stmt = $this->pdo->prepare("INSERT INTO stories (user_id,story_img,createdAt) VALUES (:userid,:story,:createdAt)");
        $stmt->bindParam(':userid', $userid, PDO::PARAM_INT);
        $stmt->bindParam(':story', $image, PDO::PARAM_STR);
        $stmt->bindParam(':createdAt', $datetime);

        $stmt->execute();

        return $this->pdo->lastInsertId();
    }

      
   
    public function recentStory($userid)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM `stories` s LEFT JOIN users u ON s.user_id=u.user_id WHERE s.story_id IN (SELECT max(story_id) FROM stories WHERE stories.user_id=:userid) AND createdAt >=now() - INTERVAL 1 DAY UNION SELECT * FROM `stories` s LEFT JOIN users u ON s.user_id=u.user_id WHERE s.user_id IN (SELECT follow.receiver  FROM follow WHERE follow.sender=:userid) AND createdAt >=now() - INTERVAL 1 DAY GROUP BY u.user_id ORDER BY story_id DESC");
        $stmt->bindParam(':userid', $userid, PDO::PARAM_INT);

        $stmt->execute();
        $storyData = $stmt->fetchAll(PDO::FETCH_OBJ);
        foreach ($storyData as $user) {
            echo ' <a href="' . url_for('stories/' . $user->username . '/' . $user->user_id) . '" class="story story--has-story">
            <div class="story__avatar">
              <div class="story__border">
                <svg width="64" height="64" xmlns="http://www.w3.org/2000/svg">
                  <circle r="31" cy="32" cx="32" />
                  <defs>
                    <linearGradient y2="0" x2="1" y1="1" x1="0" id="--story-gradient">
                      <stop offset="0" stop-color="#f09433" />
                      <stop offset="0.25" stop-color="#e6683c" />
                      <stop offset="0.5" stop-color="#dc2743" />
                      <stop offset="0.75" stop-color="#cc2366" />
                      <stop offset="1" stop-color="#bc1888" />
                    </linearGradient>
                  </defs>
                </svg>
              </div>
              <div class="story__picture">
                <img src="' . url_for($user->profileImage) . '" alt="Photo of  ' . $user->fullName . '">
              </div>
            </div>
            <span class="story__user">' . $user->username . '</span>
          </a>';
        }
    }

    public function checkStoryExist($userid)
    {
        $stmt = $this->pdo->prepare('SELECT * from stories WHERE user_id=:userid AND createdAt >=now() - INTERVAL 1 DAY');
        $stmt->bindParam(":userid", $userid, PDO::PARAM_INT);
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function statusData($userid)
    {
        $stmt = $this->pdo->prepare('SELECT * from stories s LEFT JOIN users u ON s.user_id=u.user_id WHERE s.user_id=:userid AND createdAt >=now() - INTERVAL 1 DAY ORDER BY story_id DESC');
        $stmt->bindParam(":userid", $userid, PDO::PARAM_INT);
        $stmt->execute();
        $statusData = $stmt->fetchAll(PDO::FETCH_OBJ);
        if ($stmt->rowCount() > 0) {
            foreach ($statusData as $user) {
                echo '<img src="' . url_for($user->story_img) . '" alt="Story of ' . $user->fullName . '">';
            }
        }
    }
}
