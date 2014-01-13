<h1><?php echo SELF::name ?></h1>
<h2>Simple products</h2>
	<table cellspacing="0" cellpadding="2">
		<thead>
			<tr>	
				<th scope="col" style="text-align:left;"><?php _e('SKU', 'woothemes'); ?></th>
				<th scope="col" style="text-align:left;"><?php _e('Product', 'woothemes'); ?></th>
				<th scope="col" style="text-align:left;"><?php _e('Stock', 'woothemes'); ?></th>
			</tr>
		</thead>
		<tbody>
		<?php

		$args = array(
			'post_type'			=> 'product',
			'post_status' 		=> 'publish',
	        'posts_per_page' 	=> -1,
	        'orderby'			=> 'title',
	        'order'				=> 'ASC',
			'meta_query' 		=> array(
	            array(
	                'key' 	=> '_manage_stock',
	                'value' => 'yes'
	            )
	        ),
			'tax_query' => array(
				array(
					'taxonomy' 	=> 'product_type',
					'field' 	=> 'slug',
					'terms' 	=> array('simple'),
					'operator' 	=> 'IN'
				)
			)
		);
		
		$loop = new WP_Query( $args );
		
		while ( $loop->have_posts() ) : $loop->the_post();
		
                        global $product;
			?>
			<tr>
				<td><?php echo $product->id; ?></td>
				<td><?php echo $product->get_title(); ?></td>
				<td><?php echo $product->stock; ?></td>
			</tr>
			<?php
		endwhile; 
		
		?>
		</tbody>
	</table>
	
	<h2>Variations</h2>
	<table cellspacing="0" cellpadding="2">
		<thead>
			<tr>
				<th scope="col" style="text-align:left;"><?php _e('Parent', 'woothemes'); ?></th>
				<th scope="col" style="text-align:left;"><?php _e('SKU', 'woothemes'); ?></th>
				<th scope="col" style="text-align:left;"><?php _e('Variation', 'woothemes'); ?></th>
				<th scope="col" style="text-align:left;"><?php _e('Stock', 'woothemes'); ?></th>
			</tr>
		</thead>
		<tbody>
		<?php
		
		$args = array(
			'post_type'			=> 'product_variation',
			'post_status' 		=> 'publish',
	        'posts_per_page' 	=> -1,
	        'orderby'			=> 'title',
	        'order'				=> 'ASC',
			'meta_query' => array(
				array(
					'key' 		=> '_stock',
					'value' 	=> array('', false, null),
					'compare' 	=> 'NOT IN'
				)
			)
		);
		$args = array(
			'post_type'			=> 'product_variation',
			'post_status' 		=> 'publish',
	        'posts_per_page' 	=> -1,
	        'orderby'			=> 'title',
	        'order'				=> 'ASC',
			'meta_query' => array(
				array(
					'key' 		=> '_stock'
				)
			)
		);
		
		$loop = new WP_Query( $args );
		global $wpdb;
		$query = "SELECT p.*, a.post_title AS parent_title, a.ID AS parent_id 
						FROM " . $wpdb->posts . " p
						INNER JOIN " . $wpdb->postmeta . " m ON (p.ID = m.post_id)
						INNER JOIN " . $wpdb->posts . " a ON (p.post_parent=a.ID)
						WHERE 1=1 
						AND p.post_type = 'product_variation' 
						AND p.post_status = 'publish' 
						AND m.meta_key = '_stock' 
						GROUP BY p.ID 
						ORDER BY a.post_title ASC";
		
		$rows = $wpdb->get_results($query);
		foreach($rows AS $item) {
			$product = new WC_Product_Variation( $item->ID );
			?>
			<tr<?php if($product->stock < 0){ echo " style=\"background:red\""; } ?>>
				<!--<td><?php echo get_the_title( $item->parent_id ); ?></td>-->
				<td><?php echo $product->get_title(); ?></td>
				<td><?php echo $product->sku; ?></td>
				<td><?php echo implode("/",$product->get_variation_attributes()); ?></td>
				<td><?php echo $product->stock; ?></td>
				<td><button>-</button><input style="width: 30px" type="text" name="stock_<?php echo $product->id ?>" value="<?php echo $product->stock; ?>"/><button>+</button></td>
			</tr>
			<?php
		}; 
		
		?>
		</tbody>
	</table>