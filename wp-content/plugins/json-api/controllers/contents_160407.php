<?php
/*
Controller name: Contents
Controller description: Data manipulation methods for posts
*/

class JSON_API_Contents_Controller {

  //language
  public function language( $post_type = 'language',$query = false){
    global $json_api;
    $query = $this->setOrder();

    $l_query = array(
            'numberposts' => -1,
            'posts_per_page'  => -1,
          );
    $query = $query!==false ? array_merge($query,$l_query) : $l_query;

    $posts = $json_api->introspector->get_posts($query,false,$post_type);
    $posts = $this->language_repo($posts);
    // print_r($posts);exit;
    return $this->posts_result_detail($posts);
  }

  protected function language_repo($datas){
    $posts = array();
    $lang = $this->get_lang();
    foreach ($datas as $data) {
      $post = array();
      $post['id'] = $data->id;
      // print_r($data);exit;
      foreach ($data->custom_fields as $key => $custom_field) {
         if (strpos($key, 'title_') !== false) {
             $post[$key] = $custom_field[0] ;
          }
      }
      $post['slug'] = $data->custom_fields->slug[0];
      array_push($posts, $post);
    }
    return $posts;
  }

  //map
  public function map( $post_type = 'map',$query = false){
    global $json_api;
    $query = $this->setOrder();

    $posts = $json_api->introspector->get_posts($query,false,$post_type);
    $posts = $this->map_repo($posts);
    // print_r($posts);exit;
    return $this->posts_result($posts);
  }

  protected function map_repo($datas){
    $posts = array();
    $lang = $this->get_lang();
    foreach ($datas as $data) {
      // print_r($data);exit;
      $post = array();
      $post['id'] = $data->id;
      $post['city'] = isset($data->custom_fields->{'title_'.$lang}[0])? $data->custom_fields->{'title_'.$lang}[0] : $data->title ;
      //
        $country_id = $data->taxonomy_country[0]->id;
        $country = get_field( 'title_'.$lang , 'country_'.$country_id );
        $post['country'] = $country;
      //
      //   $thumbnail = array();
        $attachments = $data->attachments;
      //   $img_i = 0;
      // foreach ($data->custom_fields as $key => $custom_field) {
      //    if (strpos($key, 'image_') !== false && strpos($key, '_thumbnail') !== false ){
      //       $key_1 = str_replace('_thumbnail', "" , $key);
            // $t = $this->wp_attach($custom_field , 'full' , $attachments );
      //       $thumbnail[$data->custom_fields->{$key_1.'_name'}[0]] = $t[0];
      //       unset($data->custom_fields->{$key_1.'_name'});
      //       $img_i++;
      //     }
      // }

      $t = $this->wp_attach($data->custom_fields->thumbnail , 'thumbnail' , $attachments );
      // print_r($t);print_r($data);exit;
      $post['thumbnail'] = $t[0]['url'];
      $t = $this->wp_attach($data->custom_fields->thumbnail , 'full' , $attachments );
      $post['thumbnail_full'] = $t[0]['url'];
        //2
      if(isset($data->custom_fields->thumbnail2)){
        $t = $this->wp_attach($data->custom_fields->thumbnail2 , 'thumbnail' , $attachments );
        $post['thumbnail2'] = $t[0]['url'];
        $t = $this->wp_attach($data->custom_fields->thumbnail2 , 'full' , $attachments );
        $post['thumbnail2_full'] = $t[0]['url'];
      }else{
        $post['thumbnail2'] = "";
        $post['thumbnail2_full'] = "";
      }
      //
      $post['download_map']['ios'] = $data->custom_fields->ios[0];
      $post['download_map']['android'] = isset($data->custom_fields->android[0]) ? wp_get_attachment_url($data->custom_fields->android[0]) : null;
      //keyword
      $keyword = isset($data->custom_fields->keyword[0]) ? $data->custom_fields->keyword[0] : 0;
      if($keyword>0){
        for ($k=0; $k < $keyword; $k++) { 
          if(isset($data->custom_fields->{'keyword_'.$k.'_name'}[0]) && isset($data->custom_fields->{'keyword_'.$k.'_value'}[0])){
            $key_name = $data->custom_fields->{'keyword_'.$k.'_name'}[0];
            $key_val = $data->custom_fields->{'keyword_'.$k.'_value'}[0];
            $post['keywords'][$key_name] =  is_numeric($key_val) ? (int) $key_val : $key_val;
          }
        }
      }else{
        $post['keywords'] = array();
      }
      //
      $post['created_date'] = $data->date;
      $post['updated_date'] = $data->modified;
      array_push($posts, $post);
    }
    return $posts;
  }

  //main
  public function main( $post_type = 'main',$query = false){
    global $json_api;
    $query = $this->setOrder();

    $posts = $json_api->introspector->get_posts($query,false,$post_type);
    $posts = $this->main_repo($posts);
    // print_r($posts);exit;
    return $this->posts_result($posts);
  }

