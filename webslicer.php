<?php
/*
 Plugin Name: webslicer
 Description: Allows you to add webslices.
 Version:     0.7
 Author:      Jack Pacheco
 Plugin URI:  http://webslicer.zobyhost.com/
 Author URI:  http://webslicer.zobyhost.com/


 */


class WebSlicer{
	
	var $webslicer_options = null;
	var $plugin_URL = "";
	
	function WebSlicer(){

		$this->plugin_URL = $webSlicer_plugin_url = trailingslashit( get_bloginfo('wpurl') ).PLUGINDIR.'/'. dirname( plugin_basename(__FILE__) );
		
		add_action('init', array(&$this,'webslicer_init'));	
		add_action('init', array(&$this,'webslicer_widget_register'));
		
		register_activation_hook( __FILE__,  array(&$this,'webslicer_activate' ));
		
	}

	
	//ACTIVATE

	function webslicer_activate(){
		
		$webslicer_options = get_option("webslicer_options");	
		if(!$webslicer_options){
			$webslicer_options = $this->set_default_options();
		}
		
		//retrocompatibility
		$manual = false;
		if($aux_opt = get_option('webslicer_option_title')){$webslicer_options['title'] = $aux_opt; $manual = true;};
		if($aux_opt = get_option('webslicer_option_content')){$webslicer_options['content'] = $aux_opt; $manual = true;};
		if($aux_opt = get_option('webslicer_option_time')){$webslicer_options['time'] = $aux_opt; $manual = true;};
		if($aux_opt = get_option('webslicer_option_feed')){$webslicer_options['feed'] = $aux_opt; $manual = true;};
		if($aux_opt = get_option('webslicer_option_rss')){$webslicer_options['rss'] = $aux_opt; $manual = true;};
		if($aux_opt = get_option('webslicer_option_visible')){$webslicer_options['visible'] = $aux_opt; $manual = true;};
	  	delete_option('webslicer_option_title');
	  	delete_option('webslicer_option_content');
	  	delete_option('webslicer_option_time');
	  	delete_option('webslicer_option_feed');
	  	delete_option('webslicer_option_rss');
	  	delete_option('webslicer_option_visible');
	  	if($manual){
	  		$webslicer_options['manual'] = "1";
	  	}
	  	
	  	update_option("webslicer_options", $webslicer_options);	
	
	}	
	
	
//	INIT:	
	
	function webslicer_init(){

		$this->webslicer_options = get_option("webslicer_options");	
		

		add_action('wp_footer', array(&$this,'webslicer_footer'));
		add_action('admin_menu', array(&$this,'webslicer_admin_menu'));
			
		add_action('wp_head', array(&$this,'webslicer_head'));				
			
		wp_enqueue_script('jquery');
		
		add_shortcode('webslice', array(&$this,'webslicer_shortcode'));				
		add_shortcode('webslice_button', array(&$this,'webslicer_button_shortcode'));		
		
	}
	
	
//	ADMIN:

	function webslicer_admin_menu(){
		add_options_page(__('Webslicer Manager Options'), __('Webslicer'), 5, basename(__FILE__), array(&$this,'webslicer_admin_page'));
	}

