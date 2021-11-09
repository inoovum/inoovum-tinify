<?php
namespace Inoovum\Tinify\Domain\Factory;

/*
 * This file is part of the Inoovum.Tinify package.
 */

use Neos\Flow\Annotations as Flow;
use Tinify;

class TinifyFactory
{

    /**
     * @Flow\Inject
     * @var \Inoovum\Tinify\Domain\Service\NodeService
     */
    protected $nodeService;

    /**
     * @var array
     */
    protected $settings;

    /**
     * @param array $settings
     * @return void
     */
    public function injectSettings(array $settings) {
        $this->settings = $settings;
    }

    /**
     * @return array
     */
    public function tinify():array
    {
        $images = $this->nodeService->getImages();
        $count = 0;
        $totalCount = 0;
        if(!empty($images)) {
            Tinify\setKey($this->settings['apiKey']);
            foreach ($images as $image) {
                if(!$this->isPNG(basename($image))) {
                    if(@Tinify\fromFile(rawurldecode($image))->toFile(rawurldecode($image))) {
                        echo '✅ ' . basename($image) . " tinified\n";
                        $count = $count + 1;
                    } else {
                        echo "⚠️ ERROR: " . basename($image) . "\n";
                    }
                    $totalCount = $totalCount + 1;
                }
            }
        }
        return [
            'tinified' => $count,
            'total' => $totalCount
        ];
    }

    /**
     * @param string $filename
     * @return bool
     */
    public function isPNG(string $filename):bool
    {
        $pathInfo = pathinfo($filename);
        $result = false;
        if($pathInfo['extension'] == 'png' || $pathInfo['extension'] == 'PNG') {
            $result = true;
        }
        return $result;
    }

}
