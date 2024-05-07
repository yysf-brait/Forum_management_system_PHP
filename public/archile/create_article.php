<?php
session_start();
?>
<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <title>创建文章</title>
    <link rel="stylesheet" href="../css/styles1.css">
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
</head>
<body>
<div class="user-info">
    <?php if (isset($_SESSION['username'])): ?>
        <a href="../user/profile.php"><?php echo htmlspecialchars($_SESSION['username']); ?></a>
    <?php else:
        header('Location: ../user/login.php');
    endif; ?>
</div>
<div class="container">
    <h1>创建新文章</h1>
    <div>
        <label for="title">标题:</label>
        <input type="text" id="title" name="title" required>
    </div>
    <div>
        <label for="content">内容:</label>
        <textarea id="content" name="content" required></textarea>
    </div>
    <button type="button" onclick="submitArticle()">提交文章</button>
</div>

<script>
function submitArticle() {
    var title = document.getElementById('title').value.trim();
    var content = document.getElementById('content').value.trim();

    // 进行输入验证
    if (!title || !content) {
        alert('标题和内容不能为空');
        return;
    }

    // console.log('Creating article:', title, content);

    // 构建GET请求的URL
    var url = new URL('../../src/api/create_article.php', window.location.origin);
    url.searchParams.append('title', title);
    url.searchParams.append('content', content);

    axios.get(url.toString())
    .then(function (response) {
        if (response.data.success) {
            alert('文章创建成功！');
            window.location.href = 'article.php?id=' + response.data.new_article_id;
        } else {
            alert('创建文章失败：' + response.data.message);
        }
    })
    .catch(function (error) {
        console.error('Error creating the article:', error);
        alert('创建文章时发生错误');
    });
}
</script>
</body>
</html>
