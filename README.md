## Simple Captcha for Laravel 4
##Installation
```json
{
	"require": {
	    "laravel/framework": "4.0.*",
	    "unodepiera/simplecaptcha": "dev-master"
	},
	"minimum-stability": "dev"
}
```
Update your packages with ```composer update``` or install with ```composer install```.
## Usage
Find the providers key in app/config/app.php and register the Captcha Service Provider.
```json
	'providers' => array(
        //...
        'Unodepiera\Simplecaptcha\SimplecaptchaServiceProvider',
    )
```
Find the aliases key in app/config/app.php.
```json
	'aliases' => array(
        //...
        'Simplecaptcha'  => 'Unodepiera\Simplecaptcha\Facades\Simplecaptcha',
    )
```
Publish assets with this command. 

```$ php artisan asset:publish unodepiera/simplecaptcha```

## Options captcha
You can set the following options for the captcha.
```php
	$defaultOptions = array(
		"font"			=>	"PT_Sans-Web-Regular.ttf",
		"width" 		=> 	250,
		"height" 		=> 	140,
		"font_size" 	=> 	25,
		"length" 		=> 	6,
		"num_lines" 	=> 	"",
		"num_circles" 	=> 	"",
		"text"			=>	"",
		"expiration"	=>	600,
		"directory"		=>	"packages/unodepiera/simplecaptcha/captcha/",
		"dir_fonts"		=>	"packages/unodepiera/simplecaptcha/fonts/",
		"type"			=>	"alpha",
		"bg_color"		=>	"181,181,181",
		"border_color"	=>	"0,0,0"
	);
```

* Font: type font for captcha, view folder public/fonts.
* Num_lines: number lines you would for captcha.
* Num_circles: number circles you would for captcha.
* Expiration: number of seconds it will take to be removed captchas.
* Directory: folder on save captchas.
* Dir_fonts: directory on save the fonts.
* Type: alpha or alphanum.

## Example Usage
```php
	Route::get("form", function()
	{

		$options = array(
			"width"			=>	280,
			"height" 		=> 	100,
			"font_size" 	=> 	28,
			"length" 		=> 	8,
			"num_circles" 	=> 	0,
			"num_lines" 	=> 	4,
			"expiration"	=>  600,
			"bg_color"		=>	"20,20,20"
		);

		$captcha = Simplecaptcha::captcha($options);
		return View::make("form", array("captcha" => $captcha));

	});
	Route::post("process", function()
	{
		$rules =  array('captcha' => array('required', 'captcha'));
	    $validator = Validator::make(Input::all(), $rules);
	    if($validator->fails())
	    {

	    	echo "fails";

	    }else{

	    	echo "success";
	    	
	    }
	});
```
Now you can use the captcha in the view as follows:
```html
    <table>
        {{ Form::open(array('url' => 'process')) }}
        <tr>
	        <td>
	        </td>
	        <td>
	            {{ $captcha["img"] }}
	        </td>
        </tr>
        <tr>
            <td>
                {{ Form::label('captcha', 'Captcha') }}
            </td>
            <td>
                {{ Form::text('captcha') }}
            </td>
        </tr>
        <tr>
            <td>
            </td>
            <td>
                {{ Form::submit('Success') }}
            </td>
        </tr>              
            {{ Form::close() }}
  </table>    
```

## Visit me

* [Visit me](http://uno-de-piera.com)
* [SimpleCaptcha on Packagist](https://packagist.org/packages/unodepiera/simplecaptcha)
* [License](http://www.opensource.org/licenses/mit-license.php)
* [Laravel website](http://laravel.com)
