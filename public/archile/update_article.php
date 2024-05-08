<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Article</title>
    <link rel="stylesheet" href="../css/styles.css">
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
</head>
<body>
<div class="container">
    <h1>Edit Article</h1>
    <form id="updateArticleForm" onsubmit="submitUpdate(); return false;">
        <input type="hidden" name="article_id" id="articleId">
        <div>
            <label for="title">Title:</label>
            <input type="text" id="title" name="title" placeholder="Article Title">
        </div>
        <div>
            <label for="content">Content:</label>
            <textarea id="content" name="content" placeholder="Article Content"></textarea>
        </div>
        <button type="submit">Submit Changes</button>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const articleId = <?php echo $_POST['article_id'] ?? 0; ?>;
        if (!articleId) {
            alert('Error: Article ID is missing.');
            // back to the previous page
            window.history.back();
            return;
        }

        // Fill article ID into hidden field
        document.getElementById('articleId').value = articleId;

        // Fetch article details and fill them into the form
        fetch(`../../src/api/article.php?id=${articleId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('title').value = data.article.title;
                    document.getElementById('content').value = data.article.content;
                } else {
                    alert(data.message || 'Failed to fetch article details');
                }
            })
            .catch(error => {
                console.error('Failed to fetch article details:', error);
                alert('An error occurred while fetching article details');
            });
    });

    function submitUpdate() {
        // Build GET request URL
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
                console.error('Failed to update article:', error);
                alert('An error occurred while updating the article');
            });
        return false; // Prevent default form submission
    }
</script>
</body>
</html>
