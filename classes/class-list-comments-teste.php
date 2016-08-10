<?php

if( ! class_exists( 'WP_Comments_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-comments-list-table.php' );
}


class lista_comenentarios extends WP_Comments_List_Table{


	public function __construct(){
		parent::__construct(
			array(
				'plural' => 'comments_fb',
				'singular' => 'comment_fb',
				'ajax' => true,
				'screen' => isset( $args['screen'] ) ? $args['screen'] : null,	
				)
			);
	}

	public function column_comment($post){

		echo '<div class="comment-author">';
        	$this->column_author( $post );
    	echo '</div>';
	}
	public function column_author($post){
		global $comment_status;
 
         $author_url = get_comment_author_url();
         if ( 'http://' == $author_url )
             $author_url = '';
         $author_url_display = preg_replace( '|http://(www\.)?|i', '', $author_url );
         if ( strlen( $author_url_display ) > 50 )
             $author_url_display = substr( $author_url_display, 0, 49 ) . '&hellip;';
 
         echo "<strong>"; comment_author(); echo '</strong><br />';
        if ( !empty( $author_url ) )
             echo "<a title='$author_url' href='$author_url'>$author_url_display</a><br />";
 
         if ( $this->user_can ) {
             if ( !empty( $comment->comment_author_email ) ) {
                 comment_author_email_link();
                 echo '<br />';
             }
             echo '<a href="edit-comments.php?s=';
             comment_author_IP();
             echo '&amp;mode=detail';
             if ( 'spam' == $comment_status )
                 echo '&amp;comment_status=spam';
             echo '">';
             comment_author_IP();
             echo '</a>';
        }
	}


	public function prepare_items() {
		$paged =  isset($_GET['paged']) ? $_GET['paged'] : 1;
		$data = new WP_Query(array('post_type'=>'facebook','post_status'=>'draft','paged'=>$paged));
		global $post;
		foreach ($data->posts as $post) {
			setup_postdata($post);
			$data_fb = unserialize(get_post_meta(get_the_ID(), 'dados_fb', true ));
			$this->data_comments_fb[] = array(
				'cb'=>get_the_ID(),
				'author' => get_the_title(),
				'comment' => get_the_content(),
				'response' => '<a href="'.get_permalink($data_fb['comment_post_ID']).'" target="_blank" title="Ver Post">'.get_the_title($data_fb['comment_post_ID']).'</a>',                   
				);   
		}	
		$columns  = $this->get_columns();
		$hidden   = array();
		$sortable = $this->get_sortable_columns();
		$this->_column_headers = array( $columns, $hidden, $sortable );
		usort( $this->data_comments_fb, array( &$this, 'usort_reorder' ) );

		$per_page = get_option( 'posts_per_page' );
		$current_page = $this->get_pagenum();
		$total_items = $data->found_posts;

		// only ncessary because we have sample data
		//$this->found_data = array_slice( $this->data_comments_fb,( ( $current_page-1 )* $per_page ), $per_page );

		$this->set_pagination_args( array(
			'total_items' => $total_items,                  //WE have to calculate the total number of items
			'per_page'    => $per_page                     //WE have to determine how many items to show on a page
			) );
		$this->items = $this->data_comments_fb;
	}
}


function tests(){
	global $testes;
	$testes->display(); 

}


function menu_commets_teste() {
	$hook = add_menu_page(__('Teste','menu_teste'), __('Teste','menu_teste'), 'manage_options', 'menu_teste', 'tests' );
	add_action( "load-$hook", 'add_options_menu_teste' );
}
add_action('admin_menu', 'menu_commets_teste');

function add_options_menu_teste() {
	global $testes;	
	$option = 'per_page';
	$args = array(
		'label' => 'Teste',
		'default' => 10,
		'option' => 'fb_comments_per_page'
		);
	add_screen_option( $option, $args );
	$testes = new WP_Comments_List_Table();
}