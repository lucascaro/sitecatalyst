<?php

require_once(__DIR__ . '/../../../src/sitecatalyst/SiteCatalyst/SiteCatalyst.php');

use sitecatalyst\SiteCatalyst\SiteCatalyst as SiteCatalyst;

class SiteCatalystTest extends \PHPUnit_Framework_TestCase
{
  public function testBasicSettings()
  {
    $expected = <<<HEREDOC
<!-- SiteCatalyst code version: H.22.1.
Copyright 1996-2011 Adobe, Inc. All Rights Reserved
More info available at http://www.omniture.com -->
<script type="text/javascript">s_account="account";</script>

<script type="text/javascript" src="jsSource"></script>

<script type="text/javascript">
s.prop0="prop-0";
s.prop1="prop-1";
s.prop2="prop-2";
s.eVar0="eVar-0";
s.eVar1="eVar-1";
s.eVar2="eVar-2";
s.channel="theChannel";
s.charSet="ISO-8859-1";
s.custom0="custom-0";
s.custom1="custom-1";
s.custom2="custom-2";
s.pageName="The Page Name";
s.events="eventeventid-0,eventeventid-1,eventeventid-2";

var s_code=s.t();if(s_code)document.write(s_code)
</script>

<script type="text/javascript">
if(navigator.appVersion.indexOf('MSIE')>=0)document.write(unescape('%3C')+'\!-'+'-')
</script>

<noscript>
<a href="http://www.omniture.com" title="Web Analytics">
  <img src="//examplecom.112.2O7.net/b/ss/examplecom/1/H.13--NS/0/4654065" height="1" width="1" border="0" alt="" />
</a>
</noscript>
<!-- End SiteCatalyst code version: H.22.1. -->

HEREDOC;

    $s = new SiteCatalyst('jsSource', 'account');

    // Set basics.
    $s->setChannel('theChannel');
    $s->setPageName('The Page Name');
    $s->setTrackerImage('//examplecom.112.2O7.net/b/ss/examplecom/1/H.13--NS/0/4654065');
    $s->setEncoding('ISO-8859-1');


    for ($i=0; $i<3; $i++) {
      // Add props.
      $s->setProp($i, "prop-$i");
      // Add evars.
      $s->setEVar($i, "eVar-$i");
      // Add custom props.
      $s->setCustomKey("custom$i", "custom-$i");
      // Add events.
      $s->addEvent("eventid-$i");
    }

    $script = $s->getScript();

    $this->assertEquals($expected, $script, 'Test Generic SiteCatalyst script');
  }
}
