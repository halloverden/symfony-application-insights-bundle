Application Insights Bundle
==============================
The Application Insights Bundle provides tools to register exceptions occurred in your Symfony application to Microsoft's Azure Application Insights. It makes use of the [Azure Application Insights package](https://github.com/halloverden/azure-application-insights) originally developed by Microsoft.  

Installation
============

Make sure Composer is installed globally, as explained in the
[installation chapter](https://getcomposer.org/doc/00-intro.md)
of the Composer documentation.

Applications that use Symfony Flex
----------------------------------

Open a command console, enter your project directory and execute:

```console
$ composer require halloverden/symfony-application-insights-bundle
```

Applications that don't use Symfony Flex
----------------------------------------

### Step 1: Download the Bundle

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

```console
$ composer require halloverden/symfony-application-insights-bundle
```

### Step 2: Enable the Bundle

Then, enable the bundle by adding it to the list of registered bundles
in the `config/bundles.php` file of your project:

```php
// config/bundles.php

return [
    // ...
    HalloVerden\ApplicationInsightsBundle\HalloVerdenApplicationInsightsBundle::class => ['all' => true],
];
```

## Usage

You have to register these values in your .env file:

```dotenv
###> symfony-application-insights-bundle ###
TRACKING_ENABLED=true # SET TO TRUE TO ENABLE THE BUNDLE 
MICROSOFT_APP_INSIGHTS_INTRUMENTATION_KEY='cfa65c5d-61ac-4336-b47a-8491578d35f3' # INTRUMENTATION KEY REQUIRED TO SEND INFO TO AZURE APPLICATION INSIGHTS
EXCEPTION_TRACKING_ENABLED=true # SET TO TRUE TO ENABLE EXCEPTION TRACKING 
EXCEPTION_TRACKING_IGNORE='["Symfony\\Component\\HttpKernel\\Exception\\NotFoundHttpException"]' # LIST OF EXCEPTION TO BE IGNORED BY THE TRACKERS
###< symfony-application-insights-bundle ### 
```

When both `TRACKING_ENABLED` and `EXCEPTION_TRACKING_ENABLED` are set to true, the Exception Subscriber provided by the bundle will listen for exceptions (ignoring those provided in the `EXCEPTION_TRACKING_IGNORE` parameter) and register them in a Telemetry Queue. The Terminate Subscriber will wait for the operation to be finished to then flush the queue and send the exceptions to Azure Application Insights.
No additional code is required.

As of now, the bundle only supports exception tracking.

---

## Contributing
Pull requests are welcome. For major changes, please open an issue first to discuss what you would like to change.

Please make sure to update tests as appropriate.

## License
[MIT](https://choosealicense.com/licenses/mit/)
