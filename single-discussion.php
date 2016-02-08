<?php
/*
Template Name: single discussion
*/

get_header(); ?>

<div id="primary">
    <div id="content"  role="main">
        <?php	// Функция query_posts получает элементы пользовательского типа записи и отображает их, используя цикл.
        query_posts(array('post_type'=>'discussion')); ?>

        <?php $args = array( 'post_type' => 'discussion' );

        $loop = new WP_Query( $args ); ?>

        <!-- Cycle through all posts -->
        <?php while ( $loop->have_posts() ) : $loop->the_post(); ?>

            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                <header class="entry-header">

                    <!-- Display Title and Author Name -->
                    <h1><?php the_title(); ?></h1>

                    <div class="postinfo">
                        <span class="post-data"><?php the_time('j F Y') ?></span>
                        <span class="post-com"><?php comments_popup_link('Нет комментариев', '1 комментарий.', '% коммент.'); ?></span>
                    </div>

                    <!-- Display featured image in right-aligned floating div -->
                    <div style="float:right; margin: 10px">
                        <?php the_post_thumbnail( array(100,100) ); ?>
                    </div>
                    <!-- Display Цитата -->


                    <?php $discussionId = get_the_ID();   ?>
                    <!-- Display Title and Author of research article -->
                    <div>
                    <strong>Статья: </strong> <?php echo esc_html( get_post_meta( $discussionId, 'ra_title', true ) ); ?><br />
                    <strong>Опубликована: </strong> <?php echo esc_html( get_post_meta( $discussionId, 'ra_pubblished', true ) ); ?><br />
                    <strong>Специализация: </strong> <?php echo esc_html( get_post_meta( $discussionId, 'ra_tag', true ) ); ?><br />
                    </div>

                    <div>
                        <strong>Читать статью полностью: </strong> <?php echo esc_html( get_post_meta( $discussionId, 'ra_link', true ) ); ?><br />
                    </div>
                    <div>
                        <strong>Хорошие статьи по данной тематике: </strong> <?php echo esc_html( get_post_meta( $discussionId, 'ra_generic_links', true ) ); ?><br />
                    </div>

                </header>

                <div>
                    <strong>Основные идеи статьи: </strong><br />
                    <?php echo esc_html( get_post_meta( $discussionId, 'ra_generic_links', true ) ); ?><br />
                </div>
                <!-- Display discussion contents -->
                <div class="entry-content"><?php the_content(); ?></div>



                <!-- Display comments -->

                <?php
                $_ids = get_the_ID();
                $args = array('post__in' => array($_ids)

                );

                // The Comment Query
                $comments_query = new WP_Comment_Query;
                $comments = $comments_query->query( $args );

                // Comment Loop
                if ( $comments ) {
                foreach ( $comments as $comment ) {
                echo '<p>' . $comment->comment_content . '</p>';
                }
                } else {
                echo 'No comments found.';
                }
                ?>



                <!--END Display comments -->




                <?php comment_form(); ?>
            </article>

        <?php endwhile; ?>
    </div>
</div>
<?php wp_reset_query(); ?>
<?php get_footer(); ?>