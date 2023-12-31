<?php
// register the custom post type
add_action('init', 'wpestate_create_property_type',1);

if( !function_exists('wpestate_create_property_type') ):
function wpestate_create_property_type() {
    $rewrites =  get_option('wp_estate_url_rewrites');
    register_post_type('estate_property', array(
        'labels' => array(
            'name'                  => __('Properties','wpestate'),
            'singular_name'         => __('Property','wpestate'),
            'add_new'               => __('Add New Property','wpestate'),
            'add_new_item'          => __('Add Property','wpestate'),
            'edit'                  => __('Edit','wpestate'),
            'edit_item'             => __('Edit Property','wpestate'),
            'new_item'              => __('New Property','wpestate'),
            'view'                  => __('View','wpestate'),
            'view_item'             => __('View Property','wpestate'),
            'search_items'          => __('Search Property','wpestate'),
            'not_found'             => __('No Properties found','wpestate'),
            'not_found_in_trash'    => __('No Properties found in Trash','wpestate'),
            'parent'                => __('Parent Property','wpestate')
        ),
        'public' => true,
        'has_archive' => true,
        'rewrite' => array('slug' => $rewrites[0]),
        'supports' => array('title', 'editor', 'thumbnail', 'comments','excerpt'),
        'can_export' => true,
        'register_meta_box_cb' => 'wpestate_add_property_metaboxes',
        'menu_icon'=>get_template_directory_uri().'/img/properties.png'
         )
    );

    
    
////////////////////////////////////////////////////////////////////////////////////////////////
// Add custom taxomies
////////////////////////////////////////////////////////////////////////////////////////////////
    register_taxonomy('property_category', array('estate_property'), array(
        'labels' => array(
            'name'              => __('Categories','wpestate'),
            'add_new_item'      => __('Add New Property Category','wpestate'),
            'new_item_name'     => __('New Property Category','wpestate')
        ),
        'hierarchical'  => true,
        'query_var'     => true,
        'rewrite'       => array( 'slug' => $rewrites[1] )
        )
    );



// add custom taxonomy
register_taxonomy('property_action_category', array('estate_property'), array(
    'labels' => array(
        'name'              => __('Action','wpestate'),
        'add_new_item'      => __('Add New Action','wpestate'),
        'new_item_name'     => __('New Action','wpestate')
    ),
    'hierarchical'  => true,
    'query_var'     => true,
    'rewrite'       => array( 'slug' =>  $rewrites[2] )
   )      
);



// add custom taxonomy
register_taxonomy('property_city', array('estate_property'), array(
    'labels' => array(
        'name'              => __('City','wpestate'),
        'add_new_item'      => __('Add New City','wpestate'),
        'new_item_name'     => __('New City','wpestate')
    ),
    'hierarchical'  => true,
    'query_var'     => true,
    'rewrite'       => array( 'slug' =>  $rewrites[3],'with_front' => false)
    )
);




// add custom taxonomy
register_taxonomy('property_area', array('estate_property'), array(
    'labels' => array(
        'name'              => __('Neighborhood','wpestate'),
        'add_new_item'      => __('Add New Neighborhood','wpestate'),
        'new_item_name'     => __('New Neighborhood','wpestate')
    ),
    'hierarchical'  => true,
    'query_var'     => true,
    'rewrite'       => array( 'slug' =>  $rewrites[4] )

    )
);

// add custom taxonomy
register_taxonomy('property_county_state', array('estate_property'), array(
    'labels' => array(
        'name'              => __('County / State','wpestate'),
        'add_new_item'      => __('Add New County / State','wpestate'),
        'new_item_name'     => __('New County / State','wpestate')
    ),
    'hierarchical'  => true,
    'query_var'     => true,
    'rewrite'       => array( 'slug' =>  $rewrites[5] )

    )
);

}// end create property type
endif; // end   wpestate_create_property_type      



///////////////////////////////////////////////////////////////////////////////////////////////////////////
///  Add metaboxes for Property
///////////////////////////////////////////////////////////////////////////////////////////////////////////
if( !function_exists('wpestate_add_property_metaboxes') ):
function wpestate_add_property_metaboxes() {
   add_meta_box('new_tabbed_interface',         __('Property Details', 'wpestate'),             'estate_tabbed_interface', 'estate_property', 'normal', 'default' );
}
endif; // end   wpestate_add_property_metaboxes  


if( !function_exists('estate_tabbed_interface') ):
    function estate_tabbed_interface(){
        global $post;
        print'<div class="property_options_wrapper meta-options">
           
             <div class="property_options_wrapper_list">
                <div class="property_tab_item active_tab" data-content="property_details">'.__('Property Details','wpestate').'</div>
                <div class="property_tab_item " data-content="property_media">'.__('Property Media','wpestate').'</div>
                <div class="property_tab_item" data-content="property_customs">'.__('Property Custom Fields','wpestate').'</div>
                <div class="property_tab_item" data-content="property_map" id="property_map_trigger">'.__('Map','wpestate').'</div>
                <div class="property_tab_item" data-content="property_features">'.__('Amenities and Features','wpestate').'</div>
                <div class="property_tab_item" data-content="property_agent">'.__('Agent','wpestate').'</div>
                <div class="property_tab_item" data-content="property_floor">'.__('Floor Plans','wpestate').'</div>
                <div class="property_tab_item" data-content="property_paid">'.__('Paid Submission','wpestate').'</div>
                <div class="property_tab_item" data-content="property_subunits">'.__('Property  Subunits','wpestate').'</div>
            </div>
            <div class="property_options_content_wrapper">
                <div class="property_tab_item_content active_tab" id="property_details"><h3>'.__('Property Details','wpestate').'</h3>';
                estate_box();
                print'</div>
                
                <div class="property_tab_item_content " id="property_media"><h3>'.__('Property Media','wpestate').'</h3>';
                estate_property_add_media();
                print'</div> 

                <div class="property_tab_item_content" id="property_customs"><h3>'.__('Property Custom','wpestate').'</h3>';
                custom_details_box();
                print'</div>
                <div class="property_tab_item_content" id="property_map"><h3>'.__('Map','wpestate').'</h3>';
                map_estate_box();
                print'</div>
                <div class="property_tab_item_content" id="property_features"><h3>'.__('Amenities and Features','wpestate').'</h3>';
                amenities_estate_box();
                print'</div>
                <div class="property_tab_item_content" id="property_agent"><h3>'.__('Responsable Agent / User','wpestate').'</h3>';
                agentestate_box();
                print'</div>
                <div class="property_tab_item_content" id="property_floor"><h3>'.__('Floor Plans','wpestate').'</h3>';
                floorplan_box();
                print'</div>
                <div class="property_tab_item_content" id="property_paid"><h3>'.__('Paid Submission','wpestate').'</h3>';
                estate_paid_submission();
                print'</div>
                <div class="property_tab_item_content" id="property_subunits"><h3>'.__('Property Subunits','wpestate').'</h3>';
                estate_propery_subunits();
                print'</div>
            </div>
         
        </div>';
    }
endif;




if( !function_exists('floorplan_box') ):
function floorplan_box(){
    global $post;
    $plan_title         =   '';
    $plan_image         =   '';
    $plan_description   =   '';
    $plan_bath=$plan_rooms=$plan_size=$plan_price='';
    $use_floor_plans   = get_post_meta($post->ID, 'use_floor_plans', true);
    print '<p class="meta-options"> 
              <input type="hidden" name="use_floor_plans" value="0">
              <input type="checkbox" id="use_floor_plans" name="use_floor_plans" value="1"'; 
            if($use_floor_plans==1){
                print ' checked="checked" ';
            }
    print' >
              <label for="use_floor_plans">'.__('Use Floor Plans','wpestate').'</label>
          </p>';
    
    print '<div id="plan_wrapper">';
    
    $plan_title_array           = get_post_meta($post->ID, 'plan_title', true);
    $plan_desc_array            = get_post_meta($post->ID, 'plan_description', true) ;
    $plan_image_array           = get_post_meta($post->ID, 'plan_image', true) ;
    $plan_image_attach_array    = get_post_meta($post->ID, 'plan_image_attach', true) ;
    $plan_size_array            = get_post_meta($post->ID, 'plan_size', true) ;
    $plan_rooms_array           = get_post_meta($post->ID, 'plan_rooms', true) ;
    $plan_bath_array            = get_post_meta($post->ID, 'plan_bath', true);
    $plan_price_array           = get_post_meta($post->ID, 'plan_price', true) ;

  
    
    if(is_array($plan_title_array)){
        foreach ($plan_title_array as $key=> $plan_name) {

            if ( isset($plan_desc_array[$key])){
                $plan_desc=$plan_desc_array[$key];
            }else{
                $plan_desc='';
            }
            
            if ( isset($plan_image_attach_array[$key])){
                $plan_image_attach=$plan_image_attach_array[$key];
            }else{
                $plan_image_attach='';
            }
                
                
            if ( isset($plan_image_array[$key])){
                $plan_img=$plan_image_array[$key];
            }else{
                $plan_img='';
            }

            if ( isset($plan_size_array[$key])){
                $plan_size=$plan_size_array[$key];
            }else{
                $plan_size='';
            }

            if ( isset($plan_rooms_array[$key])){
                $plan_rooms=$plan_rooms_array[$key];
            }else{
                $plan_rooms='';
            }

            if ( isset($plan_bath_array[$key])){
                $plan_bath=$plan_bath_array[$key];
            }else{
                $plan_bath='';
            }

            if ( isset($plan_price_array[$key])){
                $plan_price=$plan_price_array[$key];
            }else{
                $plan_price='';
            }


            print '

            <div class="plan_row">  
            <i class="fa deleter_floor fa-trash-o"></i>

            <p class="meta-options floor_p">
                <label for="plan_title">'.__('Plan Title','wpestate').'</label><br />
                <input id="plan_title" type="text" size="36" name="plan_title[]" value="'.$plan_name.'" />
           </p>

            <p class="meta-options floor_p">
                <label for="plan_description">'.__('Plan Description','wpestate').'</label><br />
                <textarea class="plan_description" type="text" size="36" name="plan_description[]" >'.$plan_desc.'</textarea>
            </p>

          

            <p class="meta-options floor_p">
                <label for="plan_size">'.__('Plan Size','wpestate').'</label><br />
                <input id="plan_size" type="text" size="36" name="plan_size[]" value="'.$plan_size.'" />
            </p>

            <p class="meta-options floor_p">
                <label for="plan_rooms">'.__('Plan Rooms','wpestate').'</label><br />
                <input id="plan_rooms" type="text" size="36" name="plan_rooms[]" value="'.$plan_rooms.'" />
            </p>

            <p class="meta-options floor_p">
                <label for="plan_bath">'.__('Plan Bathrooms','wpestate').'</label><br />
                <input id="plan_bath" type="text" size="36" name="plan_bath[]" value="'.$plan_bath.'" />
            </p>

            <p class="meta-options floor_p">
                <label for="plan_price">'.__('Plan Price','wpestate').'</label><br />
                <input id="plan_price" type="text" size="36" name="plan_price[]" value="'.$plan_price.'" />
            </p>
            

            <p class="meta-options floor_p image_plan">
                <label for="plan_image">'.__('Plan Image','wpestate').'</label><br />
                <input id="plan_image" type="text" size="36" name="plan_image[]" value="'.$plan_img.'" /> '
                    . '<input type="hidden" id="plan_image_attach" name="plan_image_attach[]" value="'.$plan_image_attach.'"/>   
                <input id="plan_image_button" type="button"   size="40" class="upload_button button floorbuttons" value="'.__('Upload Image','wpestate').'" />
              
               
            </p>
            </div>';
        }
    }
  
    
  
 
    print '
    </div>    
    <span id="add_new_plan">'.__('Add new plan','wpestate').'</span>
    ';
   
    
}
endif;


