<?php
namespace Inoovum\Tinify\Domain\Service;

/*
 * This file is part of the Inoovum.Tinify package.
 */

use Neos\Flow\Annotations as Flow;
use Neos\Eel\FlowQuery\FlowQuery;
use Neos\Eel\FlowQuery\Operations;
use Neos\ContentRepository\Domain\Model\NodeInterface;

class NodeService
{

    /**
     * @Flow\Inject
     * @var \Neos\ContentRepository\Domain\Service\ContextFactoryInterface
     */
    protected $contextFactory;

    /**
     * @Flow\Inject
     * @var \Neos\Flow\ResourceManagement\ResourceManager
     */
    protected $resourceManager;

    /**
     * @param string $siteNode
     * @return array
     */
    public function getImages(string $siteNode):array
    {
        $siteNodeImages = $this->getPropertiesWithImages($this->getSiteNode($siteNode));
        $nodeImages = $this->getPropertiesWithImages($this->getNodes($siteNode));
        $images = [];
        if(!empty($siteNodeImages)) {
            foreach ($siteNodeImages as $siteNodeImage) {
                $images[] = $siteNodeImage;
            }
        }
        if(!empty($nodeImages)) {
            foreach ($nodeImages as $nodeImage) {
                $images[] = $nodeImage;
            }
        }
        return $images;
    }

    /**
     * @param array $nodes
     * @return array
     */
    public function getPropertiesWithImages(array $nodes):array
    {
        $result = [];
        if(!empty($nodes)) {
            foreach ($nodes as $node) {
                $properties = $node->getProperties();
                if(!empty($properties)) {
                    foreach ($properties as $property) {
                        if(is_object($property)) {
                            if(get_class($property) == 'Neos\Flow\Persistence\Doctrine\Proxies\__CG__\Neos\Media\Domain\Model\Image' || get_class($property) == 'Neos\Flow\Persistence\Doctrine\Proxies\__CG__\Neos\Media\Domain\Model\ImageVariant') {
                                if(method_exists($property, 'getOriginalAsset')) {
                                    $sha1 = $property->getOriginalAsset()->getResource()->getSha1();
                                    $folderStructrure = str_split($sha1);
                                    $relativePath = constant('FLOW_PATH_ROOT') . 'Web/_Resources/Persistent/' . $folderStructrure[0] . '/' . $folderStructrure[1] . '/' . $folderStructrure[2] . '/' . $folderStructrure[3] . '/' . $sha1 . '/' . rawurlencode($property->getOriginalAsset()->getResource()->getFileName());
                                    $result[] = $relativePath;
                                }
                                if(!method_exists($property, 'getOriginalAsset')) {
                                    $sha1 = $property->getResource()->getSha1();
                                    $folderStructrure = str_split($sha1);
                                    $relativePath = constant('FLOW_PATH_ROOT') . 'Web/_Resources/Persistent/' . $folderStructrure[0] . '/' . $folderStructrure[1] . '/' . $folderStructrure[2] . '/' . $folderStructrure[3] . '/' . $sha1 . '/' . rawurlencode($property->getResource()->getFileName());
                                    $result[] = $relativePath;
                                }
                            }
                        }
                    }
                }
            }
        }
        return $result;
    }

    /**
     * @param string $siteNode
     * @return array
     */
    public function getSiteNode(string $siteNode):array
    {
        $context = $this->contextFactory->create(array('invisibleContentShown' => true));
        $siteNode = $context->getNode('/sites/'. $siteNode);
        return (new FlowQuery(array($siteNode)))->context(array('invisibleContentShown' => true))->get();
    }

    /**
     * @param string $siteNode
     * @return array
     */
    public function getNodes(string $siteNode):array
    {
        $context = $this->contextFactory->create(array('invisibleContentShown' => true));
        $siteNode = $context->getNode('/sites/'. $siteNode);
        return (new FlowQuery(array($siteNode)))->context(array('invisibleContentShown' => true))->find('[instanceof Neos.Neos:Node]')->sort('_index', 'ASC')->get();
    }

}
