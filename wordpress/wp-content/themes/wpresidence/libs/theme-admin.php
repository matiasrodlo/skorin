<?php
if( !function_exists('wpestate_new_general_set') ):
function wpestate_new_general_set() {  
   if($_SERVER['REQUEST_METHOD'] === 'POST'){	

        $allowed_html   =   array();
        
        // cusotm fields
        if( isset( $_POST['add_field_name'] ) ){
            $new_custom=array();  
            foreach( $_POST['add_field_name'] as $key=>$value ){
                $temp_array=array();
                $temp_array[0]=$value;
                $temp_array[1]= wp_kses( $_POST['add_field_label'][sanitize_key($key)] ,$allowed_html);
                $temp_array[2]= wp_kses( $_POST['add_field_type'][sanitize_key($key)] ,$allowed_html);
                $temp_array[3]= wp_kses ( $_POST['add_field_order'][sanitize_key($key)],$allowed_html);
                $temp_array[4]=  ( $_POST['add_dropdown_order'][sanitize_key($key)]);
                $new_custom[]=$temp_array;
            }

          
            usort($new_custom,"wpestate_sorting_function");
            update_option( 'wp_estate_custom_fields', $new_custom );   
        }
       
       // multiple currencies
        if( isset( $_POST['add_curr_name'] ) ){
            foreach( $_POST['add_curr_name'] as $key=>$value ){
                $temp_array=array();
                $temp_array[0]=$value;
                $temp_array[1]= wp_kses( $_POST['add_curr_label'][sanitize_key($key)] ,$allowed_html);
                $temp_array[2]= wp_kses( $_POST['add_curr_value'][sanitize_key($key)] ,$allowed_html);
                $temp_array[3]= wp_kses( $_POST['add_curr_order'][sanitize_key($key)] ,$allowed_html);
                $new_custom_cur[]=$temp_array;
            }
            
            update_option( 'wp_estate_multi_curr', $new_custom_cur );   

       }else{
           
       }

       
       

        if( isset( $_POST['theme_slider'] ) ){
            update_option( 'wp_estate_theme_slider', true);  
        }
        
       
        $permission_array=array(
            'add_field_name',
            'add_field_label',
            'add_field_type',
            'add_field_order',
            'adv_search_how',
            'adv_search_what',
            'adv_search_label',
        );
        
        $tags_array=array(
            'co_address',
            'direct_payment_details',
            'new_user',
            'admin_new_user',
            'purchase_activated',
            'password_reset_request',
            'password_reseted',
            'purchase_activated',
            'approved_listing',
            'new_wire_transfer',
            'admin_new_wire_transfer',
            'admin_expired_listing',
            'matching_submissions',
            'paid_submissions',
            'featured_submission',
            'account_downgraded',
            'membership_cancelled',
            'downgrade_warning',
            'free_listing_expired',
            'new_listing_submission' ,
            'listing_edit',
            'recurring_payment',
            'subject_new_user',
            'subject_admin_new_user',
            'subject_purchase_activated',
            'subject_password_reset_request',
            'subject_password_reseted',
            'subject_purchase_activated',
            'subject_approved_listing',
            'subject_new_wire_transfer',
            'subject_admin_new_wire_transfer',
            'subject_admin_expired_listing',
            'subject_matching_submissions',
            'subject_paid_submissions',
            'subject_featured_submission',
            'subject_account_downgraded',
            'subject_membership_cancelled',
            'subject_downgrade_warning',
            'subject_free_listing_expired',
            'subject_new_listing_submission' ,
            'subject_listing_edit',
            'subject_recurring_payment'
        );
        
        
        //$variable!='add_field_name'&& $variable!='add_field_label' && $variable!='add_field_type' && $variable!='add_field_order' && $variable!= 'adv_search_how' && $variable!='adv_search_what' && $variable!='adv_search_label'
        foreach($_POST as $variable=>$value){	
            if ($variable!='submit'){
                if (!in_array($variable, $permission_array)){
                    $variable   =   sanitize_key($variable);
                    if( in_array($variable, $tags_array) ){
                        $allowed_html_br=array(
                                'br' => array(),
                                'em' => array(),
                                'strong' => array()
                        );
                        $postmeta   =   wp_kses($value,$allowed_html_br);
                    }else{
                        $postmeta   =   wp_kses($value,$allowed_html);
                    
                    }   
                    update_option( wpestate_limit64('wp_estate_'.$variable), $postmeta );                
                }else{
                
                    update_option( 'wp_estate_'.$variable, $value );
                }	
            }	
        }
        
        if( isset($_POST['is_custom']) && $_POST['is_custom']== 1 && !isset($_POST['add_field_name']) ){
            update_option( 'wp_estate_custom_fields', '' ); 
        }
        
        if( isset($_POST['is_custom_cur']) && $_POST['is_custom_cur']== 1 && !isset($_POST['add_curr_name']) ){
            update_option( 'wp_estate_multi_curr', '' );
        }
    
        if (isset($_POST['show_save_search'])){
            $allowed_html=array();
            $show_save_search= wp_kses( $_POST['show_save_search'],$allowed_html );
            $search_alert= wp_kses( $_POST['search_alert'],$allowed_html );
            wp_estate_schedule_email_events( $show_save_search,$search_alert);
          
        }
        
       
        if ( isset( $_POST['paid_submission']) ){
            if ( $_POST['paid_submission']=='membership'){
                wp_estate_schedule_user_check();  
            }else{
                wp_clear_scheduled_hook('wpestate_check_for_users_event');
            }
        }
        
        if ( isset($_POST['auto_curency']) ){
            if( $_POST['auto_curency']=='yes' ){
                wp_estate_enable_load_exchange();
            }else{
                wp_clear_scheduled_hook('wpestate_load_exchange_action');
            }
        }
        
        if(isset($_POST['url_rewrites'])){
            flush_rewrite_rules();
        }
        
 
        if ( isset( $_POST['is_submit_page'] ) && $_POST['is_submit_page']== 1 ){
            
            if( !isset($_POST['mandatory_page_fields'])){
                update_option('wp_estate_mandatory_page_fields','');
            } 
            if( !isset($_POST['submission_page_fields'])){
                update_option('wp_estate_submission_page_fields','');
            }             
            
        }
       
       
        
}
    

    
$allowed_html   =   array();  
$active_tab = isset( $_GET[ 'tab' ] ) ? wp_kses( $_GET[ 'tab' ],$allowed_html ) : 'general_settings';  

require_once get_template_directory().'/libs/help_content.php';

print '<div class="wrap">';
    print ' <div class="wpestate_admin_search_bar">
            <label class="wpestate_adv_search_label">'.__('Theme Help Search - there are over 200 articles to help you setup and use the theme. Please use this search and if your question is not here, please open a ticket in our client support system.','wpestate').'</label>
            <input type="text" id="wpestate_search_bar" placeholder="'.__('Search help documentation. For ex. type: Adv ','wpestate').'">
            <div id="wpestate_admin_results">
            </div>
        </div>';

    print '<div class="wrap-topbar">';
        
        $hidden_tab='none';
        if(isset($_POST['hidden_tab'])) {
            $hidden_tab= esc_attr( $_POST['hidden_tab'] );
        }
        
        $hidden_sidebar='none';
        if(isset($_POST['hidden_sidebar'])) {
            $hidden_sidebar= esc_attr( $_POST['hidden_sidebar'] );
        }
        
        print '<input type="hidden" id="hidden_tab" name="hidden_tab" value="'.$hidden_tab.'">';        
        print '<input type="hidden" id="hidden_sidebar"  name="hidden_sidebar" value="'.$hidden_sidebar.'">';
        
        print   '<div id="general_settings" data-menu="general_settings_sidebar" class="admin_top_bar_button"> 
                    <img src="'.get_template_directory_uri().'/img/admin/general.png'.'" alt="general settings">'.__('General','wpestate').'
                </div>';
        
        print   '<div id="social_contact" data-menu="social_contact_sidebar" class="admin_top_bar_button"> 
                    <img src="'.get_template_directory_uri().'/img/admin/contact.png'.'" alt="general settings">'.__('Social & Contact','wpestate').'
                </div>';
        
        print   '<div id="map_settings" data-menu="map_settings_sidebar" class="admin_top_bar_button"> 
                    <img src="'.get_template_directory_uri().'/img/admin/map.png'.'" alt="general settings">'.__('Map','wpestate').'
                </div>';
         
        print   '<div id="design_settings" data-menu="design_settings_sidebar" class="admin_top_bar_button"> 
                    <img src="'.get_template_directory_uri().'/img/admin/design.png'.'" alt="general settings">'.__('Design','wpestate').'
                </div>';
        
        print   '<div id="advanced_settings" data-menu="advanced_settings_sidebar" class="admin_top_bar_button"> 
                    <img src="'.get_template_directory_uri().'/img/admin/advanced.png'.'" alt="general settings">'.__('Advanced','wpestate').'
                </div>';
            
        print   '<div id="membership_settings" data-menu="membership_settings_sidebar"  class="admin_top_bar_button"> 
                    <img src="'.get_template_directory_uri().'/img/admin/membership.png'.'" alt="general settings">'.__('Membership','wpestate').'
                </div>';
        
        print   '<div id="advanced_search_settings" data-menu="advanced_search_settings_sidebar"  class="admin_top_bar_button"> 
                    <img src="'.get_template_directory_uri().'/img/admin/search.png'.'" alt="general settings">'.__('Search','wpestate').'
                </div>';
        
        print   '<div id="help_custom" data-menu="help_custom_sidebar"  class="admin_top_bar_button"> 
                    <img src="'.get_template_directory_uri().'/img/admin/help.png'.'" alt="general settings">'.__('Help & Custom','wpestate').'
                </div>';
        
        print '<div class="theme_details">'. wp_get_theme().'</div>';
        
    print '</div>';


    print '
    <div id="wpestate_sidebar_menu">
        <div id="general_settings_sidebar" class="theme_options_sidebar">
            <ul>
                <li data-optiontab="global_settings_tab" class="selected_option">'.__('Global Theme Settings','wpestate').'</li>
                <li data-optiontab="appearance_options_tab"   class="">'.__('Appearance','wpestate').'</li>
                <li data-optiontab="logos_favicon_tab"   class="">'.__('Logos & Favicon','wpestate').'</li>
                <li data-optiontab="header_settings_tab"   class="">'.__('Header','wpestate').'</li>
                <li data-optiontab="footer_settings_tab"   class="">'.__('Footer','wpestate').'</li>
                <li data-optiontab="price_curency_tab"   class="">'.__('Price & Currency','wpestate').'</li>
                <li data-optiontab="custom_fields_tab"   class="">'.__('Custom Fields','wpestate').'</li>
                <li data-optiontab="ammenities_features_tab"   class="">'.__('Features & Amenities','wpestate').'</li>
                <li data-optiontab="listing_labels_tab"   class="">'.__('Listings Labels','wpestate').'</li>   
                <li data-optiontab="theme_slider_tab"   class="">'.__('Theme Slider','wpestate').'</li>   
                <li data-optiontab="property_rewrite_page_tab" class="">'.__('Edit Agent and Page links','wpestate').'</li>
            </ul>
        </div>
        
        <div id="social_contact_sidebar" class="theme_options_sidebar" style="display:none;">
            <ul>
                <li data-optiontab="contact_details_tab" class="">'.__('Contact Details','wpestate').'</li>
                <li data-optiontab="social_accounts_tab" class="">'.__('Social Accounts','wpestate').'</li>
                <li data-optiontab="contact7_tab" class="">'.__('Contact 7 Settings','wpestate').'</li>
            </ul>
        </div>
        

        <div id="map_settings_sidebar" class="theme_options_sidebar" style="display:none;">
            <ul>
                <li data-optiontab="general_map_tab" class="">'.__('Map Settings','wpestate').'</li>
                <li data-optiontab="pin_management_tab" class="">'.__('Pins Management','wpestate').'</li>
                <li data-optiontab="generare_pins_tab" class="">'.__('Generate Pins','wpestate').'</li>
            </ul>
        </div>
        

        <div id="design_settings_sidebar" class="theme_options_sidebar" style="display:none;">
            <ul>
                <li data-optiontab="general_design_settings_tab" class="">'.__('General Design Settings','wpestate').'</li>
                <li data-optiontab="property_page_tab" class="">'.__('Property Page','wpestate').'</li>
                <li data-optiontab="custom_colors_tab" class="">'.__('Custom Colors','wpestate').'</li>
                <li data-optiontab="custom_fonts_tab" class="">'.__('Fonts','wpestate').'</li>
                <li data-optiontab="mainmenu_design_elements_tab" class="">'.__('Main Menu Design','wpestate').'</li>
                <li data-optiontab="property_page__design_tab" class="">'.__('Property Unit/Card Design - BETA','wpestate').'</li>
                <li data-optiontab="property_list_design_tab" class="">'.__('Property,Agent,Blog Lists Design','wpestate').'</li>
                <li data-optiontab="widget_design_elements_tab" class="">'.__('Sidebar Widget Design','wpestate').'</li>
                <li data-optiontab="print_page_tab" class="">'.__('Property Print Page Design','wpestate').'</li>
                <li data-optiontab="wpestate_user_dashboard_design_tab" class="">'.__('User Dashboard Design','wpestate').'</li>
                <li data-optiontab="other_design_elements_tab" class="">'.__('Other Design Elements','wpestate').'</li>
            </ul>
        </div>
        
        <div id="advanced_search_settings_sidebar" class="theme_options_sidebar" style="display:none;">
            <ul>
                <li data-optiontab="advanced_search_settings_tab" class="">'.__('Advanced Search Settings','wpestate').'</li>
                <li data-optiontab="advanced_search_form_tab" class="">'.__('Advanced Search Form','wpestate').'</li>
            </ul>
        </div>
        
        <div id="membership_settings_sidebar" class="theme_options_sidebar" style="display:none;">
            <ul>
                <li data-optiontab="membership_settings_tab" class="">'.__('Membership Settings','wpestate').'</li>
                <li data-optiontab="property_submission_page_tab" class="">'.__('Property Submission Page','wpestate').'</li>
                <li data-optiontab="paypal_settings_tab" class="">'.__('Paypal Settings','wpestate').'</li>
                <li data-optiontab="stripe_settings_tab" class="">'.__('Stripe Settings','wpestate').'</li>
            </ul>
        </div>

 
        <div id="advanced_settings_sidebar" class="theme_options_sidebar" style="display:none;">
             <ul>
                <li data-optiontab="email_management_tab" class="selected_option">'.__('Email Management','wpestate').'</li>
                <li data-optiontab="speed_management_tab" class="selected_option">'.__('Site Speed','wpestate').'</li>
                <li data-optiontab="export_settings_tab" class="selected_option">'.__('Export Options','wpestate').'</li>
                <li data-optiontab="import_settings_tab" class="selected_option">'.__('Import Options','wpestate').'</li>
                <li data-optiontab="recaptcha_tab" class="selected_option">'.__('reCaptcha settings','wpestate').'</li>
                <li data-optiontab="yelp_tab" class="selected_option">'.__('Yelp settings','wpestate').'</li>
                <li data-optiontab="optima_express_tab" class="selected_option">'.__('Optima Express  settings','wpestate').'</li>
            </ul>
        </div>
        
        <div id="help_custom_sidebar" class="theme_options_sidebar" style="display:none;">
             <ul>
                <li data-optiontab="help_custom_tab" class="selected_option">'.__('Help & Custom','wpestate').'</li>
            </ul>
        </div>
    </div>';
    
    

    print ' <div id="wpestate_wrapper_admin_menu"> 
                <div id="general_settings_sidebar_tab" class="theme_options_wrapper_tab">
                    <form method="post" action="" >
                        <div id="global_settings_tab" class="theme_options_tab" style="display:none;">
                            <h1>'.__('General Settings','wpestate').'</h1>
                            <input type="submit" name="submit"  class="new_admin_submit new_admin_submit_right" value="'.__('Save Changes','wpestate').'" />
                            <div class="theme_option_separator"></div>';    
                            new_wpestate_theme_admin_general_settings();
                        print '        
                        </div>
                    </form>

                    <form method="post" action="">
                    <div id="appearance_options_tab" class="theme_options_tab" style="display:none;">
                        <h1>'.__('Appearance','wpestate').'</h1>
                        <input type="submit" name="submit"  class="new_admin_submit new_admin_submit_right" value="'.__('Save Changes','wpestate').'" />
                        <div class="theme_option_separator"></div>';
                        new_wpestate_appeareance();
                    print '        
                    </div>
                    </form>

                    <form method="post" action="">
                    <div id="logos_favicon_tab" class="theme_options_tab" style="display:none;">
                        <h1>'.__('Logos & Favicon','wpestate').'</h1>
                        <input type="submit" name="submit"  class="new_admin_submit new_admin_submit_right" value="'.__('Save Changes','wpestate').'" />
                        <div class="theme_option_separator"></div>';
                        new_wpestate_theme_admin_logos_favicon();
                    print '        
                    </div>
                    </form>

                    <form method="post" action="">
                    <div id="header_settings_tab" class="theme_options_tab" style="display:none;">
                        <h1>'.__('Header Settings','wpestate').'</h1>
                        <input type="submit" name="submit"  class="new_admin_submit new_admin_submit_right" value="'.__('Save Changes','wpestate').'" />
                        <div class="theme_option_separator"></div>';
                        new_wpestate_header_settings();
                    print '        
                    </div>
                    </form>

                    <form method="post" action="">
                    <div id="footer_settings_tab" class="theme_options_tab" style="display:none;">
                        <h1>'.__('Footer Settings','wpestate').'</h1>
                        <input type="submit" name="submit"  class="new_admin_submit new_admin_submit_right" value="'.__('Save Changes','wpestate').'" />
                        <div class="theme_option_separator"></div>';
                        new_wpestate_footer_settings();
                    print '        
                    </div>
                    </form>

                    <form method="post" action="">
                    <div id="price_curency_tab" class="theme_options_tab" style="display:none;">
                        <h1>'.__('Price & Currency','wpestate').'</h1>
                        <input type="submit" name="submit"  class="new_admin_submit new_admin_submit_right" value="'.__('Save Changes','wpestate').'" />
                        <div class="theme_option_separator"></div>';
                        new_wpestate_price_currency();
                    print '        
                    </div>
                    </form>

                    <form method="post" action="">
                    <div id="custom_fields_tab" class="theme_options_tab" style="display:none;">
                        <h1>'.__('Custom Fields','wpestate').'</h1>
                        <input type="submit" name="submit"  class="new_admin_submit new_admin_submit_right" value="'.__('Save Changes','wpestate').'" />
                        <div class="theme_option_separator"></div>';
                        new_wpestate_custom_fields();
                    print '        
                    </div>
                    </form>
                    
                    <form method="post" action="">
                    <div id="ammenities_features_tab" class="theme_options_tab" style="display:none;">
                        <h1>'.__('Features & Amenities','wpestate').'</h1>
                        <input type="submit" name="submit"  class="new_admin_submit new_admin_submit_right" value="'.__('Save Changes','wpestate').'" />
                        <div class="theme_option_separator"></div>';
                        new_wpestate_ammenities_features();
                    print '        
                    </div>
                    </form>

                    <form method="post" action="">
                    <div id="listing_labels_tab" class="theme_options_tab" style="display:none;">
                        <h1>'.__('Listings Labels','wpestate').'</h1>
                        <input type="submit" name="submit"  class="new_admin_submit new_admin_submit_right" value="'.__('Save Changes','wpestate').'" />
                        <div class="theme_option_separator"></div>';
                        new_wpestate_listing_labels();
                    print '        
                    </div>
                    </form>
                    

                    <form method="post" action="">
                    <div id="property_rewrite_page_tab" class="theme_options_tab" style="display:none;">
                        <h1>'.__('Property and Agent Links','wpestate').'</h1>
                        <input type="submit" name="submit"  class="new_admin_submit new_admin_submit_right" value="'.__('Save Changes','wpestate').'" />
                        <div class="theme_option_separator"></div>';
                        new_wpestate_property_links();
                    print '        
                    </div>
                    </form>
                    





                    <form method="post" action="">
                    <div id="theme_slider_tab" class="theme_options_tab" style="display:none;">
                        <h1>'.__('Theme Slider ','wpestate').'</h1>
                        <input type="submit" name="submit"  class="new_admin_submit new_admin_submit_right" value="'.__('Save Changes','wpestate').'" />
                        <div class="theme_option_separator"></div>';
                        new_wpestate_theme_slider();
                    print '        
                    </div>
                    </form>
 
                </div>
                
                <div id="social_contact_sidebar_tab" class="theme_options_wrapper_tab" style="display:none">
                        <form method="post" action="">
                        <div id="contact_details_tab" class="theme_options_tab" style="display:none;">
                            <h1>'.__('Contact Details','wpestate').'</h1>
                            <input type="submit" name="submit"  class="new_admin_submit new_admin_submit_right" value="'.__('Save Changes','wpestate').'" />
                            <div class="theme_option_separator"></div>';
                            new_wpestate_theme_contact_details();
                        print '        
                        </div>
                        </form>
                        
                        <form method="post" action="">
                        <div id="social_accounts_tab" class="theme_options_tab" style="display:none;">
                            <h1>'.__('Social Accounts ','wpestate').'</h1>
                            <input type="submit" name="submit"  class="new_admin_submit new_admin_submit_right" value="'.__('Save Changes','wpestate').'" />
                            <div class="theme_option_separator"></div>';
                            new_wpestate_theme_social_accounts();
                        print '        
                        </div>
                        </form>

                        <form method="post" action="">
                        <div id="contact7_tab" class="theme_options_tab" style="display:none;">
                            <h1>'.__('Contact 7 Settings','wpestate').'</h1>
                            <input type="submit" name="submit"  class="new_admin_submit new_admin_submit_right" value="'.__('Save Changes','wpestate').'" />
                            <div class="theme_option_separator"></div>';
                            new_wpestate_contact7();
                        print '        
                        </div>
                        </form>
                </div> 




                <div id="map_settings_sidebar_tab" class="theme_options_wrapper_tab" style="display:none">
                        <form method="post" action="">
                        <div id="general_map_tab" class="theme_options_tab" style="display:none;">
                            <h1>'.__('Map  Settings','wpestate').'</h1>
                            <input type="submit" name="submit"  class="new_admin_submit new_admin_submit_right" value="'.__('Save Changes','wpestate').'" />
                            <div class="theme_option_separator"></div>';
                            new_wpestate_map_details();
                        print '        
                        </div>
                        </form>
                        
                        <form method="post" action="">
                        <div id="pin_management_tab" class="theme_options_tab" style="display:none;">
                            <h1>'.__('Pin Management','wpestate').'</h1>
                            <input type="submit" name="submit"  class="new_admin_submit new_admin_submit_right" value="'.__('Save Changes','wpestate').'" />
                            <div class="theme_option_separator"></div>';
                            new_wpestate_pin_management();
                        print '        
                        </div>
                        </form>

                        <form method="post" action="">
                        <div id="generare_pins_tab" class="theme_options_tab" style="display:none;">
                            <h1>'.__('Generate Pins','wpestate').'</h1>
                            <input type="submit" name="submit"  class="new_admin_submit new_admin_submit_right" value="'.__('Save Changes','wpestate').'" />
                            <div class="theme_option_separator"></div>';
                            new_wpestate_generate_pins();
                        print '        
                        </div>
                        </form>
                </div> 




                
                <div id="design_settings_sidebar_tab" class="theme_options_wrapper_tab" style="display:none">
                


                       

                        <form method="post" action="">
                        <div id="property_page_tab" class="theme_options_tab" style="display:none;" >
                            <h1>'.__('Property Page Settings','wpestate').'</h1>
                            <input type="submit" name="submit"  class="new_admin_submit new_admin_submit_right" value="'.__('Save Changes','wpestate').'" />
                            <div class="theme_option_separator"></div>';
                            new_wpestate_property_page_details();
                        print '        
                        </div>
                        </form>
                        
                        <form method="post" action="">
                        <div id="property_page__design_tab" class="theme_options_tab" style="display:none;" >
                            <h1>'.__('Property Card Design','wpestate').'</h1>
                            <div class="theme_option_separator"></div>';
                            new_wpestate_property_page_design_details();
                        print '        
                        </div>
                        </form>
                        
                        <form method="post" action="">
                        <div id="mainmenu_design_elements_tab" class="theme_options_tab" style="display:none;" >
                            <h1>'.__('Main Menu Design Tab','wpestate').'</h1>
                            <input type="submit" name="submit"  class="new_admin_submit new_admin_submit_right" value="'.__('Save Changes','wpestate').'" />
                            <div class="theme_option_separator"></div>';
                            new_wpestate_main_menu_design();
                        print '        
                        </div>
                        </form>


                        
                        <form method="post" action="">
                        <div id="property_list_design_tab" class="theme_options_tab" style="display:none;" >
                            <h1>'.__('Property, Agent, Blog Lists Design','wpestate').'</h1>
                            <input type="submit" name="submit"  class="new_admin_submit new_admin_submit_right" value="'.__('Save Changes','wpestate').'" />
                            <div class="theme_option_separator"></div>';
                            new_wpestate_property_list_design_details();
                        print '        
                        </div>
                        </form>
                        
                        <form method="post" action="">
                        <div id="other_design_elements_tab" class="theme_options_tab" style="display:none;" >
                            <h1>'.__('Other Design Tab','wpestate').'</h1>
                            <input type="submit" name="submit"  class="new_admin_submit new_admin_submit_right" value="'.__('Save Changes','wpestate').'" />
                            <div class="theme_option_separator"></div>';
                            new_wpestate_other_design_details();
                        print '        
                        </div>
                        </form>

                        <form method="post" action="">
                        <div id="print_page_tab" class="theme_options_tab" style="display:none;" >
                            <h1>'.__('Print Page Design','wpestate').'</h1>
                            <input type="submit" name="submit"  class="new_admin_submit new_admin_submit_right" value="'.__('Save Changes','wpestate').'" />
                            <div class="theme_option_separator"></div>';
                            wpestate_print_page_design();
                        print '        
                        </div>
                        </form>
                        

                        <form method="post" action="">
                        <div id="wpestate_user_dashboard_design_tab" class="theme_options_tab" style="display:none;" >
                            <h1>' . __('User Dashboard Design', 'wpestate') . '</h1>
                            <input type="submit" name="submit"  class="new_admin_submit new_admin_submit_right" value="' . __('Save Changes', 'wpestate') . '" />
                            <div class="theme_option_separator"></div>';
                            wpestate_user_dashboard_design();
                        print '        
                        </div>
                        </form>


                        <form method="post" action="">
                        <div id="widget_design_elements_tab" class="theme_options_tab" style="display:none;" >
                            <h1>'.__('Sidebar Widget Tab','wpestate').'</h1>
                            <input type="submit" name="submit"  class="new_admin_submit new_admin_submit_right" value="'.__('Save Changes','wpestate').'" />
                            <div class="theme_option_separator"></div>';
                            new_widget_design_elements_details();
                        print '        
                        </div>
                        </form>



                        <form method="post" action="">
                        <div id="general_design_settings_tab" class="theme_options_tab" style="display:none;" >
                            <h1>'.__('General Design Settings','wpestate').'</h1>
                            <input type="submit" name="submit"  class="new_admin_submit new_admin_submit_right" value="'.__('Save Changes','wpestate').'" />
                            <div class="theme_option_separator"></div>';
                            wpestate_general_design_settings();
                        print '        
                        </div>
                        </form>
                        


                        <form method="post" action="">
                        <div id="custom_colors_tab" class="theme_options_tab" style="display:none;">
                            <h1>'.__('Custom Colors Settings','wpestate').'</h1>
                            <span class="header_explanation">'.__('***Please understand that we cannot add here color controls for all theme elements & details. Doing that will result in a overcrowded and useless interface. These small details need to be addressed via custom css code','wpestate').'</span>    
                            <input type="submit" name="submit"  class="new_admin_submit new_admin_submit_right" value="'.__('Save Changes','wpestate').'" />
                            <div class="theme_option_separator"></div>';
                            new_wpestate_custom_colors();
                        print '        
                        </div>
                        </form>
                        
                        <form method="post" action="">
                        <div id="custom_fonts_tab" class="theme_options_tab" style="display:none;">
                            <h1>'.__('Custom Fonts','wpestate').'</h1>
                            <input type="submit" name="submit"  class="new_admin_submit new_admin_submit_right" value="'.__('Save Changes','wpestate').'" />
                            <div class="theme_option_separator"></div>';
                            new_wpestate_custom_fonts();
                        print '        
                        </div>
                        </form>


                </div> 
                
                <div id="advanced_search_settings_sidebar_tab" class="theme_options_wrapper_tab"  style="display:none">
                        <form method="post" action="">
                        <div id="advanced_search_settings_tab" class="theme_options_tab" style="display:none;">
                            <h1>'.__('Advanced Search Settings','wpestate').'</h1>
                            <input type="submit" name="submit"  class="new_admin_submit new_admin_submit_right" value="'.__('Save Changes','wpestate').'" />
                            <div class="theme_option_separator"></div>';
                            new_wpestate_advanced_search_settings();
                        print '        
                        </div>
                        </form>
                        
                        <form method="post" action="">
                        <div id="advanced_search_form_tab" class="theme_options_tab" style="display:none;">
                            <h1>'.__('Advanced Search Form','wpestate').'</h1>
                            <input type="submit" name="submit"  class="new_admin_submit new_admin_submit_right" value="'.__('Save Changes','wpestate').'" />
                            <div class="theme_option_separator"></div>';
                            new_wpestate_advanced_search_form();
                        print '        
                        </div>
                        </form>



                       
                </div> 
                


                <div id="membership_settings_sidebar_tab" class="theme_options_wrapper_tab" style="display:none">
                        <form method="post" action="">
                        <div id="membership_settings_tab" class="theme_options_tab"  style="display:none;">
                            <h1>'.__('Membership Settings','wpestate').'</h1>
                            <input type="submit" name="submit"  class="new_admin_submit new_admin_submit_right" value="'.__('Save Changes','wpestate').'" />
                            <div class="theme_option_separator"></div>';
                            new_wpestate_membership_settings();
                        print '        
                        </div>
                        </form>
                        
                        <form method="post" action="">
                        <div id="paypal_settings_tab" class="theme_options_tab" style="display:none;">
                            <h1>'.__('PaypPal Settings','wpestate').'</h1>
                            <input type="submit" name="submit"  class="new_admin_submit new_admin_submit_right" value="'.__('Save Changes','wpestate').'" />
                            <div class="theme_option_separator"></div>';
                            new_wpestate_paypal_settings();
                        print '        
                        </div>
                        </form>
                        
                        <form method="post" action="">
                        <div id="stripe_settings_tab" class="theme_options_tab" style="display:none;">
                            <h1>'.__('Stripe Settings','wpestate').'</h1>
                            <input type="submit" name="submit"  class="new_admin_submit new_admin_submit_right" value="'.__('Save Changes','wpestate').'" />
                            <div class="theme_option_separator"></div>';
                            new_wpestate_stripe_settings();
                        print '        
                        </div>
                        </form>
                        
                        <form method="post" action="">
                        <div id="property_submission_page_tab" class="theme_options_tab" style="display:none;">
                            <h1>'.__('Property Submission Page','wpestate').'</h1>
                            <input type="submit" name="submit"  class="new_admin_submit new_admin_submit_right" value="'.__('Save Changes','wpestate').'" />
                            <div class="theme_option_separator"></div>';
                            new_property_submission_tab();
                        print '        
                        </div>
                        </form>





                </div> 
                
             

                <div id="advanced_settings_sidebar_tab" class="theme_options_wrapper_tab" style="display:none">
                
                        <form method="post" action="">
                        <div id="email_management_tab" class="theme_options_tab" style="display:none;">
                            <h1>'.__('Email Management','wpestate').'</h1>
                            <input type="submit" name="submit"  class="new_admin_submit new_admin_submit_right" value="'.__('Save Changes','wpestate').'" />
                            <div class="theme_option_separator"></div>';
                            new_wpestate_email_management();
                        print '        
                        </div>
                        </form>
                        
                        <form method="post" action="">
                        <div id="speed_management_tab" class="theme_options_tab" style="display:none;">
                            <h1>'.__('Site Speed','wpestate').'</h1>
                            <input type="submit" name="submit"  class="new_admin_submit new_admin_submit_right" value="'.__('Save Changes','wpestate').'" />
                            <div class="theme_option_separator"></div>';
                            new_wpestate_site_speed();
                        print '        
                        </div>
                        </form>
                        
                        <form method="post" action="">
                        <div id="export_settings_tab" class="theme_options_tab" style="display:none;">
                            <h1>'.__('Export Options','wpestate').'</h1>
                            <input type="submit" name="submit"  class="new_admin_submit new_admin_submit_right" value="'.__('Save Changes','wpestate').'" />
                            <div class="theme_option_separator"></div>';
                            new_wpestate_export_settings();
                        print '        
                        </div>
                        </form>
                        
                        <form method="post" action="">
                        <div id="import_settings_tab" class="theme_options_tab" style="display:none;">
                            <h1>'.__('Import Options','wpestate').'</h1>
                            <input type="submit" name="submit"  class="new_admin_submit new_admin_submit_right" value="'.__('Save Changes','wpestate').'" />
                            <div class="theme_option_separator"></div>';
                            new_wpestate_import_options_tab();
                        print '        
                        </div>
                        </form>

                        
                        <form method="post" action="">
                        <div id="recaptcha_tab" class="theme_options_tab" style="display:none;">
                            <h1>'.__('reCaptcha settings','wpestate').'</h1>
                            <input type="submit" name="submit"  class="new_admin_submit new_admin_submit_right" value="'.__('Save Changes','wpestate').'" />
                            <div class="theme_option_separator"></div>';
                            estate_recaptcha_settings();
                        print '        
                        </div>
                        </form>
                        
                        <form method="post" action="">
                        <div id="yelp_tab" class="theme_options_tab" style="display:none;">
                            <h1>'.__('Yelp settings','wpestate').'</h1>
                            <input type="submit" name="submit"  class="new_admin_submit new_admin_submit_right" value="'.__('Save Changes','wpestate').'" />
                            <div class="theme_option_separator"></div>';
                            estate_yelp_settings();
                        print '        
                        </div>
                        </form>
                        
                        <form method="post" action="">
                        <div id="optima_express_tab" class="theme_options_tab" style="display:none;">
                            <h1>'.__('Optima Express settings','wpestate').'</h1>
                            <input type="submit" name="submit"  class="new_admin_submit new_admin_submit_right" value="'.__('Save Changes','wpestate').'" />
                            <div class="theme_option_separator"></div>';
                            optima_express_settings();
                        print '        
                        </div>
                        </form>
                        


     
                </div> 


                <div id="help_custom_sidebar_tab" class="theme_options_wrapper_tab">
                    <form method="post" action="">
                    <div id="help_custom_tab" class="theme_options_tab" style="display:none;">
                        <h1>'.__('Help&Custom','wpestate').'</h1>
                        <div class="theme_option_separator"></div>';
                        new_wpestate_help_custom();
                    print '        
                    </div>
                    </form>
                </div>


           </div>';

print '</div>';



    print '<script type="text/javascript">
    //<![CDATA[
    
    function split( val ) {
      return val.split( /,\s*/ );
    }
    function extractLast( term ) {
        return split( term ).pop();
    }
    function decodeHtml(html) {
  
        var txt = document.createElement("textarea");
        txt.innerHTML = html;
        return txt.value;
    }
                jQuery(document).ready(function(){
                    var autofill='.$help_content.';
                    jQuery("#wpestate_search_bar" ).autocomplete({
                   
                    source: function( request, response ) {
                   
                     response( jQuery.ui.autocomplete.filter(
                       autofill, extractLast( request.term ) ) );
                   },
                    focus: function( event, ui ) {
                        jQuery( "#wpestate_admin_results" ).val( decodeHtml( ui.item.label ) );
                        return false;
                    }, select: function( event, ui ) { 
                        window.open(ui.item.value,"_blank");
                    }
                    
                    
                    
                }).autocomplete( "instance" )._renderItem = function( ul, item ) {
                        return jQuery( "<li>" )
                        .append( "" + decodeHtml( item.label )+ "" )
                            .appendTo( ul );
                    };
                

           });
           //]]>
           </script>';
   

   
}
endif; // end   wpestate_new_general_set  


if( !function_exists('wpestate_generate_file_pins') ):
function   wpestate_generate_file_pins(){
    print '<div class="wpestate-tab-container">';
    print '<h1 class="wpestate-tabh1">'.__('Generate pins','wpestate').'</h1>';
     print '<a href="http://help.wpresidence.net/#!/googlemaps" target="_blank" class="help_link">'.__('help','wpestate').'</a>';
  
    print '<table class="form-table">   <tr valign="top">
           <td>';  
          
  
    
    print '</td>
           </tr></table>';
    print '</div>';   
}
endif;


if( !function_exists('wpestate_show_advanced_search_options') ):

function  wpestate_show_advanced_search_options($i,$adv_search_what){
    $return_string='';

    $curent_value='';
    if(isset($adv_search_what[$i])){
        $curent_value=$adv_search_what[$i];        
    }
    
   // $curent_value=$adv_search_what[$i];
    $admin_submission_array=array('types',
                                  'categories',
                                  'county / state',
                                  'cities',
                                  'areas',
                                  'property price',
                                  'property size',
                                  'property lot size',
                                  'property rooms',
                                  'property bedrooms',
                                  'property bathrooms',
                                  'property address',                               
                                  'property zip',
                                  'property country',
                                  'property status',
                                  'property id',
                                  'keyword'
                                );
    
    foreach($admin_submission_array as $value){

        $return_string.='<option value="'.$value.'" '; 
        if($curent_value==$value){
             $return_string.= ' selected="selected" ';
        }
        $return_string.= '>'.$value.'</option>';    
    }
    
    $i=0;
    $custom_fields = get_option( 'wp_estate_custom_fields', true); 
    if( !empty($custom_fields)){  
        while($i< count($custom_fields) ){          
            $name =   $custom_fields[$i][0];
            $type =   $custom_fields[$i][1];
            $slug =   str_replace(' ','-',$name);

            $return_string.='<option value="'.$slug.'" '; 
            if($curent_value==$slug){
               $return_string.= ' selected="selected" ';
            }
            $return_string.= '>'.$name.'</option>';    
            $i++;  
        }
    }  
    $slug='none';
    $name='none';
    $return_string.='<option value="'.$slug.'" '; 
    if($curent_value==$slug){
        $return_string.= ' selected="selected" ';
    }
    $return_string.= '>'.$name.'</option>';    

       
    return $return_string;
}
endif; // end   wpestate_show_advanced_search_options  



if( !function_exists('wpestate_show_advanced_search_how') ):
function  wpestate_show_advanced_search_how($i,$adv_search_how){
    $return_string='';
    $curent_value='';
    if (isset($adv_search_how[$i])){
         $curent_value=$adv_search_how[$i];
    }
   
    
    
    $admin_submission_how_array=array('equal',
                                      'greater',
                                      'smaller',
                                      'like',
                                      'date bigger',
                                      'date smaller');
    
    foreach($admin_submission_how_array as $value){
        $return_string.='<option value="'.$value.'" '; 
        if($curent_value==$value){
             $return_string.= ' selected="selected" ';
        }
        $return_string.= '>'.$value.'</option>';    
    }
    return $return_string;
}
endif; // end   wpestate_show_advanced_search_how  





if( !function_exists('wpestate_dropdowns_theme_admin') ):
    function wpestate_dropdowns_theme_admin($array_values,$option_name,$pre=''){
        
        $dropdown_return    =   '';
        $option_value       =   esc_html ( get_option('wp_estate_'.$option_name,'') );
        foreach($array_values as $value){
            $dropdown_return.='<option value="'.$value.'"';
              if ( $option_value == $value ){
                $dropdown_return.='selected="selected"';
            }
            $dropdown_return.='>'.$pre.$value.'</option>';
        }
        
        return $dropdown_return;
        
    }
endif;




if( !function_exists('wpestate_dropdowns_theme_admin_with_key') ):
    function wpestate_dropdowns_theme_admin_with_key($array_values,$option_name){
        
        $dropdown_return    =   '';
        $option_value       =   esc_html ( get_option('wp_estate_'.$option_name,'') );
        foreach($array_values as $key=>$value){
            $dropdown_return.='<option value="'.$key.'"';
              if ( $option_value == $key ){
                $dropdown_return.='selected="selected"';
            }
            $dropdown_return.='>'.$value.'</option>';
        }
        
        return $dropdown_return;
        
    }
endif;






/////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///  Membership Settings
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////
if( !function_exists('wpestate_theme_admin_membershipsettings') ):
function wpestate_theme_admin_membershipsettings(){
    $price_submission               =   floatval( get_option('wp_estate_price_submission','') );
    $price_featured_submission      =   floatval( get_option('wp_estate_price_featured_submission','') );    
    $paypal_client_id               =   esc_html( get_option('wp_estate_paypal_client_id','') );
    $paypal_client_secret           =   esc_html( get_option('wp_estate_paypal_client_secret','') );
    $paypal_api_username            =   esc_html( get_option('wp_estate_paypal_api_username','') );
    $paypal_api_password            =   esc_html( get_option('wp_estate_paypal_api_password','') );
    $paypal_api_signature           =   esc_html( get_option('wp_estate_paypal_api_signature','') );
    $paypal_rec_email               =   esc_html( get_option('wp_estate_paypal_rec_email','') );
    $free_feat_list                 =   esc_html( get_option('wp_estate_free_feat_list','') );
    $free_mem_list                  =   esc_html( get_option('wp_estate_free_mem_list','') );
    $cache_array                    =   array('yes','no');  
    $stripe_secret_key              =   esc_html( get_option('wp_estate_stripe_secret_key','') );
    $stripe_publishable_key         =   esc_html( get_option('wp_estate_stripe_publishable_key','') );
    
    $args=array(
        'a' => array(
            'href' => array(),
            'title' => array()
        ),
        'br' => array(),
        'em' => array(),
        'strong' => array(),
    );
     $direct_payment_details         =   wp_kses( get_option('wp_estate_direct_payment_details','') ,$args);
    
    ///////////////////////////////////////////////////////////////////////////////////////////////////////
    $free_mem_list_unl='';
    if ( intval( get_option('wp_estate_free_mem_list_unl', '' ) ) == 1){
        $free_mem_list_unl=' checked="checked" ';  
    }
    
    ///////////////////////////////////////////////////////////////////////////////////////////////////////
    $paypal_array                   =   array( 'sandbox','live' );
    $paypal_api_select              =   wpestate_dropdowns_theme_admin($paypal_array,'paypal_api');

    $submission_curency_array       =   array(get_option('wp_estate_submission_curency_custom',''),'USD','EUR','AUD','BRL','CAD','CZK','DKK','HKD','HUF','ILS','INR','JPY','MYR','MXN','NOK','NZD','PHP','PLN','GBP','SGD','SEK','CHF','TWD','THB','TRY');
    $submission_curency_symbol      =   wpestate_dropdowns_theme_admin($submission_curency_array,'submission_curency');
    
    $paypal_array                   =   array('no','per listing','membership');
    $paid_submission_symbol         =   wpestate_dropdowns_theme_admin($paypal_array,'paid_submission');
    $admin_submission_symbol        =   wpestate_dropdowns_theme_admin($cache_array,'admin_submission');
    $user_agent_symbol              =   wpestate_dropdowns_theme_admin($cache_array,'user_agent');
    $enable_paypal_symbol           =   wpestate_dropdowns_theme_admin($cache_array,'enable_paypal');
    $enable_stripe_symbol           =   wpestate_dropdowns_theme_admin($cache_array,'enable_stripe');
    $enable_direct_pay_symbol       =   wpestate_dropdowns_theme_admin($cache_array,'enable_direct_pay');
   
    
    
    $free_feat_list_expiration= intval ( get_option('wp_estate_free_feat_list_expiration','') );
    
    print '<div class="wpestate-tab-container">';
    print '<h1 class="wpestate-tabh1">'.__('Membership & Payment Settings','wpestate').'</h1>';  
    print '<a href="http://help.wpresidence.net/#!/freesubmission" target="_blank" class="help_link">'.__('help','wpestate').'</a>';
  
    print '
        <table class="form-table">
        
        <tr valign="top">
            <th scope="row"><label for="admin_submission">'.__('Submited Listings should be approved by admin?','wpestate').'</label></th>
           
            <td> <select id="admin_submission" name="admin_submission">
                    '.$admin_submission_symbol.'
		 </select>
            </td>
        </tr>
        
        <tr valign="top">
            <th scope="row"><label for="user_agent">'.__('Front end registred users should be saved as agents?','wpestate').'</label></th>
           
            <td> <select id="user_agent" name="user_agent">
                    '.$user_agent_symbol.'
		 </select>
            </td>
        </tr>
        

         <tr valign="top">
            <th scope="row"><label for="paid_submission">'.__('Enable Paid Submission ?','wpestate').'</label></th>
           
            <td> <select id="paid_submission" name="paid_submission">
                    '.$paid_submission_symbol.'
		 </select>
            </td>
        </tr>
        
        <tr valign="top">
            <th scope="row"><label for="enable_paypal">'.__('Enable Paypal?','wpestate').'</label></th>
           
            <td> <select id="enable_paypal" name="enable_paypal">
                    '.$enable_paypal_symbol.'
		 </select>
            </td>
        </tr>

     
        
        <tr valign="top">
            <th scope="row"><label for="enable_stripe">'.__('Enable Stripe?','wpestate').'</label></th>
           
            <td> <select id="enable_stripe" name="enable_stripe">
                    '.$enable_stripe_symbol.'
		 </select>
            </td>
        </tr>
        
        <tr valign="top">
            <th scope="row"><label for="enable_direct_pay">'.__('Enable Direct Payment / Wire Payment?','wpestate').'</label></th>
           
            <td> <select id="enable_direct_pay" name="enable_direct_pay">
                    '.$enable_direct_pay_symbol.'
		 </select>
            </td>
        </tr>
        
        <tr valign="top">
            <th scope="row"><label for="submission_curency">'.__('Currency For Paid Submission','wpestate').'</label></th>
            <td>
                <select id="submission_curency" name="submission_curency">
                    '.$submission_curency_symbol.'
                </select> 
            </td>
        </tr>
        
        <tr valign="top">
            <th scope="row"><label for="submission_curency_custom">'.__('Custom Currency Symbol - *select it from the list above after you add it.','wpestate').'</label></th>
            <td>
               <input type="text" id="submission_curency_custom" name="submission_curency_custom" class="regular-text"  value="'.get_option('wp_estate_submission_curency_custom','').'"/>
            </td>
        </tr>

         <tr valign="top">
            <th scope="row"><label for="paypal_client_id">'.__('Paypal Client id','wpestate').'</label></th>
            <td><input  type="text" id="paypal_client_id" name="paypal_client_id" class="regular-text"  value="'.$paypal_client_id.'"/> </td>
        </tr>
        
        <tr valign="top">
            <th scope="row"><label for="paypal_client_secret ">'.__('Paypal Client Secret Key ','wpestate').'</label></th>
            <td><input  type="text" id="paypal_client_secret" name="paypal_client_secret"  class="regular-text" value="'.$paypal_client_secret.'"/> </td>
        </tr>
        
        <tr valign="top">
            <th scope="row"><label for="paypal_api">'.__('Paypal & Stripe Api ','wpestate').'</label></th>
            <td>
              <select id="paypal_api" name="paypal_api">
                    '.$paypal_api_select.'
                </select>
            </td>
        </tr>
        
        <tr valign="top">
            <th scope="row"><label for="paypal_api_username">'.__('Paypal Api User Name ','wpestate').'</label></th>
            <td><input  type="text" id="paypal_api_username" name="paypal_api_username"  class="regular-text" value="'.$paypal_api_username.'"/> </td>
        </tr>
        
        <tr valign="top">
            <th scope="row"><label for="paypal_api_password ">'.__('Paypal API Password ','wpestate').'</label></th>
            <td><input  type="text" id="paypal_api_password" name="paypal_api_password"  class="regular-text" value="'.$paypal_api_password.'"/> </td>
        </tr>
        
        <tr valign="top">
            <th scope="row"><label for="paypal_api_signature">'.__('Paypal API Signature','wpestate').'</label></th>
            <td><input  type="text" id="paypal_api_signature" name="paypal_api_signature"  class="regular-text" value="'.$paypal_api_signature.'"/> </td>
        </tr>
        
        <tr valign="top">
            <th scope="row"><label for="paypal_rec_email">'.__('Paypal receiving email','wpestate').'</label></th>
            <td><input  type="text" id="paypal_rec_email" name="paypal_rec_email"  class="regular-text" value="'.$paypal_rec_email.'"/> </td>
        </tr>
        
        <tr valign="top">
            <th scope="row"><label for="stripe_secret_key">'.__('Stripe Secret Key','wpestate').'</label></th>
            <td><input  type="text" id="stripe_secret_key" name="stripe_secret_key"  class="regular-text" value="'.$stripe_secret_key.'"/> </td>
        </tr>
       
        <tr valign="top">
            <th scope="row"><label for="stripe_publishable_key">'.__('Stripe Publishable Key','wpestate').'</label></th>
            <td><input  type="text" id="stripe_publishable_key" name="stripe_publishable_key" class="regular-text" value="'.$stripe_publishable_key.'"/> </td>
        </tr>
        

        <tr valign="top">
            <th scope="row"><label for="direct_payment_details">'.__('Wire instructions for direct payment','wpestate').'</label></th>
            <td><textarea id="direct_payment_details" name="direct_payment_details"  style="width:325px;" class="regular-text" >'.$direct_payment_details.'</textarea> </td>
        </tr>
        
        <tr valign="top">
            <th scope="row"><label for="price_submission">'.__('Price Per Submission (for "per listing" mode)','wpestate').'</label></th>
           <td><input  type="text" id="price_submission" name="price_submission"  value="'.$price_submission.'"/> </td>
        </tr>
        
        <tr valign="top">
            <th scope="row"><label for="price_featured_submission">'.__('Price to make the listing featured (for "per listing" mode)','wpestate').'</label></th>
           <td><input  type="text" id="price_featured_submission" name="price_featured_submission"  value="'.$price_featured_submission.'"/> </td>
        </tr>
        
        <tr valign="top">
            <th scope="row"><label for="free_mem_list">'.__('Free Membership - no of listings (for "membership" mode)','wpestate').' </label></th>
            <td>
                <input  type="text" id="free_mem_list" name="free_mem_list" style="margin-right:20px;"  value="'.$free_mem_list.'"/> 
       
                <input type="hidden" name="free_mem_list_unl" value="">
                <input type="checkbox"  id="free_mem_list_unl" name="free_mem_list_unl" value="1" '.$free_mem_list_unl.' />
                <label for="free_mem_list_unl">'.__('Unlimited listings ?','wpestate').'</label>
            </td>
        </tr>
        
        <tr valign="top">
            <th scope="row"><label for="free_feat_list">'.__('Free Membership - no of featured listings (for "membership" mode)','wpestate').' </label></th>
            <td>
                <input  type="text" id="free_feat_list" name="free_feat_list" style="margin-right:20px;"    value="'.$free_feat_list.'"/>
              
            </td>
        </tr>
        
  
        <tr valign="top">
            <th scope="row"><label for="free_feat_list_expiration">'.__('Free Membership Listings - no of days until a free listing will expire. *Starts from the moment the property is published on the website. (for "membership" mode) ','wpestate').' </label></th>
            <td>
                <input  type="text" id="free_feat_list_expiration" name="free_feat_list_expiration" style="margin-right:20px;"    value="'.$free_feat_list_expiration.'"/>
              
            </td>
        </tr>

        </table>
        <p class="submit">
            <input type="submit" name="submit" id="submit" class="button-primary" value="'.__('Save Changes','wpestate').'" />
        </p>  
    ';
    print '</div>';
}
endif; // end   wpestate_theme_admin_membershipsettings  




/////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///  Map Settings
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////
if( !function_exists('wpestate_theme_admin_mapsettings') ):
function wpestate_theme_admin_mapsettings(){
    $general_longitude              =   esc_html( get_option('wp_estate_general_longitude') );
    $general_latitude               =   esc_html( get_option('wp_estate_general_latitude') );
    $api_key                        =   esc_html( get_option('wp_estate_api_key') );
    $cache_array                    =   array('yes','no');
    $default_map_zoom               =   intval   ( get_option('wp_estate_default_map_zoom','') );
    $zoom_cluster                   =   esc_html ( get_option('wp_estate_zoom_cluster ','') );
    $hq_longitude                   =   esc_html ( get_option('wp_estate_hq_longitude') );
    $hq_latitude                    =   esc_html ( get_option('wp_estate_hq_latitude') );
    $min_height                     =   intval   ( get_option('wp_estate_min_height','') );
    $max_height                     =   intval   ( get_option('wp_estate_max_height','') );

    $readsys_symbol                 =   wpestate_dropdowns_theme_admin($cache_array,'readsys');
    $ssl_map_symbol                 =   wpestate_dropdowns_theme_admin($cache_array,'ssl_map');
    $cache_symbol                   =   wpestate_dropdowns_theme_admin($cache_array,'cache');
    $show_filter_map_symbol         =   wpestate_dropdowns_theme_admin($cache_array,'show_filter_map');
    $home_small_map_symbol          =   wpestate_dropdowns_theme_admin($cache_array,'home_small_map');
    $pin_cluster_symbol             =   wpestate_dropdowns_theme_admin($cache_array,'pin_cluster');

    
    
    $geolocation_radius         =   esc_html ( get_option('wp_estate_geolocation_radius','') );
   
   
    $idx_symbol             =   wpestate_dropdowns_theme_admin($cache_array,'idx_enable');

    
    
     ///////////////////////////////////////////////////////////////////////////////////////////////////////
    $cache_array2                       =   array('no','yes');
    $keep_min_symbol                    =   wpestate_dropdowns_theme_admin($cache_array2,'keep_min');
    $show_adv_search_symbol_map_close   =   wpestate_dropdowns_theme_admin($cache_array,'show_adv_search_map_close');
    $show_g_search_symbol               =   wpestate_dropdowns_theme_admin($cache_array2,'show_g_search');

     
     
    $map_style  =   esc_html ( get_option('wp_estate_map_style','') );
    
    $map_types = array('SATELLITE','HYBRID','TERRAIN','ROADMAP');
    $default_map_type_symbol               =   wpestate_dropdowns_theme_admin($map_types,'default_map_type');

    
   
    ///////////////////////////////////////////////////////////////////////////////////////////////////////
 
    print '<div class="wpestate-tab-container">';
    print '<h1 class="wpestate-tabh1">'.__('Google Maps Settings','wpestate').'</h1>';  
    print '<a href="http://help.wpresidence.net/#!/googlemaps" target="_blank" class="help_link">'.__('help','wpestate').'</a>';
  
    print '
       <table class="form-table">';
       $path=estate_get_pin_file_path(); 
   
    if ( file_exists ($path) && is_writable ($path) ){
       
    }else{
        print ' <div class="notice_file">'.__('the file Google map does NOT exist or is NOT writable','wpestate').'</div>';
    }
    
    
    print'
        <tr valign="top">
            <th scope="row"><label for="readsys">'.__('Use file reading for pins? (*recommended for over 200 listings. Read the manual for diffrences betwen file and mysql reading)','wpestate').'</label></th>
           
            <td> <select id="readsys" name="readsys">
                    '.$readsys_symbol.'
		 </select>
            </td>
        </tr>
        
        <tr valign="top">
            <th scope="row"><label for="ssl_map">'.__('Use Google maps with SSL ?','wpestate').'</label></th>
           
            <td> <select id="ssl_map" name="ssl_map">
                    '.$ssl_map_symbol.'
		 </select>
            </td>
        </tr>

        <tr valign="top">
           <th scope="row"><label for="api_key">'.__('Google Maps API KEY','wpestate').'</label></th>
           <td><input  type="text" id="api_key" name="api_key" class="regular-text" value="'.$api_key.'"/></td>
        </tr>
          <tr valign="top">
            <th scope="row"></th>
            <td>'.__('The Google Maps JavaScript API v3 REQUIRES an API key to function correctly. Get an APIs Console key and post the code in Theme Options. You can get it from  <a href="https://developers.google.com/maps/documentation/javascript/tutorial#api_key">here</a>','wpestate').'.</td>
        </tr>
        <tr valign="top">
            <th scope="row"> <label for="general_latitude">'.__('Starting Point Latitude','wpestate').'</label></th>
            <td><input  type="text" id="general_latitude"  name="general_latitude"   value="'.$general_latitude.'"/></td>
        </tr>
        
        <tr valign="top">
            <th scope="row"> <label for="general_longitude">'.__('Starting Point Longitude','wpestate').'</label></th>
            <td><input  type="text" id="general_longitude" name="general_longitude"  value="'.$general_longitude.'"/> </td>
        </tr>
        
        <tr valign="top">
            <th scope="row"><label for="default_map_zoom">'.__(' Default Map zoom (1 to 20) ','wpestate').'</label></th>
            <td>
                <input type="text" id="default_map_zoom" name="default_map_zoom" value="'.$default_map_zoom.'">   
            </td>
        </tr> 

        <tr valign="top">
            <th scope="row"><label for="default_map_type">'.__('Map Type','wpestate').'</label></th>
           
            <td> <select id="default_map_type" name="default_map_type">
                    '.$default_map_type_symbol.'
		 </select>
            </td>
        </tr>
        

        <tr valign="top">
            <th scope="row"><label for="copyright_message">'.__('Use Cache for Google maps ?(*cache will renew it self every 3h)','wpestate').'</label></th>
           
            <td> <select id="cache" name="cache">
                    '.$cache_symbol.'
		 </select>
            </td>
        </tr>
        
      
        
        <tr valign="top">
            <th scope="row"><label for="pin_cluster">'.__('Use Pin Cluster on map','wpestate').'</label></th>
           
            <td> <select id="pin_cluster" name="pin_cluster">
                    '.$pin_cluster_symbol.'
		 </select>
            </td>
        </tr>
        
        
         <tr valign="top">
            <th scope="row"><label for="zoom_cluster">'.__('Maximum zoom level for Cloud Cluster to appear','wpestate').'</label></th>
            <td><input id="zoom_cluster" type="text" size="36" name="zoom_cluster" value="'.$zoom_cluster.'" /></td>       
        </tr>
        
         <tr valign="top">
            <th scope="row"> <label for="hq_latitude">'.__('Contact Page - Company HQ Latitude','wpestate').'</label></th>
            <td><input  type="text" id="hq_latitude"  name="hq_latitude"   value="'.$hq_latitude.'"/></td>
        </tr>
        
        <tr valign="top">
            <th scope="row"> <label for="hq_longitude">'.__('Contact Page - Company HQ Longitude','wpestate').'</label></th>
            <td><input  type="text" id="hq_longitude" name="hq_longitude"  value="'.$hq_longitude.'"/> </td>
        </tr>
        <tr valign="top">
            <th scope="row"><label for="copyright_message">'.__('Enable dsIDXpress to use the map ','wpestate').'</label></th>          
            <td> <select id="idx_enable" name="idx_enable">
                    '.$idx_symbol.'
		 </select>
            </td>
        </tr>';
        /*
         <tr valign="top">
            <th scope="row"><label for="geolocation">'.__('Enable Geolocation','wpestate').'</label></th>
           
            <td> <select id="geolocation" name="geolocation">
                    '.$geolocation_symbol.'
		 </select>
            </td>
        </tr>
         */        
        print'
         <tr valign="top">
            <th scope="row"><label for="geolocation_radius">'.__('Geolocation Circle over map (in meters)','wpestate').'</label></th>
            <td>  <input id="geolocation_radius" type="text" size="36" name="geolocation_radius" value="'.$geolocation_radius.'" /></td>
        </tr>
        

        <tr valign="top">
            <th scope="row"><label for="min_height">'.__('Height of the Google Map when closed','wpestate').'</label></th>
            <td>  <input id="min_height" type="text" size="36" name="min_height" value="'.$min_height.'" /></td>
        </tr>
        
        <tr valign="top">
            <th scope="row"><label for="max_height">'.__('Height of Google Map when open','wpestate').'</label></th>
            <td>  <input id="max_height" type="text" size="36" name="max_height" value="'.$max_height.'" /></td>
        </tr>

        <tr valign="top">
            <th scope="row"><label for="keep_min">'.__('Force Google Map at the "closed" size ? ','wpestate').'</label></th>
           
            <td> <select id="keep_min" name="keep_min">
                    '.$keep_min_symbol.'
		 </select>
            </td>
        </tr>


        <tr valign="top">
            <th scope="row"><label for="keep_min">'.__('Show Google Search over Map? ','wpestate').'</label></th>
           
            <td> <select id="show_g_search" name="show_g_search">
                    '.$show_g_search_symbol.'
		 </select>
            </td>
        </tr>
        
        <tr valign="top">
            <th scope="row"><label for="map_style">'.__('Style for Google Map. Use https://snazzymaps.com/ to create styles ','wpestate').'</label></th>
            <td> 
           
                <textarea id="map_style" style="width:270px;height:350px;" name="map_style">'.stripslashes($map_style).'</textarea>
            </td>
        </tr>
        

        </table>
        <p class="submit">
            <input type="submit" name="submit" id="submit" class="button-primary"  value="'.__('Save Changes','wpestate').'" />
        </p>  
    ';
    print '</div>';
}
endif; // end   wpestate_theme_admin_mapsettings  



/////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///  General Settings
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////
   

if( !function_exists('wpestate_export_theme_options') ):
function wpestate_export_theme_options(){
    $export_options = array(
        'wp_estate_mobile_header_background_color',
        'wp_estate_mobile_header_icon_color',
        'wp_estate_mobile_menu_font_color',
        'wp_estate_mobile_menu_hover_font_color',
        'wp_estate_mobile_item_hover_back_color',
        'wp_estate_mobile_menu_backgound_color',
        'wp_estate_mobile_menu_border_color',
        'wp_estate_crop_images_lightbox',
        'wp_estate_show_lightbox_contact',
        'wp_estate_submission_page_fields',
        'wp_estate_mandatory_page_fields',
        'wp_estate_url_rewrites',
        'wp_estate_print_show_subunits',
        'wp_estate_print_show_agent',
        'wp_estate_print_show_description',
        'wp_estate_print_show_adress',
        'wp_estate_print_show_details',
        'wp_estate_print_show_features',
        'wp_estate_print_show_floor_plans',
        'wp_estate_print_show_images',
        'wp_estate_show_header_dashboard',
        'wp_estate_user_dashboard_menu_color',
        'wp_estate_user_dashboard_menu_hover_color',
        'wp_estate_user_dashboard_menu_color_hover',
        'wp_estate_user_dashboard_menu_back',
        'wp_estate_user_dashboard_package_back',
        'wp_estate_user_dashboard_package_color',
        'wp_estate_user_dashboard_buy_package',
        'wp_estate_user_dashboard_package_select',
        'wp_estate_user_dashboard_content_back',
        'wp_estate_user_dashboard_content_button_back',
        'wp_estate_user_dashboard_content_color',
        'wp_estate_property_multi_text',                
        'wp_estate_property_multi_child_text', 
        'wp_estate_theme_slider_type',
        'wp_estate_adv6_taxonomy',
        'wp_estate_adv6_taxonomy_terms',   
        'wp_estate_adv6_max_price',     
        'wp_estate_adv6_min_price',
        'wp_estate_adv_search_fields_no',
        'wp_estate_search_fields_no_per_row',
        'wp_estate_property_sidebar',
        'wp_estate_property_sidebar_name',
        'wp_estate_show_breadcrumbs',
        'wp_estate_global_property_page_template',
        'wp_estate_p_fontfamily',
        'wp_estate_p_fontsize',
        'wp_estate_p_fontsubset',
        'wp_estate_p_lineheight',
        'wp_estate_p_fontweight',
        'wp_estate_h1_fontfamily',
        'wp_estate_h1_fontsize',
        'wp_estate_h1_fontsubset',
        'wp_estate_h1_lineheight',
        'wp_estate_h1_fontweight',
        'wp_estate_h2_fontfamily',
        'wp_estate_h2_fontsize',
        'wp_estate_h2_fontsubset',
        'wp_estate_h2_lineheight',
        'wp_estate_h2_fontweight',
        'wp_estate_h3_fontfamily',
        'wp_estate_h3_fontsize',
        'wp_estate_h3_fontsubset',
        'wp_estate_h3_lineheight',
        'wp_estate_h3_fontweight',
        'wp_estate_h4_fontfamily',
        'wp_estate_h4_fontsize',
        'wp_estate_h4_fontsubset',
        'wp_estate_h4_lineheight',
        'wp_estate_h4_fontweight',
        'wp_estate_h5_fontfamily',
        'wp_estate_h5_fontsize',
        'wp_estate_h5_fontsubset',
        'wp_estate_h5_lineheight',
        'wp_estate_h5_fontweight',
        'wp_estate_h6_fontfamily',
        'wp_estate_h6_fontsize',
        'wp_estate_h6_fontsubset',
        'wp_estate_h6_lineheight',
        'wp_estate_h6_fontweight',
        'wp_estate_menu_fontfamily',
        'wp_estate_menu_fontsize',
        'wp_estate_menu_fontsubset',
        'wp_estate_menu_lineheight',
        'wp_estate_menu_fontweight',
        'wp_estate_transparent_logo_image',
        'wp_estate_stikcy_logo_image',
        'wp_estate_logo_image',
        'wp_estate_sidebar_boxed_font_color',
        'wp_estate_sidebar_heading_background_color',
        'wp_estate_use_same_colors_widgets',
        'wp_estate_map_controls_font_color',
        'wp_estate_map_controls_back',
        'wp_estate_transparent_menu_hover_font_color',
        'wp_estate_transparent_menu_font_color',
        'top_menu_hover_back_font_color',
        'wp_estate_top_menu_hover_type',
        'wp_estate_top_menu_hover_font_color',
        'wp_estate_menu_item_back_color',
        'wp_estate_sticky_menu_font_color',
        'wp_estate_top_menu_font_size',
        'wp_estate_menu_item_font_size',
        'wpestate_uset_unit',
        'wp_estate_sidebarwidget_internal_padding_top',
        'wp_estate_sidebarwidget_internal_padding_left',
        'wp_estate_sidebarwidget_internal_padding_bottom',
        'wp_estate_sidebarwidget_internal_padding_right',
        'wp_estate_widget_sidebar_border_size',
        'wp_estate_widget_sidebar_border_color',
        'wp_estate_unit_border_color',
        'wp_estate_unit_border_size',
        'wp_estate_blog_unit_min_height',
        'wp_estate_agent_unit_min_height',
        'wp_estate_agent_listings_per_row',
        'wp_estate_blog_listings_per_row',
        'wp_estate_content_area_back_color',
        'wp_estate_contentarea_internal_padding_top',
        'wp_estate_contentarea_internal_padding_left',
        'wp_estate_contentarea_internal_padding_bottom',
        'wp_estate_contentarea_internal_padding_right',
        'wp_estate_property_unit_color',
        'wp_estate_propertyunit_internal_padding_top',
        'wp_estate_propertyunit_internal_padding_left',
        'wp_estate_propertyunit_internal_padding_bottom',
        'wp_estate_propertyunit_internal_padding_right',       
        'wpestate_property_unit_structure',
        'wpestate_property_page_content',
        'wp_estate_main_grid_content_width',
        'wp_estate_main_content_width',
        'wp_estate_header_height',
        'wp_estate_sticky_header_height',
        'wp_estate_border_radius_corner',
        'wp_estate_cssbox_shadow',
        'wp_estate_prop_unit_min_height',
        'wp_estate_border_bottom_header',
        'wp_estate_sticky_border_bottom_header',
        'wp_estate_listings_per_row',
        'wp_estate_unit_card_type',
        'wp_estate_prop_unit_min_height',
        'wp_estate_main_grid_content_width',
        'wp_estate_header_height',
        'wp_estate_sticky_header_height',
        'wp_estate_border_bottom_header_sticky_color',
        'wp_estate_border_bottom_header_color',
        'wp_estate_show_top_bar_user_login',
        'wp_estate_show_top_bar_user_menu',
        'wp_estate_show_adv_search_general',
        'wp_estate_currency_symbol',
        'wp_estate_where_currency_symbol',
        'wp_estate_measure_sys',
        'wp_estate_facebook_login',
        'wp_estate_google_login',
        'wp_estate_yahoo_login',
        'wp_estate_wide_status',
        'wp_estate_header_type',
        'wp_estate_prop_no',
        'wp_estate_prop_image_number',
        'wp_estate_show_empty_city',
        'wp_estate_blog_sidebar',
        'wp_estate_blog_sidebar_name',
        'wp_estate_blog_unit',
        'wp_estate_general_latitude',
        'wp_estate_general_longitude',
        'wp_estate_default_map_zoom',
        'wp_estate_cache',
        'wp_estate_show_adv_search_map_close',
        'wp_estate_pin_cluster',
        'wp_estate_zoom_cluster',
        'wp_estate_hq_latitude',
        'wp_estate_hq_longitude',
        'wp_estate_idx_enable',
        'wp_estate_geolocation_radius',
        'wp_estate_min_height',
        'wp_estate_max_height',
        'wp_estate_keep_min',
        'wp_estate_paid_submission',
        'wp_estate_admin_submission',
        'wp_estate_user_agent',
        'wp_estate_price_submission',
        'wp_estate_price_featured_submission',
        'wp_estate_submission_curency',
        'wp_estate_free_mem_list',
        'wp_estate_free_feat_list',
        'wp_estate_free_feat_list_expiration',
        'wp_estate_custom_advanced_search',
        'wp_estate_adv_search_type',
        'wp_estate_show_adv_search',
        'wp_estate_show_adv_search_map_close',
        'wp_estate_cron_run',
        'wp_estate_show_no_features',
        'wp_estate_property_features_text',
        'wp_estate_property_description_text',
        'wp_estate_property_details_text',
        'wp_estate_status_list',
        'wp_estate_slider_cycle',
        'wp_estate_show_save_search',
        'wp_estate_search_alert',
        'wp_estate_adv_search_type',
        'wp_estate_color_scheme',
        'wp_estate_main_color',
        'wp_estate_background_color',
        'wp_estate_content_back_color',
        'wp_estate_header_color',
        'wp_estate_breadcrumbs_font_color',
        'wp_estate_font_color',
        'wp_estate_menu_items_color',
        'wp_estate_link_color',
        'wp_estate_headings_color',
        'wp_estate_sidebar_heading_boxed_color',
        'wp_estate_sidebar_heading_color',
        'wp_estate_sidebar_widget_color',
        'wp_estate_sidebar2_font_color',
        'wp_estate_footer_back_color',
        'wp_estate_footer_font_color',
        'wp_estate_footer_copy_color',
        'wp_estate_footer_copy_back_color',
        'wp_estate_menu_font_color',
        'wp_estate_menu_hover_back_color',
        'wp_estate_menu_hover_font_color',
        'wp_estate_menu_border_color',
        'wp_estate_top_bar_back',
        'wp_estate_top_bar_font',
        'wp_estate_adv_search_back_color',
        'wp_estate_adv_search_font_color',
        'wp_estate_box_content_back_color',
        'wp_estate_box_content_border_color',
        'wp_estate_hover_button_color',
        'wp_estate_show_g_search',
        'wp_estate_show_adv_search_extended',
        'wp_estate_readsys',
        'wp_estate_map_max_pins',
        'wp_estate_ssl_map',
        'wp_estate_enable_stripe',    
        'wp_estate_enable_paypal',    
        'wp_estate_enable_direct_pay',    
        'wp_estate_global_property_page_agent_sidebar',
        'wp_estate_global_prpg_slider_type',
        'wp_estate_global_prpg_content_type',
        'wp_estate_logo_margin',
        'wp_estate_header_transparent',
        'wp_estate_default_map_type',
        'wp_estate_prices_th_separator',
        'wp_estate_multi_curr',
        'wp_estate_date_lang',
        'wp_estate_blog_unit',
        'wp_estate_enable_autocomplete',
        'wp_estate_enable_user_pass',
        'wp_estate_auto_curency',
        'wp_estate_status_list',
        'wp_estate_custom_fields',
        'wp_estate_subject_password_reset_request',
        'wp_estate_password_reset_request',
        'wp_estate_subject_password_reseted',
        'wp_estate_password_reseted',
        'wp_estate_subject_purchase_activated',
        'wp_estate_purchase_activated',
        'wp_estate_subject_approved_listing',
        'wp_estate_approved_listing',
        'wp_estate_subject_new_wire_transfer',
        'wp_estate_new_wire_transfer',
        'wp_estate_subject_admin_new_wire_transfer',
        'wp_estate_admin_new_wire_transfer',
        'wp_estate_subject_admin_new_user',
        'wp_estate_admin_new_user',
        'wp_estate_subject_new_user',
        'wp_estate_new_user',
        'wp_estate_subject_admin_expired_listing',
        'wp_estate_admin_expired_listing',
        'wp_estate_subject_matching_submissions',
        'wp_estate_subject_paid_submissions',
        'wp_estate_paid_submissions',
        'wp_estate_subject_featured_submission',
        'wp_estate_featured_submission',
        'wp_estate_subject_account_downgraded',
        'wp_estate_account_downgraded',
        'wp_estate_subject_membership_cancelled',
        'wp_estate_membership_cancelled',
        'wp_estate_subject_downgrade_warning',
        'wp_estate_downgrade_warning',
        'wp_estate_subject_membership_activated',
        'wp_estate_membership_activated',
        'wp_estate_subject_free_listing_expired',
        'wp_estate_free_listing_expired',
        'wp_estate_subject_new_listing_submission',
        'wp_estate_new_listing_submission',
        'wp_estate_subject_listing_edit',
        'wp_estate_listing_edit',
        'wp_estate_subject_recurring_payment',
        'wp_estate_subject_recurring_payment',
         'wp_estate_custom_css',
        'wp_estate_company_name',
        'wp_estate_telephone_no',
        'wp_estate_mobile_no',
        'wp_estate_fax_ac',
        'wp_estate_skype_ac',
        'wp_estate_co_address',
        'wp_estate_facebook_link',
        'wp_estate_twitter_link',
        'wp_estate_pinterest_link',
        'wp_estate_instagram_link',
        'wp_estate_linkedin_link',
        'wp_estate_contact_form_7_agent',
        'wp_estate_contact_form_7_contact',
        'wp_estate_global_revolution_slider',
        'wp_estate_repeat_footer_back',
        'wp_estate_prop_list_slider',
        'wp_estate_agent_sidebar',
        'wp_estate_agent_sidebar_name',
        'wp_estate_property_list_type',
        'wp_estate_property_list_type_adv',
        'wp_estate_prop_unit',
        'wp_estate_general_font',
        'wp_estate_headings_font_subset',
        'wp_estate_copyright_message',
        'wp_estate_show_graph_prop_page',
        'wp_estate_map_style',
        'wp_estate_submission_curency_custom',
        'wp_estate_free_mem_list_unl',
        'wp_estate_adv_search_what',
        'wp_estate_adv_search_how',
        'wp_estate_adv_search_label',
        'wp_estate_adv_search_type',
        'wp_estate_adv_search_type',
        'wp_estate_show_save_search',
        'wp_estate_show_adv_search_slider',
        'wp_estate_show_adv_search_visible',
        'wp_estate_show_slider_price',
        'wp_estate_show_dropdowns',
        'wp_estate_show_slider_min_price',
        'wp_estate_show_slider_max_price',
        'wp_estate_wp_estate_adv_back_color',
        'wp_estate_adv_font_color',
        'wp_estate_adv_position',
        'wp_estate_feature_list',
        'wp_estate_show_no_features',
        'wp_estate_advanced_exteded',
        'wp_estate_adv_search_what',
        'wp_estate_adv_search_how',
        'wp_estate_adv_search_label',
        'wp_estate_adv_search_type',
        'wp_estate_property_adr_text',
        'wp_estate_property_features_text',
        'wp_estate_property_description_text',
        'wp_estate_property_details_text',
        'wp_estate_new_status',
        'wp_estate_status_list',
        'wp_estate_theme_slider',
        'wp_estate_slider_cycle',
        'wp_estate_use_mimify',
        'wp_estate_currency_label_main',
        'wp_estate_footer_background',
        'wp_estate_wide_footer',
        'wp_estate_show_footer',
    '   wp_estate_show_footer_copy',
        'wp_estate_footer_type',
        'wp_estate_logo_header_type',
        'wp_estate_wide_header',
        'wp_estate_logo_header_align',
        'wp_estate_text_header_align',
        'wp_estate_general_country'
        );
    
  
    
    $return_exported_data=array();
    // esc_html( get_option('wp_estate_where_currency_symbol') );
    foreach($export_options as $option){
        $real_option=get_option($option);
        
        if(is_array($real_option)){
            $return_exported_data[$option]= get_option($option) ;
        }else{
            $return_exported_data[$option]=esc_html( get_option($option) );
        }
     
    }
    
    return base64_encode( serialize( $return_exported_data) );
    
}
endif;


/////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///  Social $  Contact
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////


if( !function_exists('wpestate_theme_admin_social') ):
function wpestate_theme_admin_social(){
    $fax_ac                     =   esc_html (  stripslashes( get_option('wp_estate_fax_ac','') ) );
    $skype_ac                   =   esc_html (  stripslashes( get_option('wp_estate_skype_ac','') ) );
    $telephone_no               =   esc_html (  stripslashes( get_option('wp_estate_telephone_no','') ) );
    $mobile_no                  =   esc_html (  stripslashes( get_option('wp_estate_mobile_no','') ) );
    $company_name               =   esc_html (  stripslashes(get_option('wp_estate_company_name','') ) );
    $email_adr                  =   esc_html ( get_option('wp_estate_email_adr','') );
    $duplicate_email_adr        =   esc_html ( get_option('wp_estate_duplicate_email_adr','') );   
    $co_address                 =   esc_html ( stripslashes( get_option('wp_estate_co_address','') ) );
    $facebook_link              =   esc_html ( get_option('wp_estate_facebook_link','') );
    $twitter_link               =   esc_html ( get_option('wp_estate_twitter_link','') );
    $google_link                =   esc_html ( get_option('wp_estate_google_link','') );
    $linkedin_link              =   esc_html ( get_option('wp_estate_linkedin_link','') );
    $pinterest_link             =   esc_html ( get_option('wp_estate_pinterest_link','') );  
    $instagram_link             =   esc_html ( get_option('wp_estate_instagram_link','') );  
    $twitter_consumer_key       =   esc_html ( get_option('wp_estate_twitter_consumer_key','') );
    $twitter_consumer_secret    =   esc_html ( get_option('wp_estate_twitter_consumer_secret','') );
    $twitter_access_token       =   esc_html ( get_option('wp_estate_twitter_access_token','') );
    $twitter_access_secret      =   esc_html ( get_option('wp_estate_twitter_access_secret','') );
    $twitter_cache_time         =   intval   ( get_option('wp_estate_twitter_cache_time','') );
    $zillow_api_key             =   esc_html ( get_option('wp_estate_zillow_api_key','') );
    $facebook_api               =   esc_html ( get_option('wp_estate_facebook_api','') );
    $facebook_secret            =   esc_html ( get_option('wp_estate_facebook_secret','') );
    $company_contact_image      =   esc_html( get_option('wp_estate_company_contact_image','') );
    $google_oauth_api           =   esc_html ( get_option('wp_estate_google_oauth_api','') );
    $google_oauth_client_secret =   esc_html ( get_option('wp_estate_google_oauth_client_secret','') );
    $google_api_key             =   esc_html ( get_option('wp_estate_google_api_key','') );
    
    
    $social_array               =   array('no','yes');
   
    $facebook_login_select      = wpestate_dropdowns_theme_admin($social_array,'facebook_login');
    $google_login_select        = wpestate_dropdowns_theme_admin($social_array,'google_login');
    $yahoo_login_select         = wpestate_dropdowns_theme_admin($social_array,'yahoo_login');
    $contact_form_7_contact     = stripslashes( esc_html( get_option('wp_estate_contact_form_7_contact','') ) );
    $contact_form_7_agent       = stripslashes( esc_html( get_option('wp_estate_contact_form_7_agent','') ) );
    
    print '<div class="wpestate-tab-container">';
    print '<h1 class="wpestate-tabh1">Social</h1>';
    print '<a href="http://help.wpresidence.net/#!/social" target="_blank" class="help_link">'.__('help','wpestate').'</a>';
   
    print '<table class="form-table">     
        <tr valign="top">
            <th scope="row"><label for="company_contact_image">'.__('Image for Contact Page','wpestate').'</label></th>
            <td>
	        <input id="company_contact_image" type="text" size="36" name="company_contact_image" value="'.$company_contact_image.'" />
		<input id="company_contact_image_button" type="button"  class="upload_button button" value="'.__('Upload Image','wpestate').'" />
            </td>
        </tr> 
        
        <tr valign="top">
            <th scope="row"><label for="company_name">'.__('Company Name','wpestate').'</label></th>
            <td>  <input id="company_name" type="text" size="36" name="company_name" value="'.$company_name.'" /></td>
        </tr>   
        
    	<tr valign="top">
            <th scope="row"><label for="email_adr">'.__('Email','wpestate').'</label></th>
            <td>  <input id="email_adr" type="text" size="36" name="email_adr" value="'.$email_adr.'" /></td>
        </tr>    
        
        <tr valign="top">
            <th scope="row"><label for="duplicate_email_adr">'.__('Send all contact emails to:','wpestate').'</label></th>
            <td>  <input id="duplicate_email_adr" type="text" size="36" name="duplicate_email_adr" value="'.$duplicate_email_adr.'" /></td>
        </tr> 
        
        <tr valign="top">
            <th scope="row"><label for="telephone_no">'.__('Telephone','wpestate').'</label></th>
            <td>  <input id="telephone_no" type="text" size="36" name="telephone_no" value="'.$telephone_no.'" /></td>
        </tr> 
        
        <tr valign="top">
            <th scope="row"><label for="mobile_no">'.__('Mobile','wpestate').'</label></th>
            <td>  <input id="mobile_no" type="text" size="36" name="mobile_no" value="'.$mobile_no.'" /></td>
        </tr> 
        
         <tr valign="top">
            <th scope="row"><label for="fax_ac">'.__('Fax','wpestate').'</label></th>
            <td>  <input id="fax_ac" type="text" size="36" name="fax_ac" value="'.$fax_ac.'" /></td>
        </tr> 
        
        <tr valign="top">
            <th scope="row"><label for="skype_ac">'.__('Skype','wpestate').'</label></th>
            <td>  <input id="skype_ac" type="text" size="36" name="skype_ac" value="'.$skype_ac.'" /></td>
        </tr> 
        
        <tr valign="top">
            <th scope="row"><label for="co_address">'.__('Address','wpestate').'</label></th>
            <td><textarea cols="57" rows="2" name="co_address" id="co_address">'.$co_address.'</textarea></td>
        </tr> 
        
        <tr valign="top">
            <th scope="row"><label for="facebook_link">'.__('Facebook Link','wpestate').'</label></th>
            <td>  <input id="facebook_link" type="text" size="36" name="facebook_link" value="'.$facebook_link.'" /></td>
        </tr>        
        
        <tr valign="top">
            <th scope="row"><label for="twitter_link">'.__('Twitter Page Link','wpestate').'</label></th>
            <td>  <input id="twitter_link" type="text" size="36" name="twitter_link" value="'.$twitter_link.'" /></td>
        </tr>
         
        <tr valign="top">
            <th scope="row"><label for="google_link">'.__('Google+ Link','wpestate').'</label></th>
            <td>  <input id="google_link" type="text" size="36" name="google_link" value="'.$google_link.'" /></td>
        </tr>
        
        <tr valign="top">
            <th scope="row"><label for="pinterest_link">'.__('Pinterest Link','wpestate').'</label></th>
            <td>  <input id="pinterest_link" type="text" size="36" name="pinterest_link" value="'.$pinterest_link.'" /></td>
        </tr>
        
        <tr valign="top">
            <th scope="row"><label for="linkedin_link">'.__('Linkedin Link','wpestate').'</label></th>
            <td>  <input id="linkedin_link" type="text" size="36" name="linkedin_link" value="'.$linkedin_link.'" /></td>
        </tr>
        

        <tr valign="top">
            <th scope="row"><label for="twitter_consumer_key">'.__('Twitter Consumer Key','wpestate').'</label></th>
            <td>  <input id="twitter_consumer_key" type="text" size="36" name="twitter_consumer_key" value="'.$twitter_consumer_key.'" /></td>
        </tr>
        
        <tr valign="top">
            <th scope="row"><label for="twitter_consumer_secret">'.__('Twitter Consumer Secret','wpestate').'</label></th>
            <td>  <input id="twitter_consumer_secret" type="text" size="36" name="twitter_consumer_secret" value="'.$twitter_consumer_secret.'" /></td>
        </tr>
        
        <tr valign="top">
            <th scope="row"><label for="twitter_access_token">'.__('Twitter Access Token','wpestate').'</label></th>
            <td>  <input id="twitter_account" type="text" size="36" name="twitter_access_token" value="'.$twitter_access_token.'" /></td>
        </tr>
        
        <tr valign="top">
            <th scope="row"><label for="twitter_access_secret">'.__('Twitter Access Token Secret','wpestate').'</label></th>
            <td>  <input id="twitter_access_secret" type="text" size="36" name="twitter_access_secret" value="'.$twitter_access_secret.'" /></td>
        </tr>
        
        <tr valign="top">
            <th scope="row"><label for="twitter_cache_time">'.__('Twitter Cache Time in hours','wpestate').'</label></th>
            <td>  <input id="twitter_cache_time" type="text" size="36" name="twitter_cache_time" value="'.$twitter_cache_time.'" /></td>
        </tr>
         
        <tr valign="top">
            <th scope="row"><label for="facebook_api">'.__('Facebook Api Key (for Facebook login)','wpestate').'</label></th>
            <td>  <input id="facebook_api" type="text" size="36" name="facebook_api" value="'.$facebook_api.'" /></td>
        </tr>
        
        <tr valign="top">
            <th scope="row"><label for="facebook_secret">'.__('Facebook secret code (for Facebook login) ','wpestate').'</label></th>
            <td>  <input id="facebook_secret" type="text" size="36" name="facebook_secret" value="'.$facebook_secret.'" /></td>
        </tr>
       
        <tr valign="top">
            <th scope="row"><label for="google_oauth_api">'.__('Google OAuth client id (for Google login)','wpestate').'</label></th>
            <td>  <input id="google_oauth_api" type="text" size="36" name="google_oauth_api" value="'.$google_oauth_api.'" /></td>
        </tr>
        
        <tr valign="top">
            <th scope="row"><label for="google_oauth_client_secret">'.__('Google Client Secret (for Google login)','wpestate').'</label></th>
            <td>  <input id="google_oauth_client_secret" type="text" size="36" name="google_oauth_client_secret" value="'.$google_oauth_client_secret.'" /></td>
        </tr>
        
        <tr valign="top">
            <th scope="row"><label for="google_api_key">'.__('Google Api key (for Google login)','wpestate').'</label></th>
            <td>  <input id="google_api_key" type="text" size="36" name="google_api_key" value="'.$google_api_key.'" /></td>
        </tr>
        
        <tr valign="top">
            <th scope="row"><label for="facebook_login">'.__('Allow login via Facebook ? ','wpestate').'</label></th>
            <td> <select id="facebook_login" name="facebook_login">
                    '.$facebook_login_select.'
                </select>
            </td>
        </tr>
        
        <tr valign="top">
            <th scope="row"><label for="google_login">'.__('Allow login via Google ?','wpestate').' </label></th>
            <td> <select id="google_login" name="google_login">
                    '.$google_login_select.'
                </select>
            </td>
        </tr>
        
        <tr valign="top">
            <th scope="row"><label for="yahoo_login">'.__('Allow login via Yahoo ? ','wpestate').'</label></th>
            <td> <select id="yahoo_login" name="yahoo_login">
                    '.$yahoo_login_select.'
                </select>
            </td>
        </tr>
        
        <tr valign="top">
            <th scope="row"><label for="contact_form_7_agent">'.__('Contact form 7 code for agent (ex: [contact-form-7 id="2725" title="contact me"])','wpestate').'</label></th>
            <td> 
                <input type="text" size="36" id="contact_form_7_agent" name="contact_form_7_agent" value="'.$contact_form_7_agent.'" />
            </td>
        </tr>
        
        <tr valign="top">
            <th scope="row"><label for="contact_form_7_contact">'.__('Contact form 7 code for contact page template (ex: [contact-form-7 id="2725" title="contact me"])','wpestate').'</label></th>
            <td> 
                 <input type="text" size="36" id="contact_form_7_contact" name="contact_form_7_contact" value="'.$contact_form_7_contact.'" />
            </td>
        </tr>
        

    </table>
    <p class="submit">
      <input type="submit" name="submit" id="submit" class="button-primary"  value="'.__('Save Changes','wpestate').'" />
    </p>';
print '</div>';
}
endif; // end   wpestate_theme_admin_social  








/////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///  help and custom
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////
if( !function_exists('wpestate_theme_admin_help') ):
function wpestate_theme_admin_help(){
    print '<div class="wpestate-tab-container">';
    print '<h1 class="wpestate-tabh1">'.__('Help','wpestate').'</h1>';
    print '<table class="form-table">  
 
        <tr valign="top">
            <td> '.__('For theme help please check http://help.wpresidence.net/. If your question is not here, please go to http://support.wpestate.org, create an account and post a ticket. The registration is simple and as soon as you send a ticket we are notified. We usually answer in the next 24h (except weekends). Please use this system and not the email. It will help us answer your questions much faster. Thank you!','wpestate').'
            </td>             
        </tr>
        
        <tr valign="top">
            <td> '.__('For custom work on this theme please go to  <a href="http://support.wpestate.org/" target="_blank">http://support.wpestate.org</a>, create a ticket with your request and we will offer a free quote.','wpestate').'
            </td>             
        </tr>
        
        <tr valign="top">
            <td> '.__('For help files please go to  <a href="http://help.wpresidence.net/">http://help.wpresidence.net</a>.','wpestate').'
            </td>             
        </tr>
        
         
        <tr valign="top">
            <td>  '.__('Subscribe to our mailing list in order to receive news about new features and theme upgrades. <a href="http://eepurl.com/CP5U5">Subscribe Here!</a>','wpestate').'
            </td>             
        </tr>
        </table>
        
      ';
    print '</div>';
}
endif; // end   wpestate_theme_admin_help  



if( !function_exists('wpestate_general_country_list') ):
    function wpestate_general_country_list($selected){
        $countries = array(__("Afghanistan","wpestate"),__("Albania","wpestate"),__("Algeria","wpestate"),__("American Samoa","wpestate"),__("Andorra","wpestate"),__("Angola","wpestate"),__("Anguilla","wpestate"),__("Antarctica","wpestate"),__("Antigua and Barbuda","wpestate"),__("Argentina","wpestate"),__("Armenia","wpestate"),__("Aruba","wpestate"),__("Australia","wpestate"),__("Austria","wpestate"),__("Azerbaijan","wpestate"),__("Bahamas","wpestate"),__("Bahrain","wpestate"),__("Bangladesh","wpestate"),__("Barbados","wpestate"),__("Belarus","wpestate"),__("Belgium","wpestate"),__("Belize","wpestate"),__("Benin","wpestate"),__("Bermuda","wpestate"),__("Bhutan","wpestate"),__("Bolivia","wpestate"),__("Bosnia and Herzegowina","wpestate"),__("Botswana","wpestate"),__("Bouvet Island","wpestate"),__("Brazil","wpestate"),__("British Indian Ocean Territory","wpestate"),__("Brunei Darussalam","wpestate"),__("Bulgaria","wpestate"),__("Burkina Faso","wpestate"),__("Burundi","wpestate"),__("Cambodia","wpestate"),__("Cameroon","wpestate"),__("Canada","wpestate"),__("Cape Verde","wpestate"),__("Cayman Islands","wpestate"),__("Central African Republic","wpestate"),__("Chad","wpestate"),__("Chile","wpestate"),__("China","wpestate"),__("Christmas Island","wpestate"),__("Cocos (Keeling) Islands","wpestate"),__("Colombia","wpestate"),__("Comoros","wpestate"),__("Congo","wpestate"),__("Congo, the Democratic Republic of the","wpestate"),__("Cook Islands","wpestate"),__("Costa Rica","wpestate"),__("Cote d'Ivoire","wpestate"),__("Croatia (Hrvatska)","wpestate"),__("Cuba","wpestate"),__("Cyprus","wpestate"),__("Czech Republic","wpestate"),__("Denmark","wpestate"),__("Djibouti","wpestate"),__("Dominica","wpestate"),__("Dominican Republic","wpestate"),__("East Timor","wpestate"),__("Ecuador","wpestate"),__("Egypt","wpestate"),__("El Salvador","wpestate"),__("Equatorial Guinea","wpestate"),__("Eritrea","wpestate"),__("Estonia","wpestate"),__("Ethiopia","wpestate"),__("Falkland Islands (Malvinas)","wpestate"),__("Faroe Islands","wpestate"),__("Fiji","wpestate"),__("Finland","wpestate"),__("France","wpestate"),__("France Metropolitan","wpestate"),__("French Guiana","wpestate"),__("French Polynesia","wpestate"),__("French Southern Territories","wpestate"),__("Gabon","wpestate"),__("Gambia","wpestate"),__("Georgia","wpestate"),__("Germany","wpestate"),__("Ghana","wpestate"),__("Gibraltar","wpestate"),__("Greece","wpestate"),__("Greenland","wpestate"),__("Grenada","wpestate"),__("Guadeloupe","wpestate"),__("Guam","wpestate"),__("Guatemala","wpestate"),__("Guinea","wpestate"),__("Guinea-Bissau","wpestate"),__("Guyana","wpestate"),__("Haiti","wpestate"),__("Heard and Mc Donald Islands","wpestate"),__("Holy See (Vatican City State)","wpestate"),__("Honduras","wpestate"),__("Hong Kong","wpestate"),__("Hungary","wpestate"),__("Iceland","wpestate"),__("India","wpestate"),__("Indonesia","wpestate"),__("Iran (Islamic Republic of)","wpestate"),__("Iraq","wpestate"),__("Ireland","wpestate"),__("Israel","wpestate"),__("Italy","wpestate"),__("Jamaica","wpestate"),__("Japan","wpestate"),__("Jordan","wpestate"),__("Kazakhstan","wpestate"),__("Kenya","wpestate"),__("Kiribati","wpestate"),__("Korea, Democratic People's Republic of","wpestate"),__("Korea, Republic of","wpestate"),__("Kuwait","wpestate"),__("Kyrgyzstan","wpestate"),__("Lao, People's Democratic Republic","wpestate"),__("Latvia","wpestate"),__("Lebanon","wpestate"),__("Lesotho","wpestate"),__("Liberia","wpestate"),__("Libyan Arab Jamahiriya","wpestate"),__("Liechtenstein","wpestate"),__("Lithuania","wpestate"),__("Luxembourg","wpestate"),__("Macau","wpestate"),__("Macedonia (FYROM)","wpestate"),__("Madagascar","wpestate"),__("Malawi","wpestate"),__("Malaysia","wpestate"),__("Maldives","wpestate"),__("Mali","wpestate"),__("Malta","wpestate"),__("Marshall Islands","wpestate"),__("Martinique","wpestate"),__("Mauritania","wpestate"),__("Mauritius","wpestate"),__("Mayotte","wpestate"),__("Mexico","wpestate"),__("Micronesia, Federated States of","wpestate"),__("Moldova, Republic of","wpestate"),__("Monaco","wpestate"),__("Mongolia","wpestate"),__("Montserrat","wpestate"),__("Morocco","wpestate"),__("Mozambique","wpestate"),__("Montenegro","wpestate"),__("Myanmar","wpestate"),__("Namibia","wpestate"),__("Nauru","wpestate"),__("Nepal","wpestate"),__("Netherlands","wpestate"),__("Netherlands Antilles","wpestate"),__("New Caledonia","wpestate"),__("New Zealand","wpestate"),__("Nicaragua","wpestate"),__("Niger","wpestate"),__("Nigeria","wpestate"),__("Niue","wpestate"),__("Norfolk Island","wpestate"),__("Northern Mariana Islands","wpestate"),__("Norway","wpestate"),__("Oman","wpestate"),__("Pakistan","wpestate"),__("Palau","wpestate"),__("Panama","wpestate"),__("Papua New Guinea","wpestate"),__("Paraguay","wpestate"),__("Peru","wpestate"),__("Philippines","wpestate"),__("Pitcairn","wpestate"),__("Poland","wpestate"),__("Portugal","wpestate"),__("Puerto Rico","wpestate"),__("Qatar","wpestate"),__("Reunion","wpestate"),__("Romania","wpestate"),__("Russian Federation","wpestate"),__("Rwanda","wpestate"),__("Saint Kitts and Nevis","wpestate"),__("Saint Martin","wpestate"),__("Saint Lucia","wpestate"),__("Saint Vincent and the Grenadines","wpestate"),__("Samoa","wpestate"),__("San Marino","wpestate"),__("Sao Tome and Principe","wpestate"),__("Saudi Arabia","wpestate"),__("Senegal","wpestate"),__("Seychelles","wpestate"),__("Serbia","wpestate"),__("Sierra Leone","wpestate"),__("Singapore","wpestate"),__("Slovakia (Slovak Republic)","wpestate"),__("Slovenia","wpestate"),__("Solomon Islands","wpestate"),__("Somalia","wpestate"),__("South Africa","wpestate"),__("South Georgia and the South Sandwich Islands","wpestate"),__("Spain","wpestate"),__("Sri Lanka","wpestate"),__("St. Helena","wpestate"),__("St. Pierre and Miquelon","wpestate"),__("Sudan","wpestate"),__("Suriname","wpestate"),__("Svalbard and Jan Mayen Islands","wpestate"),__("Swaziland","wpestate"),__("Sweden","wpestate"),__("Switzerland","wpestate"),__("Syrian Arab Republic","wpestate"),__("Taiwan, Province of China","wpestate"),__("Tajikistan","wpestate"),__("Tanzania, United Republic of","wpestate"),__("Thailand","wpestate"),__("Togo","wpestate"),__("Tokelau","wpestate"),__("Tonga","wpestate"),__("Trinidad and Tobago","wpestate"),__("Tunisia","wpestate"),__("Turkey","wpestate"),__("Turkmenistan","wpestate"),__("Turks and Caicos Islands","wpestate"),__("Tuvalu","wpestate"),__("Uganda","wpestate"),__("Ukraine","wpestate"),__("United Arab Emirates","wpestate"),__("United Kingdom","wpestate"),__("United States","wpestate"),__("United States Minor Outlying Islands","wpestate"),__("Uruguay","wpestate"),__("Uzbekistan","wpestate"),__("Vanuatu","wpestate"),__("Venezuela","wpestate"),__("Vietnam","wpestate"),__("Virgin Islands (British)","wpestate"),__("Virgin Islands (U.S.)","wpestate"),__("Wallis and Futuna Islands","wpestate"),__("Western Sahara","wpestate"),__("Yemen","wpestate"),__("Zambia","wpestate"),__("Zimbabwe","wpestate"));
        $country_select='<select id="general_country" style="width: 200px;" name="general_country">';

        foreach($countries as $country){
            $country_select.='<option value="'.$country.'"';  
            if($selected==$country){
                $country_select.='selected="selected"';
            }
            $country_select.='>'.$country.'</option>';
        }

        $country_select.='</select>';
        return $country_select;
    }
endif; // end   wpestate_general_country_list  


function wpestate_sorting_function($a, $b) {
    return $a[3] - $b[3];
};

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///  Wpestate Price settings
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////






if( !function_exists('new_wpestate_theme_admin_general_settings') ):
function new_wpestate_theme_admin_general_settings(){
    
    $cache_array                    =   array('yes','no');
    $social_array                   =   array('no','yes');
    
    $general_country                =   esc_html( get_option('wp_estate_general_country') );
    $measure_sys='';
    $measure_array=array( __('feet','wpestate')     =>__('ft','wpestate'),
                          __('meters','wpestate')   =>__('m','wpestate') 
                        );
    
    $measure_array_status= esc_html( get_option('wp_estate_measure_sys','') );

    foreach($measure_array as $key => $value){
            $measure_sys.='<option value="'.$value.'"';
            if ($measure_array_status==$value){
                $measure_sys.=' selected="selected" ';
            }
            $measure_sys.='>'.__('square','wpestate').' '.$key.' - '.$value.'<sup>2</sup></option>';
    }

    
    
    print'<div class="estate_option_row">
        <div class="label_option_row">'.__('Country','wpestate').'</div>
        <div class="option_row_explain">'.__('Select default country','wpestate').'</div>    
        '.wpestate_general_country_list($general_country).'
    </div>';
    
    print'<div class="estate_option_row">
        <div class="label_option_row">'.__('Measurement Unit','wpestate').'</div>
        <div class="option_row_explain">'.__('Select the measurement unit you will use on the website','wpestate').'</div>    
            <select id="measure_sys" name="measure_sys">
                '.$measure_sys.'
            </select>
    </div>';
    
    
    $enable_autocomplete_symbol = wpestate_dropdowns_theme_admin($cache_array,'enable_autocomplete');
      
    print'<div class="estate_option_row">
        <div class="label_option_row">'.__('Enable Autocomplete in Front End Submission Form','wpestate').'</div>
        <div class="option_row_explain">'.__('If yes, the address field in front end submission form will use Google Places autocomplete.','wpestate').'</div>    
            <select id="enable_autocomplete" name="enable_autocomplete">
                '.$enable_autocomplete_symbol.'
            </select>
    </div>';
    
    
    $enable_user_pass_symbol    = wpestate_dropdowns_theme_admin($cache_array,'enable_user_pass');

    
    print'<div class="estate_option_row">
        <div class="label_option_row">'.__('Users can type the password on registration form','wpestate').'</div>
        <div class="option_row_explain">'.__('If no, users will get the auto generated password via email','wpestate').'</div>    
            <select id="enable_user_pass" name="enable_user_pass">
                '.$enable_user_pass_symbol.'
            </select>
    </div>';
    
    
    $date_languages=array(  'xx'=> 'default',
                            'af'=>'Afrikaans',
                            'ar'=>'Arabic',
                            'ar-DZ' =>'Algerian',
                            'az'=>'Azerbaijani',
                            'be'=>'Belarusian',
                            'bg'=>'Bulgarian',
                            'bs'=>'Bosnian',
                            'ca'=>'Catalan',
                            'cs'=>'Czech',
                            'cy-GB'=>'Welsh/UK',
                            'da'=>'Danish',
                            'de'=>'German',
                            'el'=>'Greek',
                            'en-AU'=>'English/Australia',
                            'en-GB'=>'English/UK',
                            'en-NZ'=>'English/New Zealand',
                            'eo'=>'Esperanto',
                            'es'=>'Spanish',
                            'et'=>'Estonian',
                            'eu'=>'Karrikas-ek',
                            'fa'=>'Persian',
                            'fi'=>'Finnish',
                            'fo'=>'Faroese',
                            'fr'=>'French',
                            'fr-CA'=>'Canadian-French',
                            'fr-CH'=>'Swiss-French',
                            'gl'=>'Galician',
                            'he'=>'Hebrew',
                            'hi'=>'Hindi',
                            'hr'=>'Croatian',
                            'hu'=>'Hungarian',
                            'hy'=>'Armenian',
                            'id'=>'Indonesian',
                            'ic'=>'Icelandic',
                            'it'=>'Italian',
                            'it-CH'=>'Italian-CH',
                            'ja'=>'Japanese',
                            'ka'=>'Georgian',
                            'kk'=>'Kazakh',
                            'km'=>'Khmer',
                            'ko'=>'Korean',
                            'ky'=>'Kyrgyz',
                            'lb'=>'Luxembourgish',
                            'lt'=>'Lithuanian',
                            'lv'=>'Latvian',
                            'mk'=>'Macedonian',
                            'ml'=>'Malayalam',
                            'ms'=>'Malaysian',
                            'nb'=>'Norwegian',
                            'nl'=>'Dutch',
                            'nl-BE'=>'Dutch-Belgium',
                            'nn'=>'Norwegian-Nynorsk',
                            'no'=>'Norwegian',
                            'pl'=>'Polish',
                            'pt'=>'Portuguese',
                            'pt-BR'=>'Brazilian',
                            'rm'=>'Romansh',
                            'ro'=>'Romanian',
                            'ru'=>'Russian',
                            'sk'=>'Slovak',
                            'sl'=>'Slovenian',
                            'sq'=>'Albanian',
                            'sr'=>'Serbian',
                            'sr-SR'=>'Serbian-i18n',
                            'sv'=>'Swedish',
                            'ta'=>'Tamil',
                            'th'=>'Thai',
                            'tj'=>'Tajiki',
                            'tr'=>'Turkish',
                            'uk'=>'Ukrainian',
                            'vi'=>'Vietnamese',
                            'zh-CN'=>'Chinese',
                            'zh-HK'=>'Chinese-Hong-Kong',
                            'zh-TW'=>'Chinese Taiwan',
        );  

    
    $date_lang_symbol =  wpestate_dropdowns_theme_admin_with_key($date_languages,'date_lang');
    print'<div class="estate_option_row">
        <div class="label_option_row">'.__('Language for datepicker','wpestate').'</div>
        <div class="option_row_explain">'.__('This applies for the calendar field type available for properties.','wpestate').'</div>    
        <select id="date_lang" name="date_lang">
            '.$date_lang_symbol.'
         </select>
    </div>';
      
    $google_analytics_code          =   esc_html ( get_option('wp_estate_google_analytics_code','') );
  
    
    
    print'<div class="estate_option_row">
        <div class="label_option_row">'.__('Google Analytics Tracking id','wpestate').'</div>
        <div class="option_row_explain">'.__('Google Analytics Tracking id (ex UA-41924406-1)','wpestate').'</div>    
        <input  name="google_analytics_code" id="google_analytics_code" value="'.$google_analytics_code.'"></input>
    </div>';
    
    print ' <div class="estate_option_row_submit">
    <input type="submit" name="submit"  class="new_admin_submit " value="'.__('Save Changes','wpestate').'" />
    </div>';
   
}
endif; // end   wpestate_theme_admin_general_settings  



if( !function_exists('new_wpestate_theme_admin_logos_favicon') ):
function new_wpestate_theme_admin_logos_favicon(){
    $cache_array                    =   array('yes','no');
    $social_array                   =   array('no','yes');
    $logo_image                     =   esc_html( get_option('wp_estate_logo_image','') );
    //$footer_logo_image              =   esc_html( get_option('wp_estate_footer_logo_image','') );
    $mobile_logo_image              =   esc_html( get_option('wp_estate_mobile_logo_image','') );
    $favicon_image                  =   esc_html( get_option('wp_estate_favicon_image','') );
    
    
  
    
    
    print'<div class="estate_option_row">
        <div class="label_option_row">'.__('Your Favicon','wpestate').'</div>
        <div class="option_row_explain">'.__('Upload site favicon in .ico, .png, .jpg or .gif format','wpestate').'</div>    
            <input id="favicon_image" type="text" size="36" name="favicon_image" value="'.$favicon_image.'" />
            <input id="favicon_image_button" type="button"  class="upload_button button" value="'.__('Upload Favicon','wpestate').'" />
       
    </div>';   
     
    print'<div class="estate_option_row">
        <div class="label_option_row">'.__('To add Retina logo versions, create retina logo, add _2x at the end of name of the original file (for ex logo_2x.jpg) and upload it in the same uploads folder as the non retina logo.','wpestate').'</div>
    </div>';
    print'<div class="estate_option_row">
        <div class="label_option_row">'.__('Your Logo','wpestate').'</div>
        <div class="option_row_explain">'.__('We will use the image at the uploaded size. So make sure it fits your design. If you add images directly into the input fields (without Upload button) please use the full image path. For ex: http://www.wpresidence..... . If you use the "upload"  button use also "Insert into Post" button from the pop up window.','wpestate').'</div>    
            <input id="logo_image" type="text" size="36" name="logo_image" value="'.$logo_image.'" />
            <input id="logo_image_button" type="button"  class="upload_button button" value="'.__('Upload Logo','wpestate').'" /></br>
            '.'
       
    </div>';
    $stikcy_logo_image    =   esc_html( get_option('wp_estate_stikcy_logo_image','') );
    print'<div class="estate_option_row">
        <div class="label_option_row">'.__('Your Sticky Logo','wpestate').'</div>
        <div class="option_row_explain">'.__('If you add images directly into the input fields (without Upload button) please use the full image path. For ex: http://www.wpresidence..... . If you use the "upload"  button use also "Insert into Post" button from the pop up window.','wpestate').'</div>    
            <input id="stikcy_logo_image" type="text" size="36" name="stikcy_logo_image" value="'.$stikcy_logo_image.'" />
            <input id="stikcy_logo_image_button" type="button"  class="upload_button button" value="'.__('Upload Logo','wpestate').'" /></br>
            '.'
       
    </div>';
    
    $transparent_logo_image    =   esc_html( get_option('wp_estate_transparent_logo_image','') );
    print'<div class="estate_option_row">
        <div class="label_option_row">'.__('Your Transparent Header Logo','wpestate').'</div>
        <div class="option_row_explain">'.__('If you add images directly into the input fields (without Upload button) please use the full image path. For ex: http://www.wpresidence..... . If you use the "upload"  button use also "Insert into Post" button from the pop up window.','wpestate').'</div>    
            <input id="transparent_logo_image" type="text" size="36" name="transparent_logo_image" value="'.$transparent_logo_image.'" />
            <input id="transparent_logo_image_button" type="button"  class="upload_button button" value="'.__('Upload Logo','wpestate').'" /></br>
            '.'
       
    </div>';
     
     
    $logo_margin                =   intval( get_option('wp_estate_logo_margin','') ); 
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Margin Top for logo','wpestate').'</div>
    <div class="option_row_explain">'.__('Add logo margin top number.','wpestate').'</div>    
        <input type="text" id="logo_margin" name="logo_margin" value="'.$logo_margin.'"> 
    </div>';
        
     /* 
    print'<div class="estate_option_row">
        <div class="label_option_row">'.__('Retina Logo','wpestate').'</div>
        <div class="option_row_explain">'.__('Retina ready logo (add _2x after the name. For ex logo_2x.jpg) ','wpestate').'</div>    
            <input id="footer_logo_image" type="text" size="36" name="footer_logo_image" value="'.$footer_logo_image.'" />
            <input id="footer_logo_image_button" type="button"  class="upload_button button" value="'.__('Upload Logo','wpestate').'" />
       
    </div>';
     */
      
    
    print'<div class="estate_option_row">
        <div class="label_option_row">'.__('Mobile/Tablets Logo','wpestate').'</div>
        <div class="option_row_explain">'.__('Upload mobile logo in jpg or png format.','wpestate').'</div>    
            <input id="mobile_logo_image" type="text" size="36" name="mobile_logo_image" value="'.$mobile_logo_image.'" />
            <input id="mobile_logo_image_button" type="button"  class="upload_button button" value="'.__('Upload Logo','wpestate').'" />
       
    </div>';
      
    print ' <div class="estate_option_row_submit">
    <input type="submit" name="submit"  class="new_admin_submit " value="'.__('Save Changes','wpestate').'" />
    </div>';
       
}
endif;


if( !function_exists('new_wpestate_header_settings') ):
function new_wpestate_header_settings(){
    $cache_array                =   array('yes','no');
     
    $show_top_bar_user_menu_symbol      = wpestate_dropdowns_theme_admin($cache_array,'show_top_bar_user_menu');
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Show top bar widget menu ?','wpestate').'</div>
    <div class="option_row_explain">'.__('Enable or disable top bar widget area.','wpestate').'</div>    
       <select id="show_top_bar_user_menu" name="show_top_bar_user_menu">
            '.$show_top_bar_user_menu_symbol.'
        </select>
    </div>';
    
    
    $show_top_bar_user_login_symbol     = wpestate_dropdowns_theme_admin($cache_array,'show_top_bar_user_login');
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Show user login menu in header ?','wpestate').'</div>
    <div class="option_row_explain">'.__('Enable or disable user login menu in header.','wpestate').'</div>    
        <select id="show_top_bar_user_login" name="show_top_bar_user_login">
            '.$show_top_bar_user_login_symbol.'
        </select>
    </div>';
    
          
    $cache_array                =   array('no','yes');
    $header_transparent_select  =   wpestate_dropdowns_theme_admin($cache_array,'header_transparent');
    
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Global transparent header?','wpestate').'</div>
    <div class="option_row_explain">'.__('Enable or disable the use of transparent header globally.','wpestate').'</div>    
        <select id="header_transparent" name="header_transparent">
            '.$header_transparent_select.'
        </select>
    </div>';
    
    
    $header_array_logo  =   array(
                            'type1',
                            'type2',
                            'type3',
                            'type4'
                        );
    $logo_header_select   = wpestate_dropdowns_theme_admin($header_array_logo,'logo_header_type');

    
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Header Type?','wpestate').'</div>
    <div class="option_row_explain">'.__('Select header type.Header type 4 will NOT work with half map property list template.','wpestate').'</div>    
        <select id="logo_header_type" name="logo_header_type">
            '.$logo_header_select.'
        </select>
    </div>';
    
    
    $header_array_logo_align  =   array(
                            'left',
                            'center',
                            'right',
                        );
 
    
    $logo_header_align_select   = wpestate_dropdowns_theme_admin($header_array_logo_align,'logo_header_align');
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Header Align(Logo Position)?','wpestate').'</div>
    <div class="option_row_explain">'.__('Select header alignment.Please note that there is no "center" align for type 3 and 4.','wpestate').'</div>    
        <select id="logo_header_align" name="logo_header_align">
            '.$logo_header_align_select.'
        </select>
    </div>';
           
    
    $text_header_align_select   = wpestate_dropdowns_theme_admin($header_array_logo_align,'text_header_align');
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Header 3&4 Text Align?','wpestate').'</div>
    <div class="option_row_explain">'.__('Select a text alignment for header 3&4.','wpestate').'</div>    
        <select id="text_header_align" name="text_header_align">
            '.$text_header_align_select.'
        </select>
    </div>';
    
    
    
    $cache_array                =   array('no','yes');
    $wide_header_select  =   wpestate_dropdowns_theme_admin($cache_array,'wide_header');
    
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Wide Header ? ','wpestate').'</div>
    <div class="option_row_explain">'.__('make the header 100%.','wpestate').'</div>    
        <select id="wide_header" name="wide_header">
            '.$wide_header_select.'
        </select>
    </div>';
    
    
    $header_array   =   array(
                            'none',
                            'image',
                            'theme slider',
                            'revolution slider',
                            'google map'
                            );
    $header_select   = wpestate_dropdowns_theme_admin_with_key($header_array,'header_type');

    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Media Header Type?','wpestate').'</div>
    <div class="option_row_explain">'.__('Select what media header to use globally.','wpestate').'</div>    
        <select id="header_type" name="header_type">
            '.$header_select.'
        </select>
    </div>';
       
    
    
   
          
   
    $global_revolution_slider   =   get_option('wp_estate_global_revolution_slider','');
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Global Revolution Slider','wpestate').'</div>
    <div class="option_row_explain">'.__('If media header is set to Revolution Slider, type the slider name and save.','wpestate').'</div>    
        <input type="text" id="global_revolution_slider" name="global_revolution_slider" value="'.$global_revolution_slider.'">   
    </div>';
    
    
    $global_header              =   get_option('wp_estate_global_header','');
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Global Header Static Image','wpestate').'</div>
    <div class="option_row_explain">'.__('If media header is set to image, add the image below. ','wpestate').'</div>    
        <input id="global_header" type="text" size="36" name="global_header" value="'.$global_header.'" />
        <input id="global_header_button" type="button"  class="upload_button button" value="'.__('Upload Header Image','wpestate').'" />
    </div>';
    
    
    print ' <div class="estate_option_row_submit">
    <input type="submit" name="submit"  class="new_admin_submit " value="'.__('Save Changes','wpestate').'" />
    </div>';
       
}
endif;



if( !function_exists('new_wpestate_footer_settings') ):
function new_wpestate_footer_settings(){
    //wide_footer
    //show_footer
    //show_footer_copy
    //footer_type
    
    
    $cache_array                =   array('yes','no');
    $show_footer_select  =   wpestate_dropdowns_theme_admin($cache_array,'show_footer');
    
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Show Footer ? ','wpestate').'</div>
    <div class="option_row_explain">'.__('Show Footer ?','wpestate').'</div>    
        <select id="show_footer" name="show_footer">
            '.$show_footer_select.'
        </select>
    </div>';
    
    $show_show_footer_copy_select  =   wpestate_dropdowns_theme_admin($cache_array,'show_footer_copy');
    
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Show Footer Copyright Area? ','wpestate').'</div>
    <div class="option_row_explain">'.__('Show Footer Copyright Area?.','wpestate').'</div>    
        <select id="show_footer_copy" name="show_footer_copy">
            '.$show_show_footer_copy_select.'
        </select>
    </div>';
    
    $copyright_message          =   esc_html (stripslashes( get_option('wp_estate_copyright_message','') ) );   
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Copyright Message','wpestate').'</div>
    <div class="option_row_explain">'.__('Type here the copyright message that will appear in footer.','wpestate').'</div>    
        <textarea cols="57" rows="2" id="copyright_message" name="copyright_message">'.$copyright_message.'</textarea></td>  
    </div>';
    
    $footer_background          =   get_option('wp_estate_footer_background','');
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Background for Footer','wpestate').'</div>
    <div class="option_row_explain">'.__('Insert background footer image below.','wpestate').'</div>    
        <input id="footer_background" type="text" size="36" name="footer_background" value="'.$footer_background.'" />
        <input id="footer_background_button" type="button"  class="upload_button button" value="'.__('Upload Background Image for Footer','wpestate').'" />
                 
    </div>';
    

    $repeat_array=array('repeat','repeat x','repeat y','no repeat');
    $repeat_footer_back_symbol  = wpestate_dropdowns_theme_admin($repeat_array,'repeat_footer_back');

    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Repeat Footer background ?','wpestate').'</div>
    <div class="option_row_explain">'.__('Set repeat options for background footer image.','wpestate').'</div>    
        <select id="repeat_footer_back" name="repeat_footer_back">
            '.$repeat_footer_back_symbol.'
        </select>     
    </div>';
    
    

    
    $cache_array                =   array('no','yes');
    $wide_footer_select  =   wpestate_dropdowns_theme_admin($cache_array,'wide_footer');
    
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Wide Footer ? ','wpestate').'</div>
    <div class="option_row_explain">'.__('make the footer 100%.','wpestate').'</div>    
        <select id="wide_footer" name="wide_footer">
            '.$wide_footer_select.'
        </select>
    </div>';
    
    
    
    
    $wide_array=array(
        "1"  =>     __("4 equal columns","wpestate"),
        "2"  =>     __("3 equal columns","wpestate"),
        "3"  =>     __("2 equal columns","wpestate"),
        "4"  =>     __("100% width column","wpestate"),
        "5"  =>     __("3 columns: 1/2 + 1/4 + 1/4","wpestate"),
        "6"  =>     __("3 columns: 1/4 + 1/2 + 1/4","wpestate"),
        "7"  =>     __("3 columns: 1/4 + 1/4 + 1/2","wpestate"),
        "8"  =>     __("2 columns: 2/3 + 1/3","wpestate"),
        "9"  =>     __("2 columns: 1/3 + 2/3","wpestate"),
        );
    
    
    
    $footer_type_symbol   = wpestate_dropdowns_theme_admin_with_key($wide_array,'footer_type');
    
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Footer Type','wpestate').'</div>
    <div class="option_row_explain">'.__('Footer Type','wpestate').'</div>    
        <select id="footer_type" name="footer_type">
            '.$footer_type_symbol.'
        </select>
    </div>';
    
    
    
    print ' <div class="estate_option_row_submit">
    <input type="submit" name="submit"  class="new_admin_submit " value="'.__('Save Changes','wpestate').'" />
    </div>';
       
}
endif;




if( !function_exists('new_wpestate_export_settings') ):
function  new_wpestate_export_settings(){
          
    print'<div class="estate_option_row">
        <div class="label_option_row">'.__('Export Theme Options','wpestate').'</div>
        <div class="option_row_explain">'.__('Export Theme Options ','wpestate').'</div>    
            <textarea  rows="15" style="width:100%;" id="export_theme_options" onclick="this.focus();this.select()" name="export_theme_options">'.wpestate_export_theme_options().'</textarea>
       
    </div>';
   
}
endif;


if( !function_exists('new_wpestate_import_options_tab') ):
function new_wpestate_import_options_tab(){
    
    if(isset($_POST['import_theme_options']) && $_POST['import_theme_options']!=''){
        
        $data =@unserialize(base64_decode( trim($_POST['import_theme_options']) ) );
        if ($data !== false && !empty($data) && is_array($data)) {
            foreach($data as $key=>$value){
                update_option($key, $value);          
            }
        
            print'<div class="estate_option_row">
            <div class="label_option_row">'.__('Import Completed','wpestate') .'</div>
            </div>';
            update_option('wp_estate_import_theme_options','') ;
   
        }else{
            print'<div class="estate_option_row">
            <div class="label_option_row">'.__('The inserted code is not a valid one','wpestate') .'</div>
            </div>';
            update_option('wp_estate_import_theme_options','') ;
        }

    }else{
        print'<div class="estate_option_row">
        <div class="label_option_row">'.__('Import Theme Options','wpestate').'</div>
        <div class="option_row_explain">'.__('Import Theme Options ','wpestate').'</div>    
            <textarea  rows="15" style="width:100%;" id="import_theme_options" name="import_theme_options"></textarea>
        </div>';
        print ' <div class="estate_option_row_submit">
        <input type="submit" name="submit"  class="new_admin_submit " value="'.__('Import','wpestate').'" />
        </div>';
    } 
               
}
endif;


if( !function_exists('new_wpestate_theme_contact_details') ):
function  new_wpestate_theme_contact_details (){
    
    $company_contact_image      =   esc_html( get_option('wp_estate_company_contact_image','') );
       
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Image for Contact Page','wpestate').'</div>
    <div class="option_row_explain">'.__('Add the image for the contact page contact area. Minim 350px wide for a nice design. ','wpestate').'</div>    
        <input id="company_contact_image" type="text" size="36" name="company_contact_image" value="'.$company_contact_image.'" />
        <input id="company_contact_image_button" type="button"  class="upload_button button" value="'.__('Upload Image','wpestate').'" />
    </div>';
    
    
    $company_name               =   esc_html ( stripslashes(get_option('wp_estate_company_name','') ) );
    
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Company Name','wpestate').'</div>
    <div class="option_row_explain">'.__('Company name for contact page','wpestate').'</div>    
        <input id="company_name" type="text" size="36" name="company_name" value="'.$company_name.'" />
    </div>';
             
    $email_adr                  =   esc_html ( get_option('wp_estate_email_adr','') );
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Email','wpestate').'</div>
    <div class="option_row_explain">'.__('company email','wpestate').'</div>    
      <input id="email_adr" type="text" size="36" name="email_adr" value="'.$email_adr.'" />
    </div>';
    
    
    $duplicate_email_adr        =   esc_html ( get_option('wp_estate_duplicate_email_adr','') );   
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Duplicate Email','wpestate').'</div>
    <div class="option_row_explain">'.__('Send all contact emails to','wpestate').'</div>    
      <input id="duplicate_email_adr" type="text" size="36" name="duplicate_email_adr" value="'.$duplicate_email_adr.'" />
    </div>';
    
    $telephone_no               =   esc_html ( get_option('wp_estate_telephone_no','') );
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Telephone','wpestate').'</div>
    <div class="option_row_explain">'.__('Company phone number.','wpestate').'</div>    
    <input id="telephone_no" type="text" size="36" name="telephone_no" value="'.$telephone_no.'" />
    </div>';
     
    $mobile_no                  =   esc_html ( get_option('wp_estate_mobile_no','') );
     print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Mobile','wpestate').'</div>
    <div class="option_row_explain">'.__('company mobile','wpestate').'</div>    
    <input id="mobile_no" type="text" size="36" name="mobile_no" value="'.$mobile_no.'" />
    </div>';
     
    $fax_ac                     =   esc_html ( get_option('wp_estate_fax_ac','') );
     print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Fax','wpestate').'</div>
    <div class="option_row_explain">'.__('company fax','wpestate').'</div>    
    <input id="fax_ac" type="text" size="36" name="fax_ac" value="'.$fax_ac.'" />
    </div>';
     
    $skype_ac                   =   esc_html ( get_option('wp_estate_skype_ac','') );
     print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Skype','wpestate').'</div>
    <div class="option_row_explain">'.__('Company skype','wpestate').'</div>    
    <input id="skype_ac" type="text" size="36" name="skype_ac" value="'.$skype_ac.'" />
    </div>';
    
    $co_address                 =   esc_html ( stripslashes( get_option('wp_estate_co_address','') ) );
    
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Company Address','wpestate').'</div>
    <div class="option_row_explain">'.__('Type company address','wpestate').'</div>    
        <textarea cols="57" rows="2" name="co_address" id="co_address">'.$co_address.'</textarea>
    </div>';
    
    print ' <div class="estate_option_row_submit">
    <input type="submit" name="submit"  class="new_admin_submit " value="'.__('Save Changes','wpestate').'" />
    </div>';
    
}
endif;


if( !function_exists('new_wpestate_theme_social_accounts') ):
function new_wpestate_theme_social_accounts(){
    
    $facebook_link              =   esc_html ( get_option('wp_estate_facebook_link','') );
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Facebook Link','wpestate').'</div>
    <div class="option_row_explain">'.__('Facebook page url, with https://','wpestate').'</div>    
        <input id="facebook_link" type="text" size="36" name="facebook_link" value="'.$facebook_link.'" />
    </div>';
    
      
    $twitter_link               =   esc_html ( get_option('wp_estate_twitter_link','') );
      print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Twitter page link','wpestate').'</div>
    <div class="option_row_explain">'.__('Twitter page link, with https://','wpestate').'</div>    
       <input id="twitter_link" type="text" size="36" name="twitter_link" value="'.$twitter_link.'" />
    </div>';
      
      
    $google_link                =   esc_html ( get_option('wp_estate_google_link','') );
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Google+ Link','wpestate').'</div>
    <div class="option_row_explain">'.__('Google+ page link, with https://','wpestate').'</div>    
       <input id="google_link" type="text" size="36" name="google_link" value="'.$google_link.'" />
    </div>';
      
    $linkedin_link              =   esc_html ( get_option('wp_estate_linkedin_link','') );
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Linkedin Link','wpestate').'</div>
    <div class="option_row_explain">'.__(' Linkedin page link, with https://','wpestate').'</div>    
        <input id="linkedin_link" type="text" size="36" name="linkedin_link" value="'.$linkedin_link.'" />
    </div>';
      
    $pinterest_link             =   esc_html ( get_option('wp_estate_pinterest_link','') );  
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Pinterest Link','wpestate').'</div>
    <div class="option_row_explain">'.__('Pinterest page link, with https://','wpestate').'</div>    
        <input id="pinterest_link" type="text" size="36" name="pinterest_link" value="'.$pinterest_link.'" />
    </div>';
      
    $instagram_link             =   esc_html ( get_option('wp_estate_instagram_link','') );  
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Instagram Link','wpestate').'</div>
    <div class="option_row_explain">'.__('Instagram page link, with https://','wpestate').'</div>    
        <input id="instagram_link" type="text" size="36" name="instagram_link" value="'.$instagram_link.'" />
    </div>';  
      
    $twitter_consumer_key       =   esc_html ( get_option('wp_estate_twitter_consumer_key','') );
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Twitter consumer_key','wpestate').'</div>
    <div class="option_row_explain">'.__('Twitter consumer_key is required for theme Twitter widget.','wpestate').'</div>    
        <input id="twitter_consumer_key" type="text" size="36" name="twitter_consumer_key" value="'.$twitter_consumer_key.'" />
    </div>';
      
    $twitter_consumer_secret    =   esc_html ( get_option('wp_estate_twitter_consumer_secret','') );
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Twitter Consumer Secret','wpestate').'</div>
    <div class="option_row_explain">'.__('Twitter Consumer Secret is required for theme Twitter widget.','wpestate').'</div>    
        <input id="twitter_consumer_secret" type="text" size="36" name="twitter_consumer_secret" value="'.$twitter_consumer_secret.'" />
    </div>';
      
    $twitter_access_token       =   esc_html ( get_option('wp_estate_twitter_access_token','') );
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Twitter Access Token','wpestate').'</div>
    <div class="option_row_explain">'.__('Twitter Access Token is required for theme Twitter widget.','wpestate').'</div>    
        <input id="twitter_account" type="text" size="36" name="twitter_access_token" value="'.$twitter_access_token.'" />
    </div>';
      
    $twitter_access_secret      =   esc_html ( get_option('wp_estate_twitter_access_secret','') );
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Twitter Access Secret','wpestate').'</div>
    <div class="option_row_explain">'.__('Twitter Access Secret is required for theme Twitter widget.','wpestate').'</div>    
        <input id="twitter_access_secret" type="text" size="36" name="twitter_access_secret" value="'.$twitter_access_secret.'" />
    </div>';
      
    
    $twitter_cache_time         =   intval   ( get_option('wp_estate_twitter_cache_time','') );
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Twitter Cache Time','wpestate').'</div>
    <div class="option_row_explain">'.__('Twitter Cache Time','wpestate').'</div>    
       <input id="twitter_cache_time" type="text" size="36" name="twitter_cache_time" value="'.$twitter_cache_time.'" />
    </div>';
      
      
  
      
      
    
    $facebook_api               =   esc_html ( get_option('wp_estate_facebook_api','') );
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Facebook Api key','wpestate').'</div>
    <div class="option_row_explain">'.__('Facebook Api key is required for Facebook login.','wpestate').'</div>    
        <input id="facebook_api" type="text" size="36" name="facebook_api" value="'.$facebook_api.'" />
    </div>';
      
    
    $facebook_secret            =   esc_html ( get_option('wp_estate_facebook_secret','') );
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Facebook Secret','wpestate').'</div>
    <div class="option_row_explain">'.__('Facebook Secret is required for Facebook login.','wpestate').'</div>    
        <input id="facebook_secret" type="text" size="36" name="facebook_secret" value="'.$facebook_secret.'" />
    </div>';
      
    
    $google_oauth_api           =   esc_html ( get_option('wp_estate_google_oauth_api','') );
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Google Oauth Api','wpestate').'</div>
    <div class="option_row_explain">'.__('Google Oauth Api is required for Google Login','wpestate').'</div>    
        <input id="google_oauth_api" type="text" size="36" name="google_oauth_api" value="'.$google_oauth_api.'" />
    </div>';
      
    $google_oauth_client_secret =   esc_html ( get_option('wp_estate_google_oauth_client_secret','') );
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Google Oauth Client Secret','wpestate').'</div>
    <div class="option_row_explain">'.__('Google Oauth Client Secret is required for Google Login.','wpestate').'</div>    
        <input id="google_oauth_client_secret" type="text" size="36" name="google_oauth_client_secret" value="'.$google_oauth_client_secret.'" />
    </div>';
      
    $google_api_key             =   esc_html ( get_option('wp_estate_google_api_key','') );
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Google api key','wpestate').'</div>
    <div class="option_row_explain">'.__('Google api key is required for Google Login.','wpestate').'</div>    
        <input id="google_api_key" type="text" size="36" name="google_api_key" value="'.$google_api_key.'" />
    </div>';
      
    
    $social_array               =   array('no','yes');
   
    $facebook_login_select      = wpestate_dropdowns_theme_admin($social_array,'facebook_login');
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Allow login via Facebook ? ','wpestate').'</div>
    <div class="option_row_explain">'.__('Enable or disable Facebook login. ','wpestate').'</div>    
        <select id="facebook_login" name="facebook_login">
            '.$facebook_login_select.'
        </select>
    </div>';
      
      
    $google_login_select        = wpestate_dropdowns_theme_admin($social_array,'google_login');
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Allow login via Google ?','wpestate').'</div>
    <div class="option_row_explain">'.__('Enable or disable Google login.','wpestate').'</div>    
        <select id="google_login" name="google_login">
            '.$google_login_select.'
        </select>
    </div>';
      
    $yahoo_login_select         = wpestate_dropdowns_theme_admin($social_array,'yahoo_login');
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Allow login via Yahoo ?','wpestate').'</div>
    <div class="option_row_explain">'.__('Enable or disable Yahoo login.','wpestate').'</div>    
        <select id="yahoo_login" name="yahoo_login">
            '.$yahoo_login_select.'
        </select>
    </div>';
    
          
    $zillow_api_key             =   esc_html ( get_option('wp_estate_zillow_api_key','') );  
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Zillow api key','wpestate').'</div>
    <div class="option_row_explain">'.__('Zillow api key is required for Zillow Widget.','wpestate').'</div>    
        <input id="zillow_api_key" type="text" size="36" name="zillow_api_key" value="'.$zillow_api_key.'" />
    </div>';
      
    
    
    print ' <div class="estate_option_row_submit">
    <input type="submit" name="submit"  class="new_admin_submit " value="'.__('Save Changes','wpestate').'" />
    </div>';
}
endif;


if( !function_exists('new_wpestate_contact7') ):
function new_wpestate_contact7(){
    
    $contact_form_7_contact     = stripslashes( esc_html( get_option('wp_estate_contact_form_7_contact','') ) );
    $contact_form_7_agent       = stripslashes( esc_html( get_option('wp_estate_contact_form_7_agent','') ) );
    
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Contact form 7 code for agent','wpestate').'</div>
    <div class="option_row_explain">'.__('Contact form 7 code for agent (ex: [contact-form-7 id="2725" title="contact me"])','wpestate').'</div>    
         <input type="text" size="36" id="contact_form_7_agent" name="contact_form_7_agent" value="'.$contact_form_7_agent.'" />
    </div>';
    
      
  
    
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Contact form 7 code for contact page','wpestate').'</div>
    <div class="option_row_explain">'.__('Contact form 7 code for contact page template (ex: [contact-form-7 id="2725" title="contact me"])','wpestate').'</div>    
         <input type="text" size="36" id="contact_form_7_contact" name="contact_form_7_contact" value="'.$contact_form_7_contact.'" />
    </div>';
    
    print ' <div class="estate_option_row_submit">
    <input type="submit" name="submit"  class="new_admin_submit " value="'.__('Save Changes','wpestate').'" />
    </div>';
}
endif;

if( !function_exists('new_wpestate_appeareance') ):
function new_wpestate_appeareance(){
    $cache_array                =   array('yes','no');
  
    $wide_array=array(
            "1"  =>  __("wide","wpestate"),
            "2"  =>  __("boxed","wpestate")
         );
    $wide_status_symbol   = wpestate_dropdowns_theme_admin_with_key($wide_array,'wide_status');
    
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Wide or Boxed?','wpestate').'</div>
    <div class="option_row_explain">'.__('Choose the theme layout: wide or boxed.','wpestate').'</div>    
        <select id="wide_status" name="wide_status">
            '.$wide_status_symbol.'
        </select>
    </div>';


        
  


    
    $prop_no                    =   intval   ( get_option('wp_estate_prop_no','') );
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Properties List - Properties number per page','wpestate').'</div>
    <div class="option_row_explain">'.__('Set how many properties to show per page in lists.','wpestate').'</div>    
        <input type="text" id="prop_no" name="prop_no" value="'.$prop_no.'"> 
    </div>';
    
    $prop_image_number                   =   intval   ( get_option('wp_estate_prop_image_number','') );
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Maximum no of images per property (only front-end upload)','wpestate').'</div>
    <div class="option_row_explain">'.__('The maximum no of images an user can upload on front end. Use 0 for unlimited','wpestate').'</div>    
        <input type="text" id="prop_no" name="prop_image_number" value="'.$prop_image_number.'"> 
    </div>';
    
    $prop_list_slider = array( 
                "0"  =>  __("no ","wpestate"),
                "1"  =>  __("yes","wpestate")
                );
    $prop_unit_slider_symbol = wpestate_dropdowns_theme_admin_with_key($prop_list_slider,'prop_list_slider');
    
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Use Slider in Property Unit','wpestate').'</div>
    <div class="option_row_explain">'.__('Enable / Disable the image slider in property unit (used in lists).','wpestate').'</div>    
        <select id="prop_list_slider" name="prop_list_slider">
            '.$prop_unit_slider_symbol.'
        </select>  
    </div>';
    
    
    $show_empty_city_status_symbol      = wpestate_dropdowns_theme_admin($cache_array,'show_empty_city');  
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Show Cities and Areas with 0 properties in advanced search','wpestate').'</div>
    <div class="option_row_explain">'.__('Enable or disable listing empty city or area categories in dropdowns.','wpestate').'</div>    
        <select id="show_empty_city" name="show_empty_city">
            '.$show_empty_city_status_symbol.'
        </select>
    </div>';
    
    
    
    $blog_sidebar_array=array('no sidebar','right','left');
    $agent_sidebar_pos_select     = wpestate_dropdowns_theme_admin($blog_sidebar_array,'agent_sidebar');

    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Agent Sidebar Position','wpestate').'</div>
    <div class="option_row_explain">'.__('Where to show the sidebar in agent page.','wpestate').'</div>    
       <select id="agent_sidebar" name="agent_sidebar">
            '.$agent_sidebar_pos_select.'
        </select>
    </div>';
    
    
    $agent_sidebar_name          =   esc_html ( get_option('wp_estate_agent_sidebar_name','') );
    $agent_sidebar_name_select='';
    foreach ( $GLOBALS['wp_registered_sidebars'] as $sidebar ) { 
        $agent_sidebar_name_select.='<option value="'.($sidebar['id'] ).'"';
            if($agent_sidebar_name==$sidebar['id']){ 
                $agent_sidebar_name_select.=' selected="selected"';
            }
        $agent_sidebar_name_select.=' >'.ucwords($sidebar['name']).'</option>';
    } 
    
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Agent page Sidebar','wpestate').'</div>
    <div class="option_row_explain">'.__('What sidebar to show in agent page.','wpestate').'</div>    
       <select id="agent_sidebar_name" name="agent_sidebar_name">
            '.$agent_sidebar_name_select.'
        </select>
    </div>';
    
    
    $blog_sidebar_select ='';
    $blog_sidebar= esc_html ( get_option('wp_estate_blog_sidebar','') );
    $blog_sidebar_array=array('no sidebar','right','left');

    foreach($blog_sidebar_array as $value){
            $blog_sidebar_select.='<option value="'.$value.'"';
            if ($blog_sidebar==$value){
                    $blog_sidebar_select.='selected="selected"';
            }
            $blog_sidebar_select.='>'.$value.'</option>';
    }
    
    
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Blog Category/Archive Sidebar Position','wpestate').'</div>
    <div class="option_row_explain">'.__('Where to show the sidebar for blog category/archive list.','wpestate').'</div>    
        <select id="blog_sidebar" name="blog_sidebar">
            '.$blog_sidebar_select.'
        </select>
    </div>';
    
    
    
    $blog_sidebar_name          =   esc_html ( get_option('wp_estate_blog_sidebar_name','') );
   
    $blog_sidebar_name_select='';
    foreach ( $GLOBALS['wp_registered_sidebars'] as $sidebar ) { 
        $blog_sidebar_name_select.='<option value="'.($sidebar['id'] ).'"';
            if($blog_sidebar_name==$sidebar['id']){ 
               $blog_sidebar_name_select.=' selected="selected"';
            }
        $blog_sidebar_name_select.=' >'.ucwords($sidebar['name']).'</option>';
    } 
    
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Blog Category/Archive Sidebar','wpestate').'</div>
    <div class="option_row_explain">'.__('What sidebar to show for blog category/archive list.','wpestate').'</div>    
        <select id="blog_sidebar_name" name="blog_sidebar_name">
            '.$blog_sidebar_name_select.'
        </select>
    </div>';
    
    
    $blog_unit_array    =   array(
                        'grid'    =>__('grid','wpestate'),
                        'list'      => __('list','wpestate')
                        );
    
    $blog_unit_select = wpestate_dropdowns_theme_admin_with_key($blog_unit_array,'blog_unit');
      
    
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Blog Category/Archive List type','wpestate').'</div>
    <div class="option_row_explain">'.__('Select list or grid style for Blog Category/Archive list type.','wpestate').'</div>    
        <select id="blog_unit" name="blog_unit">
            '.$blog_unit_select.'
        </select>
    </div>';
    
    
    
      
    $prop_list_array=array(
               "1"  =>  __("standard ","wpestate"),
               "2"  =>  __("half map","wpestate")
            );
    $property_list_type_symbol   = wpestate_dropdowns_theme_admin_with_key($prop_list_array,'property_list_type');
   
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Property List Type for Taxonomy pages','wpestate').'</div>
    <div class="option_row_explain">'.__('Select standard or half map style for property taxonomies pages.','wpestate').'</div>    
        <select id="property_list_type" name="property_list_type">
            '.$property_list_type_symbol.'
        </select>
    </div>';
    
    
    
   
    $property_list_type_symbol_adv   = wpestate_dropdowns_theme_admin_with_key($prop_list_array,'property_list_type_adv');
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Property List Type for Advanced Search','wpestate').'</div>
    <div class="option_row_explain">'.__('Select standard or half map style for advanced search results page.','wpestate').'</div>    
        <select id="property_list_type_adv" name="property_list_type_adv">
            '.$property_list_type_symbol_adv.'
        </select>
    </div>';
    
    
    
    
    $prop_unit_array    =   array(
                                'grid'    =>__('grid','wpestate'),
                                'list'      => __('list','wpestate')
                            );
    $prop_unit_select_view   = wpestate_dropdowns_theme_admin_with_key($prop_unit_array,'prop_unit');
    

    
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Property List display(*global option)','wpestate').'</div>
    <div class="option_row_explain">'.__('Select grid or list style for properties list pages.','wpestate').'</div>    
        <select id="prop_unit" name="prop_unit">
            '.$prop_unit_select_view.'
        </select>
    </div>';
    
      
    
    
    print ' <div class="estate_option_row_submit">
    <input type="submit" name="submit"  class="new_admin_submit " value="'.__('Save Changes','wpestate').'" />
    </div>';
}
endif;


if( !function_exists('new_wpestate_property_page_details') ):
function new_wpestate_property_page_details(){
    $sidebar_agent                  =   array('yes','no');
    $slider_type                    =   array('vertical','horizontal','full width header');
    $social_array                   =   array('no','yes');
    $content_type                   =   array('accordion','tabs');
   
    
    $pages = get_pages(array(
            'meta_key' => '_wp_page_template',
            'meta_value' => 'page_property_design.php'
            ));

    $global_property_page_template_options='<option value="">'.__('default','wpestate').'</option>';
    $wp_estate_global_page_template               =   esc_html( get_option('wp_estate_global_property_page_template') );
    foreach($pages as $page){
        $global_property_page_template_options.='<option value="'.$page->ID.'"'; 
        if($wp_estate_global_page_template==$page->ID){
            $global_property_page_template_options.=' selected="selected" ';
        }
        $global_property_page_template_options.=' >'.$page->post_title.'</option>';       
    }
    
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Use a custom property page template','wpestate').'</div>
    <div class="option_row_explain">'.__('Pick a custom property page template you made. ','wpestate').'</div>    
        <select id="global_property_page_template" name="global_property_page_template">
            '.$global_property_page_template_options.'
        </select> 
    </div>';
    
    
    $blog_sidebar_array=array('no sidebar','right','left');
    $property_sidebar_pos_select     = wpestate_dropdowns_theme_admin($blog_sidebar_array,'property_sidebar');

    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Property Sidebar Position','wpestate').'</div>
    <div class="option_row_explain">'.__('Where to show the sidebar in property page.','wpestate').'</div>    
       <select id="property_sidebar" name="property_sidebar">
            '.$property_sidebar_pos_select.'
        </select>
    </div>';
    
    
    $property_sidebar_name          =   esc_html ( get_option('wp_estate_property_sidebar_name','') );
    $property_sidebar_name_select='';
    foreach ( $GLOBALS['wp_registered_sidebars'] as $sidebar ) { 
        $property_sidebar_name_select.='<option value="'.($sidebar['id'] ).'"';
            if($property_sidebar_name==$sidebar['id']){ 
                $property_sidebar_name_select.=' selected="selected"';
            }
        $property_sidebar_name_select.=' >'.ucwords($sidebar['name']).'</option>';
    } 
    
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Property page Sidebar','wpestate').'</div>
    <div class="option_row_explain">'.__('What sidebar to show in property page.','wpestate').'</div>    
       <select id="property_sidebar_name" name="property_sidebar_name">
            '.$property_sidebar_name_select.'
        </select>
    </div>';
    
    
    
    
    $enable_global_property_page_agent_sidebar_symbol           =   wpestate_dropdowns_theme_admin($sidebar_agent,'global_property_page_agent_sidebar');
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Add Agent on Sidebar','wpestate').'</div>
    <div class="option_row_explain">'.__('Show agent and contact form on sidebar. ','wpestate').'</div>    
        <select id="global_property_page_agent_sidebar" name="global_property_page_agent_sidebar">
            '.$enable_global_property_page_agent_sidebar_symbol.'
        </select> 
    </div>';
    
    $global_prpg_slider_type_symbol                             =   wpestate_dropdowns_theme_admin($slider_type,'global_prpg_slider_type');
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Slider Type','wpestate').'</div>
    <div class="option_row_explain">'.__('What property slider type to show on property page.','wpestate').'</div>    
        <select id="global_prpg_slider_type" name="global_prpg_slider_type">
            '.$global_prpg_slider_type_symbol.'
        </select> 
    </div>';
    
    $global_prpg_content_type_symbol                            =   wpestate_dropdowns_theme_admin($content_type,'global_prpg_content_type');
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Show Content as ','wpestate').'</div>
    <div class="option_row_explain">'.__('Select tabs or accordion style for property info.','wpestate').'</div>    
        <select id="global_prpg_content_type" name="global_prpg_content_type">
            '.$global_prpg_content_type_symbol.'
        </select> 
    </div>';
    
    $walkscore_api                                              =   esc_html ( get_option('wp_estate_walkscore_api','') );
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Walkscore APi Key','wpestate').'</div>
    <div class="option_row_explain">'.__('Walkscore info doesn\'t show if you don\'t add the API.','wpestate').'</div>    
        <input type="text" name="walkscore_api" id="walkscore_api" value="'.$walkscore_api.'"> 
    </div>';
    
    
    $show_graph_prop_page                                       =   wpestate_dropdowns_theme_admin($social_array,'show_graph_prop_page');
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Show Graph on Property Page','wpestate').'</div>
    <div class="option_row_explain">'.__('Enable or disable the display of number of view by day graphic.','wpestate').'</div>    
        <select id="show_graph_prop_page" name="show_graph_prop_page">
            '.$show_graph_prop_page.'
        </select> 
    </div>';
    
    $show_lightbox_contact                                       =   wpestate_dropdowns_theme_admin($social_array,'show_lightbox_contact');
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Show Contact Form on lightbox','wpestate').'</div>
    <div class="option_row_explain">'.__('Enable or disable the contact form on lightbox.','wpestate').'</div>    
        <select id="show_lightbox_contact" name="show_lightbox_contact">
            '.$show_lightbox_contact.'
        </select> 
    </div>';
    
    $crop_images_lightbox                                       =   wpestate_dropdowns_theme_admin($social_array,'crop_images_lightbox');
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Crop Images on lightbox','wpestate').'</div>
    <div class="option_row_explain">'.__('Images will have the same size. If set to no you will need to make sure that images are about the same size','wpestate').'</div>    
        <select id="crop_images_lightbox" name="crop_images_lightbox">
            '.$crop_images_lightbox.'
        </select> 
    </div>';
   
    print ' <div class="estate_option_row_submit">
    <input type="submit" name="submit"  class="new_admin_submit " value="'.__('Save Changes','wpestate').'" />
    </div>';  
}
endif;


if( !function_exists('wpestate_general_design_settings') ):
function wpestate_general_design_settings(){
    
    $main_grid_content_width                                              =   esc_html ( get_option('wp_estate_main_grid_content_width','') );
    
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Main Grid Width in px','wpestate').'</div>
    <div class="option_row_explain">'.__('This option defines the main content width. Default value is 1200px','wpestate').'</div>    
        <input type="text" name="main_grid_content_width" id="main_grid_content_width" value="'.$main_grid_content_width.'"> 
    </div>';
    
    $main_content_width                                              =   esc_html ( get_option('wp_estate_main_content_width','') );
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Content Width (In Percent)','wpestate').'</div>
    <div class="option_row_explain">'.__('Using this option you can define the width of the content in percent.Sidebar will occupy the rest of the main content space.','wpestate').'</div>    
        <input type="text" name="main_content_width" id="main_content_width" value="'.$main_content_width.'"> 
    </div>';
    
    $header_height                                              =   esc_html ( get_option('wp_estate_header_height','') );
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Header Height','wpestate').'</div>
    <div class="option_row_explain">'.__('Header Height in px','wpestate').'</div>    
        <input type="text" name="header_height" id="header_height" value="'.$header_height.'"> 
    </div>';
    
    $sticky_header_height                                              =   esc_html ( get_option('wp_estate_sticky_header_height','') );
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Sticky Header Height','wpestate').'</div>
    <div class="option_row_explain">'.__('Sticky Header Height in px','wpestate').'</div>    
        <input type="text" name="sticky_header_height" id="sticky_header_height" value="'.$sticky_header_height.'"> 
    </div>';
      
    
    
    $border_bottom_header                                              =   esc_html ( get_option('wp_estate_border_bottom_header','') );
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Border Bottom Header Height','wpestate').'</div>
    <div class="option_row_explain">'.__('Border Bottom Header Height in px','wpestate').'</div>    
        <input type="text" name="border_bottom_header" id="border_bottom_header" value="'.$border_bottom_header.'"> 
    </div>';
    
    $sticky_border_bottom_header                                            =   esc_html ( get_option('wp_estate_sticky_border_bottom_header','') );
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Border Bottom Sticky Header Height','wpestate').'</div>
    <div class="option_row_explain">'.__('Border Bottom Sticky Header Height px','wpestate').'</div>    
        <input type="text" name="sticky_border_bottom_header" id="sticky_border_bottom_header" value="'.$sticky_border_bottom_header.'"> 
    </div>';
    
        
    $border_bottom_header_color             =  esc_html ( get_option('wp_estate_border_bottom_header_color','') );
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Header Border Bottom Color','wpestate').'</div>
    <div class="option_row_explain">'.__('Header Border Bottom Color','wpestate').'</div>    
        <input type="text" name="border_bottom_header_color" value="'.$border_bottom_header_color.'" maxlength="7" class="inptxt" />
        <div id="border_bottom_header_color" class="colorpickerHolder"><div class="sqcolor" style="background-color:#'.$border_bottom_header_color.';"></div></div>
    </div>';
     
        
    $border_bottom_header_sticky_color             =  esc_html ( get_option('wp_estate_border_bottom_header_sticky_color','') );
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Sticky Header Border Bottom Color','wpestate').'</div>
    <div class="option_row_explain">'.__('Sticky Header Border Bottom Color','wpestate').'</div>    
        <input type="text" name="border_bottom_header_sticky_color" value="'.$border_bottom_header_sticky_color.'" maxlength="7" class="inptxt" />
        <div id="border_bottom_header_sticky_color" class="colorpickerHolder"><div class="sqcolor" style="background-color:#'.$border_bottom_header_sticky_color.';"></div></div>
    </div>';
     
    $wp_estate_contentarea_internal_padding_top          = get_option('wp_estate_contentarea_internal_padding_top','');
    $wp_estate_contentarea_internal_padding_left         = get_option('wp_estate_contentarea_internal_padding_left','');
    $wp_estate_contentarea_internal_padding_bottom       = get_option('wp_estate_contentarea_internal_padding_bottom','');
    $wp_estate_contentarea_internal_padding_right        = get_option('wp_estate_contentarea_internal_padding_right','');
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Content Area Internal Padding','wpestate').'</div>
    <div class="option_row_explain">'.__('Content Area Internal Padding (top,left,bottom,right) ','wpestate').'</div>    
        <input  style="width:100px;min-width:100px;" type="text" id="wp_estate_contentarea_internal_padding_top" name="contentarea_internal_padding_top"  value="'.$wp_estate_contentarea_internal_padding_top.'"/> 
        <input  style="width:100px;min-width:100px;" type="text" id="wp_estate_contentarea_internal_padding_left" name="contentarea_internal_padding_left"  value="'.$wp_estate_contentarea_internal_padding_left.'"/> 
        <input  style="width:100px;min-width:100px;" type="text" id="wp_estate_contentarea_internal_padding_bottom" name="contentarea_internal_padding_bottom"  value="'.$wp_estate_contentarea_internal_padding_bottom.'"/> 
        <input  style="width:100px;min-width:100px;" type="text" id="wp_estate_contentarea_internal_padding_right" name="contentarea_internal_padding_right"  value="'.$wp_estate_contentarea_internal_padding_right.'"/> 
    </div>';
    
    
    $wp_estate_content_area_back_color             =  esc_html ( get_option('wp_estate_content_area_back_color','') );
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Content Area Background Color','wpestate').'</div>
    <div class="option_row_explain">'.__('Content Area Background Color','wpestate').'</div>    
        <input type="text" name="content_area_back_color" value="'.$wp_estate_content_area_back_color.'" maxlength="7" class="inptxt" />
        <div id="content_area_back_color" class="colorpickerHolder"><div class="sqcolor" style="background-color:#'.$wp_estate_content_area_back_color.';"></div></div>
    </div>';
    
    $yesno=array('yes','no');
    $enable_show_breadcrumbs           =   wpestate_dropdowns_theme_admin($yesno,'show_breadcrumbs');
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Show Breadcrumbs','wpestate').'</div>
    <div class="option_row_explain">'.__('Show Breadcrumbs?','wpestate').'</div>    
        <select id="show_breadcrumbs" name="show_breadcrumbs">
            '.$enable_show_breadcrumbs.'
        </select> 
    </div>';
    
    
    print ' <div class="estate_option_row_submit">
    <input type="submit" name="submit"  class="new_admin_submit " value="'.__('Save Changes','wpestate').'" />
    </div>'; 
    
}
endif;



if( !function_exists('new_wpestate_price_currency') ):
function new_wpestate_price_currency(){
    
     
    $custom_fields = get_option( 'wp_estate_multi_curr', true);     
    $current_fields='';
    
    $currency_symbol                =   esc_html( get_option('wp_estate_currency_symbol') );
    
    $where_currency_symbol_array    =   array('before','after');
    $where_currency_symbol          =   wpestate_dropdowns_theme_admin($where_currency_symbol_array,'where_currency_symbol');
   
    $enable_auto_symbol_array       =   array('yes','no');
    $enable_auto_symbol             =   wpestate_dropdowns_theme_admin($enable_auto_symbol_array,'auto_curency');
    
    
    $i=0;
    if( !empty($custom_fields)){    
        while($i< count($custom_fields) ){
            $current_fields.='
                <div class=field_row>
                <div    class="field_item"><strong>'.__('Currency Symbol','wpestate').'</strong></br><input   type="text" name="add_curr_name[]"   value="'.$custom_fields[$i][0].'"  ></div>
                <div    class="field_item"><strong>'.__('Currency Label','wpestate').'</strong></br><input  type="text" name="add_curr_label[]"   value="'.$custom_fields[$i][1].'"  ></div>
                <div    class="field_item"><strong>'.__('Currency Value','wpestate').'</strong></br><input  type="text" name="add_curr_value[]"   value="'.$custom_fields[$i][2].'"  ></div>
                <div    class="field_item"><strong>'.__('Currency Position','wpestate').'</strong></br><input  type="text" name="add_curr_order[]"   value="'.$custom_fields[$i][3].'"  ></div>
                
                <a class="deletefieldlink" href="#">'.__('delete','wpestate').'</a>
            </div>';    
            $i++;
        }
    }
    
    
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Price - thousands separator','wpestate').'</div>
    <div class="option_row_explain">'.__('Set the thousand separator for price numbers.','wpestate').'</div>    
        <input type="text" name="prices_th_separator" id="prices_th_separator" value="'.  stripslashes ( get_option('wp_estate_prices_th_separator','') ).'"> 
    </div>';
 
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Currency symbol','wpestate').'</div>
    <div class="option_row_explain">'.__('Set currency symbol for property price.','wpestate').'</div>    
        <input  type="text" id="currency_symbol" name="currency_symbol"  value="'.$currency_symbol.'"/>
    </div>';
    
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Currency label - will appear on front end','wpestate').'</div>
    <div class="option_row_explain">'.__('Set the currency label for multi-currency widget dropdown.','wpestate').'</div>    
        <input  type="text" id="currency_label_main"  name="currency_label_main"   value="'. get_option('wp_estate_currency_label_main','').'" size="40"/>
    </div>';
    
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Where to show the currency symbol?','wpestate').'</div>
    <div class="option_row_explain">'.__('Set the position for the currency symbol.','wpestate').'</div>    
        <select id="where_currency_symbol" name="where_currency_symbol">
            '.$where_currency_symbol.'
        </select> 
    </div>';

    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Enable auto loading of exchange rates from Yahoo(1 time per day)?','wpestate').'</div>
    <div class="option_row_explain">'.__('Symbol must be set according to international standards. Complete list is here http://www.xe.com/iso4217.php.','wpestate').'</div>    
        <select id="auto_curency" name="auto_curency">
            '.$enable_auto_symbol.'
        </select> 
    </div>';

 
    print'<div class="estate_option_row"> 
        <h3 style="margin-left:10px;width:100%;float:left;">'.__('Add Currencies for Multi Currency Widget.','wpestate').'</h3>
     
        <div id="custom_fields">
             '.$current_fields.'
            <input type="hidden" name="is_custom_cur" value="1">   
        </div>

     
        <div class="add_curency">
            <div class="cur_explanations">'.__('Currency','wpestate').'</div>
            <input  type="text" id="currency_name"  name="currency_name"   value=""/>
        
            <div class="cur_explanations">'.__('Currency label - will appear on front end','wpestate').'</div>
            <input  type="text" id="currency_label"  name="currency_label"   value="" />   

            <div class="cur_explanations">'.__('Currency value compared with the base currency value.','wpestate').'</div>
            <input  type="text" id="currency_value"  name="currency_value"   value="" />
           
            <div class="cur_explanations">'.__('Show currency before or after price - in front pages','wpestate').'</div>
                <select id="where_cur" name="where_cur"  >
                    <option value="before"> before </option>
                    <option value="after">  after </option>
                </select>
        </div>
                     
         <a href="#" id="add_curency">'.__(' click to add currency','wpestate').'</a><br>
     </div> ';
    print ' <div class="estate_option_row_submit">
    <input type="submit" name="submit"  class="new_admin_submit " value="'.__('Save Changes','wpestate').'" />
    </div>';
}
endif;

if( !function_exists('new_wpestate_map_details') ):
function new_wpestate_map_details(){
    $cache_array                    =   array('yes','no');
    $cache_array2                   =   array('no','yes');
    $show_filter_map_symbol         =   wpestate_dropdowns_theme_admin($cache_array,'show_filter_map');
    $home_small_map_symbol          =   wpestate_dropdowns_theme_admin($cache_array,'home_small_map');
    $show_adv_search_symbol_map_close   =   wpestate_dropdowns_theme_admin($cache_array,'show_adv_search_map_close');
    
    
    $path=estate_get_pin_file_path(); 
   
    if ( file_exists ($path) && is_writable ($path) ){
    }else{
        print ' <div class="notice_file">'.__('the file Google map does NOT exist or is NOT writable','wpestate').'</div>';
    }
    
    $readsys_symbol                 =   wpestate_dropdowns_theme_admin($cache_array,'readsys');
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Use file reading for pins? ','wpestate').'</div>
    <div class="option_row_explain">'.__('Use file reading for pins? (*recommended for over 200 listings. Read the manual for diffrences between file and mysql reading)','wpestate').'</div>    
        <select id="readsys" name="readsys">
            '.$readsys_symbol.'
        </select>
    </div>';
       
    
     
    $map_max_pins                 =   intval( get_option('wp_estate_map_max_pins') );
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Maximum number of pins to show on the map. ','wpestate').'</div>
    <div class="option_row_explain">'.__('A high number will increase the response time and server load. Use a number that works for your current hosting situation. Put -1 for all pins.','wpestate').'</div>    
        <input  type="text" id="map_max_pins" name="map_max_pins" class="regular-text" value="'.$map_max_pins.'"/>
  
    </div>';
    
    $ssl_map_symbol                 =   wpestate_dropdowns_theme_admin($cache_array,'ssl_map');
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Use Google maps with SSL ?','wpestate').'</div>
    <div class="option_row_explain">'.__('Set to Yes if you use SSL.','wpestate').'</div>    
        <select id="ssl_map" name="ssl_map">
            '.$ssl_map_symbol.'
        </select>
    </div>';  

    $api_key                        =   esc_html( get_option('wp_estate_api_key') );
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Google Maps API KEY','wpestate').'</div>
    <div class="option_row_explain">'.__('The Google Maps JavaScript API v3 REQUIRES an API key to function correctly. Get an APIs Console key and post the code in Theme Options. You can get it from <a href="https://developers.google.com/maps/documentation/javascript/tutorial#api_key" target="_blank">here</a>','wpestate').'</div>    
        <input  type="text" id="api_key" name="api_key" class="regular-text" value="'.$api_key.'"/>
    </div>'; 

    $general_latitude               =   esc_html( get_option('wp_estate_general_latitude') );
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Starting Point Latitude','wpestate').'</div>
    <div class="option_row_explain">'.__('Applies for global header media with google maps. Add only numbers (ex: 40.577906).','wpestate').'</div>    
    <input  type="text" id="general_latitude"  name="general_latitude"   value="'.$general_latitude.'"/>
    </div>'; 
    
    $general_longitude              =   esc_html( get_option('wp_estate_general_longitude') );
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Starting Point Longitude','wpestate').'</div>
    <div class="option_row_explain">'.__('Applies for global header media with google maps. Add only numbers (ex: -74.155058).','wpestate').'</div>    
    <input  type="text" id="general_longitude" name="general_longitude"  value="'.$general_longitude.'"/>
    </div>'; 
       
    $default_map_zoom               =   intval   ( get_option('wp_estate_default_map_zoom','') );
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Default Map zoom (1 to 20)','wpestate').'</div>
    <div class="option_row_explain">'.__('Applies for global header media with google maps, except advanced search results, properties list and taxonomies pages.','wpestate').'</div>    
    <input type="text" id="default_map_zoom" name="default_map_zoom" value="'.$default_map_zoom.'">   
    </div>'; 
    
    $map_types = array('SATELLITE','HYBRID','TERRAIN','ROADMAP');
    $default_map_type_symbol               =   wpestate_dropdowns_theme_admin($map_types,'default_map_type');
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Map Type','wpestate').'</div>
    <div class="option_row_explain">'.__('The type selected applies for Google Maps header. ','wpestate').'</div>    
        <select id="default_map_type" name="default_map_type">
            '.$default_map_type_symbol.'
        </select> 
    </div>'; 
        
    $cache_symbol                   =   wpestate_dropdowns_theme_admin($cache_array,'cache');
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Use Cache for Google maps ?(*cache will renew itself every 3h)','wpestate').'</div>
    <div class="option_row_explain">'.__('If set to yes, new property pins will update on the map every 3 hours.','wpestate').'</div>    
        <select id="cache" name="cache">
            '.$cache_symbol.'
        </select>
    </div>'; 

    $pin_cluster_symbol             =   wpestate_dropdowns_theme_admin($cache_array,'pin_cluster');
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Use Pin Cluster on map','wpestate').'</div>
    <div class="option_row_explain">'.__('If yes, it groups nearby pins in cluster.','wpestate').'</div>    
        <select id="pin_cluster" name="pin_cluster">
            '.$pin_cluster_symbol.'
        </select>
    </div>'; 
       
    $zoom_cluster                   =   esc_html ( get_option('wp_estate_zoom_cluster ','') );
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Maximum zoom level for Cloud Cluster to appear','wpestate').'</div>
    <div class="option_row_explain">'.__('Pin cluster disappears when map zoom is less than the value set in here. ','wpestate').'</div>    
        <input id="zoom_cluster" type="text" size="36" name="zoom_cluster" value="'.$zoom_cluster.'" />
    </div>'; 
    
    $hq_latitude                    =   esc_html ( get_option('wp_estate_hq_latitude') );
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Contact Page - Company HQ Latitude','wpestate').'</div>
    <div class="option_row_explain">'.__('Set company pin location for contact page template. Latitude must be a number (ex: 40.577906).','wpestate').'</div>    
        <input  type="text" id="hq_latitude"  name="hq_latitude"   value="'.$hq_latitude.'"/>
    </div>'; 
        
    $hq_longitude                   =   esc_html ( get_option('wp_estate_hq_longitude') );
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Contact Page - Company HQ Longitude','wpestate').'</div>
    <div class="option_row_explain">'.__('Set company pin location for contact page template. Longitude must be a number (ex: -74.155058).','wpestate').'</div>    
        <input  type="text" id="hq_longitude" name="hq_longitude"  value="'.$hq_longitude.'"/>
    </div>';   
        
    
    $idx_symbol             =   wpestate_dropdowns_theme_admin($cache_array,'idx_enable');
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Enable dsIDXpress to use the map','wpestate').'</div>
    <div class="option_row_explain">'.__('Enable only if you activate dsIDXpres optional plugin. See help for details.','wpestate').'</div>    
        <select id="idx_enable" name="idx_enable">
            '.$idx_symbol.'
        </select>
    </div>';     
    
    
    $geolocation_radius         =   esc_html ( get_option('wp_estate_geolocation_radius','') );
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Geolocation Circle over map (in meters)','wpestate').'</div>
    <div class="option_row_explain">'.__('Controls circle radius value for user geolocation pin. Type only numbers (ex: 400).','wpestate').'</div>    
       <input id="geolocation_radius" type="text" size="36" name="geolocation_radius" value="'.$geolocation_radius.'" />
    </div>'; 
       
    $min_height                     =   intval   ( get_option('wp_estate_min_height','') );
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Height of the Google Map when closed','wpestate').'</div>
    <div class="option_row_explain">'.__('Applies for header google maps when set as global header media type.','wpestate').'</div>    
       <input id="min_height" type="text" size="36" name="min_height" value="'.$min_height.'" />
    </div>';  
      
    $max_height                     =   intval   ( get_option('wp_estate_max_height','') );
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Height of Google Map when open','wpestate').'</div>
    <div class="option_row_explain">'.__('Applies for header google maps when set as global header media type.','wpestate').'</div>    
       <input id="max_height" type="text" size="36" name="max_height" value="'.$max_height.'" />
    </div>'; 
      
    $keep_min_symbol                    =   wpestate_dropdowns_theme_admin($cache_array2,'keep_min');
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Force Google Map at the "closed" size ? ','wpestate').'</div>
    <div class="option_row_explain">'.__('Applies for header google maps when set as global header media type, except property page.','wpestate').'</div>    
        <select id="keep_min" name="keep_min">
            '.$keep_min_symbol.'
        </select>
    </div>'; 
     
    
    $show_g_search_symbol               =   wpestate_dropdowns_theme_admin($cache_array2,'show_g_search');

    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Show Google Search over Map?','wpestate').'</div>
    <div class="option_row_explain">'.__('Enable or disable the Google Maps search bar.','wpestate').'</div>    
        <select id="show_g_search" name="show_g_search">
            '.$show_g_search_symbol.'
        </select>
    </div>'; 
     
    $map_style  =   esc_html ( get_option('wp_estate_map_style','') );    
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Style for Google Map. Use https://snazzymaps.com/ to create styles','wpestate').'</div>
    <div class="option_row_explain">'.__('Copy/paste below the custom map style code.','wpestate').'</div>    
        <textarea id="map_style" style="width:100%;height:350px;" name="map_style">'.stripslashes($map_style).'</textarea>
    </div>'; 

     print ' <div class="estate_option_row_submit">
    <input type="submit" name="submit"  class="new_admin_submit " value="'.__('Save Changes','wpestate').'" />
    </div>';

}   
endif;






    
if( !function_exists('new_wpestate_custom_colors') ):      
function new_wpestate_custom_colors(){
    $menu_items_color               =  esc_html ( get_option('wp_estate_menu_items_color','') );
    $agent_color                    =  esc_html ( get_option('wp_estate_agent_color','') );
    $color_scheme_array=array('no','yes');
    $color_scheme_select   = wpestate_dropdowns_theme_admin($color_scheme_array,'color_scheme');
    /*
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Use Custom Colors ?','wpestate').'</div>
    <div class="option_row_explain">'.__('You must set YES and save for your custom colors to apply.','wpestate').'</div>    
        <select id="color_scheme" name="color_scheme">
            '.$color_scheme_select.'
         </select>
    </div>'; 
       */
    $main_color                     =  esc_html ( get_option('wp_estate_main_color','') );
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Main Color','wpestate').'</div>
    <div class="option_row_explain">'.__('Main Color','wpestate').'</div>    
        <input type="text" name="main_color" maxlength="7" class="inptxt " value="'.$main_color.'"/>
        <div id="main_color" class="colorpickerHolder"><div class="sqcolor" style="background-color:#'.$main_color.';"  ></div></div>
    </div>';   
    
    $background_color               =  esc_html ( get_option('wp_estate_background_color','') );
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Background Color','wpestate').'</div>
    <div class="option_row_explain">'.__('Background Color','wpestate').'</div>    
        <input type="text" name="background_color" maxlength="7" class="inptxt " value="'.$background_color.'"/>
        <div id="background_color" class="colorpickerHolder"><div class="sqcolor" style="background-color:#'.$background_color.';"  ></div></div>
    </div>'; 

    $content_back_color             =  esc_html ( get_option('wp_estate_content_back_color','') );
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Content Background Color','wpestate').'</div>
    <div class="option_row_explain">'.__('Content Background Color','wpestate').'</div>    
        <input type="text" name="content_back_color" value="'.$content_back_color.'" maxlength="7" class="inptxt" />
        <div id="content_back_color" class="colorpickerHolder" ><div class="sqcolor"  style="background-color:#'.$content_back_color.';" ></div></div>
    </div>'; 
        
    $breadcrumbs_font_color         =  esc_html ( get_option('wp_estate_breadcrumbs_font_color','') );
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Breadcrumbs, Meta and Second Line Font Color','wpestate').'</div>
    <div class="option_row_explain">'.__('Breadcrumbs, Meta and Second Line Font Color','wpestate').'</div>    
        <input type="text" name="breadcrumbs_font_color" value="'.$breadcrumbs_font_color.'" maxlength="7" class="inptxt" />
        <div id="breadcrumbs_font_color" class="colorpickerHolder" ><div class="sqcolor" style="background-color:#'.$breadcrumbs_font_color.';" ></div></div>
    </div>';  
    
    
    $font_color                     =  esc_html ( get_option('wp_estate_font_color','') );
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Font Color','wpestate').'</div>
    <div class="option_row_explain">'.__('Font Color','wpestate').'</div>    
        <input type="text" name="font_color" value="'.$font_color.'" maxlength="7" class="inptxt" />
        <div id="font_color" class="colorpickerHolder" ><div class="sqcolor" style="background-color:#'.$font_color.';" ></div></div>
    </div>';
       
    $link_color                     =  esc_html ( get_option('wp_estate_link_color','') );
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Link Color','wpestate').'</div>
    <div class="option_row_explain">'.__('Link Color','wpestate').'</div>    
        <input type="text" name="link_color" value="'.$link_color.'" maxlength="7" class="inptxt" />
        <div id="link_color" class="colorpickerHolder" ><div class="sqcolor" style="background-color:#'.$link_color.';" ></div></div>
    </div>';
  
    $headings_color                 =  esc_html ( get_option('wp_estate_headings_color','') );
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Headings Color','wpestate').'</div>
    <div class="option_row_explain">'.__('Headings Color','wpestate').'</div>    
        <input type="text" name="headings_color" value="'.$headings_color.'" maxlength="7" class="inptxt" />
        <div id="headings_color" class="colorpickerHolder" ><div class="sqcolor" style="background-color:#'.$headings_color.';" ></div></div>
    </div>';
        
        
    $footer_back_color              =  esc_html ( get_option('wp_estate_footer_back_color','') );
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Footer Background Color','wpestate').'</div>
    <div class="option_row_explain">'.__('Footer Background Color','wpestate').'</div>    
        <input type="text" name="footer_back_color" value="'.$footer_back_color.'" maxlength="7" class="inptxt" />
        <div id="footer_back_color" class="colorpickerHolder" ><div class="sqcolor" style="background-color:#'.$footer_back_color.';" ></div></div>
    </div>';
        
    $footer_font_color              =  esc_html ( get_option('wp_estate_footer_font_color','') );
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Footer Font Color','wpestate').'</div>
    <div class="option_row_explain">'.__('Footer Font Color','wpestate').'</div>    
        <input type="text" name="footer_font_color" value="'.$footer_font_color.'" maxlength="7" class="inptxt" />
        <div id="footer_font_color" class="colorpickerHolder" ><div class="sqcolor" style="background-color:#'.$footer_font_color.';" ></div></div>
    </div>';
        
    $footer_copy_color              =  esc_html ( get_option('wp_estate_footer_copy_color','') );
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Footer Copyright Font Color','wpestate').'</div>
    <div class="option_row_explain">'.__('Footer Copyright Font Color','wpestate').'</div>    
        <input type="text" name="footer_copy_color" value="'.$footer_copy_color.'" maxlength="7" class="inptxt" />
        <div id="footer_copy_color" class="colorpickerHolder" ><div class="sqcolor" style="background-color:#'.$footer_copy_color.';" ></div></div>
    </div>';
        
  
    $footer_copy_back_color              =  esc_html ( get_option('wp_estate_footer_copy_back_color','') );
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Footer Copyright Area Background Font Color','wpestate').'</div>
    <div class="option_row_explain">'.__('Footer Copyright Area Background Font Color','wpestate').'</div>    
        <input type="text" name="footer_copy_back_color" value="'.$footer_copy_back_color.'" maxlength="7" class="inptxt" />
        <div id="footer_copy_back_color" class="colorpickerHolder" ><div class="sqcolor" style="background-color:#'.$footer_copy_back_color.';" ></div></div>
    </div>';
         
      
    $header_color                   =  esc_html ( get_option('wp_estate_header_color','') );
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Header Background Color','wpestate').'</div>
    <div class="option_row_explain">'.__('Header Background Color','wpestate').'</div>    
        <input type="text" name="header_color" value="'.$header_color.'" maxlength="7" class="inptxt" />
        <div id="header_color" class="colorpickerHolder" ><div class="sqcolor" style="background-color:#'.$header_color.';" ></div></div>
    </div>';
        
    
   
    $top_bar_back                   =  esc_html ( get_option('wp_estate_top_bar_back','') );
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Top Bar Background Color (Header Widget Menu)','wpestate').'</div>
    <div class="option_row_explain">'.__('Top Bar Background Color (Header Widget Menu)','wpestate').'</div>    
        <input type="text" name="top_bar_back" value="'.$top_bar_back.'" maxlength="7" class="inptxt" />
        <div id="top_bar_back" class="colorpickerHolder"><div class="sqcolor" style="background-color:#'.$top_bar_back.';"></div></div>
    </div>';
     
    $top_bar_font                   =  esc_html ( get_option('wp_estate_top_bar_font','') );
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Top Bar Font Color (Header Widget Menu)','wpestate').'</div>
    <div class="option_row_explain">'.__('Top Bar Font Color (Header Widget Menu)','wpestate').'</div>    
        <input type="text" name="top_bar_font" value="'.$top_bar_font.'" maxlength="7" class="inptxt" />
        <div id="top_bar_font" class="colorpickerHolder"><div class="sqcolor" style="background-color:#'.$top_bar_font.';"></div></div>
    </div>';
          
  
        
   
        
    $box_content_back_color         =  esc_html ( get_option('wp_estate_box_content_back_color','') );
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Boxed Content Background Color','wpestate').'</div>
    <div class="option_row_explain">'.__('Boxed Content Background Color','wpestate').'</div>    
        <input type="text" name="box_content_back_color" value="'.$box_content_back_color.'" maxlength="7" class="inptxt" />
        <div id="box_content_back_color" class="colorpickerHolder"><div class="sqcolor" style="background-color:#'.$box_content_back_color.';"></div></div>
    </div>';
        
    $box_content_border_color       =  esc_html ( get_option('wp_estate_box_content_border_color','') );
     print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Border Color','wpestate').'</div>
    <div class="option_row_explain">'.__('Border Color','wpestate').'</div>    
        <input type="text" name="box_content_border_color" value="'.$box_content_border_color.'" maxlength="7" class="inptxt" />
        <div id="box_content_border_color" class="colorpickerHolder"><div class="sqcolor" style="background-color:#'.$box_content_border_color.';"></div></div>
    </div>';
       
    $hover_button_color             =  esc_html ( get_option('wp_estate_hover_button_color','') );
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Hover Button Color','wpestate').'</div>
    <div class="option_row_explain">'.__('Hover Button Color','wpestate').'</div>    
        <input type="text" name="hover_button_color" value="'.$hover_button_color.'" maxlength="7" class="inptxt" />
        <div id="hover_button_color" class="colorpickerHolder"><div class="sqcolor" style="background-color:#'.$hover_button_color.';"></div></div>
    </div>';
     
    $map_controls_back            =  esc_html ( get_option('wp_estate_map_controls_back','') );
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Map Controls Background Color','wpestate').'</div>
    <div class="option_row_explain">'.__('Map Controls Background Color','wpestate').'</div>    
        <input type="text" name="map_controls_back" value="'.$map_controls_back.'" maxlength="7" class="inptxt" />
        <div id="map_controls_back" class="colorpickerHolder"><div class="sqcolor" style="background-color:#'.$map_controls_back.';"></div></div>
    </div>';
    
    $map_controls_font_color            =  esc_html ( get_option('wp_estate_map_controls_font_color','') );
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Map Controls Font Color','wpestate').'</div>
    <div class="option_row_explain">'.__('Map Controls Font Color','wpestate').'</div>    
        <input type="text" name="map_controls_font_color" value="'.$map_controls_font_color.'" maxlength="7" class="inptxt" />
        <div id="map_controls_font_color" class="colorpickerHolder"><div class="sqcolor" style="background-color:#'.$map_controls_font_color.';"></div></div>
    </div>';
     
       
    $custom_css                     =  esc_html ( stripslashes( get_option('wp_estate_custom_css','') ) );
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Custom Css','wpestate').'</div>
    <div class="option_row_explain">'.__('Overwrite theme css using custom css.','wpestate').'</div>    
        <textarea cols="57" rows="15" name="custom_css" id="custom_css">'.$custom_css.'</textarea>
    </div>';
        
    print ' <div class="estate_option_row_submit">
    <input type="submit" name="submit"  class="new_admin_submit " value="'.__('Save Changes','wpestate').'" />
    </div>';  
     
}
endif;


if( !function_exists('new_wpestate_custom_fonts') ):    
function   new_wpestate_custom_fonts(){
   /*   $google_fonts_array = array(                          
                                                            "Abel" => "Abel",
                                                            "Abril Fatface" => "Abril Fatface",
                                                            "Aclonica" => "Aclonica",
                                                            "Acme" => "Acme",
                                                            "Actor" => "Actor",
                                                            "Adamina" => "Adamina",
                                                            "Advent Pro" => "Advent Pro",
                                                            "Aguafina Script" => "Aguafina Script",
                                                            "Aladin" => "Aladin",
                                                            "Aldrich" => "Aldrich",
                                                            "Alegreya" => "Alegreya",
                                                            "Alegreya SC" => "Alegreya SC",
                                                            "Alex Brush" => "Alex Brush",
                                                            "Alfa Slab One" => "Alfa Slab One",
                                                            "Alice" => "Alice",
                                                            "Alike" => "Alike",
                                                            "Alike Angular" => "Alike Angular",
                                                            "Allan" => "Allan",
                                                            "Allerta" => "Allerta",
                                                            "Allerta Stencil" => "Allerta Stencil",
                                                            "Allura" => "Allura",
                                                            "Almendra" => "Almendra",
                                                            "Almendra SC" => "Almendra SC",
                                                            "Amaranth" => "Amaranth",
                                                            "Amatic SC" => "Amatic SC",
                                                            "Amethysta" => "Amethysta",
                                                            "Andada" => "Andada",
                                                            "Andika" => "Andika",
                                                            "Angkor" => "Angkor",
                                                            "Annie Use Your Telescope" => "Annie Use Your Telescope",
                                                            "Anonymous Pro" => "Anonymous Pro",
                                                            "Antic" => "Antic",
                                                            "Antic Didone" => "Antic Didone",
                                                            "Antic Slab" => "Antic Slab",
                                                            "Anton" => "Anton",
                                                            "Arapey" => "Arapey",
                                                            "Arbutus" => "Arbutus",
                                                            "Architects Daughter" => "Architects Daughter",
                                                            "Arimo" => "Arimo",
                                                            "Arizonia" => "Arizonia",
                                                            "Armata" => "Armata",
                                                            "Artifika" => "Artifika",
                                                            "Arvo" => "Arvo",
                                                            "Asap" => "Asap",
                                                            "Asset" => "Asset",
                                                            "Astloch" => "Astloch",
                                                            "Asul" => "Asul",
                                                            "Atomic Age" => "Atomic Age",
                                                            "Aubrey" => "Aubrey",
                                                            "Audiowide" => "Audiowide",
                                                            "Average" => "Average",
                                                            "Averia Gruesa Libre" => "Averia Gruesa Libre",
                                                            "Averia Libre" => "Averia Libre",
                                                            "Averia Sans Libre" => "Averia Sans Libre",
                                                            "Averia Serif Libre" => "Averia Serif Libre",
                                                            "Bad Script" => "Bad Script",
                                                            "Balthazar" => "Balthazar",
                                                            "Bangers" => "Bangers",
                                                            "Basic" => "Basic",
                                                            "Battambang" => "Battambang",
                                                            "Baumans" => "Baumans",
                                                            "Bayon" => "Bayon",
                                                            "Belgrano" => "Belgrano",
                                                            "Belleza" => "Belleza",
                                                            "Bentham" => "Bentham",
                                                            "Berkshire Swash" => "Berkshire Swash",
                                                            "Bevan" => "Bevan",
                                                            "Bigshot One" => "Bigshot One",
                                                            "Bilbo" => "Bilbo",
                                                            "Bilbo Swash Caps" => "Bilbo Swash Caps",
                                                            "Bitter" => "Bitter",
                                                            "Black Ops One" => "Black Ops One",
                                                            "Bokor" => "Bokor",
                                                            "Bonbon" => "Bonbon",
                                                            "Boogaloo" => "Boogaloo",
                                                            "Bowlby One" => "Bowlby One",
                                                            "Bowlby One SC" => "Bowlby One SC",
                                                            "Brawler" => "Brawler",
                                                            "Bree Serif" => "Bree Serif",
                                                            "Bubblegum Sans" => "Bubblegum Sans",
                                                            "Buda" => "Buda",
                                                            "Buenard" => "Buenard",
                                                            "Butcherman" => "Butcherman",
                                                            "Butterfly Kids" => "Butterfly Kids",
                                                            "Cabin" => "Cabin",
                                                            "Cabin Condensed" => "Cabin Condensed",
                                                            "Cabin Sketch" => "Cabin Sketch",
                                                            "Caesar Dressing" => "Caesar Dressing",
                                                            "Cagliostro" => "Cagliostro",
                                                            "Calligraffitti" => "Calligraffitti",
                                                            "Cambo" => "Cambo",
                                                            "Candal" => "Candal",
                                                            "Cantarell" => "Cantarell",
                                                            "Cantata One" => "Cantata One",
                                                            "Cardo" => "Cardo",
                                                            "Carme" => "Carme",
                                                            "Carter One" => "Carter One",
                                                            "Caudex" => "Caudex",
                                                            "Cedarville Cursive" => "Cedarville Cursive",
                                                            "Ceviche One" => "Ceviche One",
                                                            "Changa One" => "Changa One",
                                                            "Chango" => "Chango",
                                                            "Chau Philomene One" => "Chau Philomene One",
                                                            "Chelsea Market" => "Chelsea Market",
                                                            "Chenla" => "Chenla",
                                                            "Cherry Cream Soda" => "Cherry Cream Soda",
                                                            "Chewy" => "Chewy",
                                                            "Chicle" => "Chicle",
                                                            "Chivo" => "Chivo",
                                                            "Coda" => "Coda",
                                                            "Coda Caption" => "Coda Caption",
                                                            "Codystar" => "Codystar",
                                                            "Comfortaa" => "Comfortaa",
                                                            "Coming Soon" => "Coming Soon",
                                                            "Concert One" => "Concert One",
                                                            "Condiment" => "Condiment",
                                                            "Content" => "Content",
                                                            "Contrail One" => "Contrail One",
                                                            "Convergence" => "Convergence",
                                                            "Cookie" => "Cookie",
                                                            "Copse" => "Copse",
                                                            "Corben" => "Corben",
                                                            "Cousine" => "Cousine",
                                                            "Coustard" => "Coustard",
                                                            "Covered By Your Grace" => "Covered By Your Grace",
                                                            "Crafty Girls" => "Crafty Girls",
                                                            "Creepster" => "Creepster",
                                                            "Crete Round" => "Crete Round",
                                                            "Crimson Text" => "Crimson Text",
                                                            "Crushed" => "Crushed",
                                                            "Cuprum" => "Cuprum",
                                                            "Cutive" => "Cutive",
                                                            "Damion" => "Damion",
                                                            "Dancing Script" => "Dancing Script",
                                                            "Dangrek" => "Dangrek",
                                                            "Dawning of a New Day" => "Dawning of a New Day",
                                                            "Days One" => "Days One",
                                                            "Delius" => "Delius",
                                                            "Delius Swash Caps" => "Delius Swash Caps",
                                                            "Delius Unicase" => "Delius Unicase",
                                                            "Della Respira" => "Della Respira",
                                                            "Devonshire" => "Devonshire",
                                                            "Didact Gothic" => "Didact Gothic",
                                                            "Diplomata" => "Diplomata",
                                                            "Diplomata SC" => "Diplomata SC",
                                                            "Doppio One" => "Doppio One",
                                                            "Dorsa" => "Dorsa",
                                                            "Dosis" => "Dosis",
                                                            "Dr Sugiyama" => "Dr Sugiyama",
                                                            "Droid Sans" => "Droid Sans",
                                                            "Droid Sans Mono" => "Droid Sans Mono",
                                                            "Droid Serif" => "Droid Serif",
                                                            "Duru Sans" => "Duru Sans",
                                                            "Dynalight" => "Dynalight",
                                                            "EB Garamond" => "EB Garamond",
                                                            "Eater" => "Eater",
                                                            "Economica" => "Economica",
                                                            "Electrolize" => "Electrolize",
                                                            "Emblema One" => "Emblema One",
                                                            "Emilys Candy" => "Emilys Candy",
                                                            "Engagement" => "Engagement",
                                                            "Enriqueta" => "Enriqueta",
                                                            "Erica One" => "Erica One",
                                                            "Esteban" => "Esteban",
                                                            "Euphoria Script" => "Euphoria Script",
                                                            "Ewert" => "Ewert",
                                                            "Exo" => "Exo",
                                                            "Expletus Sans" => "Expletus Sans",
                                                            "Fanwood Text" => "Fanwood Text",
                                                            "Fascinate" => "Fascinate",
                                                            "Fascinate Inline" => "Fascinate Inline",
                                                            "Federant" => "Federant",
                                                            "Federo" => "Federo",
                                                            "Felipa" => "Felipa",
                                                            "Fjord One" => "Fjord One",
                                                            "Flamenco" => "Flamenco",
                                                            "Flavors" => "Flavors",
                                                            "Fondamento" => "Fondamento",
                                                            "Fontdiner Swanky" => "Fontdiner Swanky",
                                                            "Forum" => "Forum",
                                                            "Francois One" => "Francois One",
                                                            "Fredericka the Great" => "Fredericka the Great",
                                                            "Fredoka One" => "Fredoka One",
                                                            "Freehand" => "Freehand",
                                                            "Fresca" => "Fresca",
                                                            "Frijole" => "Frijole",
                                                            "Fugaz One" => "Fugaz One",
                                                            "GFS Didot" => "GFS Didot",
                                                            "GFS Neohellenic" => "GFS Neohellenic",
                                                            "Galdeano" => "Galdeano",
                                                            "Gentium Basic" => "Gentium Basic",
                                                            "Gentium Book Basic" => "Gentium Book Basic",
                                                            "Geo" => "Geo",
                                                            "Geostar" => "Geostar",
                                                            "Geostar Fill" => "Geostar Fill",
                                                            "Germania One" => "Germania One",
                                                            "Give You Glory" => "Give You Glory",
                                                            "Glass Antiqua" => "Glass Antiqua",
                                                            "Glegoo" => "Glegoo",
                                                            "Gloria Hallelujah" => "Gloria Hallelujah",
                                                            "Goblin One" => "Goblin One",
                                                            "Gochi Hand" => "Gochi Hand",
                                                            "Gorditas" => "Gorditas",
                                                            "Goudy Bookletter 1911" => "Goudy Bookletter 1911",
                                                            "Graduate" => "Graduate",
                                                            "Gravitas One" => "Gravitas One",
                                                            "Great Vibes" => "Great Vibes",
                                                            "Gruppo" => "Gruppo",
                                                            "Gudea" => "Gudea",
                                                            "Habibi" => "Habibi",
                                                            "Hammersmith One" => "Hammersmith One",
                                                            "Handlee" => "Handlee",
                                                            "Hanuman" => "Hanuman",
                                                            "Happy Monkey" => "Happy Monkey",
                                                            "Henny Penny" => "Henny Penny",
                                                            "Herr Von Muellerhoff" => "Herr Von Muellerhoff",
                                                            "Holtwood One SC" => "Holtwood One SC",
                                                            "Homemade Apple" => "Homemade Apple",
                                                            "Homenaje" => "Homenaje",
                                                            "IM Fell DW Pica" => "IM Fell DW Pica",
                                                            "IM Fell DW Pica SC" => "IM Fell DW Pica SC",
                                                            "IM Fell Double Pica" => "IM Fell Double Pica",
                                                            "IM Fell Double Pica SC" => "IM Fell Double Pica SC",
                                                            "IM Fell English" => "IM Fell English",
                                                            "IM Fell English SC" => "IM Fell English SC",
                                                            "IM Fell French Canon" => "IM Fell French Canon",
                                                            "IM Fell French Canon SC" => "IM Fell French Canon SC",
                                                            "IM Fell Great Primer" => "IM Fell Great Primer",
                                                            "IM Fell Great Primer SC" => "IM Fell Great Primer SC",
                                                            "Iceberg" => "Iceberg",
                                                            "Iceland" => "Iceland",
                                                            "Imprima" => "Imprima",
                                                            "Inconsolata" => "Inconsolata",
                                                            "Inder" => "Inder",
                                                            "Indie Flower" => "Indie Flower",
                                                            "Inika" => "Inika",
                                                            "Irish Grover" => "Irish Grover",
                                                            "Istok Web" => "Istok Web",
                                                            "Italiana" => "Italiana",
                                                            "Italianno" => "Italianno",
                                                            "Jim Nightshade" => "Jim Nightshade",
                                                            "Jockey One" => "Jockey One",
                                                            "Jolly Lodger" => "Jolly Lodger",
                                                            "Josefin Sans" => "Josefin Sans",
                                                            "Josefin Slab" => "Josefin Slab",
                                                            "Judson" => "Judson",
                                                            "Julee" => "Julee",
                                                            "Junge" => "Junge",
                                                            "Jura" => "Jura",
                                                            "Just Another Hand" => "Just Another Hand",
                                                            "Just Me Again Down Here" => "Just Me Again Down Here",
                                                            "Kameron" => "Kameron",
                                                            "Karla" => "Karla",
                                                            "Kaushan Script" => "Kaushan Script",
                                                            "Kelly Slab" => "Kelly Slab",
                                                            "Kenia" => "Kenia",
                                                            "Khmer" => "Khmer",
                                                            "Knewave" => "Knewave",
                                                            "Kotta One" => "Kotta One",
                                                            "Koulen" => "Koulen",
                                                            "Kranky" => "Kranky",
                                                            "Kreon" => "Kreon",
                                                            "Kristi" => "Kristi",
                                                            "Krona One" => "Krona One",
                                                            "La Belle Aurore" => "La Belle Aurore",
                                                            "Lancelot" => "Lancelot",
                                                            "Lato" => "Lato",
                                                            "League Script" => "League Script",
                                                            "Leckerli One" => "Leckerli One",
                                                            "Ledger" => "Ledger",
                                                            "Lekton" => "Lekton",
                                                            "Lemon" => "Lemon",
                                                            "Lilita One" => "Lilita One",
                                                            "Limelight" => "Limelight",
                                                            "Linden Hill" => "Linden Hill",
                                                            "Lobster" => "Lobster",
                                                            "Lobster Two" => "Lobster Two",
                                                            "Londrina Outline" => "Londrina Outline",
                                                            "Londrina Shadow" => "Londrina Shadow",
                                                            "Londrina Sketch" => "Londrina Sketch",
                                                            "Londrina Solid" => "Londrina Solid",
                                                            "Lora" => "Lora",
                                                            "Love Ya Like A Sister" => "Love Ya Like A Sister",
                                                            "Loved by the King" => "Loved by the King",
                                                            "Lovers Quarrel" => "Lovers Quarrel",
                                                            "Luckiest Guy" => "Luckiest Guy",
                                                            "Lusitana" => "Lusitana",
                                                            "Lustria" => "Lustria",
                                                            "Macondo" => "Macondo",
                                                            "Macondo Swash Caps" => "Macondo Swash Caps",
                                                            "Magra" => "Magra",
                                                            "Maiden Orange" => "Maiden Orange",
                                                            "Mako" => "Mako",
                                                            "Marck Script" => "Marck Script",
                                                            "Marko One" => "Marko One",
                                                            "Marmelad" => "Marmelad",
                                                            "Marvel" => "Marvel",
                                                            "Mate" => "Mate",
                                                            "Mate SC" => "Mate SC",
                                                            "Maven Pro" => "Maven Pro",
                                                            "Meddon" => "Meddon",
                                                            "MedievalSharp" => "MedievalSharp",
                                                            "Medula One" => "Medula One",
                                                            "Megrim" => "Megrim",
                                                            "Merienda One" => "Merienda One",
                                                            "Merriweather" => "Merriweather",
                                                            "Metal" => "Metal",
                                                            "Metamorphous" => "Metamorphous",
                                                            "Metrophobic" => "Metrophobic",
                                                            "Michroma" => "Michroma",
                                                            "Miltonian" => "Miltonian",
                                                            "Miltonian Tattoo" => "Miltonian Tattoo",
                                                            "Miniver" => "Miniver",
                                                            "Miss Fajardose" => "Miss Fajardose",
                                                            "Modern Antiqua" => "Modern Antiqua",
                                                            "Molengo" => "Molengo",
                                                            "Monofett" => "Monofett",
                                                            "Monoton" => "Monoton",
                                                            "Monsieur La Doulaise" => "Monsieur La Doulaise",
                                                            "Montaga" => "Montaga",
                                                            "Montez" => "Montez",
                                                            "Montserrat" => "Montserrat",
                                                            "Moul" => "Moul",
                                                            "Moulpali" => "Moulpali",
                                                            "Mountains of Christmas" => "Mountains of Christmas",
                                                            "Mr Bedfort" => "Mr Bedfort",
                                                            "Mr Dafoe" => "Mr Dafoe",
                                                            "Mr De Haviland" => "Mr De Haviland",
                                                            "Mrs Saint Delafield" => "Mrs Saint Delafield",
                                                            "Mrs Sheppards" => "Mrs Sheppards",
                                                            "Muli" => "Muli",
                                                            "Mystery Quest" => "Mystery Quest",
                                                            "Neucha" => "Neucha",
                                                            "Neuton" => "Neuton",
                                                            "News Cycle" => "News Cycle",
                                                            "Niconne" => "Niconne",
                                                            "Nixie One" => "Nixie One",
                                                            "Nobile" => "Nobile",
                                                            "Nokora" => "Nokora",
                                                            "Norican" => "Norican",
                                                            "Nosifer" => "Nosifer",
                                                            "Nothing You Could Do" => "Nothing You Could Do",
                                                            "Noticia Text" => "Noticia Text",
                                                            "Nova Cut" => "Nova Cut",
                                                            "Nova Flat" => "Nova Flat",
                                                            "Nova Mono" => "Nova Mono",
                                                            "Nova Oval" => "Nova Oval",
                                                            "Nova Round" => "Nova Round",
                                                            "Nova Script" => "Nova Script",
                                                            "Nova Slim" => "Nova Slim",
                                                            "Nova Square" => "Nova Square",
                                                            "Numans" => "Numans",
                                                            "Nunito" => "Nunito",
                                                            "Odor Mean Chey" => "Odor Mean Chey",
                                                            "Old Standard TT" => "Old Standard TT",
                                                            "Oldenburg" => "Oldenburg",
                                                            "Oleo Script" => "Oleo Script",
                                                            "Open Sans" => "Open Sans",
                                                            "Open Sans Condensed" => "Open Sans Condensed",
                                                            "Orbitron" => "Orbitron",
                                                            "Original Surfer" => "Original Surfer",
                                                            "Oswald" => "Oswald",
                                                            "Over the Rainbow" => "Over the Rainbow",
                                                            "Overlock" => "Overlock",
                                                            "Overlock SC" => "Overlock SC",
                                                            "Ovo" => "Ovo",
                                                            "Oxygen" => "Oxygen",
                                                            "PT Mono" => "PT Mono",
                                                            "PT Sans" => "PT Sans",
                                                            "PT Sans Caption" => "PT Sans Caption",
                                                            "PT Sans Narrow" => "PT Sans Narrow",
                                                            "PT Serif" => "PT Serif",
                                                            "PT Serif Caption" => "PT Serif Caption",
                                                            "Pacifico" => "Pacifico",
                                                            "Parisienne" => "Parisienne",
                                                            "Passero One" => "Passero One",
                                                            "Passion One" => "Passion One",
                                                            "Patrick Hand" => "Patrick Hand",
                                                            "Patua One" => "Patua One",
                                                            "Paytone One" => "Paytone One",
                                                            "Permanent Marker" => "Permanent Marker",
                                                            "Petrona" => "Petrona",
                                                            "Philosopher" => "Philosopher",
                                                            "Piedra" => "Piedra",
                                                            "Pinyon Script" => "Pinyon Script",
                                                            "Plaster" => "Plaster",
                                                            "Play" => "Play",
                                                            "Playball" => "Playball",
                                                            "Playfair Display" => "Playfair Display",
                                                            "Podkova" => "Podkova",
                                                            "Poiret One" => "Poiret One",
                                                            "Poller One" => "Poller One",
                                                            "Poly" => "Poly",
                                                            "Pompiere" => "Pompiere",
                                                            "Pontano Sans" => "Pontano Sans",
                                                            "Port Lligat Sans" => "Port Lligat Sans",
                                                            "Port Lligat Slab" => "Port Lligat Slab",
                                                            "Prata" => "Prata",
                                                            "Preahvihear" => "Preahvihear",
                                                            "Press Start 2P" => "Press Start 2P",
                                                            "Princess Sofia" => "Princess Sofia",
                                                            "Prociono" => "Prociono",
                                                            "Prosto One" => "Prosto One",
                                                            "Puritan" => "Puritan",
                                                            "Quantico" => "Quantico",
                                                            "Quattrocento" => "Quattrocento",
                                                            "Quattrocento Sans" => "Quattrocento Sans",
                                                            "Questrial" => "Questrial",
                                                            "Quicksand" => "Quicksand",
                                                            "Qwigley" => "Qwigley",
                                                            "Radley" => "Radley",
                                                            "Raleway" => "Raleway",
                                                            "Rammetto One" => "Rammetto One",
                                                            "Rancho" => "Rancho",
                                                            "Rationale" => "Rationale",
                                                            "Redressed" => "Redressed",
                                                            "Reenie Beanie" => "Reenie Beanie",
                                                            "Revalia" => "Revalia",
                                                            "Ribeye" => "Ribeye",
                                                            "Ribeye Marrow" => "Ribeye Marrow",
                                                            "Righteous" => "Righteous",
                                                            "Rochester" => "Rochester",
                                                            "Rock Salt" => "Rock Salt",
                                                            "Rokkitt" => "Rokkitt",
                                                            "Ropa Sans" => "Ropa Sans",
                                                            "Rosario" => "Rosario",
                                                            "Rosarivo" => "Rosarivo",
                                                            "Rouge Script" => "Rouge Script",
                                                            "Ruda" => "Ruda",
                                                            "Ruge Boogie" => "Ruge Boogie",
                                                            "Ruluko" => "Ruluko",
                                                            "Ruslan Display" => "Ruslan Display",
                                                            "Russo One" => "Russo One",
                                                            "Ruthie" => "Ruthie",
                                                            "Sail" => "Sail",
                                                            "Salsa" => "Salsa",
                                                            "Sancreek" => "Sancreek",
                                                            "Sansita One" => "Sansita One",
                                                            "Sarina" => "Sarina",
                                                            "Satisfy" => "Satisfy",
                                                            "Schoolbell" => "Schoolbell",
                                                            "Seaweed Script" => "Seaweed Script",
                                                            "Sevillana" => "Sevillana",
                                                            "Shadows Into Light" => "Shadows Into Light",
                                                            "Shadows Into Light Two" => "Shadows Into Light Two",
                                                            "Shanti" => "Shanti",
                                                            "Share" => "Share",
                                                            "Shojumaru" => "Shojumaru",
                                                            "Short Stack" => "Short Stack",
                                                            "Siemreap" => "Siemreap",
                                                            "Sigmar One" => "Sigmar One",
                                                            "Signika" => "Signika",
                                                            "Signika Negative" => "Signika Negative",
                                                            "Simonetta" => "Simonetta",
                                                            "Sirin Stencil" => "Sirin Stencil",
                                                            "Six Caps" => "Six Caps",
                                                            "Slackey" => "Slackey",
                                                            "Smokum" => "Smokum",
                                                            "Smythe" => "Smythe",
                                                            "Sniglet" => "Sniglet",
                                                            "Snippet" => "Snippet",
                                                            "Sofia" => "Sofia",
                                                            "Sonsie One" => "Sonsie One",
                                                            "Sorts Mill Goudy" => "Sorts Mill Goudy",
                                                            "Special Elite" => "Special Elite",
                                                            "Spicy Rice" => "Spicy Rice",
                                                            "Spinnaker" => "Spinnaker",
                                                            "Spirax" => "Spirax",
                                                            "Squada One" => "Squada One",
                                                            "Stardos Stencil" => "Stardos Stencil",
                                                            "Stint Ultra Condensed" => "Stint Ultra Condensed",
                                                            "Stint Ultra Expanded" => "Stint Ultra Expanded",
                                                            "Stoke" => "Stoke",
                                                            "Sue Ellen Francisco" => "Sue Ellen Francisco",
                                                            "Sunshiney" => "Sunshiney",
                                                            "Supermercado One" => "Supermercado One",
                                                            "Suwannaphum" => "Suwannaphum",
                                                            "Swanky and Moo Moo" => "Swanky and Moo Moo",
                                                            "Syncopate" => "Syncopate",
                                                            "Tangerine" => "Tangerine",
                                                            "Taprom" => "Taprom",
                                                            "Telex" => "Telex",
                                                            "Tenor Sans" => "Tenor Sans",
                                                            "The Girl Next Door" => "The Girl Next Door",
                                                            "Tienne" => "Tienne",
                                                            "Tinos" => "Tinos",
                                                            "Titan One" => "Titan One",
                                                            "Trade Winds" => "Trade Winds",
                                                            "Trocchi" => "Trocchi",
                                                            "Trochut" => "Trochut",
                                                            "Trykker" => "Trykker",
                                                            "Tulpen One" => "Tulpen One",
                                                            "Ubuntu" => "Ubuntu",
                                                            "Ubuntu Condensed" => "Ubuntu Condensed",
                                                            "Ubuntu Mono" => "Ubuntu Mono",
                                                            "Ultra" => "Ultra",
                                                            "Uncial Antiqua" => "Uncial Antiqua",
                                                            "UnifrakturCook" => "UnifrakturCook",
                                                            "UnifrakturMaguntia" => "UnifrakturMaguntia",
                                                            "Unkempt" => "Unkempt",
                                                            "Unlock" => "Unlock",
                                                            "Unna" => "Unna",
                                                            "VT323" => "VT323",
                                                            "Varela" => "Varela",
                                                            "Varela Round" => "Varela Round",
                                                            "Vast Shadow" => "Vast Shadow",
                                                            "Vibur" => "Vibur",
                                                            "Vidaloka" => "Vidaloka",
                                                            "Viga" => "Viga",
                                                            "Voces" => "Voces",
                                                            "Volkhov" => "Volkhov",
                                                            "Vollkorn" => "Vollkorn",
                                                            "Voltaire" => "Voltaire",
                                                            "Waiting for the Sunrise" => "Waiting for the Sunrise",
                                                            "Wallpoet" => "Wallpoet",
                                                            "Walter Turncoat" => "Walter Turncoat",
                                                            "Wellfleet" => "Wellfleet",
                                                            "Wire One" => "Wire One",
                                                            "Yanone Kaffeesatz" => "Yanone Kaffeesatz",
                                                            "Yellowtail" => "Yellowtail",
                                                            "Yeseva One" => "Yeseva One",
                                                            "Yesteryear" => "Yesteryear",
                                                            "Zeyada" => "Zeyada",
                                                    ); */
$google_fonts_array = array(
                'ABeeZee',
                'Abel',
                'Abril+Fatface',
                'Aclonica',
                'Acme',
                'Actor',
                'Adamina',
                'Advent+Pro',
                'Aguafina+Script',
                'Akronim',
                'Aladin',
                'Aldrich',
                'Alef',
                'Alegreya',
                'Alegreya+Sans',
                'Alegreya+Sans+SC',
                'Alegreya+SC',
                'Alex+Brush',
                'Alfa+Slab+One',
                'Alice',
                'Alike',
                'Alike+Angular',
                'Allan',
                'Allerta',
                'Allerta+Stencil',
                'Allura',
                'Almendra',
                'Almendra+Display',
                'Almendra+SC',
                'Amarante',
                'Amaranth',
                'Amatic+SC',
                'Amethysta',
                'Amiri',
                'Amita',
                'Anaheim',
                'Andada',
                'Andika',
                'Angkor',
                'Annie+Use+Your+Telescope',
                'Anonymous+Pro',
                'Antic',
                'Antic+Didone',
                'Antic+Slab',
                'Anton',
                'Arapey',
                'Arbutus',
                'Arbutus+Slab',
                'Architects+Daughter',
                'Archivo+Black',
                'Archivo+Narrow',
                'Arimo',
                'Arizonia',
                'Armata',
                'Artifika',
                'Arvo',
                'Arya',
                'Asap',
                'Asar',
                'Asset',
                'Astloch',
                'Asul',
                'Atomic+Age',
                'Aubrey',
                'Audiowide',
                'Autour+One',
                'Average',
                'Average+Sans',
                'Averia+Gruesa+Libre',
                'Averia+Libre',
                'Averia+Sans+Libre',
                'Averia+Serif+Libre',
                'Bad+Script',
                'Balthazar',
                'Bangers',
                'Basic',
                'Battambang',
                'Baumans',
                'Bayon',
                'Belgrano',
                'Belleza',
                'BenchNine',
                'Bentham',
                'Berkshire+Swash',
                'Bevan',
                'Bigelow+Rules',
                'Bigshot+One',
                'Bilbo',
                'Bilbo+Swash+Caps',
                'Biryani',
                'Bitter',
                'Black+Ops+One',
                'Bokor',
                'Bonbon',
                'Boogaloo',
                'Bowlby+One',
                'Bowlby+One+SC',
                'Brawler',
                'Bree+Serif',
                'Bubblegum+Sans',
                'Bubbler+One',
                'Buda',
                'Buenard',
                'Butcherman',
                'Butterfly+Kids',
                'Cabin',
                'Cabin+Condensed',
                'Cabin+Sketch',
                'Caesar+Dressing',
                'Cagliostro',
                'Calligraffitti',
                'Cambay',
                'Cambo',
                'Candal',
                'Cantarell',
                'Cantata+One',
                'Cantora+One',
                'Capriola',
                'Cardo',
                'Carme',
                'Carrois+Gothic',
                'Carrois+Gothic+SC',
                'Carter+One',
                'Catamaran',
                'Caudex',
                'Cedarville+Cursive',
                'Ceviche+One',
                'Changa+One',
                'Chango',
                'Chau+Philomene+One',
                'Chela+One',
                'Chelsea+Market',
                'Chenla',
                'Cherry+Cream+Soda',
                'Cherry+Swash',
                'Chewy',
                'Chicle',
                'Chivo',
                'Chonburi',
                'Cinzel',
                'Cinzel+Decorative',
                'Clicker+Script',
                'Coda',
                'Coda+Caption',
                'Codystar',
                'Combo',
                'Comfortaa',
                'Coming+Soon',
                'Concert+One',
                'Condiment',
                'Content',
                'Contrail+One',
                'Convergence',
                'Cookie',
                'Copse',
                'Corben',
                'Courgette',
                'Cousine',
                'Coustard',
                'Covered+By+Your+Grace',
                'Crafty+Girls',
                'Creepster',
                'Crete+Round',
                'Crimson+Text',
                'Croissant+One',
                'Crushed',
                'Cuprum',
                'Cutive',
                'Cutive+Mono',
                'Damion',
                'Dancing+Script',
                'Dangrek',
                'Dawning+of+a+New+Day',
                'Days+One',
                'Dekko',
                'Delius',
                'Delius+Swash+Caps',
                'Delius+Unicase',
                'Della+Respira',
                'Denk+One',
                'Devonshire',
                'Dhurjati',
                'Didact+Gothic',
                'Diplomata',
                'Diplomata+SC',
                'Domine',
                'Donegal+One',
                'Doppio+One',
                'Dorsa',
                'Dosis',
                'Dr+Sugiyama',
                'Droid+Sans',
                'Droid+Sans+Mono',
                'Droid+Serif',
                'Duru+Sans',
                'Dynalight',
                'Eagle+Lake',
                'Eater',
                'EB+Garamond',
                'Economica',
                'Eczar',
                'Ek+Mukta',
                'Electrolize',
                'Elsie',
                'Elsie+Swash+Caps',
                'Emblema+One',
                'Emilys+Candy',
                'Engagement',
                'Englebert',
                'Enriqueta',
                'Erica+One',
                'Esteban',
                'Euphoria+Script',
                'Ewert',
                'Exo',
                'Exo+2',
                'Expletus+Sans',
                'Fanwood+Text',
                'Fascinate',
                'Fascinate+Inline',
                'Faster+One',
                'Fasthand',
                'Fauna+One',
                'Federant',
                'Federo',
                'Felipa',
                'Fenix',
                'Finger+Paint',
                'Fira+Mono',
                'Fira+Sans',
                'Fjalla+One',
                'Fjord+One',
                'Flamenco',
                'Flavors',
                'Fondamento',
                'Fontdiner+Swanky',
                'Forum',
                'Francois+One',
                'Freckle+Face',
                'Fredericka+the+Great',
                'Fredoka+One',
                'Freehand',
                'Fresca',
                'Frijole',
                'Fruktur',
                'Fugaz+One',
                'Gabriela',
                'Gafata',
                'Galdeano',
                'Galindo',
                'Gentium+Basic',
                'Gentium+Book+Basic',
                'Geo',
                'Geostar',
                'Geostar+Fill',
                'Germania+One',
                'GFS+Didot',
                'GFS+Neohellenic',
                'Gidugu',
                'Gilda+Display',
                'Give+You+Glory',
                'Glass+Antiqua',
                'Glegoo',
                'Gloria+Hallelujah',
                'Goblin+One',
                'Gochi+Hand',
                'Gorditas',
                'Goudy+Bookletter+1911',
                'Graduate',
                'Grand+Hotel',
                'Gravitas+One',
                'Great+Vibes',
                'Griffy',
                'Gruppo',
                'Gudea',
                'Gurajada',
                'Habibi',
                'Halant',
                'Hammersmith+One',
                'Hanalei',
                'Hanalei+Fill',
                'Handlee',
                'Hanuman',
                'Happy+Monkey',
                'Headland+One',
                'Henny+Penny',
                'Herr+Von+Muellerhoff',
                'Hind',
                'Holtwood+One+SC',
                'Homemade+Apple',
                'Homenaje',
                'Iceberg',
                'Iceland',
                'IM+Fell+Double+Pica',
                'IM+Fell+Double+Pica+SC',
                'IM+Fell+DW+Pica',
                'IM+Fell+DW+Pica+SC',
                'IM+Fell+English',
                'IM+Fell+English+SC',
                'IM+Fell+French+Canon',
                'IM+Fell+French+Canon+SC',
                'IM+Fell+Great+Primer',
                'IM+Fell+Great+Primer+SC',
                'Imprima',
                'Inconsolata',
                'Inder',
                'Indie+Flower',
                'Inika',
                'Inknut+Antiqua',
                'Irish+Grover',
                'Istok+Web',
                'Italiana',
                'Italianno',
                'Itim',
                'Jacques+Francois',
                'Jacques+Francois+Shadow',
                'Jaldi',
                'Jim+Nightshade',
                'Jockey+One',
                'Jolly+Lodger',
                'Josefin+Sans',
                'Josefin+Slab',
                'Joti+One',
                'Judson',
                'Julee',
                'Julius+Sans+One',
                'Junge',
                'Jura',
                'Just+Another+Hand',
                'Just+Me+Again+Down+Here',
                'Kadwa',
                'Kalam',
                'Kameron',
                'Kantumruy',
                'Karla',
                'Karma',
                'Kaushan+Script',
                'Kavoon',
                'Kdam+Thmor',
                'Keania+One',
                'Kelly+Slab',
                'Kenia',
                'Khand',
                'Khmer',
                'Khula',
                'Kite+One',
                'Knewave',
                'Kotta+One',
                'Koulen',
                'Kranky',
                'Kreon',
                'Kristi',
                'Krona+One',
                'Kurale',
                'La+Belle+Aurore',
                'Laila',
                'Lakki+Reddy',
                'Lancelot',
                'Lateef',
                'Lato',
                'League+Script',
                'Leckerli+One',
                'Ledger',
                'Lekton',
                'Lemon',
                'Libre+Baskerville',
                'Life+Savers',
                'Lilita+One',
                'Lily+Script+One',
                'Limelight',
                'Linden+Hill',
                'Lobster',
                'Lobster+Two',
                'Londrina+Outline',
                'Londrina+Shadow',
                'Londrina+Sketch',
                'Londrina+Solid',
                'Lora',
                'Love+Ya+Like+A+Sister',
                'Loved+by+the+King',
                'Lovers+Quarrel',
                'Luckiest+Guy',
                'Lusitana',
                'Lustria',
                'Macondo',
                'Macondo+Swash+Caps',
                'Magra',
                'Maiden+Orange',
                'Mako',
                'Mallanna',
                'Mandali',
                'Marcellus',
                'Marcellus+SC',
                'Marck+Script',
                'Margarine',
                'Marko+One',
                'Marmelad',
                'Martel',
                'Martel+Sans',
                'Marvel',
                'Mate',
                'Mate+SC',
                'Maven+Pro',
                'McLaren',
                'Meddon',
                'MedievalSharp',
                'Medula+One',
                'Megrim',
                'Meie+Script',
                'Merienda',
                'Merienda+One',
                'Merriweather',
                'Merriweather+Sans',
                'Metal',
                'Metal+Mania',
                'Metamorphous',
                'Metrophobic',
                'Michroma',
                'Milonga',
                'Miltonian',
                'Miltonian+Tattoo',
                'Miniver',
                'Miss+Fajardose',
                'Modak',
                'Modern+Antiqua',
                'Molengo',
                'Molle',
                'Monda',
                'Monofett',
                'Monoton',
                'Monsieur+La+Doulaise',
                'Montaga',
                'Montez',
                'Montserrat',
                'Montserrat+Alternates',
                'Montserrat+Subrayada',
                'Moul',
                'Moulpali',
                'Mountains+of+Christmas',
                'Mouse+Memoirs',
                'Mr+Bedfort',
                'Mr+Dafoe',
                'Mr+De+Haviland',
                'Mrs+Saint+Delafield',
                'Mrs+Sheppards',
                'Muli',
                'Mystery+Quest',
                'Neucha',
                'Neuton',
                'New+Rocker',
                'News+Cycle',
                'Niconne',
                'Nixie+One',
                'Nobile',
                'Nokora',
                'Norican',
                'Nosifer',
                'Nothing+You+Could+Do',
                'Noticia+Text',
                'Noto+Sans',
                'Noto+Serif',
                'Nova+Cut',
                'Nova+Flat',
                'Nova+Mono',
                'Nova+Oval',
                'Nova+Round',
                'Nova+Script',
                'Nova+Slim',
                'Nova+Square',
                'NTR',
                'Numans',
                'Nunito',
                'Odor+Mean+Chey',
                'Offside',
                'Old+Standard+TT',
                'Oldenburg',
                'Oleo+Script',
                'Oleo+Script+Swash+Caps',
                'Open+Sans',
                'Open+Sans+Condensed',
                'Oranienbaum',
                'Orbitron',
                'Oregano',
                'Orienta',
                'Original+Surfer',
                'Oswald',
                'Over+the+Rainbow',
                'Overlock',
                'Overlock+SC',
                'Ovo',
                'Oxygen',
                'Oxygen+Mono',
                'Pacifico',
                'Palanquin',
                'Palanquin+Dark',
                'Paprika',
                'Parisienne',
                'Passero+One',
                'Passion+One',
                'Pathway+Gothic+One',
                'Patrick+Hand',
                'Patrick+Hand+SC',
                'Patua+One',
                'Paytone+One',
                'Peddana',
                'Peralta',
                'Permanent+Marker',
                'Petit+Formal+Script',
                'Petrona',
                'Philosopher',
                'Piedra',
                'Pinyon+Script',
                'Pirata+One',
                'Plaster',
                'Play',
                'Playball',
                'Playfair+Display',
                'Playfair+Display+SC',
                'Podkova',
                'Poiret+One',
                'Poller+One',
                'Poly',
                'Pompiere',
                'Pontano+Sans',
                'Poppins',
                'Port+Lligat+Sans',
                'Port+Lligat+Slab',
                'Pragati+Narrow',
                'Prata',
                'Preahvihear',
                'Press+Start+2P',
                'Princess+Sofia',
                'Prociono',
                'Prosto+One',
                'PT+Mono',
                'PT+Sans',
                'PT+Sans+Caption',
                'PT+Sans+Narrow',
                'PT+Serif',
                'PT+Serif+Caption',
                'Puritan',
                'Purple+Purse',
                'Quando',
                'Quantico',
                'Quattrocento',
                'Quattrocento+Sans',
                'Questrial',
                'Quicksand',
                'Quintessential',
                'Qwigley',
                'Racing+Sans+One',
                'Radley',
                'Rajdhani',
                'Raleway',
                'Raleway+Dots',
                'Ramabhadra',
                'Ramaraja',
                'Rambla',
                'Rammetto+One',
                'Ranchers',
                'Rancho',
                'Ranga',
                'Rationale',
                'Ravi+Prakash',
                'Redressed',
                'Reenie+Beanie',
                'Revalia',
                'Rhodium+Libre',
                'Ribeye',
                'Ribeye+Marrow',
                'Righteous',
                'Risque',
                'Roboto',
                'Roboto+Condensed',
                'Roboto+Mono',
                'Roboto+Slab',
                'Rochester',
                'Rock+Salt',
                'Rokkitt',
                'Romanesco',
                'Ropa+Sans',
                'Rosario',
                'Rosarivo',
                'Rouge+Script',
                'Rozha+One',
                'Rubik',
                'Rubik+Mono+One',
                'Rubik+One',
                'Ruda',
                'Rufina',
                'Ruge+Boogie',
                'Ruluko',
                'Rum+Raisin',
                'Ruslan+Display',
                'Russo+One',
                'Ruthie',
                'Rye',
                'Sacramento',
                'Sahitya',
                'Sail',
                'Salsa',
                'Sanchez',
                'Sancreek',
                'Sansita+One',
                'Sarala',
                'Sarina',
                'Sarpanch',
                'Satisfy',
                'Scada',
                'Scheherazade',
                'Schoolbell',
                'Seaweed+Script',
                'Sevillana',
                'Seymour+One',
                'Shadows+Into+Light',
                'Shadows+Into+Light+Two',
                'Shanti',
                'Share',
                'Share+Tech',
                'Share+Tech+Mono',
                'Shojumaru',
                'Short+Stack',
                'Siemreap',
                'Sigmar+One',
                'Signika',
                'Signika+Negative',
                'Simonetta',
                'Sintony',
                'Sirin+Stencil',
                'Six+Caps',
                'Skranji',
                'Slabo+13px',
                'Slabo+27px',
                'Slackey',
                'Smokum',
                'Smythe',
                'Sniglet',
                'Snippet',
                'Snowburst+One',
                'Sofadi+One',
                'Sofia',
                'Sonsie+One',
                'Sorts+Mill+Goudy',
                'Source+Code+Pro',
                'Source+Sans+Pro',
                'Source+Serif+Pro',
                'Special+Elite',
                'Spicy+Rice',
                'Spinnaker',
                'Spirax',
                'Squada+One',
                'Sree+Krushnadevaraya',
                'Stalemate',
                'Stalinist+One',
                'Stardos+Stencil',
                'Stint+Ultra+Condensed',
                'Stint+Ultra+Expanded',
                'Stoke',
                'Strait',
                'Sue+Ellen+Francisco',
                'Sumana',
                'Sunshiney',
                'Supermercado+One',
                'Sura',
                'Suranna',
                'Suravaram',
                'Suwannaphum',
                'Swanky+and+Moo+Moo',
                'Syncopate',
                'Tangerine',
                'Taprom',
                'Tauri',
                'Teko',
                'Telex',
                'Tenali+Ramakrishna',
                'Tenor+Sans',
                'Text+Me+One',
                'The+Girl+Next+Door',
                'Tienne',
                'Tillana',
                'Timmana',
                'Tinos',
                'Titan+One',
                'Titillium+Web',
                'Trade+Winds',
                'Trocchi',
                'Trochut',
                'Trykker',
                'Tulpen+One',
                'Ubuntu',
                'Ubuntu+Condensed',
                'Ubuntu+Mono',
                'Ultra',
                'Uncial+Antiqua',
                'Underdog',
                'Unica+One',
                'UnifrakturCook',
                'UnifrakturMaguntia',
                'Unkempt',
                'Unlock',
                'Unna',
                'Vampiro+One',
                'Varela',
                'Varela+Round',
                'Vast+Shadow',
                'Vesper+Libre',
                'Vibur',
                'Vidaloka',
                'Viga',
                'Voces',
                'Volkhov',
                'Vollkorn',
                'Voltaire',
                'VT323',
                'Waiting+for+the+Sunrise',
                'Wallpoet',
                'Walter+Turncoat',
                'Warnes',
                'Wellfleet',
                'Wendy+One',
                'Wire+One',
                'Work+Sans',
                'Yanone+Kaffeesatz',
                'Yantramanav',
                'Yellowtail',
                'Yeseva+One',
                'Yesteryear',
                'Zeyada'
            );
    $font_select='';
    /*foreach($google_fonts_array as $key=>$value){
        $font_select.='<option value="'.$key.'">'.$value.'</option>';
    }
    */
    foreach($google_fonts_array as $value){
        $font_select.='<option value="'.$value.'">'.str_replace('+',' ',$value).'</option>';
    }
    
    
    $general_font_select='';
    $general_font= esc_html ( get_option('wp_estate_general_font','') );
    if($general_font!='x'){
        $general_font_select='<option value="'.$general_font.'">'.$general_font.'</option>';
    }
    /*
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Main Font','wpestate').'</div>
    <div class="option_row_explain">'.__('Replace theme font with another Google Font from the list below.','wpestate').'</div>    
        <select id="general_font" name="general_font">
            '.$general_font_select.'
            <option value="">- original font -</option>
            '.$font_select.'                   
        </select> 
    </div>';
    

    
    $headings_font_subset   =   esc_html ( get_option('wp_estate_headings_font_subset','') );
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Font subset','wpestate').'</div>
    <div class="option_row_explain">'.__('Specify font subset(s) if you don\'t use latin language.','wpestate').'</div>    
        <input type="text" id="headings_font_subset" name="headings_font_subset" value="'.$headings_font_subset.'">  
    </div>';
    */
    
    
    
    $font_weight_array = array( 
            "normal"=>"Normal",
            "bold"=>"Bold",
            "bolder"=>"Bolder",
            "lighter"=>"Lighter",
            "100"=>"100",
            "200"=>"200",
            "300"=>"300",
            "400"=>"400",
            "500"=>"500",
            "600"=>"600",
            "700"=>"700",
            "800"=>"800",
            "900"=>"900",
            "initial"=> "Initial"    
    );

    $font_weight='';
    foreach($font_weight_array as $key=>$value){
        $font_weight.='<option value="'.$key.'">'.$value.'</option>';
    }
   
    
    
    // H1 Typography
    $h1_fontfamily='';
    $h1_fontfamily= esc_html ( get_option('wp_estate_h1_fontfamily','') );
    if($h1_fontfamily!='x'){
        $h1_fontfamily='<option value="'.$h1_fontfamily.'">'.$h1_fontfamily.'</option>';
    }
    $h1_fontsize =   esc_html( get_option('wp_estate_h1_fontsize') );
    $h1_fontsubset =   esc_html( get_option('wp_estate_h1_fontsubset') );
    $h1_lineheight =   esc_html( get_option('wp_estate_h1_lineheight') ); 
    $h1_fontweight='';
    $h1_fontweight= esc_html ( get_option('wp_estate_h1_fontweight','') );
    if($h1_fontweight!='x'){
        $h1_fontweight='<option value="'.$h1_fontweight.'">'.$h1_fontweight.'</option>';
    }

//var_dump($h1_fontweight);
    // H2 Typography
    $h2_fontfamily='';
    $h2_fontfamily= esc_html ( get_option('wp_estate_h2_fontfamily','') );
    if($h2_fontfamily!='x'){
        $h2_fontfamily='<option value="'.$h2_fontfamily.'">'.$h2_fontfamily.'</option>';
    }
    $h2_fontsize =   esc_html( get_option('wp_estate_h2_fontsize') );
    $h2_fontsubset =   esc_html( get_option('wp_estate_h2_fontsubset') );
    $h2_lineheight =   esc_html( get_option('wp_estate_h2_lineheight')) ;
    $h2_fontweight='';
    $h2_fontweight= esc_html ( get_option('wp_estate_h2_fontweight','') );
    if($h2_fontweight!='x'){
        $h2_fontweight='<option value="'.$h2_fontweight.'">'.$h2_fontweight.'</option>';
    }

    // H3 Typography
    $h3_fontfamily='';
    $h3_fontfamily= esc_html ( get_option('wp_estate_h3_fontfamily','') );
    if($h3_fontfamily!='x'){
        $h3_fontfamily='<option value="'.$h3_fontfamily.'">'.$h3_fontfamily.'</option>';
    }
    $h3_fontsize =   esc_html( get_option('wp_estate_h3_fontsize') );
    $h3_fontsubset =   esc_html( get_option('wp_estate_h3_fontsubset') );
    $h3_lineheight =   esc_html( get_option('wp_estate_h3_lineheight') );
    $h3_fontweight='';
    $h3_fontweight= esc_html ( get_option('wp_estate_h3_fontweight','') );
    if($h3_fontweight!='x'){
        $h3_fontweight='<option value="'.$h3_fontweight.'">'.$h3_fontweight.'</option>';
    }

    // H4 Typography
    $h4_fontfamily='';
    $h4_fontfamily= esc_html ( get_option('wp_estate_h4_fontfamily','') );
    if($h4_fontfamily!='x'){
        $h4_fontfamily='<option value="'.$h4_fontfamily.'">'.$h4_fontfamily.'</option>';
    }
    $h4_fontsize =   esc_html( get_option('wp_estate_h4_fontsize') );
    $h4_fontsubset =   esc_html( get_option('wp_estate_h4_fontsubset') );
    $h4_lineheight =   esc_html( get_option('wp_estate_h4_lineheight') );
    $h4_fontweight='';
    $h4_fontweight= esc_html ( get_option('wp_estate_h4_fontweight','') );
    if($h4_fontweight!='x'){
        $h4_fontweight='<option value="'.$h4_fontweight.'">'.$h4_fontweight.'</option>';
    }


    // H5 Typography
    $h5_fontfamily='';
    $h5_fontfamily= esc_html ( get_option('wp_estate_h5_fontfamily','') );
    if($h5_fontfamily!='x'){
        $h5_fontfamily='<option value="'.$h5_fontfamily.'">'.$h5_fontfamily.'</option>';
    }
    $h5_fontsize =   esc_html( get_option('wp_estate_h5_fontsize') );
    $h5_fontsubset =   esc_html( get_option('wp_estate_h5_fontsubset') );
    $h5_lineheight =   esc_html( get_option('wp_estate_h5_lineheight') );
    $h5_fontweight='';
    $h5_fontweight= esc_html ( get_option('wp_estate_h5_fontweight','') );
    if($h5_fontweight!='x'){
        $h5_fontweight='<option value="'.$h5_fontweight.'">'.$h5_fontweight.'</option>';
    }

    // H6 Typography
    $h6_fontfamily='';
    $h6_fontfamily= esc_html ( get_option('wp_estate_h6_fontfamily','') );
    if($h6_fontfamily!='x'){
        $h6_fontfamily='<option value="'.$h6_fontfamily.'">'.$h6_fontfamily.'</option>';
    }
    $h6_fontsize =   esc_html( get_option('wp_estate_h6_fontsize') );
    $h6_fontsubset =   esc_html( get_option('wp_estate_h6_fontsubset') );
    $h6_lineheight =   esc_html( get_option('wp_estate_h6_lineheight') );
    $h6_fontweight='';
    $h6_fontweight= esc_html ( get_option('wp_estate_h6_fontweight','') );
    if($h6_fontweight!='x'){
        $h6_fontweight='<option value="'.$h6_fontweight.'">'.$h6_fontweight.'</option>';
    }

    // H6 Typography
    $p_fontfamily='';
    $p_fontfamily= esc_html ( get_option('wp_estate_p_fontfamily','') );
    if($p_fontfamily!='x'){
        $p_fontfamily='<option value="'.$p_fontfamily.'">'.$p_fontfamily.'</option>';
    }
    $p_fontsize =   esc_html( get_option('wp_estate_p_fontsize') );
    $p_fontsubset =   esc_html( get_option('wp_estate_p_fontsubset') );
    $p_lineheight =   esc_html( get_option('wp_estate_p_lineheight') );
    $p_fontweight='';
    $p_fontweight= esc_html ( get_option('wp_estate_p_fontweight','') );
    if($p_fontweight!='x'){
        $p_fontweight='<option value="'.$p_fontweight.'">'.$p_fontweight.'</option>';
    }

    // Menu Typography
    $menu_fontfamily='';
    $menu_fontfamily= esc_html ( get_option('wp_estate_menu_fontfamily','') );
    if($menu_fontfamily!='x'){
        $menu_fontfamily='<option value="'.$menu_fontfamily.'">'.$menu_fontfamily.'</option>';
    }
    $menu_fontsize =   esc_html( get_option('wp_estate_menu_fontsize') );
    $menu_fontsubset =   esc_html( get_option('wp_estate_menu_fontsubset') );
    $menu_lineheight =   esc_html( get_option('wp_estate_menu_lineheight') );
    $menu_fontweight='';
    $menu_fontweight= esc_html ( get_option('wp_estate_menu_fontweight','') );
    if($menu_fontweight!='x'){
        $menu_fontweight='<option value="'.$menu_fontweight.'">'.$menu_fontweight.'</option>';
    }

     // sidebar Typography
    $sidebar_fontfamily='';
    $sidebar_fontfamily= esc_html ( get_option('wp_estate_sidebar_fontfamily','') );
    if($sidebar_fontfamily!='x'){
        $sidebar_fontfamily='<option value="'.$sidebar_fontfamily.'">'.$sidebar_fontfamily.'</option>';
    }
    $sidebar_fontsize =   esc_html( get_option('wp_estate_sidebar_fontsize') );
    $sidebar_fontsubset =   esc_html( get_option('wp_estate_sidebar_fontsubset') );
    $sidebar_lineheight =   esc_html( get_option('wp_estate_sidebar_lineheight') );
    $sidebar_fontweight='';
    $sidebar_fontweight= esc_html ( get_option('wp_estate_sidebar_fontweight','') );
    if($sidebar_fontweight!='x'){
        $sidebar_fontweight='<option value="'.$sidebar_fontweight.'">'.$sidebar_fontweight.'</option>';
    }


     // footer Typography
    $footer_fontfamily='';
    $footer_fontfamily= esc_html ( get_option('wp_estate_footer_fontfamily','') );
    if($footer_fontfamily!='x'){
        $footer_fontfamily='<option value="'.$footer_fontfamily.'">'.$footer_fontfamily.'</option>';
    }
    $footer_fontsize =   esc_html( get_option('wp_estate_footer_fontsize') );
    $footer_fontsubset =   esc_html( get_option('wp_estate_footer_fontsubset') );
    $footer_lineheight =   esc_html( get_option('wp_estate_footer_lineheight') );
    $footer_fontweight='';
    $footer_fontweight= esc_html ( get_option('wp_estate_footer_fontweight','') );
    if($footer_fontweight!='x'){
        $footer_fontweight='<option value="'.$footer_fontweight.'">'.$footer_fontweight.'</option>';
    }



    print'<div class="estate_option_row">
    <table>
    <th style="text-align:left;"><div class="label_option_row">'.__('H1 Font','wpestate').'</div><th>
    <tr><td><div class="option_row_explain">'.__('Font Family:','wpestate').'</div>    
        <select id="h1_fontfamily" name="h1_fontfamily">
            '.$h1_fontfamily.'
            <option value="">- original font -</option>
            '.$font_select.'                   
        </select> </td>
        <td>
        <div class="option_row_explain">'.__('Font Subset:','wpestate').'</div>    
             <input type="text" id="h1_fontsubset" name="h1_fontsubset" value="'.$h1_fontsubset.'">
        </td>
        <td>
    <div class="option_row_explain">'.__('Font Size:','wpestate').'</div>    
         <input type="number" id="h1_fontsize" name="h1_fontsize" value="'.$h1_fontsize.'" placeholder="in px"></td>
    </tr>
    <tr>
        <td>
        <div class="option_row_explain">'.__('Line Height:','wpestate').'</div>    
             <input type="number" id="h1_lineheight" name="h1_lineheight" value="'.$h1_lineheight.'" placeholder="in px">
        </td>
        <td><div class="option_row_explain">'.__('Font Weight:','wpestate').'</div>    
        <select id="h1_fontweight" name="h1_fontweight">
            '.$h1_fontweight.'
            <option value="">Original font weight</option>
            '.$font_weight.'                   
        </select> </td>
    </tr>
</table>
</div>
<div class="estate_option_row">
<table>
    <th style="text-align:left;"><div class="label_option_row">'.__('H2 Font','wpestate').'</div><th>
    <tr><td><div class="option_row_explain">'.__('Font Family:','wpestate').'</div>    
        <select id="h2_fontfamily" name="h2_fontfamily">
            '.$h2_fontfamily.'
            <option value="">- original font -</option>
            '.$font_select.'                   
        </select> </td>
        <td>
        <div class="option_row_explain">'.__('Font Subset:','wpestate').'</div>    
             <input type="text" id="h2_fontsubset" name="h2_fontsubset" value="'.$h2_fontsubset.'">
        </td>
    <td>
    <div class="option_row_explain">'.__('Font Size:','wpestate').'</div>    
         <input type="number" id="h2_fontsize" name="h2_fontsize" value="'.$h2_fontsize.'" placeholder="in px"></td>
    </tr>
    <tr><td>
    <div class="option_row_explain">'.__('Line Height:','wpestate').'</div>    
         <input type="number" id="h2_lineheight" name="h2_lineheight" value="'.$h2_lineheight.'" placeholder="in px"></td>
         <td><div class="option_row_explain">'.__('Font Weight:','wpestate').'</div>    
        <select id="h2_fontweight" name="h2_fontweight">
            '.$h2_fontweight.'
            <option value="">Original font weight</option>
            '.$font_weight.'                   
        </select> </td>
    </tr>
</table>
</div>
<div class="estate_option_row">
<table>
    <th style="text-align:left;"><div class="label_option_row">'.__('H3 Font','wpestate').'</div><th>
    <tr><td><div class="option_row_explain">'.__('Font Family:','wpestate').'</div>    
        <select id="h3_fontfamily" name="h3_fontfamily">
            '.$h3_fontfamily.'
            <option value="">- original font -</option>
            '.$font_select.'                   
        </select> </td>
<td>
        <div class="option_row_explain">'.__('Font Subset:','wpestate').'</div>    
             <input type="text" id="h3_fontsubset" name="h3_fontsubset" value="'.$h3_fontsubset.'">
        </td>
    <td>
    <div class="option_row_explain">'.__('Font Size:','wpestate').'</div>    
         <input type="number" id="h3_fontsize" name="h3_fontsize" value="'.$h3_fontsize.'" placeholder="in px"></td>
    </tr><td>
    <div class="option_row_explain">'.__('Line Height:','wpestate').'</div>    
         <input type="number" id="h3_lineheight" name="h3_lineheight" value="'.$h3_lineheight.'" placeholder="in px"></td>
         </td>
        <td><div class="option_row_explain">'.__('Font Weight:','wpestate').'</div>    
        <select id="h3_fontweight" name="h3_fontweight">
            '.$h3_fontweight.'
            <option value="">Original font weight</option>
            '.$font_weight.'                   
        </select> </td>
    <tr>
</table>
</div>
<div class="estate_option_row">
<table>
   <th style="text-align:left;"><div class="label_option_row">'.__('H4 Font','wpestate').'</div><th>
    <tr><td><div class="option_row_explain">'.__('Font Family:','wpestate').'</div>    
        <select id="h4_fontfamily" name="h4_fontfamily">
            '.$h4_fontfamily.'
            <option value="">- original font -</option>
            '.$font_select.'                   
        </select> </td>
        <td>
        <div class="option_row_explain">'.__('Font Subset:','wpestate').'</div>    
             <input type="text" id="h4_fontsubset" name="h4_fontsubset" value="'.$h4_fontsubset.'">
        </td>
    <td>
    <div class="option_row_explain">'.__('Font Size:','wpestate').'</div>    
         <input type="number" id="h4_fontsize" name="h4_fontsize" value="'.$h4_fontsize.'" placeholder="in px"></td>
         </tr><tr><td>
    <div class="option_row_explain">'.__('Line Height:','wpestate').'</div>    
         <input type="number" id="h4_lineheight" name="h4_lineheight" value="'.$h4_lineheight.'" placeholder="in px"></td>
        <td><div class="option_row_explain">'.__('Font Weight:','wpestate').'</div>    
        <select id="h4_fontweight" name="h4_fontweight">
            '.$h4_fontweight.'
            <option value="">Original font weight</option>
            '.$font_weight.'                   
        </select> </td> 
    </tr>
</table>
</div>
<div class="estate_option_row">
<table>
    <th style="text-align:left;"><div class="label_option_row">'.__('H5 Font','wpestate').'</div><th>
    <tr><td><div class="option_row_explain">'.__('Font Family:','wpestate').'</div>    
        <select id="h5_fontfamily" name="h5_fontfamily">
            '.$h5_fontfamily.'
            <option value="">- original font -</option>
            '.$font_select.'                   
        </select> </td>
        <td>
        <div class="option_row_explain">'.__('Font Subset:','wpestate').'</div>    
             <input type="text" id="h5_fontsubset" name="h5_fontsubset" value="'.$h5_fontsubset.'">
        </td>
    <td>
    <div class="option_row_explain">'.__('Font Size:','wpestate').'</div>    
         <input type="number" id="h5_fontsize" name="h5_fontsize" value="'.$h5_fontsize.'" placeholder="in px"></td>
         </tr><tr><td>
    <div class="option_row_explain">'.__('Line Height:','wpestate').'</div>    
         <input type="number" id="h5_lineheight" name="h5_lineheight" value="'.$h5_lineheight.'" placeholder="in px"></td>
        <td><div class="option_row_explain">'.__('Font Weight:','wpestate').'</div>    
        <select id="h5_fontweight" name="h5_fontweight">
            '.$h5_fontweight.'
            <option value="">Original font weight</option>
            '.$font_weight.'                   
        </select> </td>
    </tr>
</table>
</div>
<div class="estate_option_row">
<table>
    <th style="text-align:left;"><div class="label_option_row">'.__('H6 Font','wpestate').'</div><th>
    <tr><td><div class="option_row_explain">'.__('Font Family:','wpestate').'</div>    
        <select id="h6_fontfamily" name="h6_fontfamily">
            '.$h6_fontfamily.'
            <option value="">- original font -</option>
            '.$font_select.'                   
        </select> </td>
        <td>
        <div class="option_row_explain">'.__('Font Subset:','wpestate').'</div>    
             <input type="text" id="h6_fontsubset" name="h6_fontsubset" value="'.$h6_fontsubset.'">
        </td>
    <td>
    <div class="option_row_explain">'.__('Font Size:','wpestate').'</div>    
         <input type="number" id="h6_fontsize" name="h6_fontsize" value="'.$h6_fontsize.'" placeholder="in px"></td>
         </tr><tr><td>
    <div class="option_row_explain">'.__('Line Height:','wpestate').'</div>    
         <input type="number" id="h6_lineheight" name="h6_lineheight" value="'.$h6_lineheight.'" placeholder="in px"></td>
         <td><div class="option_row_explain">'.__('Font Weight:','wpestate').'</div>    
        <select id="h6_fontweight" name="h6_fontweight">
            '.$h6_fontweight.'
            <option value="">Original font weight</option>
            '.$font_weight.'                   
        </select> </td>
    </tr>
</table>
</div>
<div class="estate_option_row">
<table>
    <th style="text-align:left;"><div class="label_option_row">'.__('Paragraph Font','wpestate').'</div><th>
    <tr><td><div class="option_row_explain">'.__('Font Family:','wpestate').'</div>    
        <select id="p_fontfamily" name="p_fontfamily">
            '.$p_fontfamily.'
            <option value="">- original font -</option>
            '.$font_select.'                   
        </select> </td>
        <td>
        <div class="option_row_explain">'.__('Font Subset:','wpestate').'</div>    
             <input type="text" id="p_fontsubset" name="p_fontsubset" value="'.$p_fontsubset.'">
        </td>
        <td>
    <div class="option_row_explain">'.__('Font Size:','wpestate').'</div>    
         <input type="number" id="p_fontsize" name="p_fontsize" value="'.$p_fontsize.'" placeholder="in px"></td>
    </tr>
    <tr>
        <td>
        <div class="option_row_explain">'.__('Line Height:','wpestate').'</div>    
             <input type="number" id="p_lineheight" name="p_lineheight" value="'.$p_lineheight.'" placeholder="in px">
        </td>
        <td><div class="option_row_explain">'.__('Font Weight:','wpestate').'</div>    
        <select id="p_fontweight" name="p_fontweight">
            '.$p_fontweight.'
            <option value="">Original font weight</option>
            '.$font_weight.'                   
        </select> </td>
    </tr>
</table>
</div>
<div class="estate_option_row">
<table>
    <th style="text-align:left;"><div class="label_option_row">'.__('Menu Font','wpestate').'</div><th>
    <tr><td><div class="option_row_explain">'.__('Font Family:','wpestate').'</div>    
        <select id="menu_fontfamily" name="menu_fontfamily">
            '.$menu_fontfamily.'
            <option value="">- original font -</option>
            '.$font_select.'                   
        </select> </td>
        <td>
        <div class="option_row_explain">'.__('Font Subset:','wpestate').'</div>    
             <input type="text" id="menu_fontsubset" name="menu_fontsubset" value="'.$menu_fontsubset.'">
        </td>
        <td>
    <div class="option_row_explain">'.__('Font Size:','wpestate').'</div>    
         <input type="number" id="menu_fontsize" name="menu_fontsize" value="'.$menu_fontsize.'" placeholder="in px"></td>
    </tr>
    <tr>
        <td>
        <div class="option_row_explain">'.__('Line Height:','wpestate').'</div>    
             <input type="number" id="menu_lineheight" name="menu_lineheight" value="'.$menu_lineheight.'" placeholder="in px">
        </td>
        <td><div class="option_row_explain">'.__('Font Weight:','wpestate').'</div>    
        <select id="menu_fontweight" name="menu_fontweight">
            '.$menu_fontweight.'
            <option value="">Original font weight</option>
            '.$font_weight.'                   
        </select> </td>
    </tr>
</table>
</div>';
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
     print ' <div class="estate_option_row_submit">
    <input type="submit" name="submit"  class="new_admin_submit " value="'.__('Save Changes','wpestate').'" />
    </div>';  
}
endif;











if( !function_exists('new_wpestate_membership_settings') ):    
function new_wpestate_membership_settings(){
    $free_mem_list                  =   esc_html( get_option('wp_estate_free_mem_list','') );
    $cache_array                    =   array('yes','no');  
   
    $paypal_array                   =   array('no','per listing','membership');
    $paid_submission_symbol         =   wpestate_dropdowns_theme_admin($paypal_array,'paid_submission');
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Enable Paid Submission ?','wpestate').'</div>
    <div class="option_row_explain">'.__('No = submission is free. Paid listing = submission requires user to pay a fee for each listing. Membership = submission is based on user membership package.','wpestate').'</div>    
        <select id="paid_submission" name="paid_submission">
            '.$paid_submission_symbol.'
        </select>
    </div>';
    
    $paypal_array                   =   array( 'sandbox','live' );
    $paypal_api_select              =   wpestate_dropdowns_theme_admin($paypal_array,'paypal_api');  
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Paypal & Stripe Api ','wpestate').'</div>
    <div class="option_row_explain">'.__('Sandbox = test API. LIVE = real payments API. Update PayPal and Stripe settings according to API type selection.','wpestate').'</div>    
        <select id="paypal_api" name="paypal_api">
            '.$paypal_api_select.'
        </select>
    </div>'; 
    
    $admin_submission_symbol        =   wpestate_dropdowns_theme_admin($cache_array,'admin_submission');
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Submited Listings should be approved by admin?','wpestate').'</div>
    <div class="option_row_explain">'.__('If yes, admin publishes each property submitted in front end manually.','wpestate').'</div>    
        <select id="admin_submission" name="admin_submission">
            '.$admin_submission_symbol.'
        </select>
    </div>';
  
    $user_agent_symbol              =   wpestate_dropdowns_theme_admin($cache_array,'user_agent');
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Front end registred users should be saved as agents?','wpestate').'</div>
    <div class="option_row_explain">'.__('If yes, new registered users will have an agent profile synced with user profile automatically. Applies only for front end registation.','wpestate').'</div>    
        <select id="user_agent" name="user_agent">
            '.$user_agent_symbol.'
        </select>
    </div>';
    



        
       
    $submission_curency_array       =   array(get_option('wp_estate_submission_curency_custom',''),'USD','EUR','AUD','BRL','CAD','CZK','DKK','HKD','HUF','ILS','INR','JPY','MYR','MXN','NOK','NZD','PHP','PLN','GBP','SGD','SEK','CHF','TWD','THB','TRY');
    $submission_curency_symbol      =   wpestate_dropdowns_theme_admin($submission_curency_array,'submission_curency');
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Currency For Paid Submission','wpestate').'</div>
    <div class="option_row_explain">'.__('The currency in which payments are processed.','wpestate').'</div>    
        <select id="submission_curency" name="submission_curency">
            '.$submission_curency_symbol.'
        </select> 
    </div>'; 
       
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Custom Currency Symbol - *select it from the list above after you add it.','wpestate').'</div>
    <div class="option_row_explain">'.__('Add your own currency for Wire payments. ','wpestate').'</div>    
        <input type="text" id="submission_curency_custom" name="submission_curency_custom" class="regular-text"  value="'.get_option('wp_estate_submission_curency_custom','').'"/>
    </div>'; 
      

      
   
      
   
      
    $enable_direct_pay_symbol       =   wpestate_dropdowns_theme_admin($cache_array,'enable_direct_pay');
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Enable Direct Payment / Wire Payment?','wpestate').'</div>
    <div class="option_row_explain">'.__('Enable or disable the wire payment option.','wpestate').'</div>    
        <select id="enable_direct_pay" name="enable_direct_pay">
            '.$enable_direct_pay_symbol.'
        </select>
    </div>'; 
    
    
    $args=array(
        'a' => array(
            'href' => array(),
            'title' => array()
        ),
        'br' => array(),
        'em' => array(),
        'strong' => array(),
    );
    $direct_payment_details         =   wp_kses( get_option('wp_estate_direct_payment_details','') ,$args);
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Wire instructions for direct payment','wpestate').'</div>
    <div class="option_row_explain">'.__('If wire payment is enabled, type the instructions below.','wpestate').'</div>    
        <textarea id="direct_payment_details" rows="5" style="width:700px;" name="direct_payment_details"   class="regular-text" >'.$direct_payment_details.'</textarea> 
    </div>';   
    
    $price_submission               =   floatval( get_option('wp_estate_price_submission','') );   
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Price Per Submission (for "per listing" mode)','wpestate').'</div>
    <div class="option_row_explain">'.__('Use .00 format for decimals (ex: 5.50). Do not set price as 0!','wpestate').'</div>    
       <input  type="text" id="price_submission" name="price_submission"  value="'.$price_submission.'"/> 
    </div>'; 
      
    
    $price_featured_submission      =   floatval( get_option('wp_estate_price_featured_submission','') ); 
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Price to make the listing featured (for "per listing" mode)','wpestate').'</div>
    <div class="option_row_explain">'.__('Use .00 format for decimals (ex: 1.50). Do not set price as 0!','wpestate').'</div>    
       <input  type="text" id="price_featured_submission" name="price_featured_submission"  value="'.$price_featured_submission.'"/>
    </div>'; 
       
    
    $free_mem_list_unl='';
    if ( intval( get_option('wp_estate_free_mem_list_unl', '' ) ) == 1){
        $free_mem_list_unl=' checked="checked" ';  
    }
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Free Membership - no of listings (for "membership" mode)','wpestate').'</div>
    <div class="option_row_explain">'.__('If you change this value, the new value applies for new registered users. Old value applies for older registered accounts.','wpestate').'</div>    
        <input  type="text" id="free_mem_list" name="free_mem_list" style="margin-right:20px;"  value="'.$free_mem_list.'"/> 
        <input type="hidden" name="free_mem_list_unl" value="">
        <input type="checkbox"  id="free_mem_list_unl" name="free_mem_list_unl" value="1" '.$free_mem_list_unl.' />
        <label for="free_mem_list_unl">'.__('Unlimited listings ?','wpestate').'</label>
    </div>'; 
     
    $free_feat_list                 =   esc_html( get_option('wp_estate_free_feat_list','') );
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Free Membership - no of featured listings (for "membership" mode)','wpestate').'</div>
    <div class="option_row_explain">'.__('If you change this value, the new value applies for new registered users. Old value applies for older registered accounts.','wpestate').'</div>    
         <input  type="text" id="free_feat_list" name="free_feat_list" style="margin-right:20px;"    value="'.$free_feat_list.'"/>
    </div>';
        
    $free_feat_list_expiration= intval ( get_option('wp_estate_free_feat_list_expiration','') );
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Free Membership Listings - no of days until a free listing will expire. *Starts from the moment the property is published on the website. (for "membership" mode) ','wpestate').'</div>
    <div class="option_row_explain">'.__('Option applies for each free published listing.','wpestate').'</div>    
        <input  type="text" id="free_feat_list_expiration" name="free_feat_list_expiration" style="margin-right:20px;"    value="'.$free_feat_list_expiration.'"/>
    </div>';
  
    print ' <div class="estate_option_row_submit">
    <input type="submit" name="submit"  class="new_admin_submit " value="'.__('Save Changes','wpestate').'" />
    </div>';  
     
}    
endif;



if( !function_exists('new_wpestate_stripe_settings') ):  
 function  new_wpestate_stripe_settings(){
    $cache_array                    =   array('yes','no');  
      
    $enable_stripe_symbol           =   wpestate_dropdowns_theme_admin($cache_array,'enable_stripe');
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Enable Stripe?','wpestate').'</div>
    <div class="option_row_explain">'.__('You can enable or disable Stripe payment buttons.','wpestate').'</div>    
        <select id="enable_stripe" name="enable_stripe">
            '.$enable_stripe_symbol.'
        </select>
    </div>';
    
    $stripe_secret_key              =   esc_html( get_option('wp_estate_stripe_secret_key','') );
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Stripe Secret Key','wpestate').'</div>
    <div class="option_row_explain">'.__('Info is taken from your account at https://dashboard.stripe.com/login','wpestate').'</div>    
       <input  type="text" id="stripe_secret_key" name="stripe_secret_key"  class="regular-text" value="'.$stripe_secret_key.'"/> 
    </div>';
        
    $stripe_publishable_key         =   esc_html( get_option('wp_estate_stripe_publishable_key','') );
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Stripe Publishable Key','wpestate').'</div>
    <div class="option_row_explain">'.__('Info is taken from your account at https://dashboard.stripe.com/login','wpestate').'</div>    
       <input  type="text" id="stripe_publishable_key" name="stripe_publishable_key" class="regular-text" value="'.$stripe_publishable_key.'"/>
    </div>';
    
    print ' <div class="estate_option_row_submit">
    <input type="submit" name="submit"  class="new_admin_submit " value="'.__('Save Changes','wpestate').'" />
    </div>';  
}
endif;
 
 
 
if( !function_exists('new_wpestate_paypal_settings') ):   
function     new_wpestate_paypal_settings(){
      $cache_array                    =   array('yes','no');  
    $enable_paypal_symbol           =   wpestate_dropdowns_theme_admin($cache_array,'enable_paypal');
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Enable Paypal?','wpestate').'</div>
    <div class="option_row_explain">'.__('You can enable or disable PayPal buttons.','wpestate').'</div>    
        <select id="enable_paypal" name="enable_paypal">
            '.$enable_paypal_symbol.'
        </select>
    </div>';
    
    $paypal_client_id               =   esc_html( get_option('wp_estate_paypal_client_id','') );
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Paypal Client id','wpestate').'</div>
    <div class="option_row_explain">'.__('PayPal business account is required. Info is taken from https://developer.paypal.com/. See help.','wpestate').'</div>    
        <input  type="text" id="paypal_client_id" name="paypal_client_id" class="regular-text"  value="'.$paypal_client_id.'"/>
    </div>'; 
     
    $paypal_client_secret           =   esc_html( get_option('wp_estate_paypal_client_secret','') );
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Paypal Client Secret Key','wpestate').'</div>
    <div class="option_row_explain">'.__('Info is taken from https://developer.paypal.com/ See help.','wpestate').'</div>    
        <input  type="text" id="paypal_client_secret" name="paypal_client_secret"  class="regular-text" value="'.$paypal_client_secret.'"/> 
    </div>'; 
    
     $paypal_api_username            =   esc_html( get_option('wp_estate_paypal_api_username','') );
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Paypal Api User Name','wpestate').'</div>
    <div class="option_row_explain">'.__('Info is taken from https://www.paypal.com/ or http://sandbox.paypal.com/ See help.','wpestate').'</div>    
       <input  type="text" id="paypal_api_username" name="paypal_api_username"  class="regular-text" value="'.$paypal_api_username.'"/>
    </div>'; 
    
    $paypal_api_password            =   esc_html( get_option('wp_estate_paypal_api_password','') );
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Paypal API Password','wpestate').'</div>
    <div class="option_row_explain">'.__('Info is taken from https://www.paypal.com/ or http://sandbox.paypal.com/ See help.','wpestate').'</div>    
       <input  type="text" id="paypal_api_password" name="paypal_api_password"  class="regular-text" value="'.$paypal_api_password.'"/>
    </div>'; 
        
    $paypal_api_signature           =   esc_html( get_option('wp_estate_paypal_api_signature','') );
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Paypal API Signature','wpestate').'</div>
    <div class="option_row_explain">'.__('Info is taken from https://www.paypal.com/ or http://sandbox.paypal.com/ See help.','wpestate').'</div>    
       <input  type="text" id="paypal_api_signature" name="paypal_api_signature"  class="regular-text" value="'.$paypal_api_signature.'"/>
    </div>';
        
    $paypal_rec_email               =   esc_html( get_option('wp_estate_paypal_rec_email','') );
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Paypal receiving email','wpestate').'</div>
    <div class="option_row_explain">'.__('Info is taken from https://www.paypal.com/ or http://sandbox.paypal.com/ See help.','wpestate').'</div>    
       <input  type="text" id="paypal_rec_email" name="paypal_rec_email"  class="regular-text" value="'.$paypal_rec_email.'"/>
    </div>';
    
    print ' <div class="estate_option_row_submit">
    <input type="submit" name="submit"  class="new_admin_submit " value="'.__('Save Changes','wpestate').'" />
    </div>';  
}
endif; 
   
   
   
   
   
   
   
if( !function_exists('new_wpestate_pin_management') ):   
function new_wpestate_pin_management(){  
    $pins       =   array();
    $taxonomy   =   'property_action_category';
    $tax_terms  =   get_terms($taxonomy,'hide_empty=0');

    $taxonomy_cat = 'property_category';
    $categories = get_terms($taxonomy_cat,'hide_empty=0');

    // add only actions
    foreach ($tax_terms as $tax_term) {
        $name                    =  sanitize_key ( wpestate_limit64('wp_estate_'.$tax_term->slug) );
        $limit54                 =  sanitize_key ( wpestate_limit54($tax_term->slug) );
        $pins[$limit54]          =  esc_html( get_option($name) );  
    } 

    // add only categories
    foreach ($categories as $categ) {
        $name                           =   sanitize_key( wpestate_limit64('wp_estate_'.$categ->slug));
        $limit54                        =   sanitize_key(wpestate_limit54($categ->slug));
        $pins[$limit54]                 =   esc_html( get_option($name) );
    }
    
    // add combinations
    foreach ($tax_terms as $tax_term) {
        foreach ($categories as $categ) {
            $limit54            =   sanitize_key ( wpestate_limit27($categ->slug).wpestate_limit27($tax_term->slug) );
            $name               =   'wp_estate_'.$limit54;
            $pins[$limit54]     =   esc_html( get_option($name) ) ;        
        }
    }

  
    $name='wp_estate_idxpin';
    $pins['idxpin']=esc_html( get_option($name) );  

    $name='wp_estate_userpin';
    $pins['userpin']=esc_html( get_option($name) );  
   
    $taxonomy = 'property_action_category';
    $tax_terms = get_terms($taxonomy,'hide_empty=0');

    $taxonomy_cat = 'property_category';
    $categories = get_terms($taxonomy_cat,'hide_empty=0');

    print'<p class="admin-exp">'.__('Add new Google Maps pins for single actions / single categories. For speed reason, you MUST add pins if you change categories and actions names.','wpestate').'</p>';
    print '<p class="admin-exp" >'.__('If you add images directly into the input fields (without Upload button) please use the full image path. For ex: http://www.wpresidence..... . If you use the "upload"  button use also "Insert into Post" button from the pop up window.','wpestate');
    print '<p class="admin-exp" >'.__('Pins retina version must be uploaded at the same time (same folder) as the original pin and the name of the retina file should be with_2x at the end.','wpestate').' <a href="http://help.wpresidence.net/2015/10/29/retina-pin-images/" target="_blank">'.__('For help go here!','wpestate').'</a>';
      
   
    foreach ($tax_terms as $tax_term) { 
            $limit54   =  $post_name  =   sanitize_key(wpestate_limit54($tax_term->slug));
            print'<div class="estate_option_row">
            <div class="label_option_row">'.__('For action ','wpestate').'<strong>'.$tax_term->name.' </strong></div>
            <div class="option_row_explain">'.__('Image size must be 44px x 48px. ','wpestate').'</div>    
                <input type="text"    class="pin-upload-form" size="36" name="'.$post_name.'" value="'.$pins[$limit54].'" />
                <input type="button"  class="upload_button button pin-upload" value="'.__('Upload Pin','wpestate').'" />
            </div>';
            
               
    }
     
    
    foreach ($categories as $categ) {  
            $limit54   =   $post_name  =   sanitize_key(wpestate_limit54($categ->slug));
            print'<div class="estate_option_row">
            <div class="label_option_row">'.__('For category: ','wpestate').'<strong>'.$categ->name.' </strong>  </div>
            <div class="option_row_explain">'.__('Image size must be 44px x 48px. ','wpestate').'</div>    
                <input type="text"    class="pin-upload-form" size="36" name="'.$post_name.'" value="'.$pins[$limit54].'" />
                <input type="button"  class="upload_button button pin-upload" value="'.__('Upload Pin','wpestate').'"  />
            </div>';
                 
    }
    
    
    print '<p class="admin-exp">'.__('Add new Google Maps pins for actions & categories combined (example: \'apartments in sales\')','wpestate').'</p>';  
      
    foreach ($tax_terms as $tax_term) {
    
        foreach ($categories as $categ) {
            $limit54=sanitize_key(wpestate_limit27($categ->slug)).sanitize_key( wpestate_limit27($tax_term->slug) );
            
            print'<div class="estate_option_row">
            <div class="label_option_row">'.__('For action','wpestate').' <strong>'.$tax_term->name.'</strong>, '.__('category','wpestate').': <strong>'.$categ->name.'</strong>   </div>
            <div class="option_row_explain">'.__('Image size must be 44px x 48px.','wpestate').'  </div>    
                <input id="'.$limit54.'" type="text" size="36" name="'. $limit54.'" value="'.$pins[$limit54].'" />
                <input type="button"  class="upload_button button pin-upload" value="'.__('Upload Pin','wpestate').'" />
            </div>';
                
        }
    }


    print'<div class="estate_option_row">
            <div class="label_option_row">'.__('For IDX (if plugin is enabled) ','wpestate').'</div>
            <div class="option_row_explain">'.__('For IDX (if plugin is enabled) ','wpestate').'</div>    
                <input id="idxpin" type="text" size="36" name="idxpin" value="'.$pins['idxpin'].'" />
                <input type="button"  class="upload_button button pin-upload" value="'.__('Upload Pin','wpestate').'" />
            </div>';
    
    
     print'<div class="estate_option_row">
            <div class="label_option_row">'.__('Userpin in geolocation','wpestate').'</div>
            <div class="option_row_explain">'.__('Userpin in geolocation','wpestate').'</div>    
                <input id="userpin" type="text" size="36" name="userpin" value="'.$pins['userpin'].'" />
                <input type="button"  class="upload_button button pin-upload" value="'.__('Upload Pin','wpestate').'" />
            </div>';
     
     print ' <div class="estate_option_row_submit">
    <input type="submit" name="submit"  class="new_admin_submit " value="'.__('Save Changes','wpestate').'" />
    </div>'; 
}
endif;



if( !function_exists('new_wpestate_advanced_search_form') ):   
function    new_wpestate_advanced_search_form(){
       
    $value_array=array('no','yes');
    $custom_advanced_search_select = wpestate_dropdowns_theme_admin($value_array,'custom_advanced_search');
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Use Custom Fields For Advanced Search ?','wpestate').'</div>
    <div class="option_row_explain">'.__('If yes, you can set your own custom fields in the  spots available. See help for correct setup.','wpestate').'</div>    
        <select id="custom_advanced_search" name="custom_advanced_search">
            '.$custom_advanced_search_select.'
        </select> 
    </div>';
  
    $adv_search_fields_no             =    ( floatval( get_option('wp_estate_adv_search_fields_no') ) );      
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('No of Search fields','wpestate').'</div>
    <div class="option_row_explain">'.__('No of Search fields.','wpestate').'</div>    
        <input  type="text" id="adv_search_fields_no"  name="adv_search_fields_no"   value="'.$adv_search_fields_no.'" size="40"/>
    </div>';
    
    $adv_search_fields_no_per_row             =    ( floatval( get_option('wp_estate_search_fields_no_per_row') ) );      
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('No of Search fields per row','wpestate').'</div>
    <div class="option_row_explain">'.__('No of Search fields per row (Possible values: 1,2,3,4).','wpestate').'</div>    
        <input  type="text" id="search_fields_no_per_row"  name="search_fields_no_per_row"   value="'.$adv_search_fields_no_per_row.'" size="40"/>
    </div>';
    
    $tax_array      =array( 'none'                      =>__('none','wpestate'),
                            'property_category'         =>__('Categories','wpestate'),
                            'property_action_category'  =>__('Action Categories','wpestate'),
                            'property_city'             =>__('City','wpestate'),
                            'property_area'             =>__('Area','wpestate'),
                            'property_county_state'     =>__('County State','wpestate'),
                            );
    
    $adv6_taxonomy_select   =   wpestate_dropdowns_theme_admin_with_key($tax_array,'adv6_taxonomy');
    $adv6_taxonomy          =   get_option('wp_estate_adv6_taxonomy');
    $adv6_taxonomy_terms    =   get_option('wp_estate_adv6_taxonomy_terms');     
    $adv6_max_price         =   get_option('wp_estate_adv6_max_price');     
    $adv6_min_price         =   get_option('wp_estate_adv6_min_price');     
    //adv6_taxonomy
    //$adv6_taxonomy_terms
    //adv6_min_price
    //adv6_max_price
    
    
   // print_r($adv6_taxonomy_terms);
   // print_r($adv6_min_price);
    //print_r($adv6_max_price);
        
    print'<div class="estate_option_row var_price_sliders">
    <div class="label_option_row">'.__('Select Taxonomy for tabs options in Advanced Search Type 6, Type 7, Type 8, Type 9','wpestate').'</div>
    <div class="option_row_explain">'.__('This applies for  the search over media header.','wpestate').'</div>    
        <select id="adv6_taxonomy" name="adv6_taxonomy">
            '.$adv6_taxonomy_select.'
        </select> 
    

        

        <select id="adv6_taxonomy_terms" name="adv6_taxonomy_terms[]" multiple="multiple" style="';
        if($adv6_taxonomy==''){print 'display:none;';}
        print 'height:200px;">'; 
        
        if($adv6_taxonomy !=='' ){
            $terms = get_terms( array(
                'taxonomy' => $adv6_taxonomy,
                'hide_empty' => false,
                'orderby'   =>'ID',
                'order'     =>'ASC'
            ) );
       
            foreach($terms as $term){
                print '<option value="'.$term->term_id.'" ';
                if(is_array($adv6_taxonomy_terms) && in_array($term->term_id, $adv6_taxonomy_terms) ){
                    print ' selected= "selected" ';
                }
                
                print' >'.$term->name.'</option>';
            }      
    
        }
        
        
        print'</select>';

        print '<div style="margin-bottom:30px;"></div>';
        $i=0;
        
        if(is_array($adv6_taxonomy_terms)){

            print '<div class="label_option_row" style="margin-bottom:10px;">'.__('Price SLider values for advanced search with tabs','wpestate').'</div>';
            foreach ($adv6_taxonomy_terms as $term_id){
                $term = get_term( $term_id, $adv6_taxonomy);

                print '<div class="field_row">
                    <div class="field_item">'.__('Price Slider Values(min/max) for ','wpestate').$term->name.'</div>

                    <div class="field_item">
                       <input type="text" id="adv6_min_price" name="adv6_min_price[]" value="';

                        if( isset( $adv6_min_price[$i]) ){
                            echo $adv6_min_price[$i];
                        }
                        print'">
                    </div>

                    <div class="field_item">
                        <input type="text" id="adv6_max_price" name="adv6_max_price[]" value="';
                        if( isset( $adv6_max_price[$i]) ){
                            echo $adv6_max_price[$i];
                        }
                        print'">
                    </div>
                </div>';
                $i++;

            }
            
        }
       

print'</div>';

      print'<div class="" style="width:100%;float:left;margin-bottom:20px;"></div>';
    
    
    
    $custom_advanced_search= get_option('wp_estate_custom_advanced_search','');
    $adv_search_what    = get_option('wp_estate_adv_search_what','');
    $adv_search_how     = get_option('wp_estate_adv_search_how','');
    $adv_search_label   = get_option('wp_estate_adv_search_label','');

      
    print '<div class="estate_option_row">';
    print '<div id="custom_fields_search">';   
    print '<div class="field_row">
    <div class="field_item"><strong>'.__('Place in advanced search form','wpestate').'</strong></div>
    <div class="field_item"><strong>'.__('Search field','wpestate').'</strong></div>
    <div class="field_item"><strong>'.__('How it will compare','wpestate').'</strong></div>
    <div class="field_item"><strong>'.__('Label on Front end','wpestate').'</strong></div>
    </div>';
    
   
        
        
    $i=0;
    while( $i < $adv_search_fields_no ){
        $i++;
    
        print '<div class="field_row">
        <div class="field_item">'.__('Spot no ','wpestate').$i.'</div>
        
        <div class="field_item">
            <select id="adv_search_what'.$i.'" name="adv_search_what[]">';
                print   wpestate_show_advanced_search_options($i-1,$adv_search_what);
            print'</select>
        </div>
        
        <div class="field_item">
            <select id="adv_search_how'.$i.'" name="adv_search_how[]">';
                print  wpestate_show_advanced_search_how($i-1,$adv_search_how);
        
                $new_val=''; 
                if( isset($adv_search_label[$i-1]) ){
                    $new_val=$adv_search_label[$i-1]; 
                }
        print '</select>
        </div>
        
        <div class="field_item"><input type="text" id="adv_search_label'.$i.'" name="adv_search_label[]" value="'.$new_val.'"></div>
        </div>';

    }
    print'</div>';
    print'    
        <p style="margin-left:10px;">
         '.__('*Do not duplicate labels and make sure search fields do not contradict themselves','wpestate').'</br>
        '.__('*Labels will not apply for taxonomy dropdowns fields','wpestate').'</br>
      
        </p>';
    
    print'</div>';
        
    $feature_list       =   esc_html( get_option('wp_estate_feature_list') );
    $feature_list_array =   explode( ',',$feature_list);
    foreach($feature_list_array as $checker => $value){
        $feature_list_array[$checker]= stripslashes($value);
    }
    
  
    $advanced_exteded =  get_option('wp_estate_advanced_exteded');
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Amenities and Features for Advanced Search?','wpestate').'</div>
    <div class="option_row_explain">'.__('Select which features and amenities show in search.','wpestate').'</div>';    
        
        print ' <p style="margin-left:10px;">  '.__('*Hold CTRL for multiple selection','wpestate').'</p>
        <input type="hidden" name="advanced_exteded[]" value="none">
        <select name="advanced_exteded[]" multiple="multiple" style="height:400px;">';
        foreach($feature_list_array as $checker => $value){
            $value          =   stripslashes($value);
            $post_var_name  =   str_replace(' ','_', trim($value) );
            
            
            print '<option value="'.$post_var_name.'"' ;
            if(is_array($advanced_exteded)){
                if( in_array ($post_var_name,$advanced_exteded) ){
                    print ' selected="selected" ';
                } 
            }
            
            print '>'.stripslashes($value).'</option>';                
        }
        print '</select>';
        
    print'</div>';
         
    
 
       
      
        
    
    print ' <div class="estate_option_row_submit">
    <input type="submit" name="submit"  class="new_admin_submit " value="'.__('Save Changes','wpestate').'" />
    </div>';  

       // wpestate_theme_admin_adv_search();
}
endif;



if( !function_exists('new_wpestate_advanced_search_settings') ):  
 function new_wpestate_advanced_search_settings(){
    $cache_array                    =   array('yes','no');
    
    $custom_advanced_search= get_option('wp_estate_custom_advanced_search','');
    $adv_search_what    = get_option('wp_estate_adv_search_what','');
    $adv_search_how     = get_option('wp_estate_adv_search_how','');
    $adv_search_label   = get_option('wp_estate_adv_search_label','');
    
    
    
    $value_array=array('no','yes');
    $search_array = array (1,2,3,4,5,6,7,8,9);
    $show_adv_search_type= wpestate_dropdowns_theme_admin($search_array,'adv_search_type',__('Type','wpestate').' ');
    
    
    $show_adv_search_general_select     = wpestate_dropdowns_theme_admin($cache_array,'show_adv_search_general');
    $show_adv_search_slider_select      = wpestate_dropdowns_theme_admin($cache_array,'show_adv_search_slider');
    $show_adv_search_visible_select     = wpestate_dropdowns_theme_admin($cache_array,'show_adv_search_visible');
    $show_adv_search_extended_select    = wpestate_dropdowns_theme_admin($cache_array,'show_adv_search_extended');
    $show_save_search_select            = wpestate_dropdowns_theme_admin($cache_array,'show_save_search');
    $show_slider_price_select           = wpestate_dropdowns_theme_admin($cache_array,'show_slider_price');
    $show_dropdowns_select              = wpestate_dropdowns_theme_admin($cache_array,'show_dropdowns');
    
    
    
    
    $period_array   =array( 0 =>__('daily','wpestate'),
                            1 =>__('weekly','wpestate') 
                            );
    
    $search_alert_select = wpestate_dropdowns_theme_admin_with_key($period_array,'search_alert');
    
 
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Advanced Search Type ?','wpestate').'</div>
    <div class="option_row_explain">'.__('This applies for  the search over header type.','wpestate').'</div>    
        <select id="adv_search_type" name="adv_search_type">
                    '.$show_adv_search_type.'
                </select> 
    </div>';
     
     
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Use Saved Search Feature ?','wpestate').'</div>
    <div class="option_row_explain">'.__('If yes, user can save his searchs from Advanced Search Results, if he is logged in with a registered account.','wpestate').'</div>    
        <select id="show_save_search" name="show_save_search">
            '.$show_save_search_select.'
        </select> 
    </div>';


    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Send emails','wpestate').'</div>
    <div class="option_row_explain">'.__('Send weekly or daily an email alert with new published properties matching user saved searches.','wpestate').'</div>    
        <select id="search_alert" name="search_alert">
            '.$search_alert_select.'
        </select>
    </div>';
       

        
     
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Show Advanced Search ?','wpestate').'</div>
    <div class="option_row_explain">'.__('Disables or enables the display of advanced search over header media (Google Maps, Revolution Slider, theme slider or image)','wpestate').'</div>    
        <select id="show_adv_search_general" name="show_adv_search_general">
            '.$show_adv_search_general_select.'
        </select>
    </div>';    
     
        
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Show Advanced Search over sliders or images ?','wpestate').'</div>
    <div class="option_row_explain">'.__('Disables or enables the display of advanced search over header type: revolution slider, image and theme slider.','wpestate').'</div>    
        <select id="show_adv_search_slider" name="show_adv_search_slider">
            '.$show_adv_search_slider_select.'
        </select>
    </div>';  
        
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Keep Advanced Search visible? (*only for Type 1,3 and 4)','wpestate').'</div>
    <div class="option_row_explain">'.__('If no, advanced search over header will display in closed position by default. ','wpestate').'</div>    
        <select id="show_adv_search_visible" name="show_adv_search_visible">
            '.$show_adv_search_visible_select.'
        </select>
    </div>';
     
     
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Show Amenities and Features fields?','wpestate').'</div>
    <div class="option_row_explain">'.__('Select what features from Advanced Search Form. *for speed reasons, the "features checkboxes" will not filter the pins on the map','wpestate').'</div>    
        <select id="show_adv_search_extended" name="show_adv_search_extended">
            '.$show_adv_search_extended_select.'
        </select>
    </div>';
        
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Show Slider for Price?','wpestate').'</div>
    <div class="option_row_explain">'.__('If no, price field can still be used in search and it will be input type.','wpestate').'</div>    
        <select id="show_slider_price" name="show_slider_price">
            '.$show_slider_price_select.'
        </select>
    </div>';
        
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Show Dropdowns for beds, bathrooms or rooms?(*only works with Custom Fields - YES)','wpestate').'</div>
    <div class="option_row_explain">'.__('Custom Fields are enabled and set from Advanced Search Form.','wpestate').'</div>    
        <select id="show_dropdowns" name="show_dropdowns">
            '.$show_dropdowns_select.'
        </select>
    </div>';
        
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Minimum and Maximum value for Price Slider','wpestate').'</div>
    <div class="option_row_explain">'.__('Type only numbers!','wpestate').'</div>    
        <input type="text" name="show_slider_min_price"  class="inptxt " value="'.floatval(get_option('wp_estate_show_slider_min_price','')).'"/>
        -   
        <input type="text" name="show_slider_max_price"  class="inptxt " value="'.floatval(get_option('wp_estate_show_slider_max_price','')).'"/>
    </div>';
        


    $adv_back_color              =  esc_html ( get_option('wp_estate_adv_back_color','') );
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Advanced Search Background Color','wpestate').'</div>
    <div class="option_row_explain">'.__('Advanced Search Background Color','wpestate').'</div>    
        <input type="text" name="adv_back_color" value="'.$adv_back_color.'" maxlength="7" class="inptxt" />
        <div id="adv_back_color" class="colorpickerHolder" ><div class="sqcolor" style="background-color:#'.$adv_back_color.';" ></div></div>
    </div>';
        

    $adv_font_color              =  esc_html ( get_option('wp_estate_adv_font_color','') );
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Advanced Search Font Color','wpestate').'</div>
    <div class="option_row_explain">'.__('Advanced Search Font Color','wpestate').'</div>    
        <input type="text" name="adv_font_color" value="'.$adv_font_color.'" maxlength="7" class="inptxt" />
        <div id="adv_font_color" class="colorpickerHolder" ><div class="sqcolor" style="background-color:#'.$adv_font_color.';" ></div></div>
    </div>';
    
    $adv_search_back_color          =  esc_html ( get_option('wp_estate_adv_search_back_color ','') );
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Map Advanced Search Button Background Color','wpestate').'</div>
    <div class="option_row_explain">'.__('Map Advanced Search Button Background Color','wpestate').'</div>    
        <input type="text" name="adv_search_back_color" value="'.$adv_search_back_color.'" maxlength="7" class="inptxt" />
        <div id="adv_search_back_color" class="colorpickerHolder"><div class="sqcolor" style="background-color:#'.$adv_search_back_color.';"></div></div>
    </div>';
    
    $adv_search_font_color          =  esc_html ( get_option('wp_estate_adv_search_font_color','') );  
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Advanced Search Fields Font Color','wpestate').'</div>
    <div class="option_row_explain">'.__('Advanced Search Fields Font Color','wpestate').'</div>    
        <input type="text" name="adv_search_font_color" value="'.$adv_search_font_color.'" maxlength="7" class="inptxt" />
        <div id="adv_search_font_color" class="colorpickerHolder"><div class="sqcolor" style="background-color:#'.$adv_search_font_color.';"></div></div>
    </div>';
    
    $adv_position             =  esc_html ( get_option('wp_estate_adv_position','') );
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Advanced Search Form Position','wpestate').'</div>
    <div class="option_row_explain">'.__('Advanced Search Form Position (add px or % after)- the distance betwen advanced search form and the bottom of media header.','wpestate').'</div>    
        <input type="text" name="adv_position" value="'.$adv_position.'"  class="inptxt" />
    </div>';
     
    
    print ' <div class="estate_option_row_submit">
    <input type="submit" name="submit"  class="new_admin_submit " value="'.__('Save Changes','wpestate').'" />
    </div>';  
 } 
 endif;       
 
 
 
if( !function_exists('new_wpestate_custom_fields') ):   
function new_wpestate_custom_fields(){
   
    $custom_fields = get_option( 'wp_estate_custom_fields', true);     
    $current_fields='';

    
    $i=0;
    if( !empty($custom_fields)){    
        while($i< count($custom_fields) ){
            $current_fields.='
                <div class=field_row>
                <div    class="field_item"><strong>'.__('Field Name','wpestate').'</strong></br><input  type="text" name="add_field_name[]"   value="'.stripslashes( $custom_fields[$i][0] ).'"  ></div>
                <div    class="field_item"><strong>'.__('Field Label','wpestate').'</strong></br><input  type="text" name="add_field_label[]"   value="'.stripslashes( $custom_fields[$i][1]).'"  ></div>
                <div    class="field_item"><strong>'.__('Field Type','wpestate').'</strong></br>'.wpestate_fields_type_select($custom_fields[$i][2]).'</div>
                <div    class="field_item"><strong>'.__('Field Order','wpestate').'</strong></br><input  type="text" name="add_field_order[]" value="'.$custom_fields[$i][3].'"></div>     
                <div    class="field_item newfield"><strong>'.__('Dropdown values','wpestate').'</strong></br><textarea name="add_dropdown_order[]">';
                
                if( isset($custom_fields[$i][4])){
                    $current_fields.= $custom_fields[$i][4];
                }    
                $current_fields.='</textarea></div>     
             
                <a class="deletefieldlink" href="#">'.__('delete','wpestate').'</a>
            </div>';    
            $i++;
        }
    }
 

    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Custom Fields list','wpestate').'</div>
    <div class="option_row_explain">'.__('Add, edit or delete property custom fields.','wpestate').'</div>    
        <div id="custom_fields_wrapper">
        '.$current_fields.'
        <input type="hidden" name="is_custom" value="1">   
        </div>
    </div>';
    
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Add New Custom Field','wpestate').'</div>
    <div class="option_row_explain">'.__('Fill the form in order to add a new custom field','wpestate').'</div>  
     
        <div class="add_curency">
            <div class="cur_explanations">'.__('Field name','wpestate').'</div>
            <input  type="text" id="field_name"  name="field_name"   value=""/>
            
            <div class="cur_explanations">'.__('Field Label','wpestate').'</div>
             <input  type="text" id="field_label"  name="field_label"   value="" />
            
            <div class="cur_explanations">'.__('Field Type','wpestate').'</div>
                <select id="field_type" name="field_type">
                    <option value="short text">short text</option>
                    <option value="long text">long text</option>
                    <option value="numeric">numeric</option>
                    <option value="date">date</option>
                    <option value="dropdown">dropdown</option>
                </select>
            

            <div class="cur_explanations">'.__(' Order in listing page','wpestate').'</div>
            <input  type="text" id="field_order"  name="field_order"   value="" />
                
            <div class="cur_explanations">'.__('Dropdown values separated by "," (only for dropdown field type)','wpestate').'</div>
            <textarea id="drodown_values"  name="drodown_values"  style="width:300px;"></textarea>
            
            </br>
            <a href="#" id="add_field">'.__(' click to add field','wpestate').'</a>
        </div>
        
        
    </div>'; 
    print ' <div class="estate_option_row_submit">
    <input type="submit" name="submit"  class="new_admin_submit " value="'.__('Save Changes','wpestate').'" />
    </div>';   

}
endif;


if( !function_exists('new_wpestate_ammenities_features') ):   
function new_wpestate_ammenities_features(){
    $feature_list                           =   esc_html( get_option('wp_estate_feature_list') );
    $feature_list                           =   str_replace(', ',',&#13;&#10;',$feature_list);
    
    $cache_array=array('yes','no');
    $show_no_features_symbol =  wpestate_dropdowns_theme_admin($cache_array,'show_no_features');
    
    
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Add New Element in Features and Amenities','wpestate').'</div>
    <div class="option_row_explain">'.__('Type and add a new item in features and amenities list.','wpestate').'</div>    
        <input  type="text" id="new_feature"  name="new_feature"   value="type here feature name.. " size="40"/><br>
        <a href="#" id="add_feature"> click to add feature </a><br>
    </div>';
  
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Features and Amenities list','wpestate').'</div>
    <div class="option_row_explain">'.__('list of already added features and amenities','wpestate').'</div>    
        <textarea id="feature_list" name="feature_list" rows="15" cols="42">'.stripslashes( $feature_list).'</textarea> 
    </div>';
      
     print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Show the Features and Amenities that are not available','wpestate').'</div>
    <div class="option_row_explain">'.__('Show on property page the features and amenities that are not selected?','wpestate').'</div>    
        <select id="show_no_features" name="show_no_features">
            '.$show_no_features_symbol.'
        </select> 
    </div>';
    print ' <div class="estate_option_row_submit">
    <input type="submit" name="submit"  class="new_admin_submit " value="'.__('Save Changes','wpestate').'" />
    </div>';
}
endif;

if( !function_exists('new_wpestate_listing_labels') ):   
function new_wpestate_property_links(){
  
 
    $rewrites =  get_option('wp_estate_url_rewrites');
 
    $links_to_rewrite = array(
        'property_page'         =>  array(
                                        $rewrites[0],
                                        __('Property Page','wpestate')
                                    ),
        
        'property_category'     =>  array(
                                         $rewrites[1],
                                        __('Property Categories Page','wpestate')
                                    ),
        
        'property_action_category'     =>  array(
                                        $rewrites[2],
                                        __('Property Action Category Page','wpestate')
                                    ),
        'property_city'     =>  array(
                                        $rewrites[3],
                                        __('Property City Page','wpestate')
                                    ),
        
        'property_area'     =>  array(
                                        $rewrites[4],
                                        __('Property Area Page','wpestate')
                                    ),
        
        'property_county_state'     =>  array(
                                         $rewrites[5],
                                        __('Property County/State Page','wpestate')
                                    ),
        'agent_page'     =>  array(
                                         $rewrites[6],
                                        __('Agent Page','wpestate')
                                    ),
        'agent_category'     =>  array(
                                         $rewrites[7],
                                        __('Agent Categories Page','wpestate')
                                    ),
        
        'agent_action_category'     =>  array(
                                        $rewrites[8],
                                        __('Agent Action Category Page','wpestate')
                                    ),
        'agent_city'     =>  array(
                                        $rewrites[9],
                                        __('Agent City Page','wpestate')
                                    ),
        
        'agent_area'     =>  array(
                                        $rewrites[10],
                                        __('Agent Area Page','wpestate')
                                    ),
        
        'agent_county_state'     =>  array(
                                         $rewrites[11],
                                        __('Agent County/State Page','wpestate')
                                    ),
    );
    
    $i=0;
      print'<div class="estate_option_row">'.__(' You cannot use special characters like "&". After changing the url you may need to wait for a few minutes until wordpress changes all the urls.','wpestate').'</div>';
    
    
    foreach ($links_to_rewrite as $key=>$value){
        print'<div class="estate_option_row">
        <div class="label_option_row">'.$value[1].'</div>
        <div class="option_row_explain">'.__('Custom link for ','wpestate').' '.$value[1].'</div>    
           '.get_bloginfo('url').'/ <input  type="text" id="'.$value[1].'"  name="url_rewrites[]"   value="'.$rewrites[$i].'"/> /....
        </div>';
        $i++;
    }
      
      
      
    
    print ' <div class="estate_option_row_submit">
    <input type="submit" name="submit"  class="new_admin_submit " value="'.__('Save Changes','wpestate').'" />
    </div>';
}
endif;


if( !function_exists('new_wpestate_listing_labels') ):   
function new_wpestate_listing_labels(){
    $cache_array                            =   array('yes','no');
    $status_list                            =   esc_html(stripslashes( get_option('wp_estate_status_list') ) );
    $status_list                            =   str_replace(', ',',&#13;&#10;',$status_list);
    $property_adr_text                      =   stripslashes ( esc_html( get_option('wp_estate_property_adr_text') ) );
    $property_description_text              =   stripslashes ( esc_html( get_option('wp_estate_property_description_text') ) );
    $property_details_text                  =   stripslashes ( esc_html( get_option('wp_estate_property_details_text') ) );
    $property_features_text                 =   stripslashes ( esc_html( get_option('wp_estate_property_features_text') ) );
   
    $property_multi_text                      =   stripslashes ( esc_html( get_option('wp_estate_property_multi_text') ) );
    $property_multi_child_text                      =   stripslashes ( esc_html( get_option('wp_estate_property_multi_child_text') ) );
   
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Multi Unit Label','wpestate').'</div>
    <div class="option_row_explain">'.__(' Custom title instead of Multi Unit label.','wpestate').'</div>    
        <input  type="text" id="property_multi_text"  name="property_multi_text"   value="'.$property_multi_text.'"/>
    </div>';
    
     print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Multi Unit Label (*for sub unit)','wpestate').'</div>
    <div class="option_row_explain">'.__(' Custom title instead of Multi Unit label(*for sub unit).','wpestate').'</div>    
        <input  type="text" id="property_multi_child_text"  name="property_multi_child_text"   value="'.$property_multi_child_text.'"/>
    </div>';
     
 
        
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Property Address Label','wpestate').'</div>
    <div class="option_row_explain">'.__(' Custom title instead of Property Address label.','wpestate').'</div>    
        <input  type="text" id="property_adr_text"  name="property_adr_text"   value="'.$property_adr_text.'"/>
    </div>';
              
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Property Features Label','wpestate').'</div>
    <div class="option_row_explain">'.__('Update; Custom title instead of Features and Amenities label.','wpestate').'</div>    
        <input  type="text" id="property_features_text"  name="property_features_text"   value="'.$property_features_text.'" size="40"/>
    </div>';
                
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Property Description Label','wpestate').'</div>
    <div class="option_row_explain">'.__('Custom title instead of Description label.','wpestate').'</div>    
        <input  type="text" id="property_description_text"  name="property_description_text"   value="'.$property_description_text.'" size="40"/>
    </div>';

    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Property Details Label','wpestate').'</div>
    <div class="option_row_explain">'.__('Custom title instead of Property Details label. ','wpestate').'</div>    
        <input  type="text" id="property_details_text"  name="property_details_text"   value="'.$property_details_text.'" size="40"/>
    </div>';

    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Property Status ','wpestate').'</div>
    <div class="option_row_explain">'.__('Property Status (* you may need to add new css classes - please see the help files) ','wpestate').'</div>    
        <input  type="text" id="new_status"  name="new_status"   value="'.__('type here the new status... ','wpestate').'"/></br>
        <a href="#new_status" id="add_status">'.__('click to add new status','wpestate').'</a><br>
        <textarea id="status_list" name="status_list" rows="7" style="width:300px;">'.$status_list.'</textarea>  
    </div>';
    
    print ' <div class="estate_option_row_submit">
    <input type="submit" name="submit"  class="new_admin_submit " value="'.__('Save Changes','wpestate').'" />
    </div>';
}
endif; 

if( !function_exists('new_wpestate_theme_slider') ):   
 function new_wpestate_theme_slider(){
    $theme_slider   =   get_option( 'wp_estate_theme_slider', true); 
    $slider_cycle   =   get_option( 'wp_estate_slider_cycle', true); 
    
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Select Properties','wpestate').'</div>
    <div class="option_row_explain">'.__('Select properties for slider - *hold CTRL for multiple select ','wpestate').'</div>
    <div class="option_row_explain">'.__('Due to speed reason we only show here the first 50 listings. If you want to add other listings into the theme slider please go and edit the property (in wordpress admin) and select "Property in theme Slider" in Property Details tab.','wpestate').'</div>';    
        
        $args = array(  'post_type'                 =>  'estate_property',
                        'post_status'               =>  'publish',
                        'paged'                     =>  1,
                        'posts_per_page'            =>  50,
                        'cache_results'             =>  false,
                        'update_post_meta_cache'    =>  false,
                        'update_post_term_cache'    =>  false,
                );

        $recent_posts = new WP_Query($args);
        print '<select name="theme_slider[]"  id="theme_slider"  multiple="multiple">';
        while ($recent_posts->have_posts()): $recent_posts->the_post();
             $theid=get_the_ID();
             print '<option value="'.$theid.'" ';
             if( is_array($theme_slider) && in_array($theid, $theme_slider) ){
                 print ' selected="selected" ';
             }
             print'>'.get_the_title().'</option>';
        endwhile;
        print '</select>';
     
    print '</div>';
    
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Number of milisecons before auto cycling an item','wpestate').'</div>
    <div class="option_row_explain">'.__('Number of milisecons before auto cycling an item (5000=5sec).Put 0 if you don\'t want to autoslide. ','wpestate').'</div>    
        <input  type="text" id="slider_cycle" name="slider_cycle"  value="'.$slider_cycle.'"/> 
    </div>';
    
   
    $cache_array                    =   array('type1','type2');  
    $theme_slider_type_select       =   wpestate_dropdowns_theme_admin($cache_array,'theme_slider_type');
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Design Type?','wpestate').'</div>
    <div class="option_row_explain">'.__('Select the design type.','wpestate').'</div>    
        <select id="theme_slider_type" name="theme_slider_type">
            '.$theme_slider_type_select.'
        </select>
    </div>';
    
    
    print ' <div class="estate_option_row_submit">
    <input type="submit" name="submit"  class="new_admin_submit " value="'.__('Save Changes','wpestate').'" />
    </div>';
     
 }
 endif;
 
 
 if( !function_exists('new_wpestate_email_management') ):   
 function new_wpestate_email_management(){
     
        $emails=array(
            'new_user'                  =>  __('New user  notification','wpestate'),
            'admin_new_user'            =>  __('New user admin notification','wpestate'),
            'purchase_activated'        =>  __('Purchase Activated','wpestate'),
            'password_reset_request'    =>  __('Password Reset Request','wpestate'),
            'password_reseted'          =>  __('Password Reseted','wpestate'),
            'purchase_activated'        =>  __('Purchase Activated','wpestate'),
            'approved_listing'          =>  __('Approved Listings','wpestate'),
            'new_wire_transfer'         =>  __('New wire Transfer','wpestate'),
            'admin_new_wire_transfer'   =>  __('Admin - New wire Transfer','wpestate'),
            'admin_expired_listing'     =>  __('Admin - Expired Listing','wpestate'),
            'matching_submissions'      =>  __('Matching Submissions','wpestate'),
            'paid_submissions'          =>  __('Paid Submission','wpestate'),
            'featured_submission'       =>  __('Featured Submission','wpestate'),
            'account_downgraded'        =>  __('Account Downgraded','wpestate'),
            'membership_cancelled'      =>  __('Membership Cancelled','wpestate'),
            'downgrade_warning'         =>  __('Membership Expiration Warning','wpestate'),
            'free_listing_expired'      =>  __('Free Listing Expired','wpestate'),
            'new_listing_submission'    =>  __('New Listing Submission','wpestate'),
            'listing_edit'              =>  __('Listing Edit','wpestate'),
            'recurring_payment'         =>  __('Recurring Payment','wpestate'),
            'membership_activated'      =>  __('Membership Activated','wpestate'),
            'agent_update_profile'      =>  __('Update Profile','wpestate'),
           
        );
        
        

            
        print'<div class="estate_option_row">
        <div class="label_option_row">'.__('Global variables: %website_url as website url,%website_name as website name, %user_email as user_email, %username as username','wpestate').'</div>
        </div>';
        
        foreach ($emails as $key=>$label ){
            $value          = stripslashes( get_option('wp_estate_'.$key,'') );
            $value_subject  = stripslashes( get_option('wp_estate_subject_'.$key,'') );
              
            print'<div class="estate_option_row">
            <div class="label_option_row">'.__('Subject for','wpestate').' '.$label.'</div>
            <div class="option_row_explain">'.__('Email subject for').' '.$label.'</div>
            <input type="text" style="width:100%" name="subject_'.$key.'" value="'.$value_subject.'" />
            </br>
            <div class="label_option_row">'.__('Content for','wpestate').' '.$label.'</div>
            <div class="option_row_explain">'.__('Email content for').' '.$label.'</div>
            <textarea rows="10" style="width:100%" name="'.$key.'">'.$value.'</textarea>
            <div class="extra_exp_new"> '.wpestate_emails_extra_details($key).'</div>
            </div>';
    
         
        

        }
    print ' <div class="estate_option_row_submit">
    <input type="submit" name="submit"  class="new_admin_submit " value="'.__('Save Changes','wpestate').'" />
    </div>';
        
}
endif;


 if( !function_exists('new_wpestate_site_speed') ):   
function new_wpestate_site_speed(){
      
    $cache_array=array('no','yes');
    $mimify_css_js=  wpestate_dropdowns_theme_admin($cache_array,'use_mimify');
    
    
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Speed Advices','wpestate').'</div>
    <div class="option_row_explain">'.__('1. If you are NOT using "Ultimate Addons for Visual Composer" please disable it or just disable the modules you don\'t use. It will reduce the size of javascript files you are loading and increase the site speed!    ','wpestate').'</div>    
    <div class="option_row_explain">'.__('2. Use the EWWW Image Optimizer (or WP Smush IT) plugin to optimise images- optimised images increase the site speed.','wpestate').'</div>
    <div class="option_row_explain">'.__('3. CCreate a free account on Cloudflare (https://www.cloudflare.com/) and use this CDN.','wpestate').'</div>
    <div class="option_row_explain">'.__('4. If you are using custom categories make sure you are adding the custom pins images on Theme Options -> Map -> Pin Management. The site will get slow if it needs to look for images that don\'t exist.','wpestate').'</div>
    </div>';
    
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Minify css and js files','wpestate').'</div>
    <div class="option_row_explain">'.__('The system will use the minified versions of the css and js files','wpestate').'</div>    
        <select id="use_mimify" name="use_mimify">
            '.$mimify_css_js.'
        </select> 
    </div>';
    
    
    $remove_script_version=  wpestate_dropdowns_theme_admin($cache_array,'remove_script_version');
    
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Remove script version','wpestate').'</div>
    <div class="option_row_explain">'.__('The system will remove the script version when it is included. This doest not actually improve speed, but improves test score on speed tools pages.','wpestate').'</div>    
        <select id="remove_script_version" name="remove_script_version">
            '.$remove_script_version.'
        </select> 
    </div>';
     
    
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Enable Browser Cache','wpestate').'</div>
    <div class="option_row_explain">'.__('Add this code in your .httaces file(copy paste at the end). It will activate the browser cache and speed up your site.','wpestate').'</div>    
       <textarea rows="15" style="width:100%;" onclick="this.focus();this.select();">      
<IfModule mod_deflate.c>
# Insert filters
AddOutputFilterByType DEFLATE text/plain
AddOutputFilterByType DEFLATE text/html
AddOutputFilterByType DEFLATE text/xml
AddOutputFilterByType DEFLATE text/css
AddOutputFilterByType DEFLATE application/xml
AddOutputFilterByType DEFLATE application/xhtml+xml
AddOutputFilterByType DEFLATE application/rss+xml
AddOutputFilterByType DEFLATE application/javascript
AddOutputFilterByType DEFLATE application/x-javascript
AddOutputFilterByType DEFLATE application/x-httpd-php
AddOutputFilterByType DEFLATE application/x-httpd-fastphp
AddOutputFilterByType DEFLATE image/svg+xml
# Drop problematic browsers
BrowserMatch ^Mozilla/4 gzip-only-text/html
BrowserMatch ^Mozilla/4\.0[678] no-gzip
BrowserMatch \bMSI[E] !no-gzip !gzip-only-text/html
# Make sure proxies dont deliver the wrong content
Header append Vary User-Agent env=!dont-vary
</IfModule>
## EXPIRES CACHING ##
<IfModule mod_expires.c>
ExpiresActive On
ExpiresByType image/jpg "access 1 year"
ExpiresByType image/jpeg "access 1 year"
ExpiresByType image/gif "access 1 year"
ExpiresByType image/png "access 1 year"
ExpiresByType text/css "access 1 month"
ExpiresByType text/html "access 1 month"
ExpiresByType application/pdf "access 1 month"
ExpiresByType text/x-javascript "access 1 month"
ExpiresByType application/x-shockwave-flash "access 1 month"
ExpiresByType image/x-icon "access 1 year"
ExpiresDefault "access 1 month"
</IfModule>
## EXPIRES CACHING ##
       </textarea>
    </div>';
     
    print ' <div class="estate_option_row_submit">
    <input type="submit" name="submit"  class="new_admin_submit " value="'.__('Save Changes','wpestate').'" />
    </div>';
}
endif;









if( !function_exists('new_wpestate_generate_pins') ):   
 function new_wpestate_generate_pins(){
    
    if (isset($_POST['startgenerating']) == 1){
        //wpestate_generate_file_pins();
        if ( get_option('wp_estate_readsys','') =='yes' ){
        $path=estate_get_pin_file_path(); 
   
            if ( file_exists ($path) && is_writable ($path) ){
                wpestate_listing_pins_for_file();
                print'<div class="estate_option_row">
                <div class="label_option_row">'. __('File was generated','wpestate').'</div>
                </div>';
            }else{
                print'<div class="estate_option_row">
                <div class="label_option_row">'.__('the file Google map does NOT exist or is NOT writable','wpestate') .'</div>
                </div>';
            }
   
        }else{
          
            print'<div class="estate_option_row">
            <div class="label_option_row">'.  __('Pin Generation works only if the file reading option in Google Map setting is set to yes','wpestate').'</div>
            </div>';
    
        }
    
    
    }else{  
     
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Generate the pins','wpestate').'</div>
    <div class="option_row_explain">'.__('Generate the pins for the read from file map option','wpestate').'</div>    
       
    <input type="hidden" name="startgenerating" value="1" />
        
    </div>';
    
    }
    
     print ' <div class="estate_option_row_submit">
    <input type="submit" name="submit"  class="new_admin_submit " value="'.__('Generate Pins','wpestate').'" />
    </div>';
    
}
endif;


if( !function_exists('new_wpestate_help_custom') ):   
function new_wpestate_help_custom(){
  
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Help and Custom','wpestate').'</div>
    <div class="option_row_explain">'.__('Help and custom work','wpestate').'</div>    
       <p> '.__('For theme help please check http://help.wpresidence.net/. If your question is not here, please go to http://support.wpestate.org, create an account and post a ticket. The registration is simple and as soon as you send a ticket we are notified. We usually answer in the next 24h (except weekends). Please use this system and not the email. It will help us answer your questions much faster. Thank you!','wpestate').' </p>
       <p> '.__('For custom work on this theme please go to  <a href="http://support.wpestate.org/" target="_blank">http://support.wpestate.org</a>, create a ticket with your request and we will offer a free quote.','wpestate').' </p>
       <p> '.__('Subscribe to our mailing list in order to receive news about new features and theme upgrades. <a href="http://eepurl.com/CP5U5">Subscribe Here!</a>','wpestate').' </p>
                   
    </div>';
        
}
endif;



if(!function_exists('estate_yelp_settings')):
function estate_yelp_settings(){
    $yelp_terms             =   get_option('wp_estate_yelp_categories','');    
    $yelp_client_id         =   get_option('wp_estate_yelp_client_id','');
    $yelp_client_secret     =   get_option('wp_estate_yelp_client_secret','');
    if(!is_array($yelp_terms)){
        $yelp_terms=array();
    }
    
   
    
    $yelp_terms_array = 
            array (
                'active'            =>  array( 'category' => __('Active Life','wpestate'),
                                                'category_sign' => 'fa fa-bicycle'),
                'arts'              =>  array( 'category' => __('Arts & Entertainment','wpestate'), 
                                               'category_sign' => 'fa fa-music') ,
                'auto'              =>  array( 'category' => __('Automotive','wpestate'), 
                                                'category_sign' => 'fa fa-car' ),
                'beautysvc'         =>  array( 'category' => __('Beauty & Spas','wpestate'), 
                                                'category_sign' => 'fa fa-female' ),
                'education'         => array(  'category' => __('Education','wpestate'),
                                                'category_sign' => 'fa fa-graduation-cap' ),
                'eventservices'     => array(  'category' => __('Event Planning & Services','wpestate'), 
                                                'category_sign' => 'fa fa-birthday-cake' ),
                'financialservices' => array(  'category' => __('Financial Services','wpestate'), 
                                                'category_sign' => 'fa fa-money' ),                
                'food'              => array(  'category' => __('Food','wpestate'), 
                                                'category_sign' => 'fa fa fa-cutlery' ),
                'health'            => array(  'category' => __('Health & Medical','wpestate'), 
                                                'category_sign' => 'fa fa-medkit' ),
                'homeservices'      => array(  'category' =>__('Home Services ','wpestate'), 
                                                'category_sign' => 'fa fa-wrench' ),
                'hotelstravel'      => array(  'category' => __('Hotels & Travel','wpestate'), 
                                                'category_sign' => 'fa fa-bed' ),
                'localflavor'       => array(  'category' => __('Local Flavor','wpestate'), 
                                                'category_sign' => 'fa fa-coffee' ),
                'localservices'     => array(  'category' => __('Local Services','wpestate'), 
                                                'category_sign' => 'fa fa-dot-circle-o' ),
                'massmedia'         => array(  'category' => __('Mass Media','wpestate'),
                                                'category_sign' => 'fa fa-television' ),
                'nightlife'         => array(  'category' => __('Nightlife','wpestate'),
                                                'category_sign' => 'fa fa-glass' ),
                'pets'              => array(  'category' => __('Pets','wpestate'),
                                                'category_sign' => 'fa fa-paw' ),
                'professional'      => array(  'category' => __('Professional Services','wpestate'), 
                                                'category_sign' => 'fa fa-suitcase' ),
                'publicservicesgovt'=> array(  'category' => __('Public Services & Government','wpestate'),
                                                'category_sign' => 'fa fa-university' ),
                'realestate'        => array(  'category' => __('Real Estate','wpestate'), 
                                                'category_sign' => 'fa fa-building-o' ),
                'religiousorgs'     => array(  'category' => __('Religious Organizations','wpestate'), 
                                                'category_sign' => 'fa fa-cloud' ),
                'restaurants'       => array(  'category' => __('Restaurants','wpestate'),
                                                'category_sign' => 'fa fa-cutlery' ),
                'shopping'          => array(  'category' => __('Shopping','wpestate'),
                                                'category_sign' => 'fa fa-shopping-bag' ),
                'transport'         => array(  'category' => __('Transportation','wpestate'),
                                                'category_sign' => 'fa fa-bus' )
    );
    print '<div class="estate_option_row">'.__('Please note that Yelp is not working for all countries. See here ','wpestate').'<a href="https://www.yelp.com/factsheet">https://www.yelp.com/factsheet</a>'.__(' the list of countries where Yelp is available.','wpestate').'</br></div>';
//    
//    print'<div class="estate_option_row">
//    <div class="label_option_row">'.__('Yelp Api v2.0 Consumer Key','wpestate').'</div>
//    <div class="option_row_explain">'.__('Get this detail after you signup here ','wpestate').'<a target="_blank" href="https://www.yelp.com/developers/v2/">https://www.yelp.com/developers/v2/</a></div>    
//        <input  type="text" id="yelp_api_id" name="yelp_api_id"  value="'.$yelp_api_id.'"/> 
//    </div>';
//     
//    
//    print'<div class="estate_option_row">
//    <div class="label_option_row">'.__('Yelp Api v.2.0 Consumer Secret','wpestate').'</div>
//    <div class="option_row_explain">'.__('Get this detail after you signup here ','wpestate').'<a target="_blank" href="https://www.yelp.com/developers/v2/">https://www.yelp.com/developers/v2/</a></div>    
//        <input  type="text" id="yelp_api_secret" name="yelp_api_secret"  value="'.$yelp_api_secret.'"/> 
//    </div>';
//    
//    print'<div class="estate_option_row">
//    <div class="label_option_row">'.__('Yelp Api v.2.0 Token','wpestate').'</div>
//    <div class="option_row_explain">'.__('Get this detail after you signup here ','wpestate').'<a target="_blank" href="https://www.yelp.com/developers/v2/">https://www.yelp.com/developers/v2/</a></div>    
//        <input  type="text" id="yelp_api_token" name="yelp_api_token"  value="'.$yelp_api_token.'"/> 
//    </div>';
//        
//    print'<div class="estate_option_row">
//    <div class="label_option_row">'.__('Yelp Api v.2.0 Token Secret','wpestate').'</div>
//    <div class="option_row_explain">'.__('Get this detail after you signup here ','wpestate').'<a target="_blank" href="https://www.yelp.com/developers/v2/">https://www.yelp.com/developers/v2/</a></div>    
//        <input  type="text" id="yelp_api_token_secret" name="yelp_api_token_secret"  value="'.$yelp_api_token_secret.'"/> 
//    </div>';
//    
//    
//      print'<div class="estate_option_row">
//    <div class="label_option_row">'.__('Yelp Api v.2.0 Token Secret','wpestate').'</div>
//    <div class="option_row_explain">'.__('Get this detail after you signup here ','wpestate').'<a target="_blank" href="https://www.yelp.com/developers/v2/">https://www.yelp.com/developers/v2/</a></div>    
//        <input  type="text" id="yelp_api_token_secret" name="yelp_api_token_secret"  value="'.$yelp_api_token_secret.'"/> 
//    </div>';
      
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Yelp Api Fusion Client Id','wpestate').'</div>
    <div class="option_row_explain">'.__('Get this detail after you signup here ','wpestate').'<a target="_blank" href="https://www.yelp.com/developers/v3/manage_app">https://www.yelp.com/developers/v3/manage_app</a></div>    
        <input  type="text" id="yelp_client_id" name="yelp_client_id"  value="'.$yelp_client_id.'"/> 
    </div>';
    
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Yelp Api Fusion Client Secret','wpestate').'</div>
    <div class="option_row_explain">'.__('Get this detail after you signup here ','wpestate').'<a target="_blank" href="https://www.yelp.com/developers/v3/manage_app">https://www.yelp.com/developers/v3/manage_app</a></div>    
        <input  type="text" id="yelp_client_secret" name="yelp_client_secret"  value="'.$yelp_client_secret.'"/> 
    </div>';
       
       
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Yelp Categories ','wpestate').'</div>
    <div class="option_row_explain">'.__('Yelp Categories to show on front page ','wpestate').'</div>    
        <select name="yelp_categories[]" style="height:400px;" id="yelp_categories" multiple>';
        foreach($yelp_terms_array as $key=>$term){
            print '<option value="'.$key.'" ' ;
            $keyx = array_search ($key,$yelp_terms) ;
            if( $keyx!==false ){
                print 'selected= "selected" ';
            }
            print'>'.$term['category'].'</option>';
        }
    print'</select>
    </div>';
    
       
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Yelp - no of results','wpestate').'</div>
    <div class="option_row_explain">'.__('Yelp - no of results ','wpestate').'</div>    
        <input  type="text" id="yelp_results_no" name="yelp_results_no"  value="'.$yelp_results_no.'"/> 
    </div>';
    
    $cache_array=array('miles','kilometers');
    $yelp_dist_measure=  wpestate_dropdowns_theme_admin($cache_array,'yelp_dist_measure');
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Yelp Distance Measurement Unit','wpestate').'</div>
    <div class="option_row_explain">'.__('Yelp Distance Measurement Unit','wpestate').'</div>    
       <select id="yelp_dist_measure" name="yelp_dist_measure">
            '.$yelp_dist_measure.'
        </select> 
    </div>';
    
    
      print ' <div class="estate_option_row_submit">
    <input type="submit" name="submit"  class="new_admin_submit " value="'.__('Save Changes','wpestate').'" />
    </div>';
     
     
    
}endif;





if(!function_exists('estate_recaptcha_settings')):
function estate_recaptcha_settings(){
    $reCaptha_sitekey       = get_option('wp_estate_recaptha_sitekey','');
    $reCaptha_secretkey     = get_option('wp_estate_recaptha_secretkey','');
    $cache_array=array('no','yes');
    $use_captcha=  wpestate_dropdowns_theme_admin($cache_array,'use_captcha');
    
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Use reCaptcha on register ?','wpestate').'</div>
    <div class="option_row_explain">'.__('This helps preventing registration spam.','wpestate').'</div>    
        <select id="use_captcha" name="use_captcha">
            '.$use_captcha.'
        </select> 
    </div>';
    
    
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('reCaptha site key','wpestate').'</div>
    <div class="option_row_explain">'.__('Get this detail after you signup here ','wpestate').'<a target="_blank" href="https://www.google.com/recaptcha/intro/index.html">https://www.google.com/recaptcha/intro/index.html</a></div>    
        <input  type="text" id="recaptha_sitekey" name="recaptha_sitekey"  value="'.$reCaptha_sitekey.'"/> 
    </div>';
    
     print'<div class="estate_option_row">
    <div class="label_option_row">'.__('reCaptha secret key','wpestate').'</div>
    <div class="option_row_explain">'.__('Get this detail after you signup here ','wpestate').'<a target="_blank" href="https://www.google.com/recaptcha/intro/index.html">https://www.google.com/recaptcha/intro/index.html</a></div>    
        <input  type="text" id="recaptha_secretkey" name="recaptha_secretkey"  value="'.$reCaptha_secretkey.'"/> 
    </div>';
     
    print ' <div class="estate_option_row_submit">
    <input type="submit" name="submit"  class="new_admin_submit " value="'.__('Save Changes','wpestate').'" />
    </div>';
}
endif;



if(!function_exists('optima_express_settings')):
function optima_express_settings(){
   
    $cache_array=array('no','yes');
    $use_optima=  wpestate_dropdowns_theme_admin($cache_array,'use_optima');
    
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Use Optima Express plugin (idx plugin by ihomefinder) - you will need to enable the plugin ?','wpestate').'</div>
    <div class="option_row_explain">'.__('Enable compatibility mode with Optima Express plugin','wpestate').'</div>    
        <select id="use_optima" name="use_optima">
            '.$use_optima.'
        </select> 
    </div>';
    print ' <div class="estate_option_row_submit">
    <input type="submit" name="submit"  class="new_admin_submit " value="'.__('Save Changes','wpestate').'" />
    </div>';
}
endif;



if (!function_exists('new_widget_design_elements_details')):
function new_widget_design_elements_details(){
    
    $sidebarwidget_internal_padding_top          = get_option('wp_estate_sidebarwidget_internal_padding_top','');
    $sidebarwidget_internal_padding_left         = get_option('wp_estate_sidebarwidget_internal_padding_left','');
    $sidebarwidget_internal_padding_bottom       = get_option('wp_estate_sidebarwidget_internal_padding_bottom','');
    $sidebarwidget_internal_padding_right        = get_option('wp_estate_sidebarwidget_internal_padding_right','');
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Widget Internal Padding','wpestate').'</div>
    <div class="option_row_explain">'.__('Widget Internal Padding (top,left,bottom,right) ','wpestate').'</div>    
        <input  style="width:100px;min-width:100px;" type="text" id="sidebarwidget_internal_padding_top" name="sidebarwidget_internal_padding_top"  value="'.$sidebarwidget_internal_padding_top.'"/> 
        <input  style="width:100px;min-width:100px;" type="text" id="sidebarwidget_internal_padding_left" name="sidebarwidget_internal_padding_left"  value="'.$sidebarwidget_internal_padding_left.'"/> 
        <input  style="width:100px;min-width:100px;" type="text" id="sidebarwidget_internal_padding_bottom" name="sidebarwidget_internal_padding_bottom"  value="'.$sidebarwidget_internal_padding_bottom.'"/> 
        <input  style="width:100px;min-width:100px;" type="text" id="sidebarwidget_internal_padding_right" name="sidebarwidget_internal_padding_right"  value="'.$sidebarwidget_internal_padding_right.'"/> 
    </div>';
    
    $cache_array                =   array('no','yes');
    $use_same_colors_widgets    =  wpestate_dropdowns_theme_admin($cache_array,'use_same_colors_widgets');
    
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Use the same style for Boxed & Non Boxed widgets ?','wpestate').'</div>
    <div class="option_row_explain">'.__('Use the same style colors for Boxed & Non Boxed widgets? ***colors will be taken from boxed widgets style ','wpestate').'</div>    
        <select id="use_same_colors_widgets" name="use_same_colors_widgets">
            '.$use_same_colors_widgets.'
        </select> 
    </div>';
   
    
    $sidebar_widget_color           =  esc_html ( get_option('wp_estate_sidebar_widget_color','') );
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Sidebar Widget Background Color( for "boxed" widgets)','wpestate').'</div>
    <div class="option_row_explain">'.__('Sidebar Widget Background Color( for "boxed" widgets)','wpestate').'</div>    
        <input type="text" name="sidebar_widget_color" value="'.$sidebar_widget_color.'" maxlength="7" class="inptxt" />
        <div id="sidebar_widget_color" class="colorpickerHolder" ><div class="sqcolor" style="background-color:#'.$sidebar_widget_color.';" ></div></div>
    </div>';
          
        
    $sidebar_heading_boxed_color    =  esc_html ( get_option('wp_estate_sidebar_heading_boxed_color','') );
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Sidebar Heading Color (boxed widgets)','wpestate').'</div>
    <div class="option_row_explain">'.__('Sidebar Heading Color (boxed widgets)','wpestate').'</div>    
        <input type="text" name="sidebar_heading_boxed_color" value="'.$sidebar_heading_boxed_color.'" maxlength="7" class="inptxt" />
        <div id="sidebar_heading_boxed_color" class="colorpickerHolder"><div class="sqcolor" style="background-color:#'.$sidebar_heading_boxed_color.';"></div></div>
    </div>';
         
    $sidebar_heading_color          =  esc_html ( get_option('wp_estate_sidebar_heading_color','') );
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Sidebar Heading Color','wpestate').'</div>
    <div class="option_row_explain">'.__('Sidebar Heading Color','wpestate').'</div>    
        <input type="text" name="sidebar_heading_color" value="'.$sidebar_heading_color.'" maxlength="7" class="inptxt" />
        <div id="sidebar_heading_color" class="colorpickerHolder"><div class="sqcolor" style="background-color:#'.$sidebar_heading_color.';"></div></div>
    </div>';
        
    
    $sidebar_heading_background_color         =  esc_html ( get_option('wp_estate_sidebar_heading_background_color','') );
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Sidebar Heading Background Color','wpestate').'</div>
    <div class="option_row_explain">'.__('Sidebar Heading Background Color','wpestate').'</div>    
        <input type="text" name="sidebar_heading_background_color" value="'.$sidebar_heading_background_color.'" maxlength="7" class="inptxt" />
        <div id="sidebar_heading_background_color" class="colorpickerHolder"><div class="sqcolor" style="background-color:#'.$sidebar_heading_background_color.';"></div></div>
    </div>';
    
    
    $sidebar_boxed_font_color            =  esc_html ( get_option('wp_estate_sidebar_boxed_font_color','') );
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Widget Boxed Font color','wpestate').'</div>
    <div class="option_row_explain">'.__('Widget Boxed Font color','wpestate').'</div>    
        <input type="text" name="sidebar_boxed_font_color" value="'.$sidebar_boxed_font_color.'" maxlength="7" class="inptxt" />
        <div id="sidebar_boxed_font_color" class="colorpickerHolder"><div class="sqcolor" style="background-color:#'.$sidebar_boxed_font_color.';"></div></div>
    </div>';
    
    $sidebar2_font_color            =  esc_html ( get_option('wp_estate_sidebar2_font_color','') );
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Widgets Font color','wpestate').'</div>
    <div class="option_row_explain">'.__('Widgets Font color','wpestate').'</div>    
        <input type="text" name="sidebar2_font_color" value="'.$sidebar2_font_color.'" maxlength="7" class="inptxt" />
        <div id="sidebar2_font_color" class="colorpickerHolder"><div class="sqcolor" style="background-color:#'.$sidebar2_font_color.';"></div></div>
    </div>';
    
    
    
    
    $wp_estate_widget_sidebar_border_size      = get_option('wp_estate_widget_sidebar_border_size','');
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Widget Border Size','wpestate').'</div>
    <div class="option_row_explain">'.__('Widget Border Size','wpestate').'</div>    
        <input  type="text" id="widget_sidebar_border_size " name="widget_sidebar_border_size"  value="'.$wp_estate_widget_sidebar_border_size.'"/> 
    </div>';
    
    
    $widget_sidebar_border_color            =  esc_html ( get_option('wp_estate_widget_sidebar_border_color','') );
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Widget Border Color','wpestate').'</div>
    <div class="option_row_explain">'.__('Widget Border Color','wpestate').'</div>    
        <input type="text" name="widget_sidebar_border_color" value="'.$widget_sidebar_border_color.'" maxlength="7" class="inptxt" />
        <div id="widget_sidebar_border_color" class="colorpickerHolder"><div class="sqcolor" style="background-color:#'.$widget_sidebar_border_color.';"></div></div>
    </div>';
   
    

       
    print ' <div class="estate_option_row_submit">
    <input type="submit" name="submit"  class="new_admin_submit " value="'.__('Save Changes','wpestate').'" />
    </div>';
}
endif;


if (!function_exists('new_wpestate_other_design_details')):
function  new_wpestate_other_design_details(){
    
    $border_radius_corner      = get_option('wp_estate_border_radius_corner','');
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Border Corner Radius','wpestate').'</div>
    <div class="option_row_explain">'.__('Border Corner Radius for unit elements, like property unit, agent unit or blog unit etc','wpestate').'</div>    
        <input  type="text" id="border_radius_corner" name="border_radius_corner"  value="'.$border_radius_corner.'"/> 
    </div>';
    
    
    $cssbox_shadow      = get_option('wp_estate_cssbox_shadow','');
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Box Shadow on elements like property unit ','wpestate').'</div>
    <div class="option_row_explain">'.__('Box Shadow on elements like property unit. Type none for no shadow or put the css values like  0px 2px 0px 0px rgba(227, 228, 231, 1) ','wpestate').'</div>    
        <input  type="text" id="cssbox_shadow" name="cssbox_shadow"  value="'.$cssbox_shadow.'"/> 
    </div>';
    
    
    print ' <div class="estate_option_row_submit">
    <input type="submit" name="submit"  class="new_admin_submit " value="'.__('Save Changes','wpestate').'" />
    </div>';
        
}
endif;

if( !function_exists('new_wpestate_main_menu_design')):
function new_wpestate_main_menu_design(){
 
    
    $menu_font_color                =  esc_html ( get_option('wp_estate_menu_font_color','') );
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Top Menu Font Color','wpestate').'</div>
    <div class="option_row_explain">'.__('Top Menu Font Color','wpestate').'</div>    
        <input type="text" name="menu_font_color" value="'.$menu_font_color.'"  maxlength="7" class="inptxt" />
        <div id="menu_font_color" class="colorpickerHolder" ><div class="sqcolor" style="background-color:#'.$menu_font_color.';" ></div></div>
    </div>';
        
    $top_menu_hover_font_color                =  esc_html ( get_option('wp_estate_top_menu_hover_font_color','') );
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Top Menu Hover Font Color','wpestate').'</div>
    <div class="option_row_explain">'.__('Top Menu Hover Font Color','wpestate').'</div>    
        <input type="text" name="top_menu_hover_font_color" value="'.$top_menu_hover_font_color.'"  maxlength="7" class="inptxt" />
        <div id="top_menu_hover_font_color" class="colorpickerHolder" ><div class="sqcolor" style="background-color:#'.$top_menu_hover_font_color.';" ></div></div>
    </div>';
    
    $transparent_menu_font_color                =  esc_html ( get_option('wp_estate_transparent_menu_font_color','') );
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Transparent Header - Top Menu Font Color','wpestate').'</div>
    <div class="option_row_explain">'.__('Transparent Header - Top Menu Font Color','wpestate').'</div>    
        <input type="text" name="transparent_menu_font_color" value="'.$transparent_menu_font_color.'"  maxlength="7" class="inptxt" />
        <div id="transparent_menu_font_color" class="colorpickerHolder" ><div class="sqcolor" style="background-color:#'.$transparent_menu_font_color.';" ></div></div>
    </div>';
        
    
   
    
    $top_menu_hover_back_font_color                =  esc_html ( get_option('wp_estate_top_menu_hover_back_font_color','') );
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Top Menu Hover Background Color','wpestate').'</div>
    <div class="option_row_explain">'.__('Top Menu Hover Background Color (*applies on some hover types)','wpestate').'</div>    
        <input type="text" name="top_menu_hover_back_font_color" value="'.$top_menu_hover_back_font_color.'"  maxlength="7" class="inptxt" />
        <div id="top_menu_hover_back_font_color" class="colorpickerHolder" ><div class="sqcolor" style="background-color:#'.$top_menu_hover_back_font_color.';" ></div></div>
    </div>';
    
    $transparent_menu_hover_font_color               =  esc_html ( get_option('wp_estate_transparent_menu_hover_font_color','') );
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Transparent Header - Top Menu Hover Font Color','wpestate').'</div>
    <div class="option_row_explain">'.__('Transparent Header - Top Menu Hover Font Color','wpestate').'</div>    
        <input type="text" name="transparent_menu_hover_font_color" value="'.$transparent_menu_hover_font_color.'"  maxlength="7" class="inptxt" />
        <div id="transparent_menu_hover_font_color" class="colorpickerHolder" ><div class="sqcolor" style="background-color:#'.$transparent_menu_hover_font_color.';" ></div></div>
    </div>';
    
    
    $sticky_menu_font_color                =  esc_html ( get_option('wp_estate_sticky_menu_font_color','') );
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Sticky Menu Font Color','wpestate').'</div>
    <div class="option_row_explain">'.__('Sticky Menu Font Color','wpestate').'</div>    
        <input type="text" name="sticky_menu_font_color" value="'.$sticky_menu_font_color.'"  maxlength="7" class="inptxt" />
        <div id="sticky_menu_font_color" class="colorpickerHolder" ><div class="sqcolor" style="background-color:#'.$sticky_menu_font_color.';" ></div></div>
    </div>';
        
    
    $cache_array=array(1,2,3,4,5,6);
    $top_menu_hover_type=  wpestate_dropdowns_theme_admin($cache_array,'top_menu_hover_type');
    
    print'<div class="estate_option_row">
    <img  style="border:1px solid #FFE7E7;margin-bottom:10px;" src="'. get_template_directory_uri().'/img/menu_types.png" alt="logo"/>
                      
    <div class="label_option_row">'.__('Top Menu Hover Type','wpestate').'</div>
    <div class="option_row_explain">'.__('Top Menu Hover Type','wpestate').'</div>    
        <select id="top_menu_hover_type" name="top_menu_hover_type">
            '.$top_menu_hover_type.'
        </select> 
    </div>';
    
    
    $menu_items_color        =  esc_html ( get_option('wp_estate_menu_items_color','') );
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Menu Item Color','wpestate').'</div>
    <div class="option_row_explain">'.__('Menu Item Color','wpestate').'</div>    
        <input type="text" name="menu_items_color" value="'.$menu_items_color.'"  maxlength="7" class="inptxt" />
        <div id="menu_items_color" class="colorpickerHolder" ><div class="sqcolor" style="background-color:#'.$menu_items_color.';" ></div></div>
    </div>';
    
     
    $menu_item_back_color         =  esc_html ( get_option('wp_estate_menu_item_back_color','') );
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Menu Item Back Color','wpestate').'</div>
    <div class="option_row_explain">'.__('Menu Item  Back Color','wpestate').'</div>    
       <input type="text" name="menu_item_back_color" value="'.$menu_item_back_color.'"  maxlength="7" class="inptxt" />
        <div id="menu_item_back_color" class="colorpickerHolder" ><div class="sqcolor" style="background-color:#'.$menu_item_back_color.';"></div></div>
    </div>';
    
    
    $menu_hover_back_color          =  esc_html ( get_option('wp_estate_menu_hover_back_color','') );
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Menu Item Hover Back Color','wpestate').'</div>
    <div class="option_row_explain">'.__('Menu Item Hover Back Color','wpestate').'</div>    
       <input type="text" name="menu_hover_back_color" value="'.$menu_hover_back_color.'"  maxlength="7" class="inptxt" />
        <div id="menu_hover_back_color" class="colorpickerHolder" ><div class="sqcolor" style="background-color:#'.$menu_hover_back_color.';"></div></div>
    </div>';
     
    
    $menu_hover_font_color          =  esc_html ( get_option('wp_estate_menu_hover_font_color','') );
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Menu Item hover font color','wpestate').'</div>
    <div class="option_row_explain">'.__('Menu Item hover font color','wpestate').'</div>    
        <input type="text" name="menu_hover_font_color" value="'.$menu_hover_font_color.'" maxlength="7" class="inptxt" />
        <div id="menu_hover_font_color" class="colorpickerHolder" ><div class="sqcolor" style="background-color:#'.$menu_hover_font_color.';" ></div></div>
    </div>';
    
    
    $wp_estate_top_menu_font_size     = get_option('wp_estate_top_menu_font_size','');
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Top Menu Font Size','wpestate').'</div>
    <div class="option_row_explain">'.__('Top Menu Font Size','wpestate').'</div>    
        <input  type="text" id="top_menu_font_size" name="top_menu_font_size"  value="'.$wp_estate_top_menu_font_size.'"/> 
    </div>';
    
    
    $wp_estate_menu_item_font_size     = get_option('wp_estate_menu_item_font_size','');
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Menu Item Font Size','wpestate').'</div>
    <div class="option_row_explain">'.__('Menu Item Font Size','wpestate').'</div>    
        <input  type="text" id="menu_item_font_size" name="menu_item_font_size"  value="'.$wp_estate_menu_item_font_size.'"/> 
    </div>';
    
    
    $menu_border_color          =  esc_html ( get_option('wp_estate_menu_border_color','') );
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Menu border color','wpestate').'</div>
    <div class="option_row_explain">'.__('Menu border color','wpestate').'</div>    
        <input type="text" name="menu_border_color" value="'.$menu_border_color.'" maxlength="7" class="inptxt" />
        <div id="menu_border_color" class="colorpickerHolder" ><div class="sqcolor" style="background-color:#'.$menu_border_color.';" ></div></div>
    </div>';
    

    $mobile_header_background_color          =  esc_html ( get_option('wp_estate_mobile_header_background_color','') );
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Mobile header background color','wpestate').'</div>
    <div class="option_row_explain">'.__('Mobile header background color','wpestate').'</div>    
        <input type="text" name="mobile_header_background_color" value="'.$mobile_header_background_color.'" maxlength="7" class="inptxt" />
        <div id="mobile_header_background_color" class="colorpickerHolder" ><div class="sqcolor" style="background-color:#'.$mobile_header_background_color.';" ></div></div>
    </div>';   
    
    
    $mobile_header_icon_color          =  esc_html ( get_option('wp_estate_mobile_header_icon_color','') );
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Mobile header icon color','wpestate').'</div>
    <div class="option_row_explain">'.__('Mobile header icon color','wpestate').'</div>    
        <input type="text" name="mobile_header_icon_color" value="'.$mobile_header_icon_color.'" maxlength="7" class="inptxt" />
        <div id="mobile_header_icon_color" class="colorpickerHolder" ><div class="sqcolor" style="background-color:#'.$mobile_header_icon_color.';" ></div></div>
    </div>';  
    
    
    $mobile_menu_font_color          =  esc_html ( get_option('wp_estate_mobile_menu_font_color','') );
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Mobile menu font color','wpestate').'</div>
    <div class="option_row_explain">'.__('Mobile menu font color','wpestate').'</div>    
        <input type="text" name="mobile_menu_font_color" value="'.$mobile_menu_font_color.'" maxlength="7" class="inptxt" />
        <div id="mobile_menu_font_color" class="colorpickerHolder" ><div class="sqcolor" style="background-color:#'.$mobile_menu_font_color.';" ></div></div>
    </div>'; 
    
    
    $mobile_menu_hover_font_color    =esc_html(get_option('wp_estate_mobile_menu_hover_font_color','') );
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Mobile menu hover font color','wpestate').'</div>
    <div class="option_row_explain">'.__('Mobile menu hover font color','wpestate').'</div>    
        <input type="text" name="mobile_menu_hover_font_color" value="'.$mobile_menu_hover_font_color.'" maxlength="7" class="inptxt" />
        <div id="mobile_menu_hover_font_color" class="colorpickerHolder" ><div class="sqcolor" style="background-color:#'.$mobile_menu_hover_font_color.';" ></div></div>
    </div>';
    
    
    $mobile_item_hover_back_color         =  esc_html ( get_option('wp_estate_mobile_item_hover_back_color','') );
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Mobile menu item hover background color','wpestate').'</div>
    <div class="option_row_explain">'.__('Mobile menu item hover background color','wpestate').'</div>    
        <input type="text" name="mobile_item_hover_back_color" value="'.$mobile_item_hover_back_color.'" maxlength="7" class="inptxt" />
        <div id="mobile_item_hover_back_color" class="colorpickerHolder" ><div class="sqcolor" style="background-color:#'.$mobile_item_hover_back_color.';" ></div></div>
    </div>'; 
    

    $mobile_menu_backgound_color = esc_html(get_option('wp_estate_mobile_menu_backgound_color', ''));
    print'<div class="estate_option_row">
    <div class="label_option_row">' . __('Mobile menu background color', 'wpestate') . '</div>
    <div class="option_row_explain">' . __('Mobile menu background color', 'wpestate') . '</div>    
        <input type="text" name="mobile_menu_backgound_color" value="' .$mobile_menu_backgound_color. '" maxlength="7" class="inptxt" />
        <div id="mobile_menu_backgound_color" class="colorpickerHolder" ><div class="sqcolor" style="background-color:#'.$mobile_menu_backgound_color. ';" ></div></div>
    </div>';

    $mobile_menu_border_color = esc_html(get_option('wp_estate_mobile_menu_border_color', ''));
    print'<div class="estate_option_row">
    <div class="label_option_row">' . __('Mobile menu item border color', 'wpestate') . '</div>
    <div class="option_row_explain">' . __('Mobile menu item border color', 'wpestate') . '</div>    
        <input type="text" name="mobile_menu_border_color" value="' . $mobile_menu_border_color . '" maxlength="7" class="inptxt" />
        <div id="mobile_menu_border_color" class="colorpickerHolder" ><div class="sqcolor" style="background-color:#' .$mobile_menu_border_color . ';" ></div></div>
    </div>';
    
    
    print ' <div class="estate_option_row_submit">
    <input type="submit" name="submit"  class="new_admin_submit " value="'.__('Save Changes','wpestate').'" />
    </div>';
}
endif;




if( !function_exists('new_wpestate_property_list_design_details')):
function new_wpestate_property_list_design_details(){
   
    $cache_array =   array(   0 =>__('default','wpestate'),
                                1 =>__('type 1','wpestate'), 
                                2 =>__('type 2','wpestate'), 
                              
                            );
    $unit_card_type=  wpestate_dropdowns_theme_admin_with_key($cache_array,'unit_card_type');
    
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Unit Card Type','wpestate').'</div>
    <div class="option_row_explain">'.__('Unit Card Type','wpestate').'</div>    
        <select id="unit_card_type" name="unit_card_type">
            '.$unit_card_type.'
        </select> 
    </div>';
    
    
    $cache_array=array(3,4);
    $listings_per_row=  wpestate_dropdowns_theme_admin($cache_array,'listings_per_row');
    
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('No of property listings per row when the page is without sidebar','wpestate').'</div>
    <div class="option_row_explain">'.__('When the page is with sidebar the no of listings per row will be 2 or 3 - depending on your selection','wpestate').'</div>    
        <select id="listings_per_row" name="listings_per_row">
            '.$listings_per_row.'
        </select> 
    </div>';
    
    $agent_listings_per_row=  wpestate_dropdowns_theme_admin($cache_array,'agent_listings_per_row');
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('No of agent listings per row when the page is without sidebar','wpestate').'</div>
    <div class="option_row_explain">'.__('When the page is with sidebar the no of listings per row will be 2 or 3 - depending on your selection','wpestate').'</div>    
        <select id="agent_listings_per_row" name="agent_listings_per_row">
            '.$agent_listings_per_row.'
        </select> 
    </div>';
    
      
    $blog_listings_per_row=  wpestate_dropdowns_theme_admin($cache_array,'blog_listings_per_row');
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('No of blog listings per row when the page is without sidebar','wpestate').'</div>
    <div class="option_row_explain">'.__('When the page is with sidebar the no of listings per row will be 2 or 3 - depending on your selection','wpestate').'</div>    
        <select id="blog_listings_per_row" name="blog_listings_per_row">
            '.$blog_listings_per_row.'
        </select> 
    </div>';
    
    
    $prop_unit_min_height     = get_option('wp_estate_prop_unit_min_height','');
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Property Unit/Card min height','wpestate').'</div>
    <div class="option_row_explain">'.__('Property Unit/Card min height ','wpestate').'</div>    
        <input  type="text" id="prop_unit_min_height" name="prop_unit_min_height"  value="'.$prop_unit_min_height.'"/> 
    </div>';
    
    
    $agent_unit_min_height     = get_option('wp_estate_agent_unit_min_height','');
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Agent Unit/Card min height','wpestate').'</div>
    <div class="option_row_explain">'.__('Agent Unit/Card min height(works on agent lists and agent taxonomy) ','wpestate').'</div>    
        <input  type="text" id="agent_unit_min_height" name="agent_unit_min_height"  value="'.$agent_unit_min_height.'"/> 
    </div>';
    
    $blog_unit_min_height     = get_option('wp_estate_blog_unit_min_height','');
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Blog Unit/Card min height','wpestate').'</div>
    <div class="option_row_explain">'.__('Blog Unit/Card min height ','wpestate').'</div>    
        <input  type="text" id="blog_unit_min_height" name="blog_unit_min_height"  value="'.$blog_unit_min_height.'"/> 
    </div>';
      
    $propertyunit_internal_padding_top          = get_option('wp_estate_propertyunit_internal_padding_top','');
    $propertyunit_internal_padding_left         = get_option('wp_estate_propertyunit_internal_padding_left','');
    $propertyunit_internal_padding_bottom       = get_option('wp_estate_propertyunit_internal_padding_bottom','');
    $propertyunit_internal_padding_right        = get_option('wp_estate_propertyunit_internal_padding_right','');
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Property,Agent and Blog Unit/Card Internal Padding','wpestate').'</div>
    <div class="option_row_explain">'.__('Property,Agent and Blog Unit/Card Internal Padding (top,left,bottom,right)','wpestate').'</div>    
        <input  style="width:100px;min-width:100px;" type="text" id="propertyunit_internal_padding_top" name="propertyunit_internal_padding_top"  value="'.$propertyunit_internal_padding_top.'"/> 
        <input  style="width:100px;min-width:100px;" type="text" id="propertyunit_internal_padding_left" name="$propertyunit_internal_padding_left"  value="'.$propertyunit_internal_padding_left.'"/> 
        <input  style="width:100px;min-width:100px;" type="text" id="propertyunit_internal_padding_bottom" name="propertyunit_internal_padding_bottom"  value="'.$propertyunit_internal_padding_bottom.'"/> 
        <input  style="width:100px;min-width:100px;" type="text" id="propertyunit_internal_padding_right" name="propertyunit_internal_padding_right"  value="'.$propertyunit_internal_padding_right.'"/> 
    </div>';
    
    $property_unit_color            =  esc_html ( get_option('wp_estate_property_unit_color','') );
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Property,Agent and Blog Unit/Card Backgrond Color','wpestate').'</div>
    <div class="option_row_explain">'.__('Property,Agent and Blog Unit/Card Backgrond Color','wpestate').'</div>    
        <input type="text" name="property_unit_color" value="'.$property_unit_color .'" maxlength="7" class="inptxt" />
        <div id="property_unit_color" class="colorpickerHolder"><div class="sqcolor" style="background-color:#'.$property_unit_color .';"></div></div>
    </div>';
    
    $unit_border_size     = get_option('wp_estate_unit_border_size','');
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Unit border size','wpestate').'</div>
    <div class="option_row_explain">'.__('Unit border size','wpestate').'</div>    
        <input  type="text" id="unit_border_size" name="unit_border_size"  value="'.$unit_border_size.'"/> 
    </div>';
     
    $unit_border_color            =  esc_html ( get_option('wp_estate_unit_border_color','') );
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Unit/Card border color','wpestate').'</div>
    <div class="option_row_explain">'.__('Unit/Card border color','wpestate').'</div>    
        <input type="text" name="unit_border_color" value="'.$unit_border_color .'" maxlength="7" class="inptxt" />
        <div id="unit_border_color" class="colorpickerHolder"><div class="sqcolor" style="background-color:#'.$unit_border_color .';"></div></div>
    </div>';

    print ' <div class="estate_option_row_submit">
    <input type="submit" name="submit"  class="new_admin_submit " value="'.__('Save Changes','wpestate').'" />
    </div>';
    
}
endif;







if( !function_exists('new_wpestate_property_page_design_details')):
function new_wpestate_property_page_design_details(){
 
    print'<div class="estate_option_row" style="border:none;padding-bottom: 0px!important;">';
        $content        = get_option('wpestate_property_page_content',true);

          
        $wpestate_uset_unit     = intval ( get_option('wpestate_uset_unit','') );
        print'<div class="estate_option_row" style="border:none;">
        <div class="label_option_row" style="margin-left: -230px;">'.__('Use this unit/card','wpestate').'</div>
       
        <input  type="checkbox" id="use_unit_design" name="use_unit_design" ';
            if( $wpestate_uset_unit==1){
                print ' checked="checked" ';
            }
        print ' value="1"/> 
        </div>';

        print '<div>'.__('This property unit builder is a very complex feature, with a lot of options, and because of that it may not work for all design idees. We will continue to improve it, but please be aware that css problems may appear and those will have to be solved by manually adding css rules in the code.').'</div>';
        
        print '<div id="property_page_design_wrapper">';
            print '<div id="property_page_content" class="elements_container">
            <div class="property_page_content_wrapper">'. html_entity_decode ( stripslashes($content) ).'</div>
            <div class="add_me_new_row">+</div></div>';
        print '</div>';
    print'</div>';
     
    
    print ' <div class="estate_option_row_submit">
    <div id="save_prop_design">'.__('Save Design','wpestate').'</div>
  
    </div>';
    
    $extra_elements = array( 
        'Icon'          =>  'icon',
        'Text'          =>  'text',
        'Featured'      =>  'featured_icon',
        'Status'        =>  'property_status',
        'Share'         =>  'share',
        'Favorite'      =>  'favorite',
        'Compare'       =>  'compare',
        'Custom Div'    =>  'custom_div',
        'Link to page'  =>  'link_to_page'
        
        );
    $design_elements=wpestate_all_prop_details_prop_unit();
    
    print '<div class="modal fade" tabindex="-1" role="dialog" id="modal_el_pick">
        <div id="modal_el_pick_close">x</div>';
    
    foreach($extra_elements as $key=>$value){
        print '<div class="prop_page_design_el_modal" data-tip="'.$value.'">'.$key.'</div>';
    }
    
    foreach($design_elements as $key=>$value){
        print '<div class="prop_page_design_el_modal" data-tip="'.$value.'">'.$key.'</div>';
    }
    
    print'
    </div><!-- /.modal -->';
    

    print '<div class="modal fade" tabindex="-1" role="dialog" id="modal_el_options">
        <div class="modal_el_options_content">
            
            <div class="modal_el_options_content_element" id="icon-image-row">
                <label for="icon-image">Icon/Image</label>
                <input type="text" id="icon-image" name="icon-image">
            </div>
            
            <div class="modal_el_options_content_element" id="custom-text-row">
                <label for="text">Text</label>
                <input type="text" id="custom-text" name="custom-text">
            </div>

            <div class="modal_el_options_content_element">
                <label for="margin-top">Margin Top/Top </label>
                <input type="text" id="margin-top" name="margin-top">
            </div>
            
            <div class="modal_el_options_content_element">
                <label for="margin-left">Margin Left/Left </label>
                <input type="text" id="margin-left" name="margin-left">
            </div>

            <div class="modal_el_options_content_element">
                <label for="margin-left">Margin Bottom/Bottom </label>
                <input type="text" id="margin-bottom" name="margin-bottom">
            </div>

            <div class="modal_el_options_content_element">
                <label for="margin-right">Margin Right/Right</label>
                <input type="text" id="margin-right" name="margin-right">
            </div>
            

            
            <div class="modal_el_options_content_element">
                <label for="position-absolute">Position absolute?</label>
                <input type="checkbox" id="position-absolute" value="1">
            </div>
            
            
            <div class="modal_el_options_content_element">
                <label for="font-size">Font Size</label>
                <input type="text" id="font-size" name="font-size">
            </div>
            
            <div class="modal_el_options_content_element">
                <label for="font-color">Font Color</label>
                <input type="text" id="font-color" name="font-color">
            </div>
            
            <div class="modal_el_options_content_element">
                <label for="font-color">Back Color</label>
                <input type="text" id="back-color" name="back-color">
            </div>
            
            

            <div class="modal_el_options_content_element">
                <label for="padding-top">Padding Top</label>
                <input type="text" id="padding-top" name="padding-top">
            </div>
            
            <div class="modal_el_options_content_element">
                <label for="padding-left">Padding Left </label>
                <input type="text" id="padding-left" name="padding-left">
            </div>

            <div class="modal_el_options_content_element">
                <label for="padding-bottom">Padding Bottom</label>
                <input type="text" id="padding-bottom" name="padding-bottom">
            </div>

            <div class="modal_el_options_content_element">
                <label for="padding-right">Padding Right</label>
                <input type="text" id="padding-right" name="padding-right">
            </div>

            <div class="modal_el_options_content_element">
                <label for="padding-right">Align</label>
                <select id="text-align" name="text-align">
                    <option value=""></option>
                    <option value="left">left</option>
                    <option value="right">right</option>
                    <option value="center">center</option>
                </select>
            </div>
            
            <div class="modal_el_options_content_element">
                <label for="extra_css">Extra Css Class</label>
                <input type="text" id="extra_css" name="extra_css">
            </div>

            <input type="button" id="save_custom_el_css" value="apply changes">




        </div>
    <div id="modal_el_options_close">x</div>';
    
    print'
    </div><!-- /.modal -->';
    
}
endif;





if( !function_exists('wpestate_print_page_design')):
function wpestate_print_page_design(){
   
    
    $yesno=array('yes','no');
         
            
    $print_show_subunits           =   wpestate_dropdowns_theme_admin($yesno,'print_show_subunits');
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Show subunits section','wpestate').'</div>
    <div class="option_row_explain">'.__('Show subunits section in print page?','wpestate').'</div>    
        <select id="print_show_subunits" name="print_show_subunits">
            '.$print_show_subunits.'
        </select> 
    </div>';
    
    $print_show_agent           =   wpestate_dropdowns_theme_admin($yesno,'print_show_agent');
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Show agent details section','wpestate').'</div>
    <div class="option_row_explain">'.__('Show agent details section in print page?','wpestate').'</div>    
        <select id="print_show_agent" name="print_show_agent">
            '.$print_show_agent.'
        </select> 
    </div>';
    
    $print_show_description           =   wpestate_dropdowns_theme_admin($yesno,'print_show_description');
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Show description section','wpestate').'</div>
    <div class="option_row_explain">'.__('Show description section in print page?','wpestate').'</div>    
        <select id="print_show_description" name="print_show_description">
            '.$print_show_description.'
        </select> 
    </div>';
    
    
    $print_show_adress           =   wpestate_dropdowns_theme_admin($yesno,'print_show_adress');
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Show address section','wpestate').'</div>
    <div class="option_row_explain">'.__('Show address section in print page?','wpestate').'</div>    
        <select id="print_show_adress" name="print_show_adress">
            '.$print_show_adress.'
        </select> 
    </div>';
    
    
    $print_show_details           =   wpestate_dropdowns_theme_admin($yesno,'print_show_details');
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Show details section','wpestate').'</div>
    <div class="option_row_explain">'.__('Show details section in print page?','wpestate').'</div>    
        <select id="print_show_details" name="print_show_details">
            '.$print_show_details.'
        </select> 
    </div>';
    
    $print_show_features           =   wpestate_dropdowns_theme_admin($yesno,'print_show_features');
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Show features & amenities section','wpestate').'</div>
    <div class="option_row_explain">'.__('Show features & amenities section in print page?','wpestate').'</div>    
        <select id="print_show_features" name="print_show_features">
            '.$print_show_features.'
        </select> 
    </div>';
    
    
    $print_show_floor_plans           =   wpestate_dropdowns_theme_admin($yesno,'print_show_floor_plans');
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Show floor plans section','wpestate').'</div>
    <div class="option_row_explain">'.__('Show floor plans section in print page?','wpestate').'</div>    
        <select id="print_show_floor_plans" name="print_show_floor_plans">
            '.$print_show_floor_plans.'
        </select> 
    </div>';
    
    $print_show_images           =   wpestate_dropdowns_theme_admin($yesno,'print_show_images');
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Show gallery section','wpestate').'</div>
    <div class="option_row_explain">'.__('Show gallery section in print page?','wpestate').'</div>    
        <select id="print_show_images" name="print_show_images">
            '.$print_show_images.'
        </select> 
    </div>';
    
    print ' <div class="estate_option_row_submit">
    <input type="submit" name="submit"  class="new_admin_submit " value="'.__('Save Changes','wpestate').'" />
    </div>';
}
endif;



if(!function_exists('wpestate_user_dashboard_design')):
function wpestate_user_dashboard_design(){ 
    
   $cache_array                =   array('yes','no');   
   $show_header_dashboard      = wpestate_dropdowns_theme_admin($cache_array,'show_header_dashboard');
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Show Header in Dashboard ?','wpestate').'</div>
    <div class="option_row_explain">'.__('Enable or disable header in dashboard. The header will always be wide & type1 !','wpestate').'</div>    
       <select id="show_header_dashboard" name="show_header_dashboard">
            '.$show_header_dashboard.'
        </select>
    </div>';
    
    $user_dashboard_menu_color  =  esc_html ( get_option('wp_estate_user_dashboard_menu_color','') );
    print'<div class="estate_option_row">
        <div class="label_option_row">'.__('User Dashboard Menu Color','wpestate').'</div>
        <div class="option_row_explain">'.__('User Dashboard Menu Color','wpestate').'</div>    
            <input type="text" name="user_dashboard_menu_color" value="'.$user_dashboard_menu_color.'" maxlength="7" class="inptxt" />
            <div id="user_dashboard_menu_color" class="colorpickerHolder" ><div class="sqcolor" style="background-color:#'.$user_dashboard_menu_color.';" ></div></div>
        </div>';
    
    
    $user_dashboard_menu_hover_color      =  esc_html ( get_option('wp_estate_user_dashboard_menu_hover_color','') );
    print'<div class="estate_option_row">
        <div class="label_option_row">'.__('User Dashboard Menu Hover Color','wpestate').'</div>
        <div class="option_row_explain">'.__('User Dashboard Menu Hover Color','wpestate').'</div>    
            <input type="text" name="user_dashboard_menu_hover_color" value="'.$user_dashboard_menu_hover_color.'" maxlength="7" class="inptxt" />
            <div id="user_dashboard_menu_hover_color" class="colorpickerHolder" ><div class="sqcolor" style="background-color:#'.$user_dashboard_menu_hover_color.';" ></div></div>
        </div>';  
    
    $user_dashboard_menu_color_hover  =  esc_html ( get_option('wp_estate_user_dashboard_menu_color_hover','') );
    print'<div class="estate_option_row">
        <div class="label_option_row">'.__('User Dashboard Menu Item Background Color','wpestate').'</div>
        <div class="option_row_explain">'.__('User Dashboard Menu Item Background Color','wpestate').'</div>    
            <input type="text" name="user_dashboard_menu_color_hover" value="'.$user_dashboard_menu_color_hover.'" maxlength="7" class="inptxt" />
            <div id="user_dashboard_menu_color_hover" class="colorpickerHolder" ><div class="sqcolor" style="background-color:#'.$user_dashboard_menu_color_hover.';" ></div></div>
        </div>';
    
    $user_dashboard_menu_back      =  esc_html ( get_option('wp_estate_user_dashboard_menu_back ','') );
    print'<div class="estate_option_row">
        <div class="label_option_row">'.__('User Dashboard Menu Background','wpestate').'</div>
        <div class="option_row_explain">'.__('User Dashboard Menu Background','wpestate').'</div>    
            <input type="text" name="user_dashboard_menu_back" value="'.$user_dashboard_menu_back.'" maxlength="7" class="inptxt" />
            <div id="user_dashboard_menu_back" class="colorpickerHolder" ><div class="sqcolor" style="background-color:#'.$user_dashboard_menu_back .';" ></div></div>
        </div>';
    
    
    $user_dashboard_package_back      =  esc_html ( get_option('wp_estate_user_dashboard_package_back ','') );
    print'<div class="estate_option_row">
        <div class="label_option_row">'.__('User Dashboard Package Background','wpestate').'</div>
        <div class="option_row_explain">'.__('User Dashboard Package Background','wpestate').'</div>    
            <input type="text" name="user_dashboard_package_back" value="'.$user_dashboard_package_back.'" maxlength="7" class="inptxt" />
            <div id="user_dashboard_package_back" class="colorpickerHolder" ><div class="sqcolor" style="background-color:#'.$user_dashboard_package_back .';" ></div></div>
        </div>';
    
    $user_dashboard_package_color     =  esc_html ( get_option('wp_estate_user_dashboard_package_color ','') );
    print'<div class="estate_option_row">
        <div class="label_option_row">'.__('User Dashboard Package Color','wpestate').'</div>
        <div class="option_row_explain">'.__('User Dashboard Package Color','wpestate').'</div>    
            <input type="text" name="user_dashboard_package_color" value="'.$user_dashboard_package_color.'" maxlength="7" class="inptxt" />
            <div id="user_dashboard_package_color" class="colorpickerHolder" ><div class="sqcolor" style="background-color:#'.$user_dashboard_package_color .';" ></div></div>
        </div>';
    
    
    $user_dashboard_buy_package     =  esc_html ( get_option('wp_estate_user_dashboard_buy_package','') );
    print'<div class="estate_option_row">
        <div class="label_option_row">'.__('Dashboard Buy Package Select Background','wpestate').'</div>
        <div class="option_row_explain">'.__('Dashboard Package Selected','wpestate').'</div>    
            <input type="text" name="user_dashboard_buy_package" value="'.$user_dashboard_buy_package.'" maxlength="7" class="inptxt" />
            <div id="user_dashboard_buy_package" class="colorpickerHolder" ><div class="sqcolor" style="background-color:#'.$user_dashboard_buy_package .';" ></div></div>
        </div>';
    
    $user_dashboard_package_select     =  esc_html ( get_option('wp_estate_user_dashboard_package_select','') );
    print'<div class="estate_option_row">
        <div class="label_option_row">'.__('Dashboard Package Select','wpestate').'</div>
        <div class="option_row_explain">'.__('Dashboard Package Select','wpestate').'</div>    
            <input type="text" name="user_dashboard_package_select" value="'.$user_dashboard_package_select.'" maxlength="7" class="inptxt" />
            <div id="user_dashboard_package_select" class="colorpickerHolder" ><div class="sqcolor" style="background-color:#'.$user_dashboard_package_select .';" ></div></div>
        </div>';
    
    $user_dashboard_content_back     =  esc_html ( get_option('wp_estate_user_dashboard_content_back ','') );
    print'<div class="estate_option_row">
        <div class="label_option_row">'.__('Content Background Color','wpestate').'</div>
        <div class="option_row_explain">'.__('Content Background Color','wpestate').'</div>    
            <input type="text" name="user_dashboard_content_back" value="'.$user_dashboard_content_back.'" maxlength="7" class="inptxt" />
            <div id="user_dashboard_content_back" class="colorpickerHolder" ><div class="sqcolor" style="background-color:#'.$user_dashboard_content_back .';" ></div></div>
        </div>';
    
    $user_dashboard_content_button_back     =  esc_html ( get_option('wp_estate_user_dashboard_content_button_back  ','') );
    print'<div class="estate_option_row">
        <div class="label_option_row">'.__('Content Button Background','wpestate').'</div>
        <div class="option_row_explain">'.__('Content Button Background','wpestate').'</div>    
            <input type="text" name="user_dashboard_content_button_back" value="'.$user_dashboard_content_button_back.'" maxlength="7" class="inptxt" />
            <div id="user_dashboard_content_button_back" class="colorpickerHolder" ><div class="sqcolor" style="background-color:#'.$user_dashboard_content_button_back .';" ></div></div>
        </div>';
    
    $user_dashboard_content_color     =  esc_html ( get_option('wp_estate_user_dashboard_content_color','') );
    print'<div class="estate_option_row">
        <div class="label_option_row">'.__('Content Text Color','wpestate').'</div>
        <div class="option_row_explain">'.__('Content Text Color','wpestate').'</div>    
            <input type="text" name="user_dashboard_content_color" value="'.$user_dashboard_content_color.'" maxlength="7" class="inptxt" />
            <div id="user_dashboard_content_color" class="colorpickerHolder" ><div class="sqcolor" style="background-color:#'.$user_dashboard_content_color .';" ></div></div>
        </div>';
    
    
    print ' <div class="estate_option_row_submit">
    <input type="submit" name="submit"  class="new_admin_submit " value="'.__('Save Changes','wpestate').'" />
    </div>';
}
endif;










if(!function_exists('new_property_submission_tab') ):
function new_property_submission_tab(){
    
   
   
    $all_submission_fields  =   wpestate_return_all_fields();
    $all_mandatory_fields   =   wpestate_return_all_fields(1);
  
    
    $submission_page_fields =   ( get_option('wp_estate_submission_page_fields','') );
    if(is_array($submission_page_fields)){
        $submission_page_fields =   array_map("wpestate_strip_array",$submission_page_fields);
    }

       
   


    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Select the Fields for property submission.','wpestate').'</div>
    <div class="option_row_explain">'.__('Use CTRL to select multiple fields for property submission page.','wpestate').'</div>    

        <select id="submission_page_fields" name="submission_page_fields[]" multiple="multiple" style="height:400px">';

        foreach ($all_submission_fields as $key=>$value){
            print '<option value="'.$key.'"';
            if (is_array($submission_page_fields) && in_array($key, $submission_page_fields) ){
                print ' selected="selected" ';
            }
            print '>'.$value.'</option>';
        }    

        print'
        </select>

    </div>';

        
    $mandatory_fields           =   ( get_option('wp_estate_mandatory_page_fields','') );
    
    if(is_array($mandatory_fields)){
        $mandatory_fields           =   array_map("wpestate_strip_array",$mandatory_fields);
    }
    
    print'<div class="estate_option_row">
    <div class="label_option_row">'.__('Select the Mandatory Fields for property submission.','wpestate').'</div>
    <div class="option_row_explain">'.__('Make sure the mandatory fields for property submission page are part of submit form (managed from the above setting). Use CTRL for multiple fields select..','wpestate').'</div>    

        <select id="mandatory_page_fields" name="mandatory_page_fields[]" multiple="multiple" style="height:400px">';

        foreach ($all_mandatory_fields as $key=>$value){
       
            print '<option value="'.stripslashes($key).'"';
            if (is_array($mandatory_fields) && in_array( addslashes($key), $mandatory_fields) ){
                print ' selected="selected" ';
            }
            print '>'.$value.'</option>';
        }    

        print'
        </select>

    </div>';
    
    
    print '<input type="hidden" value="1" name="is_submit_page"> <div class="estate_option_row_submit">
    <input type="submit" name="submit"  class="new_admin_submit " value="'.__('Save Changes','wpestate').'" />
    </div>';
}
endif;



?>