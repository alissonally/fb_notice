<?php
if( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}
class List_Comments_Fb extends WP_List_Table {

	var $data_comments_fb = array();
	function __construct(){
		global $status, $page;

		parent::__construct( array(
            'singular'  => __( 'Comentário Fb', 'odin' ),     //singular name of the listed records
            'plural'    => __( 'Comentários Fb', 'odin' ),   //plural name of the listed records
            'ajax'      => true        //does this table support ajax?

            ) );
		add_action( 'admin_footer', array( &$this, 'admin_functions_scripts' ) );            
		add_action( 'admin_head', array( &$this, 'admin_header' ) );            
	}

	public function admin_header() {
		$page = ( isset($_GET['page'] ) ) ? esc_attr( $_GET['page'] ) : false;
		if( 'comentarios_fb' != $page )
			return;
		echo '<style type="text/css">';
		echo '.wp-list-table .column-id { width: 5%; }';
		echo '.wp-list-table .column-autor { width: 25%; }';
		echo '.wp-list-table .column-autor img { float:left; margin:0  15px 0 0 }';
		echo '.wp-list-table .column-comentario { width: 50%; }';
		echo '.wp-list-table .column-em_reposta { width: 20%;}';
		echo '</style>';
	}

	public function admin_functions_scripts(){
		//wp_enqueue_script( 'functions-admin-functions', get_template_directory_uri().'/js/function_admin.js', ('jquery'), '0.1.1', true );
	}

	public function no_items() {
		_e( 'Nenhum comentário encontrado.' );
	}

	public function column_default( $item, $column_name ) {
		switch( $column_name ) { 
			case 'autor':
			case 'comentario':
			case 'em_resposta':
			return $item[ $column_name ];
			default:
	            return print_r( $item, true ) ; //Show the whole array for troubleshooting purposes
	        }
	    }

	public function get_sortable_columns() {//campos para ordernar
		$sortable_columns = array(
			'autor'  => array('post_title',false),
			//'comentario' => array('comentario',false)
			);
		return $sortable_columns;
	}
	
	function get_columns(){//Cria as colunas
		$columns = array(
			'cb'        => '<input type="checkbox" />',
			'autor' => __( 'Autor', 'odin' ),
			'comentario'    => __( 'Comentário', 'odin' ),
			'em_resposta'    => __( 'Em resposta à', 'odin' ),
			);
		return $columns;
	}

	public function usort_reorder( $a, $b ) {
		// If no sort, default to title
		$orderby = ( ! empty( $_GET['orderby'] ) ) ? $_GET['orderby'] : 'post_title';
		// If no order, default to asc
		$order = ( ! empty($_GET['order'] ) ) ? $_GET['order'] : 'asc';
		// Determine sort order
		$result = strcmp( $a[$orderby], $b[$orderby] );
		// Send final sort direction to usort
		return ( $order === 'asc' ) ? $result : -$result;
	}

	public function column_comentario($item){	
		$actions = array(
			//'edit'      => sprintf('<a href="?page=%s&galeria=%s">Editar</a>',$_REQUEST['page'],$item['ID']),
			'delete'    => sprintf('<a href="javascript:void(0)" data-idpost="%s" data-message="Tem certeza excluir o comentário de %s">Delete</a>',$item['ID'], $item['author_name']),
			);

		return sprintf('%1$s %2$s', $item['comentario'], $this->row_actions($actions) );
	}

	public function get_bulk_actions() {
		$actions = array(
			'delete'    => 'Delete'
			);
		return $actions;
	}

	public function column_cb($item) {
		return sprintf(
			'<input type="checkbox" name="cometarios_fb[]" value="%s" />', $item['ID']
			);    
	}

	public function prepare_items() {
		$paged =  isset($_GET['paged']) ? $_GET['paged'] : 1;
		$data = new WP_Query(array('post_type'=>'facebook','post_status'=>'draft','paged'=>$paged));
		global $post;
		foreach ($data->posts as $post) {
			setup_postdata($post);
			$data_fb = unserialize(get_post_meta(get_the_ID(), 'dados_fb', true ));
			$this->data_comments_fb[] = array(
				'ID'=>get_the_ID(),
				'autor' => $this->get_authors_comment_fb(get_the_title(), $data_fb),
				'comentario' => get_the_content(),
				'em_resposta' => '<a href="'.get_permalink($data_fb['comment_post_ID']).'" target="_blank" title="Ver Post">'.get_the_title($data_fb['comment_post_ID']).'</a>',                   
				'author_name' => get_the_title(),
				);   
			//author column-author
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


	public function get_authors_comment_fb ($nome, $dados){
		return sprintf('<a href="http://fb.com/%1$s" target="_black">
					<img alt="" src="http://graph.facebook.com/%1$s/picture?height=60&type=normal&width=60" alt="Foto do perfil" class="avatar avatar-50 photo">%2$s<br></a> 
					IP: %3$s<br>
					<a href="mailto:%4$s">%4$s</a>', 
					$dados['uid'], 
					$nome, 
					$dados['comment_author_IP'], 
					$dados['comment_author_email'] 
				);
	}


	// public function delete_comment_FB_admin(){
	//         var_dump($_POST);
	//         if(wp_delete_post( $_POST['post_id_fb'], true ))
	//            echo '<div id="message" class="updated fade"><p>Registro excluído com sucesso!</p></div>';
	// 		else
	// 		   echo '<div id="message" class="updated fade"><p>Erro ao excluir registro!</p></div>';
	//     	die();
	// }

} //class