	function webslicer_admin_page(){
	
		$updated = false;
	
		if (isset($_POST['webslicer_save'])){

			$webslicer_options= array (
			
				"discover" 	=> $_POST['webslicer_discover'],
			
				"manual" => $_POST['webslicer_manual'],
				"title" => $_POST['webslicer_title'],
				"time" => $_POST['webslicer_time'],
				"rss" => $_POST['webslicer_rss'],
				"visible" => $_POST['webslicer_visible'],
				"content" => $_POST['webslicer_content'],
				"feed" => $_POST['webslicer_feed'],
				"css_title" => $_POST['webslicer_css_title'],
				"css_content" => $_POST['webslicer_css_content'],
				
				"posts" => $_POST['webslicer_posts'],
				"posts_ttl" => $_POST['webslicer_posts_ttl'],			
				"categories" => $_POST['webslicer_categories'],
				"categories_ttl" => $_POST['webslicer_categories_ttl'],
				"tags" => $_POST['webslicer_tags'],
				"tags_ttl" => $_POST['webslicer_tags_ttl'],
				"post_comments" => $_POST['webslicer_post_comments'],
				"post_comments_ttl" => $_POST['webslicer_post_comments_ttl'],
				"author" => $_POST['webslicer_author'],
				"author_ttl" => $_POST['webslicer_author_ttl'],
				"search" => $_POST['webslicer_search'],
				"search_ttl" => $_POST['webslicer_search_ttl'],

				"recents" => $_POST['webslicer_recents'],
				"recents_button" => $_POST['webslicer_recents_button'],
				"recents_value" => $_POST['webslicer_recents_value'],
				"recents_time" => $_POST['webslicer_recents_time'],
				"recents_css" => $_POST['webslicer_recents_css'],
		
				"post_css" => $_POST['webslicer_post_css'],
			
				"hack_css" => $_POST['webslicer_hack_css']
			
			);
			update_option("webslicer_options", $webslicer_options);				
			
			$updated = true;
					
		}
		elseif (isset($_POST['webslicer_default'])){
	
			$webslicer_options = $this->set_default_options();
			update_option("webslicer_options", $webslicer_options);
			
			$updated = true;
			
		}
		else{
		
			$webslicer_options = $this->webslicer_options;
			
		}
	  
		if ($updated){
			?>
			<br/>
			<div class="updated"><p><strong>Options saved.</strong></p></div>
			<?php
		}
						
		
		
	  ?>
	 
	 
	  <div class="wrap">
	
		<h2>Webslicer Settings</h2>
		  
		  
		<form method="post"  action="<?php echo $_SERVER['REQUEST_URI']; ?>">				
			

			<h3>Discovery</h3>
			<p>Can select if discover In-Page when the point is over a webslice is enabling.</p>			
			<table class="form-table">
			<tbody>
				<tr valign="top">
					<th scope="row">
					<label for="webslicer_discover">Discovery</label></th>
					<td>				
						<label><input type="checkbox" value="1" <?php checked('1', $webslicer_options['discover']); ?> id="webslicer_discover" name="webslicer_discover"/>
						Enable In-Page discovery.</label>
					</td>
				</tr>
			</tbody>
			</table>		
			<br/>
			
								
			<h3>Manual webslice</h3>
			<p>This option permits generate a more manual control webslice.</p>		
			<table class="form-table">
			<tbody>
				<tr valign="top">
					<th scope="row"><label for="webslicer_manual">Manual webslice</label></th>
					<td>
						<label>
						<input type="checkbox" value="1" <?php checked('1', $webslicer_options['manual']); ?> id="webslicer_manual" name="webslicer_manual"/>
						Enable manual webslice.
						</label>					
					</td>
				</tr>		
				<tr valign="top">
					<th scope="row"><label for="webslicer_title">Entry title</label></th>
					<td>
						<input type="text" size="40" value="<?php echo $webslicer_options['title']; ?>" id="webslicer_title" name="webslicer_title"/>
						<br/>
						<label for="webslicer_css_title">CSS style:</label>
						<input type="text" value="<?php echo $webslicer_options['css_title']; ?>" id="webslicer_css_title" name="webslicer_css_title" style="width: 100%;" />
					</td>
				</tr>							
				<tr valign="top">
					<th scope="row"><label for="webslicer_content">Entry content</label></th>
					<td>
						<textarea rows="5" cols="50"  id="webslicer_content" name="webslicer_content"><?php echo $webslicer_options['content']; ?></textarea>
						<br/>
						<label for="webslicer_css_content">CSS style:</label>
						<input type="text" value="<?php echo $webslicer_options['css_content']; ?>" id="webslicer_css_content" name="webslicer_css_content" style="width: 100%;" />
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="webslicer_time">Time to live</label></th>
					<td>
						<input type="text" size="10" value="<?php echo $webslicer_options['time']; ?>" id="webslicer_time" name="webslicer_time" style="width: 10%;" />					
					</td>
				</tr>				
				<tr valign="top">
					<th scope="row"><label for="webslicer_option_rss">Feed RSS item based?<br/>
					<small>(-deprecated- show new feature feeds webslices below)</small></label></th>
					<td>
						<label><input type="checkbox" name="webslicer_option_feed" id="webslicer_option_feed" value="1" <?php checked('1', $webslicer_options['feed']); ?> /> 
						Active. </label>
						<br/>
						<label for="webslicer_rss" >RSS address (URL):</label>
						<input type="text" size="40" value="<?php echo $webslicer_options['rss']; ?>" style="width: 85%;" id="webslicer_rss" name="webslicer_rss"/>					
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<label for="webslicer_visible">Show as a widget</label></th>
					<td>
						<label><input type="checkbox" <?php checked('1', $webslicer_options['visible']); ?>  id="webslicer_visible" name="webslicer_visible" value="1" />
						Enable show as a widget.</label>
					</td>
				</tr>		
			</tbody>
			</table>		
			<br/>			
		
		
			<h3>Feeds webslices</h3>
			<p>This option generates webslices based in different types of RSS feeds. 
			Also you can use a widget for generate visible buttons for this.</p>					
			<table class="form-table">
			<tbody>			
				<tr valign="top">
					<th scope="row"><label for="webslicer_posts">Webslice for posts</label></th>
					<td>
						<label>
						<input type="checkbox" <?php checked('1', $webslicer_options['posts']); ?>  id="webslicer_posts" name="webslicer_posts" value="1" />
						Enable weblices for posts.
						</label>
						<label for="webslicer_posts_ttl" >Time to live :</label>
						<input type="text" size="10" value="<?php echo $webslicer_options['posts_ttl']; ?>" style="width: 10%;" id="webslicer_posts_ttl" name="webslicer_posts_ttl"/>					
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="webslicer_categories">Webslice for categories</label></th>
					<td>
						<label>
						<input type="checkbox" <?php checked('1', $webslicer_options['categories']); ?>  id="webslicer_categories" name="webslicer_categories" value="1" />
						Enable weblices for categories.
						</label>
						<label for="webslicer_categories_ttl" >Time to live :</label>
						<input type="text" size="10" value="<?php echo $webslicer_options['categories_ttl']; ?>" style="width: 10%;" id="webslicer_categories_ttl" name="webslicer_categories_ttl"/>					
					</td>
				</tr>				
				<tr valign="top">
					<th scope="row"><label for="webslicer_tags">Webslice for tags</label></th>
					<td>
						<label>
						<input type="checkbox" <?php checked('1', $webslicer_options['tags']); ?>  id="webslicer_tags" name="webslicer_tags" value="1" />
						Enable weblices for tags.
						</label>
						<label for="webslicer_tags_ttl" >Time to live :</label>
						<input type="text" size="10" value="<?php echo $webslicer_options['tags_ttl']; ?>" style="width: 10%;" id="webslicer_tags_ttl" name="webslicer_tags_ttl"/>	
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="webslicer_post_comments">Webslice for post comments</label></th>
					<td>
						<label>
						<input type="checkbox" <?php checked('1', $webslicer_options['post_comments']); ?>  id="webslicer_post_comments" name="webslicer_post_comments" value="1" />
						Enable weblices for post comments.
						</label>
						<label for="webslicer_post_comments_ttl" >Time to live :</label>
						<input type="text" size="10" value="<?php echo $webslicer_options['post_comments_ttl']; ?>" style="width: 10%;" id="webslicer_post_comments_ttl" name="webslicer_post_comments_ttl"/>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="webslicer_author">Webslice for author </label></th>
					<td>
						<label>
						<input type="checkbox" <?php checked('1', $webslicer_options['author']); ?>  id="webslicer_author" name="webslicer_author" value="1" />
						Enable weblices for author.
						</label>
						<label for="webslicer_author_ttl" >Time to live :</label>
						<input type="text" size="10" value="<?php echo $webslicer_options['author_ttl']; ?>" style="width: 10%;" id="webslicer_author_ttl" name="webslicer_author_ttl"/>						
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="webslicer_search">Webslice for search</label></th>
					<td>
						<label>
						<input type="checkbox" <?php checked('1', $webslicer_options['search']); ?>  id="webslicer_search" name="webslicer_search" value="1" />
						Enable weblices for search.
						</label>
						<label for="webslicer_search_ttl" >Time to live :</label>
						<input type="text" size="10" value="<?php echo $webslicer_options['search_ttl']; ?>" style="width: 10%;" id="webslicer_search_ttl" name="webslicer_search_ttl"/>						
					</td>
				</tr>			
			</tbody>
			</table>		
			<br/>
			
					
			<h3>Recents webslices</h3>
			<p>This option generates wrapper webslices for recent comments and posts widgets.</p>	
			<table class="form-table">
			<tbody>
				<tr valign="top">
					<th scope="row"><label for="webslicer_recents">Webslice for recent comments and posts</label></th>
					<td>
						<label>
						<input type="checkbox" <?php checked('1', $webslicer_options['recents']); ?>  id="webslicer_recents" name="webslicer_recents" value="1" />
						Enable weblices for recent comments and posts.
						</label>					
					</td>
				</tr>		
				<tr valign="top">
					<th scope="row">
						<label for="webslicer_recents_button">Show button below</label></th>
					<td>
						<label><input type="checkbox" <?php checked('1', $webslicer_options['recents_button']); ?> id="webslicer_recents_button" name="webslicer_recents_button" value="1" />
						Enable show button below.</label>
						<br/>
						<label for="webslicer_recents_value" >Text for the button</label>
						<input type="text" size="40" value="<?php echo $webslicer_options['recents_value']; ?>"  id="webslicer_recents_value" name="webslicer_recents_value"/>						
					</td>
				</tr>								
				<tr valign="top">
					<th scope="row"><label for="webslicer_recents_time">Time to live for these webslices</label></th>
					<td>
						<input type="text" size="10" value="<?php echo $webslicer_options['recents_time']; ?>" id="webslicer_recents_time" name="webslicer_recents_time" style="width: 10%;" />					
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<label for="webslicer_recents_css">CSS style for these webslices<br/>
							<small>(apply only in the webslice at the Favorites Bar <a href="#note">see note</a>)</small>
						</label>
					</th>
					<td>
						<input type="text" value="<?php echo $webslicer_options['recents_css']; ?>" id="webslicer_recents_css" name="webslicer_recents_css" style="width: 100%;" />					
					</td>
				</tr>
			</tbody>
			</table>		
			<br/>

			<h3>Posts webslices</h3>
			<p>
				With webslicer can use [webslice]...[/webslice] and [webslice_button] in editor for posts and pages.
				<br/>
				These permit use optionals parameters:
			</p>
			<table class="form-table">
			<tbody>
				<tr valign="top">
					<th scope="row">
						For [webslice]...[/webslice]:
					</th>
					<td>
						<ul>
							<li>id: id of the webslice. <small>(default webslice_  + post ID)</small></li>
							<li>title: title of the webslice. <small>(default post title)</small></li>
							<li>ttl: time to live of the webslice. <small>(default 60)</small></li>
						</ul>
					</td>
				</tr>		
				<tr valign="top">
					<th scope="row">
						For [webslice_button]:
					</th>
					<td>
						<ul>
							<li>id: id of the webslice relationed. <small>(default webslice_  + post ID)</small></li>
							<li>title: title for the webslice. <small>(default post title)</small></li>
							<li>value: caption of the button. <small>(default post title)</small></li>
						</ul>						
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<label for="webslicer_post_css">CSS style for this webslice<br/>
							<small>(apply only in the webslice at the Favorites Bar <a href="#note">see note</a>)</small>
						</label>
					</th>
					<td>
						<input type="text" value="<?php echo $webslicer_options['post_css']; ?>" id="webslicer_post_css" name="webslicer_post_css" style="width: 100%;" />					
					</td>
				</tr>
			</tbody>
			</table>							

			<br/>			
			<p>
				<strong><a name="note"></a>Note about CSS style in webslices:</strong><br/>
				Webslice's specifications indicate that only the style of 
				the BODY and not the style of the parent element is apply 
				to the webslice, however you can insert directly 
				style to the entry-content of the webslice, but it is show 
				in the blog. Hence for don’t disturb the global 
				style of the blog, webslicer hacking the style of entry-content 
				for recents and posts webslices to only appear at the favorites bar 
				adding a piece of javascript that fix this. <br/>
				If you don’t want this behaviour you can uncheck this option:
				<label>
				<input type="checkbox" <?php checked('1', $webslicer_options['hack_css']); ?> id="webslicer_hack_css" name="webslicer_hack_css" value="1" />
				Enable CSS javascript hack.
				</label>
			</p> 
			
			
			
					
			<p class="submit">
				<input type="submit" value="Save Changes" name="webslicer_save" class="button-primary" />	
				<br/><br/>
				<input type="submit" value="Set Default" name="webslicer_default" class="button-secondary" />
			</p>
		
		
		</form> 
	  
		</div>
		  
		<?php 
	}

	
//	HEAD:

