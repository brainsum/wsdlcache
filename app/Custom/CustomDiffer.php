<?php
/**
 * Created by PhpStorm.
 * User: mhavelant
 * Date: 2016.07.12.
 * Time: 15:50
 */

namespace App\Custom;


use SebastianBergmann\Diff\Differ as Differ;
use SebastianBergmann\Diff\LCS\LongestCommonSubsequence;
use SebastianBergmann\Diff\LCS\TimeEfficientImplementation;
use SebastianBergmann\Diff\LCS\MemoryEfficientImplementation;

class CustomDiffer extends Differ{
  /**
   * @var string
   */
  private $header;

  /**
   * @var bool
   */
  private $showNonDiffLines;

  /**
   * @var Integer
   */
  private $diffCount = 0;

  /**
   * @param string $header
   */
  public function __construct($header = "--- Original\n+++ New\n", $showNonDiffLines = true)
  {
    $this->header           = $header;
    $this->showNonDiffLines = $showNonDiffLines;
    $this->diffCount = 0;

    parent::__construct($header, $showNonDiffLines);
  }

  /**
   * Returns the diff between two arrays or strings as string.
   *
   * @param array|string             $from
   * @param array|string             $to
   * @param LongestCommonSubsequence $lcs
   *
   * @return string
   */
  public function diff($from, $to, LongestCommonSubsequence $lcs = null)
  {
    $this->diffCount = 0;

    if (!is_array($from) && !is_string($from)) {
      $from = (string) $from;
    }

    if (!is_array($to) && !is_string($to)) {
      $to = (string) $to;
    }

    $buffer = (!empty($this->header) ? $this->header : "");
    $diff   = $this->diffToArray($from, $to, $lcs);

    $inOld = false;
    $i     = 0;
    $old   = array();

    foreach ($diff as $line) {
      if ($line[1] ===  0 /* OLD */) {
        if ($inOld === false) {
          $inOld = $i;
        }
      } elseif ($inOld !== false) {
        if (($i - $inOld) > 5) {
          $old[$inOld] = $i - 1;
        }

        $inOld = false;
      }

      ++$i;
    }

    $start = isset($old[0]) ? $old[0] : 0;
    $end   = count($diff);

    if ($tmp = array_search($end, $old)) {
      $end = $tmp;
    }

    $newChunk = true;
    $lineWritten = false;
    $sumOfDiffedLines = 0;

    for ($i = $start; $i < $end; ++$i) {
      if (isset($old[$i])) {
        // $buffer  .= "\n";
        // $newChunk = true;
        $i        = $old[$i];
      }

      if ($newChunk) {
        if ($this->showNonDiffLines === true) {
          $buffer .= "@@ @@\n";
        }
        $newChunk = false;
      }

      // Write the number of the line when a diff is detected
      if (($diff[$i][1] === 1 || $diff[$i][1] === 2)) {
        if (FALSE === $lineWritten) {
          $this->increaseDiffCount();
          $j = $i;
          for (; ; ++$j) {
            if ($end <= $j || !($diff[$j][1] === 1 || $diff[$j][1] === 2)) {
              // We search for the next non-diff line
              --$j;
              break;
            }
          }

          $currLineDifference = (($j - $i + 1) / 2);
          $sumOfDiffedLines += $currLineDifference;
          $lineNumber = ($i + ($currLineDifference - $sumOfDiffedLines) + 1);

          $buffer .= "\n@@ $lineNumber @@\n";

          $lineWritten = TRUE;
        }
      }

      if ($diff[$i][1] === 1 /* ADDED */) {
        $buffer .= '+' . $diff[$i][0] . "\n";
      } elseif ($diff[$i][1] === 2 /* REMOVED */) {
        $buffer .= '-' . $diff[$i][0] . "\n";
      } elseif ($this->showNonDiffLines === true) {
        $buffer .= ' ' . $diff[$i][0] . "\n";
        $lineWritten = false;
      }
    }

    return $buffer;
  }

  private function increaseDiffCount() {
    $this->diffCount = $this->diffCount + 1;
  }


  public function getDiffCount() {
    return $this->diffCount;
  }
}