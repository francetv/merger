# Ftv Merger

This component allows you to merge multiple data sources in a single array and based on business rules.

## How to install

In composer.json

```
"repositories": [
    {
        "type": "vcs",
        "url": "git@gitlab.ftven.net:team-infini/merger.git"
    }
]
```

And run composer

```
composer require ftv/merger
```

## How to use

Create rule class that implement `RuleInterface`
 
```
<?php

namespace App\Merger\Rules

use Ftv\Merger\Rule\RuleInterface;

class MyBusinessRule implement RuleInterface
{
    public function apply($merged, $firstSource, $secondSource)
    {
        if (!isset($secondSource['specific-key'])) {
            return $merged;
        }
        
        if ($secondSource['specific-key'] === "some specific value") {
            $merged['specific-key'] = "override value because business is business";
        }
        
        return $merged
    }
}
```

Then, create an instance of Merger and add this rule 

```
//somewhere in your application
$merger = new Merger();
$merger->addRule(new MyBusinessRule());
```

And use it.

```
//somewhere further in your application
$merged = $merger->merge($merged, $dataSource1, $dataSource2);
```

`$merged` must contain all your data merged with rules applied. If no rule is added, it merge all data source into merged array.

## How to use with Symfony

Declare rule as service

```
<service id="app.merger.rules.my_business" class="App\Merger\Rule\MyBusinessRule" />
```

Declare merger as service and add rule.

```
<service id="app.merger" class="Ftv\Merger\Merger">
    <call method="addRule">
        <argument type="service" id="app.merger.rules.my_business" />
    </call>
</service>
```

In your app, call merger service

```
//somewhere in your application
$merged = $this->container->get('app.merger')->merge($merged, $dataSource1, $dataSource2);
```

That's it!

