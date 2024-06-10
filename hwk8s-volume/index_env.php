<?php
echo '<h1>ENV</h1>'; 
echo '<hr>'; 

echo '<pre>';
var_dump(\getenv('POSTGRES_PASSWORD'));
var_dump(\getenv('POSTGRES_USER'));
var_dump(\getenv('POSTGRES_DB'));
var_dump(\getenv('POSTGRES_PORT'));
var_dump(\getenv('POSTGRES_HOST'));
echo '</pre>';
