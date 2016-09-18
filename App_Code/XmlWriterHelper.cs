using System.Text;
using System.Xml;

public static class XmlWriterHelper
{
    public static XmlWriter Create_SB(object _outputSB, object _settings)
    {
        return XmlWriter.Create(_outputSB as StringBuilder, _settings as XmlWriterSettings);
    }
}