///////////////////////////////////////////////////////////////////////////////////////////////////////////
///  Property Custom details  function
///////////////////////////////////////////////////////////////////////////////////////////////////////////
if( !function_exists('custom_details_box') ):
function custom_details_box(){
     global $post;
     $i=0;
     $custom_fields = get_option( 'wp_estate_custom_fields', true);    
     if( !empty($custom_fields)){  
        while($i< count($custom_fields) ){     
            $name =   $custom_fields[$i][0]; 
            $label =   $custom_fields[$i][1];
            $type =   $custom_fields[$i][2];
            $order =   $custom_fields[$i][3];
            $dropdown_values =   $custom_fields[$i][4];
            
            $slug         =     wpestate_limit45(sanitize_title( $name )); 
            $slug         =     sanitize_key($slug); 
            $post_id      =     $post->ID;
            $show         =     1;
            print ' <div class="property_prop_half">   ';
              wpestate_show_custom_field($show,$slug,$name,$label,$type,$order,$dropdown_values,$post_id);
            print '</div>';  
            $i++;        
       }
    }
    print '<div style="clear:both"></div>';
     
}
endif; // end   custom_details_box  


if( !function_exists('wpestate_show_custom_field')):
    function wpestate_show_custom_field( $show,$slug,$name,$label,$type,$order,$dropdown_values,$post_id,$value=''){
    
        // get value
        if($value ==''){
            $value          =   esc_html(get_post_meta($post_id, $slug, true));
            if( $type == 'numeric'  ){
                
                $value          =   (get_post_meta($post_id, $slug, true));
                if($value!==''){
                   $value =  floatval ($value);
                }
                
                
            }else{
                $value          =   esc_html(get_post_meta($post_id, $slug, true));
            }
      
        }
        
        
        $template='';
        if ( $type =='long text' ){
            $template.= '<label for="'.$slug.'">'.$label.' '.__('(*text)','wpestate').' </label>';
            $template.= '<textarea type="text" id="'.$slug.'"  size="0" name="'.$slug.'" rows="3" cols="42">' .$value. '</textarea>'; 
        }else if( $type =='short text' ){
            $template.=  '<label for="'.$slug.'">'.$label.' '.__('(*text)','wpestate').' </label>';
            $template.=  '<input type="text" id="'.$slug.'" size="40" name="'.$slug.'" value="' . $value . '">';
        }else if( $type =='numeric'  ){
            $template.=  '<label for="'.$slug.'">'.$label.' '.__('(*numeric)','wpestate').' </label>';
            $template.=  '<input type="text" id="'.$slug.'" size="40" name="'.$slug.'" value="' . $value . '">';
        }else if( $type =='date' ){
            $template.=  '<label for="'.$slug.'">'.$label.' '.__('(*date)','wpestate').' </label>';
            $template.=  '<input type="text" id="'.$slug.'" size="40" name="'.$slug.'" value="' .$value . '">';
            $template.= wpestate_date_picker_translation_return($slug);
        }else if( $type =='dropdown' ){
            $dropdown_values_array=explode(',',$dropdown_values);
           
            $template.=  '<label for="'.$slug.'">'.$label.' </label>';
            $template.= '<select id="'.$slug.'"  name="'.$slug.'" >';
            foreach($dropdown_values_array as $key=>$value_drop){
                $template.= '<option value="'.trim($value_drop).'"';
                if( trim( htmlspecialchars_decode($value) ) === trim( htmlspecialchars_decode ($value_drop) ) ){
        
                    $template.=' selected ';
                }
                if (function_exists('icl_translate') ){
                    $value_drop = apply_filters('wpml_translate_single_string', $value_drop,'custom field value','custom_field_value'.$value_drop );
                }
                
                $template.= '>'.trim($value_drop).'</option>';
            }
            $template.= '</select>';
        }
        
        if($show==1){
            print $template;
        }else{
            return $template;
        }
        
    }
endif;





///////////////////////////////////////////////////////////////////////////////////////////////////////////
///  Property Pay Submission  function
///////////////////////////////////////////////////////////////////////////////////////////////////////////
if( !function_exists('estate_paid_submission') ):

function estate_paid_submission(){
    global $post;
    print ' <div class="property_prop_half">   ';
    $paid_submission_status= esc_html ( get_option('wp_estate_paid_submission','') );
    if($paid_submission_status=='no'){
       _e('Paid Submission is disabled','wpestate');  
    }
    if($paid_submission_status=='membership'){
       _e('You are on membership mode. There are no details to show for this mode.','wpestate');  
    }
    if($paid_submission_status=='per listing'){
        _e('Pay Status: ','wpestate');
        $pay_status           = get_post_meta($post->ID, 'pay_status', true);
        if($pay_status=='paid'){
           _e('PAID','wpestate');
        }
        else{
           _e('Not Paid','wpestate');
        }
    }
    print'</div>';
    
}
endif; // end   estate_paid_submission  




///////////////////////////////////////////////////////////////////////////////////////////////////////////
///  Property details  function
///////////////////////////////////////////////////////////////////////////////////////////////////////////
if( !function_exists('details_estate_box') ):

function details_estate_box($post) {
    global $post;
    wp_nonce_field(plugin_basename(__FILE__), 'estate_property_noncename');
    
    $mypost             =   $post->ID;
    print'            
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr>
        <td width="33%" valign="top" align="left">
            <p class="meta-options">
            <label for="property_price">'.__('Price: ','wpestate').'</label><br />
            <input type="text" id="property_price" size="40" name="property_price" value="' . esc_html(get_post_meta($mypost, 'property_price', true)) . '">
            </p>
        </td>

        <td width="33%" valign="top" align="left">
            <p class="meta-options">
            <label for="property_label">'.__('After Price Label(*for example "per month"): ','wpestate').'</label><br />
            <input type="text" id="property_label" size="40" name="property_label" value="' . esc_html(get_post_meta($mypost, 'property_label', true)) . '">
            </p>
        </td>
    </tr>
    <tr>
        <td width="33%" valign="top" align="left">
            <p class="meta-options">
            <label for="property_label_before">'.__('Before Price Label(*for example "per month"): ','wpestate').'</label><br />
            <input type="text" id="property_label_before" size="40" name="property_label_before" value="' . esc_html(get_post_meta($mypost, 'property_label_before', true)) . '">
            </p>
        </td>
    </tr>
    


    <tr>
    
    <td width="33%" valign="top" align="left">
        <p class="meta-options">
        <label for="property_size">'.__('Size(*only numbers): ','wpestate').'</label><br />
        <input type="text" id="property_size" size="40" name="property_size" value="' . esc_html(get_post_meta($mypost, 'property_size', true)) . '">
        </p>
    </td>
    
    <td width="33%" valign="top" align="left">
        <p class="meta-options">
        <label for="property_lot_size">'.__('Lot Size(*only numbers): ','wpestate').'</label><br />
        <input type="text" id="property_lot_size" size="40" name="property_lot_size" value="' . esc_html(get_post_meta($mypost, 'property_lot_size', true)) . '">
        </p>
    </td>   
    </tr>
    
    <tr>      
    <td valign="top" align="left">
        <p class="meta-options">
        <label for="property_rooms">'.__('Rooms(*only numbers): ','wpestate').'</label><br />
        <input type="text" id="property_rooms" size="40" name="property_rooms" value="' . esc_html(get_post_meta($mypost, 'property_rooms', true)) . '">
        </p>
    </td>
    
    <td valign="top" align="left">
        <p class="meta-options">
        <label for="property_bedrooms">'.__('Bedrooms(*only numbers): ','wpestate').'</label><br />
        <input type="text" id="property_bedrooms" size="40" name="property_bedrooms" value="' . esc_html(get_post_meta($mypost, 'property_bedrooms', true)) . '">
        </p>
    </td>
    </tr>

    <tr>
    <td valign="top" align="left">  
        <p class="meta-options">
        <label for="property_bedrooms">'.__('Bathrooms(*only numbers): ','wpestate').'</label><br />
        <input type="text" id="property_bathrooms" size="40" name="property_bathrooms" value="' . esc_html(get_post_meta($mypost, 'property_bathrooms', true)) . '">
        </p>
    </td>
  
    </tr>
    <tr>';
     
     $option_video='';
     $video_values = array('vimeo', 'youtube');
     $video_type = get_post_meta($mypost, 'embed_video_type', true);

     foreach ($video_values as $value) {
         $option_video.='<option value="' . $value . '"';
         if ($value == $video_type) {
             $option_video.='selected="selected"';
         }
         $option_video.='>' . $value . '</option>';
     }
     
     
    print'
    <td valign="top" align="left">
        <p class="meta-options">
        <label for="embed_video_type">'.__('Video from ','wpestate').'</label><br />
        <select id="embed_video_type" name="embed_video_type" style="width: 237px;">
                ' . $option_video . '
        </select>       
        </p>
    </td>';

  
    print'
    <td valign="top" align="left">
      <p class="meta-options">     
      <label for="embed_video_id">'.__('Embed Video id: ','wpestate').'</label> <br />
        <input type="text" id="embed_video_id" name="embed_video_id" size="40" value="'.esc_html( get_post_meta($mypost, 'embed_video_id', true) ).'">
      </p>
    </td>
    </tr>';
    
  
     
    print'
    <td valign="top" align="left">
      <p class="meta-options">     
      <label for="embed_video_type">'.__('Virtual Tour ','wpestate').'</label><br />
        <textarea id="embed_virtual_tour" name="embed_virtual_tour">'.( get_post_meta($mypost, 'embed_virtual_tour', true) ).'</textarea>
    </td>
    </tr>';
    print'
    <td valign="top" align="left">
      <p class="meta-options">     
      <label for="owner_notes">'.__('Owner/Agent notes (*not visible on front end): ','wpestate').'</label> <br />
        <textarea id="owner_notes" name="owner_notes" >'.esc_html( get_post_meta($mypost, 'owner_notes', true) ).'</textarea>

      </p>
    </td>
    </tr>';
    
    
    print'
    </table>';
}
endif; // end   details_estate_box  



