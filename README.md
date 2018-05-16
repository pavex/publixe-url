# publixe-url
Url and Url script container

Here is a small example:
```php
use Publixe\Url;

$url_string = 'https://user:password@domain.ltd:443/folder/script.ext';
$url = new Url($url_string);

var_dump($url);
```
