<?php

function search_escape_like($value)
{
    return str_replace(array('\\', '%', '_'), array('\\\\', '\\%', '\\_'), $value);
}

function search_highlight_excerpt($text, $term, $length = 180)
{
    $plain = trim(preg_replace('/\s+/', ' ', html_entity_decode(strip_tags((string) $text), ENT_QUOTES, 'UTF-8')));

    if ($plain === '') {
        return '';
    }

    if ($term === '') {
        return mb_substr($plain, 0, $length);
    }

    $plainLower = mb_strtolower($plain);
    $termLower = mb_strtolower($term);
    $position = mb_strpos($plainLower, $termLower);

    if ($position === false) {
        return mb_substr($plain, 0, $length);
    }

    $start = max(0, $position - (int) floor($length / 3));
    $excerpt = mb_substr($plain, $start, $length);

    if ($start > 0) {
        $excerpt = '...' . $excerpt;
    }

    if (($start + mb_strlen($excerpt)) < mb_strlen($plain)) {
        $excerpt .= '...';
    }

    return $excerpt;
}

function search_result_image($type, $id)
{
    if (!in_array($type, array('product', 'category'))) {
        return false;
    }

    $image = get_image($type . '/' . $id);

    if (!$image) {
        return '/assets/theme/img/placeholder.jpg';
    }

    return $image . '?v=' . date('Y-m');
}

function search_page_results($term)
{
    $fields = get_table_fields('page');
    $searchable_fields = array();

    foreach (array('page_title', 'menu_title', 'h1_title', 'content', 'content_2', 'content_3', 'content_4', 'meta_description') as $field) {
        if (isset($fields[$field])) {
            $searchable_fields[] = $field;
        }
    }

    if (empty($searchable_fields)) {
        return array();
    }

    $escaped = sanitize_sql_string(search_escape_like($term));
    $where_parts = array();

    foreach ($searchable_fields as $field) {
        $where_parts[] = sprintf('%s LIKE "%%%s%%"', $field, $escaped);
    }

    $rows = table_fetch_rows('page', 'status = 1 AND (' . implode(' OR ', $where_parts) . ')', 'position ASC');
    $results = array();

    foreach ($rows as $row) {
        $content_parts = array();

        foreach (array('content', 'content_2', 'content_3', 'content_4', 'meta_description') as $field) {
            if (isset($row[$field]) && strlen(trim(strip_tags($row[$field]))) > 0) {
                $content_parts[] = $row[$field];
            }
        }

        $title = '';

        foreach (array('h1_title', 'page_title', 'menu_title') as $field) {
            if (isset($row[$field]) && strlen(trim($row[$field])) > 0) {
                $title = $row[$field];
                break;
            }
        }

        if ($title === '') {
            $title = 'Page';
        }

        $results[] = array(
            'type' => 'Information',
            'title' => $title,
            'url' => getRewriteUrl('page', $row['id']),
            'excerpt' => search_highlight_excerpt(implode(' ', $content_parts), $term),
        );
    }

    return $results;
}

function search_product_results($term)
{
    $fields = get_table_fields('products');

    if (!isset($fields['name'])) {
        return array();
    }

    $searchable_fields = array('name');

    foreach (array('description', 'meta_description') as $field) {
        if (isset($fields[$field])) {
            $searchable_fields[] = $field;
        }
    }

    $escaped = sanitize_sql_string(search_escape_like($term));
    $where_parts = array();

    foreach ($searchable_fields as $field) {
        $where_parts[] = sprintf('%s LIKE "%%%s%%"', $field, $escaped);
    }

    $status_filter = isset($fields['status']) ? 'status = 1 AND ' : '';
    $rows = table_fetch_rows('products', $status_filter . '(' . implode(' OR ', $where_parts) . ')', 'position ASC');
    $results = array();

    foreach ($rows as $row) {
        $description = '';

        foreach (array('description', 'meta_description') as $field) {
            if (isset($row[$field]) && strlen(trim(strip_tags($row[$field]))) > 0) {
                $description = $row[$field];
                break;
            }
        }

        $results[] = array(
            'type' => 'Product',
            'title' => $row['name'],
            'url' => getRewriteUrl('product', $row['id']),
            'excerpt' => search_highlight_excerpt($description, $term),
            'image' => search_result_image('product', $row['id']),
        );
    }

    return $results;
}

