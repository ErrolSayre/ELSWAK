<?php
/*
	ELSWAK File Type Detector
	
	This class examines a file name to determine its mime type based on file extension.
*/
class ELSWAK_File_Type_Detector {

	protected static $fileTypes;



	public static function typeFromExtension($extension) {
		// determine if the extension is in the list
		$fileTypes = self::fileTypes();
		if (array_key_exists($extension, $fileTypes)) {
			return $fileTypes[$extension];
		}
		return 'application/octet-stream';
	}
	public static function typeFromName($name) {
		return self::typeFromExtension(strtolower(pathinfo($name, PATHINFO_EXTENSION)));
	}
	public static function typeFromFile($file) {
		return self::typeFromName($file);
	}
	public static function fileTypeCanBeViewedInBrowser($type) {
		if (
			($type == 'image/jpeg') ||
			($type == 'image/png')
		) {
			return true;
		}
		return false;
	}
	public static function setFileTypes(array $types) {
		self::$fileTypes = $types;
	}
	public static function fileTypes() {
		if (!is_array(self::$fileTypes)) {
			return array (
				'3dm' => 'x-world/x-3dmf',
				'3dmf' => 'x-world/x-3dmf',
				'3gp' => 'video/3gpp',
				'7z' => 'application/x-7z-compressed',
				'323' => 'text/h323',
				'a' => 'application/octet-stream',
				'aab' => 'application/x-authorware-bin',
				'aam' => 'application/x-authorware-map',
				'aas' => 'application/x-authorware-seg',
				'abc' => 'text/vnd.abc',
				'abw' => 'application/x-abiword',
				'acgi' => 'text/html',
				'afl' => 'video/animaflex',
				'ai' => 'application/postscript',
				'aif' => 'audio/aiff',
				'aifc' => 'audio/aiff',
				'aiff' => 'audio/aiff',
				'aim' => 'application/x-aim',
				'aip' => 'text/x-audiosoft-intra',
				'alc' => 'chemical/x-alchemy',
				'ani' => 'application/x-navi-animation',
				'aos' => 'application/x-nokia-9000-communicator-add-on-software',
				'aps' => 'application/mime',
				'arc' => 'application/octet-stream',
				'arj' => 'application/arj',
				'arj' => 'application/octet-stream',
				'art' => 'image/x-jg',
				'asc' => 'text/plain',
				'asf' => 'video/x-ms-asf',
				'asm' => 'text/x-asm',
				'asn' => 'chemical/x-ncbi-asn1-spec',
				'aso' => 'chemical/x-ncbi-asn1-binary',
				'asp' => 'text/asp',
				'asx' => 'video/x-ms-asf',
				'atom' => 'application/atom',
				'atomcat' => 'application/atomcat+xml',
				'atomsrv' => 'application/atomserv+xml',
				'au' => 'audio/basic',
				'avi' => 'video/avi',
				'avi' => 'video/x-msvideo',
				'avs' => 'video/avs-video',
				'b' => 'chemical/x-molconn-Z',
				'bat' => 'application/x-msdos-program',
				'bcpio' => 'application/x-bcpio',
				'bib' => 'text/x-bibtex',
				'bin' => 'application/octet-stream',
				'bm' => 'image/bmp',
				'bmp' => 'image/bmp',
				'boo' => 'application/book',
				'book' => 'application/book',
				'boz' => 'application/x-bzip2',
				'bsd' => 'chemical/x-crossfire',
				'bsh' => 'application/x-bsh',
				'bz' => 'application/x-bzip',
				'bz2' => 'application/x-bzip2',
				'c' => 'text/x-csrc',
				'c++' => 'text/x-c++src',
				'c3d' => 'chemical/x-chem3d',
				'cab' => 'application/x-cab',
				'cac' => 'chemical/x-cache',
				'cache' => 'chemical/x-cache',
				'cap' => 'application/cap',
				'cascii' => 'chemical/x-cactvs-binary',
				'cat' => 'application/vnd.ms-pki.seccat',
				'cbin' => 'chemical/x-cactvs-binary',
				'cbr' => 'application/x-cbr',
				'cbz' => 'application/x-cbz',
				'cc' => 'text/x-c++src',
				'ccad' => 'application/clariscad',
				'cco' => 'application/x-cocoa',
				'cdf' => 'application/x-cdf',
				'cdr' => 'image/x-coreldraw',
				'cdt' => 'image/x-coreldrawtemplate',
				'cdx' => 'chemical/x-cdx',
				'cdy' => 'application/vnd.cinderella',
				'cef' => 'chemical/x-cxf',
				'cer' => 'chemical/x-cerius',
				'cha' => 'application/x-chat',
				'chat' => 'application/x-chat',
				'chm' => 'chemical/x-chemdraw',
				'chrt' => 'application/x-kchart',
				'cif' => 'chemical/x-cif',
				'class' => 'application/java',
				'cls' => 'text/x-tex',
				'cmdf' => 'chemical/x-cmdf',
				'cml' => 'chemical/x-cml',
				'cod' => 'application/vnd.rim.cod',
				'com' => 'application/x-msdos-program',
				'conf' => 'text/plain',
				'cpa' => 'chemical/x-compass',
				'cpio' => 'application/x-cpio',
				'cpp' => 'text/x-c++src',
				'cpt' => 'image/x-corelphotopaint',
				'crl' => 'application/x-pkcs7-crl',
				'crt' => 'application/x-x509-user-cert',
				'csf' => 'chemical/x-cache-csf',
				'csh' => 'text/x-csh',
				'csm' => 'chemical/x-csml',
				'csml' => 'chemical/x-csml',
				'css' => 'text/css',
				'csv' => 'text/csv',
				'ctab' => 'chemical/x-cactvs-binary',
				'ctx' => 'chemical/x-ctx',
				'cu' => 'application/cu-seeme',
				'cub' => 'chemical/x-gaussian-cube',
				'cxf' => 'chemical/x-cxf',
				'cxx' => 'text/x-c++src',
				'd' => 'text/x-dsrc',
				'dat' => 'chemical/x-mopac-input',
				'dcr' => 'application/x-director',
				'deb' => 'application/x-debian-package',
				'deepv' => 'application/x-deepv',
				'def' => 'text/plain',
				'dif' => 'video/x-dv',
				'diff' => 'text/x-diff',
				'dir' => 'application/x-director',
				'djv' => 'image/vnd.djvu',
				'djvu' => 'image/vnd.djvu',
				'dl' => 'video/dl',
				'dll' => 'application/x-msdos-program',
				'dmg' => 'application/x-apple-diskimage',
				'dms' => 'application/x-dms',
				'doc' => 'application/msword',
				'docm' => 'application/vnd.ms-word.document.macroEnabled.12',
				'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
				'dot' => 'application/msword',
				'dotm' => 'application/vnd.ms-word.template.macroEnabled.12',
				'dotx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.template',
				'dp' => 'application/commonground',
				'drw' => 'application/drafting',
				'dump' => 'application/octet-stream',
				'dv' => 'video/dv',
				'dv' => 'video/x-dv',
				'dvi' => 'application/x-dvi',
				'dwf' => 'model/vnd.dwf',
				'dwg' => 'image/vnd.dwg',
				'dx' => 'chemical/x-jcamp-dx',
				'dxf' => 'image/x-dwg',
				'dxr' => 'application/x-director',
				'el' => 'text/x-script.elisp',
				'elc' => 'application/x-elc',
				'emb' => 'chemical/x-embl-dl-nucleotide',
				'embl' => 'chemical/x-embl-dl-nucleotide',
				'eml' => 'message/rfc822',
				'ent' => 'chemical/x-pdb',
				'env' => 'application/x-envoy',
				'eps' => 'application/postscript',
				'es' => 'application/x-esrehber',
				'etx' => 'text/x-setext',
				'evy' => 'application/envoy',
				'exe' => 'application/x-msdos-program',
				'ez' => 'application/andrew-inset',
				'f' => 'text/plain',
				'f77' => 'text/x-fortran',
				'f90' => 'text/plain',
				'fb' => 'application/x-maker',
				'fbdoc' => 'application/x-maker',
				'fch' => 'chemical/x-gaussian-checkpoint',
				'fchk' => 'chemical/x-gaussian-checkpoint',
				'fdf' => 'application/vnd.fdf',
				'fif' => 'image/fif',
				'fig' => 'application/x-xfig',
				'flac' => 'application/x-flac',
				'fli' => 'video/fli',
				'flo' => 'image/florian',
				'flx' => 'text/vnd.fmi.flexstor',
				'fm' => 'application/x-maker',
				'fmf' => 'video/x-atomic3d-feature',
				'for' => 'text/plain',
				'fpx' => 'image/vnd.fpx',
				'frame' => 'application/x-maker',
				'frl' => 'application/freeloader',
				'frm' => 'application/x-maker',
				'funk' => 'audio/make',
				'g' => 'text/plain',
				'g3' => 'image/g3fax',
				'gal' => 'chemical/x-gaussian-log',
				'gam' => 'chemical/x-gamess-input',
				'gamin' => 'chemical/x-gamess-input',
				'gau' => 'chemical/x-gaussian-input',
				'gcd' => 'text/x-pcs-gcd',
				'gcf' => 'application/x-graphing-calculator',
				'gcg' => 'chemical/x-gcg8-sequence',
				'gen' => 'chemical/x-genbank',
				'gf' => 'application/x-tex-gf',
				'gif' => 'image/gif',
				'gjc' => 'chemical/x-gaussian-input',
				'gjf' => 'chemical/x-gaussian-input',
				'gl' => 'video/gl',
				'gnumeric' => 'application/x-gnumeric',
				'gpt' => 'chemical/x-mopac-graph',
				'gsd' => 'audio/x-gsm',
				'gsf' => 'application/x-font',
				'gsm' => 'audio/x-gsm',
				'gsp' => 'application/x-gsp',
				'gss' => 'application/x-gss',
				'gtar' => 'application/x-gtar',
				'gz' => 'application/x-gzip',
				'gzip' => 'application/x-gzip',
				'h' => 'text/plain',
				'h' => 'text/x-chdr',
				'h++' => 'text/x-c++hdr',
				'hdf' => 'application/x-hdf',
				'help' => 'application/x-helpfile',
				'hgl' => 'application/vnd.hp-hpgl',
				'hh' => 'text/x-c++hdr',
				'hin' => 'chemical/x-hin',
				'hlb' => 'text/x-script',
				'hlp' => 'application/hlp',
				'hpg' => 'application/vnd.hp-hpgl',
				'hpgl' => 'application/vnd.hp-hpgl',
				'hpp' => 'text/x-c++hdr',
				'hqx' => 'application/binhex',
				'hs' => 'text/x-haskell',
				'hta' => 'application/hta',
				'htc' => 'text/x-component',
				'htm' => 'text/html',
				'html' => 'text/html',
				'htmls' => 'text/html',
				'htt' => 'text/webviewhtml',
				'htx' => 'text/html',
				'hxx' => 'text/x-c++hdr',
				'ica' => 'application/x-ica',
				'ice' => 'x-conference/x-cooltalk',
				'ico' => 'image/x-icon',
				'ics' => 'text/calendar',
				'icz' => 'text/calendar',
				'idc' => 'text/plain',
				'ief' => 'image/ief',
				'iefs' => 'image/ief',
				'iges' => 'application/iges',
				'igs' => 'application/iges',
				'iii' => 'application/x-iphone',
				'ima' => 'application/x-ima',
				'imap' => 'application/x-httpd-imap',
				'inf' => 'application/inf',
				'inp' => 'chemical/x-gamess-input',
				'ins' => 'application/x-internet-signup',
				'ip' => 'application/x-ip2',
				'iso' => 'application/x-iso9660-image',
				'isp' => 'application/x-internet-signup',
				'ist' => 'chemical/x-isostar',
				'istr' => 'chemical/x-isostar',
				'isu' => 'video/x-isvideo',
				'it' => 'audio/it',
				'iv' => 'application/x-inventor',
				'ivr' => 'i-world/i-vrml',
				'ivy' => 'application/x-livescreen',
				'jad' => 'text/vnd.sun.j2me.app-descriptor',
				'jam' => 'audio/x-jam',
				'jar' => 'application/java-archive',
				'jav' => 'text/plain',
				'java' => 'text/x-java',
				'jcm' => 'application/x-java-commerce',
				'jdx' => 'chemical/x-jcamp-dx',
				'jfif' => 'image/jpeg',
				'jmz' => 'application/x-jmol',
				'jng' => 'image/x-jng',
				'jnlp' => 'application/x-java-jnlp-file',
				'jpe' => 'image/jpeg',
				'jpeg' => 'image/jpeg',
				'jpg' => 'image/jpeg',
				'jps' => 'image/x-jps',
				'js' => 'application/x-javascript',
				'jut' => 'image/jutvision',
				'kar' => 'audio/midi',
				'key' => 'application/pgp-keys',
				'kil' => 'application/x-killustrator',
				'kin' => 'chemical/x-kinemage',
				'kml' => 'application/vnd.google-earth.kml+xml',
				'kmz' => 'application/vnd.google-earth.kmz',
				'kpr' => 'application/x-kpresenter',
				'kpt' => 'application/x-kpresenter',
				'ksh' => 'application/x-ksh',
				'ksp' => 'application/x-kspread',
				'kwd' => 'application/x-kword',
				'kwt' => 'application/x-kword',
				'la' => 'audio/nspaudio',
				'lam' => 'audio/x-liveaudio',
				'latex' => 'application/x-latex',
				'lha' => 'application/lha',
				'lha' => 'application/x-lha',
				'lhs' => 'text/x-literate-haskell',
				'lhx' => 'application/octet-stream',
				'list' => 'text/plain',
				'lma' => 'audio/nspaudio',
				'log' => 'text/plain',
				'lsf' => 'video/x-la-asf',
				'lsp' => 'application/x-lisp',
				'lst' => 'text/plain',
				'lsx' => 'video/x-la-asf',
				'ltx' => 'application/x-latex',
				'ltx' => 'text/x-tex',
				'lyx' => 'application/x-lyx',
				'lzh' => 'application/x-lzh',
				'lzx' => 'application/x-lzx',
				'm' => 'text/plain',
				'm1v' => 'video/mpeg',
				'm2a' => 'audio/mpeg',
				'm2v' => 'video/mpeg',
				'm3u' => 'audio/x-mpegurl',
				'm4a' => 'audio/mpeg',
				'maker' => 'application/x-maker',
				'man' => 'application/x-troff-man',
				'map' => 'application/x-navimap',
				'mar' => 'text/plain',
				'mbd' => 'application/mbedlet',
				'mc$' => 'application/x-magic-cap-package-1.0',
				'mcd' => 'application/mcad',
				'mcf' => 'image/vasa',
				'mcif' => 'chemical/x-mmcif',
				'mcm' => 'chemical/x-macmolecule',
				'mcp' => 'application/netmc',
				'mdb' => 'application/msaccess',
				'me' => 'application/x-troff-me',
				'mesh' => 'model/mesh',
				'mht' => 'message/rfc822',
				'mhtml' => 'message/rfc822',
				'mid' => 'audio/midi',
				'midi' => 'audio/midi',
				'mif' => 'application/x-mif',
				'mime' => 'www/mime',
				'mjf' => 'audio/x-vnd.audioexplosion.mjuicemediafile',
				'mjpg' => 'video/x-motion-jpeg',
				'mm' => 'application/x-freemind',
				'mmd' => 'chemical/x-macromodel-input',
				'mme' => 'application/base64',
				'mmf' => 'application/vnd.smaf',
				'mml' => 'text/mathml',
				'mmod' => 'chemical/x-macromodel-input',
				'mng' => 'video/x-mng',
				'moc' => 'text/x-moc',
				'mod' => 'audio/mod',
				'mol' => 'chemical/x-mdl-molfile',
				'mol2' => 'chemical/x-mol2',
				'moo' => 'chemical/x-mopac-out',
				'moov' => 'video/quicktime',
				'mop' => 'chemical/x-mopac-input',
				'mopcrt' => 'chemical/x-mopac-input',
				'mov' => 'video/quicktime',
				'movie' => 'video/x-sgi-movie',
				'mp2' => 'video/mpeg',
				'mp3' => 'audio/mpeg3',
				'mp4' => 'video/mp4',
				'mpa' => 'audio/mpeg',
				'mpc' => 'application/x-project',
				'mpe' => 'video/mpeg',
				'mpeg' => 'video/mpeg',
				'mpega' => 'audio/mpeg',
				'mpg' => 'video/mpeg',
				'mpga' => 'audio/mpeg',
				'mpp' => 'application/vnd.ms-project',
				'mpt' => 'application/x-project',
				'mpv' => 'application/x-project',
				'mpx' => 'application/x-project',
				'mrc' => 'application/marc',
				'ms' => 'application/x-troff-ms',
				'msh' => 'model/mesh',
				'msi' => 'application/x-msi',
				'mv' => 'video/x-sgi-movie',
				'mvb' => 'chemical/x-mopac-vib',
				'mxu' => 'video/vnd.mpegurl',
				'my' => 'audio/make',
				'mzz' => 'application/x-vnd.audioexplosion.mzz',
				'nap' => 'image/naplps',
				'naplps' => 'image/naplps',
				'nb' => 'application/mathematica',
				'nc' => 'application/x-netcdf',
				'ncm' => 'application/vnd.nokia.configuration-message',
				'nif' => 'image/x-niff',
				'niff' => 'image/x-niff',
				'nix' => 'application/x-mix-transfer',
				'nsc' => 'application/x-conference',
				'nvd' => 'application/x-navidoc',
				'nwc' => 'application/x-nwc',
				'o' => 'application/x-object',
				'oda' => 'application/oda',
				'odb' => 'application/vnd.oasis.opendocument.database',
				'odc' => 'application/vnd.oasis.opendocument.chart',
				'odf' => 'application/vnd.oasis.opendocument.formula',
				'odg' => 'application/vnd.oasis.opendocument.graphics',
				'odi' => 'application/vnd.oasis.opendocument.image',
				'odm' => 'application/vnd.oasis.opendocument.text-master',
				'odp' => 'application/vnd.oasis.opendocument.presentation',
				'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
				'odt' => 'application/vnd.oasis.opendocument.text',
				'oga' => 'audio/ogg',
				'ogg' => 'application/ogg',
				'ogv' => 'video/ogg',
				'ogx' => 'application/ogg',
				'omc' => 'application/x-omc',
				'omcd' => 'application/x-omcdatamaker',
				'omcr' => 'application/x-omcregerator',
				'otg' => 'application/vnd.oasis.opendocument.graphics-template',
				'oth' => 'application/vnd.oasis.opendocument.text-web',
				'otp' => 'application/vnd.oasis.opendocument.presentation-template',
				'ots' => 'application/vnd.oasis.opendocument.spreadsheet-template',
				'ott' => 'application/vnd.oasis.opendocument.text-template',
				'oza' => 'application/x-oz-application',
				'p' => 'text/x-pascal',
				'p7a' => 'application/x-pkcs7-signature',
				'p7c' => 'application/pkcs7-mime',
				'p7m' => 'application/pkcs7-mime',
				'p7r' => 'application/x-pkcs7-certreqresp',
				'p7s' => 'application/pkcs7-signature',
				'p10' => 'application/pkcs10',
				'p12' => 'application/pkcs-12',
				'pac' => 'application/x-ns-proxy-autoconfig',
				'part' => 'application/pro_eng',
				'pas' => 'text/pascal',
				'pas' => 'text/x-pascal',
				'pat' => 'image/x-coreldrawpattern',
				'patch' => 'text/x-diff',
				'pbm' => 'image/x-portable-bitmap',
				'pcap' => 'application/cap',
				'pcf' => 'application/x-font',
				'pcl' => 'application/x-pcl',
				'pct' => 'image/x-pict',
				'pcx' => 'image/x-pcx',
				'pdb' => 'chemical/x-pdb',
				'pdf' => 'application/pdf',
				'pfa' => 'application/x-font',
				'pfb' => 'application/x-font',
				'pfunk' => 'audio/make',
				'pgm' => 'image/x-portable-graymap',
				'pgm' => 'image/x-portable-greymap',
				'pgn' => 'application/x-chess-pgn',
				'pgp' => 'application/pgp-signature',
				'php' => 'application/x-httpd-php',
				'php3' => 'application/x-httpd-php3',
				'php3p' => 'application/x-httpd-php3-preprocessed',
				'php4' => 'application/x-httpd-php4',
				'phps' => 'application/x-httpd-php-source',
				'pht' => 'application/x-httpd-php',
				'phtml' => 'application/x-httpd-php',
				'pic' => 'image/pict',
				'pict' => 'image/pict',
				'pk' => 'application/x-tex-pk',
				'pkg' => 'application/x-newton-compatible-pkg',
				'pko' => 'application/vnd.ms-pki.pko',
				'pl' => 'text/x-perl',
				'pls' => 'audio/x-scpls',
				'plx' => 'application/x-pixclscript',
				'pm' => 'text/x-perl',
				'pm4' => 'application/x-pagemaker',
				'pm5' => 'application/x-pagemaker',
				'png' => 'image/png',
				'pnm' => 'image/x-portable-anymap',
				'pot' => 'application/vnd.ms-powerpoint',
				'pot' => 'text/plain',
				'potm' => 'application/vnd.ms-powerpoint.template.macroEnabled.12',
				'potx' => 'application/vnd.openxmlformats-officedocument.presentationml.template',
				'pov' => 'model/x-pov',
				'ppa' => 'application/vnd.ms-powerpoint',
				'ppam' => 'application/vnd.ms-powerpoint.addin.macroEnabled.12',
				'ppm' => 'image/x-portable-pixmap',
				'pps' => 'application/vnd.ms-powerpoint',
				'ppsm' => 'application/vnd.ms-powerpoint.slideshow.macroEnabled.12',
				'ppsx' => 'application/vnd.openxmlformats-officedocument.presentationml.slideshow',
				'ppt' => 'application/powerpoint',
				'ppt' => 'application/vnd.ms-powerpoint',
				'pptm' => 'application/vnd.ms-powerpoint.presentation.macroEnabled.12',
				'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
				'ppz' => 'application/mspowerpoint',
				'pre' => 'application/x-freelance',
				'prf' => 'application/pics-rules',
				'prt' => 'application/pro_eng',
				'ps' => 'application/postscript',
				'psd' => 'image/x-photoshop',
				'pvu' => 'paleovu/x-pv',
				'pwz' => 'application/vnd.ms-powerpoint',
				'py' => 'text/x-python',
				'pyc' => 'application/x-python-code',
				'pyo' => 'application/x-python-code',
				'qcp' => 'audio/vnd.qcelp',
				'qd3' => 'x-world/x-3dmf',
				'qd3d' => 'x-world/x-3dmf',
				'qif' => 'image/x-quicktime',
				'qt' => 'video/quicktime',
				'qtc' => 'video/x-qtc',
				'qti' => 'image/x-quicktime',
				'qtif' => 'image/x-quicktime',
				'qtl' => 'application/x-quicktimeplayer',
				'ra' => 'audio/x-pn-realaudio',
				'ra' => 'audio/x-realaudio',
				'ram' => 'audio/x-pn-realaudio',
				'rar' => 'application/rar',
				'ras' => 'image/x-cmu-raster',
				'rast' => 'image/cmu-raster',
				'rd' => 'chemical/x-mdl-rdfile',
				'rdf' => 'application/rdf+xml',
				'rexx' => 'text/x-script.rexx',
				'rf' => 'image/vnd.rn-realflash',
				'rgb' => 'image/x-rgb',
				'rhtml' => 'application/x-httpd-eruby',
				'rm' => 'application/vnd.rn-realmedia',
				'rm' => 'audio/x-pn-realaudio',
				'rmi' => 'audio/mid',
				'rmm' => 'audio/x-pn-realaudio',
				'rmp' => 'audio/x-pn-realaudio',
				'rng' => 'application/vnd.nokia.ringing-tone',
				'rnx' => 'application/vnd.rn-realplayer',
				'roff' => 'application/x-troff',
				'ros' => 'chemical/x-rosdal',
				'rp' => 'image/vnd.rn-realpix',
				'rpm' => 'application/x-redhat-package-manager',
				'rpm' => 'audio/x-pn-realaudio-plugin',
				'rss' => 'application/rss+xml',
				'rt' => 'text/richtext',
				'rtf' => 'text/richtext',
				'rtx' => 'text/richtext',
				'rv' => 'video/vnd.rn-realvideo',
				'rxn' => 'chemical/x-mdl-rxnfile',
				's' => 'text/x-asm',
				's3m' => 'audio/s3m',
				'saveme' => 'application/octet-stream',
				'sbk' => 'application/x-tbook',
				'scm' => 'video/x-scm',
				'sct' => 'text/scriptlet',
				'sd' => 'chemical/x-mdl-sdfile',
				'sd2' => 'audio/x-sd2',
				'sda' => 'application/vnd.stardivision.draw',
				'sdc' => 'application/vnd.stardivision.calc',
				'sdd' => 'application/vnd.stardivision.impress',
				'sdf' => 'application/vnd.stardivision.math',
				'sdf' => 'chemical/x-mdl-sdfile',
				'sdml' => 'text/plain',
				'sdp' => 'application/sdp',
				'sdr' => 'application/sounder',
				'sds' => 'application/vnd.stardivision.chart',
				'sdw' => 'application/vnd.stardivision.writer',
				'sea' => 'application/sea',
				'ser' => 'application/java-serialized-object',
				'set' => 'application/set',
				'sgf' => 'application/x-go-sgf',
				'sgl' => 'application/vnd.stardivision.writer-global',
				'sgm' => 'text/sgml',
				'sgml' => 'text/sgml',
				'sh' => 'text/x-sh',
				'shar' => 'application/x-shar',
				'shtml' => 'text/html',
				'sid' => 'audio/prs.sid',
				'sid' => 'audio/x-psid',
				'silo' => 'model/mesh',
				'sis' => 'application/vnd.symbian.install',
				'sisx' => 'x-epoc/x-sisx-app',
				'sit' => 'application/x-stuffit',
				'sitx' => 'application/x-stuffit',
				'skd' => 'application/x-koan',
				'skm' => 'application/x-koan',
				'skp' => 'application/x-koan',
				'skt' => 'application/x-koan',
				'sl' => 'application/x-seelogo',
				'smi' => 'application/smil',
				'smil' => 'application/smil',
				'snd' => 'audio/basic',
				'sol' => 'application/solids',
				'spc' => 'text/x-speech',
				'spl' => 'application/x-futuresplash',
				'spr' => 'application/x-sprite',
				'sprite' => 'application/x-sprite',
				'spx' => 'audio/ogg',
				'src' => 'application/x-wais-source',
				'ssi' => 'text/x-server-parsed-html',
				'ssm' => 'application/streamingmedia',
				'sst' => 'application/vnd.ms-pki.certstore',
				'stc' => 'application/vnd.sun.xml.calc.template',
				'std' => 'application/vnd.sun.xml.draw.template',
				'step' => 'application/step',
				'sti' => 'application/vnd.sun.xml.impress.template',
				'stl' => 'application/vnd.ms-pki.stl',
				'stp' => 'application/step',
				'stw' => 'application/vnd.sun.xml.writer.template',
				'sty' => 'text/x-tex',
				'sv4cpio' => 'application/x-sv4cpio',
				'sv4crc' => 'application/x-sv4crc',
				'svf' => 'image/x-dwg',
				'svg' => 'image/svg+xml',
				'svgz' => 'image/svg+xml',
				'svr' => 'application/x-world',
				'sw' => 'chemical/x-swissprot',
				'swf' => 'application/x-shockwave-flash',
				'swfl' => 'application/x-shockwave-flash',
				'sxc' => 'application/vnd.sun.xml.calc',
				'sxd' => 'application/vnd.sun.xml.draw',
				'sxg' => 'application/vnd.sun.xml.writer.global',
				'sxi' => 'application/vnd.sun.xml.impress',
				'sxm' => 'application/vnd.sun.xml.math',
				'sxw' => 'application/vnd.sun.xml.writer',
				't' => 'application/x-troff',
				'talk' => 'text/x-speech',
				'tar' => 'application/x-tar',
				'taz' => 'application/x-gtar',
				'tbk' => 'application/toolbook',
				'tcl' => 'text/x-tcl',
				'tcsh' => 'text/x-script.tcsh',
				'tex' => 'text/x-tex',
				'texi' => 'application/x-texinfo',
				'texinfo' => 'application/x-texinfo',
				'text' => 'text/plain',
				'tgf' => 'chemical/x-mdl-tgf',
				'tgz' => 'application/x-gtar',
				'tif' => 'image/tiff',
				'tiff' => 'image/tiff',
				'tk' => 'text/x-tcl',
				'tm' => 'text/texmacs',
				'torrent' => 'application/x-bittorrent',
				'tr' => 'application/x-troff',
				'ts' => 'text/texmacs',
				'tsi' => 'audio/tsp-audio',
				'tsp' => 'audio/tsplayer',
				'tsv' => 'text/tab-separated-values',
				'turbot' => 'image/florian',
				'txt' => 'text/plain',
				'udeb' => 'application/x-debian-package',
				'uil' => 'text/x-uil',
				'uls' => 'text/iuls',
				'uni' => 'text/uri-list',
				'unis' => 'text/uri-list',
				'unv' => 'application/i-deas',
				'uri' => 'text/uri-list',
				'uris' => 'text/uri-list',
				'ustar' => 'application/x-ustar',
				'uu' => 'text/x-uuencode',
				'uue' => 'text/x-uuencode',
				'val' => 'chemical/x-ncbi-asn1-binary',
				'vcd' => 'application/x-cdlink',
				'vcf' => 'text/x-vcard',
				'vcs' => 'text/x-vcalendar',
				'vda' => 'application/vda',
				'vdo' => 'video/vdo',
				'vew' => 'application/groupwise',
				'viv' => 'video/vivo',
				'vivo' => 'video/vivo',
				'vmd' => 'application/vocaltec-media-desc',
				'vmf' => 'application/vocaltec-media-file',
				'vms' => 'chemical/x-vamas-iso14976',
				'voc' => 'audio/voc',
				'vos' => 'video/vosaic',
				'vox' => 'audio/voxware',
				'vqe' => 'audio/x-twinvq-plugin',
				'vqf' => 'audio/x-twinvq',
				'vql' => 'audio/x-twinvq-plugin',
				'vrm' => 'x-world/x-vrml',
				'vrml' => 'model/vrml',
				'vrt' => 'x-world/x-vrt',
				'vsd' => 'application/vnd.visio',
				'vsd' => 'application/x-visio',
				'vst' => 'application/x-visio',
				'vsw' => 'application/x-visio',
				'w6w' => 'application/msword',
				'w60' => 'application/wordperfect6.0',
				'w61' => 'application/wordperfect6.1',
				'wad' => 'application/x-doom',
				'wav' => 'audio/x-wav',
				'wax' => 'audio/x-ms-wax',
				'wb1' => 'application/x-qpro',
				'wbmp' => 'image/vnd.wap.wbmp',
				'wbxml' => 'application/vnd.wap.wbxml',
				'web' => 'application/vnd.xara',
				'wiz' => 'application/msword',
				'wk' => 'application/x-123',
				'wk1' => 'application/x-123',
				'wm' => 'video/x-ms-wm',
				'wma' => 'audio/x-ms-wma',
				'wmd' => 'application/x-ms-wmd',
				'wmf' => 'windows/metafile',
				'wml' => 'text/vnd.wap.wml',
				'wmlc' => 'application/vnd.wap.wmlc',
				'wmls' => 'text/vnd.wap.wmlscript',
				'wmlsc' => 'application/vnd.wap.wmlscriptc',
				'wmv' => 'video/x-ms-wmv',
				'wmx' => 'video/x-ms-wmx',
				'wmz' => 'application/x-ms-wmz',
				'word' => 'application/msword',
				'wp' => 'application/wordperfect',
				'wp5' => 'application/wordperfect',
				'wp6' => 'application/wordperfect',
				'wpd' => 'application/wordperfect',
				'wq1' => 'application/x-lotus',
				'wri' => 'application/mswrite',
				'wrl' => 'model/vrml',
				'wrz' => 'model/vrml',
				'wsc' => 'text/scriptlet',
				'wsrc' => 'application/x-wais-source',
				'wtk' => 'application/x-wintalk',
				'wvx' => 'video/x-ms-wvx',
				'wz' => 'application/x-wingz',
				'x-png' => 'image/png',
				'xbm' => 'image/x-xbitmap',
				'xbm' => 'image/xbm',
				'xcf' => 'application/x-xcf',
				'xdr' => 'video/x-amt-demorun',
				'xgz' => 'xgl/drawing',
				'xht' => 'application/xhtml+xml',
				'xhtml' => 'application/xhtml+xml',
				'xif' => 'image/vnd.xiff',
				'xl' => 'application/excel',
				'xla' => 'application/excel',
				'xlam' => 'application/vnd.ms-excel.addin.macroEnabled.12',
				'xlb' => 'application/excel',
				'xlb' => 'application/vnd.ms-excel',
				'xlc' => 'application/excel',
				'xld' => 'application/excel',
				'xll' => 'application/excel',
				'xlm' => 'application/excel',
				'xls' => 'application/vnd.ms-excel',
				'xlsb' => 'application/vnd.ms-excel.sheet.binary.macroEnabled.12',
				'xlsm' => 'application/vnd.ms-excel.sheet.macroEnabled.12',
				'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
				'xlt' => 'application/vnd.ms-excel',
				'xltm' => 'application/vnd.ms-excel.template.macroEnabled.12',
				'xltx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.template',
				'xlv' => 'application/excel',
				'xlw' => 'application/excel',
				'xm' => 'audio/xm',
				'xml' => 'text/xml',
				'xmz' => 'xgl/movie',
				'xpi' => 'application/x-xpinstall',
				'xpix' => 'application/x-vnd.ls-xpix',
				'xpm' => 'image/x-xpixmap',
				'xps' => 'application/vnd.ms-xpsdocument',
				'xsl' => 'application/xml',
				'xsr' => 'video/x-amt-showrun',
				'xtel' => 'chemical/x-xtel',
				'xul' => 'application/vnd.mozilla.xul+xml',
				'xwd' => 'image/x-xwindowdump',
				'xyz' => 'chemical/x-xyz',
				'z' => 'application/x-compressed',
				'zip' => 'application/x-zip-compressed',
				'zip' => 'application/zip',
				'zmt' => 'chemical/x-mopac-input',
				'zoo' => 'application/octet-stream',
				'zsh' => 'text/x-script.zsh'
			);
		}
		return self::$fileTypes;
	}
}