	function webslicer_head(){

	    if($this->webslicer_options['discover']){
			echo '<meta name="slice" scheme="IE" content="on" />' . "\n";
	    }
	    else{
	    	echo '<meta name="slice" scheme="IE" content="off" />' . "\n";
	    }
	    
	    if($this->is_IE8()){
    		echo '<link type="text/css" href="' . $this->plugin_URL . '/webslicer_button_style.css" rel="stylesheet" />' . "\n";
	    }
	   
		
//	    echo '<script type="text/javascript" language="JavaScript">';
//	    echo 'jQuery(document).ready(function () {';
//		echo 'jQuery("#recent_entries_webslice .entry-content").removeAttr("style");';
//		echo '});';
//		echo '</script>';
		
 
	    
	}

	
//	FOOTER:

	function webslicer_footer(){
		
	    if($this->webslicer_options['manual']){
	    	$this->create_manual_webslice($this->webslicer_options['visible']);
	    }

	    
		if($this->webslicer_options['posts']){
			$this->create_feed_webslice('posts_webslice', get_feed_link(), 'Posts webslice', $this->webslicer_options['posts_ttl']);
		}
		
		if(is_category() && $this->webslicer_options['categories']){
			$cat_id = get_query_var('cat');
			$category = get_category($cat_id);
			$this->create_feed_webslice('categories_webslice', get_category_feed_link($cat_id), $category->name . ' webslice', $this->webslicer_options['categories_ttl']);
		}
		else if(is_tag() && $this->webslicer_options['tags']){
			$tag_id = get_query_var('tag_id');
			$tag = get_tag($tag_id);
			$this->create_feed_webslice('tags_webslice', get_tag_feed_link($tag_id), $tag->name . ' webslice', $this->webslicer_options['tags_ttl']);
		}
		else if(is_author() && $this->webslicer_options['author']){
			$author_id = get_query_var('author');
			$author = get_userdata($author_id);
			$this->create_feed_webslice('author_webslice', get_author_feed_link($author_id), $author->user_nicename . ' webslice', $this->webslicer_options['author_ttl']);
		}
		else if(is_search() && $this->webslicer_options['search']){
			$search_query = get_query_var('s');
			$this->create_feed_webslice('search_webslice', get_search_feed_link($author_id), $search_query . ' search webslice', $this->webslicer_options['search_ttl']);
		}
		else if(is_single() && $this->webslicer_options['post_comments']){
			$post_id = get_query_var('p');		
			$post = get_post($post_id);
			$this->create_feed_webslice('post_comments_webslice', get_post_comments_feed_link($post_id), $post->post_title . ' comments webslice', $this->webslicer_options['post_comments_ttl']);
		}	    
		    
	}
	

//	SHORTCODES POST:
	
