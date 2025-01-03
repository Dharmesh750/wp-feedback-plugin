<?php 
/**
 * Plugin Name: Custom User Feedback
 * Plugin URI: https://example.com/wp-user-feedback
 * Description: A Custom user feedback form ,which is sortable and Pagination.
 * Version: 1.0
 * Author: Test
 * Author URI: https://example.com/
 * Text-domain: wp-user-feedback
 * License: GPL2
 */

function user_feedback_menu(){
    global $user_feedback_page;
    $user_feedback_page = add_menu_page(__('WP User Feedback','wp-user-feedback'), __('WP Feedback','wp-user-feedback'),'manage_options', 'wp-user-feedback','wp_user_feedback', 10);
    
    add_action("load-$user_feedback_page","user_feedback_screen_option");
} 
add_action('admin_menu','user_feedback_menu');

function user_feedback_screen_option(){
    global $user_feedback_page;
    global $table;

    $screen = get_current_screen();
    if(!is_object($screen) || $screen->id != $user_feedback_page)
    return;

    $args = array(
        'label'=>__('Elements per page','wp-user-feedback'),
        'default'=> 2,
        'option' => 'elements_per_page'
    );
    add_screen_option('per_page',$args);

    $table = new feedback_list_table();
}


function wp_user_enqueue(){
    wp_enqueue_style('wp_user_css','https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous"');
    wp_enqueue_style('wp_user_custom_css',plugin_dir_url(__FILE__).'asset/style.css',array(),1.0,'all');
    wp_enqueue_script('wp_user_js','https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"');
    wp_enqueue_script('wp_user_custom_js',plugin_dir_url(__FILE__).'asset/script.js',array('jquery'),1.0,false);
    wp_localize_script('wp_user_custom_js','ajax_request',array('admin_url'=>admin_url('admin-ajax.php')));
}
add_action('wp_enqueue_scripts','wp_user_enqueue');

// WP List Table
if(!class_exists('WP_List_Table')){
    require_once (ABSPATH."wp-admin\includes\class-wp-list-table.php");
}
class feedback_list_table extends WP_List_Table {

    private $table_data;

    // Create table columns
    public function get_columns() { 
        $columns = array(
            'cb' => '<input type="checkbox" />',
            'name' => __('Username', 'wp-user-feedback'),
            'email' => __('E-Mail', 'wp-user-feedback'),
            'gender' => __('Gender', 'wp-user-feedback'),
            'feedback' => __('Message', 'wp-user-feedback')
        );
        return $columns;
    }

    public function prepare_items() {

        //data
        if ( isset($_POST['s']) ) {
            $this->table_data = $this->get_table_data($_POST['s']);
        } else {
            $this->table_data = $this->get_table_data();
        }
        $columns = $this->get_columns();
        $hidden = ( is_array(get_user_meta( get_current_user_id(), 'managetoplevel_page_WP_User_feedback_tablecolumnshidden', true)) ) ? get_user_meta( get_current_user_id(), 'managetoplevel_page_WP_User_feedback_tablecolumnshidden', true) : array();
        
        $sortable = $this->get_sortable_columns();
        $primary = 'name';
        $this->_column_headers = array($columns, $hidden, $sortable, $primary);
        
        usort($this->table_data, array(&$this, 'usort_reorder'));

         /* pagination */
         $per_page = $this->get_items_per_page('elements_per_page', 10);
         $current_page = $this->get_pagenum();
         $total_items = count($this->table_data);
 
         $this->table_data = array_slice($this->table_data, (($current_page - 1) * $per_page), $per_page);
 
         $this->set_pagination_args(array(
                 'total_items' => $total_items, // total number of items
                 'per_page'    => $per_page, // items to show on a page
                 'total_pages' => ceil( $total_items / $per_page ) // use ceil to round up
         ));
        $this->items = $this->table_data;
    }

    private function get_table_data($search='') {
        global $wpdb;
        $table = $wpdb->prefix . 'feedback'; 
        if(!empty($search)){
            return $wpdb->get_results("select * from $table where name LIKE'%$search%' OR email LIKE '%$search%' OR gender LIKE '%$search%'",ARRAY_A);
        }
        return $wpdb->get_results("SELECT * FROM $table", ARRAY_A);
    }