  protected function main_repo($datas){
    $posts = array();
    $lang = $this->get_lang();
    foreach ($datas as $data) {
      $post = array();
      $post['id'] = $data->id;
      $post['title'] = isset($data->custom_fields->{'title_'.$lang}[0])? $data->custom_fields->{'title_'.$lang}[0] : $data->title ;
      //
        $type = strtolower($data->taxonomy_main_type[0]->title);
        $post['type'] = $type;
      //
        $img_array = array();
        $attachments = $data->attachments;
      //   $thumbnail[0] = isset($data->custom_fields->thumbnail[0]) ? $data->custom_fields->thumbnail[0] : null;
      //   if( $thumbnail[0] ){
      //     $thumbnail[0] = $this->wp_attach($thumbnail , 'thumbnail' , $attachments , "thumbnail");
      //   }
      // $post['thumbnail'] = $thumbnail[0];
      $t = $this->wp_attach($data->custom_fields->thumbnail , 'thumbnail' , $attachments );
      $post['thumbnail'] = $t[0]['url'];
      $t = $this->wp_attach($data->custom_fields->thumbnail , 'full' , $attachments );
      $post['thumbnail_full'] = $t[0]['url'];
      //
      $post['link_url'] = $data->custom_fields->link_url[0];
      //
      $post['created_date'] = $data->date;
      $post['updated_date'] = $data->modified;
      array_push($posts, $post);
    }
    return $posts;
  }

  //hotline
  public function hotline( $post_type = 'hotline',$query = false){
    global $json_api;
    $query = $this->setOrder();

    $posts = $json_api->introspector->get_posts($query,false,$post_type);
    $posts = $this->hotline_repo($posts);
    // print_r($posts);exit;
    return $this->posts_result($posts);
  }

  protected function hotline_repo($datas){
    $posts = array();
    $lang = $this->get_lang();
    foreach ($datas as $data) {
      $post = array();
      $post['id'] = $data->id;
      $post['title'] = isset($data->custom_fields->{'title_'.$lang}[0])? $data->custom_fields->{'title_'.$lang}[0] : $data->title ;
      //
      $post['telephone'] = $data->custom_fields->telephone[0];
      //
      $post['created_date'] = $data->date;
      $post['updated_date'] = $data->modified;
      array_push($posts, $post);
    }
    return $posts;
  }

  //basic function
  protected function get_lang(){
    return isset($_GET['lang'])&&!empty($_GET['lang']) ? strtolower($_GET['lang']) : 'th'; 
  }

  public function wp_attach ($images = array() , $size = "thumbnail" , $attachments =array() , $mode = null ){
    $datas = array();
    $attach = array();
    foreach ($attachments as $attachment) {
      if(isset($attachment->images) && count($attachment->images)>0)
        $attach[$attachment->id] = $attachment->images;
    }
    foreach ($images as $image_id) {
      if( array_key_exists( $image_id, $attach) === true){
        array_push( $datas, ["id"=>(int) $image_id , "url"=>$attach[$image_id][$size]->url , "width"=>$attach[$image_id][$size]->width , "height"=>$attach[$image_id][$size]->height , "size"=>$size] );
        if($mode == "thumbnail"){
          $size_2 = "full";
          array_push( $datas, ["id"=>(int)$image_id , "url"=>$attach[$image_id][$size_2]->url , "width"=>$attach[$image_id][$size_2]->width , "height"=>$attach[$image_id][$size_2]->height , "size"=>$size_2] );
        }
      }else{
        $attachment = wp_get_attachment_image_src( $image_id , $size );
        array_push( $datas, ["id"=>(int)$image_id , "url"=>$attachment[0] , "width"=>$attachment[1] , "height"=>$attachment[2] , "size"=>$size] );
        if($mode == "thumbnail"){
          $size_2 = "full";
          $attachment = wp_get_attachment_image_src( $image_id , $size_2 );
          array_push( $datas, ["id"=>(int)$image_id , "url"=>$attachment[0] , "width"=>$attachment[1] , "height"=>$attachment[2] , "size"=>$size_2] );
        }
      }
    }

    return $datas;
  }

  protected function setOrder(){
    $lang = $this->get_lang();

    $order = isset($_GET['order'])&&!empty($_GET['order']) ? strtolower($_GET['order']) : "DESC"; 
    $order_by = isset($_GET['order_by'])&&!empty($_GET['order_by']) ? strtolower($_GET['order_by']) : "id";
    if($order_by == "id" || $order_by == "created_date" || $order_by == "updated_date" ){
      $order_by = $order_by == "id" ? "ID" : $order_by;
      $order_by = $order_by == "created_date" ? "date" : $order_by;
      $order_by = $order_by == "updated_date" ? "modified" : $order_by;

      return array( "orderby" => $order_by , "order" => $order);
    }else{
      $order_by = $order_by == "title" ? $order_by.'_'.$lang : $order_by;
      
      return array( "meta_key" => $order_by ,"orderby" => "meta_value" ,  "order" => $order );
    }
  }

  protected function posts_result($posts) {
    global $wp_query;

    return array(
      'offset' => count($posts), //offset
      'total' => (int) $wp_query->found_posts,
      'cur_pages' => $wp_query->query_vars['paged'] <= 0 ? 1 : $wp_query->query_vars['paged'],
      'pages' => $wp_query->max_num_pages,
      'post_type' => $wp_query->query_vars['post_type'],
      'datas' => $posts
    );
  }

  protected function posts_result_detail($posts) {
    global $wp_query;

    return array(
      'datas' => $posts
    );
  }
//
}

?>
