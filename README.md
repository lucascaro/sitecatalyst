sitecatalyst
============

SiteCatalyst API &amp; Helper.

This class provides an API for creating the javascript needed to communicate
with Adobe SiteCatalyst.

Usage
-----
```
// Instantiate the class.
$sc = new SiteCatalyst($js_location, $account, $version);
// Add properties.
$sc->setProp(1, "Value for prop1");
// Add evars.
$sc->setEVar(2, "Value for evar2");
// Add custom props.
$sc->setCustomKey("custom3", "Value for custom3");
// Add events.
$sc->addEvent("eventid");

// Generate the script.
$script = $sc->getScript();
```

This will generate the whole script code with all the properties, eVars, events
and custom properties specified before.

For advanced usage, for example adding custom js code, there are 3 methods
available: `getHeader()`, `getPayload()` and `getFooter()` which will return
the script declaration, the variables and the ending. This allows to add code
before or after the variables but in the same code block.

For convenience, there are method defined for the most common properties,
`setChannel()`, `setPageName()` and `setEncoding()`.


Accounts
--------

If the `s_account` variable is not defined in the s_code.js script, you can
specify it in the constructor, as the second parameter, or use the
`setAccount()` method to define it later. when the code is generated, `s_account`
will be set to the specified value before loading the `jsSource` script.
