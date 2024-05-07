<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <title>编辑文章</title>
    <link rel="stylesheet" href="../css/styles.css">
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
</head>
<body>
<div class="container">
    <h1>编辑文章</h1>
    <form id="updateArticleForm" onsubmit="submitUpdate(); return false;">
        <input type="hidden" name="article_id" id="articleId">
        <div>
            <label for="title">标题:</label>
            <input type="text" id="title" name="title" placeholder="文章标题">
        </div>
        <div>
            <label for="content">内容:</label>
            <textarea id="content" name="content" placeholder="文章内容"></textarea>
        </div>
        <button type="submit">提交更改</button>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const articleId = <?php echo $_POST['article_id'] ?? 0; ?>;
        if (!articleId) {
            alert('错误：文章ID缺失。');
            // back to the previous page
            window.history.back();
            return;
        }

        // 填充文章ID到隐藏字段
        document.getElementById('articleId').value = articleId;

        // 获取文章的详细信息并填充到表单中
        fetch(`../../src/api/article.php?id=${articleId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('title').value = data.article.title;
                    document.getElementById('content').value = data.article.content;
                } else {
                    alert(data.message || '获取文章详情失败');
                }
            })
            .catch(error => {
                console.error('获取文章详情失败:', error);
                alert('获取文章详情时发生错误');
            });
    });

    function submitUpdate() {
        // 构建GET请求的URL
        var url = new URL('../../src/api/update_article.php', window.location.origin);
        url.searchParams.append('article_id', document.getElementById('articleId').value);
        url.searchParams.append('title', document.getElementById('title').value);
        url.searchParams.append('content', document.getElementById('content').value);

        axios.get(url.toString())
            .then(function (response) {
                const data = response.data;
                if (data.message) {
                    alert(data.message);
                    if (response.status === 200) {
                        window.location.href = 'article.php?id=' + document.getElementById('articleId').value;
                    }
                }
            })
            .catch(error => {
                console.error('更新文章失败:', error);
                alert('更新文章时发生错误');
            });
        return false; // Prevent default form submission
    }
</script>
</body>
</html>
