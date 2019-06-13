TOTElib
=======

Knihovna a sada nástrojů pro připojení k TOTE SQL serveru.

Instalace
=========
Instalace je nejjednodušší pomocí composeru 

```
composer require mepatek/totelib
```

nebo přidáním do composer.json souboru.
 
Použití
=======

Using the Option Type in your API
---------------------------------
```php
class MyRepository
{
    public function findSomeEntity($criteria)
    {
        if (null !== $entity = $this->em->find(...)) {
            return new \PhpOption\Some($entity);
        }

        // We use a singleton, for the None case.
        return \PhpOption\None::create();
    }
}
```

If you are consuming an existing library, you can also use a shorter version
which by default treats ``null`` as ``None``, and everything else as ``Some`` case:

```php
class MyRepository
{
    public function findSomeEntity($criteria)
    {
        return \PhpOption\Option::fromValue($this->em->find(...));

        // or, if you want to change the none value to false for example:
        return \PhpOption\Option::fromValue($this->em->find(...), false);
    }
}
```

Case 1: You always Require an Entity in Calling Code
----------------------------------------------------
```php
$entity = $repo->findSomeEntity(...)->get(); // returns entity, or throws exception
```

Case 2: Fallback to Default Value If Not Available
--------------------------------------------------
```php
$entity = $repo->findSomeEntity(...)->getOrElse(new Entity());

// Or, if you want to lazily create the entity.
$entity = $repo->findSomeEntity(...)->getOrCall(function() {
    return new Entity();
});
```

More Examples
=============

No More Boiler Plate Code
-------------------------
```php
// Before
if (null === $entity = $this->findSomeEntity()) {
    throw new NotFoundException();
}
echo $entity->name;

// After
echo $this->findSomeEntity()->get()->name;
```

No More Control Flow Exceptions
-------------------------------
```php
// Before
try {
    $entity = $this->findSomeEntity();
} catch (NotFoundException $ex) {
    $entity = new Entity();
}

// After
$entity = $this->findSomeEntity()->getOrElse(new Entity());
```

More Concise Null Handling
--------------------------
```php
// Before
$entity = $this->findSomeEntity();
if (null === $entity) {
    return new Entity();
}

return $entity;

// After
return $this->findSomeEntity()->getOrElse(new Entity());
```

Trying Multiple Alternative Options
-----------------------------------
If you'd like to try multiple alternatives, the ``orElse`` method allows you to
do this very elegantly:

```php
return $this->findSomeEntity()
            ->orElse($this->findSomeOtherEntity())
            ->orElse($this->createEntity());
```
The first option which is non-empty will be returned. This is especially useful 
with lazy-evaluated options, see below.

Lazy-Evaluated Options
----------------------
The above example has the flaw that we would need to evaluate all options when
the method is called which creates unnecessary overhead if the first option is 
already non-empty.

Fortunately, we can easily solve this by using the ``LazyOption`` class:

```php
return $this->findSomeEntity()
            ->orElse(new LazyOption(array($this, 'findSomeOtherEntity')))
            ->orElse(new LazyOption(array($this, 'createEntity')));
```

This way, only the options that are necessary will actually be evaluated.


Performance Considerations
==========================
Of course, performance is important. Attached is a performance benchmark which
you can run on a machine of your choosing.

The overhead incurred by the Option type comes down to the time that it takes to
create one object, our wrapper. Also, we need to perform one additional method call
to retrieve the value from the wrapper.

* Overhead: Creation of 1 Object, and 1 Method Call
* Average Overhead per Invocation (some case/value returned): 0.000000761s (that is 761 nano seconds)
* Average Overhead per Invocation (none case/null returned): 0.000000368s (that is 368 nano seconds)

The benchmark was run under Ubuntu precise with PHP 5.4.6. As you can see the
overhead is surprisingly low, almost negligible.

So in conclusion, unless you plan to call a method thousands of times during a
request, there is no reason to stick to the ``object|null`` return value; better give
your code some options!
