<?php

// config mysql server
$host = 'localhost';
$user = 'root';
$password = 'r0salinda';
$database = 'demo';

function query($sql, $link) {
    $result = mysql_query($sql, $link);
    if (!$result) {
        echo "DB Error, could not query the database\n";
        echo 'MySQL Error: ' . mysql_error();
        exit;
    }
    return $result;
}

// create connection
$link = mysql_connect($host, $user, $password);
if (!$link) {
    die('Could not connect: ' . mysql_error());
}

// select database
if (!mysql_select_db($database, $link)) {
    echo 'Could not select database';
    exit;
}
$xTable = $_POST['x-table'];
$data = array();
// set order
if (isset($xTable['order_column']) && isset($xTable['order_type'])) {
    $order = " ORDER BY " . mysql_real_escape_string($xTable['order_column']) . " " . mysql_real_escape_string($xTable['order_type']);
}
// row per page
$rowPerPage = (isset($xTable['record_per_page'])) ? (int) $xTable['record_per_page'] : 20;
// page index
$pageIndex = ((int) $xTable['page_index'] > 0) ? (int) $xTable['page_index'] : 1;
// offset
$offset = ($pageIndex - 1) * $rowPerPage;
// limit
$limit = " LIMIT " . $offset . "," . $rowPerPage;

$where = '';
$strAnd = '';

$conditions = $_POST['x-table']['conditions'];

// create sql conditions
if (isset($conditions['rank']) && strlen(trim($conditions['rank'])) > 0) {
    $where .= $strAnd . " rank = '" . mysql_real_escape_string($conditions['rank']) . "' ";
    $strAnd = " AND ";
}
if (isset($conditions['rating']) && strlen(trim($conditions['rating'])) > 0) {
    $where .= $strAnd . " rating = '" . mysql_real_escape_string($conditions['rating']) . "' ";
    $strAnd = " AND ";
}
if (isset($conditions['title']) && strlen(trim($conditions['title'])) > 0) {
    $where .= $strAnd . " title like '%" . mysql_real_escape_string($conditions['title']) . "%' ";
    $strAnd = " AND ";
}
if (isset($conditions['votes']) && strlen(trim($conditions['votes'])) > 0) {
    $where .= $strAnd . " votes = '" . mysql_real_escape_string($conditions['votes']) . "' ";
    $strAnd = " AND ";
}

if (strlen($where) > 0) {
    $where = " WHERE " . $where;
}

$sql = 'SELECT SQL_CALC_FOUND_ROWS * FROM xtable ' . $where . $order . $limit;
//error_log($sql."\n",3,dirname(__FILE__).'/sql.log');
// get data
$result = query($sql, $link);
while ($row = mysql_fetch_assoc($result)) {
    $data[] = $row;
}

$sql = 'SELECT  FOUND_ROWS() as total';
$result = query($sql, $link);

// get number of records
$row = mysql_fetch_assoc($result);

// calculate number total page
$totalPage = ceil($row['total'] / $rowPerPage);

// build return data to x-table
$var = array();
$var['rows'] = $data;
$var['total_page'] = $totalPage;
$var['total_row'] = $row['total'];
$var['page_index'] = $pageIndex;
$var['offset'] = $offset + 1;
$var['row_per_page'] = $rowPerPage;

header('Content-Type: application/json');
// json encode return data
echo json_encode($var);

// close mysql connection
mysql_close($link);
