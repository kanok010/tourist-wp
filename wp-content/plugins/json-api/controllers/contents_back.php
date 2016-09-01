<?php
/*
Controller name: Contents
Controller description: Data manipulation methods for posts
*/

class JSON_API_Contents_Controller {

  //interest
  public function interests( $post_type = 'interests'){
    global $json_api;
    $posts = $json_api->introspector->get_posts(false,false,$post_type);
    $posts = $this->interest_repo($posts);
    return $this->posts_result($posts);
  }

  protected function interest_repo($datas){
    $posts = array();
    $lang = $this->get_lang();
    foreach ($datas as $data) {
      $post = array();
      $post['id'] = $data->id;
      $post['title'] = $lang == ''? $data->title : $data->custom_fields->{'title_'.$lang}[0];
      $post['description'] = $data->content;
      $post['created_date'] = $data->date;
      $post['updated_date'] = $data->modified;
      $post['author'] = $data->author;
      array_push($posts, $post);
    }
    return $posts;
  }

  //user_interest
  public function user_interests($post_type = 'user_interests'){
    global $json_api;
    $posts = $json_api->introspector->get_posts(array(
        's' => $json_api->query->ssoid
      ),false,$post_type);
    $posts = $this->user_interest_repo( $posts );
    return $this->posts_result($posts);
  }

  protected function user_interest_repo( $datas = null ){
    global $json_api;

    $posts = array();
    $lang = $this->get_lang();
    foreach ($datas as $data) {
      if( $json_api->query->ssoid ){
        if($json_api->query->ssoid == $data->title){
          $post = array();
          $post['id'] = $data->id;
          $post['ssoid'] = $data->title ;
          $post['interests'] = unserialize($data->custom_fields->interests[0]);
          $post['created_date'] = $data->date;
          $post['updated_date'] = $data->modified;
          $post['author'] = $data->author;
          array_push($posts, $post);
        }
      }else{
        $post = array();
        $post['id'] = $data->id;
        $post['ssoid'] = $data->title ;
        $post['interests'] = unserialize($data->custom_fields->interests[0]);
        $post['created_date'] = $data->date;
        $post['updated_date'] = $data->modified;
        $post['author'] = $data->author;
        array_push($posts, $post);
      }
    }
    return $posts;
  }

  public function user_interest_create($post_type = 'user_interests') {
    global $json_api;

    $ssoid = isset($_GET['ssoid'])&&!empty($_GET['ssoid']) ? strtolower($_GET['ssoid']) : ''; 
    $interests = isset($_GET['interests'])&&!empty($_GET['interests']) ? $_GET['interests'] : '[]'; 
    $interests = str_replace( [ '[',']','{','}' ] , '', $interests ) ; 
    $interests = @explode(',',$interests);

    $args = array(
      'post_type' => $post_type,
      's' => $ssoid,
    );
    $query = new WP_Query( $args );
    $post = $query->post;

    if($post){
      $post_id = $post->ID;
      update_post_meta($post_id, 'interests', serialize($interests) ); 
    }else{
      $my_post=array(
          'post_title' => wp_strip_all_tags($ssoid),
          'post_author' => 1,
          'post_category' => array(),
          'post_content' => '',
          'post_status' => 'publish',
          'post_type' => $post_type
      );
      // add post
      $post_id=wp_insert_post($my_post);
      //custom_fields
      add_post_meta($post_id, 'interests', serialize($interests) );
    }

    return array(
        'id' => $post_id
    );
  }

  //basic function
  protected function get_lang(){
    return isset($_GET['lang'])&&!empty($_GET['lang']) ? strtolower($_GET['lang']) : ''; 
  }

  protected function posts_result($posts) {
    global $wp_query;

    return array(
      'offset' => count($posts), //offset
      'total' => (int) $wp_query->found_posts,
      'pages' => $wp_query->max_num_pages,
      'post_type' => $wp_query->query_vars['post_type'],
      'posts' => $posts
    );
  }
//
}

?>
