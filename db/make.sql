/* MySQL */

drop database if exists project240415;
create database project240415;
use project240415;

CREATE TABLE users
(
    user_id    INT AUTO_INCREMENT PRIMARY KEY,
    username   VARCHAR(255)                        NOT NULL UNIQUE CHECK (NOT username LIKE '%,%'),
    password   VARCHAR(255)                        NOT NULL, -- Store encrypted password
    email      VARCHAR(255)                        NOT NULL,
    is_admin   BOOLEAN   DEFAULT FALSE             NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL
);

-- Create articles table
CREATE TABLE articles
(
    article_id INT AUTO_INCREMENT PRIMARY KEY,
    title      VARCHAR(255)                        NOT NULL,
    content    LONGTEXT                            NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create tags table
CREATE TABLE tags
(
    tag_id   INT AUTO_INCREMENT PRIMARY KEY,
    tag_name VARCHAR(100) NOT NULL UNIQUE CHECK (NOT tag_name LIKE '%,%')
);

-- Many-to-many relationship table between articles and tags
CREATE TABLE article_tags
(
    article_id INT,
    tag_id     INT,
    PRIMARY KEY (article_id, tag_id),
    FOREIGN KEY (article_id) REFERENCES articles (article_id) ON DELETE CASCADE,
    FOREIGN KEY (tag_id) REFERENCES tags (tag_id) ON DELETE CASCADE
);

-- Many-to-many relationship table between articles and authors
CREATE TABLE article_authors
(
    article_id INT,
    user_id    INT,
    PRIMARY KEY (article_id, user_id),
    FOREIGN KEY (article_id) REFERENCES articles (article_id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users (user_id) ON DELETE CASCADE
);

insert into articles (title, content)
values ('article1', 'a1234567890');
insert into article_authors (article_id, user_id)
values (1, 1);
insert into articles (title, content)
values ('article2', 'a12345678901');
insert into article_authors (article_id, user_id)
values (2, 1);
insert into articles (title, content)
values ('article3', 'a12345678902');
insert into article_authors (article_id, user_id)
values (3, 1);
insert into articles (title, content)
values ('article4', 'a12345678903');
insert into article_authors (article_id, user_id)
values (4, 2);
insert into articles (title, content)
values ('article5', 'a12345678904');
insert into article_authors (article_id, user_id)
values (5, 1);
insert into article_authors (article_id, user_id)
values (5, 2);
insert into articles (title, content)
values ('article6', 'a12345678905');
insert into articles (title, content)
values ('article7', 'a12345678906');
insert into articles (title, content)
values ('article8', 'a12345678907');
insert into articles (title, content)
values ('article9', 'a12345678908');
insert into articles (title, content)
values ('article10', 'a12345678909');
insert into articles (title, content)
values ('article11', 'a12345678910');
insert into articles (title, content)
values ('article12', 'a12345678911');
insert into tags (tag_name)
values ('tag1');
insert into tags (tag_name)
values ('tag2');
insert into tags (tag_name)
values ('tag3');
insert into tags (tag_name)
values ('tag4');
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


-- Create view:
-- Display article id and all tag names
-- If an article does not have tags, it will not appear in this table
DROP view if exists article_tags_view;
CREATE VIEW article_tags_view AS
SELECT at.article_id,
       GROUP_CONCAT(t.tag_name SEPARATOR ', ') AS tags
FROM article_tags at
         JOIN tags t ON at.tag_id = t.tag_id
GROUP BY at.article_id;

-- Create view:
-- Display article id and all author names
-- If an article does not have authors, it will not appear in this table
-- DROP view if exists article_authors_view;
CREATE VIEW article_authors_view AS
SELECT aa.article_id,
       GROUP_CONCAT(u.username SEPARATOR ', ') AS authors
FROM article_authors aa
         JOIN users u ON aa.user_id = u.user_id
GROUP BY aa.article_id;

-- Create view:
-- Display article details, including article title, all authors, creation time, latest update time, all tag names, and article content
-- If there are no authors or tags, use 'Unknown' and 'None' to fill
DROP view if exists article_detail_view;
CREATE VIEW article_detail_view AS
SELECT a.article_id,
       a.title,
       IFNULL(authors, 'Unknown') AS authors,
       a.created_at,
       a.updated_at,
       IFNULL(tags, 'None')      AS tags,
       a.content
FROM articles a
         LEFT JOIN article_authors_view aav ON a.article_id = aav.article_id
         LEFT JOIN article_tags_view atv ON a.article_id = atv.article_id;

select *
from article_detail_view
where article_id = 1;

-- Create view:
-- Display list of articles, including article title, all authors, creation time, latest update time, and all tag names
-- If there are no authors or tags, use 'Unknown' and 'None' to fill
-- Sort by the latest update time descending
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

-- Create stored procedure:
-- Add pagination functionality to article_list_view
DROP PROCEDURE IF EXISTS FetchArticlesByPage;
DELIMITER //

CREATE PROCEDURE FetchArticlesByPage(
    IN pageNum INT,
    IN pageSize INT
)
BEGIN
    DECLARE offset INT;
    SET offset = (pageNum - 1) * pageSize; -- Calculate offset

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

-- Create view:
-- Query all tags' ids, names, and the count of articles under each tag
-- Order by the count of articles in descending order
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

-- Create stored procedure:
-- Query article list based on a list of tag_ids
-- Articles associated with Ids included in tagIds
-- With pagination
DROP PROCEDURE IF EXISTS FetchArticlesByTagsWithPaging;

DELIMITER //

CREATE PROCEDURE FetchArticlesByTagsWithPaging(
    IN tagIds VARCHAR(255), -- Comma-separated list of tag_ids
    IN pageNum INT,
    IN pageSize INT,
    OUT totalArticles INT
)
BEGIN
    DECLARE offset INT;
    SET offset = (pageNum - 1) * pageSize;
    -- Helper table:
    -- Articles associated with Ids included in tagIds
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

-- Create stored procedure:
-- Query article list based on a list of tag_ids
-- Articles where associated Ids fully cover tagIds
DROP PROCEDURE IF EXISTS FetchArticlesByAllTagsWithPaging;

DELIMITER //

CREATE PROCEDURE FetchArticlesByAllTagsWithPaging(
    IN tagIds VARCHAR(255), -- Comma-separated list of tag_ids
    IN pageNum INT,
    IN pageSize INT,
    OUT totalArticles INT
)
BEGIN
    DECLARE offset INT;
    SET offset = (pageNum - 1) * pageSize;
    -- Helper table:
    -- Articles where associated Ids fully cover tagIds
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

-- Create stored procedure:
-- Search article list based on a search keyword
-- With pagination
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


CALL SearchArticlesWithPaging('a1', 1, 5, @totalPageCount);
SELECT @totalPageCount;

-- Create stored procedure:
-- Create new article
-- New title, new content, uploader's user_id
-- Output the new article's id
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

CALL CreateArticle('Article 14', 'New content', 2, @new_article_id);
SELECT @new_article_id;

-- Create stored procedure:
-- Update article
-- Based on article id, update article's title and content, new uploader's user_id
-- If the article does not exist, output error message
-- If the uploader is not already an author, add as a new author, output message
-- If the uploader is already an author, output message

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
    -- Check if the article exists
    IF NOT EXISTS (SELECT 1
                   FROM articles
                   WHERE article_id = article) THEN
        SET message = 'Article does not exist';
        LEAVE proc; -- specify the label to leave
    END IF;

    -- Update article title and content
    UPDATE articles
    SET title   = new_title,
        content = new_content
    WHERE article_id = article;

    -- Check if the uploader is already an article's author
    IF NOT EXISTS (SELECT 1
                   FROM article_authors
                   WHERE article_id = article
                     AND user_id = author_id) THEN
        -- If not, add as a new author
        INSERT INTO article_authors (article_id, user_id)
        VALUES (article, author_id);
        SET message = 'Success updated with new author';
    ELSE
        SET message = 'Success updated';
    END IF;
END proc //

DELIMITER ;

-- Test call to stored procedure
CALL UpdateArticle(13, 'Updated title', 'Updated content', 4, @message);
SELECT @message;

-- Create stored procedure:
-- Delete article
-- Return message, if article does not exist, cannot delete, otherwise success
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

-- Create stored procedure:
-- Update tags for an article
-- Input article id, list of tag ids to add, list of tag ids to remove
DROP PROCEDURE IF EXISTS UpdateArticleTags;
DELIMITER //

CREATE PROCEDURE UpdateArticleTags(
    IN curr_article_id INT,
    IN add_tag_ids VARCHAR(255),
    IN remove_tag_ids VARCHAR(255)
)
BEGIN
    -- Delete tags
    IF remove_tag_ids IS NOT NULL THEN
        DELETE FROM article_tags
        WHERE article_id = curr_article_id
          AND tag_id IN (SELECT tag_id
                         FROM tags
                         WHERE FIND_IN_SET(tag_id, remove_tag_ids));
    END IF;

    -- Add tags
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



