<?php
/*
Plugin Name: Fb Notice
Plugin URI: http://alisson.portalv1.com.br
Description: Grava notificações dos comentários feitos através do facebook
Version: 0.1
Author: Alisson Araújo
Author URI: http://alisson.portalv1.com.br
*/
define('NOTICE_PATH', plugin_dir_path( __FILE__ ));
define('NOTICE_URL', plugins_url() .'/fb_notice');

require_once NOTICE_PATH . '/classes/class-list-comments-fb.php';
require_once NOTICE_PATH . '/classes/class-list-comments-teste.php';

function FB_Notice_comment_notify_callback() {
    $time = current_time('mysql');
    $data = array(
        'post_status' => 'draft',
        'post_type'  => 'facebook',
        'post_title' => $_POST['autor'],
        'post_content' => $_POST['comentario']
    );
    $seralize = array(
        'pic_facebook'=> $_POST['autorImg'],
        'comment_id'=> $_POST['comentarioID'],
        'comment_post_ID'=> (int) $_POST['post'],
        'comment_author_IP'=> $_SERVER['HTTP_X_FORWARDED_FOR'],
        'comment_agent'=> $_SERVER['HTTP_USER_AGENT'],
        'comment_author_email'=> $_POST['autorEmail'],
        'uid'=> $_POST['autorID'],
        'comment_author_url'=> $_POST['autorUrl'],
    );
    $post_id = wp_insert_post( $data);
    if($post_id){
        add_post_meta($post_id, 'dados_fb', maybe_serialize($seralize));
        echo $post_id .' Criado com sucesso';
    } else {
        echo 'Erro ao criar';
    }
    die();
}
add_action( 'wp_ajax_FB_Notice_comment', 'FB_Notice_comment_notify_callback' );
add_action( 'wp_ajax_nopriv_FB_Notice_comment', 'FB_Notice_comment_notify_callback' );



function delete_comment_FB_notice(){
    global $wpdb;
    $req = ("SELECT * FROM wp_postmeta WHERE meta_key='dados_fb' AND meta_value LIKE '%" . $_POST['comentarioID'] . "%' AND meta_value LIKE '%" . intval($_POST['post_id_fb']) . "%'");
    $meta = $wpdb->get_results($req, ARRAY_A);

    if( check_ajax_referer($_POST['post_id_fb'].'-del_fb_comment', 'nonce_del_fb') ){
        if(wp_delete_post( $meta[0]['post_id'], true ))
           echo 'comentario deletado';
       echo $meta[0]['post_id'];
    } else {
        echo 'Erro ao verificar nonce';
    }

    die();
}
add_action( 'wp_ajax_FB_notice_comment_delete', 'delete_comment_FB_notice' );
add_action( 'wp_ajax_nopriv_FB_notice_comment_delete', 'delete_comment_FB_notice' );


function load_comments_fb(){
    $limit = 20;
    $paged = isset($_GET['paged']) ? $_GET['paged'] : 1;
    $inicio = ($paged * $limit) - $limit;

    $args = array(
        'post_type'=>'facebook',
        'posts_per_page'=>intval($limit),
        'offset'=>intval($inicio),
        'paged'=>intval($paged)
        );
    $commentFb = new WP_Query($args);
    while($commentFb->have_posts()) : $commentFb->the_post();
     $data_fb = unserialize(get_post_meta(get_the_ID(), 'dados_fb', true ));
     $json[] = array(
            'comment_id'    => get_the_ID(),
            'imagem'    => $data_fb['pic_facebook'],
            'user'    => get_the_title(),
            'comentario'      => get_the_content(),
            'link'    => get_permalink($data_fb['comment_post_ID']),
            'titulo'    => get_the_title($data_fb['comment_post_ID']),
            'ip'    => $data_fb['comment_author_IP'],
            'email'    => $data_fb['comment_author_email'],
            'url_face'    => $data_fb['comment_author_url'],
            'date'    => get_the_time('d/m/Y'),
            'max_num_pages' => $commentFb->max_num_pages
        );  
    endwhile;  
    wp_reset_query();
    $matriz = array('fb'=>$json);
    if($commentFb->found_posts){        
        echo json_encode($matriz);
    } else {
      $json[0]['max_num_pages'] = null; 
      echo json_encode($json);  
    }
die();
}
add_action( 'wp_ajax_comments_fb', 'load_comments_fb' );
add_action( 'wp_ajax_nopriv_comments_fb', 'load_comments_fb' );

