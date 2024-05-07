/* MySQL */

drop database if exists project240415;
create database project240415;
use project240415;

CREATE TABLE users
(
    user_id    INT AUTO_INCREMENT PRIMARY KEY,
    username   VARCHAR(255)                        NOT NULL UNIQUE CHECK (NOT username LIKE '%,%'),
    password   VARCHAR(255)                        NOT NULL, -- 存储加密后的密码
    email      VARCHAR(255)                        NOT NULL,
    is_admin   BOOLEAN   DEFAULT FALSE             NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL
);

-- 创建文章表
CREATE TABLE articles
(
    article_id INT AUTO_INCREMENT PRIMARY KEY,
    title      VARCHAR(255)                        NOT NULL,
    content    LONGTEXT                            NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- 创建标签表
CREATE TABLE tags
(
    tag_id   INT AUTO_INCREMENT PRIMARY KEY,
    tag_name VARCHAR(100) NOT NULL UNIQUE CHECK (NOT tag_name LIKE '%,%')
);

-- 文章和标签的多对多关系表
CREATE TABLE article_tags
(
    article_id INT,
    tag_id     INT,
    PRIMARY KEY (article_id, tag_id),
    FOREIGN KEY (article_id) REFERENCES articles (article_id) ON DELETE CASCADE,
    FOREIGN KEY (tag_id) REFERENCES tags (tag_id) ON DELETE CASCADE
);

-- 文章和作者的多对多关系表
CREATE TABLE article_authors
(
    article_id INT,
    user_id    INT,
    PRIMARY KEY (article_id, user_id),
    FOREIGN KEY (article_id) REFERENCES articles (article_id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users (user_id) ON DELETE CASCADE
);

insert into articles (title, content)
values ('文章1', '内容1234567890');
insert into article_authors (article_id, user_id)
values (1, 1);
insert into articles (title, content)
values ('文章2', '内容12345678901');
insert into article_authors (article_id, user_id)
values (2, 1);
insert into articles (title, content)
values ('文章3', '内容12345678902');
insert into article_authors (article_id, user_id)
values (3, 1);
insert into articles (title, content)
values ('文章4', '内容12345678903');
insert into article_authors (article_id, user_id)
values (4, 2);
insert into articles (title, content)
values ('文章5', '内容12345678904');
insert into article_authors (article_id, user_id)
values (5, 1);
insert into article_authors (article_id, user_id)
values (5, 2);
insert into articles (title, content)
values ('文章6', '内容12345678905');
insert into articles (title, content)
values ('文章7', '内容12345678906');
insert into articles (title, content)
values ('文章8', '内容12345678907');
insert into articles (title, content)
values ('文章9', '内容12345678908');
insert into articles (title, content)
values ('文章10', '内容12345678909');
insert into articles (title, content)
values ('文章11', '内容12345678910');
insert into articles (title, content)
values ('文章12', '内容12345678911');
insert into tags (tag_name)
values ('标签1');
insert into tags (tag_name)
values ('标签2');
insert into tags (tag_name)
values ('标签3');
insert into tags (tag_name)
values ('标签4');
insert into article_tags (article_id, tag_id)
values (1, 1);
insert into article_tags (article_id, tag_id)
values (1, 2);
insert into article_tags (article_id, tag_id)
values (2, 1);
insert into article_tags (article_id, tag_id)
values (2, 3);
insert into article_tags (article_id, tag_id)
values (3, 2);

insert into article_tags (article_id, tag_id)
values (6, 4);
insert into article_tags (article_id, tag_id)
values (7, 4);
insert into article_tags (article_id, tag_id)
values (8, 4);
insert into article_tags (article_id, tag_id)
values (9, 4);


-- 创建视图：
-- 展示文章id、所有标签名字
-- 可能文章不存在标签，则不在表中
DROP view if exists article_tags_view;
CREATE VIEW article_tags_view AS
SELECT at.article_id,
       GROUP_CONCAT(t.tag_name SEPARATOR ', ') AS tags
FROM article_tags at
         JOIN tags t ON at.tag_id = t.tag_id
GROUP BY at.article_id;

-- 创建视图：
-- 展示文章id、所有作者名字
-- 可能文章不存在作者，则不在表中
-- DROP view if exists article_authors_view;
CREATE VIEW article_authors_view AS
SELECT aa.article_id,
       GROUP_CONCAT(u.username SEPARATOR ', ') AS authors
FROM article_authors aa
         JOIN users u ON aa.user_id = u.user_id
GROUP BY aa.article_id;


-- 创建视图：
-- 展示文章细节，包含文章标题、所有作者、创建时间、最晚更新时间、所有标签名字、文章内容
-- 作者与标签可能不存在，此时使用‘未知’和‘无’填充
DROP view if exists article_detail_view;
CREATE VIEW article_detail_view AS
SELECT a.article_id,
       a.title,
       IFNULL(authors, 'Unknown') AS authors,
       a.created_at,
       a.updated_at,
       IFNULL(tags, 'null')      AS tags,
       a.content
FROM articles a
         LEFT JOIN article_authors_view aav ON a.article_id = aav.article_id
         LEFT JOIN article_tags_view atv ON a.article_id = atv.article_id;

select *
from article_detail_view
where article_id = 1;

-- 创建视图：
-- 展示文章列表，包含文章标题、所有作者、创建时间、最晚更新时间、所有标签名字
-- 作者与标签可能不存在，此时使用‘未知’和‘无’填充
-- 按照最晚更新时间倒序排序
DROP view if exists article_list_view;
CREATE VIEW article_list_view AS
SELECT a.article_id,
       a.title,
       IFNULL(authors, 'Unknown') AS authors,
       a.created_at,
       a.updated_at,
       IFNULL(tags, 'null')      AS tags
FROM articles a
         LEFT JOIN article_authors_view aav ON a.article_id = aav.article_id
         LEFT JOIN article_tags_view atv ON a.article_id = atv.article_id
ORDER BY a.updated_at DESC;

-- 创建存储过程：
-- 为article_list_view增加分页功能
DROP PROCEDURE IF EXISTS FetchArticlesByPage;
DELIMITER //

CREATE PROCEDURE FetchArticlesByPage(
    IN pageNum INT,
    IN pageSize INT
)
BEGIN
    DECLARE offset INT;
    SET offset = (pageNum - 1) * pageSize; -- 计算偏移量

    SELECT article_id,
           title,
           authors,
           created_at,
           updated_at,
           tags
    FROM article_list_view
    ORDER BY updated_at DESC
    LIMIT pageSize OFFSET offset;
END //

DELIMITER ;

CALL FetchArticlesByPage(1, 5);



-- 创建视图：
-- 查询所有tags的id、名称，以及每个tag下的文章数量
-- 按照文章数量倒序排序
DROP view if exists tag_article_count_view;
CREATE VIEW tag_article_count_view AS
SELECT t.tag_id,
       t.tag_name,
       COUNT(at.article_id) AS article_count
FROM tags t
         LEFT JOIN article_tags at ON t.tag_id = at.tag_id
GROUP BY t.tag_id, t.tag_name
ORDER BY article_count DESC;

select *
from tag_article_count_view;

-- 创建存储过程：
-- 根据tag_id的list查询文章列表
-- 作品关联的Ids包含在tagIds中的作品
-- 进行分页
DROP PROCEDURE IF EXISTS FetchArticlesByTagsWithPaging;

DELIMITER //

CREATE PROCEDURE FetchArticlesByTagsWithPaging(
    IN tagIds VARCHAR(255), -- 以逗号分隔的tag_id列表
    IN pageNum INT,
    IN pageSize INT,
    OUT totalArticles INT
)
BEGIN
    DECLARE offset INT;
    SET offset = (pageNum - 1) * pageSize;
    -- 辅助表：
    -- 作品关联的Ids包含在tagIds中的作品
    WITH FilteredArticles AS (SELECT at.article_id
                              FROM article_tags at
                              WHERE FIND_IN_SET(at.tag_id, tagIds)
                              GROUP BY at.article_id)
    SELECT SQL_CALC_FOUND_ROWS alv.article_id,
                               alv.title,
                               alv.authors,
                               alv.created_at,
                               alv.updated_at,
                               alv.tags
    FROM article_list_view alv
             JOIN FilteredArticles fa ON alv.article_id = fa.article_id
    ORDER BY alv.updated_at DESC
    LIMIT pageSize OFFSET offset;

    SELECT FOUND_ROWS() INTO totalArticles;
END //

DELIMITER ;

CALL FetchArticlesByTagsWithPaging('4,1,2', 1, 5, @totalArticles);
SELECT @totalArticles;

-- 创建存储过程：
-- 根据tag_id的list查询文章列表
-- 作品关联的Ids完全涵盖了tagIds
DROP PROCEDURE IF EXISTS FetchArticlesByAllTagsWithPaging;

DELIMITER //

CREATE PROCEDURE FetchArticlesByAllTagsWithPaging(
    IN tagIds VARCHAR(255), -- 以逗号分隔的tag_id列表
    IN pageNum INT,
    IN pageSize INT,
    OUT totalArticles INT
)
BEGIN
    DECLARE offset INT;
    SET offset = (pageNum - 1) * pageSize;
    -- 辅助表：
    -- 作品关联的Ids完全涵盖了tagIds
    WITH FilteredArticles AS (SELECT at.article_id
                              FROM article_tags at
                              WHERE FIND_IN_SET(at.tag_id, tagIds)
                              GROUP BY at.article_id
                              HAVING COUNT(at.tag_id) = LENGTH(tagIds) - LENGTH(REPLACE(tagIds, ',', '')) + 1)
    SELECT SQL_CALC_FOUND_ROWS alv.article_id,
                               alv.title,
                               alv.authors,
                               alv.created_at,
                               alv.updated_at,
                               alv.tags
    FROM article_list_view alv
             JOIN FilteredArticles fa ON alv.article_id = fa.article_id
    ORDER BY alv.updated_at DESC
    LIMIT pageSize OFFSET offset;

    SELECT FOUND_ROWS() INTO totalArticles;
END //

DELIMITER ;

CALL FetchArticlesByAllTagsWithPaging('2', 1, 10, @totalArticles);
SELECT @totalArticles;


create view article_count_view as
select count(*) as article_count
from articles;

SELECT article_count
FROM article_count_view;

-- 创建存储过程：
-- 根据搜索关键字查询文章列表
-- 进行分页
DROP PROCEDURE IF EXISTS SearchArticlesWithPaging;
DELIMITER //

CREATE PROCEDURE SearchArticlesWithPaging(
    IN searchQuery VARCHAR(255),
    IN pageNum INT,
    IN pageSize INT,
    OUT totalArticles INT
)
BEGIN
    DECLARE offset INT;
    SET offset = (pageNum - 1) * pageSize;

    SELECT SQL_CALC_FOUND_ROWS alv.article_id,
                               alv.title,
                               alv.authors,
                               alv.created_at,
                               alv.updated_at,
                               alv.tags
    FROM article_list_view alv
    WHERE alv.title LIKE CONCAT('%', searchQuery, '%')
    ORDER BY alv.updated_at DESC
    LIMIT pageSize OFFSET offset;

    SELECT FOUND_ROWS() INTO totalArticles;
END //

DELIMITER ;


CALL SearchArticlesWithPaging('章', 1, 5, @totalPageCount);
SELECT @totalPageCount;

-- 存储过程：
-- 新建文章
-- 新的标题，新的内容，上传者user_id
-- 输出新文章的id
DROP PROCEDURE IF EXISTS CreateArticle;
DELIMITER //

CREATE PROCEDURE CreateArticle(
    IN new_title VARCHAR(255),
    IN new_content LONGTEXT,
    IN author_id INT,
    OUT new_article_id INT
)
BEGIN
    INSERT INTO articles (title, content)
    VALUES (new_title, new_content);
    SET @article_id = LAST_INSERT_ID();
    INSERT INTO article_authors (article_id, user_id)
    VALUES (@article_id, author_id);
    SET new_article_id = @article_id;
END //

DELIMITER ;

CALL CreateArticle('文章14', '新内容', 2, @new_article_id);
SELECT @new_article_id;

-- 存储过程：
-- 更新文章
-- 根据文章id，更新文章的标题和内容，新的上传者user_id
-- 如果文章不存在，output 错误信息
-- 如果上传者不是文章的作者，添加为新的作者，output 提示信息
-- 如果上传者是文章的作者，output 提示信息

DROP PROCEDURE IF EXISTS UpdateArticle;
DELIMITER //

CREATE PROCEDURE UpdateArticle(
    IN article INT,
    IN new_title VARCHAR(255),
    IN new_content LONGTEXT,
    IN author_id INT,
    OUT message VARCHAR(255)
)
proc:
BEGIN
    -- 检查文章是否存在
    IF NOT EXISTS (SELECT 1
                   FROM articles
                   WHERE article_id = article) THEN
        SET message = 'Article does not exist';
        LEAVE proc; -- 指定离开的标签
    END IF;

    -- 更新文章标题和内容
    UPDATE articles
    SET title   = new_title,
        content = new_content
    WHERE article_id = article;

    -- 检查上传者是否已经是文章的作者
    IF NOT EXISTS (SELECT 1
                   FROM article_authors
                   WHERE article_id = article
                     AND user_id = author_id) THEN
        -- 如果不是，添加为新的作者
        INSERT INTO article_authors (article_id, user_id)
        VALUES (article, author_id);
        SET message = 'Success updated with new author';
    ELSE
        SET message = 'Success updated';
    END IF;
END proc //

DELIMITER ;

-- 测试调用存储过程
CALL UpdateArticle(13, '更新后的标题', '更新后的内容', 4, @message);
SELECT @message;

-- 存储过程：
-- 删除文章
-- 返回message，文章不存在，无法删除，否则删除成功
DROP PROCEDURE IF EXISTS DeleteArticle;
DELIMITER //

CREATE PROCEDURE DeleteArticle(
    IN article_to_delete INT,
    OUT message VARCHAR(255)
)
BEGIN
    DELETE FROM articles WHERE article_id = article_to_delete;

    IF ROW_COUNT() = 0 THEN
        SET message = 'Error article does not exist';
    ELSE
        SET message = 'Success deleted';
    END IF;
END //

DELIMITER ;


CALL DeleteArticle(2, @message);
SELECT @message;


-- 存储过程：
-- 为文章更新标签
-- 输入文章id，增加的标签id列表，删除的标签id列表
DROP PROCEDURE IF EXISTS UpdateArticleTags;
DELIMITER //

CREATE PROCEDURE UpdateArticleTags(
    IN curr_article_id INT,
    IN add_tag_ids VARCHAR(255),
    IN remove_tag_ids VARCHAR(255)
)
BEGIN
    -- 删除标签
    IF remove_tag_ids IS NOT NULL THEN
        DELETE FROM article_tags
        WHERE article_id = curr_article_id
          AND tag_id IN (SELECT tag_id
                         FROM tags
                         WHERE FIND_IN_SET(tag_id, remove_tag_ids));
    END IF;

    -- 添加标签
    IF add_tag_ids IS NOT NULL THEN
        INSERT INTO article_tags (article_id, tag_id)
        SELECT curr_article_id, tag_id
        FROM tags
        WHERE FIND_IN_SET(tag_id, add_tag_ids);
    END IF;
END //

DELIMITER ;


select * from article_tags where article_id = 1;
CALL UpdateArticleTags(1, '3,4', '1');



