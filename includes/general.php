<?php

add_action( 'admin_init', 'add_meta_boxes' );
function add_meta_boxes() {
    add_meta_box( 'some_metabox', 'Anime Relationship', 'anime_field', 'anime_episode' );
}

function anime_field() {
    global $post;
    $selected_anime = get_post_meta( $post->ID, '_anime', true );
    $all_anime = get_posts( array(
        'post_type'   => 'anime',
        'numberposts' => -1,
        'orderby'     => 'post_title',
        'order'       => 'ASC'
    ));
    ?>
    <input type="hidden" name="anime_nonce" value="<?php echo wp_create_nonce( basename( __FILE__ ) ); ?>" />
    <table class="form-table">
        <tr valign="top">
            <th scope="row">
                <label for="anime">Anime</label>
            </th>
            <td>
                <select name="anime">
                    <?php foreach ( $all_anime as $anime ) : ?>
                        <option value="<?php echo $anime->ID; ?>" <?php echo selected( $selected_anime, $anime->ID, false); ?>><?php echo $anime->post_title; ?></option>
                    <?php endforeach; ?>
                </select>
            </td>
        </tr>
    </table>
    <?php
}

add_action( 'save_post', 'save_anime_field' );
function save_anime_field( $post_id ) {

    // only run this for series
    if ( 'anime_episode' != get_post_type( $post_id ) )
        return $post_id;

    // verify nonce
    if ( empty( $_POST['anime_nonce'] ) || !wp_verify_nonce( $_POST['anime_nonce'], basename( __FILE__ ) ) )
        return $post_id;

    // check autosave
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
        return $post_id;

    // check permissions
    if ( !current_user_can( 'edit_post', $post_id ) )
        return $post_id;

    // save
    update_post_meta( $post_id, '_anime', $_POST['anime'] );
}

/**
 * Create dropdown to filter episodes y anime.
 * 
 * @return void
 */
function anime_admin_episodes_filter_restrict_manage_posts() {

    $type = 'anime';

    if (isset($_GET['post_type'])) {
        $type = $_GET['post_type'];
    }

    //only add filter to post type you want
    if ('anime_episode' == $type){
        $all_anime = get_posts( array(
            'post_type'   => 'anime',
            'numberposts' => -1,
            'orderby'     => 'post_title',
            'order'       => 'ASC'
        ));
        ?>
        <select name="anime_filter">
        <option value="">
            <?php _e( 'All Anime ', 'wose45436' ); ?>
        </option>
        <?php
            $current = isset( $_GET['anime_filter'] ) ? $_GET['anime_filter'] : '';
            foreach ( $all_anime as $anime ) {
                printf( '<option value="%s" %s>%s</option>',
                    $anime->ID,
                    $anime->ID == $current ? ' selected="selected"' : '',
                    $anime->post_title
                );
            }
        ?>
        </select>
        <?php
    }
}
add_action( 'restrict_manage_posts', 'anime_admin_episodes_filter_restrict_manage_posts' );


/**
 * if submitted filter by post meta
 *
 * @param  $query The wp_query object
 * @return Void
 */
function anime_admin_episodes_filter( $query ) {
    global $pagenow;
    $type = 'anime_episode';
    
    if (isset($_GET['post_type'])) {
        $type = $_GET['post_type'];
    }
    
    if ( 'anime_episode' == $type && is_admin() && $pagenow == 'edit.php' && isset( $_GET['anime_filter'] ) && $_GET['anime_filter'] != '' ) {
        $query->query_vars['meta_key'] = '_anime';
        $query->query_vars['meta_value'] = $_GET['anime_filter'];
    }
}
add_filter( 'parse_query', 'anime_admin_episodes_filter' );


function anime_post_type_link_filter( $post_link, $id = 0, $leavename = FALSE ) {
    if ( strpos('%anime%', $post_link ) === 'FALSE' ) {
        return $post_link;
    }

    $post = get_post( $id );
    
    if ( ! is_object( $post ) || $post->post_type != 'anime_episode' ) {
        return $post_link;
    }

    $episode_id = $post->ID;
    $anime_id = get_post_meta( $post->ID, '_anime', true );
    $anime    = get_post( $anime_id );
    
    if ( ! $anime_id ) {
        $episode_slug = apply_filters( 'anva_anime_episode_slug', '%anime%/episode' );
        return str_replace( $episode_slug, '', $post_link );
    }

    $anime_slug = $anime->post_name;

    return str_replace( '%anime%', $anime_slug, $post_link );
}
add_filter( 'post_type_link', 'anime_post_type_link_filter', 1, 3 );
