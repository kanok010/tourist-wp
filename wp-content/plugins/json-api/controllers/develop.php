<?php
/*
Controller name: Develop
Controller description: Data manipulation methods for post (dev)
*/

class JSON_API_Develop_Controller {

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
      $t = $this->wp_attach($data->custom_fields->thumbnail2 , 'thumbnail' , $attachments );
      $post['thumbnail2'] = $t[0]['url'];
      $t = $this->wp_attach($data->custom_fields->thumbnail2 , 'full' , $attachments );
      $post['thumbnail2_full'] = $t[0]['url'];
      //
      $post['link_url'] = $data->custom_fields->link_url[0];
      //
      $post['created_date'] = $data->date;
      $post['updated_date'] = $data->modified;
      array_push($posts, $post);
    }
    return $posts;
  }
  
  
  //get phone number
  public function get_phone_number(){
    
    $msisdn = (isset($_SERVER['HTTP_X_MSISDN'])?$_SERVER['HTTP_X_MSISDN']:'');

    $oper = (isset($_SERVER['HTTP_X_OPER'])?$_SERVER['HTTP_X_OPER']:'');
    return array(
      'msisdn' => $msisdn, 
      'oper' => $oper
    );
  }
  
  //privileges
  public function privileges( $post_type = 'privilege',$query = false){
    global $json_api;
    $query = array("orderby" => "menu_order" ,  "order" => "ASC", "post_status"=>'pending' );
    $posts = $json_api->introspector->get_posts($query,false,$post_type);
    
     // print_r($posts);exit;
    $posts = $this->privileges_repo($posts);
    
    return $this->posts_result2($posts);
  }
  
  protected function privileges_repo($datas){
    date_default_timezone_set("Asia/Bangkok");   
    $today = date("Y-m-d");
    $posts = array();
    foreach ($datas as $data) {
     
      $s_date =  $data->custom_fields->start_date[0];
      $yy = substr($s_date,0,4);
      $mm = substr($s_date,4,2);
      $dd = substr($s_date,6,2);
      $start_date = date('Y-m-d', strtotime($yy."-".$mm."-".$dd));
      
      
      $e_date =  $data->custom_fields->end_date[0];
      $yyy = substr($e_date,0,4);
      $mmm = substr($e_date,4,2);
      $ddd = substr($e_date,6,2);
      $end_date = date('Y-m-d', strtotime($yyy."-".$mmm."-".$ddd));
      
      //echo "Start:".$start_date."<br/>";
      //echo "End:".$end_date."<br/>";
          
      if((strtotime($start_date) <= strtotime($today)) &&(strtotime($today) <= strtotime($end_date))) {
        $post = array();
        $post['id'] = $data->id;
        $post['title'] = $data->title ;
        $post['detail'] = $data->custom_fields->detail[0];
        $post['ussd'] = $data->custom_fields->ussd[0];
        $post['pack_code'] = $data->custom_fields->pack_code[0];
        $attachments = $data->attachments;
        $t = $this->wp_attach($data->custom_fields->thumbnail , 'full' , $attachments );
        $post['thumbnail'] = $t[0]['url'];
        $t = $this->wp_attach($data->custom_fields->image , 'full' , $attachments );
        $post['image'] = $t[0]['url'];
        $post['start_date'] = $start_date;
        $post['end_date'] = $end_date;   
        //$post['sort_order'] = $data->custom_fields->sort_order[0];   
        $post['status'] = $data->status;
        $post['created_date'] = $data->date;
        $post['updated_date'] = $data->modified;

        array_push($posts, $post);
      }//end if
       
    }
    //print_r($posts);exit;
    return $posts;
  }
  
   //privileges
  public function privilege_detail( $post_type = 'privilege',$query = false){
      
    global $json_api;
    
     if (isset($_GET['id']) && ($_GET['id']!="")){
        $id = $_GET['id'];
    }else{
        $id = 0;
    }  
    $query = array("p" => $id);
    $posts = $json_api->introspector->get_posts($query,false,$post_type);
    
    // print_r($posts);exit;
    $posts = $this->privilege_detail_repo($posts);
    
    return $this->posts_result2($posts);
  }
  
  protected function privilege_detail_repo($datas){   
    date_default_timezone_set("Asia/Bangkok");
    $today = date("Y-m-d"); 
    $posts = array();
    foreach ($datas as $data) {
     
      $s_date =  $data->custom_fields->start_date[0];
      $yy = substr($s_date,0,4);
      $mm = substr($s_date,4,2);
      $dd = substr($s_date,6,2);
      $start_date = date('Y-m-d', strtotime($yy."-".$mm."-".$dd));
      
      
      $e_date =  $data->custom_fields->end_date[0];
      $yyy = substr($e_date,0,4);
      $mmm = substr($e_date,4,2);
      $ddd = substr($e_date,6,2);
      $end_date = date('Y-m-d', strtotime($yyy."-".$mmm."-".$ddd));
      
      //echo "Start:".$start_date."<br/>";
      //echo "End:".$end_date."<br/>";
                 
      if((strtotime($start_date) <= strtotime($today)) &&(strtotime($today) <= strtotime($end_date))) {
        $post = array(); 
        $post['id'] = $data->id;
        $post['title'] = $data->title ;
        $post['detail'] = $data->custom_fields->detail[0];
        $post['ussd'] = $data->custom_fields->ussd[0];
        $post['pack_code'] = $data->custom_fields->pack_code[0];
        $post['lang'] = $data->custom_fields->lang[0];
        
        $attachments = $data->attachments;
        $t = $this->wp_attach($data->custom_fields->thumbnail , 'full' , $attachments );
        $post['thumbnail'] = $t[0]['url'];
        $t = $this->wp_attach($data->custom_fields->image , 'full' , $attachments );
        $post['image'] = $t[0]['url'];
        $post['start_date'] = $start_date;
        $post['end_date'] = $end_date;   
        //$post['sort_order'] = $data->custom_fields->sort_order[0];   
        $post['status'] = $data->status;
        $post['created_date'] = $data->date;
        $post['updated_date'] = $data->modified;
        
        array_push($posts, $post);
      }//end if
       
    }
    //print_r($posts);exit;
    return $posts;
  }
  
  //Packages
  public function packages( $post_type = 'package',$query = false){
    global $json_api;
    $query = array("orderby" => "menu_order" ,  "order" => "ASC" , "post_status"=>'pending');
    
    $posts = $json_api->introspector->get_posts($query,false,$post_type);
    
    //print_r($posts);exit;
    $posts = $this->packages_repo($posts);
    
    return $this->posts_result2($posts);
  }
  
  protected function packages_repo($datas){
    $posts = array();
    foreach ($datas as $data) {
     
      
        $post = array();
        $post['id'] = $data->id;
        $post['title'] = $data->title ;
        $post['title_cn'] = $data->custom_fields->title_cn[0];
        $post['description'] = $data->custom_fields->description[0];
        $post['description_cn'] = $data->custom_fields->description_cn[0];
        $post['detail'] = $data->custom_fields->detail[0];
        $post['detail_cn'] = $data->custom_fields->detail_cn[0];
        $post['term'] = $data->custom_fields->term[0];
        $post['term_cn'] = $data->custom_fields->term_cn[0];
        $attachments = $data->attachments;
        $t = $this->wp_attach($data->custom_fields->thumbnail , 'full' , $attachments );
        $post['thumbnail'] = $t[0]['url'];
        $tt = $this->wp_attach($data->custom_fields->thumbnail_cn , 'full' , $attachments );
        $post['thumbnail_cn'] = $tt[0]['url'];
        if($post['thumbnail']==""){
           $post['type'] = "text"; 
        }else{
           $post['type'] = "image";  
        }
        $post['pack_code'] = $data->custom_fields->pack_code[0];
        $post['price'] = $data->custom_fields->price[0];
        $post['ussd'] = $data->custom_fields->ussd[0];
        //$post['sort_order'] = $data->custom_fields->sort_order[0];   
        $post['status'] = $data->status;
        $post['created_date'] = $data->date;
        $post['updated_date'] = $data->modified;

        array_push($posts, $post);
      
       
    }
    //print_r($posts);exit;
    return $posts;
  }
  
    //Package Detail
  public function package_detail( $post_type = 'package',$id){
    global $json_api;
    
    if (isset($_GET['id']) && ($_GET['id']!="")){
        $id = $_GET['id'];
    }else{
        $id = 0;
    }  
    $query = array("p" => $id);

    $posts = $json_api->introspector->get_posts($query,false,$post_type);
    
    //print_r($posts);exit;
    $posts = $this->package_detail_repo($posts);
    
    return $this->posts_result2($posts);
  }
  protected function package_detail_repo($datas){
    $posts = array();
    foreach ($datas as $data) {
     
      
        $post = array();
        $post['id'] = $data->id;
        $post['title'] = $data->title ;
        $post['title_cn'] = $data->custom_fields->title_cn[0];
        $post['description'] = $data->custom_fields->description[0];
        $post['description_cn'] = $data->custom_fields->description_cn[0];
        $post['detail'] = $data->custom_fields->detail[0];
        $post['detail_cn'] = $data->custom_fields->detail_cn[0];
        $post['term'] = $data->custom_fields->term[0];
        $post['term_cn'] = $data->custom_fields->term_cn[0];
        
        $attachments = $data->attachments;
        $t = $this->wp_attach($data->custom_fields->thumbnail , 'full' , $attachments );
        $post['thumbnail'] = $t[0]['url'];
        $tt = $this->wp_attach($data->custom_fields->thumbnail_cn , 'full' , $attachments );
        $post['thumbnail_cn'] = $tt[0]['url'];
        if($post['thumbnail']==""){
           $post['type'] = "text"; 
        }else{
           $post['type'] = "image";  
        }
        $post['pack_code'] = $data->custom_fields->pack_code[0];
        $post['price'] = $data->custom_fields->price[0];
        $post['ussd'] = $data->custom_fields->ussd[0];
        //$post['sort_order'] = $data->custom_fields->sort_order[0];   
        $post['status'] = $data->status;
        $post['created_date'] = $data->date;
        $post['updated_date'] = $data->modified;

        array_push($posts, $post);
      
       
    }
    //print_r($posts);exit;
    return $posts;
  }
  
  //Advertise
  public function advertising( $post_type = 'advertising',$query = false){
    global $json_api,$wp_query;
    //$query = array("orderby" => "menu_order" ,  "order" => "ASC" );
    $query = array("orderby" => "menu_order" ,  "order" => "ASC" , "post_status"=>'pending');

    $posts = $json_api->introspector->get_posts($query,false,$post_type);
    
    //print_r($posts);exit;
    $posts = $this->advertising_repo($posts);
    
    return array(
      'count' => count($posts), //offset
      'post_type' => $wp_query->query_vars['post_type'],
      'weight' => 10,  
      'datas' => $posts
    );
  }
  
  protected function advertising_repo($datas){
    date_default_timezone_set("Asia/Bangkok");  
    $today = date("Y-m-d");  
    $posts = array();
    foreach ($datas as $data) {
     
        $s_date =  $data->custom_fields->start_date[0];
        $yy = substr($s_date,0,4);
        $mm = substr($s_date,4,2);
        $dd = substr($s_date,6,2);
        $start_date = date('Y-m-d', strtotime($yy."-".$mm."-".$dd));
      
      
        $e_date =  $data->custom_fields->end_date[0];
        $yyy = substr($e_date,0,4);
        $mmm = substr($e_date,4,2);
        $ddd = substr($e_date,6,2);
        $end_date = date('Y-m-d', strtotime($yyy."-".$mmm."-".$ddd));
        if((strtotime($start_date) <= strtotime($today)) &&(strtotime($today) <= strtotime($end_date))) {
            $post = array();
            $post['id'] = $data->id;
            $post['title'] = $data->title ;
            $attachments = $data->attachments;
            $t = $this->wp_attach($data->custom_fields->thumbnail , 'full' , $attachments );     
            $post['thumbnail'] = $t[0]['url'];
            
            $post['type'] = $data->custom_fields->type[0];
            if($post['type'] =="schema"){
                $post['page_type'] = $data->custom_fields->page_type[0];
                $page_id = $data->custom_fields->page_id[0];
                if($page_id && $page_id!=""){
                    if($post['page_type'] == "package"){
                        $post['link_url'] = get_site_url()."/api/contents/package_detail?id=".$page_id;
                    }
                    if($post['page_type'] == "privilege"){
                        $post['link_url'] = get_site_url()."/api/contents/privilege_detail?id=".$page_id;
                    }
                }else{
                    $post['link_url'] = "";
                }
                
            }else{
                $post['page_type'] = "";
                $post['link_url'] = $data->custom_fields->link_url[0];                
            }
  
            $post['start_date'] = $start_date;
            $post['end_date'] = $end_date; 
            $post['status'] = $data->status;
            $post['created_date'] = $data->date;
            $post['updated_date'] = $data->modified;

            array_push($posts, $post);
        }//end if
       
    }
    //print_r($posts);exit;
    return $posts;
  }
  
    //announcement
  public function announcement( $post_type = 'announcement',$query = false){
    global $json_api;
    //$query = array("orderby" => "menu_order" ,  "order" => "ASC" );
    $query = array("orderby" => "menu_order" ,  "order" => "ASC" , "post_status"=>'pending');
    $query = array("posts_per_page" => "1");
    $posts = $json_api->introspector->get_posts($query,false,$post_type);
    
    //print_r($posts);exit;
    $posts = $this->announcement_repo($posts);
    
    return $this->posts_result2($posts);
  }
  
  protected function announcement_repo($datas){
    date_default_timezone_set("Asia/Bangkok");   
    $today = date("Y-m-d");  
    $posts = array();
    foreach ($datas as $data) {
     
        $s_date =  $data->custom_fields->start_date[0];
        if($s_date){
            $yy = substr($s_date,0,4);
            $mm = substr($s_date,4,2);
            $dd = substr($s_date,6,2);
            $start_date = date('Y-m-d', strtotime($yy."-".$mm."-".$dd));            
        }else{
            $start_date ="";
        }

        $e_date =  $data->custom_fields->end_date[0];
        if($e_date){
            $yyy = substr($e_date,0,4);
            $mmm = substr($e_date,4,2);
            $ddd = substr($e_date,6,2);
            $end_date = date('Y-m-d', strtotime($yyy."-".$mmm."-".$ddd));
        }else{
            $end_date = "";
        }
        if(($start_date!="") && ($end_date!="")){
            if((strtotime($start_date) <= strtotime($today)) &&(strtotime($today) <= strtotime($end_date))) {
                $post = array();
                $post['id'] = $data->id;
                $post['title'] = $data->title;
                $post['detail'] = $data->custom_fields->detail[0];
                $attachments = $data->attachments;
                $t = $this->wp_attach($data->custom_fields->image , 'full' , $attachments );
                $post['image'] = $t[0]['url'];
                $post['start_date'] = $start_date;
                $post['end_date'] = $end_date; 
                $post['btn_status'] = $data->custom_fields->btn_status[0];
                if($post['btn_status'] == "1"){
                    $post['btn_title'] = $data->custom_fields->btn_title[0];
                    $post['btn_type'] = $data->custom_fields->btn_type[0];
                    $post['btn_link'] = $data->custom_fields->btn_link[0];
                }else{
                    $post['btn_title'] = "";
                    $post['btn_type'] = "";
                    $post['btn_link'] = "";
                }
                $post['status'] = $data->status;
                $post['created_date'] = $data->date;
                $post['updated_date'] = $data->modified;

                array_push($posts, $post);
            }//end if2
        }//end if1
        if(($start_date!="") && ($end_date=="")){
            if(strtotime($start_date) <= strtotime($today)){
                $post = array();
                $post['id'] = $data->id;
                $post['title'] = $data->title;
                $post['detail'] = $data->custom_fields->detail[0];
                $attachments = $data->attachments;
                $t = $this->wp_attach($data->custom_fields->image , 'full' , $attachments );
                $post['image'] = $t[0]['url'];
                $post['start_date'] = $start_date;
                $post['end_date'] = $end_date; 
                $post['btn_status'] = $data->custom_fields->btn_status[0];
                if($post['btn_status'] == "1"){
                    $post['btn_title'] = $data->custom_fields->btn_title[0];
                    $post['btn_type'] = $data->custom_fields->btn_type[0];
                    $post['btn_link'] = $data->custom_fields->btn_link[0];
                }else{
                    $post['btn_title'] = "";
                    $post['btn_type'] = "";
                    $post['btn_link'] = "";
                }
                $post['status'] = $data->status;
                $post['created_date'] = $data->date;
                $post['updated_date'] = $data->modified;

                array_push($posts, $post);
            }//end if2
        }//end if1
    }
    //print_r($posts);exit;
    return $posts;
  }

  //hotline
  public function hotline( $post_type = 'hotline',$query = false){
    global $json_api;
    $query = $this->setOrder();
    $query = array("post_status"=>'pending');
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

  // shelf
  public function shelf( $post_type = 'shelf',$query = false){
    global $json_api;
    global $wp_query;
    $shelf = $this->get_shelf();

    $limit = isset($_GET['limit']) ? $_GET['limit'] : $wp_query->query_vars['posts_per_page'];
    $offset = isset($_GET['page']) ? ($_GET['page']-1) * $limit : $wp_query->query_vars['paged'];
    $page = isset($_GET['page']) ? $_GET['page']-1 : ($wp_query->query_vars['paged'] <= 0 ? 0 : $wp_query->query_vars['paged']-1);

    if(is_null($shelf)){
      return $this->posts_result_setting(array(),array(),0,$page+1,0,"",$limit);
    }
    $query = array(
        'name' => $shelf
      );

    $post = $json_api->introspector->get_posts($query,false,$post_type);
    $type_post = $post[0]->taxonomy_shelf[0]->slug;
    $list_state_all = unserialize($post[0]->custom_fields->{$type_post}[0]); 

      $attachments = $post[0]->attachments;
      $t = $this->wp_attach($post[0]->custom_fields->thumbnail , 'full' , $attachments );
    $shelf_list["thumbnail_full"] = $t[0]['url'];
      $t = $this->wp_attach($post[0]->custom_fields->thumbnail2 , 'full' , $attachments );
    $shelf_list["thumbnail2_full"] = $t[0]['url'];

    if(count($post) <= 0){ return $this->posts_result_setting(array(),array(),0,$page+1,0,$shelf,$limit); }

    wp_reset_query();
    $query = array();
    $total_states = count($list_state_all);

    $state_limit = array_chunk($list_state_all, $limit);
    $list = $state_limit[$page];

    $loc_array = array_chunk($list, 5);
    $total_loc = $total_states < $limit ? $total_states : count($list)<$limit ? count($list) : $limit;

      for ($i=0 ; $i<ceil($total_loc/5) ; $i++) {
        $query[$i] = array(
            'post__in' => $loc_array[$i],
          );
      }

    $posts = array();
        $json_api->query->page = 1; 
        foreach ($query as $query_value) {
          $posts_sub = $json_api->introspector->get_posts($query_value, false , $type_post );
          wp_reset_query();
            if(count($posts)>0){
              foreach ($posts_sub as $post_sub) {
                array_push($posts, $post_sub);
              }
            }else{
              $posts = $posts_sub;
            }
        }
 
      $wp_query->found_posts = $total_loc[0]->total;
      $wp_query->max_num_pages = ceil($total_loc[0]->total/$limit);
      $wp_query->query_vars['paged'] = ($offset / $limit) + 1 ; 

    $posts = $this->{$type_post.'_repo'}($posts);
    $posts = $this->order_shelf($list,$posts);
    // $posts = array_reverse($posts);
    // print_r($posts);exit;
    return $this->posts_result_setting($posts,$shelf_list,$total_states,$page+1,count($state_limit),$shelf,$limit);
  }

  public function shelf_list( $post_type = 'shelf',$query = false){
    global $json_api;

    $query = array(
        'numberposts' => -1,
        'posts_per_page'  => -1,
      );

    $posts = $json_api->introspector->get_posts($query,false,$post_type);
    $posts = $this->shelf_repo($posts);

    return $this->posts_result_detail($posts);
  }
  protected function order_shelf($list,$posts)
  {
    $data = array();
    $list = array_flip($list);
    
    foreach ($posts as $post) {
      $id = $post['id'];
      $l_id = $list[$id];

      $data[$l_id] = $post;
    }
    ksort($data);

    return $data;
  }

  protected function shelf_repo($datas){
    $posts = array();

    foreach ($datas as $data) {
      $post = array();
      $post['id'] = $data->id;
      $post['type'] = $data->type;
      $post['shelf_name'] = $data->slug;
      $post['shelf_detail'] = $data->taxonomy_shelf[0]->title;

      array_push($posts, $post);
    }
    return $posts;
  }
  //basic function
  protected function get_lang(){
    return isset($_GET['lang'])&&!empty($_GET['lang']) ? strtolower($_GET['lang']) : 'en'; 
  }

  protected function get_shelf(){
    return isset($_GET['shelf_name'])&&!empty($_GET['shelf_name']) ? strtolower($_GET['shelf_name']) : null; 
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
      'limit' => $wp_query->query_vars['posts_per_page'],
      'count' => count($posts), //offset
      'count' => (int) $wp_query->found_posts,
      'cur_pages' => $wp_query->query_vars['paged'] <= 0 ? 1 : $wp_query->query_vars['paged'],
      'pages' => $wp_query->max_num_pages,
      'post_type' => $wp_query->query_vars['post_type'],
      'datas' => $posts
    );
  }

  protected function posts_result2($posts) {
    global $wp_query;

    return array(
      'count' => count($posts), //offset
      'post_type' => $wp_query->query_vars['post_type'],
      'datas' => $posts
    );
  }

  protected function posts_result_setting($posts,$shelf_list,$total,$cur_pages,$pages,$post_type,$limit) {
    global $wp_query;
    
    $data = array(
      'limit' => (int) $limit,
      'count' => count($posts), //offset
      'total' => (int) $total,
      'cur_pages' => (int) $cur_pages,
      'pages' => (int) $pages,
      'shelf_name' => $post_type,
    );
    $data = array_merge($data,$shelf_list);
    // print_r($data);exit;
    return array_merge($data, array('datas' => $posts));
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
