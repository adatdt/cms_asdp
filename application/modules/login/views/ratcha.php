<?php
/*
Copyright (C) 2007  Tubagus Rafi Kusuma

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

class Image{
	var $type;
	var $width;
	var $height;
	var $backgroundColor;
	function image_index($type, $width, $height){
		$this->type = $type;
		$this->width = $width;
		$this->height = $height;
	}
	function create(){
		$this->handle = @imagecreate($this->width, $this->height);
		@imagecolorallocate($this->handle, rand(255, 255), rand(255, 255), rand(255, 255));
		return $this->handle;
	}
}

class RatchaImage extends Image{
	var $handle;
	var $fonts;
	var $bg;
	function ratcha_image($type, $width, $height){
		$this->handle = NULL;
		$this->fonts = array();
		$this->image_index($type, $width, $height);
		
	}

	function apply($effect){
		$effect->apply($this);
	}
	function render($quality = 100){
		header("Content-type: image/$this->type");
		@imageinterlace($this->handle, 1);
		@imagepng($this->handle, NULL, $quality);
		@imagedestroy($this->handle);
	}
}

class RatchaGif extends RatchaImage{
	function __construct($width, $height){
		$this->ratcha_image('GIF', $width, $height);
	}
	function createFrom($src){
		return ($this->handle = @imagecreatefromgif($src));
	}
	function render($quality = 100){
		header("Content-type: image/$this->type");
		@imageinterlace($this->handle, 1);
		@imagegif($this->handle, NULL, $quality);
		@imagedestroy($this->handle);
	}
}

class Effect{
	function apply($image){
		die('---');
	}
}

class GradientEffect extends Effect{
	function apply($image){
		for($i = 0, $rd = rand(0, 0), $gr = rand(0, 0), $bl= rand(0, 0); $i <= $image->height; $i++){
			$g = @imagecolorallocate($image->handle, $rd+=2, $gr+=2, $bl+=2);
			@imageline($image->handle, 0, $i, $image->width, $i, $g);
		}
		$image->backgroundColor = $g;
	}
}

class TextEffect extends Effect{
	var $text;
	var $size;
	var $depth;
	var $fonts;
	function __construct($text, $size, $depth=5){
		$this->text = $text;
		$this->size = $size;
		$this->depth = $depth;
		$this->fonts = array();
	}
	
	function addFont($path){
		if(file_exists($path)){
			$this->fonts[] = realpath($path);
		}else{
			echo"tidak ada".$path;
		}
	}
	
	function url_exists($url){
		$url = str_replace("http://", "", $url);
		if (strstr($url, "/")) {
		    $url = explode("/", $url, 2);
		    $url[1] = "/".$url[1];
		} else {
		    $url = array($url, "/");
		}
	    
		$fh = fsockopen($url[0], 80);
		if ($fh) {
		    fputs($fh,"GET ".$url[1]." HTTP/1.1\nHost:".$url[0]."\n\n");
		    if (fread($fh, 22) == "HTTP/1.1 404 Not Found") { return FALSE; }
		    else { return TRUE;    }
	    
		} else { return FALSE;}
	}
	
	function apply($image){
		$c = @imagecolorallocate($image->handle, rand(0, 0), rand(0, 0), rand(0, 0));
		$width = $image->width;
		$height = $image->height;
		$text = strtoupper($this->text);
		$charCount = count($this->fonts);
		if($charCount > 0){
			for($i = 0, $strlen = strlen($this->text), $p = floor(abs((($width-($this->size*$strlen))/2)-floor($this->size/2))); $i < $strlen; $i++, $p +=$this->size){
				$f = $this->fonts[rand(0, $charCount-1)];
				$d = rand(-8, 8);
				$y = rand(floor($height/2)+floor($this->size/2), $height-floor($this->size/2));
				for($b = 0; $b <= $this->depth; $b++){
					imagettftext($image->handle, $this->size, $d, $p++, $y++, $c, $f, $this->text{$i});
				}
				@imagettftext($image->handle, $this->size, $d, $p, $y, $this->bg, $f, $this->text{$i});
			}
		}
		else{
			imagestring ($image->handle, $this->size, floor(abs(((($width/2)-($this->size*strlen($this->text)))/2))), floor(($height/2)-($this->size/2)), $this->text, $c );
		
		}
	}

}
?>