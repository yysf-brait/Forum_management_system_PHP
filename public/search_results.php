<?php
session_start();
?>
<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <title>搜索文章</title>
    <link rel="stylesheet" href="css/styles.css">
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
</head>
<body>
<div class="user-info">
    <?php if (isset($_SESSION['username'])): ?>
        <a href="user/profile.php"><?php echo htmlspecialchars($_SESSION['username']); ?></a>
    <?php else: ?>
        <a href="user/login.php">登录</a>
    <?php endif; ?>
</div>
<div class="search-container">
    <label for="searchQueryInput"></label><input type="text" id="searchQueryInput" placeholder="搜索文章标题...">
    <button onclick="reSearchArticles()">搜索</button>
</div>
<div class="container">
    <ul id="articlesList" class="list-group">
        <!-- 搜索结果将在这里显示 -->
    </ul>
</div>
<div class="pagination-controls">
    <button id="prevPage">上一页</button>
    <span id="currentPageDisplay"></span>
    <button id="nextPage">下一页</button>
    <span>每页显示：</span>
    <label for="pageSizeSelect"></label><select id="pageSizeSelect">
        <option value="5">5条</option>
        <option value="10" selected>10条</option>
        <option value="15">15条</option>
        <option value="20">20条</option>
    </select>
    <span>跳转到页码:</span>
    <label for="pageInput"></label><input type="number" id="pageInput" min="1" style="width: 50px;">
    <button onclick="jumpToPage()">跳转</button>
</div>

<script>
    var currentPage = 1;
    var pageSize = 10;
    var totalPages = 0;

    document.getElementById('pageSizeSelect').addEventListener('change', function() {
        pageSize = parseInt(this.value);
        searchArticles();  // 重新搜索并重置页码
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
            params: { query: query, pageNum: page, pageSize: pageSize }
        }).then(function (response) {
            const articlesList = document.getElementById('articlesList');
            articlesList.innerHTML = '';  // 清空列表
            response.data.articles.forEach(article => {
                const li = document.createElement('li');
                li.className = 'list-group-item';
                li.innerHTML = `<strong><a href="archile/article.php?id=${article.article_id}">${article.title}</a></strong> - 更新日期：${article.updated_at}<br>创建日期: ${article.created_at}<br>作者: ${article.authors || '未知'}<br>标签: ${article.tags || '无'}`;
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
        currentPageDisplay.textContent = `第 ${currentPage} 页，共 ${totalPages} 页`;
    }

    function jumpToPage() {
        var page = parseInt(document.getElementById('pageInput').value);
        if (page > 0 && page <= totalPages) {
            currentPage = page;
            searchArticles();  // 重新加载指定页码的文章
        }
    }
</script>
</body>
</html>
