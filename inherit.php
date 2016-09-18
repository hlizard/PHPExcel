--TEST--
XMLReader: class inheritance
--SKIPIF--
<?php if (!extension_loaded("xmlreader")) print "skip - xmlreader extension not loaded."; ?>
--FILE--
<?php 
class XMLReader2 extends PHP\Library\Xml\XMLReader {

	public function __construct():parent(PHP\Core\ScriptContext::$CurrentContext, TRUE)
	{

	}

	/**
	 * @return bool|string
	 */
	function nodeContents() {
		if( $this->isEmptyElement ) {
			return "";
		}
		$buffer = "";
		while( $this->read() ) {
			switch( $this->nodeType ) {
			case TEXT:
			case PHP\Library\Xml\XMLReader::SIGNIFICANT_WHITESPACE:
				$buffer .= $this->value;
				break;
			case PHP\Library\Xml\XMLReader::END_ELEMENT:
				return $buffer;
			}
		}
		return $this->close();
	}
}

$a = new XMLReader2();
$a->open('test.xml');
?>
===DONE===
--EXPECTF--

===DONE===
