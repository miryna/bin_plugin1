<?php
/*
Plugin Name: Discussion of scientific articles
Description: Add a new post-type. Allows scientists to discuss scientific articles.

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
 * Adds  the metabox 'Discussion author'
 *-------------------------------------------------
 */
function discussion_author_box() {
    add_meta_box( 'discussion_author',
        __('Author'),
        'discussion_author_callback',
        'discussion',
        'advanced',
        'default' );
}
add_action('add_meta_boxes', 'discussion_author_box');

/**
 *  Describes the metabox 'Discussion author'
 */

function discussion_author_callback( $post ) {
    wp_nonce_field( basename(__FILE__), 'discussion_author_nonce');
    $discussion_author_stored = get_post_meta( $post->ID );
    ?>
    <p>
        <label for="d_author" class="discussion_author">
            <?php _e( '<i>Author:</i>', 'textdomain' )?>
        </label><br>
        <input type="text" size="67" name="d_author" id="d_author" value="<?php if ( isset ( $discussion_author_stored['d_author'] ) ) echo $discussion_author_stored['d_author'][0]; ?>" />
    </p>
<?php
}

/**
 * Saves the metabox (custom field 'd_author')
 */
function discussion_author_save( $post_id ) {

    // Checks save status
    $is_autosave = wp_is_post_autosave( $post_id );
    $is_revision = wp_is_post_revision( $post_id );
    $is_valid_nonce = ( isset( $_POST[ 'discussion_author_nonce' ] ) && wp_verify_nonce( $_POST[ 'discussion_author_nonce' ], basename( __FILE__ ) ) ) ? 'true' : 'false';

    // Exits script depending on save status
    if ( $is_autosave || $is_revision || !$is_valid_nonce ) {
        return;
    }

    // Checks for input and sanitizes/saves if needed
    if( isset( $_POST[ 'd_author' ] ) ) {
        update_post_meta( $post_id, 'd_author', sanitize_text_field( $_POST[ 'd_author' ] ) );
    }
}
add_action( 'save_post', 'discussion_author_save' );



/**
 * Adds the metabox 'discussion_titles' (ra - research article)
 *-------------------------------------------------
 */
function discussion_titles_box() {
    add_meta_box( 'discussion_titles',
        __('Research article titles'),
        'discussion_titles_callback',
        'discussion',
        'advanced',
        'default' );
}
add_action('add_meta_boxes', 'discussion_titles_box');

/**
 *  Describes the metabox 'discussion_titles'
 */
function discussion_titles_callback( $post ) {
    wp_nonce_field( basename(__FILE__), 'discussion_titles_nonce');
    $discussion_titles_stored = get_post_meta( $post->ID );
    ?>
    <p>
        <label for="ra_title" class="discussion_titles">
            <?php _e( '<i>Title and Author:</i>', 'textdomain' )?>
        </label><br>
        <textarea rows="2" cols="65" name="ra_title" id="ra_title"> <?php if ( isset ( $discussion_titles_stored['ra_title'] ) ) echo $discussion_titles_stored['ra_title'][0]; ?></textarea>
    </p>
    <p>
        <label for="ra_pubblished" class="discussion_titles">
            <?php _e( '<i>Pubblished:</i>', 'textdomain' )?>
        </label><br>
        <textarea rows="2" cols="65" name="ra_pubblished" id="ra_pubblished"> <?php if ( isset ( $discussion_titles_stored['ra_pubblished'] ) ) echo $discussion_titles_stored['ra_pubblished'][0]; ?></textarea>
    </p>
    <p>
        <label for="ra_tag" class="discussion_titles">
            <?php _e( '<i>Specialization:</i>', 'textdomain' )?>
        </label><br>
        <input type="text" size="67" name="ra_tag" id="ra_tag" value="<?php if ( isset ( $discussion_titles_stored['ra_tag'] ) ) echo $discussion_titles_stored['ra_tag'][0]; ?>" />
    </p>
    <p>
        <label for="ra_link" class="discussion_titles">
            <?php _e( '<i>Link:</i>', 'textdomain' )?>
        </label><br>
        <textarea rows="2" cols="65" name="ra_link" id="ra_link"> <?php if ( isset ( $discussion_titles_stored['ra_link'] ) ) echo $discussion_titles_stored['ra_link'][0]; ?></textarea>
    </p>
    <p>
        <label for="ra_generic_links" class="discussion_titles">
            <?php _e( '<i>Links to generic research articles:</i>', 'textdomain' )?>
        </label><br>
        <textarea rows="5" cols="65" name="ra_generic_links" id="ra_generic_links"> <?php if ( isset ( $discussion_titles_stored['ra_generic_links'] ) ) echo $discussion_titles_stored['ra_generic_links'][0]; ?></textarea>
    </p>
<?php
}

