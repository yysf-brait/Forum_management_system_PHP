<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Article List</title>
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
    <button onclick="window.location.href='search_results.php'">Go to Search Page</button>
</div>

<!-- Add a button for creating articles -->
<div class="create-article-container">
    <button onclick="window.location.href='archile/create_article.php'">Create New Article</button>
</div>

<div class="tags-container">
    <!-- Tag buttons will be dynamically generated here -->
    <button id="toggleTagLogic" class="toggle-button">Switch to All-Inclusive</button>
</div>
<div class="container mt-5">
    <h1>Latest Articles</h1>
    <ul id="articlesList" class="list-group list-group-flush">
        <!-- Article list will be dynamically populated here -->
    </ul>
</div>

<div class="pagination-controls">
    <button id="prevPage">Previous Page</button>
    <span id="currentPageDisplay"></span>
    <button id="nextPage">Next Page</button>
    <span>Jump to page:</span>
    <label for="pageInput"></label><input type="number" id="pageInput" min="1" style="width: 50px;">
    <button id="jumpPageButton">Jump</button>
    <label for="pageSizeSelect"></label><select id="pageSizeSelect">
        <option value="5">5 per page</option>
        <option value="10" selected>10 per page</option>
        <option value="15">15 per page</option>
        <option value="20">20 per page</option>
    </select>
</div>

<script>
    var currentPage = 1; // Initial page
    var totalPages = 0; // Total pages
    var pageSize = 10; // Initial number of items per page
    var inclusiveTags = false; // Initially set to non-inclusive tag filtering logic

    document.addEventListener('DOMContentLoaded', function () {
        loadTags();
        fetchTotalPages();
        loadArticles(currentPage);

        document.getElementById('pageSizeSelect').addEventListener('change', function () {
            pageSize = parseInt(this.value);
            fetchTotalPages();
            loadArticles(1);
            currentPage = 1;
            updatePaginationDisplay();
        });

        document.getElementById('prevPage').addEventListener('click', function () {
            if (currentPage > 1) {
                loadArticles(--currentPage);
            }
        });

        document.getElementById('nextPage').addEventListener('click', function () {
            if (currentPage < totalPages) {
                loadArticles(++currentPage);
            }
        });

        document.getElementById('jumpPageButton').addEventListener('click', jumpToPage);

        document.getElementById('toggleTagLogic').addEventListener('click', function () {
            inclusiveTags = !inclusiveTags;
            this.textContent = inclusiveTags ? 'Switch to Any-Inclusive' : 'Switch to All-Inclusive';
            this.classList.toggle('active');
            loadArticles(currentPage);
        });

        function fetchTotalPages() {
            axios.get('../src/api/article_count_view.php')
                .then(function (response) {
                    totalPages = Math.ceil(response.data.article_count / pageSize);
                    updatePaginationDisplay();
                })
                .catch(function (error) {
                    console.error('Error fetching total pages:', error);
                });
        }

        function loadTags() {
            axios.get('../src/api/tags.php')
                .then(function (response) {
                    const tagsContainer = document.querySelector('.tags-container');
                    response.data.forEach(tag => {
                        const button = document.createElement('button');
                        button.textContent = tag.tag_name;
                        button.className = 'tag-button';
                        button.setAttribute('data-tag-id', tag.tag_id);
                        button.onclick = function () {
                            toggleTagSelection(this);
                        };
                        tagsContainer.appendChild(button);
                    });
                })
                .catch(function (error) {
                    console.error('Error loading tags:', error);
                });
        }

        function loadArticles(page) {
            const selectedTags = document.querySelectorAll('.tag-button.selected');
            const tagIds = Array.from(selectedTags).map(btn => btn.getAttribute('data-tag-id')).join(',');
            const apiURL = inclusiveTags ? '../src/api/articles_all_tags.php' : '../src/api/articles.php';
            //console.log(`${apiURL}?pageNum=${page}&pageSize=${pageSize}&tagIds=${tagIds}`)
            axios.get(`${apiURL}?pageNum=${page}&pageSize=${pageSize}&tagIds=${tagIds}`)
                .then(function (response) {
                    const articlesList = document.getElementById('articlesList');
                    articlesList.innerHTML = '';
                    response.data.articles.forEach(article => {
                        const li = document.createElement('li');
                        li.className = 'list-group-item';

                        li.innerHTML = `
                <strong><a href="archile/article.php?id=${article.article_id}">${article.title}</a></strong> - Updated date: ${article.updated_at}<br>
                Creation date: ${article.created_at}<br>
                Author: ${article.authors || 'Unknown'}<br>
                Tags: ${article.tags || 'None'}`;
                        articlesList.appendChild(li);
                    });
                    if (response.data.total !== null)
                        totalPages = Math.ceil(response.data.total / pageSize);
                    else
                        fetchTotalPages();
                    updatePaginationDisplay();
                })
                .catch(function (error) {
                    console.error('Error loading the articles:', error);
                });
        }

        function toggleTagSelection(button) {
            button.classList.toggle('selected');
            loadArticles(1);
            currentPage = 1;
            updatePaginationDisplay();
        }

        function updatePaginationDisplay() {
            const currentPageDisplay = document.getElementById('currentPageDisplay');
            currentPageDisplay.textContent = `Page ${currentPage} of ${totalPages}`;
        }

        function jumpToPage() {
            const page = parseInt(document.getElementById('pageInput').value);
            if (page > 0 && page <= totalPages) {
                currentPage = page;
                loadArticles(currentPage);
            }
        }
    });
</script>
</body>
</html>
