### Requirements
- PHP 7+
- Composer

### Use
1. Install **composer dependency** on terminal
   ```bash
   composer install
   ```
2. Create file **.env**

    Get key from [key generator for KOMA-MVC](https://komabikinapp.000webhostapp.com/key-generator.php)

3. Create file **.htaccess**
4. Import database sql

## Snippets
### Database instance static
```php
$db = Database::getInstance();
```
### Plain SQL script execution
```php
$db = Database::getInstance();
$sql = "SELECT * FROM admin";
$db = $db->query($sql);
```
By default data will be retrieved in the form of ARRAY, you can add an optional attribute to the method to change the format of the data to be retrieved.
```php
$db->query($sql, 'ARRAY_ONE');
// Retrieve a data in object array

$db->query($sql, 'NUM_ROWS');
// Retrieve the number of rows of data

$db->query($sql, 'SQL');
// Retrieve the plain query SQL

$db->query($sql, 'BOOLEAN');
// Retrieve true/false from query
```

### Request
- Post request
  ```php
  $data = $this->request()->post;
  ```
- Get request
  ```php
  $data = $this->request()->get;
  ```