///////////////////////////////////////////////////////////////////////////////////////////////////////////
///  Google map function
///////////////////////////////////////////////////////////////////////////////////////////////////////////
if( !function_exists('map_estate_box') ):
 
function map_estate_box() {
    wp_nonce_field(plugin_basename(__FILE__), 'estate_property_noncename');
    global $post;
    
    $mypost                 =   $post->ID;
    $gmap_lat               =   floatval(get_post_meta($mypost, 'property_latitude', true));
    $gmap_long              =   floatval(get_post_meta($mypost, 'property_longitude', true));
    $google_camera_angle    =   intval( esc_html(get_post_meta($mypost, 'google_camera_angle', true)) );
    $cache_array            =   array('yes','no');
    $keep_min_symbol        =   '';
    $keep_min_status        =   esc_html ( get_post_meta($post->ID, 'keep_min', true) );

    foreach($cache_array as $value){
            $keep_min_symbol.='<option value="'.$value.'"';
            if ($keep_min_status==$value){
                    $keep_min_symbol.=' selected="selected" ';
            }
            $keep_min_symbol.='>'.$value.'</option>';
    }
    
    
    $page_custom_zoom  = get_post_meta($mypost, 'page_custom_zoom', true);
    if ($page_custom_zoom==''){
        $page_custom_zoom=16;
    }
   
    wpestate_date_picker_translation('property_date');
    print'
    <p class="meta-options"> 
    <div id="googleMap" style="width:100%;height:380px;margin-bottom:30px;"></div>    
    <p class="meta-options"> 
        <a class="button" href="#" id="admin_place_pin">'.__('Place Pin with Property Address','wpestate').'</a>
    </p>
    
    <div class="property_prop_half">
        <label for="embed_video_id">'.__('Latitude:','wpestate').'</label> <br />
        <input type="text" id="property_latitude" style="margin-right:20px;" size="40" name="property_latitude" value="' . $gmap_lat . '">
    </div>
    
    <div class="property_prop_half">  
        <label for="embed_video_id">'.__('Longitude:','wpestate').'</label> <br />
        <input type="text" id="property_longitude" style="margin-right:20px;" size="40" name="property_longitude" value="' . $gmap_long . '">
    </div>

   <div class="property_prop_half">  
       <label for="page_custom_zoom">'.__('Zoom Level for map (1-20)','wpestate').'</label><br />
       <select name="page_custom_zoom" id="page_custom_zoom">';
      
        for ($i=1;$i<21;$i++){
            print '<option value="'.$i.'"';
            if($page_custom_zoom==$i){
                print ' selected="selected" ';
            }
            print '>'.$i.'</option>';
        }
        
        print'
        </select>
    </div>
    

    <div class="property_prop_half" style="padding-top:20px;">  
        <input type="hidden" name="property_google_view" value="">
        <input type="checkbox"  id="property_google_view" name="property_google_view" value="1" ';
            if (esc_html(get_post_meta($mypost, 'property_google_view', true)) == 1) {
                print'checked="checked"';
            }
            print' />
        <label class="checklabel" for="property_google_view">'.__('Enable Google Street View','wpestate').'</label>
    </div>    


    <div class="property_prop_half">  
        <label for="google_camera_angle" >'.__('Google View Camera Angle','wpestate').'</label>
        <input type="text" id="google_camera_angle" style="margin-right:0px;" size="5" name="google_camera_angle" value="'.$google_camera_angle.'">
    </div>

   
    ';     
}
endif; // end   map_estate_box 




///////////////////////////////////////////////////////////////////////////////////////////////////////////
///  Agent box function
///////////////////////////////////////////////////////////////////////////////////////////////////////////
if( !function_exists('agentestate_box') ):
function agentestate_box() {
    global $post;
    wp_nonce_field(plugin_basename(__FILE__), 'estate_property_noncename');
   
    $mypost         =   $post->ID;
    $originalpost   =   $post;
    $agent_list     =   '';
    $picked_agent   =   (get_post_meta($mypost, 'property_agent', true));

    $args = array(
       'post_type'      => 'estate_agent',
       'post_status'    => 'publish',
       'posts_per_page' => -1
       );
    
    $agent_selection  =  new WP_Query($args);

    while ($agent_selection->have_posts()){
        $agent_selection->the_post();  
        $the_id       =  get_the_ID();

        $agent_list  .=  '<option value="' . $the_id . '"  ';
        if ($the_id == $picked_agent) {
            $agent_list.=' selected="selected" ';
        }
        $agent_list.= '>' . get_the_title() . '</option>';
    }
      
    wp_reset_postdata();
    $post = $originalpost;
      
    print '
        <div class="property_prop_half">  
        <label for="property_agent">'.__('Agent Responsible: ','wpestate').'</label><br />
        <select id="property_agent" style="width: 237px;" name="property_agent">
            <option value="">none</option>
            <option value=""></option>
            '. $agent_list .'
        </select>
        </div>';  
    
    
    $originalpost   =   $post;
    $blog_list      =   '';
    $original_user  =   wpsestate_get_author();


    
    $blogusers = get_users( 'blog_id=1&orderby=nicename&role=subscriber' );

    foreach ( $blogusers as $user ) {
 
        $the_id=$user->ID;
        $blog_list  .=  '<option value="' . $the_id . '"  ';
            if ($the_id == $original_user) {
                $blog_list.=' selected="selected" ';
            }
        $blog_list.= '>' .$user->user_login . '</option>';
    }


    
      
    print '
    <div class="property_prop_half">  
        <label for="property_user">'.__('User: ','wpestate').'</label><br />
        <select id="property_user" style="width: 237px;" name="property_user">
            <option value=""></option>
            <option value="1">admin</option>
            '. $blog_list .'
        </select>
      </div>';  
    
    
}
endif; // end   agentestate_box  





///////////////////////////////////////////////////////////////////////////////////////////////////////////
///  Features And Amenties function
///////////////////////////////////////////////////////////////////////////////////////////////////////////
if( !function_exists('amenities_estate_box') ):
function amenities_estate_box() {
    wp_nonce_field(plugin_basename(__FILE__), 'estate_property_noncename');
    global $post;
    $mypost             =   $post->ID;
    $feature_list_array =   array();
    $feature_list       =   esc_html( get_option('wp_estate_feature_list') );
    $feature_list_array =   explode( ',',$feature_list);
    $counter            =   0;
    

    foreach($feature_list_array as $key => $value){
        $counter++;
        $post_var_name=  str_replace(' ','_', trim($value) );
        $value_label=$value;
        if (function_exists('icl_translate') ){
            $value_label    =   icl_translate('wpestate','wp_estate_property_custom_amm_'.$value, $value ) ;                                      
        }
    
    
        if( ($counter-1) % 3 == 0){
            print'<tr>';
        }
        $input_name =   wpestate_limit45(sanitize_title( $post_var_name ));
        $input_name =   sanitize_key($input_name);
      
      
        print '     
        <div class="property_prop_half propcheck"  style="padding-top:20px;">
            <input type="hidden"    name="'.$input_name.'" value="">
            <input type="checkbox"  name="'.$input_name.'" value="1" ';
        
        if (esc_html(get_post_meta($mypost, $input_name, true)) == 1) {
            print' checked="checked" ';
        }
        print' />
            <label class="checklabel" for="'.$input_name.'">'.stripslashes($value_label).'</label>
           
        </div>';
       
    }
  
}
endif; // end   amenities_estate_box  



///////////////////////////////////////////////////////////////////////////////////////////////////////////
/// Property custom fields
///////////////////////////////////////////////////////////////////////////////////////////////////////////
if( !function_exists('estate_property_add_media') ): 
function estate_property_add_media() {


global $post;

$arguments      = array(
    'numberposts' => -1,
    'post_type' => 'attachment',
    'post_mime_type' => 'image',
    'post_parent' => $post->ID,
    'post_status' => null,
    'exclude' => get_post_thumbnail_id(),
    'orderby' => 'menu_order',
    'order' => 'ASC',
    'orderby' => 'menu_order',
    'order' => 'ASC'
    );

$already_in='';
$post_attachments   = get_posts($arguments);

print '<div class="property_uploaded_thumb_wrapepr" id="property_uploaded_thumb_wrapepr">';
foreach ($post_attachments as $attachment) {
    
    $already_in         =   $already_in.$attachment->ID.',';
    $preview            =   wp_get_attachment_image_src($attachment->ID, 'thumbnail');
    print '<div class="uploaded_thumb" data-imageid="'.$attachment->ID.'">
        <img  src="'.$preview[0].'"  alt="slider" />
        <a target="_blank" href="'.admin_url().'post.php?post='.$attachment->ID.'&action=edit" class="attach_edit"><i class="fa fa-pencil" aria-hidden="true"></i></a>
        <span class="attach_delete"><i class="fa fa-trash-o" aria-hidden="true"></i></span>
    </div>';
}
  
print '<input type="hidden" id="image_to_attach" name="image_to_attach" value="'.$already_in.'"/>';
 

print '</div>';

print '<button class="upload_button button" id="button_new_image" data-postid="'.$post->ID.'">'.__('Upload new Image','wpestate').'</button>';
  
 
    $mypost = $post->ID;
    $option_video='';
    $video_values = array('vimeo', 'youtube');
    $video_type = get_post_meta($mypost, 'embed_video_type', true);

    foreach ($video_values as $value) {
        $option_video.='<option value="' . $value . '"';
        if ($value == $video_type) {
            $option_video.='selected="selected"';
        }
        $option_video.='>' . $value . '</option>';
    }
     
 

  
    print'
    <div class="property_prop_half" style="clear: both;">   
        <label for="embed_video_id">'.__('Video From: ','wpestate').'</label> <br />
         <select id="embed_video_type" name="embed_video_type" >
                ' . $option_video . '
        </select>  
    </div>
    

    <div class="property_prop_half">   
        <label for="embed_video_id">'.__('Embed Video id: ','wpestate').'</label> <br />
        <input type="text" id="embed_video_id" name="embed_video_id" size="40" value="'.esc_html( get_post_meta($mypost, 'embed_video_id', true) ).'">
    </div>';
    
    print'
    <div class="property_prop_half">   
        <label for="embed_video_type">'.__('Virtual Tour ','wpestate').'</label><br />
        <textarea id="embed_virtual_tour" name="embed_virtual_tour">'.( get_post_meta($mypost, 'embed_virtual_tour', true) ).'</textarea>
    </div>';
}
endif;

