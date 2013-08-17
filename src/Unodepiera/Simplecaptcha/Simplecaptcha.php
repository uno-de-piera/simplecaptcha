<?php
 
namespace Unodepiera\Simplecaptcha;
 
use Str, Session;

/**
 *
 * Laravel 4 Simplecaptcha package
 * @version 1.0.0
 * @copyright Copyright (c) 2013 Unodepiera
 * @author Israel Parra
 * @contact unodepiera@uno-de-piera.com
 * @link http://www.uno-de-piera.com
 * @date 2013-03-27
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 */

class Simplecaptcha
{
 
 	/**
	 * Generator lines in the captcha.
	 *
	 * @access private  
	 * @param  array   		$options
	 * @param  resource  	$imgCaptcha
	 */
    private function createNumLinesCaptcha($options = array(),$imgCaptcha)
	{
		for($i=1; $i<=$options["num_lines"]; $i++) 
		{
		    //colores aleatorios para la imagen
		    $colorA = mt_rand(0,180);
		    $colorB = mt_rand(0,180);
		    $colorC = mt_rand(0,180);
			$x1 = mt_rand(1, 100);//distancia izquierda
			$y1 = mt_rand(1, 100);//distancia arriba izquierda
			$x2 = mt_rand(50, $options["width"]);//largo de la línea
			$y2 = mt_rand(1, $options["height"]);//inclinación hacia abajo
			//pintamos tanta líneas como haya puesto el usuario
			imageline($imgCaptcha, $x1, $y1, $x2, $y2, imagecolorallocate ($imgCaptcha, $colorA, $colorB, $colorC));
		}
	}


	/**
	 * Generator circles in the captcha.
	 *
	 * @access private  
	 * @param  array   		$options
	 * @param  resource  	$imgCaptcha
	 */
	private function createNumCirclesCaptcha($options = array(),$imgCaptcha)
	{
		for($i=1; $i<=$options["num_circles"]; $i++) 
		{
		    $colorA = mt_rand(30,150);
		    $colorB = mt_rand(30,150);
		    $colorC = mt_rand(30,150);
		    $left = mt_rand(20,$options["width"]);
		    $top = mt_rand(10, $options["height"] - 30);
		    $_width = mt_rand(10, 40);
		    $_height = mt_rand(10, 40);
			$circlesColor = imagecolorallocate($imgCaptcha, $colorA, $colorB, $colorC);
			//pintamos tantos circulos como haya puesto el usuario
			imagefilledellipse($imgCaptcha, $left, $top, $_width, $_height, $circlesColor);
		}
	}

	/**
	 * Generate a "random" alpha-numeric string.
	 *
	 * @access private  
	 * @param  array   $options
	 * @param  string  $type
	 * @return string
	 */
	private function createAlphaCaptcha($options,$type)
	{
		if($type == "alpha")
		{
			$alph = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		}else{
			$alph = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		}
		

		$string = '';
		for ($i = 0; $i < $options["length"]; $i++)
		{
			$string .= substr($alph, mt_rand(0, strlen($alph)-1), 1);
		}

		$options["text"] = $string;

		return $options["text"];
	}

	/**
	 * Delete olds captchas files.
	 *
	 * @access private  
	 * @param  array   		$options
	 * @param  int  		$unix
	 */
	private function deleteOldFiles($options = array(),$unix)
	{
		$files = glob($options["directory"] . "{*.png}", GLOB_BRACE);
        foreach ($files as $file) 
        {
        	$file_rep1 = str_replace($options["directory"], "", $file);
        	$file_rep2 = str_replace(".png", "", $file_rep1);
        	if (($file_rep2 + $options["expiration"]) < $unix)
			{
				@unlink($options["directory"].$file_rep1);
			}
        }
	}

	/**
	 * Push random chars into captcha.
	 *
	 * @access private  
	 * @param  array   		$options
	 * @param  resource  	$imgCaptcha
	 * @param  boolean		$fonts
	 */
	private function pushCharsIntoCaptcha($options = array(),$imgCaptcha, $fonts)
	{
		$posLetters = ($options["width"]-50) / $options["length"];
	    $pos = (20 - $posLetters);
		for($i = 0; $i < $options["length"]; $i++)
		{
		    //colores para las letras de la imagen
		    $colorA = mt_rand(40,200);
	        $colorB = mt_rand(40,200);
	    	$colorC = mt_rand(40,200);
		    $random = mt_rand(0,1);

		    //si random es 0 damos un angulo de 0 a 30 grados
		    if($random == 0)
		    {
		    	$angulo = mt_rand(0,20);
		    }else{
		    	//si es 1 giramos de 320 a 360
		    	$angulo = mt_rand(340,360);
		    }

		    $pos += $posLetters;

		    //si el captcha tiene un archivo con fuentes
		    if($fonts == true)
		    {
		    	imagettftext($imgCaptcha, $options["font_size"], $angulo, $pos, mt_rand($options["height"]/2,$options["height"]/1.5), 
		    	imagecolorallocate ($imgCaptcha, $colorA, $colorB, $colorC), $options["dir_fonts"].$options["font"], 
		    	substr($options["text"], $i, 1));
		    //si no tiene archivo con fuentes
		    }else{
		    	imagestring($imgCaptcha, $options["font_size"], $pos, mt_rand(40, $options["height"] - 30), 
		    	substr($options["text"], $i, 1), imagecolorallocate ($imgCaptcha, $colorA, $colorB, $colorC));
		    }
		    
		}
	}

