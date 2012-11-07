<?php

class SiteCatalyst
{
  protected $account = null;
  protected $jsSource = null;
  protected $channel = null;
  protected $encoding = 'UTF-8';

  protected $pageName = null;

  protected $props = array();
  protected $evars = array();
  protected $events = array();
  protected $custom = array();

  public function __construct($account, $jsSource)
  {
// Use test account by default
    $this->account = $account;
    $this->jsSource = $jsSource;
  }

  public function getPayload()
  {
    $output = '';
// note: output is javascript
    $output .= sprintf('s.channel="%s";', $this->channel) . PHP_EOL;
    $output .= sprintf('s.charSet="%s";', $this->encoding) . PHP_EOL;
    $output .= sprintf('s.pageName="%s";', $this->getPageName()) . PHP_EOL;

    ksort($this->props);
    ksort($this->evars);
    ksort($this->custom);

    foreach ($this->props as $id => $value)
    {
      $output .= sprintf('s.prop%d="%s";', $id, $value) . PHP_EOL;
    }

    foreach ($this->evars as $id => $value)
    {
      $output .= sprintf('s.eVar%d="%s";', $id, $value) . PHP_EOL;
    }

    foreach ($this->custom as $id => $value)
    {
      $output .= sprintf('s.%s="%s";', $id, $value) . PHP_EOL;
    }

    $output .= sprintf('s.events="%s";', $this->getEventString()) . PHP_EOL;

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

  /* TODO: The noscript is referencing examplecom. Need to double check with documentation and probably change it in Chris' original class. */
  public function getScript()
  {
    return <<<HEREDOC
<script type="text/javascript">s_account="{$this->getAccount()}";</script>
<script type="text/javascript" src="{$this->jsSource}"></script>

<script type="text/javascript">
{$this->getPayload()}
var s_code=s.t();if(s_code)document.write(s_code)
</script>

<script type="text/javascript">
if(navigator.appVersion.indexOf('MSIE')>=0)document.write(unescape('%3C')+'\!-'+'-')
</script>

<noscript>
<a href="http://www.omniture.com" title="Web Analytics"><img src="http://examplecom.112.2O7.net/b/ss/examplecom/1/H.13--NS/0/4654065"
height="1" width="1" border="0" alt="" /></a>
</noscript>
HEREDOC;
  }

}