if( !function_exists('estate_box') ): 
function estate_box() {
    global $post;
    wp_nonce_field(plugin_basename(__FILE__), 'estate_property_noncename');
    $mypost = $post->ID;
    
    
    print'            
    <div class="property_prop_half">
        <label for="property_price">'.__('Price: ','wpestate').'</label><br />
        <input type="text" id="property_price" size="40" name="property_price" value="' . esc_html(get_post_meta($mypost, 'property_price', true)) . '">
    </div>
    
    <div class="property_prop_half">
        <label for="property_label">'.__('After Price Label(*for example "per month"): ','wpestate').'</label><br />
        <input type="text" id="property_label" size="40" name="property_label" value="' . esc_html(get_post_meta($mypost, 'property_label', true)) . '">
    </div>
    
    <div class="property_prop_half">
        <label for="property_label_before">'.__('Before Price Label(*for example "per month"): ','wpestate').'</label><br />
        <input type="text" id="property_label_before" size="40" name="property_label_before" value="' . esc_html(get_post_meta($mypost, 'property_label_before', true)) . '">
    </div>
    
 
    
    <div class="property_prop_half">
        <label for="property_address">'.__('Address(*only street name and building no): ','wpestate').'</label><br />
        <input type="text" type="text" id="property_address"  size="40" name="property_address" value="' . esc_html(get_post_meta($mypost, 'property_address', true)) . '" >
    </div>
   
    <div class="property_prop_half">
        <label for="property_zip">'.__('Zip: ','wpestate').'</label><br />
        <input type="text" id="property_zip" size="40" name="property_zip" value="' . esc_html(get_post_meta($mypost, 'property_zip', true)) . '">
    </div>
    
    <div class="property_prop_half">
        <label for="property_country">'.__('Country: ','wpestate').'</label><br />';
    print wpestate_country_list(esc_html(get_post_meta($mypost, 'property_country', true)));
    print '</div>
    
    
    <div class="property_prop_half">
        <label for="property_size">'.__('Size(*only numbers): ','wpestate').'</label><br />
        <input type="text" id="property_size" size="40" name="property_size" value="' . esc_html(get_post_meta($mypost, 'property_size', true)) . '">
    </div>

    <div class="property_prop_half">
        <label for="property_lot_size">'.__('Lot Size(*only numbers): ','wpestate').'</label><br />
        <input type="text" id="property_lot_size" size="40" name="property_lot_size" value="' . esc_html(get_post_meta($mypost, 'property_lot_size', true)) . '">
    </div>
    
    <div class="property_prop_half">    
        <label for="property_rooms">'.__('Rooms(*only numbers): ','wpestate').'</label><br />
        <input type="text" id="property_rooms" size="40" name="property_rooms" value="' . esc_html(get_post_meta($mypost, 'property_rooms', true)) . '">
    </div>        

    <div class="property_prop_half">
        <label for="property_bedrooms">'.__('Bedrooms(*only numbers): ','wpestate').'</label><br />
        <input type="text" id="property_bedrooms" size="40" name="property_bedrooms" value="' . esc_html(get_post_meta($mypost, 'property_bedrooms', true)) . '">
    </div>
    
    <div class="property_prop_half">  
        <label for="property_bedrooms">'.__('Bathrooms(*only numbers): ','wpestate').'</label><br />
        <input type="text" id="property_bathrooms" size="40" name="property_bathrooms" value="' . esc_html(get_post_meta($mypost, 'property_bathrooms', true)) . '">
    </div>';
     
    
  
     
 
    print'
    <div class="property_prop_half prop_full"> 
        <label for="owner_notes">'.__('Owner/Agent notes (*not visible on front end): ','wpestate').'</label> <br />
        <textarea id="owner_notes" name="owner_notes" >'.esc_html( get_post_meta($mypost, 'owner_notes', true) ).'</textarea>
    </div>
    ';
    
    
    
    
   
    
    $status_values          =   esc_html( get_option('wp_estate_status_list') );
    $status_values_array    =   explode(",",$status_values);
    $prop_stat              =   get_post_meta($mypost, 'property_status', true);
    $property_status        =   '';

    
    foreach ($status_values_array as $key=>$value) {
        if(trim($value)!= ''){
            if (function_exists('icl_translate') ){
                $value     =   icl_translate('wpestate','wp_estate_property_status_'.$value, $value ) ;                                      
            }

            $value = trim($value);
            $property_status.='<option value="' . $value . '"';
            if ($value == $prop_stat) {
                $property_status.='selected="selected"';
            }
            $property_status.='>' .stripslashes( $value ). '</option>';
        }   
    }
    
    
    
    
    $normal_selected='';
    if ( trim($status_values)==''){
      print   $normal_selected= ' selected ' ;
    }

 
    print'    
    <div class="property_prop_half">
        <label for="property_status">'.__('Property Status:','wpestate').'</label><br />
        <select id="property_status"  name="property_status">
        <option value="normal" '.$normal_selected.'>normal</option>
        ' . $property_status . '
        </select>
    </div>
    
     <div class="property_prop_half" style="padding-top:20px;">
            <input type="hidden" name="prop_featured" value="0">
            <input type="checkbox"  id="prop_featured" name="prop_featured" value="1" ';
            if (intval(get_post_meta($mypost, 'prop_featured', true)) == 1) {
                print'checked="checked"';
            }
            print' />
            <label class="checklabel" for="prop_featured">'.__('Make it Featured Property','wpestate').'</label>
    </div>    
    
    <div class="property_prop_half">
        <input type="hidden" name="property_theme_slider" value="0">
        <input type="checkbox"  id="property_theme_slider" name="property_theme_slider" value="1" ';
        $theme_slider   =   get_option( 'wp_estate_theme_slider', ''); 
        
        if ( is_array($theme_slider) && in_array ( $mypost, $theme_slider) ) {
            print'checked="checked"';
        }
        print' />
        <label class="checklabel" for="property_theme_slider">'.__('Property in theme Slider','wpestate').'</label>
    </div>


    ';
       
}
endif; // end   estate_box 


///////////////////////////////////////////////////////////////////////////////////////////////////////////
///  Country list function
///////////////////////////////////////////////////////////////////////////////////////////////////////////


if( !function_exists('wpestate_country_list') ): 
function wpestate_country_list($selected,$class='') {
    $countries = array(__("Afghanistan","wpestate"),__("Albania","wpestate"),__("Algeria","wpestate"),__("American Samoa","wpestate"),__("Andorra","wpestate"),__("Angola","wpestate"),__("Anguilla","wpestate"),__("Antarctica","wpestate"),__("Antigua and Barbuda","wpestate"),__("Argentina","wpestate"),__("Armenia","wpestate"),__("Aruba","wpestate"),__("Australia","wpestate"),__("Austria","wpestate"),__("Azerbaijan","wpestate"),__("Bahamas","wpestate"),__("Bahrain","wpestate"),__("Bangladesh","wpestate"),__("Barbados","wpestate"),__("Belarus","wpestate"),__("Belgium","wpestate"),__("Belize","wpestate"),__("Benin","wpestate"),__("Bermuda","wpestate"),__("Bhutan","wpestate"),__("Bolivia","wpestate"),__("Bosnia and Herzegowina","wpestate"),__("Botswana","wpestate"),__("Bouvet Island","wpestate"),__("Brazil","wpestate"),__("British Indian Ocean Territory","wpestate"),__("Brunei Darussalam","wpestate"),__("Bulgaria","wpestate"),__("Burkina Faso","wpestate"),__("Burundi","wpestate"),__("Cambodia","wpestate"),__("Cameroon","wpestate"),__("Canada","wpestate"),__("Cape Verde","wpestate"),__("Cayman Islands","wpestate"),__("Central African Republic","wpestate"),__("Chad","wpestate"),__("Chile","wpestate"),__("China","wpestate"),__("Christmas Island","wpestate"),__("Cocos (Keeling) Islands","wpestate"),__("Colombia","wpestate"),__("Comoros","wpestate"),__("Congo","wpestate"),__("Congo, the Democratic Republic of the","wpestate"),__("Cook Islands","wpestate"),__("Costa Rica","wpestate"),__("Cote d'Ivoire","wpestate"),__("Croatia (Hrvatska)","wpestate"),__("Cuba","wpestate"),__("Cyprus","wpestate"),__("Czech Republic","wpestate"),__("Denmark","wpestate"),__("Djibouti","wpestate"),__("Dominica","wpestate"),__("Dominican Republic","wpestate"),__("East Timor","wpestate"),__("Ecuador","wpestate"),__("Egypt","wpestate"),__("El Salvador","wpestate"),__("Equatorial Guinea","wpestate"),__("Eritrea","wpestate"),__("Estonia","wpestate"),__("Ethiopia","wpestate"),__("Falkland Islands (Malvinas)","wpestate"),__("Faroe Islands","wpestate"),__("Fiji","wpestate"),__("Finland","wpestate"),__("France","wpestate"),__("France Metropolitan","wpestate"),__("French Guiana","wpestate"),__("French Polynesia","wpestate"),__("French Southern Territories","wpestate"),__("Gabon","wpestate"),__("Gambia","wpestate"),__("Georgia","wpestate"),__("Germany","wpestate"),__("Ghana","wpestate"),__("Gibraltar","wpestate"),__("Greece","wpestate"),__("Greenland","wpestate"),__("Grenada","wpestate"),__("Guadeloupe","wpestate"),__("Guam","wpestate"),__("Guatemala","wpestate"),__("Guinea","wpestate"),__("Guinea-Bissau","wpestate"),__("Guyana","wpestate"),__("Haiti","wpestate"),__("Heard and Mc Donald Islands","wpestate"),__("Holy See (Vatican City State)","wpestate"),__("Honduras","wpestate"),__("Hong Kong","wpestate"),__("Hungary","wpestate"),__("Iceland","wpestate"),__("India","wpestate"),__("Indonesia","wpestate"),__("Iran (Islamic Republic of)","wpestate"),__("Iraq","wpestate"),__("Ireland","wpestate"),__("Israel","wpestate"),__("Italy","wpestate"),__("Jamaica","wpestate"),__("Japan","wpestate"),__("Jordan","wpestate"),__("Kazakhstan","wpestate"),__("Kenya","wpestate"),__("Kiribati","wpestate"),__("Korea, Democratic People's Republic of","wpestate"),__("Korea, Republic of","wpestate"),__("Kuwait","wpestate"),__("Kyrgyzstan","wpestate"),__("Lao, People's Democratic Republic","wpestate"),__("Latvia","wpestate"),__("Lebanon","wpestate"),__("Lesotho","wpestate"),__("Liberia","wpestate"),__("Libyan Arab Jamahiriya","wpestate"),__("Liechtenstein","wpestate"),__("Lithuania","wpestate"),__("Luxembourg","wpestate"),__("Macau","wpestate"),__("Macedonia (FYROM)","wpestate"),__("Madagascar","wpestate"),__("Malawi","wpestate"),__("Malaysia","wpestate"),__("Maldives","wpestate"),__("Mali","wpestate"),__("Malta","wpestate"),__("Marshall Islands","wpestate"),__("Martinique","wpestate"),__("Mauritania","wpestate"),__("Mauritius","wpestate"),__("Mayotte","wpestate"),__("Mexico","wpestate"),__("Micronesia, Federated States of","wpestate"),__("Moldova, Republic of","wpestate"),__("Monaco","wpestate"),__("Mongolia","wpestate"),__("Montserrat","wpestate"),__("Morocco","wpestate"),__("Mozambique","wpestate"),__("Montenegro","wpestate"),__("Myanmar","wpestate"),__("Namibia","wpestate"),__("Nauru","wpestate"),__("Nepal","wpestate"),__("Netherlands","wpestate"),__("Netherlands Antilles","wpestate"),__("New Caledonia","wpestate"),__("New Zealand","wpestate"),__("Nicaragua","wpestate"),__("Niger","wpestate"),__("Nigeria","wpestate"),__("Niue","wpestate"),__("Norfolk Island","wpestate"),__("Northern Mariana Islands","wpestate"),__("Norway","wpestate"),__("Oman","wpestate"),__("Pakistan","wpestate"),__("Palau","wpestate"),__("Panama","wpestate"),__("Papua New Guinea","wpestate"),__("Paraguay","wpestate"),__("Peru","wpestate"),__("Philippines","wpestate"),__("Pitcairn","wpestate"),__("Poland","wpestate"),__("Portugal","wpestate"),__("Puerto Rico","wpestate"),__("Qatar","wpestate"),__("Reunion","wpestate"),__("Romania","wpestate"),__("Russian Federation","wpestate"),__("Rwanda","wpestate"),__("Saint Kitts and Nevis","wpestate"),__("Saint Martin","wpestate"),__("Saint Lucia","wpestate"),__("Saint Vincent and the Grenadines","wpestate"),__("Samoa","wpestate"),__("San Marino","wpestate"),__("Sao Tome and Principe","wpestate"),__("Saudi Arabia","wpestate"),__("Senegal","wpestate"),__("Seychelles","wpestate"),__("Serbia","wpestate"),__("Sierra Leone","wpestate"),__("Singapore","wpestate"),__("Slovakia (Slovak Republic)","wpestate"),__("Slovenia","wpestate"),__("Solomon Islands","wpestate"),__("Somalia","wpestate"),__("South Africa","wpestate"),__("South Georgia and the South Sandwich Islands","wpestate"),__("Spain","wpestate"),__("Sri Lanka","wpestate"),__("St. Helena","wpestate"),__("St. Pierre and Miquelon","wpestate"),__("Sudan","wpestate"),__("Suriname","wpestate"),__("Svalbard and Jan Mayen Islands","wpestate"),__("Swaziland","wpestate"),__("Sweden","wpestate"),__("Switzerland","wpestate"),__("Syrian Arab Republic","wpestate"),__("Taiwan, Province of China","wpestate"),__("Tajikistan","wpestate"),__("Tanzania, United Republic of","wpestate"),__("Thailand","wpestate"),__("Togo","wpestate"),__("Tokelau","wpestate"),__("Tonga","wpestate"),__("Trinidad and Tobago","wpestate"),__("Tunisia","wpestate"),__("Turkey","wpestate"),__("Turkmenistan","wpestate"),__("Turks and Caicos Islands","wpestate"),__("Tuvalu","wpestate"),__("Uganda","wpestate"),__("Ukraine","wpestate"),__("United Arab Emirates","wpestate"),__("United Kingdom","wpestate"),__("United States","wpestate"),__("United States Minor Outlying Islands","wpestate"),__("Uruguay","wpestate"),__("Uzbekistan","wpestate"),__("Vanuatu","wpestate"),__("Venezuela","wpestate"),__("Vietnam","wpestate"),__("Virgin Islands (British)","wpestate"),__("Virgin Islands (U.S.)","wpestate"),__("Wallis and Futuna Islands","wpestate"),__("Western Sahara","wpestate"),__("Yemen","wpestate"),__("Zambia","wpestate"),__("Zimbabwe","wpestate"));

    $country_select = '<select id="property_country"  name="property_country" class="'.$class.'">';

    if ($selected == '') {
        $selected = get_option('wp_estate_general_country');
    }
    foreach ($countries as $country) {
        $country_select.='<option value="' . $country . '"';
        if ($selected == $country) {
            $country_select.='selected="selected"';
        }
        $country_select.='>' . $country . '</option>';
    }

    $country_select.='</select>';
    return $country_select;
}
endif; // end   wpestate_country_list 



