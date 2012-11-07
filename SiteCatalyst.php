<?php

class SiteCatalyst
{
  /**
   * @var string
   * Used to specify the js api version number as a comment.
   */
  protected $apiVersion = NULL;
  protected $account = NULL;
  protected $jsSource = NULL;
  protected $channel = NULL;
  protected $encoding = 'UTF-8';

  protected $pageName = NULL;

  protected $props = array();
  protected $evars = array();
  protected $events = array();
  protected $custom = array();

  public function __construct($account, $jsSource, $apiVersion = 'H.22.1')
  {
    // Use test account by default
    $this->account = $account;
    $this->jsSource = $jsSource;
    $this->apiVersion = $apiVersion;
  }

  public function getPayload()
  {
    $output = '';
    // note: output is javascript
    $output .= 's.channel="'  . $this->channel  . '";' . PHP_EOL;
    $output .= 's.charSet="'  . $this->encoding . '";' . PHP_EOL;
    $output .= 's.pageName="' . $this->pageName . '";' . PHP_EOL;

    ksort($this->props);
    ksort($this->evars);
    ksort($this->custom);

    foreach ($this->props as $id => $value)
    {
      $output .= 's.prop' . $id . '="' . $value . '";' . PHP_EOL;
    }

    foreach ($this->evars as $id => $value)
    {
      $output .= 's.eVar' . $id . '="' . $value . '";' . PHP_EOL;
    }

    foreach ($this->custom as $id => $value)
    {
      $output .= 's.' . $id . '="' . $value . '";' . PHP_EOL;
    }
    $eventString = $this->getEventString();
    $output .= 's.events="' . $eventString . '";' . PHP_EOL;

    return $output;
  }

  public function addEvent($id)
  {
    if(!in_array($id, $this->events))
    {
      $this->events[] = $id;
      ksort($this->events);
    }
  }

  public function setChannel($channel)
  {
    $this->channel = $channel;
  }

  public function setEncoding($encoding)
  {
    $this->encoding = $encoding;
  }

  public function setPageName($pageName)
  {
    $this->pageName = $pageName;
  }

  public function setAccount($account)
  {
    $this->account = $account;
  }

  public function setProp($id, $value)
  {
    $this->props[$id] = $value;
  }

  public function setEVar($id, $value)
  {
    $this->evars[$id] = $value;
  }

  public function setCustomKey($key, $value)
  {
    $this->custom[$key] = $value;
  }

  public function getEventString()
  {
    return 'event' . implode(',event', $this->events);
  }

  public function getAccount()
  {
    return $this->account;
  }

  public function getEncoding()
  {
    return $this->encoding;
  }

  public function getPageName()
  {
    return $this->pageName;
  }

  /* If you don't need to add any custom JS between header, vars, footer then getScript() should be called. These functions
  *  should only be called together as any individual one will not return a full block of valid markup.
  */
  public function getHeader($suffix = NULL)
  {
    return <<<HEREDOC
<!-- SiteCatalyst code version: {$this->apiVersion}.
Copyright 1996-2011 Adobe, Inc. All Rights Reserved
More info available at http://www.omniture.com -->
<script type="text/javascript">s_account="{$this->account}";</script>
<script type="text/javascript" src="{$this->jsSource}"></script>

<script type="text/javascript">
{$suffix}
HEREDOC;
  }

  /* TODO: The noscript is referencing examplecom. Need to double check with documentation and probably change it in Chris' original class. */
  public function getFooter($prefix = NULL)
  {
    return <<<HEREDOC
{$prefix}
var s_code=s.t();if(s_code)document.write(s_code)
</script>

<script type="text/javascript">
if(navigator.appVersion.indexOf('MSIE')>=0)document.write(unescape('%3C')+'\!-'+'-')
</script>

<noscript>
<a href="http://www.omniture.com" title="Web Analytics"><img src="http://examplecom.112.2O7.net/b/ss/examplecom/1/H.13--NS/0/4654065"
height="1" width="1" border="0" alt="" /></a>
</noscript>
<!-- End SiteCatalyst code version: {$this->apiVersion}. -->

HEREDOC;
  }

  /* Main function used to print out the markup. */
  public function getScript()
  {
    return $this->getHeader() . $this->getPayload() . $this->getFooter();
  }
}