	/**
	 * Generate the captcha.
	 *
	 * @access public  
	 * @param  array   	$options
	 * @return array
	 */
	public function captcha($options = array())
	{

		//opciones por defecto que queremos dar al captcha
		$defaultOptions = array(
			"font"			=>	"PT_Sans-Web-Regular.ttf",
			"width" 		=> 	250,
			"height" 		=> 	140,
			"font_size" 	=> 	25,
			"length" 		=> 	6,
			"num_lines" 	=> 	"",
			"num_circles" 	=> 	"",
			"text"			=>	"",
			"expiration"	=>	10,
			"directory"		=>	"packages/unodepiera/simplecaptcha/captcha/",
			"dir_fonts"		=>	"packages/unodepiera/simplecaptcha/fonts/",
			"type"			=>	"alpha",
			"bg_color"		=>	"181,181,181",
			"border_color"	=>	"0,0,0"
		);

		//le damos valores por defecto a nuestro captcha por 
		//si el usuario no se las da
		foreach ($defaultOptions as $key => $value) 
		{
			if(!array_key_exists($key, $options))
			{
				$options[$key] = $defaultOptions[$key];
			}
		}

		$imgCaptcha = imagecreatetruecolor($options["width"], $options["height"]);

		/*colores para el captcha*/
		$bgcolors = explode(",", $options["bg_color"]);
		$bordercolors = explode(",", $options["border_color"]);
		$bg_color = imagecolorallocate ($imgCaptcha, $bgcolors[0],$bgcolors[1],$bgcolors[2]);//color fondo
		$border_color = imagecolorallocate ($imgCaptcha, $bordercolors[0],$bordercolors[1],$bordercolors[2]);//borde captcha

		imagefill($imgCaptcha,0,0,$bg_color); //llenamos el fondo del captcha

		//obtenemos una cadena alfa o alfanúmerica de tantos carácteres como length tenga
		if ($options["text"] == "")
	    {
	    	if($options["type"] == "alpha")
	    	{
	    		$options["text"] = $this->createAlphaCaptcha($options,$type = "alpha");
	    	}else{
	    		$options["text"] = $this->createAlphaCaptcha($options,$type = "alphanum");
	    	}    	
	    }

	    //si hemos establecido un número de líneas las pintamos

	    if($options["num_lines"] != "")
	    {
		    $this->createNumLinesCaptcha($options,$imgCaptcha);
		}

		//si hemos establecido un número de circulos los pintamos
		if($options["num_circles"] != "")
		{
			$this->createNumCirclesCaptcha($options,$imgCaptcha);
		}

	    //colocamos los carácteres en el captcha con distintos angulos
	    //y a distintas medidas de captcha

		if(is_dir($options["dir_fonts"]) && file_exists($options["dir_fonts"].$options["font"]))
		{
			$this->pushCharsIntoCaptcha($options,$imgCaptcha,$fonts = true);
		}else{
			$this->pushCharsIntoCaptcha($options,$imgCaptcha,$fonts = false);				
		}

		//conseguimos el timestamp para darle nombre a la imagen
		$timestamp = date("Y-m-d H:i:s", time());
		$unix = strtotime($timestamp);
		$img_name = $unix.'.png';

		//pintamos el borde de la imagen
		imagerectangle($imgCaptcha, 0, 0, $options["width"]-1, $options["height"]-1, $border_color);

		//si no existe el directorio lo creamos y damos permisos
		if(!is_dir($options["directory"])) 
		{
            mkdir($options["directory"] , 0777);
        }

        $this->deleteOldFiles($options,$unix);

		//creamos la imagen y le damos nombre
		imagepng($imgCaptcha, $options["directory"].$img_name);

		$img = "<img src=".$options["directory"].$img_name." width=".$options["width"]." height=".$options["height"]." />";

		//eliminamos la imagen
		imagedestroy($imgCaptcha);

		Session::put("text",Str::lower($options["text"]));
		return array("img" => $img);

	}	

	/**
     * Checks if field captcha equals imagen catcha
     * 
     * @access public  
     * @param	string	$value
     * @return	bool
     */
	public function check($captcha)
	{
		return Session::get("text") === $captcha ? true : false;
	}
}