if( !function_exists('wpestate_country_list_search') ): 
function wpestate_country_list_search($selected) {
    $countries = array(__("Afghanistan","wpestate"),__("Albania","wpestate"),__("Algeria","wpestate"),__("American Samoa","wpestate"),__("Andorra","wpestate"),__("Angola","wpestate"),__("Anguilla","wpestate"),__("Antarctica","wpestate"),__("Antigua and Barbuda","wpestate"),__("Argentina","wpestate"),__("Armenia","wpestate"),__("Aruba","wpestate"),__("Australia","wpestate"),__("Austria","wpestate"),__("Azerbaijan","wpestate"),__("Bahamas","wpestate"),__("Bahrain","wpestate"),__("Bangladesh","wpestate"),__("Barbados","wpestate"),__("Belarus","wpestate"),__("Belgium","wpestate"),__("Belize","wpestate"),__("Benin","wpestate"),__("Bermuda","wpestate"),__("Bhutan","wpestate"),__("Bolivia","wpestate"),__("Bosnia and Herzegowina","wpestate"),__("Botswana","wpestate"),__("Bouvet Island","wpestate"),__("Brazil","wpestate"),__("British Indian Ocean Territory","wpestate"),__("Brunei Darussalam","wpestate"),__("Bulgaria","wpestate"),__("Burkina Faso","wpestate"),__("Burundi","wpestate"),__("Cambodia","wpestate"),__("Cameroon","wpestate"),__("Canada","wpestate"),__("Cape Verde","wpestate"),__("Cayman Islands","wpestate"),__("Central African Republic","wpestate"),__("Chad","wpestate"),__("Chile","wpestate"),__("China","wpestate"),__("Christmas Island","wpestate"),__("Cocos (Keeling) Islands","wpestate"),__("Colombia","wpestate"),__("Comoros","wpestate"),__("Congo","wpestate"),__("Congo, the Democratic Republic of the","wpestate"),__("Cook Islands","wpestate"),__("Costa Rica","wpestate"),__("Cote d'Ivoire","wpestate"),__("Croatia (Hrvatska)","wpestate"),__("Cuba","wpestate"),__("Cyprus","wpestate"),__("Czech Republic","wpestate"),__("Denmark","wpestate"),__("Djibouti","wpestate"),__("Dominica","wpestate"),__("Dominican Republic","wpestate"),__("East Timor","wpestate"),__("Ecuador","wpestate"),__("Egypt","wpestate"),__("El Salvador","wpestate"),__("Equatorial Guinea","wpestate"),__("Eritrea","wpestate"),__("Estonia","wpestate"),__("Ethiopia","wpestate"),__("Falkland Islands (Malvinas)","wpestate"),__("Faroe Islands","wpestate"),__("Fiji","wpestate"),__("Finland","wpestate"),__("France","wpestate"),__("France Metropolitan","wpestate"),__("French Guiana","wpestate"),__("French Polynesia","wpestate"),__("French Southern Territories","wpestate"),__("Gabon","wpestate"),__("Gambia","wpestate"),__("Georgia","wpestate"),__("Germany","wpestate"),__("Ghana","wpestate"),__("Gibraltar","wpestate"),__("Greece","wpestate"),__("Greenland","wpestate"),__("Grenada","wpestate"),__("Guadeloupe","wpestate"),__("Guam","wpestate"),__("Guatemala","wpestate"),__("Guinea","wpestate"),__("Guinea-Bissau","wpestate"),__("Guyana","wpestate"),__("Haiti","wpestate"),__("Heard and Mc Donald Islands","wpestate"),__("Holy See (Vatican City State)","wpestate"),__("Honduras","wpestate"),__("Hong Kong","wpestate"),__("Hungary","wpestate"),__("Iceland","wpestate"),__("India","wpestate"),__("Indonesia","wpestate"),__("Iran (Islamic Republic of)","wpestate"),__("Iraq","wpestate"),__("Ireland","wpestate"),__("Israel","wpestate"),__("Italy","wpestate"),__("Jamaica","wpestate"),__("Japan","wpestate"),__("Jordan","wpestate"),__("Kazakhstan","wpestate"),__("Kenya","wpestate"),__("Kiribati","wpestate"),__("Korea, Democratic People's Republic of","wpestate"),__("Korea, Republic of","wpestate"),__("Kuwait","wpestate"),__("Kyrgyzstan","wpestate"),__("Lao, People's Democratic Republic","wpestate"),__("Latvia","wpestate"),__("Lebanon","wpestate"),__("Lesotho","wpestate"),__("Liberia","wpestate"),__("Libyan Arab Jamahiriya","wpestate"),__("Liechtenstein","wpestate"),__("Lithuania","wpestate"),__("Luxembourg","wpestate"),__("Macau","wpestate"),__("Macedonia (FYROM)","wpestate"),__("Madagascar","wpestate"),__("Malawi","wpestate"),__("Malaysia","wpestate"),__("Maldives","wpestate"),__("Mali","wpestate"),__("Malta","wpestate"),__("Marshall Islands","wpestate"),__("Martinique","wpestate"),__("Mauritania","wpestate"),__("Mauritius","wpestate"),__("Mayotte","wpestate"),__("Mexico","wpestate"),__("Micronesia, Federated States of","wpestate"),__("Moldova, Republic of","wpestate"),__("Monaco","wpestate"),__("Mongolia","wpestate"),__("Montserrat","wpestate"),__("Morocco","wpestate"),__("Mozambique","wpestate"),__("Montenegro","wpestate"),__("Myanmar","wpestate"),__("Namibia","wpestate"),__("Nauru","wpestate"),__("Nepal","wpestate"),__("Netherlands","wpestate"),__("Netherlands Antilles","wpestate"),__("New Caledonia","wpestate"),__("New Zealand","wpestate"),__("Nicaragua","wpestate"),__("Niger","wpestate"),__("Nigeria","wpestate"),__("Niue","wpestate"),__("Norfolk Island","wpestate"),__("Northern Mariana Islands","wpestate"),__("Norway","wpestate"),__("Oman","wpestate"),__("Pakistan","wpestate"),__("Palau","wpestate"),__("Panama","wpestate"),__("Papua New Guinea","wpestate"),__("Paraguay","wpestate"),__("Peru","wpestate"),__("Philippines","wpestate"),__("Pitcairn","wpestate"),__("Poland","wpestate"),__("Portugal","wpestate"),__("Puerto Rico","wpestate"),__("Qatar","wpestate"),__("Reunion","wpestate"),__("Romania","wpestate"),__("Russian Federation","wpestate"),__("Rwanda","wpestate"),__("Saint Kitts and Nevis","wpestate"),__("Saint Martin","wpestate"),__("Saint Lucia","wpestate"),__("Saint Vincent and the Grenadines","wpestate"),__("Samoa","wpestate"),__("San Marino","wpestate"),__("Sao Tome and Principe","wpestate"),__("Saudi Arabia","wpestate"),__("Senegal","wpestate"),__("Seychelles","wpestate"),__("Serbia","wpestate"),__("Sierra Leone","wpestate"),__("Singapore","wpestate"),__("Slovakia (Slovak Republic)","wpestate"),__("Slovenia","wpestate"),__("Solomon Islands","wpestate"),__("Somalia","wpestate"),__("South Africa","wpestate"),__("South Georgia and the South Sandwich Islands","wpestate"),__("Spain","wpestate"),__("Sri Lanka","wpestate"),__("St. Helena","wpestate"),__("St. Pierre and Miquelon","wpestate"),__("Sudan","wpestate"),__("Suriname","wpestate"),__("Svalbard and Jan Mayen Islands","wpestate"),__("Swaziland","wpestate"),__("Sweden","wpestate"),__("Switzerland","wpestate"),__("Syrian Arab Republic","wpestate"),__("Taiwan, Province of China","wpestate"),__("Tajikistan","wpestate"),__("Tanzania, United Republic of","wpestate"),__("Thailand","wpestate"),__("Togo","wpestate"),__("Tokelau","wpestate"),__("Tonga","wpestate"),__("Trinidad and Tobago","wpestate"),__("Tunisia","wpestate"),__("Turkey","wpestate"),__("Turkmenistan","wpestate"),__("Turks and Caicos Islands","wpestate"),__("Tuvalu","wpestate"),__("Uganda","wpestate"),__("Ukraine","wpestate"),__("United Arab Emirates","wpestate"),__("United Kingdom","wpestate"),__("United States","wpestate"),__("United States Minor Outlying Islands","wpestate"),__("Uruguay","wpestate"),__("Uzbekistan","wpestate"),__("Vanuatu","wpestate"),__("Venezuela","wpestate"),__("Vietnam","wpestate"),__("Virgin Islands (British)","wpestate"),__("Virgin Islands (U.S.)","wpestate"),__("Wallis and Futuna Islands","wpestate"),__("Western Sahara","wpestate"),__("Yemen","wpestate"),__("Zambia","wpestate"),__("Zimbabwe","wpestate"));
    $country_select_list='';
    foreach ($countries as $country) {
        $country_select_list.='<li role="presentation" data-value="'.$country.'">'.$country.'</li>';
    }
    


    return $country_select_list;
}
endif; // end   wpestate_country_list 