    public function column_default($item, $column_name) {
        switch($column_name) {
            case 'id':
            case 'name':
            case 'mail':
            case 'gender':
            case 'message':
            default:
                return $item[$column_name];
        }
    }
    function column_cb($item){
        return sprintf(
            '<input type="checkbox" name="element[]" value="%s"/>',$item['id']
        );
    }
    protected function get_sortable_columns(){
        $sortable_columns = array(
            'name' => array('name',false),
            'mail'=> array('mail',false),
            'gender'=>array('gender',false)
        );
        return $sortable_columns;
    }
    //sorting function
    function usort_reorder($a, $b)
    {
        $orderby = (!empty($_GET['orderby'])) ? $_GET['orderby'] : 'user_login';

        $order = (!empty($_GET['order'])) ? $_GET['order'] : 'asc';

        $result = strcmp($a[$orderby], $b[$orderby]);

        return ($order === 'asc') ? $result : -$result;
    }
    // Adding action links to column
    function column_name($item)
    {
        $actions = array(
                'edit'      => sprintf('<a href="?page=%s&action=%s&element=%s">' . __('Edit', 'wp-user-feedback') . '</a>', $_REQUEST['page'], 'edit', $item['ID']),
                'delete'    => sprintf('<a href="?page=%s&action=%s&element=%s">' . __('Delete', 'wp-user-feedback') . '</a>', $_REQUEST['page'], 'delete', $item['ID']),
        );

        return sprintf('%1$s %2$s', $item['name'], $this->row_actions($actions));
    }
     // To show bulk action dropdown
     function get_bulk_actions()
     {
             $actions = array(
                     'delete_all'    => __('Delete', 'wp-user-feedback'),
                     'draft_all' => __('Move to Draft', 'wp-user-feedback')
             );
             return $actions;
     }
    
    
}

function wp_user_feedback(){
    $list = new feedback_list_table();
    echo '<div class="wrap"><h2>User feedback</h2>';
    echo '<form method="post">';
    $list->prepare_items();
    // Search form
    $list->search_box('search', 'search_id');
    $list->display();
    echo '</div></form>';
}



add_shortcode('feedbackform','wp_user_form');
function wp_user_form(){
    ob_start();
    include_once "user.php";
    return ob_get_clean();
}
add_action('wp_ajax_user_data','user_data');
add_action('wp_ajax_nopriv_user_data','user_data');

function user_data(){
    if (isset($_POST['form'])) { 
    $form = $_POST['form'];
   $data_array = explode('&', $form);

$parsed_data = [];
foreach ($data_array as $item) {
  $key_value = explode('=', $item);
  $parsed_data[$key_value[0]] = urldecode($key_value[1]);
}

$username = $parsed_data['uname'];
$email = $parsed_data['uemail'];
$gender = $parsed_data['ugender'];
$message = $parsed_data['umsg'];

// Sanitize the data before using it!
$username = sanitize_text_field($username);
$email = sanitize_email($email);
$gender = sanitize_text_field($gender);
$message = sanitize_textarea_field($message);

    $response = "User Name: $username\nEmail: $email\nGender: $gender\nMessage: $message";
    error_log("User Data Received: $response");
    // echo $response;
    saveFeedback($username,$email,$gender,$message);
}
die();
}
function saveFeedback($user,$mail,$gender,$message){
    global $wpdb;
    $table = $wpdb->prefix.'feedback';

    $have_table = $wpdb->get_var( "Select COUNT(*) from information_schema.tables where table_schema='".DB_NAME."' and table_name='".$table."'");
    
    if($have_table==0){
        $query = "Create table $table(
            id bigint not null auto_increment,
            name varchar(255) not null,
            email varchar(255) not null,
            gender varchar(255) not null,
            feedback text not null,
            primary key(id)
            )";
            $wpdb->query($query);
    }
    $data = array(
        'name' => sanitize_text_field($user),
        'email' => sanitize_email($mail),
        'gender' => sanitize_text_field($gender),
        'feedback'=> sanitize_textarea_field($message),
    );
    $format =array(
        '%s',
        '%s',
        '%s',
        '%s',
    );
    $wpdb->insert($table,$data,$format);
    if ($wpdb->insert_id) {
        echo 'Thank you for your feedback!';
    } else {
        echo 'There was an issue submitting your feedback. Please try again.';
    }
die();
}

