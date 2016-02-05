<?php
/*
Plugin Name: Discussion of scientific articles
Description: Add a new post-type, text widget. Allows scientists to discuss scientific articles.

Plugin URI: #
Author: mIryna
Author URI: http://www.timeintent.com
Version: 0.8
*/

// CUSTOM_POST_TYPE 'discussion'
//=======================================================
// register_post_type(), hooks: 'init', 'post_updated_messages', 'contextual_help',

/**
 * Adds the custom_post_type 'discussion'
 *-------------------------------------------------
 */
function create_custom_post_type_discussion() {
	
    $labels = array(
        'name'               => _x( 'Статьи для дискуссии', 'discussion' ),
        'singular_name'      => _x( 'Статья для дискуссии', 'discussion' ),
        'add_new'            => _x( 'Добавить новую статью для дискуссии', 'discussion' ),
        'add_new_item'       => __( 'Добавить новую статью для дискуссии' ),
        'edit_item'          => __( 'Редактировать статью для дискуссии' ),
        'new_item'           => __( 'Новая статья для дискуссии' ),
        'all_items'          => __( 'Все статьи для дискуссии' ),
        'view_item'          => __( 'Просмотр статей для дискуссии' ),
        'search_items'       => __( 'Поиск статей для дискуссии' ),
        'not_found'          => __( 'Материалы  для дискуссии не найдены' ),
        'not_found_in_trash' => __( 'Материалы  для дискуссии не найдены в Корзине' ),
        'parent_item_colon'  => '',
        'menu_name'          => 'Дискуссии'
    );
    $args = array(
        'labels'        => $labels,
        'description'   => 'Allows scientists to discuss scientific articles',
        'public'        => true,
        'publicly_queryable' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'query_var' => true,
        'capability_type' => 'post',
        'rewrite'       => array( 'slug' => 'discussion' ),
        'menu_position' => 5,
        'supports'      => array( 'title', 'editor', 'author', 'custom-fields', 'comments', 'revisions', 'thumbnail', 'excerpt', ),
        'has_archive'   => true,
    );
    register_post_type( 'discussion', $args );
}
add_action( 'init', 'create_custom_post_type_discussion' );

/**
 * shows the updated messages for custom_post_type discussion
 *-------------------------------------------------
 */
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

/**
 * shows the help section for custom_post_type discussion
 *-------------------------------------------------
 */
//$contextual_help .= var_dump($screen); // чтобы помочь определить параметр $screen->id

function discussion_contextual_help( $contextual_help, $screen_id, $screen ) {
    if ( 'discussion' == $screen->id ) {
        $contextual_help =
            '<p>Напоминалка при редактировании записи:</p>
	  <ul>
	  <li>Указать специализацию, например биоэнергетика или генетика.</li>
	  <li>Указать ссылку на полный текст статьи</li>
	  <li>Если есть другие связанные  материалы, укажите ссылку на них</li>
	  </ul>
	  <p>Если нужно запланировать публикацию на будущее:</p>
	  <ul>
	  <li>В блоке с кнопкой "опубликовать" нажмите редактировать дату.</li>
	  <li>Измените дату на нужную, будущую и подтвердите изменения кнопкой ниже "ОК".</li>
	  </ul>
	  <p><strong>За дополнительной информацией обращайтесь</strong></p>
	  <p>к администратору сайта</p>';
    } elseif ( 'edit-discussion' == $screen->id ) {
        $contextual_help =
        '<p>Это раздел помощи показанный для типа записи "Обсуждение научных материалов"</p>' ;
    }
    return $contextual_help;
}
add_action( 'contextual_help', 'discussion_contextual_help', 10, 3 );


// METABOXES
//=======================================================

/**
 * Adds  the metabox 'Author and Title of the article'
 *-------------------------------------------------
 */
