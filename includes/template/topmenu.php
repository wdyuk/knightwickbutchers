<!-- Menu -->
<nav id="menu">
	<div class="inner">
		<h2>Menu</h2>
		<ul class="links">
			<li><a href="/">Home</a></li>
			<li><a href="/#delivery-radius">Delivery/Collection</a></li>
			<li><a href="/#gallery">Gallery</a></li>
			<li><a href="/#buy">Shop</a></li>
			<li><a href="/faqs">FAQs</a></li>
			<li><a href="/testimonials">Testimonials</a></li>
			<li><a href="/#footer-area">Contact Us</a></li>
			<!-- <?php $parents = table_fetch_rows('page', 'status = 1 AND top_nav = 1 AND parent_id < 0', 'position ASC'); ?>
			<?php if(count($parents) > 0): ?>
			<?php foreach($parents as $key => $parent): ?>
			<li><a href="<?php echo getRewriteUrl('page', $parent['id']); ?>"><?php echo $parent['menu_title']; ?></a></li>
			
			<?php endforeach; ?> -->
		</ul>
		<?php endif; ?>
		<a href="#" class="close">Close</a>
	</div>
</nav>