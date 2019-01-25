<?php
//https://www.smashingmagazine.com/2011/11/native-admin-tables-wordpress/
class Links_List_Table extends WP_List_Table {
   /**
    * Constructor, we override the parent to pass our own arguments
    * We usually focus on three parameters: singular and plural labels, as well as whether the class supports AJAX.
    */
    function __construct() {
       parent::__construct( array(
      'singular'=> 'wp_list_text_link', //Singular label
      'plural' => 'wp_list_test_links', //plural label, also this well be one of the table css class
      'ajax'   => false //We won't support Ajax for this table
      ) );
    }

    function extra_tablenav( $which ){
      if( $which == "top" ){
        echo 'top';
      }
      if( $which == "bottom" ){
        echo 'bottom';
      }
    }

    function get_columns(){
      return $columns = array(
        'col_link_id'           => __('ID'),
        'col_link_question'     => __('Question'),
        'col_link_connection'   => __('Category'),
        'col_link_type'         => __('Type'),
      );
    }

    public function get_sortable_columns() {
       return $sortable = array(
          'col_link_id'         => 'id',
          'col_link_question'   => 'question',
          'col_link_connection' => 'connection_id',
          'col_link_type'       => 'type',
       );
    }

    function prepare_items(){
      global $wpdb, $_wp_column_headers;
      $screen = get_current_screen();
      $table = $wpdb->prefix . 'bwcr_features';

      $query = "SELECT * FROM $table";

      //Parameters that are going to be used to order the result
      $orderby = !empty($_GET["orderby"]) ? mysql_real_escape_string($_GET["orderby"]) : 'ASC';
      $order = !empty($_GET["order"]) ? mysql_real_escape_string($_GET["order"]) : '';
      if(!empty($orderby) & !empty($order)){ $query.=' ORDER BY '.$orderby.' '.$order; }

       /* -- Pagination parameters -- */
        //Number of elements in your table?
        $totalitems = $wpdb->query($query); //return the total number of affected rows
        //How many to display per page?
        $perpage = 5;
        //Which page is this?
        $paged = !empty($_GET["paged"]) ? mysql_real_escape_string($_GET["paged"]) : "";
        //Page Number
        $totalpages = ceil($totalitems/$perpage);

        if(empty($paged) || !is_numeric($paged) || $paged<=0 ){ $paged=1; } //How many pages do we have in total?  //adjust the query to take pagination into account if(!empty($paged) && !empty($perpage)){ $offset=($paged-1)*$perpage; $query.=' LIMIT '.(int)$offset.','.(int)$perpage; } /* -- Register the pagination -- */
          $this->set_pagination_args( array(
           "total_items" => $totalitems,
           "total_pages" => $totalpages,
           "per_page" => $perpage,
         ) );
      //The pagination links are automatically built according to those parameters

         /* -- Register the Columns -- */
            $columns = $this->get_columns();
            $_wp_column_headers[$screen->id]=$columns;

         /* -- Fetch the items -- */
            $this->items = $wpdb->get_results($query);
  }

  /**
 * Display the rows of records in the table
 * @return string, echo the markup of the rows
 */
function display_rows() {

   //Get the records registered in the prepare_items method
   $records = $this->items;

   //Get the columns registered in the get_columns and get_sortable_columns methods
   list( $columns, $hidden ) = $this->get_column_info();
   pre_dump( $columns );
   //Loop for each record
   if(!empty($records)){
     foreach($records as $rec){

      //Open the line
        echo '<tr id="record_'.$rec->id.'">';
      foreach ( $columns as $column_name => $column_display_name ) {
         //Style attributes for each col
         $class = "class='$column_name column-$column_name'";
         $style = "";
         if ( in_array( $column_name, $hidden ) ) $style = ' style="display:none;"';
         $attributes = $class . $style;

         //edit link
         $editlink  = '/wp-admin/edit-comments.php?page=bwcr-menu&action=edit&link_id='.(int)$rec->link_id;

         //Display the cell
         switch ( $column_name ) {
            case "col_link_id":  echo '< td '.$attributes.'>'.stripslashes($rec->id).'< /td>';   break;
            case "col_link_question": echo '< td '.$attributes.'>'.stripslashes($rec->question).'< /td>'; break;
            case "col_link_connection": echo '< td '.$attributes.'>'.stripslashes($rec->connection_id).'< /td>'; break;
            case "col_link_type": echo '< td '.$attributes.'>'.$rec->type.'< /td>'; break;
            case "col_link_edit": echo '< td '.$attributes.'>'.$rec->created.'< /td>'; break;
         }
      }

      //Close the line
      echo'</tr>';
   }}
}

}
