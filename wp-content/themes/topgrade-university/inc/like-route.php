<?php

add_action('rest_api_init', 'universityLikeRoutes');

function universityLikeRoutes(){
    register_rest_route('university/v1', 'manageLike', array(
        'methods' => 'POST',
        'callback' => 'createLike'
    ));

    register_rest_route('university/v1', 'manageLike', array(
        'methods' => 'DELETE',
        'callback' => 'deleteLike'
    ));
}

function createLike($data){
    if(is_user_logged_in()) {
        $professor = sanitize_text_field($data['professorId']);
        $existQuery = new WP_Query(array(
            'author' => get_current_user_id(),
            'post_type' => 'like',
            'meta_query' => array(
              array(
                'key' => 'liked_professor_id',
                'compare' => '=',
                'value' => $professor
              )
            )
            ));
      if($existQuery->found_posts == 0 AND get_post_type($professor) == 'professor'){
        return wp_insert_post(array(
            'post_type' => 'like',
            'post_status' => 'publish',
            'post_title' => 'Second TestLike',
            'meta_input' => array(
                'liked_professor_id' => $professor
            )
        ));
      } else{
        die("Already liked");
      }

        
    } else {
        die("Only logged in users can like a professor.");
    }
}

function deleteLike($data){
    $likedId = sanitize_text_field($data['like']);

    if(get_current_user_id() == get_post_field('post_author', $likedId) AND get_post_type($likedId) == 'like') {
        wp_delete_post($likedId, true);
        return 'Congrats, like deleted.';
    } else {
        die("You do not have permission.");
    }
}
?>