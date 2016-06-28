<?php
/**
 * This file is part of the php-merge package.
 *
 * (c) Fabian Bircher <opensource@fabianbircher.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace RelaxedMerge;

use PhpMerge\MergeException;
use PhpMerge\PhpMerge;
use Relaxed\Merge\ConflictException;

/**
 * Class RelaxedMerge merges three strings with the ThreeWayMerge.
 *
 * This implementation is basically just to test the ThreeWayMerge with the
 * PhpMerge test suite.
 */
class ThreeWayMerge
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
     * @param \PhpMerge\PhpMerge $merger
     *   The merger instance
     */
    public function __construct(PhpMerge $merger = null)
    {
        if (!$merger) {
            $merger = new PhpMerge();
        }
        $this->merger = $merger;
    }


    /**
     * Perform merge on associative array
     *
     * @param array $ancestor
     * @param array $local
     * @param array $remote
     *
     * @return array
     */
    public function performMerge(array $ancestor, array $local, array $remote)
    {
        // Returning a new Array for now. Can return the modified ancestor as well.
        $merged = [];
        foreach ($ancestor as $key => $value) {
            // Checks if the value contains an array itself.
            if (is_array($value) && array_key_exists($key, $local) && array_key_exists($key, $remote)) {
                $merged[$key] = $this->performMerge(
                  $value,
                  $local[$key],
                  $remote[$key]
                );
            } else {
                if (array_key_exists($key, $local) && array_key_exists($key, $remote)) {
                    try {
                        $changed =  ['base' => $ancestor[$key], 'remote' => $remote[$key], 'local' => $local[$key]];
                        // Insert new lines to make insertions detectable.
//                        $changed = array_map(function ($text) {
//                            return $text . "\n";
//                        }, $changed);
//                        $changed = array_map(function ($text) {
//                            return str_replace("\n", "\n%php%merge%\n%new%line%\n", $text);
//                        }, $changed);

                        $merge_result = $this->merger->merge($changed['base'], $changed['remote'], $changed['local']);
                        // undo the changes to the result.
//                        $merge_result = str_replace("\n%php%merge%\n%new%line%\n", "\n", $merge_result);
//                        $merge_result = substr($merge_result, 0, -1);

                        $merged[$key] = $merge_result;
                    } catch (MergeException $e) {
                        throw new ConflictException("A php-merge conflict has occured");
                    }
                } elseif (array_key_exists($key, $local)) {
                    if ($ancestor[$key] != $local[$key]) {
                        throw new ConflictException("A conflict has occured");
                    }
                } elseif (array_key_exists($key, $remote)) {
                    if ($ancestor[$key] != $remote[$key]) {
                        throw new ConflictException("A conflict has occured");
                    }
                } else {
                    unset($merged[$key]);
                }
            }
        }
        return $merged;
    }


}
