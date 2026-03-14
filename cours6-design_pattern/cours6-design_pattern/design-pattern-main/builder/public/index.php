<?php
require('../vendor/autoload.php');

use App\MySQLQueryBuilder;

$builder = new MySQLQueryBuilder();

$query1 = $builder
	->select(['id', 'name', 'email'])
	->from('users')
	->where('active = 1')
	->orderBy('name', 'ASC')
	->limit(10)
	->getQuery();

$query2 = $builder
	->reset()
	->select(['id', 'title'])
	->from('posts')
	->where('published = 1')
	->where('category = "tech"')
	->getQuery();

echo '<pre>';
echo $query1 . PHP_EOL;
echo $query2 . PHP_EOL;
echo '</pre>';