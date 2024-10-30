<?php
class Ceneo_Plugin_Popular_Products_Vertical extends WP_Widget {

  // Declaring global plugin values.
  private $plugin_args;
  
  function Urlize_Category_Name($str, $space)
  {
  	$char_table = Array(
  	//WIN
  			"\xb9" => "a", "\xa5" => "A", "\xe6" => "c", "\xc6" => "C",
  			"\xea" => "e", "\xca" => "E", "\xb3" => "l", "\xa3" => "L",
  			"\xf3" => "o", "\xd3" => "O", "\x9c" => "s", "\x8c" => "S",
  			"\x9f" => "z", "\xaf" => "Z", "\xbf" => "z", "\xac" => "Z",
  			"\xf1" => "n", "\xd1" => "N",
  			//UTF
  			"\xc4\x85" => "a", "\xc4\x84" => "A", "\xc4\x87" => "c", "\xc4\x86" => "C",
  			"\xc4\x99" => "e", "\xc4\x98" => "E", "\xc5\x82" => "l", "\xc5\x81" => "L",
  			"\xc3\xb3" => "o", "\xc3\x93" => "O", "\xc5\x9b" => "s", "\xc5\x9a" => "S",
  			"\xc5\xbc" => "z", "\xc5\xbb" => "Z", "\xc5\xba" => "z", "\xc5\xb9" => "Z",
  			"\xc5\x84" => "n", "\xc5\x83" => "N",
  			//ISO
  			"\xb1" => "a", "\xa1" => "A", "\xe6" => "c", "\xc6" => "C",
  			"\xea" => "e", "\xca" => "E", "\xb3" => "l", "\xa3" => "L",
  			"\xf3" => "o", "\xd3" => "O", "\xb6" => "s", "\xa6" => "S",
  			"\xbc" => "z", "\xac" => "Z", "\xbf" => "z", "\xaf" => "Z",
  			"\xf1" => "n", "\xd1" => "N",
  			//I to co nie potrzebne
  			"$" => "-", "!" => "-", "@" => "-", "#" => "-", "%" => "-", " "=>$space);
  
  	return strtolower(strtr($str,$char_table));
  }

  function Ceneo_Plugin_Popular_Products_Vertical() {
     /* Widget settings. */
    $widget_ops = array(
      'classname' => 'ceneoplugpopprodvert',
      'description' => 'Pozwalana na dodanie pionowego widgetu z produktami Ceneo..');

     /* Widget control settings. */
    $control_ops = array(
       'width' => 300,
       'height' => 250,
       'id_base' => 'ceneoplugpopprodvert-widget');

    /* Create the widget. */
   $this->WP_Widget('ceneoplugpopprodvert-widget', 'Produkty Ceneo Pionowo', $widget_ops, $control_ops );
   
   // Assigning global plugin option values to local variable.
   $this->plugin_args = get_option('ceneo_plugin_options');
  }

