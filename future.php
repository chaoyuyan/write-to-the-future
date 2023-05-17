<?php
session_start();
date_default_timezone_set('Asia/Shanghai');
function encrypt_content($content, $key) {
    $iv = random_bytes(16);
    return openssl_encrypt($content, 'AES-128-CBC', $key, OPENSSL_RAW_DATA, $iv);
}

function decrypt_content($content, $key) {
    $iv = substr($content, 0, 16);
    $content = substr($content, 16);
    return openssl_decrypt($content, 'AES-128-CBC', $key, OPENSSL_RAW_DATA, $iv);
}


function count_files($path, $pattern = '*') {
    $files = glob($path . $pattern);
    return count($files);
}

// 判断是否提交表单
if (isset($_POST['submit'])) {
    // 获取用户输入的邮件信息
    $content = $_POST['content'];
    $date = $_POST['date'];
  // 确定要使用的密钥
    if (isset($_POST['key']) && !empty($_POST['key'])) {
        $key = $_POST['key'];
    } else {
        $key = date('Ymd', strtotime($date));
    }
	// 检查密钥是否只包含字母和数字
    if (!preg_match('/^[a-zA-Z0-9]+$/', $key)) {
        // 如果密钥包含其他字符，返回错误提示
        $_SESSION['error'] = '密钥只能包含字母和数字';
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }
	
    // 生成文件名
    $filename = date('Ymd', strtotime($date)) . '_' . $key . '_' . rand(1000, 9999);
    $filename .= '.txt';

    // 写入文件
    $file = fopen('letters/' . $filename, 'w');
    
    $encrypted_content = encrypt_content($content, $key);
    fwrite($file, $encrypted_content);
    fclose($file);

    // 输出提示信息
    $_SESSION['message'] = '邮件已保存到服务器。'.$filename;
	header('Location: ' . $_SERVER['PHP_SELF']);
exit;
}
//下载文件
if (isset($_GET['download'])) {
    $filename = 'letters/' . $_GET['download'];
    if (file_exists($filename)) {
		
        // 从文件名中获取密钥
        $parts = explode('_', basename($filename));
        $key1 = $parts[1];
		$date1 = $parts[0]; 
        $current_date = date_format(date_create('now'), 'Ymd');// 获取当前日期并格式化为Ymd		
		if ($date1<$current_date) {
            echo '文件不可下载哦，你还没有抵达收信的日子！';
            exit;
        }


        // 检查是否提供了正确的密钥
        if (!isset($_GET['key1']) || empty($_GET['key1']) || $_GET['key1'] !== $key1) {
            echo '请输入正确的密钥';
            exit;
        }
		
        $key1 = $_GET['key1'];
        $content = decrypt_content(file_get_contents($filename), $key1);
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($filename) . '"');
        echo $content;
        exit;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="keywords" content="穿越, 信件, 未来, 自己, 记录, 经验, 回忆, 信息, 心情, 想法, 挑战, time travel, letter, future, self, record, experience, memory, information, mood, thoughts, challenges">
  <meta name="description" content="写给未来的自己，记录下此刻的心情和想法，传递重要信息和经验教训，让未来的自己回忆起过去的美好时光，同时帮助未来的自己更好地应对未来的挑战。Write a letter to the future self, record the current mood and thoughts, convey important information and lessons learned, let the future self recall the beautiful moments of the past, and help the future self better cope with future challenges.">
  <title>写给未来的信 - Write to the future a letter</title>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="1.css" id="css-file">
	 <script>
    var cssFile = document.getElementById("css-file");
    var cssIndex = 1; // 初始CSS文件索引为1

    function changeCSS() {
      cssIndex = cssIndex % 3 + 1; // 切换到下一个CSS文件
      cssFile.setAttribute("href", cssIndex + ".css"); // 更新CSS文件路径
    }
  </script>
</head>
<body><button onclick="changeCSS()">切换CSS</button>
    <h1>写给未来的信</h1>
    <?php if (isset($_SESSION['message'])): ?>
        <p><?= $_SESSION['message'] ?></p>
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>
	<?php if (isset($_SESSION['error'])): ?>
        <p><?= $_SESSION['error'] ?></p>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>
    <form method="post">
	
		<p>
            <label>接收日期：</label>
            <input type="date" name="date">
        </p>
        <p>
            <label>信件内容：</label>
            <textarea name="content" rows="5" cols="30"></textarea>
        </p>
        
		<p>
            <label>设置密钥：</label>
            <input type="text" name="key">
        </p>
        <p>
            <input type="submit" name="submit" value="发送">
        </p>
    </form>
    <div>
        <h2>展示区</h2>
        <p>已有 <?= count_files('letters/') ?> 人给未来写了信。</p>
    </div>
    <div>
        <h2>下载区</h2>
        <form method="get">
            <p>
                <label>文件名：</label>
                <input type="text" name="download">
            </p>
           
                <p>
                    <label>密钥：</label>
                    <input type="password" name="key1">
                </p>
            <input type="submit" value="下载">
        </form>
    </div>
</body>
</html>
