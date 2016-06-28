<?php

namespace RelaxedMerge;

use PhpMerge\PhpMerge;
use Relaxed\Merge\ConflictException;
use Relaxed\Merge\ThreeWayMerge;
use SebastianBergmann\Diff\Differ;

/**
 * Class RelaxedMerge merges three strings with the ThreeWayMerge.
 *
 * This implementation is basically just to test the ThreeWayMerge with the
 * PhpMerge test suite.
 */
class RelaxedMerge extends PhpMerge
{

    /**
     * The merge instance.
     *
     * @var \Relaxed\Merge\ThreeWayMerge
     */
    protected $merger;
    
    /**
     * Constructor, not setting anything up.
     *
     * @param \Relaxed\Merge\ThreeWayMerge $merger
     *   The merger instance
     * @param \SebastianBergmann\Diff\Differ $differ
     *   The differ for PhpMerge
     */
    public function __construct(ThreeWayMerge $merger = null, Differ $differ = null)
    {
        parent::__construct($differ);
        if (!$merger) {
            $merger = new ThreeWayMerge();
        }
        $this->merger = $merger;
    }

    /**
     * @inheritDoc
     */
    public function merge($base, $remote, $local)
    {
        // ThreeWayMerge merges arrays, so make arrays.
        $ancestor = ['text' => $base];
        $local_array = ['text' => $local];
        $remote_array = ['text' => $remote];
        try {
            $result = $this->merger->performMerge($ancestor, $local_array, $remote_array);
            return $result['text'];
        } catch (ConflictException $e) {
            // Fall back to PhpMerge conflict resolution since the
            // \Relaxed\Merge\ConflictException does not tell what conflicted.
            return parent::merge($base, $remote, $local);
        }
    }


}
