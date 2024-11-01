<?php

if (!defined('ABSPATH'))
  exit;

if (!class_exists('OCPF_menu')) {

   class OCPF_menu {

      protected static $instance;
     
      function OCPF_create_menu() {
         $post_type = 'product_table';
         $singular_name = 'Product Table';
         $plural_name = 'Product Table';
         $slug = 'product_table';
         $labels = array(
            'name'               => _x( $plural_name, 'post type general name', 'ocpf' ),
            'singular_name'      => _x( $singular_name, 'post type singular name', 'ocpf' ),
            'menu_name'          => _x( $singular_name, 'admin menu name', 'ocpf' ),
            'name_admin_bar'     => _x( $singular_name, 'add new name on admin bar', 'ocpf' ),
            'add_new'            => __( 'Add New', 'ocpf' ),
            'add_new_item'       => __( 'Add New '.$singular_name, 'ocpf' ),
            'new_item'           => __( 'New '.$singular_name, 'ocpf' ),
            'edit_item'          => __( 'Edit '.$singular_name, 'ocpf' ),
            'view_item'          => __( 'View '.$singular_name, 'ocpf' ),
            'all_items'          => __( 'All '.$plural_name, 'ocpf' ),
            'search_items'       => __( 'Search '.$plural_name, 'ocpf' ),
            'parent_item_colon'  => __( 'Parent '.$plural_name.':', 'ocpf' ),
            'not_found'          => __( 'No Table found.', 'ocpf' ),
            'not_found_in_trash' => __( 'No Table found in Trash.', 'ocpf' )
         );

         $args = array(
            'labels'             => $labels,
            'description'        => __( 'Description.', 'ocpc' ),
            'public'             => false,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => array( 'slug' => $slug ),
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => null,
            'supports'           => array( 'title' ),
            'menu_icon'          => 'dashicons-editor-table'
         );
         register_post_type( $post_type, $args );
      }

      
      function OCPF_add_meta_box() {
         add_meta_box(
            'OCPF_metabox',
            __( 'Table Settings', 'ocpc' ),
            array($this, 'OCPF_metabox_cb'),
            'product_table',
            'normal'
         );
      }


      function OCPF_metabox_cb( $post ) {
         // Add a nonce field so we can check for it later.
         wp_nonce_field( 'OCPF_meta_save', 'OCPF_meta_save_nounce' );
         ?> 
         <div class="ocpf-container">
            <div class="ocpf_shortcode">
               <span><?php echo __( 'Shortcode:', OCPF_DOMAIN );?></span>
               <input type="text" id="ocpf-selectdata_<?php echo $post_id;?>" value="[ocpf-Product-table id=<?php echo $post->ID;?>]" size="30" onclick="ocpf_select_data(this.id)" readonly>
            </div>
            <ul class="tabs">
               <li class="tab-link current" data-tab="tab-default">
                  <?php echo __( 'Default Settings', OCPF_DOMAIN );?>
               </li>
               <li class="tab-link" data-tab="tab-general">
                  <?php echo __( 'Filter Settings', OCPF_DOMAIN );?>
               </li>
               <li class="tab-link" data-tab="tab-data">
                  <?php echo __( 'Field Settings', OCPF_DOMAIN );?>
               </li>
            </ul>
            <div id="tab-default" class="tab-content current">
                  <h3><?php echo __( "Default Display Product", OCPF_DOMAIN );?></h3>

                  <div class="child_div">
                     <p>(Note: Press and hold Ctrl Key and select more than one item from the list.)</p>
                     <p>If you are not select any option in dropdown then it will display all option wise product</p>
                     <?php 
                        //for all taxonomy
                        global $product;
                        $args = array(
                          'public'   => true,
                          '_builtin' => false
                        ); 
                        $output = 'name'; // or objects
                        $operator = 'and'; // 'and' or 'or'
                        $taxonomies = get_taxonomies( $args, $output, $operator );
                        if ( $taxonomies ) {
                           foreach ( $taxonomies  as $key => $taxonomy ) {
                              if($taxonomy->name != "product_shipping_class"){
                                 $slug = $taxonomy->name;
                                 ?>
                                 <div class="ocpf_tax_div">
                                    <label>
                                       <input type="radio" name="ocpf_default_display" value="<?php echo $taxonomy->name; ?>"  <?php if(get_post_meta( $post->ID, 'ocpf_default_display', true ) == $taxonomy->name ){echo "checked";} ?>><?php echo $taxonomy->label; ?>
                                    </label>
                                 
                                    <?php
                                       $orderby = 'name';
                                       $order = 'asc';
                                       $hide_empty = false;
                                       $cat_args = array(
                                           'orderby'    => $orderby,
                                           'order'      => $order,
                                           'hide_empty' => $hide_empty,
                                           'parent' => 0,
                                       );
                                       
                                       $ocpf_categories = get_terms( $slug, $cat_args );
                                    ?>
                                    <?php $val_default = explode(",",get_post_meta( $post->ID, 'ocpf_default', true ));?>
                                    <select multiple name="ocpf_product_categories[][<?php echo $taxonomy->name; ?>]" class="ocpf_tax_sel" tax_name="<?php echo $taxonomy->name; ?>" style="<?php if(get_post_meta( $post->ID, 'ocpf_default_display', true ) == $taxonomy->name ){ ?> display: block; <?php } ?>" >
                                       <?php
                                       foreach( $ocpf_categories as $ocpf_category ) {
                                       ?>
                                          <option value="<?php echo $ocpf_category->slug;?>" <?php if(in_array( $ocpf_category->slug ,$val_default)){echo "selected";} ?>><?php echo $ocpf_category->name;?></option>
                                       <?php } ?>
                                    </select>
                                 </div>
                                 <?php
                              }
                           }
                        }
                     
                        //for all attribute
                     
                        $variations = wc_get_attribute_taxonomies(); 
                        foreach( $variations as $variation ) {
                           ?>
                           <div class="ocpf_tax_div">
                              <label>
                                 <input type="radio" name="ocpf_default_display" value="pa_<?php echo $variation->attribute_name; ?>" <?php if(get_post_meta( $post->ID, 'ocpf_default_display', true ) == "pa_".$variation->attribute_name ){echo "checked";} ?>>
                                 <?php echo $variation->attribute_label; ?>
                              </label>
                              <?php 
                                    $attr_slug = "pa_".$variation->attribute_name;
                                    $terms = get_terms($attr_slug); 
                                   // print_r($terms);  
                              ?>
                              <select multiple name="ocpf_product_categories[][<?php echo $attr_slug; ?>]" class="ocpf_tax_sel" tax_name="<?php echo $attr_slug; ?>" style="<?php if(get_post_meta( $post->ID, 'ocpf_default_display', true ) == $attr_slug ){ ?> display: block; <?php } ?>" >
                                       <?php
                                       foreach( $terms as $ocpf_terms ) {
                                       ?>
                                          <option value="<?php echo $ocpf_terms->slug;?>" <?php if(in_array( $ocpf_terms->slug ,$val_default)){echo "selected";} ?>><?php echo $ocpf_terms->name;?></option>
                                       <?php } ?>
                              </select>
                           </div>
                           <?php 
                        } 
                     ?>
                  </div>
            </div>
            <div id="tab-general" class="tab-content">
               <h3><?php echo __( "Filter Option", OCPF_DOMAIN );?></h3>
               <?php $val_fil = explode(",",get_post_meta( $post->ID, 'ocpf_filter', true )); ?>
               <div class="child_div">
                  <p>(Note: Press and hold Ctrl Key and select more than one item from the list.)</p>
                  <select multiple name="ocpf_filter[]">
                     <option value=""> 
                       <?php echo __( '--- Select Filter ----', OCPF_DOMAIN );?> 
                     </option>
                     <option value="product_cat" <?php if(in_array("product_cat" ,$val_fil)){echo "selected";} ?>>Category</option>
                     <option value="price" <?php if(in_array("price" ,$val_fil)){echo "selected";} ?>>Price</option>
                     <?php 
                       $attribute_taxonomies = wc_get_attribute_taxonomies();
                       if ( $attribute_taxonomies ) :
                         foreach ($attribute_taxonomies as $tax) :
                           if (taxonomy_exists(wc_attribute_taxonomy_name($tax->attribute_name))) :
                             
                             ?>
                             <option value="pa_<?php echo $tax->attribute_name ?>" <?php if(in_array("pa_".$tax->attribute_name ,$val_fil)){echo "selected";} ?>><?php echo $tax->attribute_label; ?></option>
                             <?php
                           endif;
                         endforeach;
                       endif;
                     ?>
                  </select>
               </div>
            </div>
            <div id="tab-data" class="tab-content">
               <div class="child_div">
                  <h3><?php echo __( "Table Field Option", OCPF_DOMAIN );?></h3>
                  <?php 

                     $ocpf_tbl_clm_db = "thumbnail,name,price,sku,category,review,add_cart";
                     $val_all_clm = explode(",",$ocpf_tbl_clm_db);

                     $val_clm = explode(",",get_post_meta( $post->ID, 'ocpf_tbl_clm', true ));
                     $valcuts2 = array();

                     foreach ($val_all_clm as $keya => $valuea) {
                        if (in_array($valuea, $val_clm)){
                           //$valcuts1[]=$valuea;
                        }else{
                           $valcuts2[]=$valuea;
                        }
                     }
                  ?>
                  <ul id="sortable">
                     <?php foreach ($val_clm as $clm) {
                        if(!empty($clm)){
                        ?>
                        
                           <li class="ui-state-default" id="<?php echo $clm; ?>">
                              <span class="ui-icon ui-icon-arrowthick-2-n-s"></span>
                              <input type="checkbox" name="ocpf_tbl_clm[]" value="<?php echo $clm; ?>" <?php if(in_array($clm ,$val_clm)){echo "checked";} ?>><?php echo str_replace('_', ' ', $clm); ?>
                           </li>
                        
                        <?php }
                     }
                     foreach ($valcuts2 as $clm) { ?>
                        
                           <li class="ui-state-default" id="<?php echo $clm; ?>">
                              <span class="ui-icon ui-icon-arrowthick-2-n-s"></span>
                              <input type="checkbox" name="ocpf_tbl_clm[]" value="<?php echo $clm; ?>" <?php if(in_array($clm ,$val_clm)){echo "checked";} ?>><?php echo str_replace('_', ' ', $clm); ?>
                           </li>
                        
                     <?php } ?>
                  </ul>
                  <p class="ocpf-tips"><?php echo __( "If Not Selected Any Field Then display all Field", OCPF_DOMAIN );?></p>

                  <h3>Add Multiple Product Add To Cart Button</h3>

                  <input type="checkbox" name="ocpf_mul_add_cart" value="yes" <?php if(get_post_meta( $post->ID, 'ocpf_mul_add_cart', true ) == 'yes'){echo "checked";} ?>>

                  <p class="ocpf-tips inline"><?php echo __( "Add to cart Button For add Multiple Product in the cart", OCPF_DOMAIN );?></p>
               </div>
            </div>
         </div>
         <?php
      }

      function OCPF_add_new_columns($new_columns){
         $new_columns = array();
         $new_columns['cb']   = '<input type="checkbox" />';
         $new_columns['title']   = esc_html__('Name', OCPF_DOMAIN);
         $new_columns['shortcode']   = esc_html__('Shortcode', OCPF_DOMAIN);
         $new_columns['date']   = esc_html__('Created at', OCPF_DOMAIN);
         return $new_columns;
      }

      //Add shortcode column
      function OCPF_manage_custom_columns( $column_name, $post_id ) {
         switch($column_name){
            case 'shortcode': ?>
               <input type="text" id="ocpf-selectdata_<?php echo $post_id;?>" value="[ocpf-Product-table id=<?php echo $post_id;?>]" size="30" onclick="ocpf_select_data(this.id)" readonly>
            <?php
            break;
            default:
            break;
         }
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


      function OCPF_meta_save( $post_id, $post ){
         // the following line is needed because we will hook into edit_post hook, so that we can set default value of checkbox.
         if ($post->post_type != 'product_table') {return;}
         // Is the user allowed to edit the post or page?
         if ( !current_user_can( 'edit_post', $post_id )) return;
         // Perform checking for before saving
         $is_autosave = wp_is_post_autosave($post_id);
         $is_revision = wp_is_post_revision($post_id);
         $is_valid_nonce = (isset($_POST['OCPF_meta_save_nounce']) && wp_verify_nonce( $_POST['OCPF_meta_save_nounce'], 'OCPF_meta_save' )? 'true': 'false');

            if ( $is_autosave || $is_revision || !$is_valid_nonce ) return;

            //default-option-tax
            $default_option_tax = sanitize_text_field( $_REQUEST['ocpf_default_display'] );
            update_post_meta( $post_id, 'ocpf_default_display', $default_option_tax );

            //default-option-tax_cat
            $cat = $this->recursive_sanitize_text_field( $_REQUEST['ocpf_product_categories'] );
            $default_option = array();
            foreach ($cat as $key => $value) {
              
               if(array_keys($value)[0] == $default_option_tax){
                  array_push($default_option,$value[$default_option_tax]);
               }
               
            }
           
            $default_option_db = implode(",",$default_option);
            update_post_meta( $post_id, 'ocpf_default', $default_option_db );


            //filter-option
            $filter_option = $this->recursive_sanitize_text_field( $_REQUEST['ocpf_filter'] );
            $filter_option_db = implode(",",$filter_option);
            update_post_meta( $post_id, 'ocpf_filter', $filter_option_db );

            //table column
            
            if(empty($_REQUEST['ocpf_tbl_clm'])){
               $ocpf_tbl_clm_db = "thumbnail,name,price,sku,category,review,add_cart";
            }else{
               $ocpf_tbl_clm = $this->recursive_sanitize_text_field( $_REQUEST['ocpf_tbl_clm'] );
               $ocpf_tbl_clm_db = implode(",",$ocpf_tbl_clm);
            }
            update_post_meta( $post_id, 'ocpf_tbl_clm', $ocpf_tbl_clm_db );
            
       
            //multiple add to cart
            if(empty($_REQUEST['ocpf_mul_add_cart'])){
               $mul_add_cart = "no";
            }else{
               $mul_add_cart = sanitize_text_field( $_REQUEST["ocpf_mul_add_cart"] );
            }
            update_post_meta( $post_id, 'ocpf_mul_add_cart', $mul_add_cart );
      }

      function duplicate_title( $new, $old, $post ) { 
          if ( $post->post_type == 'product' ) { 
              update_post_meta( $post->ID, 'ocpf_title', $post->post_title ); 
          } 
      }

      function init() {
         add_action('init', array($this, 'OCPF_create_menu'));
         add_action('add_meta_boxes', array($this, 'OCPF_add_meta_box'));
         add_filter('manage_product_table_posts_columns', array($this,'OCPF_add_new_columns'));
         add_action('manage_product_table_posts_custom_column', array($this, 'OCPF_manage_custom_columns'), 10, 2);
         add_action( 'edit_post', array($this, 'OCPF_meta_save'), 10, 2);
         add_action( 'transition_post_status', array($this, 'duplicate_title'), 10, 3 ); 
      }

      public static function instance() {
         if (!isset(self::$instance)) {
            self::$instance = new self();
            self::$instance->init();
         }
         return self::$instance;
      }

   }
   OCPF_menu::instance();
}

