<?php
// 外部ファイルに書き込み
$datafile = 'bbs.dat';

session_start();

function setToken() {
  $token = sha1(uniqid(mt_rand(), true));
  $_SESSION['token'] = $token;
}

function checkToken() {
  if (empty($_SESSION['token']) || ($_SESSION['token'] != $_POST['token'])) {
  echo "不正なPOSTが行われました!";
  exit;
  }
}

function h($s) {
  return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' &&
  isset($_POST['message']) &&
  isset($_POST['user'])) {

  checkToken();
  $message = trim($_POST['message']);
  $user = trim($_POST['user']);

  // もしメッセージが空欄ではなく
  if ($message !== '') {
  // もしユーザー名が空欄なら
  $user = ($user === '') ? 'ななしさん' : $user;

  $message = str_replace("\t", ' ', $message);
  $user = str_replace("\t", ' ', $user);

  $postedAt = date('Y-m-d H:i:s');

  // 変数newDataにメッセージ、ユーザー名、投稿日時を代入
  $newData = $message . "\t" . $user . "\t" . $postedAt. "\n";

  $fp = fopen($datafile, 'a');
  fwrite($fp, $newData);
  fclose($fp);
  }
} else {
  setToken();
}

$posts = file($datafile, FILE_IGNORE_NEW_LINES);

// var_dump($posts);

$posts = array_reverse($posts);
?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <title>簡易掲示板</title>
</head>
<body>
  <h1>簡易掲示板</h1>
  <form action="" method="post">
    message: <input type="text" name="message">
    user: <input type="text" name="user">
    <input type="submit" value="投稿">
    <input type="hidden" name="token" value="<?php echo h($_SESSION['token']); ?>">
  </form>
  <h2>投稿一覧 (<?php echo count($posts); ?>件) </h2>
  <ul>
    <!-- もし投稿があれば -->
    <?php if (count($posts)) : ?>
      <?php foreach ($posts as $post) : ?>
      <?php list($message, $user, $postedAt) = explode("\t", $post); ?>
        <li><?php echo h($message); ?> (<?php echo h($user); ?>) - <?php echo h($postedAt); ?></li>
      <?php endforeach; ?>
    <?php else : ?>
      <li>まだ投稿はありません。</li>
    <?php endif; ?>
  </ul>
</body>
</html>