	function webslicer_shortcode($atts, $content = null ){
		global $post;
		
		extract(shortcode_atts(array(
			'id' => 'webslice_' . $post->ID,
			'title' => $post->post_title,
			'ttl' => '60',
		), $atts));
	
		$style = "";
		if($this->webslicer_options['post_css']){
			$style = "style=\"" . $this->webslicer_options['post_css'] ."\"";
		}
			
		$webslice_content .= "<div class=\"hslice webslicer\" id=\"$id\"  >\n";
		$webslice_content .= "<p class=\"entry-title\"  style=\"display:none;\">\n";
		$webslice_content .= $title."\n ";
		$webslice_content .= "</p>\n";
		$webslice_content .= "<div class=\"entry-content \" $style >\n ";
		$webslice_content .= $content."\n ";
		$webslice_content .= "</div>\n ";
		$webslice_content .= "<span class=\"ttl\" style=\"display:none;\">\n ";
		$webslice_content .= $ttl."\n ";
		$webslice_content .= "</span>\n ";
		$webslice_content .= "</div>\n";	
	
		if($this->webslicer_options['hack_css']){
		    $webslice_content .= '<script type="text/javascript" language="JavaScript">';
			$webslice_content .= 'jQuery("#'.$id.' .entry-content").removeAttr("style");';
			$webslice_content .= '</script>';
		}
			
		return $webslice_content;
	   
	   
	}
	