if( !function_exists('wpestate_agent_list') ):
function wpestate_agent_list($mypost) {
    return $agent_list;
}
endif; // end   wpestate_agent_list








///////////////////////////////////////////////////////////////////////////////////////////////////////////
///  Manage property lists
///////////////////////////////////////////////////////////////////////////////////////////////////////////
add_filter( 'manage_edit-estate_property_columns', 'wpestate_my_columns' );

if( !function_exists('wpestate_my_columns') ):
function wpestate_my_columns( $columns ) {
    $slice=array_slice($columns,2,2);
    unset( $columns['comments'] );
    unset( $slice['comments'] );
    $splice=array_splice($columns, 2);   
    $columns['estate_thumb']    = __('Image','wpestate');
    $columns['estate_city']     = __('City','wpestate');
    $columns['estate_action']   = __('Action','wpestate');
    $columns['estate_category'] = __( 'Category','wpestate');
    $columns['estate_autor']    = __('User','wpestate');
    $columns['estate_status']   = __('Status','wpestate');
    $columns['estate_price']    = __('Price','wpestate');
    $columns['estate_featured'] = __('Featured','wpestate');
    return  array_merge($columns,array_reverse($slice));
}
endif; // end   wpestate_my_columns  


add_action( 'manage_posts_custom_column', 'wpestate_populate_columns' );
if( !function_exists('wpestate_populate_columns') ):
function wpestate_populate_columns( $column ) {
    
    global $post;
    $the_id= $post->ID;

    
    if ( 'estate_agent_email' == $column ) {
        $agent_email = get_post_meta($the_id , 'agent_email', true);
        echo $agent_email;
    } else if ( 'estate_agent_phone' == $column ) {
        $agent_phone = get_post_meta($the_id , 'agent_phone', true);
        $agent_mobile = get_post_meta($the_id , 'agent_mobile', true);
        echo $agent_phone.' / '.$agent_mobile;
    } else if ( 'estate_agent_city' == $column ) {
        $estate_action = get_the_term_list( $the_id, 'property_city_agent', '', ', ', '');
        echo ($estate_action);
    } else if ( 'estate_agent_action' == $column ) {
        $estate_action = get_the_term_list( $the_id, 'property_action_category_agent', '', ', ', '');
        echo ($estate_action);
    }else if ( 'estate_agent_category' == $column ) {
        $estate_category =  get_the_term_list( $the_id, 'property_category_agent', '', ', ', '');
        echo ($estate_category) ;
    }else if ( 'estate_status' == $column ) {
        $estate_status = get_post_status($the_id); 
        if($estate_status=='publish'){
            echo __('published','wpestate');
        }else{
            echo esc_html($estate_status);
        }
        
        $pay_status    = get_post_meta($the_id, 'pay_status', true);
        if($pay_status!=''){
            echo " | ";
            if($pay_status=='paid'){
                _e('PAID','wpestate');
            }else{
                _e('Not Paid','wpestate');
            }
        }
        
    }else if ( 'estate_autor' == $column ) {
       $temp_id= $post->ID;
        $user_id=wpsestate_get_author($the_id);
        $estate_autor = get_the_author_meta('display_name',$user_id);
        echo '<a href="'.get_edit_user_link($user_id).'" >'.$estate_autor.'</a>';
      
        $post->ID=$temp_id=$the_id;
    }else if ( 'estate_thumb' == $column  || 'estate_agent_thumb' == $column) {
        $thumb_id           =   get_post_thumbnail_id( $the_id);
        $post_thumbnail_url =    wp_get_attachment_image_src($thumb_id, 'slider_thumb');
        echo '<img src="'.$post_thumbnail_url[0].'" style="width:100%;height:auto;">';
    }else if ( 'estate_city' == $column ) {
        $estate_city = get_the_term_list( $the_id, 'property_city', '', ', ', '');
        echo ($estate_city);
    }else if ( 'estate_action' == $column ) {
        $estate_action = get_the_term_list( $the_id, 'property_action_category', '', ', ', '');
        echo ($estate_action);
    }elseif ( 'estate_category' == $column ) {
        $estate_category =  get_the_term_list( $the_id, 'property_category', '', ', ', '');
        echo ($estate_category) ;
    }else if ( 'estate_price' == $column ) {
        $currency                   =   esc_html( get_option('wp_estate_currency_symbol', '') );
        $where_currency             =   esc_html( get_option('wp_estate_where_currency_symbol', '') );
        
        $price = floatval( get_post_meta($the_id, 'property_price', true) );
        if ($price != 0) {
           $price = number_format($price);

           if ($where_currency == 'before') {
               $price = $currency . ' ' . $price;
           } else {
               $price = $price . ' ' . $currency;
           }
        }else{
            $price='';
        }
        
        echo get_post_meta($the_id, 'property_label_before', true).' '.$price.' '. get_post_meta($the_id, 'property_label', true);
    }else if ( 'estate_featured' == $column ) {
        $estate_featured = get_post_meta($the_id, 'prop_featured', true); 
        if($estate_featured==1){
            $estate_featured=__('Yes','wpestate');
        }else{
            $estate_featured=__('No','wpestate'); 
        }
        echo esc_html($estate_featured);
    }
}
endif; // end   wpestate_populate_columns 





//'manage_edit-estate_property_columns
add_filter( 'manage_edit-estate_property_sortable_columns', 'wpestate_sort_me' );
if( !function_exists('wpestate_sort_me') ):
function wpestate_sort_me( $columns ) {
    $columns['estate_autor']        = 'estate_autor';
    $columns['estate_featured']       = 'estate_featured';
    $columns['estate_price']       = 'estate_price';
    return $columns;
}
endif; // end   wpestate_sort_me 


add_filter( 'request', 'bs_event_date_column_orderby' );
function bs_event_date_column_orderby( $vars ) {
    if ( isset( $vars['orderby'] ) && 'estate_featured' == $vars['orderby'] ) {
        $vars = array_merge( $vars, array(
            'meta_key' => 'prop_featured',
            'orderby' => 'meta_value_num'
        ) );
    }
    if ( isset( $vars['orderby'] ) && 'estate_price' == $vars['orderby'] ) {
        $vars = array_merge( $vars, array(
            'meta_key' => 'property_price',
            'orderby' => 'meta_value_num'
        ) );
    }
    
    
      if ( isset( $vars['orderby'] ) && 'estate_autor' == $vars['orderby'] ) {
        $vars = array_merge( $vars, array(
           
            'orderby' => 'author'
        ) );
    }
    
   

    return $vars;
}




///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Tie area with city
///////////////////////////////////////////////////////////////////////////////////////////////////////////
add_action( 'property_area_edit_form_fields',   'wpestate_property_area_callback_function', 10, 2);
add_action( 'property_area_add_form_fields',    'wpestate_property_area_callback_add_function', 10, 2 );  
add_action( 'created_property_area',            'wpestate_property_area_save_extra_fields_callback', 10, 2);
add_action( 'edited_property_area',             'wpestate_property_area_save_extra_fields_callback', 10, 2);
add_filter('manage_edit-property_area_columns', 'ST4_columns_head');  
add_filter('manage_property_area_custom_column','ST4_columns_content_taxonomy', 10, 3); 


if( !function_exists('ST4_columns_head') ):
function ST4_columns_head($new_columns) {   
 
    $new_columns = array(
        'cb'            => '<input type="checkbox" />',
        'name'          => __('Name','wpestate'),
        'city'          => __('City','wpestate'),
        'header_icon'   => '',
        'slug'          => __('Slug','wpestate'),
        'posts'         => __('Posts','wpestate')
        );
    
    
    return $new_columns;
} 
endif; // end   ST4_columns_head  


if( !function_exists('ST4_columns_content_taxonomy') ):
function ST4_columns_content_taxonomy($out, $column_name, $term_id) {  
    if ($column_name == 'city') {    
        $term_meta= get_option( "taxonomy_$term_id");
        print stripslashes( $term_meta['cityparent'] );
    }  
}  
endif; // end   ST4_columns_content_taxonomy  






