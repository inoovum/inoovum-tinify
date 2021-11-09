<?php
namespace Inoovum\Tinify\Command;

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Cli\CommandController;

/**
 * @Flow\Scope("singleton")
 */
class TinifyCommandController extends CommandController
{

    /**
     * @Flow\Inject
     * @var \Inoovum\Tinify\Domain\Factory\TinifyFactory
     */
    protected $tinifyFactory;

    /**
     * Tinify jpegs
     *
     * @return void
     */
    public function compressCommand()
    {
        $tinify = $this->tinifyFactory->tinify();
        $this->outputLine("\n" . $tinify['tinified'] . ' out of ' . $tinify['total'] . ' images were tinified. 💪');
    }

}
