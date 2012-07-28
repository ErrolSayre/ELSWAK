<?php
/*
	ELSWAK Coder Interface
	
	This interface provides a common ancestor for coders (classes that provide data encoding and decoding).
*/

interface ELSWAK_Data_Coder_Interface
	extends ELSWAK_Data_Encoder_Interface, ELSWAK_Data_Decoder_Interface {
}