if( !function_exists('wpestate_property_area_callback_add_function') ):
    function wpestate_property_area_callback_add_function($tag){
        if(is_object ($tag)){
            $t_id                       =   $tag->term_id;
            $term_meta                  =   get_option( "taxonomy_$t_id");
            $cityparent                 =   $term_meta['cityparent'] ? $term_meta['cityparent'] : ''; 
            $pagetax                    =   $term_meta['pagetax'] ? $term_meta['pagetax'] : '';
            $category_featured_image    =   $term_meta['category_featured_image'] ? $term_meta['category_featured_image'] : '';
            $category_tagline           =   $term_meta['category_tagline'] ? $term_meta['category_tagline'] : '';
            
            $category_attach_id         =   $term_meta['category_attach_id'] ? $term_meta['category_attach_id'] : '';
        }else{
            $cityparent                 =   wpestate_get_all_cities();
            $pagetax                    =   '';
            $category_featured_image    =   '';
            $category_tagline           =   '';
            $category_attach_id         =   '';
        }

        print'
            <div class="form-field">
            <label for="term_meta[cityparent]">'. esc_html__( 'Which city has this area','wpestate').'</label>
                <select name="term_meta[cityparent]" class="postform">  
                    '.$cityparent.'
                </select>
            </div>
            ';

         print'
            <div class="form-field">
            <label for="term_meta[pagetax]">'. esc_html__( 'Page id for this term','wpestate').'</label>
                <input type="text" name="term_meta[pagetax]" class="postform" value="'.$pagetax.'">  
            </div>

            <div class="form-field">
            <label for="term_meta[pagetax]">'. esc_html__( 'Featured Image','wpestate').'</label>
                <input id="category_featured_image" type="text" size="36" name="term_meta[category_featured_image]" value="'.$category_featured_image.'" />
                <input id="category_featured_image_button" type="button"  class="upload_button button category_featured_image_button" value="'.esc_html__( 'Upload Image','wpestate').'" />
                <input id="category_attach_id" type="hidden" size="36" name="term_meta[category_attach_id]" value="'.$category_attach_id.'" />

            </div> 


            <div class="form-field">
            <label for="term_meta[category_tagline]">'. esc_html__( 'Category Tagline','wpestate').'</label>
                <input id="category_featured_image" type="text" size="36" name="term_meta[category_tagline]" value="'.$category_tagline.'" />
            </div>  
            <input id="category_tax" type="hidden" size="36" name="term_meta[category_tax]" value="property_area" />
            ';
    }
endif; // end     




if( !function_exists('wpestate_property_area_callback_function') ):
    function wpestate_property_area_callback_function($tag){
        if(is_object ($tag)){
            $t_id                       =   $tag->term_id;
            $term_meta                  =   get_option( "taxonomy_$t_id");
            $cityparent                 =   $term_meta['cityparent'] ? $term_meta['cityparent'] : ''; 
            $pagetax                    =   '';
            $category_featured_image    =   '';
            $category_tagline           =   '';
            $category_attach_id         =   '';
            if(isset($term_meta['pagetax'] )){
                $pagetax                    =   $term_meta['pagetax'] ? $term_meta['pagetax'] : '';
            }
            
            if(isset($term_meta['category_featured_image'] )){
                $category_featured_image    =   $term_meta['category_featured_image'] ? $term_meta['category_featured_image'] : '';
            }
            
            if(isset($term_meta['category_tagline'] )){
                $category_tagline           =   $term_meta['category_tagline'] ? $term_meta['category_tagline'] : '';
            }
            
            $category_tagline           =   stripslashes($category_tagline);
            if(isset($term_meta['category_attach_id'] )){
                $category_attach_id         =   $term_meta['category_attach_id'] ? $term_meta['category_attach_id'] : '';
            }
            
            $cityparent =   wpestate_get_all_cities($cityparent);
        }else{
            $cityparent                 =   wpestate_get_all_cities();
            $pagetax                    =   '';
            $category_featured_image    =   '';
            $category_tagline           =   '';
            $category_attach_id         =   '';

        }

        print'
            <table class="form-table">
            <tbody>
                    <tr class="form-field">
                            <th scope="row" valign="top"><label for="term_meta[cityparent]">'. esc_html__( 'Which city has this area','wpestate').'</label></th>
                            <td> 
                                <select name="term_meta[cityparent]" class="postform">  
                                 '.$cityparent.'
                                    </select>
                                <p class="description">'.esc_html__( 'City that has this area','wpestate').'</p>
                            </td>
                    </tr>

                   <tr class="form-field">
                            <th scope="row" valign="top"><label for="term_meta[pagetax]">'.esc_html__( 'Page id for this term','wpestate').'</label></th>
                            <td> 
                                <input type="text" name="term_meta[pagetax]" class="postform" value="'.$pagetax.'">  
                                <p class="description">'.esc_html__( 'Page id for this term','wpestate').'</p>
                            </td>
                    </tr>

                    <tr valign="top">
                        <th scope="row"><label for="logo_image">'.esc_html__( 'Featured Image','wpestate').'</label></th>
                        <td>
                            <input id="category_featured_image" type="text" size="36" name="term_meta[category_featured_image]" value="'.$category_featured_image.'" />
                            <input id="category_featured_image_button" type="button"  class="upload_button button category_featured_image_button" value="'.esc_html__( 'Upload Image','wpestate').'" />
                            <input id="category_attach_id" type="hidden" size="36" name="term_meta[category_attach_id]" value="'.$category_attach_id.'" />
                        </td>
                    </tr> 

                    <tr valign="top">
                        <th scope="row"><label for="term_meta[category_tagline]">'. esc_html__( 'Category Tagline','wpestate').'</label></th>
                        <td>
                          <input id="category_featured_image" type="text" size="36" name="term_meta[category_tagline]" value="'.$category_tagline.'" />
                        </td>
                    </tr> 


                    <input id="category_tax" type="hidden" size="36" name="term_meta[category_tax]" value="property_area" />




              </tbody>
             </table>';
    }
endif; // end     




if( !function_exists('wpestate_get_all_cities') ): 
function wpestate_get_all_cities($selected=''){
    $taxonomy       =   'property_city';
    $args = array(
        'hide_empty'    => false
    );
    $tax_terms      =   get_terms($taxonomy,$args);
    $select_city    =   '';
    
    foreach ($tax_terms as $tax_term) {             
        $select_city.= '<option value="' . $tax_term->name.'" ';
        if($tax_term->name == $selected){
            $select_city.= ' selected="selected" ';
        }
        $select_city.= ' >' . $tax_term->name . '</option>'; 
    }
    return $select_city;
}
endif; // end   wpestate_get_all_cities 





if( !function_exists('wpestate_property_area_save_extra_fields_callback') ):
    function wpestate_property_area_save_extra_fields_callback($term_id ){
          if ( isset( $_POST['term_meta'] ) ) {
            $t_id = $term_id;
            $term_meta = get_option( "taxonomy_$t_id");
            $cat_keys = array_keys($_POST['term_meta']);
            $allowed_html   =   array();
                foreach ($cat_keys as $key){
                    $key=sanitize_key($key);
                    if (isset($_POST['term_meta'][$key])){
                        $term_meta[$key] =  wp_kses( $_POST['term_meta'][$key],$allowed_html);
                    }
                }
            //save the option array
            update_option( "taxonomy_$t_id", $term_meta );
        }
    }
endif; // end     


add_action( 'init', 'wpestate_my_custom_post_status' );
if( !function_exists('wpestate_my_custom_post_status') ):
function wpestate_my_custom_post_status(){
    register_post_status( 'expired', array(
            'label'                     => __( 'expired', 'wpestate' ),
            'public'                    => true,
            'exclude_from_search'       => false,
            'show_in_admin_all_list'    => true,
            'show_in_admin_status_list' => true,
            'label_count'               => _n_noop( 'Membership Expired <span class="count">(%s)</span>', 'Membership Expired <span class="count">(%s)</span>' ),
    ) ,
    register_post_status( 'disabled', array(
            'label'                     => esc_html__(  'disabled', 'wpestate' ),
            'public'                    => false,
            'exclude_from_search'       => false,
            'show_in_admin_all_list'    => true,
            'show_in_admin_status_list' => true,
            'label_count'               => _n_noop( 'Disabled by user <span class="count">(%s)</span>', 'Disabled by user <span class="count">(%s)</span>','wpestate' ),
    )
    
    ) 

                
    );
}
endif; // end   wpestate_my_custom_post_status  


/////////////////////////////////////////////////////////
// customizable taxonomy header
/////////////////////////////////////////////////////////

add_action( 'property_city_edit_form_fields',   'wpestate_property_city_callback_function', 10, 2);
add_action( 'property_city_add_form_fields',    'wpestate_property_city_callback_add_function', 10, 2 );  
add_action( 'created_property_city',            'wpestate_property_city_save_extra_fields_callback', 10, 2);
add_action( 'edited_property_city',             'wpestate_property_city_save_extra_fields_callback', 10, 2);

if( !function_exists('wpestate_property_city_callback_function') ):
    function wpestate_property_city_callback_function($tag){
        if(is_object ($tag)){
            $t_id                       =   $tag->term_id;
            $term_meta                  =   get_option( "taxonomy_$t_id");
            $pagetax                    =   $term_meta['pagetax'] ? $term_meta['pagetax'] : '';
            $category_featured_image    =   $term_meta['category_featured_image'] ? $term_meta['category_featured_image'] : '';
            $category_tagline           =   $term_meta['category_tagline'] ? $term_meta['category_tagline'] : '';
            $category_tagline           =   stripslashes($category_tagline);
            $category_attach_id         =   $term_meta['category_attach_id'] ? $term_meta['category_attach_id'] : '';
        }else{
            $pagetax                    =   '';
            $category_featured_image    =   '';
            $category_tagline           =   '';
            $category_attach_id         =   '';
        }

        print'
        <table class="form-table">
        <tbody>    
            <tr class="form-field">
                <th scope="row" valign="top"><label for="term_meta[pagetax]">'.esc_html__( 'Page id for this term','wpestate').'</label></th>
                <td> 
                    <input type="text" name="term_meta[pagetax]" class="postform" value="'.$pagetax.'">  
                    <p class="description">'.esc_html__( 'Page id for this term','wpestate').'</p>
                </td>

                <tr valign="top">
                    <th scope="row"><label for="category_featured_image">'.esc_html__( 'Featured Image','wpestate').'</label></th>
                    <td>
                        <input id="category_featured_image" type="text" class="postform" size="36" name="term_meta[category_featured_image]" value="'.$category_featured_image.'" />
                        <input id="category_featured_image_button" type="button"  class="upload_button button category_featured_image_button" value="'.esc_html__( 'Upload Image','wpestate').'" />
                        <input id="category_attach_id" type="hidden" size="36" name="term_meta[category_attach_id]" value="'.$category_attach_id.'" />
                    </td>
                </tr> 

                <tr valign="top">
                    <th scope="row"><label for="term_meta[category_tagline]">'. esc_html__( 'Category Tagline','wpestate').'</label></th>
                    <td>
                        <input id="category_tagline" type="text" size="36" name="term_meta[category_tagline]" value="'.$category_tagline.'" />
                    </td>
                </tr> 



                <input id="category_tax" type="hidden" size="36" name="term_meta[category_tax]" value="property_city" />


            </tr>
        </tbody>
        </table>';
    }
endif;



