<?php

if (!defined('ABSPATH'))
exit;

if (!class_exists('OCPF_shortcode')) {
    class OCPF_shortcode {

        protected static $instance;

        function OCPF_table($atts, $content = null) {
            ob_start();  
            extract(shortcode_atts(array(
                'id' => '',
            ), $atts));


            if('publish' != get_post_status( $id )){
                return false;
            }

            $table_clm = get_post_meta( $id, 'ocpf_tbl_clm', true );
            $arr = explode(",", $table_clm);
            $arr_foot = explode(",", $table_clm);
            $multi_add = get_post_meta( $id, 'ocpf_mul_add_cart', true );

            ?>
                <div class="ocpf_table_main_div woocommerce">
                    <?php
                        if($multi_add == 'yes'){
                            array_unshift($arr,'<input type="checkbox" name="select_all" value="1" id="example-select-all" class="example-select-all">');
                            ?>
                            <div class="add_multiple">
                                <a class="button" id="add_mul">Add To Cart</a>
                            </div>
                            <?php
                        }

                        $default_filter = get_post_meta( $id, 'ocpf_filter', true );
                        $default_filter = explode(",",$default_filter);
                        //print_r($default_filter);
                        if(!empty($default_filter)) { 
                            ?>
                                <table class="filter_tbl">
                                    <thead>
                                        <tr>
                                            <td>
                                                <?php
                                                    foreach ($default_filter as $default_filters) {
                                                        if($default_filters == "price"){
                                                            ?>
                                                                <div>
                                                                  <input type="number" name="min" value="" id="min" class="price_filter" placeholder="Min">
                                                                </div>
                                                                <div>
                                                                  <input type="number" name="max" value="" id="max" class="price_filter" placeholder="Max">
                                                                </div>
                                                            <?php
                                                        }else{
                                                            ?>
                                                                <div>
                                                                    <?php if(!empty($default_filters)) { ?>
                                                                    <select name="<?php echo $default_filters; ?>" id="<?php echo $default_filters; ?>" class="filter_ft">
                                                                    <?php $attr_terms = get_terms( $default_filters );
                                                                        echo '<option value="">Choose '.str_replace(array('product_cat','pa_'), array('Category',' '), $default_filters).'</option>';
                                                                        foreach ($attr_terms as $terms) {
                                                                          ?>
                                                                          <option value="<?php echo $terms->slug; ?>"><?php echo $terms->name; ?></option>
                                                                          <?php
                                                                        }
                                                                    ?>
                                                                    </select>
                                                                    <?php } ?>
                                                                </div>
                                                            <?php
                                                        }
                                                    }
                                                ?>
                                            </td>
                                        </tr>
                                    </thead>       
                                </table>
                            <?php 
                        } 
                    ?>
                    <table class="display responsive nowrap <?php if($multi_add == yes){ echo "chk_tbl" ;} ?>" id="pro_table" data-id="<?php echo $id; ?>" style="width:100%">
                        <thead>
                            <tr>
                                <?php 
                                    foreach ($arr as $value) {
                                        echo '<th>'.str_replace('_', ' ', $value).'</th>';
                                    }
                                ?>
                            </tr>
                        </thead> 
                        <tbody>
                        	
                        </tbody>
                        <tfoot>
                            <tr> 
                                <?php
                                    if($multi_add == 'yes'){
                                        ?>
                                            <th></th>
                                        <?php
                                    }
                                ?>            
                                <?php 
                                    foreach ($arr_foot as $value) {
                                        echo '<th>'.str_replace('_', ' ', $value).'</th>';
                                    }
                                ?>
                            </tr>
                        </tfoot>      
                    </table>
                </div>
            <?php
            return $var = ob_get_clean();
        }

        function datatables_server_side_callback() {
            //echo "hello";
            header("Content-Type: application/json");
 
            $request= $_GET;
            //print_r($request);
            $id = sanitize_text_field($request['pid']);

            //default display column
            $table_clm = get_post_meta( $id, 'ocpf_tbl_clm', true );
            $arr = explode(",", $table_clm);
      
            // $columns = array();
            // foreach ($arr as $key => $value) {
            //  array_push($columns,$value);
            // }

            //multiple add to cart
            $multi_add = get_post_meta( $id, 'ocpf_mul_add_cart', true );
            if($multi_add == yes){
                array_unshift($arr,"multi");
                //array_unshift($columns,"multi");
            }
    

            //default display table texomony wise
            $default_display = get_post_meta( $id, 'ocpf_default_display', true );
            $default_display_cat = get_post_meta( $id, 'ocpf_default', true );
            $cat = explode(",",$default_display_cat);
            $args = array(
                'post_type' => array('product', 'product_variation'),
                'post_status' => 'publish',
                'posts_per_page' => sanitize_text_field($request['length']),
                'offset' => sanitize_text_field($request['start']),
                'order' => sanitize_text_field($request['order'][0]['dir']),
            );

            $args['tax_query'] =array(
                'relation' => 'AND'
            );

            if(!empty($default_display)){
                if(!empty($default_display_cat)){
                    $args['tax_query'][]=array(
                        'taxonomy' => $default_display,
                        'field'    => 'slug',
                        'terms'    => $cat,
                        'operator' => 'IN',
                    );
                }else{
                    $orderby = 'name';
                    $order = 'asc';
                    $hide_empty = false;
                    $cat_args = array(
                        'orderby'    => $orderby,
                        'order'      => $order,
                        'hide_empty' => $hide_empty,
                        'parent' => 0,
                    );
                    $not_select_default = array();
                    $ocpf_categories = get_terms( $default_display, $cat_args );
                    foreach( $ocpf_categories as $ocpf_category ) {
                        array_push($not_select_default,$ocpf_category->slug);
                    }
                    $args['tax_query'][]=array(
                        'taxonomy' => $default_display,
                        'field'    => 'slug',
                        'terms'    => $not_select_default,
                        'operator' => 'IN',
                    );
                }
            }


            //print_r($request);
            //asc and desc order
            // if ($request['order'][0]['column'] == 2) {
            //   $args['orderby'] = $columns[$request['order'][0]['column']];
            // } else
            // if (in_array("review", $arr)) { 
            //   if ($request['order'][0]['column'] == 4 ) {
            //     $args['orderby'] = 'meta_value_num';
            //     $args['meta_key'] = "_wc_average_rating";
            //   } 
            // }

            //search
            if( !empty($request['search']['value']) ) { // When datatables search is used
                $args['s'] = sanitize_text_field($request['search']['value']);
            }

            //filter taxonomy
            $sel = $this->recursive_sanitize_text_field( $request['sel'] );      
            $fil_val = $this->recursive_sanitize_text_field( $request['fil_val'] );
            // print_r($fil_val);

            foreach ($fil_val as $key => $value) {
                if(!empty($value)){
                    // echo "string";
                    $args['tax_query'][]=array(
                        'taxonomy' => $sel[$key],
                        'field'    => 'slug',
                        'terms'    => $value,
                        'operator' => 'IN',
                    );
                }
            }

            //price filter
            $min = sanitize_text_field($request['min']);      
            $max = sanitize_text_field($request['max']);
            if(!empty($min) && !empty($max)){
                $args['meta_query'] = array(
                    array(
                        'key' => '_price',
                        'value' => array( $min, $max ),
                        'compare' => 'BETWEEN',
                        'type'=> 'NUMERIC'
                    )
                );
            }elseif (!empty($min) && empty($max)) {
                $args['meta_query'] = array(
                    array(
                        'key' => '_price',
                        'value' => $min,
                        'compare' => '>=',
                        'type'=> 'NUMERIC'
                    )
                );
            }elseif(empty($min) && !empty($max)) {
                $args['meta_query'] = array(
                    array(
                        'key' => '_price',
                        'value' => $max,
                        'compare' => '<=',
                        'type'=> 'NUMERIC'
                    )
                );
            }
            //print_r($args);
            //exit();

            $movie_query = new WP_Query($args);
            //print_r($movie_query);
            $totalData = $movie_query->found_posts;
            if ( $movie_query->have_posts() ) {
                while ( $movie_query->have_posts() ) {
                    $movie_query->the_post();
                    
                    global $product,$post;
                    $rating_count = $product->get_rating_count();
                    $review_count = $product->get_review_count();
                    $average      = $product->get_average_rating();
                    $stock = "";

                    if($product->get_stock_status() == "outofstock"){
                        $stock = " (outofstock)";
                    }

                    $nestedData = array();
                    
                    $the_id = $movie_query->post->post_parent > 0 ? $movie_query->post->post_parent : $movie_query->post->ID;
                    $cat_name = array();
                    $terms = get_the_terms ( $the_id, 'product_cat' );
                    foreach ( $terms as $term ) {
                        $cat_name[] = $term->name;
                    }
                    $cat_h = implode(",",$cat_name);
                    

                    if( ! $product->is_type('variable') ){
                        foreach ($arr as  $value) {
                            if($value == "multi") {
                                $nestedData[] = '<input type="checkbox" name="id[]" value="'.$product->get_id().'" class="ids">';
                            }
                            if($value == "thumbnail") {
                                $nestedData[] = '<div class="product_img_div">'.$product->get_image().'</div>';
                            }
                            if($value == "name") {
                                $nestedData[] = '<a href="'.get_permalink( $product->get_id() ).'">'.get_the_title().'</a></br>'.$stock;
                            }
                            if($value == "price") {
                                $nestedData[] = $product->get_price_html();
                            }
                            if($value == "sku") {
                                if (!empty($product->get_sku())){
                                    $nestedData[] = $product->get_sku();
                                }else{
                                    $nestedData[] = "-";
                                }
                            }
                            if($value == "category") {
                                $nestedData[] = $cat_h;
                            }
                            if($value == "review") {
                                if ( $rating_count > 0 ) {
                                    $nestedData[] = '<div class="woocommerce-product-rating">'.wc_get_rating_html( $average, $rating_count ).'</div><span>('. $product->get_review_count() .')</span>';
                                }else{
                                    $nestedData[] = '<div class="star-rating no_rated"></div>';
                                }
                            }
                            if($value == "add_cart"){
                                if($product->get_stock_status() == "outofstock"){
                                    $cl = "disabled";
                                }else{
                                    $cl = "";
                                }
                                $nestedData[] = '<input type="number" class="qty_box" min="1" value="1" '.$cl.'><a class="button single_add '.$cl.'" id="single_add" pids="'.$product->get_id().'"><img src="'. OCPF_PLUGIN_DIR .'/includes/images/shopping-cart.png"></a>';
                            }
                        }
                    }else {
                        foreach ($arr as  $value) {
                            if($value == "multi"){
                                $nestedData[] = '<input type="checkbox" name="id[]" value="">';
                            }
                            if($value == "thumbnail"){
                                $nestedData[] = '<div class="product_img_div">'.$product->get_image().'</div>';
                            }
                            if($value == "name"){
                                $nestedData[] = '<a href="'.get_permalink( $product->get_id() ).'">'.get_the_title().'</a>';
                            }
                            if($value == "price"){
                                $nestedData[] = $product->get_price_html();
                            }
                            if($value == "sku"){
                                if (!empty($product->get_sku())){
                                    $nestedData[] = $product->get_sku();
                                }else{
                                    $nestedData[] = "-";
                                }
                            }
                            if($value == "category"){
                                $nestedData[] = $cat_h;
                            }
                            if($value == "review"){
                                if ( $rating_count > 0 ){
                                    $nestedData[] = '<div class="woocommerce-product-rating">'.wc_get_rating_html( $average, $rating_count ).'</div><span>('. $product->get_review_count() .')</span>';
                                }else{
                                    $nestedData[] = '<div class="star-rating no_rated"></div>';
                                }
                            }
                            if($value == "add_cart"){
                                $nestedData[] = '<a class="button" href="'.get_permalink( $product->get_id() ).'" id="view_link" target="_blank"><img src="'. OCPF_PLUGIN_DIR .'/includes/images/eye.png"></a>';
                            }
                        }
                    }
                    $data[] = $nestedData;
                    
                }
   
                wp_reset_query();
                $json_data = array(
                    "draw" => intval($request['draw']),
                    "recordsTotal" => intval($totalData),
                    "recordsFiltered" => intval($totalData),
                    "data" => $data
                );
                echo json_encode($json_data);
            }else {
                $json_data = array(
                    "data" => array()
                );
                echo json_encode($json_data);
            }
            wp_die();
        }
        
        function woocommerce_ajax_add_to_cart() {
            $ids = $this->recursive_sanitize_text_field($_REQUEST['product_id']);
            foreach ($ids as $key => $value) {
                WC()->cart->add_to_cart( $key, $value);
            }
            exit();
        }

        function woocommerce_ajax_add_to_cart_single() {
            $ids = sanitize_text_field($_REQUEST['product_id']);
            $qty = sanitize_text_field($_REQUEST['qty']);
            
            WC()->cart->add_to_cart( $ids, $qty);
            exit();
        }


        function recursive_sanitize_text_field($array) {
            foreach ( $array as $key => &$value ) {
                if ( is_array( $value ) ) {
                    $value = $this->recursive_sanitize_text_field($value);
                }else{
                    $value = sanitize_text_field( $value );
                }
            }
            return $array;
        }


        function init() {
            add_shortcode( 'ocpf-Product-table', array($this,'OCPF_table'));
            add_action('wp_ajax_product_datatables', array($this,'datatables_server_side_callback'));
            add_action('wp_ajax_nopriv_product_datatables', array($this,'datatables_server_side_callback'));
            add_action('wp_ajax_woocommerce_ajax_add_to_cart', array($this,'woocommerce_ajax_add_to_cart'));
            add_action('wp_ajax_nopriv_woocommerce_ajax_add_to_cart', array($this,'woocommerce_ajax_add_to_cart'));
            add_action('wp_ajax_woocommerce_ajax_add_to_cart_single', array($this,'woocommerce_ajax_add_to_cart_single'));
            add_action('wp_ajax_nopriv_woocommerce_ajax_add_to_cart_single', array($this,'woocommerce_ajax_add_to_cart_single'));
        }


        public static function instance() {
            if (!isset(self::$instance)) {
                self::$instance = new self();
                self::$instance->init();
            }
            return self::$instance;
        }
    }
    OCPF_shortcode::instance();
}

