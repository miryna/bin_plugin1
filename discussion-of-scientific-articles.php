<?php
/*
Plugin Name: Discussion of scientific articles
Description: Add a new post-type, text widget. Allows scientists to discuss scientific articles.

Plugin URI: #
Author: mIryna
Author URI: http://www.timeintent.com
Version: 0.8
*/

//Создание нового custom_post_type 
//-------------------------------------------------

function custom_post_discussion_of_sca() {
	
    $labels = array(
        'name'               => _x( 'Научные статьи для дискуссии', 'discussion-of-sca' ),
        'singular_name'      => _x( 'Научная статья для дискуссии', 'discussion-of-sca' ),
        'add_new'            => _x( 'Добавить новую статью для дискуссии', 'discussion-of-sa' ),
        'add_new_item'       => __( 'Добавить информацию о новой статье для дискуссии' ),
        'edit_item'          => __( 'Редактировать информацию о статье для дискуссии' ),
        'new_item'           => __( 'Новая научная статья для дискуссии' ),
        'all_items'          => __( 'Все научные статьи для дискуссии' ),
        'view_item'          => __( 'Просмотр научных статей для дискуссии' ),
        'search_items'       => __( 'Поиск научных статей для дискуссии' ),
        'not_found'          => __( 'Материалы о научной статье для дискуссии не найдены' ),
        'not_found_in_trash' => __( 'Материалы о научной статье для дискуссии не найдены в Корзине' ),
        'parent_item_colon'  => '',
        'menu_name'          => 'Дискуссии'
    );
    $args = array(
        'labels'        => $labels,
        'description'   => 'Allow allows scientists to discuss scientific articles',
        'public'        => true,
        'menu_position' => 5,
        'supports'      => array( 'title_sca', 'author_sca', 'link_sca', 'thumbnail', 'excerpt', 'excerpt_sca', 'comments' ),
        'has_archive'   => true,
    );
    register_post_type( 'discussion-of-sca', $args );    
}
add_action( 'init', 'custom_post_discussion_of_sca' );

// Пользовательские информационные сообщения

 function discussion_updated_messages( $messages ) {
	 
    global $post, $post_ID;
    $messages['discussion'] = array(
        0 => '',
        1 => sprintf( __('Discussion updated. <a href="%s">View Discussion</a>'), esc_url( get_permalink($post_ID) ) ),
        2 => __('Discussion updated.'),
        3 => __('Discussion deleted.'),
        4 => __('Discussion updated.'),
        5 => isset($_GET['revision']) ? sprintf( __('Discussion restored to revision from %s'), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
        6 => sprintf( __('Discussion published. <a href="%s">View Discussion</a>'), esc_url( get_permalink($post_ID) ) ),
        7 => __('Discussion saved.'),
        8 => sprintf( __('Discussion submitted. <a target="_blank" href="%s">Preview Discussion</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
        9 => sprintf( __('Discussion scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview Discussion</a>'), date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( get_permalink($post_ID) ) ),
        10 => sprintf( __('Discussion draft updated. <a target="_blank" href="%s">Preview Discussion</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
    );
    return $messages;
}
add_filter( 'post_updated_messages', 'discussion_updated_messages' );







