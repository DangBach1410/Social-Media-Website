<?php

function query($pdo, $sql, $parameters = []) {
	$query = $pdo->prepare($sql);
	$query->execute($parameters);
	return $query;
}

function allPosts($pdo) {
	$query = query($pdo, 'SELECT * FROM question ORDER BY time DESC');
    return $query;
}

function getPostsOfUser($pdo, $user_id) {
	$parameters = [':user_id' => $user_id];
	$query = query($pdo, 'SELECT * FROM question WHERE user_id = :user_id ORDER BY time DESC', $parameters);
	return $query;
}

function getPostByPostId($pdo, $post_id) {
    $parameters = [':post_id' => $post_id];
    $query = query($pdo, 'SELECT * FROM question WHERE id = :post_id', $parameters);
    return $query->fetch();
}

function getUserByUserId($pdo, $user_id) {
	$parameters = [':user_id' => $user_id];
    $query = query($pdo, 'SELECT * FROM user WHERE id = :user_id', $parameters);
    return $query->fetch();
}

function getUserByUserName($pdo, $username) {
	$parameters = [':username' => $username];
    $query = query($pdo, 'SELECT * FROM user WHERE username = :username', $parameters);
    return $query->fetch();
}

function getModuleByModuleId($pdo, $module_id) {
	$parameters = [':module_id' => $module_id];
    $query = query($pdo, 'SELECT * FROM module WHERE id = :module_id', $parameters);
    return $query->fetch();
}

function getImagesOfPost($pdo, $question_id){
	$query = 'SELECT * FROM question_image WHERE question_id = :question_id';
    $parameters = [':question_id' => $question_id];
    $query = query($pdo, $query, $parameters);
	return $query->fetchAll();
}

function insertPost($pdo, $user_id, $module_id, $content) {
	$query = 'INSERT INTO question SET 
            user_id = :user_id,
            module_id = :module_id,
            content = :content';
	$parameters = [':user_id' => $user_id, ':module_id' => $module_id, ':content' => $content];
	query($pdo, $query, $parameters);
}

function insertComment($pdo, $post_id, $user_id, $content) {
	$query = 'INSERT INTO comment SET 
	        question_id = :post_id,
            user_id = :user_id,
            content = :content';
	$parameters = [':post_id' => $post_id, ':user_id' => $user_id, ':content' => $content];
	query($pdo, $query, $parameters);
}

function updatePost($pdo, $module_id, $content, $question_id) {
	$query = 'UPDATE question SET module_id = :module_id, content = :content WHERE id = :id';
	$parameters = [':module_id' => $module_id, ':content' => $content, ':id' => $question_id];
	query($pdo, $query, $parameters);
}

function deletePost($pdo, $postId) {
	$parameters = [':id' => $postId];
	query($pdo, 'DELETE FROM question WHERE id = :id', $parameters);
}


function getCommentsByPostId($pdo, $post_id) {
    $parameters = [':post_id' => $post_id];
    $query = query($pdo, 'SELECT * FROM comment WHERE question_id = :post_id ORDER BY time DESC', $parameters);
    return $query->fetchAll();
}

function CountCommentsByPostId($pdo, $post_id) {
    $parameters = [':post_id' => $post_id];
    $query = query($pdo, 'SELECT COUNT(*) FROM comment WHERE question_id = :post_id', $parameters);
    return $query->fetchColumn();
}

function getCommentById($pdo, $commentId) {
    $stmt = $pdo->prepare('SELECT * FROM comment WHERE id = :id');
    $stmt->bindValue(':id', $commentId);
    $stmt->execute();
    return $stmt->fetch();
}

function getImagesOfComment($pdo, $commentId) {
    $parameters = [':comment_id' => $commentId];
    $query = query($pdo, 'SELECT * FROM comment_image WHERE comment_id = :comment_id', $parameters);
    return $query->fetchAll();
}

function updateComment($pdo, $content, $comment_id) {
    $parameters = [':content' => $content, ':comment_id' => $comment_id];
    $query = query($pdo, 'UPDATE comment SET content = :content WHERE id = :comment_id', $parameters);
    return $query;
}