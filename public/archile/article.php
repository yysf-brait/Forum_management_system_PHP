<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../user/login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <title>文章详情</title>
    <link rel="stylesheet" href="../css/styles.css">
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
</head>
<body>
<div class="article-details">
    <h1 id="articleTitle"></h1>
    <div>作者: <span id="articleAuthors"></span></div>
    <div>标签: <span id="articleTags"></span></div>
    <div>创建时间: <span id="articleCreatedAt"></span></div>
    <div>更新时间: <span id="articleUpdatedAt"></span></div>
    <p id="articleContent"></p>
    <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
        <form action="update_article.php" method="POST">
            <input type="hidden" name="article_id" id="updateArticleId">
            <input type="submit" value="编辑文章">
        </form>
        <form action="article_tag_choice.php" method="POST">
            <input type="hidden" name="article_id" id="editTagsArticleId">
            <input type="submit" value="编辑标签">
        </form>
        <button onclick="deleteArticle()">删除文章</button>
    <?php endif; ?>
</div>

<script>
    function fetchArticleDetails() {
        const articleId = new URLSearchParams(window.location.search).get('id');
        if (!articleId) {
            alert('文章 ID 缺失');
            return;
        }

        axios.get(`../../src/api/article.php?id=${articleId}`)
            .then(function (response) {
                if (response.data.success) {
                    const article = response.data.article;
                    document.getElementById('updateArticleId').value = articleId; // 更新文章表单的隐藏字段
                    document.getElementById('editTagsArticleId').value = articleId; // 编辑标签表单的隐藏字段
                    document.getElementById('articleTitle').textContent = article.title;
                    document.getElementById('articleAuthors').textContent = article.authors;
                    document.getElementById('articleTags').textContent = article.tags;
                    document.getElementById('articleCreatedAt').textContent = article.created_at;
                    document.getElementById('articleUpdatedAt').textContent = article.updated_at;
                    document.getElementById('articleContent').innerHTML = article.content.replace(/\n/g, '<br>');
                } else {
                    alert(response.data.message);
                }
            })
            .catch(function (error) {
                console.error('Error fetching article details:', error);
            });
    }

    function deleteArticle() {
        const articleId = document.getElementById('articleId').value;
        if (confirm("Are you sure you want to delete this article?")) {
            axios.get(`../../src/api/delete_article.php?article_id=${articleId}`)
                .then(function (response) {
                    alert(response.data.message);
                    if (response.data.message.startsWith('Success')) {
                        // back
                        window.history.back();
                    }
                })
                .catch(function (error) {
                    console.error('Error deleting the article:', error);
                    alert('删除文章时发生错误');
                });
        }
    }

    document.addEventListener('DOMContentLoaded', fetchArticleDetails);
</script>
</body>
</html>
