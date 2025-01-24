<?php
$site_domain = 'site_domain';
$host = 'localhost';
$user = 'db_user';
$password = 'db_password';
$database = 'db_name';
$query = "SELECT pi.image 
          FROM oc_product p 
          LEFT JOIN oc_product_image pi ON p.product_id = pi.product_id 
          WHERE p.manufacturer_id = '8'";

$results = shell_exec("mysql -h $host -u $user -p\"$password\" $database -se \"$query\"");

$data = [];

if (!empty($results)) {
    $rows = explode("\n", trim($results));
    foreach ($rows as $row) {
        $data[] = trim($row);
    }
}

foreach ($data as $result) {
    // в базе данных хранится путь до изображения не полностью, поэтому добавляем.
    $linkimages = 'image/' . $result;
    if (!file_exists($linkimages)) {
        $directories = explode('/', $linkimages);
        $i = 0;
        foreach ($directories as $directory) {
            $inew = $i + 1;
            if (count($directories) > $inew) {
                if ($i !== 0) {
                    $path = '';
                    $prev_dir = $i - 1;
                    foreach (range(0, $prev_dir) as $number) {
                        $path .= $directories[$number] . '/';
                    }
                    if (!is_dir($directory)) {
                        if (!file_exists($path . $directory)) {
                            mkdir($path . '/' . $directory);
                        }
                    }
                }
                $i++;
            }
        }
        $url = '../optom-sumka.ru/' . $linkimages;
        $img = $linkimages;
        shell_exec('cp ' . $url . ' ' . $img);
        echo $img . '<br>';
    }
}
?>