function menu_commets_fb() {
    $hook = add_menu_page(__('Comentário Fb','comentarios_fb'), __('Comentários','comentarios_fb'), 'manage_options', 'comentarios_fb', 'listar_comentarios_fb' );
    add_action( "load-$hook", 'add_options_fb' );
}
add_action('admin_menu', 'menu_commets_fb');

function add_options_fb() {
  global $listar_comentarios_instance;

  $option = 'per_page';
  $args = array(
         'label' => 'Comentários',
         'default' => 10,
         'option' => 'fb_comments_per_page'
         );
  add_screen_option( $option, $args );
  $listar_comentarios_instance = new List_Comments_Fb();
}


function listar_comentarios_fb(){
    global $listar_comentarios_instance;  
    echo '</pre><div class="wrap"><h2>Comentário via facebook</h2>';
    $listar_comentarios_instance->prepare_items(); 
    ?>
    <form method="post">
    <input type="hidden" name="page" value="test_list_table">
    <?php
    $listar_comentarios_instance->search_box( 'buscar', 'search_id' );
    echo '<div id="message"></div>';
    $listar_comentarios_instance->display(); 
    echo '</form></div>'; 
}

require_once __DIR__ . '/libs/Facebook/autoload.php';
function notice_includes_public() {
    //Estilos
    if(is_admin())  
        wp_enqueue_style( 'notice_css', NOTICE_URL .'/assets/css/style.css', array(), '1.0','all' );
    //Scripts
    wp_enqueue_script( 'jquery' );
    if(is_singular() || is_admin() ||is_post_type_archive('copa2014'))    
         wp_enqueue_script( 'functions-notice', NOTICE_URL .'/assets/js/functions.js', ('jquery'), rand(0,5), true );

    // Localize strings.
    wp_localize_script( 
      'functions-notice',
      'NoticeCont',
      array(
        'adminpath'=> admin_url(),
        'templatepath'=> get_template_directory_uri(),
        'pluginpath'=> NOTICE_PATH,
        'pluginurl'=> NOTICE_URL,
        'is_singular'=> is_single(),
        'is_home'=> is_home(),
        'home_url'=> get_bloginfo('url'),
        'current_page'=> $pagina = is_page() ? get_the_title(get_the_ID()) : '',
        'post'=> get_the_ID(),
        'nonce_del_fb'=> wp_create_nonce( get_the_ID().'-del_fb_comment' ),
        ) 
      );
}
add_action( 'wp_enqueue_scripts', 'notice_includes_public' );
add_action( 'admin_enqueue_scripts', 'notice_includes_public' );

//registrando custom post type
add_action( 'init', 'facebook_notice' );
function facebook_notice() {
    register_post_type( 'facebook',
        array(
            'labels' => array(
                'name' => __( 'Facebook' ),
                'singular_name' => __( 'Facebook')
            ),
        'public' => false,
        'has_archive' => true,
        'exclude_from_search' => false,
        'supports'=>array( 'title', 'editor', 'custom-fields'),
        )
    );
}

/**
 * Delete comente admin
 */

function delete_comment_fb_admin(){
    if(wp_delete_post( $_POST['post_id_fb'], true ))
       echo '<div id="message" class="updated fade"><p>Registro excluído com sucesso!</p></div>';
    else
       echo '<div id="message" class="updated fade"><p>Erro ao excluir registro!</p></div>';
    wp_die();
}
add_action( 'wp_ajax_FB_notice_comment_delete_admin', 'delete_comment_fb_admin' );


function teste_fb(){
    $fb = new Facebook\Facebook([
      'app_id' => '104968829952230',
      'app_secret' => '5b1c33ebdb5d38f469e944513cdf129e',
      'default_graph_version' => 'v2.5',
    ]);

    $helper = $fb->getCanvasHelper();
    var_dump($helper);
    try {
      $accessToken = $helper->getAccessToken();
    } catch(Facebook\Exceptions\FacebookResponseException $e) {
      // When Graph returns an error
      echo 'Graph returned an error: ' . $e->getMessage();
      exit;
    } catch(Facebook\Exceptions\FacebookSDKException $e) {
      // When validation fails or other local issues
      echo 'Facebook SDK returned an error: ' . $e->getMessage();
      exit;
    }

    if (! isset($accessToken)) {
      echo 'No OAuth data could be obtained from the signed request. User has not authorized your app yet.';
      exit;
    }

    // Logged in
    echo '<h3>Signed Request</h3>';
    var_dump($helper->getSignedRequest());

    echo '<h3>Access Token</h3>';
    var_dump($accessToken->getValue());
    //var_dump($fb->getUser());

    die();
}
add_action( 'wp_ajax_tfb', 'teste_fb' );
add_action( 'wp_ajax_nopriv_tfb', 'teste_fb' );