  function form ($instance) {
     /* Set up some default widget settings. */
    $defaults = array('title' => 'Popularne produkty na Ceneo'
    				);
    				
    $instance = wp_parse_args( (array) $instance, $defaults );  
    ?>
    
    <p>
    	<label for="<?php echo $this->get_field_id('title'); ?>">Tytuł:</label>
    	<input type="text" name="<?php echo $this->get_field_name('title') ?>" id="<?php echo $this->get_field_id('title') ?> " value="<?php echo $instance['title'] ?>" size="30">
    </p>
    
    <p>
    	<label for="<?php echo $this->get_field_id('category_name'); ?>">Kategoria Ceneo:</label>
    	<input type="text" name="<?php echo $this->get_field_name('category_name') ?>" id="<?php echo $this->get_field_id('category_name') ?> " value="<?php echo $instance['category_name'] ?>" size="30">
    </p>
   <p>
   		<label for="<?php echo $this->get_field_id('product_ids'); ?>">Identyfikatory produktów<br/>(po przecinku):</label>
   		<input type="text" name="<?php echo $this->get_field_name('product_ids') ?>" id="<?php echo $this->get_field_id('product_ids') ?> " value="<?php echo $instance['product_ids'] ?>" size="20">
   </p>
   
   <p>
   <label for="<?php echo $this->get_field_id('products_count'); ?>">Ilość wyświetlanych:</label>
   <select id="<?php echo $this->get_field_id('products_count'); ?>" name="<?php echo $this->get_field_name('products_count'); ?>">
   <?php for ($i=1;$i<=10;$i++) {
         echo '<option value="'.$i.'"';
         if ($i==$instance['products_count']) echo ' selected="selected"';
         echo '>'.$i.'</option>';
        } ?>
       </select>
   </p>
	<p>
   		<label for="<?php echo $this->get_field_id('is_rows_numbered'); ?>">Numerowanie produktów:</label>
   		<input type="checkbox" name="<?php echo $this->get_field_name('is_rows_numbered') ?>" id="<?php echo $this->get_field_id('is_rows_numbered') ?> " value="yes" <?php checked($instance["is_rows_numbered"], 'yes'); ?>">
  	</p>
   <?php 
}

function update ($new_instance, $old_instance) {
  $instance = $old_instance;

  $instance['products_count'] = $new_instance['products_count'];
  $instance['title'] = $new_instance['title'];
  $instance['category_name'] = $new_instance['category_name'];
  $instance['product_ids'] = $new_instance['product_ids'];
  $instance['is_rows_numbered'] = $new_instance['is_rows_numbered'];
  
  return $instance;
}

function widget ($args,$instance) {
	
	global $ceneo_api_default_api_key;
	global $ceneo_api_endpoint;	
	global $ceneo_api_method_for_products;
	global $ceneo_api_method_for_categories;
	
	$ceneo_api_key = $this->plugin_args['ceneo_api_key'];
	if(str_replace(" ","",$ceneo_api_key) == '')
	{$ceneo_api_key = $ceneo_api_default_api_key;}
	
   extract($args);

  $title = $instance['title'];
  $products_count = $instance['products_count'];
  $category_name = $instance['category_name'];
  $product_ids =  str_replace (" ", "", $instance['product_ids']);
  $is_rows_numbered = $instance['is_rows_numbered'];
  
    // retrieve product img from database
  //global $wpdb;
 
  $reader = new XMLReader();
  $apiMethod = "";
  
  if($product_ids != "")
  {
  	$apiMethod = $ceneo_api_method_for_products.'/Call?top='.$products_count.'&product_ids_comma_separated='.$product_ids.'&apiKey='.$ceneo_api_key.'&resultFormatter=xml&resultPlainText=true';
  }
  else 
  {
  	$apiMethod = $ceneo_api_method_for_categories.'/Call?category_name='.str_replace(" ","+",urlencode($category_name)).'&top='.$products_count.'&apiKey='.$ceneo_api_key.'&resultFormatter=xml&resultPlainText=true';
  }
  $url = $ceneo_api_endpoint.$apiMethod;
  
  $reader->open($url);
  
  $counter = 0;
  while($reader->read()) {
	if($reader->nodeType == XMLReader::ELEMENT) {
		$name = $reader->name;
	}
 
	if($reader->nodeType == XMLReader::ELEMENT && 
	$reader->name == 'product')
	{
		$counter++;
	}
 
	if($reader->nodeType == XMLReader::TEXT || 
	   $reader->nodeType == XMLReader::CDATA)
	{
		switch($name) {
			case 'pid';
				$instance['pid_'.$counter] = $reader->value;
				break;
			case 'name';
				$instance['pname_'.$counter] = $reader->value;
				break;
			case 'price_min':
				$instance['price_min_'.$counter] = $reader->value;
				break;
			case 'price_max':
				$instance['price_max_'.$counter] = $reader->value;
				break;
			case 'offers_count':
				$instance['offers_count_'.$counter] = $reader->value;
				break;
			case 'url':
				$instance['url_'.$counter] = $reader->value;
				break;
			case 'thumbnail_mini_url':
				$instance['thumbnail_url_'.$counter] = $reader->value;
				break;
		}
	}
  }
  
  //layout do wtyczki 
  $out = '<h3 class="widget-title">';
  $out .= '<a href="http://www.ceneo.pl" target="_blank"><img src="http://image2.ceneo.pl/data/banners/020212/logo-ceneo-mini.png"/></a>'.$title;
  $out .= '</h3>';
  
  $out .= '<div class="ceneo-vertical-plugin">';
			
 $position_count = $counter;
 if($position_count > $products_count)
 {
 	$position_count = $products_count;
 }
 for ($i=1;$i<=$position_count;$i++) 
 {
  	$priceMinFormatted = number_format($instance['price_min_'.$i], 2, ',', ' ');
 	$priceMaxFormatted = number_format($instance['price_max_'.$i], 2, ',', ' ');

 	$out .= '<div class="prodItem">';
  	$out .= '<h4>';
  	if($is_rows_numbered == 'yes')
  	{
  		$out .= $i . '. ';
  	}
  	$out .= '<a href="'.$instance['url_'.$i].'"  target="_blank">'.$instance['pname_'.$i].'</a></h4>'; 
  	$out .= '<a href="'.$instance['url_'.$i].'"  target="_blank" class="prodPix"><img src="'.$instance['thumbnail_url_'.$i].'" /></a>';
	$out .= '<div class="prodDetails">';
 	if($instance['offers_count_'.$i] > 0)
  	{
  		$out .= '<p>od <strong>'.$priceMinFormatted.' zł</strong><br/>';
  		$out .= 'do <strong>'.$priceMaxFormatted.' zł</strong></p>';
  		$out .= '<p class="shopInfo">w '.$instance['offers_count_'.$i].' sklepach</p>';
  	}
	$out .= '</div><div style="clear:both"></div>';
	$out .= '</div>';

 }
  		
  $out .= '</div>';
  if($category_name != "")
  {
  	$out .= 'Więcej z kategorii <a href="http://www.ceneo.pl/'.$this->Urlize_Category_Name($category_name,"_").'" target="_blank">'.$category_name.'  &raquo;</a>';
  }
  else
  {
  	$out .= 'Więcej na <a href="http://www.ceneo.pl" target="_blank">Ceneo &raquo;</a>';
  }
  
  //print the widget for the sidebar
  echo $before_widget;
  //echo $before_title.$title.$after_title;
  echo $out;
  echo $after_widget;
 }
 
 
}
?>