if( !function_exists('wpestate_property_city_callback_add_function') ):
    function wpestate_property_city_callback_add_function($tag){
        if(is_object ($tag)){
            $t_id                       =   $tag->term_id;
            $term_meta                  =   get_option( "taxonomy_$t_id");
            $pagetax                    =   $term_meta['pagetax'] ? $term_meta['pagetax'] : '';
            $category_featured_image    =   $term_meta['category_featured_image'] ? $term_meta['category_featured_image'] : '';
            $category_tagline           =   $term_meta['category_tagline'] ? $term_meta['category_tagline'] : '';
            $category_attach_id         =   $term_meta['category_attach_id'] ? $term_meta['category_attach_id'] : '';
        }else{
            $pagetax                    =   '';
            $category_featured_image    =   '';
            $category_tagline           =   '';
            $category_attach_id         =   '';

        }

        print'
        <div class="form-field">
        <label for="term_meta[pagetax]">'. esc_html__( 'Page id for this term','wpestate').'</label>
            <input type="text" name="term_meta[pagetax]" class="postform" value="'.$pagetax.'">  
        </div>

        <div class="form-field">
            <label for="term_meta[pagetax]">'. esc_html__( 'Featured Image','wpestate').'</label>
            <input id="category_featured_image" type="text" size="36" name="term_meta[category_featured_image]" value="'.$category_featured_image.'" />
            <input id="category_featured_image_button" type="button"  class="upload_button button category_featured_image_button" value="'.esc_html__( 'Upload Image','wpestate').'" />
           <input id="category_attach_id" type="hidden" size="36" name="term_meta[category_attach_id]" value="'.$category_attach_id.'" />

        </div>     

        <div class="form-field">
        <label for="term_meta[category_tagline]">'. esc_html__( 'Category Tagline','wpestate').'</label>
            <input id="category_tagline" type="text" size="36" name="term_meta[category_tagline]" value="'.$category_tagline.'" />
        </div> 
        <input id="category_tax" type="hidden" size="36" name="term_meta[category_tax]" value="property_city" />
        ';
    }
endif;

if( !function_exists('wpestate_property_city_save_extra_fields_callback') ):
    function wpestate_property_city_save_extra_fields_callback($term_id ){
        if ( isset( $_POST['term_meta'] ) ) {
            $t_id = $term_id;
            $term_meta = get_option( "taxonomy_$t_id");
            $cat_keys = array_keys($_POST['term_meta']);
            $allowed_html   =   array();
                foreach ($cat_keys as $key){
                    $key=sanitize_key($key);
                    if (isset($_POST['term_meta'][$key])){
                        $term_meta[$key] =  wp_kses( $_POST['term_meta'][$key],$allowed_html);
                    }
                }
            //save the option array
             update_option( "taxonomy_$t_id", $term_meta );
        }
    }
endif;


if( !function_exists('wpestate_listing_full_width_slider') ):
function wpestate_listing_full_width_slider($prop_id){
    $background_image_style='';
    $counter_lightbox=0;
    if( has_post_thumbnail($prop_id) ){
        $counter_lightbox++;
        $post_thumbnail_id  =   get_post_thumbnail_id( $prop_id );
        $full_prty          =   wp_get_attachment_image_src($post_thumbnail_id, 'full');
        $thumb              =   wp_get_attachment_image_src($post_thumbnail_id, 'slider_thumb');
    } 
    
    $items = '<div class="item active">
            <div class="propery_listing_main_image lightbox_trigger" style="background-image:url('.$full_prty[0].')" data-slider-no="'.$counter_lightbox.'"></div>
            <div class="carousel-caption">
            </div>
        </div>';
    $indicator = '<li data-target="#carousel-property-page-header" data-slide-to="0" class="active"><div class="carousel-property-page-header-overalay"></div><img src="'.$thumb[0].'"></li>';
    
    $arguments      = array(
        'numberposts'       => -1,
        'post_type'         => 'attachment',
        'post_mime_type'    => 'image',
        'post_parent'       => $prop_id,
        'post_status'       => null,
        'exclude'           => get_post_thumbnail_id(),
        'orderby'           => 'menu_order',
        'order'             => 'ASC'
    );
                
    $post_attachments   = get_posts($arguments);
    $slides='';

    $no_slides = 0;
    foreach ($post_attachments as $attachment) { 
        $no_slides++;
        $counter_lightbox++;
        $preview    =   wp_get_attachment_image_src($attachment->ID, 'full');
        $thumb      =   wp_get_attachment_image_src($attachment->ID, 'slider_thumb');
        $indicator .= '<li data-target="#carousel-property-page-header" data-slide-to="'.$no_slides.'" class=""><div class="carousel-property-page-header-overalay"></div><img src="'.$thumb[0].'"></li>';    
        $items .= '<div class="item ">
            <div class="propery_listing_main_image lightbox_trigger" data-slider-no="'.$counter_lightbox.'" style="background-image:url('.$preview[0].')" ></div>
            <div class="carousel-caption">
            </div>
        </div>';            
    }

    
    
    print '<div id="carousel-property-page-header" class="carousel slide propery_listing_main_image" data-interval="false" data-ride="carousel">

 

    <div class="carousel-inner" role="listbox">
        '.$items.'
    </div>
    
    <div class="carousel-indicators-wrapper-header-prop">
        <ol class="carousel-indicators">
            '.$indicator.'
        </ol>
    </div>
    
    <!-- Controls -->
    <a class="left carousel-control" href="#carousel-property-page-header" role="button" data-slide="prev">
      <i class="fa fa-angle-left"></i>
    </a>
    <a class="right carousel-control" href="#carousel-property-page-header" role="button" data-slide="next">
       <i class="fa fa-angle-right"></i>
    </a>

    </div>';
    
}
endif;




///////////////////////////////////////////////////////////////////////////////////////////////////////////
///  Property details  function
///////////////////////////////////////////////////////////////////////////////////////////////////////////
if( !function_exists('estate_propery_subunits') ):

function estate_propery_subunits() {
    global $post;
    wp_nonce_field(plugin_basename(__FILE__), 'estate_property_noncename');
    
    $mypost             =   $post->ID;
    print'            
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr>
        <td width="100%" valign="top" align="left">
            <p class="meta-options">';
    
            $property_subunits_master=intval(get_post_meta($mypost, 'property_subunits_master', true));  
          
            if($property_subunits_master!=0 && $property_subunits_master!=$post->ID){
            print '<span>'.__('Already Subunit for','wpestate').' <a href="'.get_permalink($property_subunits_master).'" target="_blank">'.get_the_title($property_subunits_master).'</a></span></br></br>';
            }
            print'
            <input type="hidden" name="property_has_subunits" value="">
            <input type="checkbox"  id="property_has_subunits" name="property_has_subunits" value="1" ';
                if (intval(get_post_meta($mypost, 'property_has_subunits', true)) == 1) {
                    print'checked="checked"';
                }
                print' />
            <label class="checklabel" for="property_has_subunits">'.__('Enable ','wpestate').'</label>
            </p>
        </td>
    </tr>
    <tr>
        <td width="100%" valign="top" align="left">
            <p class="meta-options">';
           
                
            print'<span>'.__('Due to speed & usability reasons we only show your first 50 properties. If the Listing you want to add as subunit is not in this list please add the id manually.','wpestate').'</span>
            <label for="property_subunits_list">'.__('Select Subunits From the list: ','wpestate').'</label><br />';
           // <input type="text" id="property_subunits_list" size="40" name="property_subunits_list" value="' . esc_html(get_post_meta($mypost, 'property_subunits_list', true)) . '">
            $property_subunits_list   =  get_post_meta($mypost, 'property_subunits_list', true); 
            
           
            $post__not_in   =   array();
            $post__not_in[] =   $mypost;
            $args = array(       
                        'post_type'                 =>  'estate_property',
                        'post_status'               =>  'publish',
                        'nopaging'                  =>  'true',
                        'cache_results'             =>  false,
                        'update_post_meta_cache'    =>  false,
                        'update_post_term_cache'    =>  false,
                        'post__not_in'              =>  $post__not_in,
                       
                );

            $recent_posts = new WP_Query($args);
            print '<select name="property_subunits_list[]"  style="height:350px;" id="property_subunits_list"  multiple="multiple">';
            while ($recent_posts->have_posts()): $recent_posts->the_post();
                 $theid=get_the_ID();
                 print '<option value="'.$theid.'" ';
                 if( is_array($property_subunits_list) && in_array($theid, $property_subunits_list) ){
                     print ' selected="selected" ';
                 }
                 print'>'.get_the_title().'</option>';
            endwhile;
            wp_reset_postdata();
            $recent_posts->reset_postdata();
            $post->ID=$mypost;
            print '</select>';
            print'
            </p>
        </td>
    </tr>
    <tr>
        
        <td width="100%" valign="top" align="left">
            <p class="meta-options">
            <label for="property_subunits_list_manual">'.__('Or add the ids separated by comma. ','wpestate').'</label><br />
            <textarea id="property_subunits_list_manual" size="40" name="property_subunits_list_manual" >' . esc_html(get_post_meta($mypost, 'property_subunits_list_manual', true)) . '</textarea>
            </p>
        </td>
    </tr>
    </table>
    ';
}
endif; // end  

$restrict_manage_posts = function($post_type, $taxonomy) {
    return function() use($post_type, $taxonomy) {
        global $typenow;

        if($typenow == $post_type) {
            $selected = isset($_GET[$taxonomy]) ? $_GET[$taxonomy] : '';
            $info_taxonomy = get_taxonomy($taxonomy);

            wp_dropdown_categories(array(
                'show_option_all'   => __("Show All {$info_taxonomy->label}"),
                'taxonomy'          => $taxonomy,
                'name'              => $taxonomy,
                'orderby'           => 'name',
                'selected'          => $selected,
                'show_count'        => TRUE,
                'hide_empty'        => TRUE,
                'hierarchical'      => true
            ));

        }

    };

};

$parse_query = function($post_type, $taxonomy) {

    return function($query) use($post_type, $taxonomy) {
        global $pagenow;

        $q_vars = &$query->query_vars;

        if( $pagenow == 'edit.php'
            && isset($q_vars['post_type']) && $q_vars['post_type'] == $post_type
            && isset($q_vars[$taxonomy])
            && is_numeric($q_vars[$taxonomy]) && $q_vars[$taxonomy] != 0
        ) {
            $term = get_term_by('id', $q_vars[$taxonomy], $taxonomy);
            $q_vars[$taxonomy] = $term->slug;
        }

    };

};

add_action('restrict_manage_posts', $restrict_manage_posts('estate_property', 'property_category') );
add_filter('parse_query', $parse_query('estate_property', 'property_category') );

add_action('restrict_manage_posts', $restrict_manage_posts('estate_property', 'property_action_category') );
add_filter('parse_query', $parse_query('estate_property', 'property_action_category') );


add_action('restrict_manage_posts', $restrict_manage_posts('estate_property', 'property_city') );
add_filter('parse_query', $parse_query('estate_property', 'property_city') );
?>