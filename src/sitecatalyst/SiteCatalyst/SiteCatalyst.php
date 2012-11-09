<?php

namespace sitecatalyst\SiteCatalyst;

class SiteCatalyst
{
  /**
   * @var string
   * Used to specify the js api version number as a comment.
   */
  protected $apiVersion = NULL;
  protected $account = NULL;
  protected $jsSource = NULL;

  protected $props = array();
  protected $evars = array();
  protected $events = array();
  protected $custom = array();

  /**
   * @var string
   * The src of the tracker image.
   * @see SiteCatalyst::setTrackerImage()
   */
  protected $trackerImage;

  public function __construct($jsSource, $account = NULL, $apiVersion = 'H.22.1')
  {
    // Use test account by default
    $this->account = $account;
    $this->jsSource = $jsSource;
    $this->apiVersion = $apiVersion;
    $this->trackerImage = 'http://examplecom.112.2O7.net/b/ss/examplecom/1/H.13--NS/0/4654065';

  }

  public function getPayload()
  {
    $output = '';
    // note: output is javascript
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
    $this->setCustomKey('channel', $channel);
  }

  public function setEncoding($encoding)
  {
    $this->setCustomKey('charSet', $encoding);
  }

  public function setPageName($pageName)
  {
    $this->setCustomKey('pageName', $pageName);
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

  public function getCustomKey($key)
  {
    return !empty($this->custom[$key]) ? $this->custom[$key] : NULL;
  }

  /**
   * Sets the tracker image source.
   *
   * The default value is
   * http://examplecom.112.2O7.net/b/ss/examplecom/1/H.13--NS/0/4654065
   * You can use protocol relative urls to have the image in both http and https
   * pages:
   * //examplecom.112.2O7.net/b/ss/examplecom/1/H.13--NS/0/4654065
   *
   * @param string $img
   *   The image src for the tracker image.
   */
  public function setTrackerImage($img)
  {
    $this->trackerImage = $img;
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
    return $this->getCustomKey('charSet');
  }

  public function getPageName()
  {
    return $this->getCustomKey('pageName');
  }

  public function getChannel()
  {
    return $this->getCustomKey('channel');
  }

  /* If you don't need to add any custom JS between header, vars, footer then getScript() should be called. These functions
  *  should only be called together as any individual one will not return a full block of valid markup.
  */
  public function getHeader($suffix = NULL)
  {
    if (!empty($this->account)) {
      $accountScript = '<script type="text/javascript">s_account="';
      $accountScript .= $this->account . '";</script>' . PHP_EOL;
    } else {
      $accountScript = '';
    }
    return <<<HEREDOC
<!-- SiteCatalyst code version: {$this->apiVersion}.
Copyright 1996-2011 Adobe, Inc. All Rights Reserved
More info available at http://www.omniture.com -->
{$accountScript}
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
<a href="http://www.omniture.com" title="Web Analytics">
  <img src="{$this->trackerImage}" height="1" width="1" border="0" alt="" />
</a>
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