	function webslicer_button_shortcode($atts, $content = null ){
		if($this->is_IE8()){    
			global $post;
			
			extract(shortcode_atts(array(
				'id' => 'webslice_' . $post->ID,
				'title' => $post->post_title,
				'value' => 'Webslice for this'
			), $atts));
			
			return $this->create_button($id, $title, $value);
		}	
	}
		
	
//	WIDGETS:
		
	function webslicer_widget_register(){
		
		if($this->webslicer_options['manual'] && $this->webslicer_options['visible']){
			register_sidebar_widget('manual webslice', array(&$this,'webslicer_widget_manual'));		
		}
		
		
		register_sidebar_widget('feeds webslice', array(&$this,'webslicer_widget_feeds'));		
		
				
		//sorry need overwrite core functions :(
		if($this->webslicer_options['recents']){

			$widget_ops = array('classname' => 'widget_recent_comments', 'description' => __( 'The most recent comments' ) );
			wp_register_sidebar_widget('recent-comments', __('Recent Comments'), array(&$this,'webslicer_wp_widget_recent_comments'), $widget_ops);
						
			$widget_ops = array('classname' => 'widget_recent_entries', 'description' => __( "The most recent posts on your blog") );			
			wp_register_sidebar_widget('recent-posts', __('Recent Posts'), array(&$this,'webslicer_wp_widget_recent_entries'), $widget_ops);
			
		}	
	}
	
