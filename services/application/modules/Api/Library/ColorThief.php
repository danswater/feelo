<?php
use ColorThief\ColorThief;

class Api_Library_ColorThief {
		
	public static getDominantColor ( $imageSource ) {
		return ColorThief::getColor( $imageSource );
	}
}