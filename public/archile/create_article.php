<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Article</title>
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
    <h1>Create New Article</h1>
    <div>
        <label for="title">Title:</label>
        <input type="text" id="title" name="title" required>
    </div>
    <div>
        <label for="content">Content:</label>
        <textarea id="content" name="content" required></textarea>
    </div>
    <button type="button" onclick="submitArticle()">Submit Article</button>
</div>

<script>
function submitArticle() {
    var title = document.getElementById('title').value.trim();
    var content = document.getElementById('content').value.trim();

    // Validate inputs
    if (!title || !content) {
        alert('Title and content cannot be empty');
        return;
    }

    // console.log('Creating article:', title, content);

    // Build the GET request URL
    var url = new URL('../../src/api/create_article.php', window.location.origin);
    url.searchParams.append('title', title);
    url.searchParams.append('content', content);

    axios.get(url.toString())
    .then(function (response) {
        if (response.data.success) {
            alert('Article created successfully!');
            window.location.href = 'article.php?id=' + response.data.new_article_id;
        } else {
            alert('Failed to create article: ' + response.data.message);
        }
    })
    .catch(function (error) {
        console.error('Error creating the article:', error);
        alert('An error occurred while creating the article');
    });
}
</script>
</body>
</html>