	function webslicer_widget_manual($args){
	    extract($args);	
	    
    	?>
	  	<?php echo $before_widget; ?>
	        <?php 
	        	echo $this->create_manual_webslice(true);
	        ?>		       
		<?php echo $after_widget; ?>
		<?php
	    
	}
	
	function webslicer_widget_feeds($args){
	    extract($args);
	    
	    if($this->is_IE8()){    
    	?>
	  	<?php echo $before_widget; ?>
	        <?php
	        	
			    if($this->webslicer_options['posts']){					
					echo $this->create_button('posts_webslice', 'posts webslice', 'Webslice for posts');
				}
				
				if(is_category() && $this->webslicer_options['categories']){
					$cat_id = get_query_var('cat');
					$category = get_category($cat_id);					
					echo $this->create_button('categories_webslice', $category->name . ' webslice', 'Webslice for '. $category->name );
				}
				else if(is_tag() && $this->webslicer_options['tags']){
					$tag_id = get_query_var('tag_id');
					$tag = get_tag($tag_id);
					echo $this->create_button('tags_webslice', $tag->name . ' webslice', 'Webslice for '. $tag->name);
				}
				else if(is_author() && $this->webslicer_options['author']){
					$author_id = get_query_var('author');
					$author = get_userdata($author_id);
					echo $this->create_button('author_webslice', $author->user_nicename . ' webslice', 'Webslice for '. $author->user_nicename);
				}
				else if(is_search() && $this->webslicer_options['search']){
					$search_query = get_query_var('s');
					echo $this->create_button('search_webslice', $search_query . ' search webslice', 'Webslice for '. $search_query . ' search ');					
				}
				else if(is_single() && $this->webslicer_options['post_comments']){
					$post_id = get_query_var('p');
					$post = get_post($post_id);
					echo $this->create_button('post_comments_webslice', $post->post_title . ' comments webslice', 'Webslice for '. $post->post_title . ' comments');
				}				
	        ?>		       
		<?php echo $after_widget; ?>
		<?php
		
		}
	    
	}	
	
	
	//overwrite !!!
	function webslicer_wp_widget_recent_comments($args){
		global $wpdb, $comments, $comment;
		extract($args, EXTR_SKIP);
		$options = get_option('widget_recent_comments');
		$title = empty($options['title']) ? __('Recent Comments') : apply_filters('widget_title', $options['title']);
		if ( !$number = (int) $options['number'] )
			$number = 5;
		else if ( $number < 1 )
			$number = 1;
		else if ( $number > 15 )
			$number = 15;
	
		if ( !$comments = wp_cache_get( 'recent_comments', 'widget' ) ) {
			$comments = $wpdb->get_results("SELECT * FROM $wpdb->comments WHERE comment_approved = '1' ORDER BY comment_date_gmt DESC LIMIT $number");
			wp_cache_add( 'recent_comments', $comments, 'widget' );
		}
		
		
		$style = "";
		if($this->webslicer_options['recents_css']){
			$style = "style=\"" . $this->webslicer_options['recents_css'] ."\"";
		}		
		
		?>
	
		<?php echo $before_widget; ?>
		<div class="hslice webslicer" id="recent_comments_webslice" > 
	        <span class="entry-title" >
	       
				<?php echo $before_title . $title . $after_title; ?>
					
			</span>
	        <div class="entry-content" <?php echo $style; ?> >
	        
				<ul id="recentcomments">
				<?php
				if ( $comments ) : foreach ( (array) $comments as $comment) :
				echo  '<li class="recentcomments">' . sprintf(__('%1$s on %2$s'), get_comment_author_link(), '<a href="' . clean_url( get_comment_link($comment->comment_ID) ) . '">' . get_the_title($comment->comment_post_ID) . '</a>') . '</li>';
				endforeach; endif;
				?>
				</ul>  
				      
	        </div>	        
	        <span class="ttl" style="display:none;">
	        <?php  echo $this->webslicer_options['recents_time']; ?>
	        </span>
	    </div>
	    <?php
	    if($this->webslicer_options['hack_css']){
	    ?>
			<script type="text/javascript" language="JavaScript">
				jQuery("#recent_comments_webslice .entry-content").removeAttr("style");
			</script>
	    <?php 
		}
		
	    if($this->webslicer_options['recents_button'] &&  $this->is_IE8()){
			
			echo $this->create_button('recent_comments_webslice', $title, $this->webslicer_options['recents_value']);
			
	    }
		?>	    
		<?php echo $after_widget; ?>
					
		<?php
	}	
	function webslicer_wp_widget_recent_entries($args){
		if ( '%BEG_OF_TITLE%' != $args['before_title'] ) {
			if ( $output = wp_cache_get('widget_recent_entries', 'widget') )
				return print($output);
			ob_start();
		}
	
		extract($args);
		$options = get_option('widget_recent_entries');
		$title = empty($options['title']) ? __('Recent Posts') : apply_filters('widget_title', $options['title']);
		if ( !$number = (int) $options['number'] )
			$number = 10;
		else if ( $number < 1 )
			$number = 1;
		else if ( $number > 15 )
			$number = 15;
	
		$r = new WP_Query(array('showposts' => $number, 'what_to_show' => 'posts', 'nopaging' => 0, 'post_status' => 'publish', 'caller_get_posts' => 1));
		
		$style = "";
		if($this->webslicer_options['recents_css']){
			$style = "style=\"" . $this->webslicer_options['recents_css'] ."\"";
		}
		
		
		if ($r->have_posts()) :
			?>
					<?php echo $before_widget; ?>					
					<div class="hslice webslicer" id="recent_entries_webslice" > 
				        <span class="entry-title" >
						
							<?php echo $before_title . $title . $after_title; ?>
						
						</span>
				        <div class="entry-content" <?php echo $style; ?> >					        		
											
							<ul>
							<?php  while ($r->have_posts()) : $r->the_post(); ?>
							<li><a href="<?php the_permalink() ?>"><?php if ( get_the_title() ) the_title(); else the_ID(); ?> </a></li>
							<?php endwhile; ?>
							</ul>
										
				        </div>
				        <span class="ttl" style="display:none;">
				        <?php  echo $this->webslicer_options['recents_time']; ?>
				        </span>         
				    </div>
				    <?php
				    if($this->webslicer_options['hack_css']){
				    ?>
						<script type="text/javascript" language="JavaScript">
							jQuery("#recent_entries_webslice .entry-content").removeAttr("style");
						</script>
				    <?php 
					}
								    			    
				    if($this->webslicer_options['recents_button'] &&  $this->is_IE8()){
				    	
						echo $this->create_button('recent_entries_webslice', $title, $this->webslicer_options['recents_value']);
						
				    }
					?>
								    				
					<?php echo $after_widget; ?>
			<?php
			wp_reset_query();  // Restore global post data stomped by the_post().
		endif;
	
		if ( '%BEG_OF_TITLE%' != $args['before_title'] )
			wp_cache_add('widget_recent_entries', ob_get_flush(), 'widget');
	}	
	
	
	
//	UTILS
	