function search_category_results($term)
{
    $fields = get_table_fields('categories');

    if (!isset($fields['name'])) {
        return array();
    }

    $searchable_fields = array('name');

    foreach (array('description', 'meta_description') as $field) {
        if (isset($fields[$field])) {
            $searchable_fields[] = $field;
        }
    }

    $escaped = sanitize_sql_string(search_escape_like($term));
    $where_parts = array();

    foreach ($searchable_fields as $field) {
        $where_parts[] = sprintf('%s LIKE "%%%s%%"', $field, $escaped);
    }

    $status_filter = isset($fields['status']) ? 'status = 1 AND ' : '';
    $rows = table_fetch_rows('categories', $status_filter . '(' . implode(' OR ', $where_parts) . ')', 'position ASC');
    $results = array();

    foreach ($rows as $row) {
        $description = '';

        foreach (array('description', 'meta_description') as $field) {
            if (isset($row[$field]) && strlen(trim(strip_tags($row[$field]))) > 0) {
                $description = $row[$field];
                break;
            }
        }

        $results[] = array(
            'type' => 'Category',
            'title' => $row['name'],
            'url' => getRewriteUrl('category', $row['id']),
            'excerpt' => search_highlight_excerpt($description, $term),
            'image' => search_result_image('category', $row['id']),
        );
    }

    return $results;
}

function search_faq_results($term)
{
    $fields = get_table_fields('faqs');

    if (!isset($fields['question']) || !isset($fields['answer'])) {
        return array();
    }

    $escaped = sanitize_sql_string(search_escape_like($term));
    $status_filter = isset($fields['status']) ? 'status = 1 AND ' : '';
    $where = $status_filter . '(question LIKE "%' . $escaped . '%" OR answer LIKE "%' . $escaped . '%")';
    $rows = table_fetch_rows('faqs', $where, 'position ASC');
    $results = array();

    foreach ($rows as $row) {
        $results[] = array(
            'type' => 'FAQ',
            'title' => $row['question'],
            'url' => '/faqs',
            'excerpt' => search_highlight_excerpt($row['answer'], $term),
        );
    }

    return $results;
}

function search_delivery_results($term, $store_settings, $home_page)
{
    $haystack = array(
        'delivery',
        'collection',
        'deliver',
        'collect',
        isset($store_settings['company_collect_text']) ? $store_settings['company_collect_text'] : '',
        isset($store_settings['company_collect_address']) ? $store_settings['company_collect_address'] : '',
        isset($home_page['content_2']) ? $home_page['content_2'] : '',
    );

    $joined = implode(' ', $haystack);
    $termLower = mb_strtolower($term);
    $joinedLower = mb_strtolower(strip_tags($joined));

    if ($term === '' || mb_strpos($joinedLower, $termLower) === false) {
        return array();
    }

    return array(
        array(
            'type' => 'Information',
            'title' => 'Delivery Information',
            'url' => '/#delivery',
            'excerpt' => search_highlight_excerpt($joined, $term),
        )
    );
}

function search_site($term, $store_settings)
{
    $term = trim($term);

    if ($term === '') {
        return array();
    }

    $home_page = table_fetch_row('page', 'status = 1 AND page_title = "Home"');
    $results = array_merge(
        search_product_results($term),
        search_category_results($term),
        search_page_results($term),
        search_faq_results($term),
        search_delivery_results($term, $store_settings, $home_page ? $home_page : array())
    );

    $deduped = array();
    $seen = array();

    foreach ($results as $result) {
        $key = $result['type'] . '|' . $result['title'] . '|' . $result['url'];

        if (isset($seen[$key])) {
            continue;
        }

        $seen[$key] = true;
        $deduped[] = $result;
    }

    return $deduped;
}

?>
