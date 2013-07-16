<?php
namespace Maxposter\DacBundle\CacheWarmer;

use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmer;
use Maxposter\DacBundle\Mapping\Service\Annotations;

/**
 * Кеш карты аннотаций... брр
 */
class AnnotationsCacheWarmer extends CacheWarmer
{
    public function __construct(Annotations $annotation)
    {
        $this->annotations = $annotation;
    }

    public function warmUp($cacheDir)
    {
        // FIXME: как папочку создать? дурдом
        $this->writeCacheFile(sprintf('%s/max_dac.annotations.cache', $cacheDir), serialize($this->annotations->getAnnotationsMap()));
    }


    /**
     * Если true - то только для prod и cache:clear если я правильно понимаю
     *
     * @return bool
     */
    public function isOptional()
    {
        return false;
    }
}