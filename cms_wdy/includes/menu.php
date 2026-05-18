<?php
$menu = array (
  
  array (
    'module' => 'admin',
    'title' => 'Admin',
    'submenu' => 
    array (
      0 => 
      array (
        'action' => 'add',
        'title' => 'Add',
      ),
      1 => 
      array (
        'action' => 'list',
        'title' => 'Overview',
      ),
    ),
  ),
   
  array (
    'module' => 'page',
    'title' => 'Page',
    'submenu' => 
    array (
      0 => 
      array (
        'action' => 'form',
        'title' => 'Add',
      ),
      1 => 
      array (
        'action' => 'list',
        'title' => 'Overview',
      ),
    ),
  ),
  array (
    'module' => 'homepage_slider',
    'title' => 'Homepage Slider',
    'submenu' => 
    array (
      0 => 
      array (
        'action' => 'form',
        'title' => 'Add',
      ),
      1 => 
      array (
        'action' => 'list',
        'title' => 'Overview',
      ),
    ),
  ),
  array (
    'module' => 'categories',
    'title' => 'Categories',
    'submenu' => 
    array (
      0 => 
      array (
        'action' => 'form',
        'title' => 'Add',
      ),
      1 => 
      array (
        'action' => 'list',
        'title' => 'Overview',
      ),
    ),
  ),
  array (
    'module' => 'products',
    'title' => 'Products',
    'submenu' => 
    array (
      0 => 
      array (
        'action' => 'form',
        'title' => 'Add',
      ),
      1 => 
      array (
        'action' => 'list',
        'title' => 'Overview',
      ),
    ),
  ),
  array (
    'module' => 'orders',
    'title' => 'Orders',
    'submenu' => 
    array (
      0 => 
      array (
        'action' => 'form',
        'title' => 'Add',
      ),
      1 => 
      array (
        'action' => 'list',
        'title' => 'Overview',
      ),
    ),
  ),
  // array (
  //   'module' => 'blocks_of_text',
  //   'title' => 'Blocks Of Text',
  //   'submenu' => 
  //   array (
  //     0 => 
  //     array (
  //       'action' => 'form',
  //       'title' => 'Add',
  //     ),
  //     1 => 
  //     array (
  //       'action' => 'list',
  //       'title' => 'Overview',
  //     ),
  //   ),
  // ),
  // array (
  //   'module' => 'blog',
  //   'title' => 'Blog',
  //   'submenu' => 
  //   array (
  //     0 => 
  //     array (
  //       'action' => 'form',
  //       'title' => 'Add',
  //     ),
  //     1 => 
  //     array (
  //       'action' => 'list',
  //       'title' => 'Overview',
  //     ),
  //   ),
  // ),
  // array (
  //   'module' => 'gallery',
  //   'title' => 'Gallery',
  //   'submenu' => 
  //   array (
  //     0 => 
  //     array (
  //       'action' => 'form',
  //       'title' => 'Edit',
  //     ),
  //   ),
  // ),
  // array (//
  //   'module' => 'videos',
  //   'title' => 'Videos',
  //   'submenu' => 
  //   array (
  //     0 => 
  //     array (
  //       'action' => 'form',
  //       'title' => 'Add',
  //     ),
  //     1 => 
  //     array (
  //       'action' => 'list',
  //       'title' => 'Overview',
  //     ),
  //   ),
  // ),
  array (
    'module' => 'testimonials',
    'title' => 'Testimonials',
    'submenu' => 
    array (
      0 => 
      array (
        'action' => 'form',
        'title' => 'Add',
      ),
      1 => 
      array (
        'action' => 'list',
        'title' => 'Overview',
      ),
    ),
  ),
  array (
    'module' => 'faqs',
    'title' => 'Faqs',
    'submenu' => 
    array (
      0 => 
      array (
        'action' => 'form',
        'title' => 'Add',
      ),
      1 => 
      array (
        'action' => 'list',
        'title' => 'Overview',
      ),
    ),
  ),
  array (
    'module' => 'voucher_codes',
    'title' => 'Voucher Codes',
    'submenu' => 
    array (
      0 => 
      array (
        'action' => 'form',
        'title' => 'Add',
      ),
      1 => 
      array (
        'action' => 'list',
        'title' => 'Overview',
      ),
    ),
  ),
  // array (
  //   'module' => 'contact_forms',
  //   'title' => 'Website Enquiries',
  //   'submenu' => 
  //   array (
  //     0 => 
  //     array (
  //       'action' => 'list',
  //       'title' => 'Overview',
  //     ),
  //   ),
  // ),
  
  array (
    'module' => 'web_forms',
    'title' => 'Web Forms',
    'submenu' => 
    array (
      0 => 
      array (
        'action' => 'manage',
        'title' => 'Manage',
      ),
    ),
  ),
  array (
    'module' => 'store_settings',
    'title' => 'Store Settings',
    'submenu' => 
    array (
      0 => 
      
      array (
        'action' => 'form',
        'title' => 'Manage',
      ),
      
    ),
  ),
);

if(!isset($_GET['module'])) {
  $_GET['module'] = 'page';
  $_GET['action'] = 'list';
}

$selected_module = $_GET['module'];

if (strpos($selected_module, '/') !== false) {
  $selected_module = substr($selected_module, 0, strpos($selected_module, '/'));
}
?>
<!-- Sidebar Holder -->
<nav id="sidebar">
   <!--  <div class="sidebar-header">
        <h3 style="color: #fff;">Menu</h3>
    </div> -->

    <ul class="list-unstyled components">
        <?php
        foreach ($menu as $module) { ?>
        <li <?php echo $selected_module == $module['module'] ? 'class="active"' : ''; ?>>
            <a href="#<?= str_replace(array(' ','/'), '-', $module['title']); ?>Submenu" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle"><?php echo ucfirst($module['title']); ?></a>
            <ul class="collapse list-unstyled actions" id="<?= str_replace(array(' ','/'), '-', $module['title']); ?>Submenu">
            <?php foreach ($module['submenu'] as $item) { ?>
                <li class="<?php printf('%s-%s %s', $module['module'], $item['action'], $item['action']); ?>">
                    <a <?php echo ($_GET['module'] == $module['module'] && (isset($_GET['action']) && $_GET['action'] == $item['action'])) ? 'class="selected"' : ''; ?>
                        href="<?php printf('?module=%s&action=%s', $module['module'], $item['action']); ?>">
                        <?php echo ucfirst($item['title']); ?>
                    </a>
                </li>
            <?php } ?>
            </ul>
        </li>
        <?php } ?>
    </ul>
</nav>
