<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../user/login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Article Details</title>
    <link rel="stylesheet" href="../css/styles.css">
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
</head>
<body>
<div class="article-details">
    <h1 id="articleTitle"></h1>
    <div>Author: <span id="articleAuthors"></span></div>
    <div>Tags: <span id="articleTags"></span></div>
    <div>Creation Time: <span id="articleCreatedAt"></span></div>
    <div>Update Time: <span id="articleUpdatedAt"></span></div>
    <p id="articleContent"></p>
    <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
        <form action="update_article.php" method="POST">
            <input type="hidden" name="article_id" id="updateArticleId">
            <input type="submit" value="Edit Article">
        </form>
        <form action="article_tag_choice.php" method="POST">
            <input type="hidden" name="article_id" id="editTagsArticleId">
            <input type="submit" value="Edit Tags">
        </form>
        <button onclick="deleteArticle()">Delete Article</button>
    <?php endif; ?>
</div>

<script>
    function fetchArticleDetails() {
        const articleId = new URLSearchParams(window.location.search).get('id');
        if (!articleId) {
            alert('Article ID is missing');
            return;
        }

        axios.get(`../../src/api/article.php?id=${articleId}`)
            .then(function (response) {
                if (response.data.success) {
                    const article = response.data.article;
                    document.getElementById('updateArticleId').value = articleId; // Update the hidden field in the update form
                    document.getElementById('editTagsArticleId').value = articleId; // Update the hidden field in the edit tags form
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
        const articleId = new URLSearchParams(window.location.search).get('id');
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
                    alert('An error occurred while deleting the article');
                });
        }
    }

    document.addEventListener('DOMContentLoaded', fetchArticleDetails);
</script>
</body>
</html>
