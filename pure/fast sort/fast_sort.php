<?php

$sort_by = $_POST['sort_by'];
$products = ; // здесь тестовый массив, просто для простоты восприятия данных. тут можно использовать вариант из вашего движка: можем получать из бд, использовать кеш товаров (страница так то была уже сгенерирована).
// Здесь больше оптимизаторская идея: если бд сильно загружена (в целях экономии нагрузки на бд), и/или у нас ситуация, когда список сохранен в кеше, и мы можем избежать ситуации излишней нагрузки на бд вовсе.
//
