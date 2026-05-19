<?php

$messages = array();
$errors = array();

if (isset($_POST['save'])) {
    $config = array(
        'enabled' => isset($_POST['enabled']) ? (string) ((int) $_POST['enabled']) : '0',
        'title' => isset($_POST['title']) ? trim($_POST['title']) : '',
        'content' => isset($_POST['content']) ? $_POST['content'] : '',
        'button_text' => isset($_POST['button_text']) ? trim($_POST['button_text']) : '',
        'button_url' => isset($_POST['button_url']) ? trim($_POST['button_url']) : '',
        'updated_at' => date('Y-m-d H:i:s'),
    );

    if (!homepage_popup_table_exists()) {
        $errors[] = 'Homepage popup table not found. Please run the database migration first.';
    } else {
        if (isset($_POST['delete_image'])) {
            homepage_popup_delete_image();
        }

        if (isset($_FILES['popup_image']) && !empty($_FILES['popup_image']['tmp_name'])) {
            if (!is_dir(UPLOADS_DIR . 'homepage_popup')) {
                mkdir(UPLOADS_DIR . 'homepage_popup', 0777, true);
            }

            $imgData = pathinfo($_FILES['popup_image']['name']);
            $image = new AdvancedSimpleImage();

            $image->fromFile($_FILES['popup_image']['tmp_name']);
            $targetWidth = 680;
            $targetHeight = 400;
            $sourceWidth = $image->getWidth();
            $sourceHeight = $image->getHeight();
            $sourceRatio = $sourceWidth / $sourceHeight;
            $targetRatio = $targetWidth / $targetHeight;

            if ($sourceRatio > $targetRatio) {
                $image->resize(null, $targetHeight);
            } else {
                $image->resize($targetWidth, null);
            }

            $resizedWidth = $image->getWidth();
            $resizedHeight = $image->getHeight();
            $cropX1 = max(0, (int) floor(($resizedWidth - $targetWidth) / 2));
            $cropY1 = max(0, (int) floor(($resizedHeight - $targetHeight) / 2));

            $image->crop($cropX1, $cropY1, $cropX1 + $targetWidth, $cropY1 + $targetHeight);

            delete_files('popup-image', 'homepage_popup');
            $image->toFile(UPLOADS_DIR . 'homepage_popup' . DIRECTORY_SEPARATOR . 'popup-image-image.' . $imgData['extension']);
        }

        homepage_popup_save_config($config);
        $messages[] = 'Saved successfully.';
    }
}

$data = homepage_popup_get_config();
$image_url = homepage_popup_get_image_url();

?>
<div class="row">
    <div class="col-md-12">
        <?php if(!empty($messages)) {
           show_messages($messages);
        };
        if(!empty($errors)) {
           show_errors($errors);
        };
        ?>
    </div>
</div>
<form class="validate-form" method="post" enctype="multipart/form-data">
    <div class="card mb-4">
        <div class="card-header">
            <h1>Edit Homepage Popup</h1>
        </div>
        <div class="card-body">
            <div class="form-group">
                <label for="enabled">Status:</label>
                <select name="enabled" id="enabled" class="form-control">
                    <option value="1" <?php echo ($data['enabled'] === '1') ? 'selected="selected"' : ''; ?>>Enable</option>
                    <option value="0" <?php echo ($data['enabled'] !== '1') ? 'selected="selected"' : ''; ?>>Disable</option>
                </select>
            </div>
            <div class="form-group">
                <label for="title">Popup Heading:</label>
                <input class="form-control" name="title" id="title" size="50" type="text" value="<?php echo htmlspecialchars($data['title'], ENT_QUOTES, 'UTF-8'); ?>" />
            </div>
            <div class="form-group">
                <label for="content">Popup Content:</label>
                <?php show_fckeditor('content', $data['content']); ?>
            </div>
            <div class="form-group">
                <label for="button_text">Button Text: (optional)</label>
                <input class="form-control" name="button_text" id="button_text" size="50" type="text" value="<?php echo htmlspecialchars($data['button_text'], ENT_QUOTES, 'UTF-8'); ?>" />
            </div>
            <div class="form-group">
                <label for="button_url">Button Link URL: (optional)</label>
                <input class="form-control" name="button_url" id="button_url" size="50" type="text" value="<?php echo htmlspecialchars($data['button_url'], ENT_QUOTES, 'UTF-8'); ?>" />
            </div>
            <div class="form-group">
                <label for="popup_image">Popup Image: (optional, 680px by 400px)</label>
                <input class="form-control" name="popup_image" id="popup_image" size="100" type="file" />
            </div>
            <?php if ($image_url !== false): ?>
                <div class="form-group">
                    <p><img src="<?php echo $image_url; ?>" alt="Homepage popup image" style="max-width: 320px; height: auto;" /></p>
                    <label><input type="checkbox" name="delete_image" value="1" /> Delete current image</label>
                </div>
            <?php endif; ?>
            <div class="form-group">
                <?php show_big_button('save', 'Save'); ?>
            </div>
        </div>
    </div>
</form>