function author_and_title_custom_meta() {
    add_meta_box( 'author_and_title_meta',
        __('The discussed article'),
        'author_and_title_meta_callback',
        'discussion',
        'advanced',
        'default' );
}
add_action('add_meta_boxes', 'author_and_title_custom_meta');

/**
 *  Describes the metabox 'Author and Title of the article'
 */

function author_and_title_meta_callback( $post ) {
    wp_nonce_field( basename(__FILE__), 'author_and_title_nonce');
    $author_and_title_stored_meta = get_post_meta( $post->ID );
    ?>
    <p>
        <label for="author_and_title-article-original" class="author_and_title-row-title">
            <?php _e( '<i>Author and Title of the article:</i>', 'links-textdomain' )?>
        </label><br>
        <textarea rows="5" cols="70" name="author_and_title-article-original" id="author_and_title-article-original"><?php if ( isset ( $author_and_title_stored_meta['author_and_title-article-original'] ) ) echo $author_and_title_stored_meta['author_and_title-article-original'][0]; ?></textarea>
    </p>
<?php
}

/**
 * Saves the custom meta input 'Author and Title of the article'
 */
function author_and_title_meta_save( $post_id ) {

    // Checks save status
    $is_autosave = wp_is_post_autosave( $post_id );
    $is_revision = wp_is_post_revision( $post_id );
    $is_valid_nonce = ( isset( $_POST[ 'author_and_title_nonce' ] ) && wp_verify_nonce( $_POST[ 'author_and_title_nonce' ], basename( __FILE__ ) ) ) ? 'true' : 'false';

    // Exits script depending on save status
    if ( $is_autosave || $is_revision || !$is_valid_nonce ) {
        return;
    }

    // Checks for input and sanitizes/saves if needed
    if( isset( $_POST[ 'author_and_title-article-original' ] ) ) {
        update_post_meta( $post_id, 'author_and_title-article-original', sanitize_text_field( $_POST[ 'author_and_title-article-original' ] ) );
    }
}
add_action( 'save_post', 'author_and_title_meta_save' );


/**
 * Adds the metabox 'Link to article' (Ссылка на статью)
 *-------------------------------------------------
 */
function links_custom_meta() {
    add_meta_box( 'links_meta',
        __('Link to article'),
        'links_meta_callback',
        'discussion',
        'advanced',
        'default' );
}
add_action('add_meta_boxes', 'links_custom_meta');

/**
 *  Describes metabox 'links_meta'
 */
function links_meta_callback( $post ) {
    wp_nonce_field( basename(__FILE__), 'links_nonce');
    $links_stored_meta = get_post_meta( $post->ID );
    ?>
    <p>
        <label for="links-article-original" class="links-row-title">
            <?php _e( '<i>Site:</i>', 'links-textdomain' )?>
        </label><br>
        <textarea rows="2" cols="70" name="links-article-original" id="links-article-original"> <?php if ( isset ( $links_stored_meta['links-article-original'] ) ) echo $links_stored_meta['links-article-original'][0]; ?></textarea>
    </p>
<?php
}

/**
 * Saves the custom meta textarea 'links_meta'
 */
function links_meta_save( $post_id ) {

    // Checks save status
    $is_autosave = wp_is_post_autosave( $post_id );
    $is_revision = wp_is_post_revision( $post_id );
    $is_valid_nonce = ( isset( $_POST[ 'links_nonce' ] ) && wp_verify_nonce( $_POST[ 'links_nonce' ], basename( __FILE__ ) ) ) ? 'true' : 'false';

    // Exits script depending on save status
    if ( $is_autosave || $is_revision || !$is_valid_nonce ) {
        return;
    }

    // Checks for input and sanitizes/saves if needed
    if( isset( $_POST[ 'links-article-original' ] ) ) {
        update_post_meta( $post_id, 'links-article-original', sanitize_text_field( $_POST[ 'links-article-original' ] ) );
    }
}
add_action( 'save_post', 'links_meta_save' );







