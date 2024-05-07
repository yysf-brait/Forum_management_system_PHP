<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <title>编辑文章标签</title>
    <link rel="stylesheet" href="../css/styles.css">
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
</head>
<body>
<div class="container">
    <h1>编辑文章标签</h1>
    <div id="tags-container"></div>
    <button onclick="updateArticleTags()">更新标签</button>
</div>

<div id="add-tag-form">
    <label for="newTagName"></label><input type="text" id="newTagName" placeholder="输入新标签名">
    <button onclick="addNewTag()">添加新标签</button>
</div>


<script>
    const articleId = <?php echo $_POST['article_id'] ?? 0; ?>;
    let initialSelectedTags = new Set();  // 初始已选中的标签
    let selectedTags = new Set();         // 当前已选中的标签
    let addTags = new Set();              // 要添加的标签
    let removeTags = new Set();           // 要删除的标签

    // 加载标签并标记已选中的
    function loadTags() {
        axios.get('../../src/api/tags.php')
            .then(function (response) {
                const tags = response.data;
                const container = document.getElementById('tags-container');
                container.innerHTML = '';
                axios.get(`../../src/api/get_article_tags.php?article_id=${articleId}`)
                    .then(res => {
                        const activeTags = res.data.tags.map(tag => tag.tag_id); // 获取此文章已有的标签

                        tags.forEach(tag => {
                            const tagElement = document.createElement('button');
                            tagElement.textContent = tag.tag_name;
                            tagElement.classList.add('tag-button');
                            tagElement.dataset.tagId = tag.tag_id;

                            if (activeTags.includes(parseInt(tag.tag_id))) {
                                tagElement.classList.add('selected');
                                selectedTags.add(tag.tag_id);
                                initialSelectedTags.add(tag.tag_id);
                            }

                            tagElement.onclick = function () {
                                const tagId = this.dataset.tagId;
                                if (selectedTags.has(tagId)) {
                                    selectedTags.delete(tagId);
                                    this.classList.remove('selected');
                                } else {
                                    selectedTags.add(tagId);
                                    this.classList.add('selected');
                                }
                                updateTagSets(tagId);
                            };
                            container.appendChild(tagElement);
                        });
                    })
                    .catch(error => {
                        console.error('加载文章标签失败:', error);
                    });
            })
            .catch(function (error) {
                console.error('加载所有标签失败:', error);
            });
    }

    function updateTagSets(tagId) {
        if (initialSelectedTags.has(tagId)) {
            if (selectedTags.has(tagId)) {
                removeTags.delete(tagId);
            } else {
                removeTags.add(tagId);
            }
            addTags.delete(tagId);
        } else {
            if (selectedTags.has(tagId)) {
                addTags.add(tagId);
                removeTags.delete(tagId);
            } else {
                addTags.delete(tagId);
            }
        }
    }

    // 更新文章标签
    function updateArticleTags() {
        const formData = new FormData();
        formData.append('article_id', articleId);
        formData.append('add_tags', Array.from(addTags).join(','));
        formData.append('remove_tags', Array.from(removeTags).join(','));

        axios.post('../../src/api/article_tag.php', formData)
            .then(function (response) {
                alert('标签更新成功');
                // 重置初始选中状态和集合
                initialSelectedTags = new Set([...selectedTags]);
                addTags.clear();
                removeTags.clear();
            })
            .catch(function (error) {
                console.error('更新标签失败:', error);
            });
    }

    function addNewTag() {
    const tagName = document.getElementById('newTagName').value.trim();
    if (!tagName) {
        alert('标签名不能为空！');
        return;
    }
    const url = `../../src/api/add_tag.php?name=${encodeURIComponent(tagName)}&article_id=${encodeURIComponent(articleId)}`;
    axios.get(url)
    .then(response => {
        if (response.data.success) {
            alert('新标签添加成功！');
            document.getElementById('newTagName').value = ''; // 清空输入框
            loadTags(); // 重新加载标签
        } else {
            alert('添加新标签失败: ' + response.data.message);
        }
    })
    .catch(error => {
        console.error('添加新标签失败:', error);
    });
}


    document.addEventListener('DOMContentLoaded', loadTags);

</script>
</body>
</html>
