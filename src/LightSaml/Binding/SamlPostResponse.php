<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Binding;

use Symfony\Component\HttpFoundation\Response;

class SamlPostResponse extends Response
{
    /** @var string */
    protected $destination;

    /** @var array */
    protected $data;

    /**
     * @param string $destination
     * @param int    $status
     * @param array  $headers
     */
    public function __construct($destination, array $data, $status = 200, $headers = [])
    {
        parent::__construct('', $status, $headers);

        $this->destination = $destination;
        $this->data = $data;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return string
     */
    public function getDestination()
    {
        return $this->destination;
    }

    public function renderContent()
    {
        $content = <<<'EOT'
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <title>POST data</title>
    <script>
        // https://support.pingidentity.com/s/article/Safari-Users-are-unable-to-complete-Authentication-process-when-SSO-is-initiated-from-an-embedded-webpage
        function setADCTrustCookie(cname,cvalue,exdays) {
            var d = new Date();
            d.setTime(d.getTime() + (exdays*24*60*60*1000));
            var expires = "expires=" + d.toGMTString();
            document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
        }
    </script>
</head>
<body onload="setADCTrustCookie('adctrust','AllowSafariToTrustEmbeddedLoginForm','1');document.getElementById('a-very-unique-input-id#lightSAML').click();">

    <noscript>
        <p><strong>Note:</strong> Since your browser does not support JavaScript, you must press the button below once to proceed.</p>
    </noscript>

    <form method="post" action="%s">
        <input id="a-very-unique-input-id#lightSAML" type="submit" style="display:none;"/>

        %s

        <noscript>
            <input type="submit" value="Submit" />
        </noscript>

    </form>
</body>
</html>
EOT;
        $fields = '';
        foreach ($this->data as $name => $value) {
            $fields .= sprintf(
                '<input type="hidden" name="%s" value="%s" />',
                htmlspecialchars($name),
                htmlspecialchars($value)
            );
        }

        $content = sprintf($content, htmlspecialchars($this->destination), $fields);

        $this->setContent($content);
    }
}