/**
 * Saves the metabox
 * (custom fields 'ra_title', 'ra_pubblished', 'ra_tag', 'ra_link', 'ra_generic_links')
 */
function discussion_titles_save( $post_id ) {

    // Checks save status
    $is_autosave = wp_is_post_autosave( $post_id );
    $is_revision = wp_is_post_revision( $post_id );
    $is_valid_nonce = ( isset( $_POST[ 'discussion_titles_nonce' ] ) && wp_verify_nonce( $_POST[ 'discussion_titles_nonce' ], basename( __FILE__ ) ) ) ? 'true' : 'false';

    // Exits script depending on save status
    if ( $is_autosave || $is_revision || !$is_valid_nonce ) {
        return;
    }

    // Checks for input and sanitizes/saves if needed
    if( isset( $_POST[ 'ra_title' ] ) ) {
        update_post_meta( $post_id, 'ra_title', sanitize_text_field( $_POST[ 'ra_title' ] ) );
    }

    if( isset( $_POST[ 'ra_pubblished' ] ) ) {
        update_post_meta( $post_id, 'ra_pubblished', sanitize_text_field( $_POST[ 'ra_pubblished' ] ) );
    }

    if( isset( $_POST[ 'ra_tag' ] ) ) {
        update_post_meta( $post_id, 'ra_tag', sanitize_text_field( $_POST[ 'ra_tag' ] ) );
    }

    if( isset( $_POST[ 'ra_link' ] ) ) {
        update_post_meta( $post_id, 'ra_link', sanitize_text_field( $_POST[ 'ra_link' ] ) );
    }

    if( isset( $_POST[ 'ra_generic_links' ] ) ) {
        update_post_meta( $post_id, 'ra_generic_links', sanitize_text_field( $_POST[ 'ra_generic_links' ] ) );
    }

}
add_action( 'save_post', 'discussion_titles_save' );



/**
 * Adds  the metabox 'discussion text'
 *-------------------------------------------------
 */
function discussion_text_box() {
    add_meta_box( 'discussion_text',
        __('Research article text'),
        'discussion_text_callback',
        'discussion',
        'advanced',
        'default' );
}
add_action('add_meta_boxes', 'discussion_text_box');

/**
 *  Describes the metabox 'Discussion text'
 */

function discussion_text_callback( $post ) {
    wp_nonce_field( basename(__FILE__), 'discussion_text_nonce');
    $discussion_text_stored = get_post_meta( $post->ID );
    ?>
    <p>
        <label for="ra_text" class="discussion_text">
            <?php _e( '<i>Text of the Research article:</i>', 'textdomain' )?>
        </label><br>
        <textarea rows="20" cols="65" name="ra_text" id="ra_text"><?php if ( isset ( $discussion_text_stored['ra_text'] ) ) echo $discussion_text_stored['ra_text'][0]; ?></textarea>
    </p>
<?php
}

/**
 * Saves the metabox (custom field 'ra_text')
 */
function discussion_text_save( $post_id ) {

    // Checks save status
    $is_autosave = wp_is_post_autosave( $post_id );
    $is_revision = wp_is_post_revision( $post_id );
    $is_valid_nonce = ( isset( $_POST[ 'discussion_text_nonce' ] ) && wp_verify_nonce( $_POST[ 'discussion_text_nonce' ], basename( __FILE__ ) ) ) ? 'true' : 'false';

    // Exits script depending on save status
    if ( $is_autosave || $is_revision || !$is_valid_nonce ) {
        return;
    }

    // Checks for input and sanitizes/saves if needed
    if( isset( $_POST[ 'ra_text' ] ) ) {
        update_post_meta( $post_id, 'ra_text', sanitize_text_field( $_POST[ 'ra_text' ] ) );
    }
}
add_action( 'save_post', 'discussion_text_save' );





