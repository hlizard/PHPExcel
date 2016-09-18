<?php

use \System\Xml\XmlTextWriter;
use System\Text\Encoding;
use System\IO\MemoryStream;

class XMLWriter
{
    private $_writer;
    private $_buffer;
    private $_encoding;
        
    public function openMemory()
    {
        $this->_buffer = new MemoryStream;
        return true;
    }
        
    public function setIndent($indent)
    {
        $this->_writer->Indentation = 2;
        return true;
    }

    public function startDocument($version = 1.0 , $encoding = NULL , $standalone )
    {
        $this->_encoding = Encoding::GetEncoding($encoding);
        $this->_writer = new XmlTextWriter($this->_buffer, $this->_encoding);
        $bStandalone = null;
        if ($standalone == "yes")
            $bStandalone = true;
        else if ($standalone == "no")
            $bStandalone = false;
        if ($bStandalone != null)
            $this->_writer->WriteStartDocument($bStandalone);
        else
            $this->_writer->WriteStartDocument();
        return true;
    }
    
    public function startElement($name)
    {
        $this->_writer->WriteStartElement($name);
        return true;
    }

    public function writeAttribute ( $name , $value )
    {
        $this->_writer->WriteAttributeString($name, $value);
        return true;
    }

    public function endElement()
    {
        $this->_writer->WriteEndElement();
        return true;
    }
    
    public function writeElement($name, $content = NULL)
    {
        $this->startElement($name);


        $this->_writer->WriteValue($content);


        $this->endElement();
        return true;
    }

    public function text($content)
    {
        $this->_writer->WriteString($content);
        return true;
    }

    public function outputMemory($flush)
    {
        if ($flush)
            $this->_writer->Flush();
        
		$this->_buffer->Position = 0;
		$sr = new System\IO\StreamReader($this->_buffer, $this->_encoding);
		$myStr = $sr->ReadToEnd();
        return $myStr;
    }

}

?>