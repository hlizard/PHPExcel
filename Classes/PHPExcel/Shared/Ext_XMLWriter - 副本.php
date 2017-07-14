<?php

use \System\Text\StringBuilder;
use \System\Xml\XmlWriter as Net_XmlWriter;
use \System\Xml\XmlWriterSettings;
use System\Text\Encoding;

class XMLWriter
{
    private $_outputSB;
    private $_writer;
    private $_settings;

    public function __construct()
    {
        $this->_settings = new System\Xml\XmlWriterSettings();
    }
        
    public function openMemory()
    {
        $this->_outputSB = new StringBuilder();
        //$_writer = System\Xml\XmlWriter::Create($_outputSB, $_settings); //failure because overloading
        //$_writer = System\Xml\XmlWriter::Create(new StringBuilder(), $_settings); //ok
        $this->_writer = \XmlWriterHelper::Create_SB($this->_outputSB, $this->_settings); //more power
        return true;
    }
        
    public function setIndent($indent)
    {
        $this->_settings->Indent = $indent;
        return true;
    }

    public function startDocument($version = 1.0 , $encoding = NULL , $standalone )
    {
        if($encoding != null)
            $this->_settings->Encoding = Encoding::GetEncoding($encoding);
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
    
    private $_latestElementName;
    private $_latestNS;
    public function startElement($name)
    {
        //$this->_writer->WriteStartElement($name);
        $this->_latestElementName = $name;
        return true;
    }

    public function writeAttribute ( $name , $value )
    {
        $pieces = explode(":", $name);
        if ($this->_latestElementName != null)
        {
            $elementNamePieces = explode(":", $this->_latestElementName);
            if ($name == "xmlns")
                if (count($elementNamePieces) > 1)
                    $this->_writer->WriteStartElement($elementNamePieces[0], $elementNamePieces[1], $value);
                else
                    $this->_writer->WriteStartElement($this->_latestElementName, $value);
            else if ($pieces[0] == "xmlns"){
                $this->_writer->WriteStartElement($pieces[1], $this->_latestElementName, $value);
                $this->_latestNS = $value;
            }
            else
                if (count($elementNamePieces) > 1)
                    $this->_writer->WriteStartElement($elementNamePieces[0], $elementNamePieces[1], $this->_latestNS);
                else
                    $this->_writer->WriteStartElement($this->_latestElementName);
            $this->_latestElementName = null;
        }


        if ($pieces[0] != "xmlns")
        {
            $this->_writer->WriteAttributeString($name, $value);
        }
        return true;
    }

    public function endElement()
    {
        if ($this->_latestElementName != null)
        {
            $elementNamePieces = explode(":", $this->_latestElementName);
            if (count($elementNamePieces) > 1)
                $this->_writer->WriteStartElement($elementNamePieces[0], $elementNamePieces[1], "");
            else
                $this->_writer->WriteStartElement($this->_latestElementName);
            $this->_latestElementName = null;
        }


        $this->_writer->WriteEndElement();
        return true;
    }
    
    public function writeElement($name, $content = NULL)
    {
        $this->startElement($name);
        if ($this->_latestElementName != null)
        {
            $elementNamePieces = explode(":", $this->_latestElementName);
            if (count($elementNamePieces) > 1)
                $this->_writer->WriteStartElement($elementNamePieces[0], $elementNamePieces[1], "");
            else
                $this->_writer->WriteStartElement($this->_latestElementName);
            $this->_latestElementName = null;
        }


        $this->_writer->WriteValue($content);


        $this->endElement();
        return true;
    }

    public function outputMemory($flush)
    {
        if ($flush)
            $this->_writer->Flush();

        return $this->_outputSB->ToString();
    }

}

?>