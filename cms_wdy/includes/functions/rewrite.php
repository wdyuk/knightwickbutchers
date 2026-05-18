<?php

define("REWRITE_TABLE", "url_rewrite");

function cleanURL($url)
{
    $url = str_replace(" ", "-", $url);
    return preg_replace("/[^a-z0-9\-\/]/" ,"",strtolower($url));
}

function validateURL($table,$id,$module,$url)
{
    
    $url = cleanURL($url);
    $tmpurl = $url;
    $count = 0;
    
    $rewriteData = getRewrite($table, $id, $module);
    
    do
    {
        $count++;
        
        $where = array();
        
        if($rewriteData !== false) {
            $where[] = 'id <> ' . $rewriteData['id'];
        }
        
        $where[] = 'url = "' . $tmpurl . '"';

        $tmpdata = table_fetch_row(REWRITE_TABLE, implode(' AND ', $where));
        
        if($tmpdata !== false) 
        {
            
            $tmpurl = $tmpurl . '-' . $count;
            continue;
        }

    }while($tmpdata !== false);
    
    return $tmpurl;
}

function saveRewrite($table, $id, $module, $url)
{
    $data = array();
    $data['table_name'] = $table;
    $data['table_id'] = $id;
    $data['module'] = $module;
    $data['url'] = validateURL($table,$id,$module,$url);
    
    $where = array();
    $where[] = 'table_name = "' . $data['table_name'] . '"';
    $where[] = 'table_id = ' . $data['table_id'];
    $where[] = 'module = "' . $data['module'] . '"';
    
    $rewritedata = table_fetch_row(REWRITE_TABLE, implode(' AND ', $where));
    
    $fields = array('table_name', 'table_id', 'module', 'url');

    if($rewritedata !== false){
        table_update(REWRITE_TABLE, $fields, $data, 'id=' . $rewritedata['id']);
    }
    else{
        table_insert(REWRITE_TABLE, $fields, $data);
    }
}

function getRewrite($table, $id, $module)
{
    $where = array();
    $where[] = 'table_name = "' . $table . '"';
    $where[] = 'table_id = ' . $id;
    $where[] = 'module = "' . $module . '"';

    return table_fetch_row(REWRITE_TABLE, implode(' AND ', $where));
}

function getRewriteUrl($table, $id, $module = '')
{
    $rewritedata = getRewrite($table, $id, $module);
    
    if($rewritedata !== false)
        return $rewritedata['url'];
    else
        return '';
}

function getRewriteByURL($url)
{
    return table_fetch_row(REWRITE_TABLE, 'url = "' . $url . '"');
}

?>
