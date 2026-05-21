<?php

$messages = array();
$errors = array();
$dateTypes = blocked_fulfilment_date_types();

if (isset($_POST['save'])) {
    if (!blocked_fulfilment_dates_table_exists()) {
        $errors[] = 'Blocked dates table not found. Please run the database migration first.';
    } else {
        $blockedDate = isset($_POST['blocked_date']) ? preferred_fulfilment_date_normalize($_POST['blocked_date']) : false;
        $fulfilmentType = isset($_POST['fulfilment_type']) ? strtolower(trim($_POST['fulfilment_type'])) : '';

        if ($blockedDate === false) {
            $errors[] = 'Please choose a valid date.';
        }

        if (!isset($dateTypes[$fulfilmentType])) {
            $errors[] = 'Please choose a valid blocked date type.';
        }

        if (empty($errors)) {
            $existing = table_fetch_row(
                blocked_fulfilment_dates_table_name(),
                'blocked_date = "'.$blockedDate.'" AND fulfilment_type = "'.sanitize_sql_string($fulfilmentType).'"'
            );

            if ($existing) {
                $errors[] = 'That date is already blocked for the selected type.';
            } else {
                table_insert(
                    blocked_fulfilment_dates_table_name(),
                    array('blocked_date', 'fulfilment_type', 'created_at'),
                    array(
                        'blocked_date' => $blockedDate,
                        'fulfilment_type' => $fulfilmentType,
                        'created_at' => date('Y-m-d H:i:s'),
                    )
                );
                $messages[] = 'Blocked date saved successfully.';
            }
        }
    }
}

if (isset($_POST['delete_selected'])) {
    if (!blocked_fulfilment_dates_table_exists()) {
        $errors[] = 'Blocked dates table not found. Please run the database migration first.';
    } else {
        $deleteIds = isset($_POST['delete_ids']) && is_array($_POST['delete_ids']) ? $_POST['delete_ids'] : array();
        $deleted = 0;

        foreach ($deleteIds as $deleteId) {
            $deleteId = (int) $deleteId;

            if ($deleteId > 0) {
                $deleted += (int) table_delete_row(blocked_fulfilment_dates_table_name(), 'id = '.$deleteId);
            }
        }

        if ($deleted > 0) {
            $messages[] = 'Selected blocked dates removed successfully.';
        } elseif (empty($deleteIds)) {
            $errors[] = 'Please select at least one blocked date to delete.';
        }
    }
}

$blockedDates = blocked_fulfilment_dates_fetch_all();

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

<form class="validate-form" method="post">
    <div class="card mb-4">
        <div class="card-header">
            <h1>Manage Blocked Dates</h1>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="blocked_date">Blocked Date</label>
                        <input class="form-control datepicker" name="blocked_date" id="blocked_date" type="text" value="<?php echo isset($_POST['blocked_date']) ? htmlspecialchars($_POST['blocked_date'], ENT_QUOTES, 'UTF-8') : ''; ?>" />
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="fulfilment_type">Blocked For</label>
                        <select class="form-control" name="fulfilment_type" id="fulfilment_type">
                            <?php foreach ($dateTypes as $typeValue => $typeLabel): ?>
                                <option value="<?php echo $typeValue; ?>" <?php echo (isset($_POST['fulfilment_type']) && $_POST['fulfilment_type'] === $typeValue) ? 'selected="selected"' : ''; ?>><?php echo $typeLabel; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <div class="form-group">
                        <?php show_big_button('save', 'Add Blocked Date'); ?>
                    </div>
                </div>
            </div>

            <hr>

            <h2>Saved Blocked Dates</h2>
            <?php if (empty($blockedDates)): ?>
                <p>No blocked dates have been added yet.</p>
            <?php else: ?>
                <div class="blocked-date-tags">
                    <?php foreach ($blockedDates as $blockedDate): ?>
                        <?php
                        $type = $blockedDate['fulfilment_type'];
                        $label = isset($dateTypes[$type]) ? $dateTypes[$type] : ucfirst($type);
                        ?>
                        <label class="blocked-date-tag-item">
                            <input type="checkbox" name="delete_ids[]" value="<?php echo (int) $blockedDate['id']; ?>" />
                            <span class="badge <?php echo blocked_fulfilment_dates_badge_class($type); ?>">
                                <?php echo htmlspecialchars(preferred_fulfilment_date_display($blockedDate['blocked_date']), ENT_QUOTES, 'UTF-8'); ?>
                                -
                                <?php echo htmlspecialchars($label, ENT_QUOTES, 'UTF-8'); ?>
                            </span>
                        </label>
                    <?php endforeach; ?>
                </div>
                <div class="form-group mt-3">
                    <?php show_big_button('delete_selected', 'Delete Selected', 'btn-danger'); ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</form>

<style>
    .blocked-date-tags {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 15px;
    }

    .blocked-date-tag-item {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 0;
        cursor: pointer;
    }

    .blocked-date-tag-item .badge {
        padding: 8px 12px;
        font-size: 0.95rem;
    }
</style>

<script type="text/javascript">
$(function() {
    $('.datepicker').datepicker({
        dateFormat : 'dd/mm/yy'
    });
});
</script>
