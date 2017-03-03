<?php

namespace AppBundle\Serializer;

use JMS\Serializer\Builder\DefaultDriverFactory;
use JMS\Serializer\Builder\DriverFactoryInterface;
use JMS\Serializer\Handler\PhpCollectionHandler;
use JMS\Serializer\Handler\PropelCollectionHandler;
use JMS\Serializer\Exception\RuntimeException;
use Metadata\Driver\DriverInterface;
use Metadata\MetadataFactory;
use JMS\Serializer\Metadata\Driver\AnnotationDriver;
use JMS\Serializer\Handler\HandlerRegistry;
use AppBundle\Serializer\Construction\UnserializeObjectConstructor;
use PhpCollection\Map;
use JMS\Serializer\EventDispatcher\EventDispatcher;
use Metadata\Driver\DriverChain;
use JMS\Serializer\Metadata\Driver\YamlDriver;
use JMS\Serializer\Metadata\Driver\XmlDriver;
use Metadata\Driver\FileLocator;
use JMS\Serializer\Handler\DateHandler;
use JMS\Serializer\Handler\ArrayCollectionHandler;
use JMS\Serializer\Construction\ObjectConstructorInterface;
use JMS\Serializer\EventDispatcher\Subscriber\DoctrineProxySubscriber;
use JMS\Serializer\Naming\CamelCaseNamingStrategy;
use JMS\Serializer\Naming\PropertyNamingStrategyInterface;
use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\FileCacheReader;
use Metadata\Cache\FileCache;
use JMS\Serializer\Naming\SerializedNameAnnotationStrategy;
use JMS\Serializer\Exception\InvalidArgumentException;

use JMS\Serializer\XmlSerializationVisitor;
use JMS\Serializer\YamlSerializationVisitor;
use JMS\Serializer\JsonSerializationVisitor;
use JMS\Serializer\XmlDeserializationVisitor;
use JMS\Serializer\YamlDeserializationVisitor;
use JMS\Serializer\JsonDeserializationVisitor;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder as BaseSerializerBuilder;
use JMS\Serializer\VisitorInterface;

/**
 * Builder for serializer instances.
 *
 * This object makes serializer construction a breeze for projects that do not use
 * any special dependency injection container.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
class SerializerBuilder extends BaseSerializerBuilder 
{
    private $metadataDirs = array();
    private $handlerRegistry;
    private $handlersConfigured = false;
    private $eventDispatcher;
    private $listenersConfigured = false;
    private $objectConstructor;
    private $serializationVisitors;
    private $deserializationVisitors;
    private $visitorsAdded = false;
    private $propertyNamingStrategy;
    private $debug = false;
    private $cacheDir;
    private $annotationReader;
    private $includeInterfaceMetadata = false;
    private $driverFactory;

    public function __construct()
    {
        $this->handlerRegistry = new HandlerRegistry();
        $this->eventDispatcher = new EventDispatcher();
        $this->driverFactory = new DefaultDriverFactory();
        $this->serializationVisitors = new Map();
        $this->deserializationVisitors = new Map();
    }

    public function addDefaultHandlers()
    {
        $this->handlersConfigured = true;
        $this->handlerRegistry->registerSubscribingHandler(new DateHandler());
        $this->handlerRegistry->registerSubscribingHandler(new PhpCollectionHandler());
        $this->handlerRegistry->registerSubscribingHandler(new ArrayCollectionHandler());
        $this->handlerRegistry->registerSubscribingHandler(new PropelCollectionHandler());

        return $this;
    }

    public function configureHandlers(\Closure $closure)
    {
        $this->handlersConfigured = true;
        $closure($this->handlerRegistry);

        return $this;
    }

    public function addDefaultListeners()
    {
        $this->listenersConfigured = true;
        $this->eventDispatcher->addSubscriber(new DoctrineProxySubscriber());

        return $this;
    }

    public function configureListeners(\Closure $closure)
    {
        $this->listenersConfigured = true;
        $closure($this->eventDispatcher);

        return $this;
    }

    public function addDefaultSerializationVisitors()
    {
        $this->initializePropertyNamingStrategy();

        $this->visitorsAdded = true;
        $this->serializationVisitors->setAll(array(
            'xml' => new XmlSerializationVisitor($this->propertyNamingStrategy),
            'yml' => new YamlSerializationVisitor($this->propertyNamingStrategy),
            'json' => new JsonSerializationVisitor($this->propertyNamingStrategy),
        ));

        return $this;
    }

    public function addDefaultDeserializationVisitors()
    {
        $this->initializePropertyNamingStrategy();

        $this->visitorsAdded = true;
        $this->deserializationVisitors->setAll(array(
            'xml' => new XmlDeserializationVisitor($this->propertyNamingStrategy),
            'json' => new JsonDeserializationVisitor($this->propertyNamingStrategy),
        ));

        return $this;
    }

    public function build()
    {
        $annotationReader = $this->annotationReader;
        if (null === $annotationReader) {
            $annotationReader = new AnnotationReader();

            if (null !== $this->cacheDir) {
                $this->createDir($this->cacheDir.'/annotations');
                $annotationReader = new FileCacheReader($annotationReader, $this->cacheDir.'/annotations', $this->debug);
            }
        }

        $metadataDriver = $this->driverFactory->createDriver($this->metadataDirs, $annotationReader);
        $metadataFactory = new MetadataFactory($metadataDriver, null, $this->debug);

        $metadataFactory->setIncludeInterfaces($this->includeInterfaceMetadata);

        if (null !== $this->cacheDir) {
            $this->createDir($this->cacheDir.'/metadata');
            $metadataFactory->setCache(new FileCache($this->cacheDir.'/metadata'));
        }

        if ( ! $this->handlersConfigured) {
            $this->addDefaultHandlers();
        }

        if ( ! $this->listenersConfigured) {
            $this->addDefaultListeners();
        }

        if ( ! $this->visitorsAdded) {
            $this->addDefaultSerializationVisitors();
            $this->addDefaultDeserializationVisitors();
        }

        return new Serializer(
            $metadataFactory,
            $this->handlerRegistry,
            $this->objectConstructor ?: new UnserializeObjectConstructor(),
            $this->serializationVisitors,
            $this->deserializationVisitors,
            $this->eventDispatcher
        );
    }

    private function initializePropertyNamingStrategy()
    {
        if (null !== $this->propertyNamingStrategy) {
            return;
        }

        $this->propertyNamingStrategy = new SerializedNameAnnotationStrategy(new CamelCaseNamingStrategy());
    }

    private function createDir($dir)
    {
        if (is_dir($dir)) {
            return;
        }

        if (false === @mkdir($dir, 0777, true)) {
            throw new RuntimeException(sprintf('Could not create directory "%s".', $dir));
        }
    }
}