	function set_default_options(){
		$webslicer_options= array (
		
			"discover" 	=> "1",
		
			"manual" => "0",
			"title" => "",
			"time"	=> "60",
			"rss" => get_bloginfo('rss2_url'),
			"visible" => "0",
			"content" => "",
			"feed" => "0",
			"css_title" => "",
			"css_content" => "",
		
			"posts" => "1",
			"posts_ttl" => "60",		
			"categories" => "1",
			"categories_ttl" => "60",
			"tags" => "1",
			"tags_ttl" => "60",
			"post_comments" => "1",
			"post_comments_ttl" => "60",
			"author" => "1",
			"author_ttl" => "60",
			"search" => "1",
			"search_ttl" => "60",
		
			"recents" => "1",
			"recents_button" => "1",
			"recents_value" => "Webslice for this",
			"recents_time" => "60",
			"recents_css" => "background: white; height: 100%;",
		
			"post_css" => "background: white; height: 100%;",
		
			"hack_css" => "1"

		);
		return $webslicer_options;		
	}
		
	function create_manual_webslice($visible){
	  	
		$style_title = "";
		if($this->webslicer_options['css_title']){
			$style_title = "style=\"" . $this->webslicer_options['css_title'] ."\"";
		}
		
		$style_content = "";
		if($this->webslicer_options['css_content']){
			$style_content = "style=\"" . $this->webslicer_options['css_content'] ."\"";
		}
				
		if($visible){
	  		?> <div class="hslice webslicer" id="1" > <?php
	  	}
	  	else{
	  		?> <div class="hslice webslicer" id="1" style="display:none;"> <?php
	  	}		
	  	?>
	  	            
	        <p class="entry-title"  <?php echo $style_title; ?> >
				<?php echo $this->webslicer_options['title']; ?>			
			</p>
	        <div class="entry-content" <?php echo $style_content; ?> >
				<?php echo $this->webslicer_options['content']; ?>			            
	        </div>
	        
	        <?php
			  	if($this->webslicer_options['feed']){ 
			  		?>  <a rel="feedurl" href="<?php echo $this->webslicer_options['rss']; ?>" style="display:none;"></a> <?php
			  	}
	        ?>
	        
	        <p style="display:none;"><span class="ttl"><?php echo $this->webslicer_options['time'];?></span></p>         
	    </div>
	 
	   
	     <?php 
	     
	}
	
