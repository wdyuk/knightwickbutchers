<?php 
	$properties = table_fetch_rows('properties', 'end_date >= "' . date('Y-m-d') . '"', 'position ASC', 0,3);  
	if(count($properties)):
?>
<div id="property_auction">
	<h2>Current Property Auctions</h2>

	<?php 
		foreach($properties as $property): 
			$image = get_image('properties/' . $property['id'] . '-small');
	?>
    
	<div class="auction_text">
    <a href="<?php echo getRewriteUrl('properties', $property['id']); ?>" >
		<img src="<?php echo !empty($image) ? $image : 'assets/images/Layer23.png'; ?>" class="auction_images"></a>
        <div class="clear"></div>
        <div style="min-height: 60px;">
		<h3><?php echo $property['title'] ?></h3>
       </div>
		<?php 
			$content = strip_tags($property['description']);
          
			if(strlen($content) > 300)
			echo substr($content,0,300) . '...';
			else
			echo $content;
			
		?>
		<h4>Guide price: <?php echo $property['price_range'] ?></h4>
	</div>
	<?php endforeach; ?>

</div>
<div class="clear"></div>
<?php endif; ?>
