<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Search Articles</title>
    <link rel="stylesheet" href="css/styles.css">
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
</head>
<body>
<div class="user-info">
    <?php if (isset($_SESSION['username'])): ?>
        <a href="user/profile.php"><?php echo htmlspecialchars($_SESSION['username']); ?></a>
    <?php else: ?>
        <a href="user/login.php">Login</a>
    <?php endif; ?>
</div>
<div class="search-container">
    <label for="searchQueryInput"></label><input type="text" id="searchQueryInput"
                                                 placeholder="Search article titles...">
    <button onclick="reSearchArticles()">Search</button>
</div>
<div class="container">
    <ul id="articlesList" class="list-group">
        <!-- Search results will be displayed here -->
    </ul>
</div>
<div class="pagination-controls">
    <button id="prevPage">Previous Page</button>
    <span id="currentPageDisplay"></span>
    <button id="nextPage">Next Page</button>
    <span>Items per page:</span>
    <label for="pageSizeSelect"></label><select id="pageSizeSelect">
        <option value="5">5 items</option>
        <option value="10" selected>10 items</option>
        <option value="15">15 items</option>
        <option value="20">20 items</option>
    </select>
    <span>Jump to page:</span>
    <label for="pageInput"></label><input type="number" id="pageInput" min="1" style="width: 50px;">
    <button onclick="jumpToPage()">Jump</button>
</div>

<script>
    var currentPage = 1;
    var pageSize = 10;
    var totalPages = 0;

    document.getElementById('pageSizeSelect').addEventListener('change', function () {
        pageSize = parseInt(this.value);
        searchArticles();  // Re-search and reset page number
    });

    document.getElementById('prevPage').addEventListener('click', function () {
        if (currentPage > 1) {
            loadArticles(--currentPage);
        }
        searchArticles();
    });

    document.getElementById('nextPage').addEventListener('click', function () {
        if (currentPage < totalPages) {
            loadArticles(++currentPage);
        }
        searchArticles();
    });

    function searchArticles() {
        var query = document.getElementById('searchQueryInput').value;
        loadArticles(query, currentPage);
    }

    function reSearchArticles() {
        currentPage = 1;
        searchArticles();
    }

    function loadArticles(query, page) {
        axios.get(`../src/api/search_articles.php`, {
            params: {query: query, pageNum: page, pageSize: pageSize}
        }).then(function (response) {
            const articlesList = document.getElementById('articlesList');
            articlesList.innerHTML = '';  // Clear the list
            response.data.articles.forEach(article => {
                const li = document.createElement('li');
                li.className = 'list-group-item';
                li.innerHTML = `<strong><a href="archile/article.php?id=${article.article_id}">${article.title}</a></strong> - Updated date: ${article.updated_at}<br>Creation date: ${article.created_at}<br>Author: ${article.authors || 'Unknown'}<br>Tags: ${article.tags || 'None'}`;
                articlesList.appendChild(li);
            });
            totalPages = Math.ceil(response.data.total / pageSize);
            updatePaginationDisplay();
        }).catch(function (error) {
            console.error('Error loading the articles:', error);
        });
    }

    function updatePaginationDisplay() {
        const currentPageDisplay = document.getElementById('currentPageDisplay');
        currentPageDisplay.textContent = `Page ${currentPage} of ${totalPages}`;
    }

    function jumpToPage() {
        var page = parseInt(document.getElementById('pageInput').value);
        if (page > 0 && page <= totalPages) {
            currentPage = page;
            searchArticles();  // Reload articles for the specified page
        }
    }
</script>
</body>
</html>
