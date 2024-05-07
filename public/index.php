<?php
session_start();  // 开始或继续会话
?>
<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <title>文章列表</title>
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
    <button onclick="window.location.href='search_results.php'">前往搜索页面</button>
</div>

<!-- Add a button for creating articles -->
<div class="create-article-container">
    <button onclick="window.location.href='archile/create_article.php'">创建新文章</button>
</div>

<div class="tags-container">
    <!-- 标签按钮将在这里动态生成 -->
    <button id="toggleTagLogic" class="toggle-button">切换至全包含</button>
</div>
<div class="container mt-5">
    <h1>最新文章</h1>
    <ul id="articlesList" class="list-group list-group-flush">
        <!-- 文章列表将在这里动态填充 -->
    </ul>
</div>

<div class="pagination-controls">
    <button id="prevPage">上一页</button>
    <span id="currentPageDisplay"></span>
    <button id="nextPage">下一页</button>
    <span>跳转到页码:</span>
    <label for="pageInput"></label><input type="number" id="pageInput" min="1" style="width: 50px;">
    <button id="jumpPageButton">跳转</button>
    <label for="pageSizeSelect"></label><select id="pageSizeSelect">
        <option value="5">5条/页</option>
        <option value="10" selected>10条/页</option>
        <option value="15">15条/页</option>
        <option value="20">20条/页</option>
    </select>
</div>

<script>
    var currentPage = 1; // 初始页面
    var totalPages = 0; // 总页数
    var pageSize = 10; // 初始每页显示条数
    var inclusiveTags = false; // 初始为非全包含标签筛选逻辑

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
            this.textContent = inclusiveTags ? '切换至任意包含' : '切换至全包含';
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
                <strong><a href="archile/article.php?id=${article.article_id}">${article.title}</a></strong> - 更新日期：${article.updated_at}<br>
                创建日期: ${article.created_at}<br>
                作者: ${article.authors || '未知'}<br>
                标签: ${article.tags || '无'}`;
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
            currentPageDisplay.textContent = `第 ${currentPage} 页，共 ${totalPages} 页`;
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
