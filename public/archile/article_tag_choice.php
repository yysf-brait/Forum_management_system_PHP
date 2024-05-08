<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Article Tags</title>
    <link rel="stylesheet" href="../css/styles.css">
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
</head>
<body>
<div class="container">
    <h1>Edit Article Tags</h1>
    <div id="tags-container"></div>
    <button onclick="updateArticleTags()">Update Tags</button>
</div>

<div id="add-tag-form">
    <label for="newTagName"></label><input type="text" id="newTagName" placeholder="Enter new tag name">
    <button onclick="addNewTag()">Add New Tag</button>
</div>


<script>
    const articleId = <?php echo $_POST['article_id'] ?? 0; ?>;
    let initialSelectedTags = new Set();  // Initially selected tags
    let selectedTags = new Set();         // Currently selected tags
    let addTags = new Set();              // Tags to be added
    let removeTags = new Set();           // Tags to be removed

    // Load tags and mark those already selected
    function loadTags() {
        axios.get('../../src/api/tags.php')
            .then(function (response) {
                const tags = response.data;
                const container = document.getElementById('tags-container');
                container.innerHTML = '';
                axios.get(`../../src/api/get_article_tags.php?article_id=${articleId}`)
                    .then(res => {
                        const activeTags = res.data.tags.map(tag => tag.tag_id); // Get tags already associated with this article

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
                        console.error('Failed to load article tags:', error);
                    });
            })
            .catch(function (error) {
                console.error('Failed to load all tags:', error);
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

    // Update article tags
    function updateArticleTags() {
        const formData = new FormData();
        formData.append('article_id', articleId);
        formData.append('add_tags', Array.from(addTags).join(','));
        formData.append('remove_tags', Array.from(removeTags).join(','));

        axios.post('../../src/api/article_tag.php', formData)
            .then(function (response) {
                alert('Tags updated successfully');
                // Reset the initial selected state and sets
                initialSelectedTags = new Set([...selectedTags]);
                addTags.clear();
                removeTags.clear();
            })
            .catch(function (error) {
                console.error('Failed to update tags:', error);
            });
    }

    function addNewTag() {
    const tagName = document.getElementById('newTagName').value.trim();
    if (!tagName) {
        alert('The tag name cannot be empty!');
        return;
    }
    const url = `../../src/api/add_tag.php?name=${encodeURIComponent(tagName)}&article_id=${encodeURIComponent(articleId)}`;
    axios.get(url)
    .then(response => {
        if (response.data.success) {
            alert('New tag added successfully!');
            document.getElementById('newTagName').value = ''; // Clear the input field
            loadTags(); // Reload tags
        } else {
            alert('Failed to add new tag: ' + response.data.message);
        }
    })
    .catch(error => {
        console.error('Failed to add new tag:', error);
    });
}


    document.addEventListener('DOMContentLoaded', loadTags);

</script>
</body>
</html>
