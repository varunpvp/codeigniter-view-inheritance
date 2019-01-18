# codeigniter-view-inheritance
CodeIgniter View/Template inheritance or scaffolding

This project allows you to implement view inheritance or scaffolding in your CodeIgniter project.
For example, you can have a layout file and make a view extends that layout.


# How to setup view inheritance in you CodeIgniter project

1. Enable hooks in your project, by default they are disabled. To enable hooks, go to `application/config/config.php` and update the hooks to true

 ```
$config['hooks'] = TRUE;
```

2. Register a `display_override` hook in `application/config/hooks.php`

 ```
$hook['display_override'] = [
    'filepath' => 'hooks',
    'filename' => 'ViewCompiler.php',
    'class' => 'ViewCompiler',
    'function' => 'compile'
];
```

3. Create a file for hook in `application/hooks/ViewCompiler.php` in your project and paste the code from this repo's [`application/hooks/ViewCompiler.php`](https://github.com/varunpvp/codeigniter-view-inheritance/blob/master/application/hooks/ViewCompiler.php) file

----
## Usage

Now you project is setup for view inheritance. Next thing to do is created a layout file.

```
<!DOCTYPE html>
<html>
<head>
	<title>My App</title>
	@provide(head)
</head>
<body>
	@provide(content)
</body>
</html>

```

Here `@provide(section_name)` is used to define section that child view can have,
with in brackets you can specify your section name. It should be all lower case alphabet only.

Now in the child view you can do

```
@extends(layouts/site)
@section(content)
<h1>Welcome to My App</h1>
<p>Hi all welcome to my app I hope you will like it.</p>
@endsection
```

* `@extends` specifies the parent layout starting from project's views folder
* `@section(content)` begins the section content
* `@endsection` ends the section content
* everything between `@section(content)` and `@endsection` will be inserted in the
place of `@provide(content)` in the layout view

## Tips

* If one of you controller uses the same layout for all the methods, you can skip
`@extends` in view and define the layout view in controller constructor

 ```
function __construct() {
    parent::__construct();
    $this->layout = 'layouts/site';
}
```

* If layout view is defined in both controller and view. View's layout view be
considered as layout view
* If layout view defines head section but you child doesn't require head section
you can skip it and just defines only the sections you need
