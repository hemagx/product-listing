<?php

use Core\Http\HttpRouter as Router;

Router::addRoute('GET', '/product/list', \App\Resources\Product::class);
Router::addRoute(['DELETE', 'POST'], '/product', \App\Resources\Product::class);