	function create_feed_webslice($feed_id, $feed_link, $feed_name, $time){

		$feed_link = str_replace("&amp;","&",$feed_link);
	    
		?>
		
	  	<div class="hslice webslicer" id="<?php echo $feed_id; ?>" style="display:none;">
         
	        <span class="entry-title" >
				<?php echo $feed_name; ?>
			</span>
	        <div class="entry-content"  >				
				<span class="webslicer_button" ><?php echo $feed_name; ?></span>             
	        </div>
			<a rel="feedurl" href="<?php echo $feed_link; ?>" ></a> 	        
	        <span class="ttl" style="display:none;"><?php echo $time;?></span>         
	    </div>
	 
	   
	     <?php 
	     
	}
	
	function create_button($webslice_id, $title, $value){
	
		$page_URL = 'http';		
		if($_SERVER["HTTPS"] == "on"){
			$page_URL .= "s";
		}			
		$page_URL .= "://";			
		if($_SERVER["SERVER_PORT"] != "80"){
			$page_URL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
		}
		else{
			$page_URL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
		}
		

		$webslice_button.= "<div>";
   		$webslice_button.= "<a class=\"webslicer_button\" href=\"javascript:return false;\" onclick=\"javascript:window.external.addToFavoritesBar('$page_URL#$webslice_id', '$title', 'slice');\" title=\"$value\">$value</a>\n";
		$webslice_button .= "</div>";
		
		return $webslice_button;

	}	

	function is_IE8(){
		return strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE 8.0');
	}
	
}

$webSlicer = &new WebSlicer();

?>